{{-- Only rendered for the in-house accounts the team posts from. Every other
     company never sees these fields and keeps posting exactly as before.

     Deliberately placed first on the form, with no pre-selected value, so the
     person posting makes a conscious choice every time — the computer is
     shared, so remembering the last choice would mis-attribute jobs. --}}
@if (! empty($isJobTracking))
    <div class="post-job-item rt-mb-15 tw-w-full tw-overflow-hidden">
        <div class="row">
            <div class="col-lg-6 rt-mb-20">
                <x-forms.label name="posted_by" :required="true" class="tw-text-sm tw-mb-2" />
                <select name="team_member_id" class="form-control @error('team_member_id') is-invalid @enderror">
                    <option value="">{{ __('Select your name') }}</option>
                    @foreach ($teamMembers as $member)
                        <option value="{{ $member->id }}" {{ old('team_member_id') == $member->id ? 'selected' : '' }}>
                            {{ $member->name }}
                        </option>
                    @endforeach
                </select>
                @error('team_member_id')
                    <span class="error invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-lg-6 rt-mb-20">
                <x-forms.label name="job_source" :required="true" class="tw-text-sm tw-mb-2" />
                <select name="job_source_id" class="form-control @error('job_source_id') is-invalid @enderror">
                    <option value="">{{ __('Select source') }}</option>
                    @foreach ($jobSources as $source)
                        <option value="{{ $source->id }}" {{ old('job_source_id') == $source->id ? 'selected' : '' }}>
                            {{ $source->name }}
                        </option>
                    @endforeach
                </select>
                @error('job_source_id')
                    <span class="error invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-lg-12 rt-mb-20">
                <x-forms.label name="source_note" :required="false" class="tw-text-sm tw-mb-2" />
                <input value="{{ old('source_note') }}" name="source_note"
                    class="form-control @error('source_note') is-invalid @enderror" type="text"
                    placeholder="{{ __('Link or note (optional)') }}">
                @error('source_note')
                    <span class="error invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
@endif
