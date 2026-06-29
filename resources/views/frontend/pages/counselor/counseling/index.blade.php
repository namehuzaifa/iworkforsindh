@extends('frontend.layouts.app')

@section('title', __('Counseling Sessions'))

@section('main')
    <div class="dashboard-wrapper">
        <div class="container">
            <div class="row">
                <x-website.counselor.sidebar />
                <div class="col-lg-9">
                    <div class="dashboard-right tw-ps-0 lg:tw-ps-5">
                        <div class="dashboard-right-header">
                            <div class="left-text">
                                <h5>{{ __('Counseling Sessions') }}</h5>
                                <p class="m-0">{{ __('Manage your counseling sessions and schedules') }}</p>
                            </div>
                            <span class="sidebar-open-nav">
                                <i class="ph-list"></i>
                            </span>
                        </div>

                        <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
                            <div>
                                <a href="{{ route('counselor.counseling.create') }}" class="btn btn-primary">
                                    <i class="ph-plus-circle tw-mr-1"></i> {{ __('Create Session') }}
                                </a>
                                <a href="{{ route('counselor.counseling.all-bookings') }}"
                                    class="btn btn-outline-primary tw-ms-2">
                                    <i class="ph-calendar-check tw-mr-1"></i> {{ __('All Bookings') }}
                                </a>
                            </div>
                        </div>

                        @if($sessions->count() > 0)
                            <div class="row">
                                @foreach($sessions as $session)
                                    <div class="col-md-6 tw-mb-4">
                                        <div class="tw-border tw-rounded-lg tw-p-4 tw-bg-white tw-shadow-sm tw-h-full">
                                            <div class="tw-flex tw-justify-between tw-items-start tw-mb-3">
                                                <h6 class="tw-font-semibold tw-text-lg tw-m-0">{{ $session->title }}</h6>
                                                <span
                                                    class="tw-px-2 tw-py-1 tw-rounded-full tw-text-xs tw-font-medium {{ $session->is_active ? 'tw-bg-green-100 tw-text-green-700' : 'tw-bg-red-100 tw-text-red-700' }}">
                                                    {{ $session->is_active ? __('Active') : __('Inactive') }}
                                                </span>
                                            </div>

                                            @if($session->description)
                                                <p class="tw-text-gray-600 tw-text-sm tw-mb-3">
                                                    {{ Str::limit($session->description, 100) }}</p>
                                            @endif

                                            <div class="tw-space-y-2 tw-mb-3">
                                                <div class="tw-flex tw-items-center tw-text-sm tw-text-gray-500">
                                                    <i class="ph-video-camera tw-mr-2"></i>
                                                    <a href="{{ $session->zoom_link }}" target="_blank" class="tw-text-blue-600 tw-underline tw-truncate" style="max-width: 200px;" title="{{ $session->zoom_link }}">
                                                        {{ __('Open Zoom Link') }}
                                                    </a>
                                                </div>
                                                @if($session->zoom_meeting_id)
                                                    <div class="tw-flex tw-items-center tw-text-sm tw-text-gray-500">
                                                        <i class="ph-hash tw-mr-2"></i>
                                                        <span>ID: {{ $session->zoom_meeting_id }}</span>
                                                        @if($session->zoom_passcode)
                                                            <span class="tw-mx-1">|</span>
                                                            <span>Pass: {{ $session->zoom_passcode }}</span>
                                                        @endif
                                                    </div>
                                                @endif
                                                <div class="tw-flex tw-items-center tw-text-sm tw-text-gray-500">
                                                    <i class="ph-currency-circle-dollar tw-mr-2"></i>
                                                    <span>{{ $session->fee > 0 ? 'Rs. ' . number_format($session->fee, 0) : 'Free' }}</span>
                                                </div>
                                                <div class="tw-flex tw-items-center tw-text-sm tw-text-gray-500">
                                                    <i class="ph-clock tw-mr-2"></i>
                                                    <span>30 {{ __('min slots') }}</span>
                                                </div>
                                                <div class="tw-flex tw-items-center tw-text-sm tw-text-gray-500">
                                                    <i class="ph-users tw-mr-2"></i>
                                                    <span>{{ $session->bookings_count }} {{ __('Bookings') }}</span>
                                                </div>
                                            </div>

                                            <div class="tw-flex tw-flex-wrap tw-gap-2 tw-mb-3">
                                                @foreach($session->schedules ?? [] as $schedule)
                                                    <span class="tw-px-2 tw-py-1 tw-bg-blue-50 tw-text-blue-700 tw-rounded tw-text-xs">
                                                        {{ $schedule->day_name }}
                                                    </span>
                                                @endforeach
                                            </div>

                                            <div class="tw-flex tw-gap-2 tw-flex-wrap tw-border-t tw-pt-3">
                                                <a href="{{ $session->zoom_link }}" target="_blank" class="btn btn-sm btn-success">
                                                    <i class="ph-video-camera"></i> {{ __('Start Meeting') }}
                                                </a>
                                                <a href="{{ route('counselor.counseling.edit', $session) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="ph-pencil-simple"></i> {{ __('Edit') }}
                                                </a>
                                                <a href="{{ route('counselor.counseling.bookings', $session) }}"
                                                    class="btn btn-sm btn-outline-info">
                                                    <i class="ph-calendar"></i> {{ __('Bookings') }}
                                                </a>
                                                <form action="{{ route('counselor.counseling.toggle', $session) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="btn btn-sm {{ $session->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                                        <i class="ph-{{ $session->is_active ? 'pause' : 'play' }}"></i>
                                                        {{ $session->is_active ? __('Deactivate') : __('Activate') }}
                                                    </button>
                                                </form>
                                                <form action="{{ route('counselor.counseling.destroy', $session) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Are you sure? All bookings will be cancelled.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="ph-trash"></i> {{ __('Delete') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center tw-py-12">
                                <x-svg.not-found-icon />
                                <p class="mt-4 tw-text-gray-500">{{ __('No counseling sessions yet.') }}</p>
                                <a href="{{ route('counselor.counseling.create') }}" class="btn btn-primary mt-3">
                                    <i class="ph-plus-circle tw-mr-1"></i> {{ __('Create Your First Session') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="dashboard-footer text-center body-font-4 text-gray-500">
            <x-website.footer-copyright />
        </div>
    </div>
@endsection
