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

    a {
        text-decoration: none;
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
        height: 62px;
        gap: 0;
    }

    .navbar-logo {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
        margin-right: 32px;
    }

    .logo-icon {
        width: 38px;
        height: 38px;
        background: linear-gradient(135deg, #0052cc, #0072ff);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 16px;
        font-weight: 900;
    }

    .logo-text {
        font-size: 15px;
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
        gap: 22px;
        flex: 1;
    }

    .navbar-links a {
        font-size: 13px;
        font-weight: 500;
        color: #444;
        transition: color 0.3s;
        white-space: nowrap;
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
        margin-left: auto;
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
        padding: 6px 14px;
        border: 1.5px solid #0052cc;
        border-radius: 5px;
        background: #fff;
        color: #0052cc;
        font-size: 12px;
        font-weight: 700;
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
        margin-left: auto;
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

    .mobile-menu a:hover {
        color: #0052cc;
    }

    /* ===== HERO SECTION ===== */
    .hero {
        position: relative;
        background: linear-gradient(135deg, #1a5ba8 0%, #2876d9 40%, #3d8ff5 100%);
        overflow: hidden;
        min-height: 370px;
    }

    .hero-bg {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('{{ asset('frontend/assets/images/JobsBanner.png') }}') no-repeat center center;
        background-size: cover;
        opacity: 1;
        pointer-events: none;
        z-index: 0;
    }

    .hero-inner {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 300px;
        padding: 36px 0 0 0;
    }

    .hero-content {
        flex: 1;
        max-width: 580px;
        padding: 0 20px 36px 0;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #fff;
        background: rgba(255, 255, 255, 0.22);
        padding: 5px 14px;
        border-radius: 5px;
        font-size: 11px;
        font-weight: 800;
        margin-bottom: 14px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .hero-title {
        font-size: 48px;
        font-weight: 900;
        color: #fff;
        line-height: 1.08;
        margin-bottom: 0;
        letter-spacing: -1px;
        text-shadow: 0 3px 25px rgba(0, 0, 0, 0.2);
    }

    .hero-title-row {
        display: flex;
        align-items: baseline;
        flex-wrap: wrap;
        gap: 0 10px;
    }

    .hero-highlight {
        color: #ffe600;
    }

    .hero-added {
        font-size: 42px;
        font-weight: 900;
        color: #fff;
        line-height: 1.08;
    }

    .hero-from {
        font-size: 48px;
        font-weight: 900;
        color: #fff;
        line-height: 1.1;
        margin-top: 4px;
    }

    .hero-desc {
        font-size: 16px;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.95);
        line-height: 1.6;
        margin-top: 14px;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
    }

    .hero-image {
        flex-shrink: 0;
        width: 420px;
        display: flex;
        justify-content: flex-end;
        align-items: flex-end;
        align-self: flex-end;
    }

    /* ===== FILTER SECTION ===== */
    .filter-section {
        background: #fff;
        padding: 22px 0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    .filter-inner {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .filter-title {
        font-size: 24px;
        font-weight: 600;
        white-space: nowrap;
        flex-shrink: 0;
        font-family: sans-serif;
        color: #233660;
    }

    .filter-bar {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
        flex-wrap: wrap;
    }

    .filter-select {
        flex: 1;
        min-width: 180px;
        padding: 11px 16px;
        border: 1.5px solid #d0d5dd;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        color: #555;
        background: #fff;
        cursor: pointer;
        appearance: none !important;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23666' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        padding-right: 36px;
        transition: border-color 0.3s;
    }

    .filter-select:focus {
        outline: none;
        border-color: #0052cc;
    }

    .btn-filter {
        padding: 11px 36px;
        background: #0052cc;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.3s;
        white-space: nowrap;
    }

    .btn-filter:hover {
        background: #003d99;
    }

    .btn-clear {
        padding: 11px 36px;
        background: #6c757d;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.3s;
        white-space: nowrap;
    }

    .btn-clear:hover {
        background: #5a6268;
    }

    /* ===== WORKERS SECTION ===== */
    .workers-section {
        padding: 45px 0 55px;
        position: relative;
        overflow: hidden;
    }

    .workers-bg {
        position: absolute;
        inset: 0;
        pointer-events: none;
        z-index: 0;
    }

    /* Gradient layer */
    .workers-bg::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(to right, #1768C5, #8ec5ff);
        opacity: 0.6;
    }

    /* Image layer */
    .workers-bg::after {
        content: "";
        position: absolute;
        inset: 0;
        /* background: url('{{ asset('frontend/assets/images/Background.png') }}') no-repeat center center; */
        background: url('{{ asset('frontend/assets/images/BackgroundTexture.png') }}') no-repeat center center;
        background-size: cover;
        opacity: 0.10;
    }

    .workers-section .container {
        position: relative;
        z-index: 1;
    }

    .section-title {
        text-align: center;
        font-size: 34px;
        font-weight: 600;
        color: #233660;
        margin-bottom: 36px;
        line-height: 1.3;
        font-family: sans-serif;
    }

    .title-blue {
        color: #0052cc;
        font-weight: 900;
    }

    .workers-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 18px;
        margin-bottom: 36px;
    }

    /* Worker Card */
    .worker-card {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 14px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .worker-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 10px 32px rgba(0, 82, 204, 0.15);
    }

    .worker-image {
        width: 100%;
        height: 175px;
        overflow: hidden;
        background: #f0f4f8;
    }

    .worker-img {
        width: 100%;
        height: 100%;
        /* object-fit: cover; */
        object-fit: contain;
        display: block;
        background: #c2dfff;
    }

    .worker-body {
        padding: 14px 16px 18px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .worker-status {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 8px;
    }

    .status-label {
        font-size: 13px;
        font-weight: 700;
        color: #1a1a1a;
    }

    .status-value {
        font-size: 13px;
        font-weight: 500;
        color: #333;
    }

    .status-value.approved {
        color: #333;
    }

    .worker-name {
        font-size: 17px;
        font-weight: 800;
        color: #1a1a1a;
        margin-bottom: 8px;
        line-height: 1.25;
    }

    .worker-profession,
    .worker-skill {
        font-size: 13px;
        color: #333;
        line-height: 1.6;
        margin-bottom: 2px;
    }

    .worker-profession strong,
    .worker-skill strong {
        font-weight: 700;
        color: #1a1a1a;
    }

    /* View All Button */
    .view-all-wrapper {
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
        }

        .hero-inner {
            flex-direction: column;
            text-align: center;
            min-height: auto;
            padding: 28px 0 0 0;
            gap: 20px;
        }

        .hero-content {
            max-width: 100%;
            padding: 0 0 28px 0;
        }

        .hero-title {
            font-size: 36px;
        }

        .hero-added {
            font-size: 32px;
        }

        .hero-from {
            font-size: 36px;
        }

        .hero-image {
            width: 280px;
            justify-content: center;
            align-self: center;
        }

        .filter-inner {
            flex-direction: column;
            align-items: flex-start;
            gap: 14px;
        }

        .filter-bar {
            width: 100%;
        }

        .workers-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    /* ===== RESPONSIVE - MOBILE ===== */
    @media (max-width: 600px) {
        .container {
            padding: 0 16px;
        }

        .navbar-inner {
            height: 52px;
        }

        .hero-title {
            font-size: 26px;
        }

        .hero-added {
            font-size: 24px;
        }

        .hero-from {
            font-size: 26px;
        }

        .hero-desc {
            font-size: 13px;
        }

        .hero-image {
            width: 200px;
        }

        .filter-section {
            padding: 18px 0;
        }

        .filter-title {
            font-size: 16px;
        }

        .filter-bar {
            flex-direction: column;
        }

        .filter-select {
            width: 100%;
            min-width: 100%;
        }

        .btn-filter,
        .btn-clear {
            width: 100%;
            text-align: center;
        }

        .section-title {
            font-size: 20px;
            margin-bottom: 22px;
        }

        .workers-section {
            padding: 28px 0 40px;
        }

        .workers-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .worker-image {
            height: 200px;
        }
    }

    /* ===== LARGE SCREENS ===== */
    @media (min-width: 1400px) {
        .container {
            max-width: 1300px;
        }

        .hero-title {
            font-size: 54px;
        }

        .hero-added {
            font-size: 48px;
        }

        .hero-from {
            font-size: 54px;
        }

        .hero-image {
            width: 460px;
        }
    }
</style>

<style>
    .card-actions {
        position: absolute;
        /* right: 0; */
        margin-left: 7px;
        margin-top: 8px;
    }

    .card-actions a, .card-actions button {
        padding: 3px 7px;
        /* background: #bfc2c5; */
        /* color: #0052cc; */
        border: 0;
    }
</style>

 <!-- ===== HERO SECTION ===== -->
    <section class="hero">
        <div class="hero-bg"></div>
    </section>

    <!-- ===== FILTER SECTION ===== -->
    
    <section class="filter-section">
        <div class="container">
            <div class="filter-inner">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                <form method="GET" class="row" style="width: 100%;">
                    <div class="col-md-3">
                        <select name="profession_id" class="form-control select2 profession">
                            <option value="">-- Select Profession --</option>
                            @foreach($professions as $profession)
                                <option value="{{ $profession->id }}" {{ request('profession_id') == $profession->id ? 'selected' : '' }}>
                                    {{ $profession->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                
                    <div class="col-md-3">
                        <select name="skill_id" class="form-control select2 skill">
                            <option value="">-- Select Skill --</option>
                            @foreach($skills as $skill)
                                <option value="{{ $skill->id }}" {{ request('skill_id') == $skill->id ? 'selected' : '' }}>
                                    {{ $skill->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                
                    <div class="col-md-3">
                        <input type="text" name="location" class="form-control" placeholder="Enter Location" value="{{ request('location') }}">
                    </div>
                
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-50">Filter</button>
                        <a href="{{ route('skilled-labour.index') }}" class="btn btn-secondary w-50">Clear</a>
                    </div>
                </form>


            </div>
        </div>
    </section>

    <!-- ===== WORKERS SECTION ===== -->
    <section class="workers-section">
        <div class="workers-bg"></div>
        <div class="container">
            <h2 class="section-title"><span class="title-blue">1000+</span> Skilled Labourers Now Listed!
             @auth
                @if(Auth::user()->role === 'rider')
                    <a href="{{ route('skilled-labour.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Labor
                    </a>
                @endif
            @endauth
            </h2>
             
            <div class="workers-grid">
                <!-- Worker Card 1 -->
               
                @foreach($labors as $labor)
                    <div class="worker-card" data-labor-id="{{ $labor->id }}" style="cursor: pointer;">
                        @if ($labor->user_id == auth()->id())
                            <div class="card-actions">
                                <a href="{{ route('skilled-labour.edit', $labor->id) }}" class="btn btn-sm btn-primary edit-btn">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('skilled-labour.destroy', $labor->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger delete-btn" onclick="return confirm('Are you sure you want to delete this labor?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                        <div class="labor-card" data-labor-id="{{ $labor->id }}" style="cursor: pointer;">
                            <div class="worker-image">
                                <img src="{{ asset($labor->image) }}" alt="{{ $labor->name }}" class="worker-img">
                            </div>
                            <div class="worker-body">
                                <h3 class="worker-name">{{ $labor->name }}</h3>
                                <p class="worker-profession"><strong>Status:</strong> {{ $labor->status == 1 ? 'Approved' : 'Pending' }}</p>
                                <p class="worker-profession"><strong>Tailor:</strong> {{ $labor->profession->name }}</p>
                                <p class="worker-skill"><strong>Skill:</strong> {{ $labor->skill->name }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>

            <div class="pagination" style="justify-content: center;">
                {{-- {{ $labors->links() }} --}}
                {{ $labors->appends(request()->query())->links() }}
            </div>
        </div>
    </section>

    <script>
        const hamburger = document.getElementById('hamburger');
        const mobileMenu = document.getElementById('mobileMenu');
        hamburger.addEventListener('click', () => {
            mobileMenu.classList.toggle('active');
            const icon = hamburger.querySelector('i');
            icon.classList.toggle('fa-bars');
            icon.classList.toggle('fa-times');
        });
    </script>



<!-- Modal -->
<div class="modal fade" id="laborModal" tabindex="-1" aria-labelledby="laborModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="laborModalLabel">Labor Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="laborModalBody">
                <!-- Content will be loaded here via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .labor-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
    }
    
    .labor-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .card-img-top-container {
        height: 200px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
    }
    
    .labor-image {
        height: 100%;
        width: auto;
        object-fit: cover;
    }
    
    .detail-row {
        margin-bottom: 10px;
    }
    
    .detail-label {
        font-weight: bold;
        color: #495057;
    }
    
    .detail-value {
        color: #212529;
    }
</style>
@endpush

@push('frontend_scripts')
<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script>
    $(document).ready(function() {
        $('.labor-card').click(function() {
            const laborId = $(this).data('labor-id');
            
            $.ajax({
                url: `/skilled-helper/${laborId}`,
                method: 'GET',
                success: function(response) {
                    $('#laborModalBody').html(response);
                    $('#laborModal').modal('show');
                },
                error: function(xhr) {
                    alert('Error loading labor details');
                }
            });
        });

        $('.select2.profession').select2({
            placeholder: 'Select Profession',
            allowClear: true,
            width: '100%' // important for responsiveness
        });

        $('.select2.skill').select2({
            placeholder: 'Select Skill',
            allowClear: true,
            width: '100%' // important for responsiveness
        });

        // Delete confirmation
        function confirmDelete(e) {
            if(!confirm('Are you sure you want to delete this labor?')) {
                e.preventDefault();
            }
        }
        
        // Attach event listeners to all delete buttons
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', confirmDelete);
        });

        $(document).on('click', '.showbtn', function(e) {
            $('.labor-details').hide();
        });  

        // Enable/disable show button based on checkbox
        $(document).on('change', '#agreeTerms', function() {
            $('#showContactBtn').prop('disabled', !this.checked);
        });
        
        // Show contact info when button is clicked
        $(document).on('click', '#showContactBtn', function() {
            // Unmask phone number
            $('#maskedPhone').hide();
            $('#actualPhone').show();

            // Unmask CNIC
            $('#maskedCnic').hide();
            $('#actualCnic').show();
            
            // Close modal
            $('#termsModal').modal('hide');
            $('.showbtn').hide();
            $('.labor-details').show();
        });

        jQuery('#agreeTerms').change(function() {
            alert();
            if (jQuery(this).is(':checked')) {
                jQuery('#showContactBtn').removeAttr('disabled');
            } else {
                jQuery('#showContactBtn').attr('disabled', 'disabled');
            }
        });

    });
</script>
@endpush