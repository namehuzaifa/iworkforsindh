@extends('frontend.layouts.app')

@section('title', __('counselor_dashboard'))

@section('main')
    <div class="dashboard-wrapper">
        <div class="container">
            <div class="row">
                <x-website.counselor.sidebar />
                <div class="col-lg-9">
                    <div class="dashboard-right tw-ps-0 lg:tw-ps-5">
                        <div class="dashboard-right-header">
                            <div class="left-text">
                                <h5>{{ __('hello') }}, {{ ucfirst(auth()->user()->name) }}</h5>
                                <p class="m-0">Welcome to your Counselor Dashboard</p>
                            </div>
                            <span class="sidebar-open-nav">
                                <i class="ph-list"></i>
                            </span>
                        </div>
                        <div class="row">
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="single-feature-box">
                                    <div class="single-feature-data">
                                        <h6 class="tw-text-[#18191C] tw-text-2xl tw-font-semibold">{{ $totalSessions }}</h6>
                                        <p>Total Sessions</p>
                                    </div>
                                    <div class="single-feature-icon">
                                        <i class="ph-chalkboard-teacher"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="single-feature-box">
                                    <div class="single-feature-data">
                                        <h6 class="tw-text-[#18191C] tw-text-2xl tw-font-semibold">{{ $activeSessions }}</h6>
                                        <p>Active Sessions</p>
                                    </div>
                                    <div class="single-feature-icon">
                                        <i class="ph-check-circle"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="single-feature-box bg-success-50">
                                    <div class="single-feature-data">
                                        <h6 class="tw-text-[#18191C] tw-text-2xl tw-font-semibold">{{ $totalBookings }}</h6>
                                        <p>Total Bookings</p>
                                    </div>
                                    <div class="single-feature-icon">
                                        <i class="ph-calendar-check text-success-500"></i>
                                    </div>
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
