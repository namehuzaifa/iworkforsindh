@extends('frontend.layouts.app')

@section('main')

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    img.card-img-top.labor-image {
        width: 200px;
        height: 150px;
        object-fit: contain;
    }

    section.faq-section {
        background: #ffffff;
        border-radius: 20px;
    }

    a.btn.btn-sm.btn-primary.edit-btn {
        padding: 5px 10px;
    }

    button.btn.btn-sm.btn-danger.delete-btn {
        padding: 5px 11px;
    }
    /* .select2-container .select2-selection--single {
        height: 38px !important;
        padding: 5px 10px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
    } */
</style>

<style>
        /* ===== BASIC RESET ===== */
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
    }

    body {
    background-color: #0a2a6e;
    overflow-x: hidden;
    }

    /* ===== PAGE WRAPPER ===== */
    .page-wrapper {
    background-image: url('{{ asset('frontend/assets/images/Background.png') }}');
    background-size: cover;
    background-position: center top;
    background-repeat: no-repeat;
    min-height: 100vh;
    }

    /* ===== CONTAINER ===== */
    .container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 20px;
    }

    /* ===== BANNER ===== */
    .main-banner {
    width: 100%;
    position: relative;
    overflow: hidden;
    }

    .main-banner img.banner-img {
    width: 100%;
    height: auto;
    display: block;
    max-height: 550px;
    object-fit: cover;
    object-position: center top;
    }

    /* ===== FEATURES RIBBON ===== */
    .features-ribbon {
    background: rgba(255, 255, 255, 0.3);
    padding: 18px 0;
    border-top: 1px solid rgba(255, 255, 255, 0.35);
    border-bottom: 1px solid rgba(200, 220, 255, 0.35);
    margin-top: 15px;
    margin-bottom: 11px;
    }

    .ribbon-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0;
    flex-wrap: wrap;
    }

    .feature-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 6px 40px;
    color: #1a2f6e;
    }

    .feature-item i {
    font-size: 22px;
    color: #1a3a8a;
    flex-shrink: 0;
    }

    .feature-item span {
    font-size: 15px;
    font-weight: 700;
    color: #1a2f6e;
    letter-spacing: 0.3px;
    white-space: nowrap;
    }

    .divider {
    width: 1.5px;
    height: 35px;
    background: rgba(100, 140, 200, 0.35);
    flex-shrink: 0;
    }

    /* ===== LAYOUT GRID ===== */
    .layout-grid {
    display: grid;
    grid-template-columns: repeat(6, 220px);
    gap: 20px;
    justify-content: center;
    padding: 10px;
    }

    .layout-card {
    position: relative;
    background: #fff;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    width: 100%;
    cursor: pointer;
    overflow: hidden;
    transition: transform 0.2s ease;
    }

    .layout-card:hover {
    transform: translateY(-10px);
    }

    .layout-card img {
    width: 100%;
    border-radius: 10px;
    display: block;
    }

    .select-btn {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #0077cc;
    color: white;
    border: none;
    padding: 10px 5px;
    border-radius: 5px;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
    font-size: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    cursor: pointer;
    width: 150px;
    white-space: nowrap;
    overflow: visible;
    }

    .layout-card:hover .select-btn {
    opacity: 1;
    pointer-events: auto;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1200px) {
    .layout-grid {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    }
    }

    @media (max-width: 992px) {
    .divider {
        display: none;
    }

    .feature-item {
        padding: 8px 20px;
    }

    .ribbon-wrapper {
        gap: 10px;
        justify-content: center;
    }

    .layout-grid {
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    }
    }

    @media (max-width: 768px) {
    .main-banner img.banner-img {
        max-height: 350px;
    }

    .feature-item span {
        font-size: 14px;
    }

    .layout-grid {
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    }
    }

    @media (max-width: 600px) {
    .feature-item {
        width: 100%;
        justify-content: center;
        padding: 8px 15px;
    }

    .feature-item span {
        font-size: 13px;
    }

    .layout-grid {
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 15px;
        padding: 10px;
    }

    .select-btn {
        width: 120px;
        font-size: 11px;
    }

    .main-banner img.banner-img {
        max-height: 250px;
    }
    }

    @media (max-width: 400px) {
    .layout-grid {
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    }

    .select-btn {
        width: 100px;
        font-size: 10px;
    }

    .feature-item span {
        font-size: 12px;
    }
    }
</style>

<div class="page-wrapper">

     @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <!-- BANNER -->
    <section class="main-banner">
      <img class="banner-img" src="{{ asset('frontend/assets/images/BannerNew.jpeg') }}" alt="AI Resume Maker Banner">
    </section>

    <!-- FEATURES RIBBON -->
    <div class="features-ribbon">
        <div class="container ribbon-wrapper">

            <div class="feature-item">
                <i class="fa-solid fa-globe"></i>
                <span>50+ International Formats &amp; Styles</span>
            </div>

            <div class="divider"></div>

            <div class="feature-item">
                <i class="fa-solid fa-gears"></i>
                <span>ATS-Friendly Templates</span>
            </div>

            <div class="divider"></div>

            <div class="feature-item">
                <i class="fa-solid fa-bolt"></i>
                <span>Fast &amp; Easy Resume Building</span>
            </div>

        </div>
    </div>


    <div class="">
        <div class="cvmaker_div">
            <iframe width="100%" height="3000px" src="https://iwork4sindh.com/public/CVBulider/index.html"></iframe>
        </div>
    </div>

</div>

{{-- <div class="container py-4">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="cvmaker_div">
        <iframe width="100%" height="4000px" src="https://iwork4sindh.com/public/CVBulider/index.html"></iframe>
    </div>
</div> --}}

@endsection

@push('frontend_scripts')
<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

@endpush