@extends('frontend.layouts.app')

@section('title', __('Reschedule Booking') . ' - ' . $session->title)

    @section('main')
        <div class="breadcrumbs breadcrumbs-height">
            <div class="container">
                <div class="breadcrumb-menu">
                    <h6 class="f-size-18 m-0">{{ __('Reschedule Booking') }}</h6>
                    <ul>
                        <li><a href="{{ route('website.home') }}">{{ __('home') }}</a></li>
                        <li>/</li>
                        <li><a href="{{ route('candidate.counseling.bookings') }}">{{ __('My Bookings') }}</a></li>
                        <li>/</li>
                        <li>{{ __('Reschedule') }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="dashboard-wrapper">
            <div class="container">
                <div class="row">
                    @if (auth()->check() && auth()->user()->role == 'candidate')
                        <x-website.candidate.sidebar />
                    @endif
                    <div class="{{ auth()->check() && auth()->user()->role == 'candidate' ? 'col-lg-9' : 'col-12' }}">
                        <div class="dashboard-right">
                            <div class="row">
                    {{-- Session Details --}}
                    <div class="col-lg-5 tw-mb-4">
                        <div class="tw-border tw-rounded-xl tw-bg-white tw-p-6 tw-shadow-sm">
                            <h5 class="tw-font-bold tw-mb-4">{{ __('Current Booking Details') }}</h5>
                            <div class="tw-bg-gray-50 tw-p-4 tw-rounded-lg tw-mb-4">
                                <p class="tw-mb-2"><strong>{{ __('Session:') }}</strong> {{ $session->title }}</p>
                                <p class="tw-mb-2"><strong>{{ __('Date:') }}</strong>
                                    {{ $booking->booking_date->format('d M, Y (l)') }}</p>
                                <p class="tw-mb-0"><strong>{{ __('Time:') }}</strong>
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} -
                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                                </p>
                            </div>

                            <h6 class="tw-font-semibold tw-mb-2">{{ __('Available Days') }}</h6>
                            <div class="tw-space-y-2">
                                @foreach($session->schedules as $schedule)
                                    <div
                                        class="tw-flex tw-justify-between tw-items-center tw-bg-gray-50 tw-rounded-lg tw-px-3 tw-py-2">
                                        <span class="tw-font-medium tw-text-sm">{{ $schedule->day_name }}</span>
                                        <span class="tw-text-sm tw-text-gray-600">
                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} -
                                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Slot Booking --}}
                    <div class="col-lg-7 tw-mb-4">
                        <div class="tw-border tw-rounded-xl tw-bg-white tw-p-6 tw-shadow-sm">
                            <h5 class="tw-font-bold tw-mb-4">
                                <i class="ph-calendar-plus tw-mr-1"></i> {{ __('Select New Slot') }}
                            </h5>

                            {{-- Date Picker --}}
                            <div class="tw-mb-4">
                                <label class="tw-font-medium tw-text-sm tw-mb-2 tw-block">{{ __('Select a New Date') }}</label>
                                <input type="text" id="booking-date" class="form-control" placeholder="YYYY-MM-DD" autocomplete="off" readonly style="background-color: white;"
                                    value="{{ $booking->booking_date->format('Y-m-d') }}">
                            </div>

                            {{-- Slots Container --}}
                            <div id="slots-container" style="display: none;">
                                <label class="tw-font-medium tw-text-sm tw-mb-2 tw-block">{{ __('Available Slots') }}</label>
                                <div id="slots-grid" class="tw-grid tw-grid-cols-3 sm:tw-grid-cols-4 tw-gap-2 tw-mb-4">
                                    {{-- Slots loaded via AJAX --}}
                                </div>
                            </div>

                            <div id="slots-loading" style="display: none;" class="tw-text-center tw-py-4">
                                <div class="spinner-border spinner-border-sm tw-text-primary" role="status"></div>
                                <span class="tw-ml-2">{{ __('Loading slots...') }}</span>
                            </div>

                            <div id="no-slots-message" style="display: none;" class="tw-text-center tw-py-4 tw-text-gray-500">
                                <i class="ph-calendar-x tw-text-3xl tw-mb-2 tw-block"></i>
                                <p id="no-slots-text">{{ __('No available slots for this date') }}</p>
                            </div>

                            {{-- Booking Form --}}
                            <form id="booking-form" action="{{ route('candidate.counseling.booking.update', $booking) }}"
                                method="POST" style="display: none;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="date" id="form-date">
                                <input type="hidden" name="start_time" id="form-start-time">
                                <input type="hidden" name="end_time" id="form-end-time">

                                <div class="tw-mb-3">
                                    <div id="selected-slot-display"
                                        class="tw-bg-blue-50 tw-border tw-border-blue-200 tw-rounded-lg tw-p-3 tw-text-center">
                                        <span class="tw-text-blue-700 tw-font-medium" id="selected-slot-text"></span>
                                    </div>
                                </div>

                                <div class="tw-mb-4">
                                    <label class="tw-font-medium tw-text-sm tw-mb-1">{{ __('Update Notes (optional)') }}</label>
                                    <textarea name="notes" class="form-control"
                                        rows="2">{{ old('notes', $booking->notes) }}</textarea>
                                </div>

                                <button type="submit" class="btn btn-primary tw-w-full">
                                    <i class="ph-check-circle tw-mr-1"></i> {{ __('Confirm Reschedule') }}
                                </button>
                            </form>
                        </div>
                    </div>
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

