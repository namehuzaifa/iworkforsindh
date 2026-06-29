@extends('frontend.layouts.app')

@section('title', __('Session Bookings') . ' - ' . $session->title)

@section('main')
    <div class="dashboard-wrapper">
        <div class="container">
            <div class="row">
                <x-website.counselor.sidebar />
                <div class="col-lg-9">
                    <div class="dashboard-right tw-ps-0 lg:tw-ps-5">
                        <div class="dashboard-right-header">
                            <div class="left-text">
                                <h5>{{ __('Bookings') }}: {{ $session->title }}</h5>
                                <p class="m-0">{{ __('View who has booked this counseling session') }}</p>
                            </div>
                            <span class="sidebar-open-nav">
                                <i class="ph-list"></i>
                            </span>
                        </div>

                        <div class="tw-mb-3">
                            <a href="{{ route('counselor.counseling.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="ph-arrow-left tw-mr-1"></i> {{ __('Back to Sessions') }}
                            </a>
                        </div>

                        <div class="db-job-card-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>{{ __('Candidate') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Time') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Notes') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($bookings->count() > 0)
                                        @foreach($bookings as $booking)
                                            <tr>
                                                <td>
                                                    <div class="tw-flex tw-items-center tw-gap-2">
                                                        <img src="{{ $booking->candidate->user->image_url }}" alt=""
                                                            class="tw-w-8 tw-h-8 tw-rounded-full tw-object-cover">
                                                        <div>
                                                            <span
                                                                class="tw-font-medium">{{ $booking->candidate->user->name }}</span>
                                                            <br>
                                                            <small
                                                                class="tw-text-gray-500">{{ $booking->candidate->user->email }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span
                                                        class="tw-font-medium">{{ $booking->booking_date->format('d M, Y') }}</span>
                                                    <br>
                                                    <small
                                                        class="tw-text-gray-500">{{ $booking->booking_date->format('l') }}</small>
                                                </td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} -
                                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                                                </td>
                                                <td>
                                                    @if($booking->status == 'confirmed')
                                                        <span
                                                            class="tw-px-2 tw-py-1 tw-bg-green-100 tw-text-green-700 tw-rounded-full tw-text-xs tw-font-medium">
                                                            {{ __('Confirmed') }}
                                                        </span>
                                                    @elseif($booking->status == 'pending')
                                                        <span
                                                            class="tw-px-2 tw-py-1 tw-bg-yellow-100 tw-text-yellow-700 tw-rounded-full tw-text-xs tw-font-medium">
                                                            {{ __('Pending') }}
                                                        </span>
                                                    @elseif($booking->status == 'cancelled')
                                                        <span
                                                            class="tw-px-2 tw-py-1 tw-bg-red-100 tw-text-red-700 tw-rounded-full tw-text-xs tw-font-medium">
                                                            {{ __('Cancelled') }}
                                                        </span>
                                                    @elseif($booking->status == 'completed')
                                                        <span
                                                            class="tw-px-2 tw-py-1 tw-bg-blue-100 tw-text-blue-700 tw-rounded-full tw-text-xs tw-font-medium">
                                                            {{ __('Completed') }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small>{{ $booking->notes ?? '-' }}</small>
                                                </td>
                                                <td>
                                                    @if(in_array($booking->status, ['pending', 'confirmed']))
                                                        <div class="tw-flex tw-gap-1">
                                                            <form action="{{ route('counselor.counseling.booking.complete', $booking) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-outline-success"
                                                                    title="{{ __('Mark Complete') }}">
                                                                    <i class="ph-check"></i>
                                                                </button>
                                                            </form>
                                                            <form action="{{ route('counselor.counseling.booking.cancel', $booking) }}"
                                                                method="POST" class="d-inline"
                                                                onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                    title="{{ __('Cancel Booking') }}">
                                                                    <i class="ph-x"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @else
                                                        <span class="tw-text-gray-400 tw-text-sm">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center">
                                                <x-svg.not-found-icon />
                                                <p class="mt-4">{{ __('No bookings yet for this session') }}</p>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
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
