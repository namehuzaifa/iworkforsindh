@extends('frontend.layouts.app')

@section('title', __('All Counseling Bookings'))

@section('main')
    <div class="dashboard-wrapper">
        <div class="container">
            <div class="row">
                <x-website.counselor.sidebar />
                <div class="col-lg-9">
                    <div class="dashboard-right tw-ps-0 lg:tw-ps-5">
                        <div class="dashboard-right-header">
                            <div class="left-text">
                                <h5>{{ __('All Counseling Bookings') }}</h5>
                                <p class="m-0">{{ __('View all bookings across all your counseling sessions') }}</p>
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
                                        <th>{{ __('Session') }}</th>
                                        <th>{{ __('Date & Time') }}</th>
                                        <th>{{ __('Status') }}</th>
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
                                                    <span class="tw-font-medium">{{ $booking->counselingSession->title }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="tw-font-medium">{{ $booking->booking_date->format('d M, Y') }}</span>
                                                    <br>
                                                    <small>
                                                        {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} -
                                                        {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                                                    </small>
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
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" class="text-center">
                                                <x-svg.not-found-icon />
                                                <p class="mt-4">{{ __('No bookings found') }}</p>
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
