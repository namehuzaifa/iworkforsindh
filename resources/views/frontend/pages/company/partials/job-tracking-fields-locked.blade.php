{{-- Edit form counterpart of job-tracking-fields. Who posted the job and where
     it came from are recorded once, when the job is posted, so here they are
     only shown. The selects are disabled and have no name, so nothing from
     them is submitted with the update. --}}
@if (! empty($isJobTracking))
    <div class="post-job-item rt-mb-15 tw-w-full tw-overflow-hidden">
        <div class="row">
            <div class="col-lg-6 rt-mb-20">
                <x-forms.label name="posted_by" :required="false" class="tw-text-sm tw-mb-2" />
                <select class="form-control" disabled>
                    <option>{{ optional(optional($postingLog)->teamMember)->name ?? __('Not recorded') }}</option>
                </select>
            </div>
            <div class="col-lg-6 rt-mb-20">
                <x-forms.label name="job_source" :required="false" class="tw-text-sm tw-mb-2" />
                <select class="form-control" disabled>
                    <option>{{ optional(optional($postingLog)->source)->name ?? __('Not recorded') }}</option>
                </select>
            </div>
            @if (optional($postingLog)->source_note)
                <div class="col-lg-12 rt-mb-20">
                    <x-forms.label name="source_note" :required="false" class="tw-text-sm tw-mb-2" />
                    <input value="{{ $postingLog->source_note }}" class="form-control" type="text" disabled>
                </div>
            @endif
        </div>
    </div>
@endif
