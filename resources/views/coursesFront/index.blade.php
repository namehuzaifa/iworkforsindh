@extends('frontend.layouts.app')

@section('main')

    <style>
                /* ===== RESET & BASE ===== */
        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #e8f0fe;
            color: #222;
            line-height: 1.6;
            overflow-x: hidden;
        }

        ul.pagination li.page-item a {
            background: #ffffff;
        }

        a {
            text-decoration: none !important;
            color: inherit;
        }

        img {
            max-width: 100%;
            display: block;
        }

        .container {
            width: 100%;
            max-width: 1180px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* ===== NAVBAR ===== */
        .navbar {
            background: #fff;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-inner {
            display: flex;
            align-items: center;
            height: 56px;
            gap: 0;
        }

        .navbar-logo {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
            margin-right: 24px;
        }

        .logo-icon {
            width: 34px;
            height: 34px;
            background: linear-gradient(135deg, #0052cc, #0072ff);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 14px;
        }

        .logo-text {
            font-size: 14px;
            font-weight: 800;
            color: #0052cc;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .logo-highlight {
            color: #00a651;
        }

        .navbar-links {
            display: flex;
            align-items: center;
            gap: 18px;
            flex: 1;
            justify-content: center;
        }

        .navbar-links a {
            font-size: 13px;
            font-weight: 500;
            color: #444;
            transition: color 0.3s;
            white-space: nowrap;
            position: relative;
        }

        .navbar-links a:hover,
        .navbar-links a.active {
            color: #0052cc;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-shrink: 0;
            margin-left: 20px;
        }

        .phone-number {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #333;
            white-space: nowrap;
        }

        .phone-number i {
            color: #0052cc;
            font-size: 13px;
        }

        .lang-buttons {
            display: flex;
            gap: 6px;
        }

        .btn-lang {
            padding: 5px 12px;
            border: 1px solid #0052cc;
            border-radius: 4px;
            background: #fff;
            color: #0052cc;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            white-space: nowrap;
        }

        .btn-lang:hover {
            background: #0052cc;
            color: #fff;
        }

        .btn-lang-dark {
            background: #1a1a2e;
            color: #fff;
            border-color: #1a1a2e;
        }

        .btn-lang-dark:hover {
            background: #0d0d1a;
        }

        /* Hamburger */
        .hamburger {
            display: none;
            background: none;
            border: none;
            font-size: 22px;
            color: #0052cc;
            cursor: pointer;
            padding: 4px;
        }

        /* Mobile menu */
        .mobile-menu {
            display: none;
            flex-direction: column;
            background: #fff;
            padding: 0 20px 16px;
            border-top: 1px solid #eee;
        }

        .mobile-menu.active {
            display: flex;
        }

        .mobile-menu a {
            padding: 12px 0;
            font-size: 14px;
            font-weight: 500;
            color: #333;
            border-bottom: 1px solid #f0f0f0;
        }

        .mobile-menu a:hover,
        .mobile-menu a.active {
            color: #0052cc;
        }

        .mobile-extras {
            padding-top: 12px;
        }

        .mobile-phone {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 0;
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }

        .mobile-phone i {
            color: #0052cc;
        }

        .mobile-lang-buttons {
            display: flex;
            gap: 8px;
            padding-top: 8px;
        }

        /* ===== HERO SECTION ===== */
        .hero {
            position: relative;
            background: linear-gradient(135deg, #1a5ba8 0%, #2876d9 40%, #3d8ff5 100%);
            overflow: hidden;
            min-height: 340px;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('{{ asset('frontend/assets/images/Background.png') }}') no-repeat center center;
            background-size: cover;
            opacity: 0.35;
            pointer-events: none;
        }

        .hero::after {
            content: '';
            position: absolute;
            top: -20%;
            right: -5%;
            width: 55%;
            height: 140%;
            background: radial-gradient(ellipse at center, rgba(255, 255, 255, 0.18) 0%, rgba(255, 255, 255, 0.1) 40%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        .hero-inner {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 340px;
            padding-top: 40px;
            padding-bottom: 40px;
        }

        .hero-content {
            flex: 1;
            max-width: 540px;
            padding-right: 20px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #fff;
            background: rgba(255, 255, 255, 0.25);
            padding: 5px 14px;
            border-radius: 5px;
            font-size: 10px;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: 1px;
            text-transform: uppercase;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .hero-badge::before {
            content: 'ðŸ“š';
            font-size: 12px;
        }

        .hero-title {
            font-size: 62px;
            font-weight: 900;
            color: #fff;
            line-height: 1;
            margin-bottom: 10px;
            letter-spacing: -1.5px;
            text-shadow: 0 3px 25px rgba(0, 0, 0, 0.2);
        }

        .hero-amp {
            font-style: italic;
            font-weight: 800;
            color: #fff;
        }

        .hero-subtitle {
            font-size: 21px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 14px;
            line-height: 1.3;
            text-shadow: 0 2px 15px rgba(0, 0, 0, 0.15);
        }

        .hero-desc {
            font-size: 15px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.98);
            line-height: 1.65;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        }

        .hero-image {
            flex-shrink: 0;
            width: 440px;
            display: flex;
            justify-content: flex-end;
            align-items: flex-end;
            align-self: flex-end;
            position: relative;
        }

        .hero-image::before {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.12) 0%, transparent 65%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        .hero-person {
            width: 100%;
            height: 300px;
            object-fit: contain;
            object-position: bottom center;
            border-radius: 0;
            position: relative;
            z-index: 1;
            filter: drop-shadow(0 12px 35px rgba(0, 0, 0, 0.3));
            /* Transparent PNG image - no background needed */
        }

        /* ===== STATS BAR ===== */
        .stats-bar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            padding: 16px 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border-bottom: 1px solid rgba(232, 237, 243, 0.5);
            position: relative;
        }

        .stats-bar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('./Assets/Background.png') no-repeat center center;
            background-size: cover;
            opacity: 0.05;
            pointer-events: none;
            z-index: 0;
        }

        .stats-inner {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }

        .stat-item {
            display: flex;
            align-items: baseline;
            gap: 6px;
        }

        .stat-number-blue {
            font-size: 24px;
            font-weight: 800;
            color: #0052cc;
        }

        .stat-number-red {
            font-size: 28px;
            font-weight: 800;
            color: #e63946;
        }

        .stat-bold {
            font-size: 18px;
            font-weight: 700;
            color: #222;
        }

        .stat-normal {
            font-size: 18px;
            font-weight: 400;
            color: #222;
        }

        .stat-label-sub {
            font-size: 14px;
            font-weight: 700;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-divider {
            width: 1px;
            height: 32px;
            background: #d0d5dd;
            flex-shrink: 0;
        }

        /* ===== COURSES SECTION ===== */
        .courses-section {
            padding: 20px 0 55px;
            background: linear-gradient(180deg, #a7c9fc 0%, #576f97 100%);
            position: relative;
            overflow: hidden;
        }

        .courses-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('./Assets/Background.png') no-repeat center center;
            background-size: cover;
            opacity: 0.4;
            pointer-events: none;
            z-index: 0;
        }

        .courses-section .container {
            position: relative;
            z-index: 1;
        }

        .section-title {
            text-align: center;
            font-size: 34px;
            font-weight: 600;
            color: #1F3D63;
            margin-bottom: 34px;
            line-height: 1.3;
            letter-spacing: 0.5px;
            margin-top: 14px;
            font-family: sans-serif;
        }

        .title-blue {
            color: #0052cc;
            font-weight: 600;
        }

        .courses-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
            margin-bottom: 36px;
        }

        /* Course Card */
        .course-card {
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 82, 204, 0.14);
        }

        .course-image {
            position: relative;
            width: 100%;
            height: 195px;
            overflow: hidden;
        }

        .course-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .course-badges {
            position: absolute;
            bottom: 12px;
            left: 12px;
            right: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .badge {
            padding: 4px 14px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        .badge-platform {
            background: #0052cc;
            color: #fff;
        }

        .badge-price {
            background: #0052cc;
            color: #fff;
            text-decoration: line-through;
        }

        .course-body {
            padding: 14px 16px 16px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .course-tags {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .tag {
            font-size: 12px;
            font-weight: 500;
            color: #666;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            display: inline-block;
        }

        .dot.green {
            background: #00a651;
        }

        .dot.blue {
            background: #0052cc;
        }

        .tag-category {
            color: #888;
        }

        .tag-category i {
            font-size: 11px;
            color: #aaa;
        }

        .course-title {
            font-size: 16px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 6px;
            line-height: 1.35;
        }

        .course-desc {
            font-size: 13px;
            color: #777;
            line-height: 1.5;
            margin-bottom: 14px;
            flex: 1;
        }

        .btn-enroll {
            display: block;
            width: 100%;
            text-align: center;
            padding: 11px 20px;
            background: #0052cc;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            border-radius: 8px;
            transition: background 0.3s, transform 0.2s;
            letter-spacing: 0.3px;
        }

        .btn-enroll i {
            margin-left: 6px;
            font-size: 10px;
        }

        .btn-enroll:hover {
            background: #003d99;
            transform: translateY(-1px);
        }

        /* Explore Button */
        .explore-btn-wrapper {
            text-align: center;
        }

        .btn-explore {
            display: inline-block;
            padding: 13px 32px;
            border: 2px solid #0052cc;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            border-radius: 8px;
            transition: all 0.3s ease;
            letter-spacing: 0.3px;
            background: #0052cc;
        }

        .btn-explore i {
            margin-left: 6px;
            font-size: 11px;
        }

        .btn-explore:hover {
            background: #fff;
            color: #0052cc;
        }

        .btn-enroll {
            display: block;
            width: 100%;
            text-align: center;
            padding: 11px 20px;
            background: #0052cc;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            border-radius: 8px;
            transition: all 0.3s ease;
            letter-spacing: 0.3px;
            border: 2px solid #0052cc;
        }

        .btn-enroll:hover {
            background: #fff;
            color: #0052cc;
        }

        .btn-enroll:hover i {
            color: #0052cc;
        }

        /* ===== RESPONSIVE - TABLET ===== */
        @media (max-width: 1024px) {
            .navbar-links {
                display: none;
            }

            .navbar-right {
                display: none;
            }

            .hamburger {
                display: block;
                margin-left: auto;
            }

            .hero-inner {
                flex-direction: column;
                text-align: center;
                min-height: auto;
                padding: 32px 20px;
                gap: 24px;
            }

            .hero-content {
                max-width: 100%;
                padding-right: 0;
            }

            .hero-title {
                font-size: 44px;
            }

            .hero-subtitle {
                font-size: 18px;
            }

            .hero-desc {
                font-size: 14px;
            }

            .hero-desc br {
                display: none;
            }

            .hero-image {
                width: 300px;
                justify-content: center;
                align-self: center;
            }

            .hero {
                background: linear-gradient(135deg, #1e5ba8 0%, #2b7fd9 50%, #4a9ff5 100%);
            }

            .hero-person {
                height: 220px;
                border-radius: 12px;
            }

            .courses-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 18px;
            }

            .stats-inner {
                gap: 12px;
            }

            .stat-number-blue {
                font-size: 18px;
            }

            .stat-number-red {
                font-size: 22px;
            }

            .stat-bold,
            .stat-normal {
                font-size: 14px;
            }

            .section-title {
                font-size: 20px;
            }
        }

        /* ===== RESPONSIVE - MOBILE ===== */
        @media (max-width: 600px) {
            .container {
                padding: 0 16px;
            }

            .navbar-inner {
                height: 50px;
            }

            .logo-text {
                font-size: 12px;
            }

            .logo-icon {
                width: 28px;
                height: 28px;
                font-size: 12px;
            }

            .hero {
                min-height: auto;
            }

            .hero-inner {
                padding: 24px 16px;
                gap: 16px;
            }

            .hero-badge {
                font-size: 12px;
            }

            .hero-title {
                font-size: 34px;
            }

            .hero-subtitle {
                font-size: 15px;
            }

            .hero-desc {
                font-size: 13px;
            }

            .hero-image {
                width: 220px;
            }

            .hero-person {
                height: 180px;
            }

            .stats-inner {
                flex-direction: column;
                gap: 6px;
                padding: 4px 0;
            }

            .stat-divider {
                display: none;
            }

            .stat-item {
                justify-content: center;
            }

            .stat-number-red {
                font-size: 20px;
            }

            .stat-label-sub {
                font-size: 11px;
            }

            .section-title {
                font-size: 18px;
                margin-bottom: 20px;
                padding: 0 8px;
            }

            .courses-section {
                padding: 28px 0 40px;
            }

            .courses-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .course-image {
                height: 180px;
            }

            .btn-explore {
                padding: 11px 24px;
                font-size: 14px;
            }
        }

        /* ===== LARGE SCREENS ===== */
        @media (min-width: 1400px) {
            .container {
                max-width: 1300px;
            }

            .hero-title {
                font-size: 64px;
            }

            .hero-image {
                width: 480px;
            }

            .hero-person {
                height: 320px;
            }
        }
    </style>
        <!-- ===== HERO SECTION ===== -->
    <section class="main-banner">
        <img class="banner-img" src="{{ asset('/images/course-banner.png') }}" alt="Courses Banner">
    </section>

    <!-- ===== COURSES SECTION ===== -->
    <section class="courses-section">
        <div class="container">
           
             <!-- Search + Category Row -->
            <form action="{{ route('courses.index') }}" method="GET" class="row justify-content-center g-2 p-3" style="background: #7c99c64d; border-radius: 20px;">

                <!-- Search (Bara) -->
                <div class="col-12 col-md-5">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="form-control border-start-0" placeholder="Search courses...">
                    </div>
                </div>

                <!-- Category (Chhoti) -->
                <div class="col-12 col-md-3">
                    <select name="category_id" class="form-control shadow-sm" onchange="this.form.submit()">
                        <option value="all">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Platform (Chhoti) -->
                <div class="col-12 col-md-3">
                    <select name="platform" class="form-control shadow-sm" onchange="this.form.submit()">
                        <option value="all">All Platforms</option>
                        @foreach($platforms as $platform)
                            <option value="{{ $platform }}" {{ request('platform') == $platform ? 'selected' : '' }}>
                                {{ $platform }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-1">
                    <button type="submit" class="btn btn-primary w-100" style="padding: 10px;"><i class="bi bi-search"></i></button>
                </div>

            </form>
        
            <h2 class="section-title"><span class="title-blue">60+ Free</span> Online Courses From Around The World!</h2>

            <div class="courses-grid">
                @foreach($courses as $course)
                    <div class="course-card">
                        <div class="course-image">

                             @auth
                                @if(Auth::user()->role === 'course_manager')
                                    <div class="position-absolute top-0 end-0 p-2 d-flex gap-1">
                                        <!-- Edit -->
                                        <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-sm btn-light shadow">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Delete -->
                                        <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger delete-btn"
                                                onclick="return confirm('Are you sure you want to delete this labor?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            @endauth

                            <img src="{{ $course->thumbnail_url }}" class="course-img">

                            <div class="course-badges">
                                <span class="badge badge-platform">{{ $course->platform }}</span>
                                <span class="badge badge-price">Rs {{ $course->price }}</span>
                            </div>
                        </div>

                        <div class="course-body">

                            <div class="course-tags">
                                <span class="tag">
                                    <span class="dot blue"></span> Free
                                </span>

                                <span class="tag tag-category">
                                    <i class="fa-solid fa-tag"></i>
                                    {{ strtolower($course->category->name) }}
                                </span>
                            </div>

                            <h3 class="course-title">
                               {{ $course->title }}
                            </h3>

                            <p class="course-desc">
                               {{ Str::limit($course->description, 90) }}
                            </p>

                            <a href="{{ $course->external_link }}" class="btn-enroll">
                                Enroll Now <i class="fas fa-chevron-right"></i>
                            </a>

                        </div>
                    </div>
                @endforeach

            </div>
             <div class="pagination" style="justify-content: center;">
                {{-- {{ $labors->links() }} --}}
                {{ $courses->appends(request()->query())->links() }}
            </div>

            
        </div>
    </section>

    <script>
        const hamburger = document.getElementById('hamburger');
        const mobileMenu = document.getElementById('mobileMenu');
        hamburger.addEventListener('click', () => {
            mobileMenu.classList.toggle('active');
            hamburger.querySelector('i').classList.toggle('fa-bars');
            hamburger.querySelector('i').classList.toggle('fa-times');
        });
    </script>



    {{-- <style>
        :root {
            --primary-blue: #0a65cc;
            --dark-blue: #0a65cc;
            --light-bg: #f8fbff;
            --text-main: #2d3436;
        }

        .main-body {
            font-family: 'Poppins', sans-serif !important;
            background-color: var(--light-bg) !important;
            color: var(--text-main) !important;
        }

        /* Hero */
        .hero-section {
            padding: 80px 0 40px;
            background: radial-gradient(circle at top right, #e3f2fd, transparent);
        }

        .text-gradient {
            background: linear-gradient(45deg, var(--primary-blue), var(--dark-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .search-container {
            max-width: 500px;
        }

        /* Cards */
        .course-card {
            border: none;
            border-radius: 20px;
            background: #fff;
            transition: .3s;
            overflow: hidden;
        }

        .course-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, .1);
        }

        .card-img-wrapper {
            height: 180px;
            overflow: hidden;
            position: relative;
        }


        /* .card-img-wrapper .courseimg {
            width: 100%;
            height: 100%;
            object-fit: fill;
        } */

        .main-heading {
            font-family: 'Poppins', sans-serif !important;
        }

        .card-img-top {
            width: 100%;
            height: 100%;
            object-fit: fill;
        }

        .price-tag,
        .platform-tag {
            position: absolute;
            bottom: 10px;
            background: rgba(255, 255, 255, 0.95);
            padding: 5px 15px;
            border-radius: 50px;
            font-weight: 700;
            color: var(--dark-blue);
            font-size: 0.9rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);

        }

        .platform-tag {
            left: 10px;
        }

        .price-tag {
            right: 10px;
            text-decoration: line-through;
        }

        .free {
            color: white;
            background: green;
            padding: 3px;
            border-radius: 50px;
        }

        .btn-enroll {
            border-radius: 12px !important;
            padding: 10px !important;
            font-weight: 600;
            background: linear-gradient(45deg, var(--primary-blue), var(--dark-blue)) !important;
            border: none !important;
            color: white !important;
            /* font-weight: bold !important; */
        }

        /* .old-price {
            text-decoration: line-through;
            color: #999;
        } */
    </style>
    <div class="main-body">
        <!-- HERO -->
        <div class="hero-section text-center">
            <div class="container">
                <h1 class="display-4 fw-bold main-heading">Learn. <span class="text-gradient">Earn. </span> Rise.</h1>
                <p class="lead text-muted mx-auto" style="max-width: 600px;">Sindh's own digital learning portal - where
                    skills turn into income.</p>

                <!-- Search + Category Row -->
                <div class="container mt-4">
                    <form action="{{ route('courses.index') }}" method="GET" class="row justify-content-center g-2">

                        <!-- Search (Bara) -->
                        <div class="col-12 col-md-5">
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    class="form-control border-start-0" placeholder="Search courses...">
                            </div>
                        </div>

                        <!-- Category (Chhoti) -->
                        <div class="col-12 col-md-3">
                            <select name="category_id" class="form-control shadow-sm" onchange="this.form.submit()">
                                <option value="all">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Platform (Chhoti) -->
                        <div class="col-12 col-md-3">
                            <select name="platform" class="form-control shadow-sm" onchange="this.form.submit()">
                                <option value="all">All Platforms</option>
                                @foreach($platforms as $platform)
                                    <option value="{{ $platform }}" {{ request('platform') == $platform ? 'selected' : '' }}>
                                        {{ $platform }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-1">
                            <button type="submit" class="btn btn-primary w-100" style="padding: 10px;"><i class="bi bi-search"></i></button>
                        </div>

                    </form>
                </div>


            </div>
        </div>

        <!-- COURSES -->
        <main class="container my-5">

            <div id="noCourses" class="text-center text-muted mb-5" style="{{ $courses->count() ? 'display:none' : '' }}">
                <h5>No courses available</h5>
            </div>

            <div class="row g-4" id="courseContainer">
                @foreach($courses as $course)
                    <div class="col-md-6 col-lg-4 course-card-wrapper">
                        <div class="card course-card h-100">

                            <div class="card-img-wrapper">
                                <img src="{{ $course->thumbnail_url }}" class="card-img-top">

                                <div class="price-tag">
                                    Rs {{ $course->price }}
                                  
                                </div>
                                @if($course->platform)
                                    <div class="platform-tag">
                                        {{ $course->platform }}
                                    </div>
                                @endif


                             
                                @auth
                                    @if(Auth::user()->role === 'course_manager')
                                        <div class="position-absolute top-0 end-0 p-2 d-flex gap-1">
                                            <!-- Edit -->
                                            <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-sm btn-light shadow">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <!-- Delete -->
                                            <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger delete-btn"
                                                    onclick="return confirm('Are you sure you want to delete this labor?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                @endauth
                            </div>

                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge badge-type text-primary">
                                        <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>Free
                                    </span>
                                    <small class="text-muted category-text"><i
                                            class="bi bi-tag-fill me-1"></i>{{ strtolower($course->category->name) }}</small>
                                </div>
                              
                                <h5 class="card-title fw-bold mb-3">{{ $course->title }}</h5>
                                <p>{{ Str::limit($course->description, 90) }}</p>

                                <a href="{{ $course->external_link }}" target="_blank"
                                    class="btn btn-primary btn-enroll fw-bold w-100">
                                    Enroll Now
                                    <i class="bi bi-arrow-right-short ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="pagination" style="justify-content: center;">
                
                {{ $courses->appends(request()->query())->links() }}

            </div>
        </main>
    </div> --}}

@endsection

@push('frontend_scripts')
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Fonts: Poppins (Professional & Modern) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
@endpush