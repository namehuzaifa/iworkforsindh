@extends('frontend.layouts.app')

@section('description')
    @php
        $data = metaData('home');
    @endphp
    {{ $data->description }}
@endsection
@section('og:image')
    {{ asset($data->image) }}
@endsection
@section('title')
    {{ $data->title }}
@endsection

@section('main')
    <section class="hero-section-3">
        <div class="container">
            <div class="tw-flex tw-justify-center tw-items-center tw-relative tw-z-50">
                <div class="tw-max-w-3xl tw-text-white tw-text-center">
                    <h1 class="tw-text-white">{!! __('no_1_job_portal_home_3') !!}</h1>
                    <p>{{ __('job_seekers_stats') }}</p>
                    <form action="{{ route('website.job') }}" method="GET" id="job_search_form">
                        <div class="jobsearchBox d-flex flex-column flex-md-row bg-gray-10 input-transparent rt-mb-24"
                            data-aos="fadeinup" data-aos-duration="400" data-aos-delay="50">
                            <div class="flex-grow-1 fromGroup has-icon">
                                <input id="index_search" name="keyword" type="text"
                                    placeholder="{{ __('job_title_keyword') }}" value="{{ request('keyword') }}"
                                    autocomplete="off" class="text-gray-900">
                                <div class="icon-badge">
                                    <x-svg.search-icon />
                                </div>
                                <span id="autocomplete_index_job_results"></span>
                            </div>
                            <input type="hidden" name="lat" id="lat" value="">
                            <input type="hidden" name="long" id="long" value="">
                            @php
                                $oldLocation = request('location');
                                $map = $setting->default_map;
                            @endphp

                            @if ($map == 'google-map')
                                <div class="flex-grow-1 fromGroup has-icon banner-select no-border">
                                    <input type="text" id="searchInput" placeholder="{{ __('enter_location') }}"
                                        name="location" value="{{ $oldLocation }}" class="text-gray-900">
                                    <div id="google-map" class="d-none"></div>
                                    <div class="icon-badge">
                                        <x-svg.location-icon stroke="{{ $setting->frontend_primary_color }}" width="24"
                                            height="24" />
                                    </div>
                                </div>
                            @else
                                <div class="flex-grow-1 fromGroup has-icon banner-select no-border">
                                    <input name="long" class="leaf_lon" type="hidden">
                                    <input name="lat" class="leaf_lat" type="hidden">
                                    <input type="text" id="leaflet_search" placeholder="{{ __('enter_location') }}"
                                        name="location" value="{{ $oldLocation }}" autocomplete="off"
                                        class="text-gray-900">
                                    <div class="icon-badge">
                                        <x-svg.location-icon stroke="{{ $setting->frontend_primary_color }}" width="24"
                                            height="24" />
                                    </div>
                                </div>
                            @endif
                            <div class="flex-grow-0">
                                <button type="submit"
                                    class="btn btn-primary d-block d-md-inline-block ">{{ __('find_job_now') }}</button>
                            </div>
                        </div>
                    </form>
                    @if ($top_categories->count())
                        <div class="f-size-14 banner-quciks-links" data-aos="" data-aos-duration="1000"
                            data-aos-delay="500">
                            <span class="!tw-text-gray-300">{{ __('suggestion') }}: </span>
                            @foreach ($top_categories as $item)
                                @if ($item->slug)
                                    <a class="!tw-text-white tw-underline"
                                        href="{{ route('website.job.category.slug', ['category' => $item->slug]) }}">>
                                        {{ $item->name }} {{ !$loop->last ? ',' : '' }}</a>
                                @endif
                            @endforeach
                    @endif
                </div>
            </div>
        </div>
    </section>
    <!-- google adsense area -->
    @if (advertisement_status('home_page_ad'))
        @if (advertisementCode('home_page_thin_ad_after_counter_section'))
            <div class="container my-4">
                {!! advertisementCode('home_page_thin_ad_after_counter_section') !!}
            </div>
        @endif
    @endif
    <!-- google adsense area end -->
    <!-- category section -->
    <section class="jobs-card-section md:tw-py-20 tw-py-12">
        <div class="container">
            <div>
                <h2>{{ __('top_categories') }}</h2>
            </div>
            <div class="tw-mt-8 tw-relative tw-z-50">
                <div class="tw-grid tw-grid-cols-1  md:tw-grid-cols-2 lg:tw-grid-cols-4 tw-gap-6">
                    @php
                        $popular_categories = $popular_categories->toArray();
                        ksort($popular_categories);
                    @endphp
                    @foreach ($popular_categories as $key => $category)
                        @isset($category['slug'])
                            <a href="{{ route('website.job.category.slug', $category['slug']) }}"
                                class="!tw-bg-white tw-transition-all tw-duration-300 hover:-tw-translate-y-[2px] tw-shadow-md tw-rounded-md tw-px-4 tw-py-2.5 tw-flex tw-gap-4 tw-items-center">
                                <span class="tw-text-2xl">
                                    <i class="{{ $category['icon'] }}"></i>
                                </span>
                                <div class=" tw-flex-1">
                                    <h4 class="tw-mb-0 tw-text-lg">{{ $category['name'] }}</h4>
                                    <p class="tw-mb-0 tw-text-sm">{{ $category['jobs_count'] }} {{ __('open_positions') }}</p>
                                </div>
                            </a>
                        @endisset
                    @endforeach
                </div>

            </div>
        </div>
    </section>
    <!-- create profile -->
    {{-- <section class="md:tw-py-20 tw-py-12 !tw-border-t !tw-border-b !tw-border-primary-100">
        <div class="container">
            <div class="row tw-items-center">
                <div class="col-lg-6">
                    <img class="tw-rounded-lg" src="{{ asset('frontend') }}/assets/images/all-img/cta-1.png">
                </div>
                <div class="col-lg-6">
                    <div class="lg:tw-ps-12 tw-pt-6 lg:tw-pt-0">
                        <h5 class="tw-text-primary-500 tw-mb-4">{{ __('create_profile') }}</h5>
                        <h2 class="">{{ __('create_your_personal_account_profile') }}</h2>
                        <p class="">{{ __('work_profile_description') }}</p>
                        <div class="">
                            <a href="{{ route('register') }}" class="apply-button">{{ __('create_profile') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}

    <!-- working process section -->
    <section class="working-process tw-bg-white">
        <div class="rt-spacer-100 rt-spacer-md-50"></div>
        <div class="container">
            <div class="row">
                <div class="col-12 text-center text-h4 ft-wt-5">
                    <span class="text-primary-500 has-title-shape">{{ config('app.name') }}
                        <img src="{{ asset('frontend') }}/assets/images/all-img/title-shape.png" alt="">
                    </span>
                    <label for="">{{ __('working_process') }}</label>
                </div>
            </div>
            <div class="rt-spacer-50"></div>
            <div class="row">
                <div class="col-lg-3 col-sm-6 rt-mb-24 position-relative">
                    <div class="has-arrow first">
                        <img src="{{ asset('frontend') }}/assets/images/all-img/arrow-1.png" alt=""
                            draggable="false">
                    </div>
                    <div class="rt-single-icon-box hover:!tw-bg-primary-50 working-progress icon-center">
                        <div class="icon-thumb rt-mb-24">
                            <div class="icon-72">
                                <i class="ph-user-plus"></i>
                            </div>
                        </div>
                        <div class="iconbox-content">
                            <div class="body-font-2 rt-mb-12">{{ __('Discover Endless Opportunities') }}</div>
                            <div class="body-font-4 text-gray-400">
                                {{ __('Explore a wide variety of jobs curated just for you — based on your interests, goals, and expertise. Your next big break starts here!') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 rt-mb-24 col-sm-6 position-relative">
                    <div class="has-arrow middle">
                        <img src="{{ asset('frontend') }}/assets/images/all-img/arrow-2.png" alt=""
                            draggable="false">
                    </div>
                    <div class="rt-single-icon-box hover:!tw-bg-primary-50 working-progress icon-center">
                        <div class="icon-thumb rt-mb-24">
                            <div class="icon-72">
                                <i class="ph-cloud-arrow-up"></i>
                            </div>
                        </div>
                        <div class="iconbox-content">
                            <div class="body-font-2 rt-mb-12">{{ __(' Build Your Power Profile') }}</div>
                            <div class="body-font-4 text-gray-400">
                                {{ __('Showcase your talent like a pro! Highlight your skills, experience, and achievements to stand out and get noticed by top employers.') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 rt-mb-24 col-sm-6 position-relative">
                    <div class="has-arrow last">
                        <img src="{{ asset('frontend') }}/assets/images/all-img/arrow-1.png" alt=""
                            draggable="false">
                    </div>
                    <div class="rt-single-icon-box hover:!tw-bg-primary-50 working-progress icon-center">
                        <div class="icon-thumb rt-mb-24">
                            <div class="icon-72">
                                <i class="ph-magnifying-glass-plus"></i>
                            </div>
                        </div>
                        <div class="iconbox-content">
                            <div class="body-font-2 rt-mb-12">{{ __('Apply in Just a Click') }}</div>
                            <div class="body-font-4 text-gray-400">
                                {{ __('No more complicated forms! Apply quickly and easily to the jobs that match your dream career — all with a few simple clicks.') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 rt-mb-24 col-sm-6">
                    <div class="rt-single-icon-box hover:!tw-bg-primary-50 working-progress icon-center">
                        <div class="icon-thumb rt-mb-24">
                            <div class="icon-72">
                                <i class="ph-circle-wavy-check"></i>
                            </div>
                        </div>
                        <div class="iconbox-content">
                            <div class="body-font-2 rt-mb-12">{{ __('Track. Improve. Succeed.') }}</div>
                            <div class="body-font-4 text-gray-400">
                                {{ __('Stay in control of your job journey! Monitor your applications, get updates, and keep moving forward with confidence.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="rt-spacer-100 rt-spacer-md-50"></div>
    </section>

    <section class="working-process tw-bg-white d-none">
        <div class="rt-spacer-100 rt-spacer-md-50"></div>
        <div class="container">
            <div class="row">
                <div class="col-12 text-center text-h4 ft-wt-5">
                    <span class="text-primary-500 has-title-shape">{{ config('app.name') }}
                        <img src="{{ asset('frontend') }}/assets/images/all-img/title-shape.png" alt="">
                    </span>
                    <label for="">{{ __('working_process') }}</label>
                </div>
            </div>
            <div class="rt-spacer-50"></div>
            <div class="row">
                <div class="col-lg-3 col-sm-6 rt-mb-24 position-relative">
                    <div class="has-arrow first">
                        <img src="{{ asset('frontend') }}/assets/images/all-img/arrow-1.png" alt=""
                            draggable="false">
                    </div>
                    <div class="rt-single-icon-box hover:!tw-bg-primary-50 working-progress icon-center">
                        <div class="icon-thumb rt-mb-24">
                            <div class="icon-72">
                                <i class="ph-user-plus"></i>
                            </div>
                        </div>
                        <div class="iconbox-content">
                            <div class="body-font-2 rt-mb-12">{{ __('explore_opportunities') }}</div>
                            <div class="body-font-4 text-gray-400">
                                {{ __('browse_through_a_diverse_range_of_job_listings_tailored_to_your_interests_and_expertise') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 rt-mb-24 col-sm-6 position-relative">
                    <div class="has-arrow middle">
                        <img src="{{ asset('frontend') }}/assets/images/all-img/arrow-2.png" alt=""
                            draggable="false">
                    </div>
                    <div class="rt-single-icon-box hover:!tw-bg-primary-50 working-progress icon-center">
                        <div class="icon-thumb rt-mb-24">
                            <div class="icon-72">
                                <i class="ph-cloud-arrow-up"></i>
                            </div>
                        </div>
                        <div class="iconbox-content">
                            <div class="body-font-2 rt-mb-12">{{ __('create_your_profile') }}</div>
                            <div class="body-font-4 text-gray-400">
                                {{ __('build_a_standout_profile_highlighting_your_skills_experience_and_qualifications') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 rt-mb-24 col-sm-6 position-relative">
                    <div class="has-arrow last">
                        <img src="{{ asset('frontend') }}/assets/images/all-img/arrow-1.png" alt=""
                            draggable="false">
                    </div>
                    <div class="rt-single-icon-box hover:!tw-bg-primary-50 working-progress icon-center">
                        <div class="icon-thumb rt-mb-24">
                            <div class="icon-72">
                                <i class="ph-magnifying-glass-plus"></i>
                            </div>
                        </div>
                        <div class="iconbox-content">
                            <div class="body-font-2 rt-mb-12">{{ __('apply_with_ease') }}</div>
                            <div class="body-font-4 text-gray-400">
                                {{ __('effortlessly_apply_to_jobs_that_match_your_preferences_with_just_a_few_clicks') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 rt-mb-24 col-sm-6">
                    <div class="rt-single-icon-box hover:!tw-bg-primary-50 working-progress icon-center">
                        <div class="icon-thumb rt-mb-24">
                            <div class="icon-72">
                                <i class="ph-circle-wavy-check"></i>
                            </div>
                        </div>
                        <div class="iconbox-content">
                            <div class="body-font-2 rt-mb-12">{{ __('track_your_progress') }}</div>
                            <div class="body-font-4 text-gray-400">
                                {{ __('stay_informed_on_your_applications_and_manage_your_job_seeking_journey_effectively') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="rt-spacer-100 rt-spacer-md-50"></div>
    </section>

 <!-- steps progress animation section -->
    {{-- <style>
        .steps-wrapper {
          position: relative;
          display: flex;
          flex-direction: column;
        }
    
        .steps-container {
          display: flex;
          flex-direction: row;
          align-items: center;
          justify-content: center;
          flex-wrap: wrap;
          gap: 40px;
          position: relative;
        }
    
        .step {
          display: flex;
          flex-direction: column;
          align-items: center;
          opacity: 0.5;
          transform: scale(1);
          transition: all 0.4s ease;
          color: #333;
          position: relative;
          padding: 20px;
        }
    
        .step h3 {
          margin-top: 8px;
          font-size: 14px;
          font-weight: bold;
        }
    
        .step-icon {
          width: 50px;
          height: 50px;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 20px;
          transition: all 0.4s ease;
          background: #f1f1f1;
          padding: 35px;
          border: 3px solid transparent; /* fixed height with invisible border */
    
        }
    
        .step.active {
          opacity: 1;
          transform: scale(1.1);
          color: #0A65CC;
           /* padding: 20px; */
        }
    
        .step.active .step-icon {
          border: 2px solid #0A65CC;
          background: #fff;
          /* padding: 35px; */
        }
    
        .arrow {
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
          font-size: 24px;
          color: #0A65CC;
          display: none;
          pointer-events: none;
          font-weight: bold;
        }
    
        .arrow.show {
          display: block;
        }
    
        @media screen and (max-width: 768px) {
          .steps-container {
            flex-direction: column;
            gap: 40px;
          }
        }
    
        .arrow.right {
          /* desktop arrow */
        }
    
        .arrow.down {
          display: none;
        }
    
        @media screen and (max-width: 768px) {
          .arrow.right {
            display: none;
          }
          .arrow.down {
            display: block;
          }
        }
    </style>

    <div class="steps-wrapper">
      <div class="steps-container" id="steps-container">
        <div class="step">
          <div class="step-icon">👤</div>
          <h3>Register</h3>
        </div>
        <div class="step">
          <div class="step-icon">📝</div>
          <h3>Complete Profile</h3>
        </div>
        <div class="step">
          <div class="step-icon">💼</div>
          <h3>Apply</h3>
        </div>
        <div class="step">
          <div class="step-icon">✨</div>
          <h3>Shortlist</h3>
        </div>
        <div class="step">
          <div class="step-icon">💬</div>
          <h3>Interview</h3>
        </div>
        <div class="step">
          <div class="step-icon">✅</div>
          <h3>Employed</h3>
        </div>
      </div>
    
      <!-- Overlaid Arrow -->
        <!-- Right Arrow SVG -->
        <div class="arrow right" id="arrow">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0A65CC" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12h14"></path>
            <path d="m12 5 7 7-7 7"></path>
        </svg>
        </div>
        
        <!-- Down Arrow SVG -->
        <div class="arrow down" id="arrow-mobile">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0A65CC" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 5v14"></path>
            <path d="m19 12-7 7-7-7"></path>
        </svg>
        </div>
    </div>

    <script>
      const steps = document.querySelectorAll('.step');
      const arrow = document.getElementById('arrow');
      const arrowMobile = document.getElementById('arrow-mobile');
      let current = 0;
    
      function positionArrow(fromElem, toElem) {
        if (!fromElem || !toElem) return;
    
        const fromRect = fromElem.getBoundingClientRect();
        const toRect = toElem.getBoundingClientRect();
        const containerRect = document.getElementById('steps-container').getBoundingClientRect();
    
        if (window.innerWidth > 768) {
          arrow.style.left = (fromRect.right + toRect.left) / 2 - containerRect.left + 'px';
          arrow.style.top = (fromRect.top + fromRect.height / 2) - containerRect.top + 'px';
        } else {
          arrowMobile.style.top = (fromRect.bottom + toRect.top) / 2 - containerRect.top + 'px';
          arrowMobile.style.left = fromRect.left + fromRect.width / 2 - containerRect.left + 'px';
        }
      }
    
      function highlightStep() {
        steps.forEach(step => step.classList.remove('active'));
        steps[current].classList.add('active');
    
        // Hide arrows first
        arrow.classList.remove('show');
        arrowMobile.classList.remove('show');
    
        const next = current + 1 < steps.length ? steps[current + 1] : null;
    
        if (next) {
          if (window.innerWidth > 768) {
            positionArrow(steps[current], next);
            arrow.classList.add('show');
          } else {
            positionArrow(steps[current], next);
            arrowMobile.classList.add('show');
          }
        }
    
        current = (current + 1) % steps.length;
      }
    
      highlightStep();
      setInterval(highlightStep, 2000);
    
      window.addEventListener('resize', () => {
        highlightStep(); // reposition on resize
      });
    </script> --}}

    <!-- google adsense area -->
    @if (advertisement_status('home_page_ad'))
        @if (advertisementCode('home_page_fat_ad_after_workingprocess_section'))
            <div class="container my-4">
                {!! advertisementCode('home_page_fat_ad_after_workingprocess_section') !!}
            </div>
        @endif
    @endif
    <!-- google adsense area end -->
    <!-- jobs card -->
    {{-- <section class="tw-bg-primary-50 md:tw-py-20 tw-py-12">
        <div class="container">
            <div class="row md:tw-pb-12 tw-pb-8">
                <div class="col-12">
                    <div class="tw-flex tw-gap-3 tw-items-center tw-flex-wrap">
                        <div class="flex-grow-1">
                            <h4 class="tw-mb-0">
                                {{ __('top') }}
                                <span class="text-primary-500 has-title-shape">{{ __('featured_job') }}
                                    <img src="{{ asset('frontend') }}/assets/images/all-img/title-shape.png"
                                        alt="">
                                </span>
                            </h4>
                        </div>
                        <a href="{{ route('website.job') }}" class="flex-grow-0 rt-pt-md-10">
                            <button class="btn btn-outline-primary !tw-border-primary-500">
                                <span class="button-content-wrapper ">
                                    <span class="button-icon align-icon-right">
                                        <i class="ph-arrow-right"></i>
                                    </span>
                                    <span>
                                        {{ __('view_all') }}
                                    </span>
                                </span>
                            </button>
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                @if ($featured_jobs && count($featured_jobs) > 0)
                    @foreach ($featured_jobs as $job)
                        <div class="col-xl-3 col-md-4 fade-in-bottom  condition_class rt-mb-24 tw-self-stretch">
                            <a href="{{ route('website.job.details', $job->slug) }}"
                                class="tw-h-full card tw-card tw-block jobcardStyle1 tw-border-gray-200 hover:!-tw-translate-y-1 hover:tw-bg-primary-50 tw-bg-gray-50"
                                tabindex="0">
                                <div class="tw-p-6 tw-flex tw-gap-3 tw-flex-col tw-justify-between tw-h-full">
                                    <div>
                                        <div class="tw-mb-1.5">
                                            <span class="tw-text-[#18191C] tw-text-lg tw-font-medium">
                                                {{ $job->title }}
                                            </span>
                                        </div>
                                        <div class="tw-flex tw-flex-wrap tw-gap-2 tw-items-center tw-mb-1.5">
                                            <span
                                                class="tw-text-[#0BA02C] tw-text-[12px] tw-leading-[12px] tw-font-semibold tw-bg-[#E7F6EA] tw-px-2 tw-py-1 tw-rounded-[3px]">
                                                {{ $job->job_type ? $job->job_type->name : '' }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="tw-text-sm tw-text-[#767F8C]">
                                                @if ($job->salary_mode == 'range')
                                                    {{ currencyAmountShort($job->min_salary) }} -
                                                    {{ currencyAmountShort($job->max_salary) }}
                                                    {{ currentCurrencyCode() }}
                                                @else
                                                    {{ $job->custom_salary }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="tw-flex tw-items-center tw-gap-2">
                                        <span>
                                            <div class="tw-w-[56px] tw-h-[56px]">
                                                <img class="tw-rounded-lg tw-w-[56px] tw-h-[56px]"
                                                    src="{{ $job?->company?->logo_url }}" alt=""
                                                    draggable="false">

                                            </div>
                                        </span>
                                        <div class="iconbox-content">
                                            <div class="tw-mb-1 tw-inline-flex">
                                                <span
                                                    class="tw-text-base tw-font-medium tw-text-[#18191C]">{{ $job->company->user->name ?? " "}}</span>
                                            </div>
                                            <span class="tw-flex tw-items-center tw-gap-1">
                                                <i class="ph-map-pin"></i>
                                                <span class="tw-location">{{ $job->country }}</span>
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        <button
                                            class="btn hover:tw-text-white hover:tw-bg-primary-700 tw-px-2.5 tw-py-1 tw-text-white tw-bg-primary-500">{{ __('apply_now') }}</button>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section> --}}
    {{-- Jobs listing --}}
    
        @if (!auth('user')->check() || (auth('user')->check() && authUser()->role == 'candidate'))
            {{-- <section class="tw-bg-primary-50 md:tw-py-20 tw-py-12"> --}}
            <section class="jobs-card-section md:tw-py-20 tw-py-12">
               
                <div class="container">
                    <div class="row md:tw-pb-12 tw-pb-8">
                        <div class="col-12">
                            <div class="d-flex flex-wrap">
                                <div class="flex-grow-1">
                                    <h4>Latest <span
                                            class="text-primary-500 has-title-shape">Jobs
                                            <img src="{{ asset('frontend') }}/assets/images/all-img/title-shape.png"
                                                alt="">
                                        </span></h4>
                                </div>
                                <a href="{{ route('website.job') }}" class="flex-grow-0 rt-pt-md-10">
                                    <button class="apply-button">
                                        <span class="button-content-wrapper ">
                                            <span class="button-icon align-icon-right">
                                                <i class="ph-arrow-right"></i>
                                            </span>
                                            <span>
                                                {{ __('view_all') }}
                                            </span>
                                        </span>
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="main-section flex flex-col gap-8">
                        @foreach ($latestjobs as $job)
                            <div class="job-card">
                                <div class="header">Deadline {{ \Carbon\Carbon::parse($job->deadline)->format('d M Y') }}</div>
                                <div class="content">
                                    <div class="job-title">{{ $job->title }}</div>
                                    <div class="job-detail"> <b>Job Type:</b> {{ $job->job_type ? $job->job_type->name : '' }}</div>
                                    <div class="job-detail"> <b>Salary:</b>  
                                        @if ($job->salary_mode == 'range')
                                        {{ currencyAmountShort($job->min_salary) }} -
                                        {{ currencyAmountShort($job->max_salary) }} {{ currentCurrencyCode() }}
                                        @else
                                            {{ $job->custom_salary }}
                                        @endif
                                    </div>
                                   
                                    <div class="job-detail"> <b>Location:</b> {{$job->exact_location}}</div>
                                    <a href="{{ route('website.job.details', $job->slug) }}" class="apply-button">Apply Now</a>

                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </section>
        @endif

        {{-- @php
        dd($latestjobs);
    @endphp --}}
    
    {{-- Jobs listing --}}
    <!-- google adsense area -->
    @if (advertisement_status('home_page_ad'))
        @if (advertisementCode('home_page_fat_ad_after_featuredjob_section'))
            <div class="container my-4">
                {!! advertisementCode('home_page_fat_ad_after_featuredjob_section') !!}
            </div>
        @endif
    @endif
    <!-- google adsense area end -->
    <!-- top companaies -->
    @if ($top_companies && count($top_companies) > 0)
        @if (!auth('user')->check() || (auth('user')->check() && authUser()->role == 'candidate'))
            <section class="md:tw-py-20 tw-py-12">
                <div class="container">
                    <div class="row md:tw-pb-12 tw-pb-8">
                        <div class="col-12">
                            <div class="d-flex flex-wrap">
                                <div class="flex-grow-1">
                                    <h4>{{ __('top') }} <span
                                            class="text-primary-500 has-title-shape">{{ __('companies') }}
                                            <img src="{{ asset('frontend') }}/assets/images/all-img/title-shape.png"
                                                alt="">
                                        </span></h4>
                                </div>
                                <a href="{{ route('website.company') }}" class="flex-grow-0 rt-pt-md-10">
                                    <button class="apply-button">
                                        <span class="button-content-wrapper ">
                                            <span class="button-icon align-icon-right">
                                                <i class="ph-arrow-right"></i>
                                            </span>
                                            <span>
                                                {{ __('view_all') }}
                                            </span>
                                        </span>
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @foreach ($top_companies as $company)
                            <div class="col-xl-3 col-md-4 fade-in-bottom  condition_class rt-mb-24 tw-self-stretch">
                                <a href="{{ route('website.employe.details', $company->user->username) }}"
                                    class="card jobcardStyle1 tw-h-full">
                                    <div class="tw-p-6 tw-flex tw-flex-col tw-gap-1.5">
                                        <div class="tw-w-14 tw-h-14">
                                            <img class="tw-w-full tw-h-full tw-object-cover"
                                                src="{{ $company->logo_url }}" alt="" draggable="false">
                                        </div>
                                        <div>
                                            <div class="">
                                                <span
                                                    class="tw-text-[#191F33] tw-text-base tw-font-medium">{{ $company->user->name }}</span>
                                            </div>
                                            <span
                                                class="tw-inline-flex tw-text-sm tw-gap-1 tw-items-center text-gray-400 ">
                                                <i class="ph-map-pin"></i>
                                                {{ $company->country }}
                                            </span>
                                        </div>
                                        <div class="tw-flex tw-flex-wrap tw-gap-1.5">
                                            <span
                                                class="tw-px-2 tw-py-0.5 tw-inline-block tw-text-xs tw-font-medium tw-text-[#474C54] tw-rounded-[52px] tw-bg-primary-50 ll-primary-border">
                                                {{ $company?->industry?->name ?? '' }}
                                            </span>
                                            <span
                                                class="tw-px-2 tw-py-0.5 tw-inline-block tw-text-xs tw-font-medium tw-text-[#474C54] tw-rounded-[52px] tw-bg-primary-50 ll-primary-border">{{ $company->jobs_count }}
                                                {{ __('open_position') }}</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    @endif
    <!-- google adsense area -->
    @if (advertisement_status('home_page_ad'))
        @if (advertisementCode('home_page_fat_ad_after_client_section'))
            <div class="container my-4">
                {!! advertisementCode('home_page_fat_ad_after_client_section') !!}
            </div>
        @endif
    @endif
    <!-- google adsense area end -->

    {{-- testimonials --}}
    <section class="testimonial-section">
        <div class="testimonial-heading">
            <!-- <h5>TESTIMONIAL</h5> -->
            <h4>HEAR WHAT <span class="text-primary-500 has-title-shape">OUR OFFICIALS SAY<img src="http://portal.test/frontend/assets/images/all-img/title-shape.png" alt=""></span></h4>
        </div>
        <div class="testimonial-slider" id="testimonial-slider">
            <div class="testimonial-card">
                <p>”I Work For Sindh’ is not just a slogan — it’s a movement that puts the people at the heart of governance. Our mission is to create jobs, empower youth, and deliver results that matter to every citizen. This initiative reflects the Pakistan Peoples Party’s commitment to a people-first development agenda.”</p>
                <div class="testimonial-footer">
                    <img src="{{ asset('frontend') }}/assets/images/bilawal.jpg" alt="Bilawal Bhutto Zardari">
                    <h4>Bilawal Bhutto Zardari</h4>
                    <div class="stars">Chairman PPP</div>
                </div>
            </div>
            <div class="testimonial-card">
                <p>“Under the ‘I Work For Sindh’ initiative, we are ensuring transparency, merit, and equal opportunity for all. Our administration is focused on inclusive growth, investing in human capital, and uplifting communities across the province. We are building a stronger, progressive Sindh — together.”</p>
                <div class="testimonial-footer">
                    <img src="{{ asset('frontend') }}/assets/images/syed_murad_ali_shah.jpg" alt="Syed Murad Ali Shah">
                    <h4>Syed Murad Ali Shah</h4>
                    <div class="stars">Chief Minister Sindh</div>
                </div>
            </div>
            <div class="testimonial-card">
                <p>"This project is a step forward in making governance participatory. Through ‘I Work For Sindh’, we are inviting the people to be part of the change. Whether it’s through employment, innovation, or infrastructure, this is the time to rise and work for our province with pride.”</p>
                <div class="testimonial-footer">
                    <img src="{{ asset('frontend') }}/assets/images/sharjeel-memon.jpg" alt="Sharjeel Inam Memon">
                    <h4>Sharjeel Inam Memon</h4>
                    <div class="stars">Information Minister Sindh</div>
                </div>
            </div>
        </div>
    </section>
    {{-- end testimonials --}}
    <!-- newsletter -->
    <section class="section-box tw-mb-8 d-none">
        <div class="container">
            <div class="tw-bg-primary-500 tw-p-8 tw-rounded-xl">
                <div class="row align-items-center">
                    <div class="tw-relative tw-min-h-[400px] col-xl-3 col-12 text-center d-none d-xl-block">
                        <div class="tw-flex tw-gap-3 tw-items-start tw-flex-wrap">
                            <img class="tw-w-1/2 tw-rounded tw-shadow-sm animation-float-bottom tw-self-center"
                                src="{{ asset('frontend/assets/images/image-01.jpeg') }}" alt="">
                            <img class="tw-w-2/5 tw-rounded tw-shadow-sm animation-float-right tw-self-center"
                                src="{{ asset('frontend/assets/images/image-02.jpeg') }}" alt="">
                            <img class="tw-w-1/2 tw-rounded tw-shadow-sm animation-float-top tw-self-center"
                                src="{{ asset('frontend/assets/images/image-03.jpeg') }}" alt="">
                        </div>
                    </div>
                    <div class="col-lg-12 col-xl-6 col-12 md:tw-px-10">
                        <h2 class="tw-text-white tw-font-bold tw-mb-8 text-center md:tw-text-4xl tw-text-2xl"> {!! __('updates_regularly') !!}
                        </h2>
                        <div class="box-form-newsletter mt-40">
                            <form action="{{ route('newsletter.subscribe') }}" method="POST" class="tw-gap-2 tw-flex tw-flex-col sm:tw-flex-row">
                                @csrf
                                <input required class="input-newsletter" type="email" value="" name="email"
                                    placeholder="{{ __('enter_email_here') }}">
                                <button type="submit"
                                    class="tw-border-0 tw-min-h-[48px] tw-rounded tw-px-3 tw-font-medium tw-bg-orange-400 !tw-text-white">{{ __('subscribe') }}</button>
                            </form>
                        </div>
                    </div>
                    <div class="tw-relative tw-h-full col-xl-3 col-12 text-center d-none d-xl-block">
                        <div class="tw-flex tw-gap-3 tw-items-start tw-flex-wrap">
                            <img class="tw-w-2/5 tw-rounded tw-shadow-sm animation-float-left tw-self-center"
                                src="{{ asset('frontend/assets/images/image-06.jpeg') }}" alt="">
                            <img class="tw-w-1/2 tw-rounded tw-shadow-sm animation-float-bottom tw-self-center"
                                src="{{ asset('frontend/assets/images/image-04.jpeg') }}" alt="">
                            <img class="tw-w-1/2 tw-rounded tw-shadow-sm animation-float-top tw-self-center"
                                src="{{ asset('frontend/assets/images/image-05.jpeg') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('backend') }}/plugins/fontawesome-free/css/all.min.css">
    <x-map.leaflet.autocomplete_links />
    @include('map::links')
    <style>

        .whatsapp-float {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 999;
            border-radius: 50%;
            padding: 12px;
            transition: transform 0.3s ease;
        }

        .whatsapp-float:hover {
            transform: scale(1.1);
        }

        .whatsapp-float img {
            width: 100px;
            height: 100px;
        }

       .jobcardStyle1 {
            background: white !important;
            border-radius: 1rem !important;
            border: 2px solid #0076BF !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1) !important;
            transition: 400ms;
        }

        .jobcardStyle1:hover {
            transform: scale(1.1, 1.1) !important;
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.15) !important;
        }



        .jobcardStyle1 span.tw-text-base {
            font-size: 1.25rem !important;
            font-weight: bold !important;
            color: #0076BF !important;
        }
    
       .apply-button {
            display: inline-block;
            /* margin-top: 10px; */
            background: #0076BF;
            color: white;
            padding: 8px 16px;
            border-radius: 999px;
            text-decoration: none;
            font-size: 0.95rem;
            transition: background 0.3s ease;
        }

        .apply-button:hover {
            background: #0076BF;
        }
        .main-section {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        @media (min-width: 768px) {
            /* Tablet: 2 columns */
            .main-section {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            /* Large screen: 3 columns */
            .main-section {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        .job-card {
            background: white;
            border-radius: 1rem;
            overflow: hidden;
            cursor: pointer;
            /* border: 2px solid #0076BF; */
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1),
                    0 4px 6px -4px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
            padding-bottom: 15px;
        }

        .job-card:hover {
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.15);
        }

        .job-card .header {
            background: #0076BF;
            color: white;
            padding: 10px 20px;
            font-weight: bold;
            border-bottom: 1px solid #eee;
            width: 210px;
            float: right;
            border-radius: 0px 10px 0px 10px;
        }

        .job-card .content {
            padding: 20px;
        }

        .job-title {
            font-size: 1.25rem;
            font-weight: bold;
            color: #0076BF;
            margin-bottom: 0.5rem;
        }

        .job-detail {
            font-size: 0.95rem;
            color: #444;
            margin-bottom: 0.3rem;
        }

        section.jobs-card-section {
            background: linear-gradient(135deg, #ffffff, #0a65cc);
        }

        .apply-button {
            padding: 10px 15px;
            border: unset;
            border-radius: 15px;
            /* color: #212121; */
            z-index: 1;
            /* background: #e8e8e8; */
            position: relative;
            font-weight: 700;
            font-size: 12px;
            -webkit-box-shadow: 4px 8px 19px -3px rgba(0, 0, 0, 0.27);
            box-shadow: 5px 7px 7px 1px rgb(42 121 210 / 36%);
            transition: all 250ms;
            float: right;
            overflow: hidden;
        }

        .apply-button::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 0;
            border-radius: 15px;
            background-color: #0076bf;        
            z-index: -1;
            -webkit-box-shadow: 4px 8px 19px -3px rgba(0,0,0,0.27);
            box-shadow: 4px 8px 19px -3px rgba(0,0,0,0.27);
            transition: all 250ms
        }

        .apply-button:hover {
            color: #e8e8e8;
        }

        .apply-button:hover::before {
            width: 100%;
        }


        .main-section .job-card {
            cursor: pointer;
            transition: 400ms;
        }

        .main-section .job-card p.tip {
            font-size: 1em;
            font-weight: 700;
        }

        .main-section .job-card p.second-text {
            font-size: .7em;
        }

        .main-section .job-card:hover {
            transform: scale(1.1, 1.1);
        }

    </style>


    {{-- Testimonials --}}
    <style>
        .testimonial-section {
        max-width: 1300px;
        margin: 100px auto;
        padding: 0 20px;
        text-align: center;
        }
        
        .testimonial-heading h5 {
        color: #0076BF;
        letter-spacing: 1px;
        font-weight: bold;
        font-size: 14px;
        margin-bottom: 10px;
        }
        
        .testimonial-heading h2 {
        font-size: 32px;
        font-weight: 600;
        margin-bottom: 10px;
        }
        
        .testimonial-heading p {
        color: #666;
        font-size: 16px;
        max-width: 700px;
        margin: 0 auto 40px;
        }
        
        .testimonial-slider {
        display: flex;
        gap: 20px;
        overflow-x: auto;
        overflow-y: visible; /*  Added this */
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        scroll-behavior: smooth;
            cursor: grab;
        
        }
        
        /* Default for desktop (≥992px) */
        .testimonial-card {
        flex: 0 0 32%;
        scroll-snap-align: start;
        background: #f9f9f9;
        border-radius: 10px;
        padding: 30px;
        box-sizing: border-box;
        min-width: 300px;
        position: relative;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 40px;
        transition: 400ms;
        }
        .testimonial-card:hover{
        /* transform: scale(1.1, 1.1); */
        z-index: 2;
        background: linear-gradient(135deg, #ffffff, #0a65cc);
        
        
        }
        
        /* Tablet (600px to 991px) — show 2 cards */
        @media (max-width: 991px) and (min-width: 600px) {
        .testimonial-card {
            flex: 0 0 50%;
        }
        }
        
        /* Mobile (<600px) — show 1 card */
        @media (max-width: 599px) {
        .testimonial-card {
            flex: 0 0 100% !important;
        }
        }
        
        
        
        .testimonial-card p {
        /* font-style: italic; */
        margin-bottom: 30px;
        font-size: 17px;
        }
        
        .testimonial-footer {
        text-align: center;
        /* margin-top: 85px; */
        }
        
        .testimonial-footer img {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        margin-bottom: 10px;
        object-fit: cover;
        }
        
        .testimonial-footer h4 {
        margin: 0;
        font-size: 20px;
        color: #0076BF;
        font-weight: bold;
        }
        
        .stars {
        color: #000000;
        font-size: 18px;
        margin-top: 5px;
        }
        
        .testimonial-slider {
        scrollbar-width: none; /* Firefox */
        -ms-overflow-style: none; /* IE and Edge */
        }
        
        .testimonial-slider::-webkit-scrollbar {
        display: none; /* Chrome, Safari */
        }
        
        .testimonial-slider.dragging {
        cursor: grabbing;
        }
        
    </style>
    {{-- Testimonials --}}

    <style>
        

        .hero-section-3 {
            padding: 100px 0px;
            background-image: url('{{ asset('frontend/assets/images/H_1.jpg') }}');
            background-repeat: no-repeat;
            background-size: cover;
            position: relative;
            animation: changeBackground 30s infinite; /* 15s = 5s per image * 3 images */
        }

        @keyframes changeBackground {
            0% {
                background-image: url('{{ asset('frontend/assets/images/H_1.jpg') }}');
            }
            25% {
                background-image: url('{{ asset('frontend/assets/images/H_2.jpg') }}');
            }
            50% {
                background-image: url('{{ asset('frontend/assets/images/H_3.jpg') }}');
            }
            75% {
                background-image: url('{{ asset('frontend/assets/images/H_4.jpg') }}');
            } 
            100% {
                background-image: url('{{ asset('frontend/assets/images/H_1.jpg') }}');
            }
        }

        .hero-section-3::after {
            background-color: black;
            content: "";
            height: 100%;
            left: 0;
            opacity: .5;
            position: absolute;
            top: 0;
            width: 100%;
            z-index: 1;
        }

        span.select2-container--default .select2-selection--single {
            border: none !important;
        }

        span.select2-selection.select2-selection--single {
            outline: none;
        }

        .marginleft {
            margin-left: 10px !important;
        }

        .category-slider .slick-slide {
            margin: 0px 8px;
        }

        .category-slider .slick-dots {
            bottom: -32px;
        }

        .category-slider .slick-dots li {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            margin: 0px;
        }

        .category-slider .slick-dots li button {
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            width: 10px;
            height: 10px;
        }

        .category-slider .slick-dots li.slick-active button {
            background: rgba(255, 255, 255, 1);
            width: 12px;
            height: 12px;
        }

        .category-slider .slick-dots li button::before {
            display: none;
        }

        body:has(.hero-section-2) .n-header--bottom {
            box-shadow: none; !important;
        }

        #desktop {
            display: block;
        }

        #mobile {
            display: none;
        }

        /* Mobile View - using media query */
        @media screen and (max-width: 768px) {
            #desktop {
                display: none; /* Hide desktop design on mobile */
            }

            #mobile {
                display: block; /* Show mobile design on mobile */
            }
        }


    </style>
@endsection

@section('script')
    <script>
        const slider = document.getElementById('testimonial-slider');
    
        // Clone testimonial cards for infinite scroll illusion
        const cards = slider.querySelectorAll('.testimonial-card');
        cards.forEach(card => {
        const clone = card.cloneNode(true);
        slider.appendChild(clone);
        });
    
        let scrollAmount = 0;
        let cardWidth;
    
        const setCardWidth = () => {
        const firstCard = slider.querySelector('.testimonial-card');
        cardWidth = firstCard.offsetWidth + 20; // + gap
        };
    
        setCardWidth();
        window.addEventListener('resize', setCardWidth);
    
        const autoScroll = () => {
        if (scrollAmount >= slider.scrollWidth / 2) {
            // Reset to start for infinite scroll effect
            scrollAmount = 0;
            slider.scrollLeft = 0;
        }
    
        scrollAmount += cardWidth;
        slider.scrollTo({
            left: scrollAmount,
            behavior: 'smooth'
        });
        };
    
        
    
        let interval = setInterval(autoScroll, 2000);
    
        // Pause on hover
        slider.addEventListener('mouseenter', () => clearInterval(interval));
        slider.addEventListener('mouseleave', () => {
        interval = setInterval(autoScroll, 2000);
        });
    
        // Optional: allow manual drag (retains your original logic)
        //   const slider = document.getElementById('testimonial-slider');
    
        let isDragging = false;
        let startX = 0;
        let scrollStart = 0;
    
        slider.addEventListener('mousedown', (e) => {
        isDragging = true;
        slider.classList.add('dragging');
        startX = e.pageX;
        scrollStart = slider.scrollLeft;
        });
    
        document.addEventListener('mousemove', (e) => {
        if (!isDragging) return;
        const walk = (e.pageX - startX);
        slider.scrollLeft = scrollStart - walk;
        });
    
        document.addEventListener('mouseup', () => {
        isDragging = false;
        slider.classList.remove('dragging');
        });
    
        // Optional: support touch devices
        slider.addEventListener('touchstart', (e) => {
        isDragging = true;
        startX = e.touches[0].pageX;
        scrollStart = slider.scrollLeft;
        });
    
        slider.addEventListener('touchmove', (e) => {
        if (!isDragging) return;
        const walk = e.touches[0].pageX - startX;
        slider.scrollLeft = scrollStart - walk;
        });
    
        slider.addEventListener('touchend', () => {
        isDragging = false;
        });
    </script>
    <script>
        $('.category-slider').slick({
            dots: true,
            arrows: false,
            infinite: true,
            autoplay: true,
            speed: 300,
            slidesToShow: 4,
            slidesToScroll: 1,
            responsive: [{
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });
    </script>
@endsection


@section('mobile')
    <div id="mobile" >
        
        <style>
            body, html {
                height: 100%;
                margin: 0;
                font-family: 'Montserrat', sans-serif;
                overflow-x: hidden;
            }
            
            .background-container {
                position: fixed;
                width: 100%;
                height: 100%;
                top: 0;
                left: 0;
                z-index: -1;
                overflow: hidden;
            }
            
            #background-video {
                position: absolute;
                top: 50%;
                left: 50%;
                min-width: 100%;
                min-height: 100%;
                width: auto;
                height: auto;
                transform: translateX(-50%) translateY(-50%);
                object-fit: cover;
                max-width: none;
                max-height: none;
            }
            
            @media (min-aspect-ratio: 16/9) {
                #background-video {
                    width: 100%;
                    height: auto;
                }
            }
            
            @media (max-aspect-ratio: 16/9) {
                #background-video {
                    width: auto;
                    height: 100%;
                }
            }
            
            .gradient-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
            }
            
            .search-icon {
                position: absolute;
                left: 30px;
                top: 50%;
                transform: translateY(-50%);
                color: #777;
                z-index: 99;
            }
            
            .login-btn {
                background: linear-gradient(45deg, #a53f98, #7a1ea1);
                color: white;
                border: none;
                border-radius: 50%;
                font-weight: 500;
                letter-spacing: 1px;
                position: relative;
                overflow: hidden;
                box-shadow: 0 0 15px #a53f98;
                animation: glow 1.5s infinite alternate;
                width: 85px;
                height: 85px;
            }
            
            .login-form-btn {
                /* background: linear-gradient(45deg, #a53f98, #7a1ea1); */
                background-color: #1967d2;
                color: white;
                border: none;
                border-radius: 25px;
                font-weight: bold;
                letter-spacing: 1px;
                padding: 12px 0;
                width: 100%;
                /* box-shadow: 0 0 10px rgba(165, 63, 152, 0.7); */
            }
            
            .register-form-btn {
                /* background: transparent; */
                background-color: #1967d2;
    
                color: white;
                border: 1px solid #1967d2;
                border-radius: 25px;
                font-weight: bold;
                letter-spacing: 1px;
                padding: 12px 0;
                width: 100%;
                /* transition: background 0.3s; */
            }
            
            .register-form-btn:hover {
                background: rgba(255, 255, 255, 0.1);
            }
            
            @keyframes glow {
                from {
                    box-shadow: 0 0 5px #a53f98;
                }
                to {
                    box-shadow: 0 0 20px #a53f98, 0 0 30px #a53f98;
                }
            }
            
            .app-icon {
                /* background: rgba(255, 255, 255, 0.2); */
                /* border-radius: 15px; */
                /* padding: 10px; */
                width: 135px;
                transition: transform 0.3s;
            }
            
            .app-icon:hover {
                transform: scale(1.05);
            }
            
            .custom-footer {
                /* color: white; */
               
           
            }
            footer{
                background-color: #00278C; /* Background color */
                color: #fff; /* Text color (white) */
                padding: 10px 0; /* Optional: Add padding for better spacing */
                width: 100%; /* Full width */
                position: sticky;
                bottom: 0;
                z-index: 1000; /* Ensures the footer is always on top */
                border-top: 1px solid white;
                /* padding-top: 15px; */
                font-size: 10px;
                text-align: center;
            }
            
            .jobs-text {
                color: #9E9E9E;
                font-size: 14px;
                text-align: center;
            }
            
            .form-control {
                font-family: 'Montserrat', sans-serif;
            }
            
            .custom-control {
                font-family: 'Montserrat', sans-serif;
            }
            
            .forgotten-password {
                color: white;
                text-decoration: none;
                font-size: 14px;
                opacity: 0.9;
                transition: opacity 0.3s;
            }
            
            .forgotten-password:hover {
                opacity: 1;
                color: white;
                text-decoration: underline;
            }
            
            .login-section {
                display: none;
                margin: 20px auto;
            }
            
            .form-check-input:checked {
                background-color: #a53f98;
                border-color: #a53f98;
            }
            .img-fluid{
                margin-top: 75px;
            }
            .search-field-text{
                text-align: center;;
                margin-bottom: 0px;
                font-size: 16px !important;
                margin-top: 0px !important;
                font-weight: 500;
            }
            .main-heading {
                font-size: 16px !important;
                margin-bottom: -10px !important;
                font-weight: 500 !important;
                color: #fff;
            }
            #search-section{
                /* width: 400px; */
                margin: 20px auto;
            }
            .search-field{
                padding: 12px 45px !important;
            }
            .download-heading{
                font-size:  16px;
                color: #fff;
            }
            #login-section .form-control{
                padding: 12px 25px !important;
                font-size: 16px;
            }
            #login-section .login-form-btn, #login-section .register-form-btn{
                font-size: 16px !important;
                font-weight: 500 !important
            }
        </style>
        <!-- Background Container with video -->
        <div class="main-container">
            <div class="background-container">
                <video autoplay muted loop playsinline id="background-video" class="w-100 h-100">
                    <source src="{{ url('video/mobile-view-home.mp4') }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <div class="gradient-overlay"></div>
            </div>
        
        <!-- Main Content -->
            <div class="container-fluid d-flex flex-column min-vh-100 position-relative text-white p-3">
                <!-- Logo -->
                <div class="row mt-5">
                    <div class="col-12 text-start">
                        <img src="{{ url('uploads/app/logo/logo.png') }}" alt="iWork4Sindh Logo" class="img-fluid" style="max-width: 220px;">
                    </div>
                </div>
                
                <!-- Main Text -->
                <div class="row mt-3 " >
                    <div class="col-12 text-start">
                        <h4 class="fw-bold main-heading">{!! __('no_1_job_portal_home_3') !!}</h4>

                    </div>
                </div>
                
                <!-- Search Section - Initially Visible -->
                <div id="search-section">
                    <!-- Search Field with icon -->
                    <p class="mt-4 fs-5 search-field-text">Search Your Perfect Job</p>

                    <form class="row mt-3" action="/jobs">
                        <div class="col-12 position-relative">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" name="keyword" class="form-control rounded-pill py-3 ps-5 search-field" placeholder="Job title, keywords...">
                        </div>
                    </form>
                    
                    <!-- Text below search -->
                    <div class="row mt-2 mb-4">
                        <div class="col-12">
                            <p class="jobs-text">Find opportunities that match your skills and interests</p>
                        </div>
                    </div>
                    
                    <!-- Login Button -->
                    <div class="row">
                        <div class="col-12 text-center">
                            <button id="login-toggle-btn" class="login-btn">{{Auth::check() ? 'Jobs' : 'Login' }}</button>
                        </div>
                    </div>
                </div>
                
                <!-- Download App Section -->
                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <h4 class="mb-3 download-heading">DOWNLOAD APP</h4>
                        <div class="d-flex justify-content-center gap-2">
                            <div>
                                <a target="_blank" href="https://play.google.com/store/apps/details?id=com.sindh.iworkforsindh&pcampaignid=web_share"> <img src="{{ url('uploads/app/logo/Andriod_Button-1.png') }}" alt="Google Play" class="app-icon"></a>
                            </div>
                            <div>
                               <a target="_blank" href="https://apps.apple.com/pk/app/iwork4sindh/id6745734134"><img src="{{ url('uploads/app/logo/IOS-BUTTON.png') }}" alt="App Store" class="app-icon"></a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer -->
            </div>
            <footer class="footer mt-auto">
                <div class="col-12 text-center custom-footer">
                    All Rights Reserved By <b>Sindh Information Department</b>
                </div>
            </footer>
        </div>

        <!-- Scripts - Local Bootstrap JS -->
        <script src="js/bootstrap.bundle.min.js"></script>
        
        <!-- Toggle Login Form Script -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const loginToggleBtn = document.getElementById('login-toggle-btn');
                const searchSection = document.getElementById('search-section');
                const loginSection = document.getElementById('login-section');
                
                loginToggleBtn.addEventListener('click', function() {
                    window.location.href = "{{Auth::check() ? URL::to('jobs') : URL::to('login')}}";
                    // searchSection.style.display = 'none';
                    
                    // // Show login section
                    // loginSection.style.display = 'block';
                });
            });
        </script>
    </div>
@endsection
