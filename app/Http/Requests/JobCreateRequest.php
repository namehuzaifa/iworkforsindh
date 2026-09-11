<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JobCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'category_id' => 'required',
            'role_id' => 'required',
            'experience' => 'required',
            'education' => 'required',
            'job_type' => 'required',
            'vacancies' => 'required',
            'salary_mode' => 'required',
            'custom_salary' => 'required_if:salary_mode,==,custom',
            'min_salary' => 'nullable|numeric',
            'max_salary' => 'nullable|numeric',
            'salary_type' => 'required',
            'deadline' => 'required|date',
            'description' => 'required|string|min:50',
            'featured' => 'nullable|numeric',
            'is_remote' => 'nullable|numeric',
            'apply_on' => 'required',
            'location' => $this->method() == 'PUT' ? '' : Rule::requiredIf(! session('location')),

            // Only the in-house accounts record who posted and where the job
            // came from; for every other company these stay optional and the
            // fields are not even rendered.
            'team_member_id' => [Rule::requiredIf($this->tracksJobPosting()), 'nullable', 'exists:team_members,id'],
            'job_source_id' => [Rule::requiredIf($this->tracksJobPosting()), 'nullable', 'exists:job_sources,id'],
            'source_note' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'team_member_id.required' => __('Please select who is posting this job.'),
            'job_source_id.required' => __('Please select where this job came from.'),
        ];
    }

    protected function tracksJobPosting(): bool
    {
        return (bool) optional(currentCompany())->is_job_tracking;
    }
}
