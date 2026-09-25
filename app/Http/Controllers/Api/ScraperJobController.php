<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\JobAble;
use App\Models\Company;
use App\Models\EducationTranslation;
use App\Models\ExperienceTranslation;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobCategoryTranslation;
use App\Models\JobPostingLog;
use App\Models\JobRole;
use App\Models\JobRoleTranslation;
use App\Models\JobSource;
use App\Models\JobTypeTranslation;
use App\Models\SalaryTypeTranslation;
use App\Models\TeamMember;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ScraperJobController extends Controller
{
    use JobAble;

    /**
     * Name recorded as "posted by" on every job that arrives through here, so
     * the job posting report can tell scraped jobs apart from the team's.
     */
    const POSTED_BY = 'Scraper';

    /**
     * Category for any job whose category the portal does not have.
     */
    const FALLBACK_CATEGORY = 'Others';

    /**
     * POST /api/scraper/jobs
     * Post one scraped job. Header: X-API-KEY.
     *
     * Every job goes to the company in services.scraper.company_id and waits
     * as pending until an admin approves it. Candidates always apply on the
     * original site, so a listing with no usable apply_url or apply_email is
     * skipped, as is one whose source_url was already posted. Skips answer
     * 200 so the scraper simply moves on to its next job.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'source_url' => 'required|url|max:2048',
            'category' => 'nullable|string|max:255',
            'role' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'job_type' => 'nullable|string|max:255',
            'experience' => 'nullable|string|max:255',
            'education' => 'nullable|string|max:255',
            'salary_type' => 'nullable|string|max:255',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => ['nullable', 'numeric', 'min:0', Rule::when($request->filled('min_salary'), 'gte:min_salary')],
            'custom_salary' => 'nullable|string|max:255',
            'vacancies' => 'nullable|integer|min:1',
            'deadline' => 'nullable|date|after:today',
            // Checked below instead: a bad apply link skips the job, it is not
            // a malformed request.
            'apply_url' => 'nullable|string|max:2048',
            'apply_email' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'is_remote' => 'nullable|boolean',
            'skills' => 'nullable|array',
            'skills.*' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $applyUrl = filter_var(trim((string) $request->apply_url), FILTER_VALIDATE_URL) ?: null;
        $applyEmail = filter_var(trim((string) $request->apply_email), FILTER_VALIDATE_EMAIL) ?: null;

        if (! $applyUrl && ! $applyEmail) {
            return response()->json([
                'status' => true,
                'created' => false,
                'skipped' => true,
                'message' => 'Skipped: no valid apply_url or apply_email.',
            ], 200);
        }

        $request->merge(['apply_url' => $applyUrl, 'apply_email' => $applyEmail]);

        $company = Company::find(config('services.scraper.company_id'));

        if (! $company) {
            return response()->json([
                'status' => false,
                'message' => 'Scraper company is not configured on the server.',
            ], 500);
        }

        $urlHash = sha1($request->source_url);

        if ($existing = $this->alreadyPosted($urlHash)) {
            return $existing;
        }

        try {
            $job = DB::transaction(function () use ($request, $company, $urlHash) {
                $job = Job::create($this->jobAttributes($request, $company));

                $this->jobSkillsInsert($request->skills, $job);

                // The observer may already have logged this job if the company
                // tracks postings, so update that row rather than add a second.
                JobPostingLog::updateOrCreate(['job_id' => $job->id], [
                    'company_id' => $company->id,
                    'team_member_id' => $this->scraperMember()->id,
                    'job_source_id' => $request->filled('source')
                        ? JobSource::firstOrCreate(['name' => trim($request->source)])->id
                        : null,
                    'source_note' => mb_substr($request->source_url, 0, 255),
                    'source_url' => $request->source_url,
                    'source_url_hash' => $urlHash,
                    'ip_address' => $request->ip(),
                    'user_agent' => mb_substr((string) $request->userAgent(), 0, 255),
                ]);

                return $job;
            });
        } catch (QueryException $e) {
            // Two requests for the same listing raced past the check above and
            // the unique index stopped the second one.
            if ($e->getCode() === '23000' && ($existing = $this->alreadyPosted($urlHash))) {
                return $existing;
            }

            throw $e;
        }

        return response()->json([
            'status' => true,
            'created' => true,
            'message' => 'Job posted and waiting for admin approval.',
            'data' => [
                'job_id' => $job->id,
                'slug' => $job->slug,
                'status' => $job->status,
            ],
        ], 201);
    }

    // ─────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────

    protected function alreadyPosted(string $urlHash): ?JsonResponse
    {
        $log = JobPostingLog::where('source_url_hash', $urlHash)->first(['job_id']);

        if (! $log) {
            return null;
        }

        return response()->json([
            'status' => true,
            'created' => false,
            'message' => 'This job was already posted.',
            'data' => [
                'job_id' => $log->job_id,
            ],
        ], 200);
    }

    protected function jobAttributes(Request $request, Company $company): array
    {
        $country = $request->country ?: 'Pakistan';
        $city = $request->city;

        if ($request->filled('min_salary') || $request->filled('max_salary')) {
            $salaryMode = 'range';
        } else {
            $salaryMode = 'custom';
        }

        // store() has already made sure at least one of the two is set.
        $applyOn = $request->filled('apply_url') ? 'custom_url' : 'email';

        $deadline = $request->filled('deadline')
            ? Carbon::parse($request->deadline)
            : now()->addDays((int) setting('job_deadline_expiration_limit') ?: 30);

        return [
            'title' => $request->title,
            'description' => $request->description,
            'company_id' => $company->id,
            'category_id' => $this->categoryId($request->category),
            'role_id' => $this->roleId($request->role ?: 'Other'),
            'job_type_id' => $this->lookupId(JobTypeTranslation::class, 'job_type_id', $request->job_type, 'Full Time'),
            'experience_id' => $this->lookupId(ExperienceTranslation::class, 'experience_id', $request->experience, 'Fresher'),
            'education_id' => $this->lookupId(EducationTranslation::class, 'education_id', $request->education, 'Any'),
            'salary_type_id' => $this->lookupId(SalaryTypeTranslation::class, 'salary_type_id', $request->salary_type, 'Monthly'),
            'salary_mode' => $salaryMode,
            'custom_salary' => $salaryMode === 'custom' ? ($request->custom_salary ?: 'Competitive') : null,
            'min_salary' => $request->min_salary,
            'max_salary' => $request->max_salary,
            'vacancies' => $request->vacancies ?: 1,
            'deadline' => $deadline->format('Y-m-d'),
            'apply_on' => $applyOn,
            'apply_url' => $request->apply_url,
            'apply_email' => $request->apply_email,
            'is_remote' => $request->boolean('is_remote'),
            'country' => $country,
            'region' => $country,
            'district' => $city,
            'place' => $city,
            'exact_location' => $city ? "{$city},{$country},{$country}" : $country,
            'status' => 'pending',
        ];
    }

    /**
     * Match a category by id or name. Scraped text never becomes a new
     * category; anything unknown goes under "Others", which is only created
     * if the portal does not have it at all.
     */
    protected function categoryId(?string $value): int
    {
        if (filled($value) && ($found = $this->findTranslation(JobCategoryTranslation::class, 'job_category_id', $value))) {
            return $found;
        }

        if ($found = $this->findTranslation(JobCategoryTranslation::class, 'job_category_id', self::FALLBACK_CATEGORY)) {
            return $found;
        }

        $category = JobCategory::create(['name' => self::FALLBACK_CATEGORY]);
        foreach (loadLanguage() as $language) {
            $category->translateOrNew($language->code)->name = self::FALLBACK_CATEGORY;
        }
        $category->save();

        return $category->id;
    }

    protected function roleId(string $value): int
    {
        if ($found = $this->findTranslation(JobRoleTranslation::class, 'job_role_id', $value)) {
            return $found;
        }

        $role = JobRole::create(['name' => $value]);
        foreach (loadLanguage() as $language) {
            $role->translateOrNew($language->code)->name = $value;
        }
        $role->save();

        return $role->id;
    }

    /**
     * Match a fixed list (job type, experience, ...) by id or name. These are
     * never created from scraped text; anything unknown falls back to the
     * default, and failing that to the first entry.
     */
    protected function lookupId(string $translation, string $key, ?string $value, string $default): int
    {
        foreach (array_filter([$value, $default]) as $candidate) {
            if ($found = $this->findTranslation($translation, $key, $candidate)) {
                return $found;
            }
        }

        return $translation::orderBy($key)->value($key);
    }

    /**
     * Only an all-digit value is treated as an id. MySQL would otherwise cast
     * a name like "2 Years" to the number 2 and match the wrong row.
     */
    protected function findTranslation(string $translation, string $key, string $value): ?int
    {
        $value = trim($value);

        if (ctype_digit($value) && ($id = $translation::where($key, $value)->value($key))) {
            return $id;
        }

        return $translation::where('name', $value)->value($key);
    }

    /**
     * The "Scraper" entry in team members. It is created inactive so it never
     * shows up in the "posted by" dropdown the team picks from.
     */
    protected function scraperMember(): TeamMember
    {
        return TeamMember::firstOrCreate(['name' => self::POSTED_BY], ['is_active' => false]);
    }
}
