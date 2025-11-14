<?php

namespace App\Services\API\Website\Company\PostingJob;

use App\Http\Traits\CompanyJobTrait;
use App\Http\Traits\Jobable;
use App\Models\Admin;
use App\Models\Education;
use App\Models\EducationTranslation;
use App\Models\Experience;
use App\Models\ExperienceTranslation;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobCategoryTranslation;
use App\Models\JobRole;
use App\Models\JobRoleTranslation;
use App\Notifications\Admin\NewJobAvailableNotification;
use App\Notifications\Website\Company\JobCreatedNotification;
use Carbon\Carbon;
use F9Web\ApiResponseHelpers;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Language\Entities\Language;
use Illuminate\Support\Str;

class StoreJobService
{
    // use ApiResponseHelpers, CompanyJobTrait, Jobable;
    use ApiResponseHelpers;

    public function execute($request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'category' => 'required',
            'job_role' => 'required',
            // 'experience' => 'required',
            'education' => 'required',
            // 'job_type' => 'required',
            'vacancies' => 'required',
            // 'salary_mode' => 'required',
            // 'custom_salary' => 'required_if:salary_mode,==,custom',
            // 'min_salary' => 'nullable|numeric',
            // 'max_salary' => 'nullable|numeric',
            // 'salary_type' => 'required',
            'deadline' => 'required|date',
            'description' => 'required',
            'featured' => 'nullable|numeric',
            'is_remote' => 'nullable',
            'apply_on' => 'required',
            //   'location' => request()->method() == 'PUT' ? '' : Rule::requiredIf(!session('location'))
        ]);

        if ($validator->fails()) {
            return response()->json(
                ['errors' => $validator->messages()], 422
            );
        }

        $min = $request->min_salary;
        $max = $request->max_salary;

        $request->validate([
            'min_salary' => 'nullable|numeric|between:0,'.$max,
            'max_salary' => 'nullable|numeric|min:'.$min,
        ]);

        if ($request->apply_on === 'custom_url') {
            $request->validate([
                'apply_url' => 'required|url',
            ]);
        }
        if ($request->apply_on === 'email') {
            $request->validate([
                'apply_email' => 'required|email',
            ]);
        }

        // Highlight & featured
        $highlight = $request->badge == 'highlight' ? 1 : 0;
        $featured = $request->badge == 'featured' ? 1 : 0;

        // Job Category
        $job_category_request = $request->category;

        $job_category = JobCategoryTranslation::where('job_category_id', $job_category_request)->orWhere('name', $job_category_request)->first();
        if (! $job_category) {
            $new_job_category = JobCategory::create(['name' => $job_category_request]);

            $languages = Language::all();
            foreach ($languages as $language) {
                $new_job_category->translateOrNew($language->code)->name = $job_category_request;
            }
            $new_job_category->save();

            $job_category_id = $new_job_category->id;
        } else {
            $job_category_id = $job_category->job_category_id;
        }

        // Job Role
        $job_role_request = $request->job_role;

        $job_category = JobRoleTranslation::where('job_role_id', $job_role_request)->orWhere('name', $job_role_request)->first();

        if (! $job_category) {
            $new_job_role = JobRole::create(['name' => $job_role_request]);

            $languages = Language::all();
            foreach ($languages as $language) {
                $new_job_role->translateOrNew($language->code)->name = $job_role_request;
            }
            $new_job_role->save();

            $job_role_id = $new_job_role->id;
        } else {
            $job_role_id = $job_category->job_role_id;
        }

        // Education
        $education_request = $request->education;
        $education = EducationTranslation::where('education_id', $education_request)->orWhere('name', $education_request)->first();
        if (! $education) {
            // $education = Education::where('name', $education_request)->first();

            // if (! $education) {/
            // $education = Education::create(['name' => $education_request]);
            $skill = new Education;
            $skill->slug = Str::slug($education_request);
            $skill->save();
            $education = $skill;

            $skill->translateOrNew()->name = $education_request;
            $skill->save();
           
            // }
        }

        // Experience
        // $experience_request = $request->experience;
        $experience_request = 'Fresher';
        $experience = ExperienceTranslation::where('experience_id', $experience_request)->orWhere('name', $experience_request)->first();
        if (! $experience) {
            // $experience = Experience::where('name', $experience_request)->first();

            // if (! $experience) {
                // $experience = Experience::create(['name' => $experience_request]);

            $skill = new Experience;
            $skill->slug = Str::slug($experience_request);
            $skill->save();
            $experience = $skill;

            $skill->translateOrNew()->name = $experience_request;
            $skill->save();
            // }
        }

        // $deadline = Carbon::parse(now()
        //     ->addDays(setting('job_deadline_expiration_limit')))
        //     ->format('Y-m-d');
        $deadline = Carbon::parse($request->deadline)->format('Y-m-d');

        $jobCreated = Job::create([
            'title' => $request->title,
            'company_id' => auth('sanctum')->user()->company->id,
            'category_id' => $job_category_id,
            'role_id' => $job_role_id,
            'education_id' => $education->id,
            'experience_id' => $experience->id,
            // 'salary_mode' => $request->salary_mode,
            // 'custom_salary' => $request->custom_salary,
            'salary_mode' => 'custom',
            'custom_salary' => 'Market Competitive',
            // 'min_salary' => $request->min_salary,
            // 'max_salary' => $request->max_salary,
            // 'salary_type_id' => $request->salary_type,
            'salary_type_id' => 1,
            'deadline' => $deadline,
            // 'job_type_id' => $request->job_type,
            'job_type_id' => 6,
            'vacancies' => $request->vacancies,
            'apply_on' => $request->apply_on,
            'apply_email' => $request->apply_email ?? null,
            'apply_url' => $request->apply_url ?? null,
            'description' => $request->description,
            'featured' => $featured,
            'highlight' => $highlight,
            'is_remote' => $request->is_remote ?? 0,
            'country' => 'Pakistan',
            'exact_location' => 'Karachi Division,Pakistan,Pakistan',
            'address' => 'pakistan-pakistan',
            'status' => 'active',
            // 'status' => setting('job_auto_approved') ? 'active' : 'pending',
        ]);

        // Location
        updateMap($jobCreated);

        // Benefits
        $benefits = $request->benefits ?? null;
        if ($benefits) {
            $this->jobBenefitsInsert($request->benefits, $jobCreated);
        }

        // Tags
        $tags = $request->tags ?? null;
        if ($tags) {
            $this->jobTagsInsert($request->tags, $jobCreated);
        }

        if ($jobCreated) {
            $user_plan = auth('sanctum')->user()->company->userPlan()->first();

            $user_plan->job_limit = $user_plan->job_limit - 1;
            if ($featured) {
                $user_plan->featured_job_limit = $user_plan->featured_job_limit - 1;
            }
            if ($highlight) {
                $user_plan->highlight_job_limit = $user_plan->highlight_job_limit - 1;
            }
            // $user_plan->save();

            storePlanInformation();

            // Notification::send(auth('sanctum')->user(), new JobCreatedNotification($jobCreated));

            // if (checkMailConfig()) {
            //     // make notification to admins for approved
            //     $admins = Admin::all();
            //     foreach ($admins as $admin) {
            //         Notification::send($admin, new NewJobAvailableNotification($admin, $jobCreated));
            //     }
            // }
        }

        return $this->respondWithSuccess([
            'data' => [
                'job' => $jobCreated,
                'message' => __('job_created_successfully'),
            ],
        ]);
    }
}