@section('css')
    <link rel="stylesheet" href="{{ asset('frontend') }}/assets/css/bootstrap-datepicker.min.css">
    <style>
        /* Light highlight for current/today date */
        body .datepicker-dropdown table tr td.today,
        body .datepicker-dropdown table tr td.today:hover,
        body .datepicker-dropdown table tr td.today.disabled,
        body .datepicker-dropdown table tr td.today.disabled:hover {
            background-image: none !important;
            background-color: #e0f2fe !important; /* sky-100 */
            color: #0369a1 !important; /* sky-700 */
            font-weight: bold !important;
        }

        /* Highlight currently active/selected date */
        body .datepicker-dropdown table tr td.active,
        body .datepicker-dropdown table tr td.active.highlighted,
        body .datepicker table tr td.active {
            background-image: none !important;
            background-color: #2563eb !important; /* blue-600 */
            color: #ffffff !important;
            border-color: #1d4ed8 !important;
        }

        /* Dim disabled/past dates */
        body .datepicker-dropdown table tr td.disabled,
        body .datepicker-dropdown table tr td.disabled:hover,
        body .datepicker table tr td.disabled {
            opacity: 0.2 !important;
            cursor: not-allowed !important;
            color: #cbd5e1 !important; /* slate-300 */
            background-color: #f8fafc !important; /* slate-50 */
            text-decoration: none !important;
        }
    </style>
@endsection

@section('script')
    <script src="{{ asset('frontend/assets/js/bootstrap-datepicker.min.js') }}"></script>
    <script>
        const availableDays = @json($availableDays).map(Number);
        const slotsUrl = "{{ route('counseling.slots') }}";
        const csrfToken = "{{ csrf_token() }}";

        // Initialize datepicker
        $('#booking-date').datepicker({
            format: 'yyyy-mm-dd',
            startDate: new Date(),
            autoclose: true,
            todayHighlight: true
        }).on('changeDate', function(e) {
            // Trigger the change event manually to load slots
            document.getElementById('booking-date').dispatchEvent(new Event('change'));
        });

        // Handle date change
        document.getElementById('booking-date').addEventListener('change', function () {
            const dateStr = this.value;
            if (!dateStr) return;

            // Load slots for the selected date
            document.getElementById('slots-loading').style.display = 'block';
            document.getElementById('slots-container').style.display = 'none';
            document.getElementById('booking-form').style.display = 'none';
            document.getElementById('no-slots-message').style.display = 'none';

            fetch(slotsUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    session_id: {{ $session->id }},
                    date: dateStr,
                })
            })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('slots-loading').style.display = 'none';

                    if (data.slots && data.slots.length > 0) {
                        const grid = document.getElementById('slots-grid');
                        grid.innerHTML = '';

                        let hasAvailable = false;
                        data.slots.forEach(slot => {
                            // If this slot is the CURRENT booked slot, make it selectable and available
                            const isCurrentSlot = (dateStr === "{{ $booking->booking_date->format('Y-m-d') }}" && slot.start_time === "{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}");
                            const isBooked = slot.is_booked && !isCurrentSlot;

                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = isBooked
                                ? 'btn btn-sm btn-outline-secondary tw-opacity-50'
                                : 'btn btn-sm btn-outline-primary slot-btn';
                            btn.disabled = isBooked;
                            btn.innerHTML = slot.start_time + ' - ' + slot.end_time;

                            if (isCurrentSlot) {
                                btn.innerHTML += ' <small>(Current)</small>';
                            } else if (isBooked) {
                                btn.innerHTML += ' <small>(Booked)</small>';
                            }

                            btn.dataset.start = slot.start_time;
                            btn.dataset.end = slot.end_time;

                            if (!isBooked) {
                                hasAvailable = true;
                                btn.addEventListener('click', function () {
                                    selectSlot(this, dateStr);
                                });
                            }

                            grid.appendChild(btn);
                        });

                        document.getElementById('slots-container').style.display = 'block';

                        if (!hasAvailable) {
                            document.getElementById('no-slots-message').style.display = 'block';
                            document.getElementById('no-slots-text').textContent = 'All other slots are booked for this date.';
                        }
                    } else {
                        document.getElementById('no-slots-message').style.display = 'block';
                        document.getElementById('no-slots-text').textContent = 'No available slots for this date.';
                    }
                })
                .catch(error => {
                    document.getElementById('slots-loading').style.display = 'none';
                    document.getElementById('no-slots-message').style.display = 'block';
                    document.getElementById('no-slots-text').textContent = 'Error loading slots. Please try again.';
                });
        });

        function selectSlot(btn, date) {
            // Remove active class from all slot buttons
            document.querySelectorAll('.slot-btn').forEach(b => {
                b.classList.remove('btn-primary');
                b.classList.add('btn-outline-primary');
            });

            // Add active class to selected
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-primary');

            // Fill form
            document.getElementById('form-date').value = date;
            document.getElementById('form-start-time').value = btn.dataset.start;
            document.getElementById('form-end-time').value = btn.dataset.end;
            document.getElementById('selected-slot-text').textContent =
                'Selected New Slot: ' + date + ' | ' + btn.dataset.start + ' - ' + btn.dataset.end;

            // Show booking form
            document.getElementById('booking-form').style.display = 'block';
        }
    </script>
@endsection
