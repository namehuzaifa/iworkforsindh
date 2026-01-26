@extends('frontend.layouts.app')

@section('main')

    <style>
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
            right: 335px;
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
                                    {{-- <span class="free">FREE</span> --}}
                                </div>
                                @if($course->platform)
                                    <div class="platform-tag">
                                        {{ $course->platform }}
                                        {{-- <span class="free">FREE</span> --}}
                                    </div>
                                @endif


                                {{-- EDIT / DELETE (ONLY FOR LOGGED IN USER) --}}
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
                                {{-- <i class="bi bi-tag-fill me-1"></i>
                                <small class="text-muted category-text">

                                    {{ strtolower($course->category->name) }}
                                </small> --}}

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
                {{-- {{ $labors->links() }} --}}
                {{ $courses->appends(request()->query())->links() }}

            </div>
        </main>
    </div>

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