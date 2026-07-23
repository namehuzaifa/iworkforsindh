@extends('frontend.layouts.app')

@section('title', __('Counseling Sessions'))

@section('main')
    <section class="main-banner">
        <img class="banner-img" src="https://iwork4sindh.com/images/careercounseling.png" alt="CareerCounceling Banner">
    </section>
    
    <!--<div class="breadcrumbs breadcrumbs-height">-->
    <!--    <div class="container">-->
    <!--        <div class="breadcrumb-menu">-->
    <!--            <h6 class="f-size-18 m-0">{{ __('Counseling Sessions') }}</h6>-->
    <!--            <ul>-->
    <!--                <li><a href="{{ route('website.home') }}">{{ __('home') }}</a></li>-->
    <!--                <li>/</li>-->
    <!--                <li>{{ __('Counseling Sessions') }}</li>-->
    <!--            </ul>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->
    
   

    <div class="dashboard-wrapper">
        <div class="container">
            <div class="row">
                @if (auth()->check() && auth()->user()->role == 'candidate')
                    <x-website.candidate.sidebar />
                @endif
                <div class="{{ auth()->check() && auth()->user()->role == 'candidate' ? 'col-lg-9' : 'col-12' }}">
                    <div class="dashboard-right">
                        <div class="dashboard-right-header tw-mb-6">
                            <div class="left-text">
                                <h5>{{ __('Available Counseling Sessions') }}</h5>
                                <p class="m-0">{{ __('Browse and book Zoom counseling sessions with our partner counselors') }}</p>
                            </div>
                            @if (auth()->check() && auth()->user()->role == 'candidate')
                            <span class="sidebar-open-nav">
                                <i class="ph-list"></i>
                            </span>
                            @endif
                        </div>

                        {{-- Search and Filter Form --}}
                        <div class="tw-mb-6 tw-bg-white tw-p-4 tw-rounded-xl tw-shadow-sm tw-border">
                            <form action="{{ route('counseling.sessions') }}" method="GET">
                                <div class="row align-items-center">
                                    <div class="col-lg-6 col-md-5 mb-3 mb-md-0">
                                        <div class="position-relative">
                                            <i class="ph-magnifying-glass position-absolute" style="left: 15px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                                            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search by title or counselor...') }}" class="form-control" style="padding-left: 40px;">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 mb-3 mb-md-0">
                                        <select name="category_id" class="form-control" onchange="this.form.submit()">
                                            <option value="">{{ __('All Categories') }}</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-md-3">
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary w-100">
                                                {{ __('Search') }}
                                            </button>
                                            @if(request('search') || request('category_id'))
                                                <a href="{{ route('counseling.sessions') }}" class="btn btn-outline-secondary">
                                                    {{ __('Clear') }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

            @if($sessions->count() > 0)
                <div class="row">
                    @foreach($sessions as $session)
                        <div class="col-lg-4 col-md-6 tw-mb-4">
                            <div
                                class="tw-border tw-rounded-xl tw-bg-white tw-shadow-sm tw-h-full tw-flex tw-flex-col tw-overflow-hidden hover:tw-shadow-md tw-transition-shadow">
                                <div class="tw-p-5 tw-flex-1">
                                    <div class="tw-flex tw-items-center tw-gap-3 tw-mb-3">
                                        <img src="{{ $session->counselor->user->image_url }}"
                                            alt="{{ $session->counselor->user->name }}"
                                            class="tw-w-10 tw-h-10 tw-rounded-full tw-object-cover tw-border">
                                        <div>
                                            <h6 class="tw-font-semibold tw-m-0 tw-text-base">{{ $session->title }}</h6>
                                            <small class="tw-text-gray-500">{{ $session->counselor->user->name }}</small>
                                        </div>
                                    </div>

                                    <div class="tw-flex tw-flex-wrap tw-gap-2 tw-mb-3">
                                        @if($session->counselingCategory)
                                            <span class="badge bg-primary text-white">{{ $session->counselingCategory->name }}</span>
                                        @endif
                                    </div>

                                    @if($session->description)
                                        <p class="tw-text-gray-600 tw-text-sm tw-mb-3">{{ Str::limit($session->description, 120) }}</p>
                                    @endif

                                    <div class="tw-space-y-2 tw-mb-3">
                                        <div class="tw-flex tw-items-center tw-text-sm tw-text-gray-500">
                                            <i class="ph-video-camera tw-mr-2 tw-text-blue-500"></i>
                                            <span>{{ __('Zoom Meeting') }}</span>
                                        </div>
                                        <div class="tw-flex tw-items-center tw-text-sm tw-text-gray-500">
                                            <i class="ph-clock tw-mr-2 tw-text-blue-500"></i>
                                            <span>30 {{ __('min per session') }}</span>
                                        </div>
                                        <div class="tw-flex tw-items-center tw-text-sm">
                                            <i class="ph-currency-circle-dollar tw-mr-2 tw-text-green-500"></i>
                                            <span
                                                class="tw-font-semibold {{ $session->fee > 0 ? 'tw-text-green-600' : 'tw-text-blue-600' }}">
                                                {{ $session->fee > 0 ? 'Rs. ' . number_format($session->fee, 0) : 'Free' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="tw-flex tw-flex-wrap tw-gap-1">
                                        @foreach($session->schedules as $schedule)
                                            <span class="tw-px-2 tw-py-0.5 tw-bg-blue-50 tw-text-blue-700 tw-rounded tw-text-xs">
                                                {{ $schedule->day_name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="tw-p-4 tw-border-t tw-bg-gray-50">
                                    <a href="{{ route('counseling.session.show', $session) }}"
                                        class="btn btn-primary btn-sm tw-w-full">
                                        <i class="ph-calendar-plus tw-mr-1"></i> {{ __('View & Book') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="tw-mt-4">
                    {{ $sessions->links() }}
                </div>
            @else
                <div class="text-center tw-py-12">
                    <x-svg.not-found-icon />
                    <p class="mt-4 tw-text-gray-500">{{ __('No counseling sessions available at the moment') }}</p>
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