@extends('frontend.layouts.app')

@section('title', __('My Counseling Bookings'))

@section('main')
    <div class="dashboard-wrapper">
        <div class="container">
            <div class="row">
                <x-website.candidate.sidebar />
                <div class="col-lg-9">
                    <div class="dashboard-right tw-ps-0 lg:tw-ps-5">
                        <div class="dashboard-right-header">
                            <div class="left-text">
                                <h5>{{ __('My Counseling Bookings') }}</h5>
                                <p class="m-0">{{ __('View and manage your counseling session bookings') }}</p>
                            </div>
                            <span class="sidebar-open-nav">
                                <i class="ph-list"></i>
                            </span>
                        </div>

                        {{-- Upcoming Bookings --}}
                        <div class="tw-mb-6">
                            <h6 class="tw-font-semibold tw-mb-3">
                                <i class="ph-calendar-check tw-mr-1 tw-text-green-500"></i>
                                {{ __('Upcoming Bookings') }} ({{ $upcomingBookings->count() }})
                            </h6>

                            @if($upcomingBookings->count() > 0)
                                <div class="tw-space-y-3">
                                    @foreach($upcomingBookings as $booking)
                                        <div class="tw-border tw-rounded-lg tw-p-4 tw-bg-white tw-shadow-sm">
                                            <div class="tw-flex tw-justify-between tw-items-start tw-flex-wrap tw-gap-3">
                                                <div class="tw-flex tw-items-center tw-gap-3">
                                                    <img src="{{ $booking->counselingSession->counselor->user->image_url }}"
                                                         alt="" class="tw-w-10 tw-h-10 tw-rounded-full tw-object-cover tw-border">
                                                    <div>
                                                        <h6 class="tw-font-semibold tw-m-0">{{ $booking->counselingSession->title }}</h6>
                                                        <small class="tw-text-gray-500">
                                                            {{ $booking->counselingSession->counselor->user->name }}
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="tw-text-right">
                                                    <span class="tw-px-2 tw-py-1 tw-rounded-full tw-text-xs tw-font-medium
                                                        {{ $booking->status == 'confirmed' ? 'tw-bg-green-100 tw-text-green-700' : 'tw-bg-yellow-100 tw-text-yellow-700' }}">
                                                        {{ ucfirst($booking->status) }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="tw-mt-3 tw-flex tw-flex-wrap tw-gap-4 tw-text-sm">
                                                <div class="tw-flex tw-items-center tw-gap-1 tw-text-gray-600">
                                                    <i class="ph-calendar tw-text-blue-500"></i>
                                                    {{ $booking->booking_date->format('d M, Y (l)') }}
                                                </div>
                                                <div class="tw-flex tw-items-center tw-gap-1 tw-text-gray-600">
                                                    <i class="ph-clock tw-text-blue-500"></i>
                                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} -
                                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                                                </div>
                                                <div class="tw-flex tw-items-center tw-gap-1 tw-text-gray-600">
                                                    <i class="ph-video-camera tw-text-blue-500"></i>
                                                    <a href="{{ $booking->counselingSession->zoom_link }}" target="_blank"
                                                       class="tw-text-blue-600 tw-underline">
                                                        {{ __('Join Zoom Meeting') }}
                                                    </a>
                                                </div>
                                            </div>

                                            @if($booking->counselingSession->zoom_meeting_id)
                                                <div class="tw-mt-2 tw-text-sm tw-text-gray-500">
                                                    <strong>Meeting ID:</strong> {{ $booking->counselingSession->zoom_meeting_id }}
                                                    @if($booking->counselingSession->zoom_passcode)
                                                        | <strong>Passcode:</strong> {{ $booking->counselingSession->zoom_passcode }}
                                                    @endif
                                                </div>
                                            @endif

                                            <div class="tw-mt-3">
                                                <div class="tw-flex tw-gap-2">
                                                    @if($booking->counselingSession->zoom_link)
                                                        <a href="{{ $booking->counselingSession->zoom_link }}"
                                                           target="_blank"
                                                           class="btn btn-sm btn-primary">
                                                            <i class="ph-video-camera tw-mr-1"></i> {{ __('Join') }}
                                                        </a>
                                                    @endif
                                                    
                                                    <a href="{{ route('candidate.counseling.booking.edit', $booking) }}"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="ph-pencil-simple tw-mr-1"></i> {{ __('Reschedule') }}
                                                    </a>

                                                    <form action="{{ route('candidate.counseling.booking.cancel', $booking) }}" method="POST"
                                                          onsubmit="return confirm('{{ __('Are you sure you want to cancel this booking?') }}')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="ph-x tw-mr-1"></i> {{ __('Cancel') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="tw-text-center tw-py-6 tw-bg-gray-50 tw-rounded-lg">
                                    <p class="tw-text-gray-500 tw-m-0">{{ __('No upcoming bookings') }}</p>
                                    <a href="{{ route('counseling.sessions') }}" class="btn btn-sm btn-primary tw-mt-2">
                                        {{ __('Browse Sessions') }}
                                    </a>
                                </div>
                            @endif
                        </div>

                        {{-- Past Bookings --}}
                        <div>
                            <h6 class="tw-font-semibold tw-mb-3">
                                <i class="ph-clock-counter-clockwise tw-mr-1 tw-text-gray-400"></i>
                                {{ __('Past / Cancelled Bookings') }} ({{ $pastBookings->count() }})
                            </h6>

                            @if($pastBookings->count() > 0)
                                <div class="db-job-card-table">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>{{ __('Session') }}</th>
                                                <th>{{ __('Date & Time') }}</th>
                                                <th>{{ __('Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pastBookings as $booking)
                                                <tr>
                                                    <td>
                                                        <span class="tw-font-medium">{{ $booking->counselingSession->title }}</span>
                                                        <br>
                                                        <small class="tw-text-gray-500">{{ $booking->counselingSession->counselor->user->name }}</small>
                                                    </td>
                                                    <td>
                                                        {{ $booking->booking_date->format('d M, Y') }}
                                                        <br>
                                                        <small>
                                                            {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} -
                                                            {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        @if($booking->status == 'cancelled')
                                                            <span class="tw-px-2 tw-py-1 tw-bg-red-100 tw-text-red-700 tw-rounded-full tw-text-xs tw-font-medium">
                                                                {{ __('Cancelled') }}
                                                            </span>
                                                        @elseif($booking->status == 'completed')
                                                            <span class="tw-px-2 tw-py-1 tw-bg-blue-100 tw-text-blue-700 tw-rounded-full tw-text-xs tw-font-medium">
                                                                {{ __('Completed') }}
                                                            </span>
                                                        @else
                                                            <span class="tw-px-2 tw-py-1 tw-bg-gray-100 tw-text-gray-700 tw-rounded-full tw-text-xs tw-font-medium">
                                                                {{ ucfirst($booking->status) }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="tw-text-center tw-py-4 tw-bg-gray-50 tw-rounded-lg">
                                    <p class="tw-text-gray-500 tw-m-0">{{ __('No past bookings') }}</p>
                                </div>
                            @endif
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
