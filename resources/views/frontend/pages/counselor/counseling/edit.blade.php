@extends('frontend.layouts.app')

@section('title', __('Edit Counseling Session'))

@section('main')
    <div class="dashboard-wrapper">
        <div class="container">
            <div class="row">
                <x-website.counselor.sidebar />
                <div class="col-lg-9">
                    <div class="dashboard-right tw-ps-0 lg:tw-ps-5">
                        <div class="dashboard-right-header">
                            <div class="left-text">
                                <h5>{{ __('Edit Counseling Session') }}</h5>
                                <p class="m-0">{{ $session->title }}</p>
                            </div>
                            <span class="sidebar-open-nav">
                                <i class="ph-list"></i>
                            </span>
                        </div>

                        <div class="tw-bg-white tw-rounded-lg tw-border tw-p-6">
                            <form action="{{ route('counselor.counseling.update', $session) }}" method="POST">
                                @csrf
                                @method('PUT')

                                {{-- Session Info --}}
                                <div class="row tw-mb-4">
                                    <div class="col-12">
                                        <h6 class="tw-font-semibold tw-mb-3">
                                            <i class="ph-info tw-mr-1"></i> {{ __('Session Information') }}
                                        </h6>
                                    </div>
                                    <div class="col-md-12 tw-mb-3">
                                        <label class="tw-font-medium tw-text-sm tw-mb-1">{{ __('Category') }} <span class="tw-text-red-500">*</span></label>
                                        <select name="counseling_category_id" class="form-control @error('counseling_category_id') is-invalid @enderror" required>
                                            <option value="">{{ __('Select Category') }}</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ (old('counseling_category_id') ?? $session->counseling_category_id) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('counseling_category_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 tw-mb-3">
                                        <label class="tw-font-medium tw-text-sm tw-mb-1">{{ __('Session Title') }} <span class="tw-text-red-500">*</span></label>
                                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                               value="{{ old('title', $session->title) }}" required>
                                        @error('title')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 tw-mb-3">
                                        <label class="tw-font-medium tw-text-sm tw-mb-1">{{ __('Fee (Rs.)') }}</label>
                                        <input type="number" name="fee" class="form-control @error('fee') is-invalid @enderror"
                                               value="{{ old('fee', $session->fee) }}" min="0" step="1">
                                        @error('fee')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-12 tw-mb-3">
                                        <label class="tw-font-medium tw-text-sm tw-mb-1">{{ __('Description') }}</label>
                                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                                  rows="3">{{ old('description', $session->description) }}</textarea>
                                        @error('description')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Zoom Details --}}
                                <div class="row tw-mb-4">
                                    <div class="col-12">
                                        <h6 class="tw-font-semibold tw-mb-3">
                                            <i class="ph-video-camera tw-mr-1"></i> {{ __('Zoom Meeting Details') }}
                                        </h6>
                                    </div>
                                    <div class="col-md-12 tw-mb-3">
                                        <label class="tw-font-medium tw-text-sm tw-mb-1">{{ __('Zoom Meeting Link') }} <span class="tw-text-red-500">*</span></label>
                                        <input type="url" name="zoom_link" class="form-control @error('zoom_link') is-invalid @enderror"
                                               value="{{ old('zoom_link', $session->zoom_link) }}" required>
                                        @error('zoom_link')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 tw-mb-3">
                                        <label class="tw-font-medium tw-text-sm tw-mb-1">{{ __('Zoom Meeting ID') }}</label>
                                        <input type="text" name="zoom_meeting_id" class="form-control"
                                               value="{{ old('zoom_meeting_id', $session->zoom_meeting_id) }}">
                                    </div>
                                    <div class="col-md-6 tw-mb-3">
                                        <label class="tw-font-medium tw-text-sm tw-mb-1">{{ __('Zoom Passcode') }}</label>
                                        <input type="text" name="zoom_passcode" class="form-control"
                                               value="{{ old('zoom_passcode', $session->zoom_passcode) }}">
                                    </div>
                                </div>

                                {{-- Schedule --}}
                                <div class="row tw-mb-4">
                                    <div class="col-12">
                                        <h6 class="tw-font-semibold tw-mb-3">
                                            <i class="ph-calendar tw-mr-1"></i> {{ __('Available Schedule') }}
                                        </h6>
                                        <p class="tw-text-sm tw-text-gray-500 tw-mb-3">
                                            {{ __('Select the days you are available and set time ranges. 30-minute slots will be automatically generated.') }}
                                        </p>
                                        @error('days')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    @php
                                        $oldDays = old('days', array_keys($scheduleData));
                                    @endphp

                                    @foreach($days as $dayNum => $dayName)
                                        <div class="col-12 tw-mb-3">
                                            <div class="tw-border tw-rounded-lg tw-p-3">
                                                <div class="tw-flex tw-items-center tw-flex-wrap tw-gap-3">
                                                    <div class="form-check tw-min-w-[120px]">
                                                        <input type="checkbox" name="days[]" value="{{ $dayNum }}"
                                                               id="day_{{ $dayNum }}" class="form-check-input day-checkbox"
                                                               data-day="{{ $dayNum }}"
                                                               {{ in_array($dayNum, $oldDays) ? 'checked' : '' }}>
                                                        <label class="form-check-label tw-font-medium" for="day_{{ $dayNum }}">
                                                            {{ $dayName }}
                                                        </label>
                                                    </div>
                                                    <div class="tw-flex tw-items-center tw-gap-2 time-fields" id="time_fields_{{ $dayNum }}"
                                                         style="{{ in_array($dayNum, $oldDays) ? '' : 'display:none' }}">
                                                        <label class="tw-text-sm tw-text-gray-600">{{ __('From') }}</label>
                                                        <input type="time" name="start_time[{{ $dayNum }}]"
                                                               class="form-control form-control-sm tw-w-auto"
                                                               value="{{ old('start_time.' . $dayNum, $scheduleData[$dayNum]['start_time'] ?? '09:00') }}">
                                                        <label class="tw-text-sm tw-text-gray-600">{{ __('To') }}</label>
                                                        <input type="time" name="end_time[{{ $dayNum }}]"
                                                               class="form-control form-control-sm tw-w-auto"
                                                               value="{{ old('end_time.' . $dayNum, $scheduleData[$dayNum]['end_time'] ?? '17:00') }}">
                                                        <span class="tw-text-xs tw-text-gray-400">(30 min slots)</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="tw-flex tw-gap-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ph-check-circle tw-mr-1"></i> {{ __('Update Session') }}
                                    </button>
                                    <a href="{{ route('counselor.counseling.index') }}" class="btn btn-outline-secondary">
                                        {{ __('Cancel') }}
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="dashboard-footer text-center body-font-4 text-gray-500">
            <x-website.footer-copyright />
        </div>
    </div>
@endsection

@section('script')
<script>
    document.querySelectorAll('.day-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            var dayNum = this.dataset.day;
            var timeFields = document.getElementById('time_fields_' + dayNum);
            timeFields.style.display = this.checked ? 'flex' : 'none';
        });
    });
</script>
@endsection
