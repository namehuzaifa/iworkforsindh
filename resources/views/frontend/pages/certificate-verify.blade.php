@extends('frontend.layouts.app')

@section('title', 'Certificate Verification - ' . config('app.name'))

@section('css')
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Pinyon+Script&family=Alex+Brush&family=Cinzel:wght@500;700;800&family=Lora:ital,wght@0,400;0,600;1,400&family=Outfit:wght@300;400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('Certificate-Generator/extra.css') }}" />
    
    <style>
        .verification-portal-container {
            padding: 1.5rem 0 2rem;
            background: #f1f5f9;
            min-height: 60vh;
            font-family: 'Outfit', sans-serif;
        }

        /* ── Success Card ── */
        .portal-card {
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(6, 92, 196, 0.08);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .portal-header {
            padding: 1.5rem 1.5rem 1.2rem;
            text-align: center;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .portal-header.success-header {
            background: linear-gradient(135deg, #065cc4 0%, #002d62 100%);
        }
        .portal-header.success-header::after {
            content: '';
            position: absolute;
            top: -40%; right: -20%;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
            pointer-events: none;
        }

        .success-check-circle {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            border: 2px solid rgba(255,255,255,0.4);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.6rem;
            animation: pop-in 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }
        .success-check-circle svg {
            width: 22px; height: 22px;
        }
        .success-check-circle .check-path {
            stroke: #fff;
            stroke-width: 3;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
            stroke-dasharray: 24;
            stroke-dashoffset: 24;
            animation: draw-check 0.5s 0.4s ease forwards;
        }
        @keyframes pop-in {
            0% { transform: scale(0); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes draw-check {
            to { stroke-dashoffset: 0; }
        }

        .portal-header h3 {
            font-weight: 700;
            margin: 0 0 0.15rem;
            font-size: 1.15rem;
            position: relative;
            color: #ffffff;
        }

        .portal-header p {
            margin: 0;
            font-size: 0.78rem;
            opacity: 0.8;
            font-weight: 300;
        }

        /* Clean Table */
        .verify-table {
            width: 100%;
            margin: 0;
            border-collapse: collapse;
        }
        .verify-table tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.15s ease;
        }
        .verify-table tr:hover {
            background: #f8fafc;
        }
        .verify-table tr:last-child {
            border-bottom: none;
        }
        .verify-table th {
            width: 40%;
            padding: 0.6rem 1.2rem;
            font-size: 0.8rem;
            font-weight: 600;
            color: #64748b;
            background: transparent;
            text-align: left;
            vertical-align: middle;
        }
        .verify-table th i {
            width: 20px;
            text-align: center;
            color: #065cc4;
            margin-right: 0.35rem;
            font-size: 0.72rem;
            opacity: 0.7;
        }
        .verify-table td {
            padding: 0.6rem 1.2rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: #55719b;
            vertical-align: middle;
        }
        .cert-number-val {
            color: #065cc4;
            font-weight: 700;
            font-family: 'Outfit', monospace;
            letter-spacing: 0.5px;
        }

        .status-badge {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            color: white;
            padding: 0.3rem 0.85rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            box-shadow: 0 2px 8px rgba(22, 163, 74, 0.25);
        }

        .certificate-preview-section {
            padding: 1.2rem 1.5rem 1.5rem;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
        }
        .certificate-preview-section h5 {
            text-align: center;
            font-weight: 700;
            color: #065cc4;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .certificate-wrapper {
            width: 100%;
            max-width: 650px;
            margin: 0 auto;
            container-type: inline-size;
            container-name: certificate;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
            border-radius: 8px;
            overflow: hidden;
        }

        .signature-img-placeholder {
            font-family: 'Great Vibes', 'Pinyon Script', 'Alex Brush', cursive !important;
            font-size: 3.5cqw !important;
            white-space: nowrap !important;
            display: inline-block;
            margin-top: -3.5cqw;
            margin-bottom: 0.3cqw;
        }
        @supports not (width: 1cqw) {
            .signature-img-placeholder {
                font-size: 16px !important;
            }
        }
        .qr-code-section #verify-page-qrcode img,
        .qr-code-section #verify-page-qrcode canvas {
            width: 9cqw !important;
            height: 9cqw !important;
            min-width: 36px !important;
            min-height: 36px !important;
            max-width: 80px;
            max-height: 80px;
            object-fit: contain;
        }

        /* ── Error State – Premium Design ── */
        .error-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 0;
        }
        .error-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.08);
            overflow: hidden;
            max-width: 550px;
            width: 100%;
        }
        .error-card-header {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            padding: 2.5rem 2rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .error-card-header::before {
            content: '';
            position: absolute;
            top: -30px;
            right: -30px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
        }
        .error-card-header::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: -20px;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }
        .error-icon-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.2rem;
            animation: pulse-ring 2s ease-in-out infinite;
        }
        @keyframes pulse-ring {
            0%, 100% { box-shadow: 0 0 0 0 rgba(255,255,255,0.3); }
            50% { box-shadow: 0 0 0 15px rgba(255,255,255,0); }
        }
        .error-icon-circle i {
            font-size: 2.5rem;
            color: #fff;
        }
        .error-card-header h3 {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            color: #fff;
            margin: 0 0 0.3rem;
        }
        .error-card-header p {
            color: rgba(255,255,255,0.8);
            font-size: 0.9rem;
            margin: 0;
        }
        .error-card-body {
            padding: 2.5rem 2rem;
            text-align: center;
        }
        .error-code-badge {
            display: inline-block;
            background: #fef2f2;
            color: #dc2626;
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 1.5px;
            padding: 0.5rem 1.2rem;
            border-radius: 50px;
            border: 1px solid #fecaca;
            margin-bottom: 1.5rem;
        }
        .error-message {
            font-family: 'Outfit', sans-serif;
            font-size: 0.95rem;
            color: #475569;
            line-height: 1.7;
            margin-bottom: 2rem;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }
        .error-suggestions {
            background: #f8fafc;
            border-radius: 12px;
            padding: 1.2rem 1.5rem;
            margin-bottom: 2rem;
            text-align: left;
        }
        .error-suggestions h6 {
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            font-size: 0.85rem;
            color: #334155;
            margin-bottom: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .error-suggestions ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .error-suggestions ul li {
            font-size: 0.88rem;
            color: #64748b;
            padding: 0.3rem 0;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .error-suggestions ul li i {
            color: #94a3b8;
            font-size: 0.75rem;
            flex-shrink: 0;
        }
        .error-actions {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            align-items: center;
        }
        .btn-error-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #065cc4, #002d62);
            color: #fff;
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(6,92,196,0.25);
            border: none;
        }
        .btn-error-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(6,92,196,0.35);
            color: #fff;
            text-decoration: none;
        }
        .btn-error-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: transparent;
            color: #64748b;
            font-family: 'Outfit', sans-serif;
            font-weight: 500;
            font-size: 0.85rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
        }
        .btn-error-secondary:hover {
            color: #065cc4;
            text-decoration: none;
        }

        /* ── Manual Search Form ── */
        .manual-verify-section {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e2e8f0;
        }
        .manual-verify-section h6 {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            color: #1e293b;
            margin-bottom: 0.8rem;
            text-align: center;
        }
        .manual-verify-section p {
            font-family: 'Outfit', sans-serif;
            font-size: 0.85rem;
            color: #64748b;
            text-align: center;
            margin-bottom: 1rem;
        }
        .manual-search-form {
            display: flex;
            gap: 0.5rem;
            max-width: 380px;
            margin: 0 auto;
        }
        .manual-search-form input {
            flex: 1;
            padding: 0.7rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-family: 'Outfit', sans-serif;
            font-size: 0.95rem;
            font-weight: 500;
            color: #0f172a;
            outline: none;
            transition: border-color 0.2s ease;
        }
        .manual-search-form input:focus {
            border-color: #065cc4;
            box-shadow: 0 0 0 3px rgba(6, 92, 196, 0.1);
        }
        .manual-search-form input::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }
        .manual-search-form button {
            padding: 0.7rem 1.3rem;
            background: linear-gradient(135deg, #065cc4, #002d62);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .manual-search-form button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(6, 92, 196, 0.3);
        }
    </style>
@endsection

@section('main')
    <div class="verification-portal-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    @if(isset($error) || !$certificate)
                        {{-- ── Invalid Certificate – Premium Error View ── --}}
                        <div class="error-container">
                            <div class="error-card">
                                <div class="error-card-header">
                                    <div class="error-icon-circle">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <h3>Verification Failed</h3>
                                    <p>This certificate could not be verified</p>
                                </div>
                                <div class="error-card-body">
                                    <div class="error-code-badge">CERT NOT FOUND</div>
                                    <p class="error-message">
                                        The certificate you are trying to verify does not exist in our records, has been revoked, or the verification link is invalid.
                                    </p>
                                    <div class="error-suggestions">
                                        <h6><i class="fas fa-lightbulb"></i> Possible reasons</h6>
                                        <ul>
                                            <li><i class="fas fa-chevron-right"></i> The QR code may be damaged or altered</li>
                                            <li><i class="fas fa-chevron-right"></i> The certificate link has expired or been revoked</li>
                                            <li><i class="fas fa-chevron-right"></i> The URL was typed incorrectly</li>
                                        </ul>
                                    </div>

                                    {{-- Manual Verification Search --}}
                                    <div class="manual-verify-section">
                                        <h6><i class="fas fa-search"></i> Verify Manually</h6>
                                        <p>Enter your certificate number (e.g. A00000) to verify</p>
                                        <form class="manual-search-form" action="" method="GET" id="manualVerifyForm">
                                            <input type="text" id="certNumberInput" placeholder="Enter Certificate No." required />
                                            <button type="submit"><i class="fas fa-arrow-right"></i> Verify</button>
                                        </form>
                                    </div>

                                    <div class="error-actions" style="margin-top: 1.5rem;">
                                        <a href="{{ route('website.home') }}" class="btn-error-primary">
                                            <i class="fas fa-home"></i> Go to Homepage
                                        </a>
                                        <a href="mailto:support@iwork4sindh.com" class="btn-error-secondary">
                                            <i class="fas fa-envelope"></i> Contact Support
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- ── Valid Certificate – Data + Certificate View ── --}}
                        <div class="portal-card">
                            <div class="portal-header success-header">
                                <div class="success-check-circle">
                                    <svg viewBox="0 0 24 24"><polyline class="check-path" points="6 12 10 16 18 8"/></svg>
                                </div>
                                <h3>Certificate Verified Successfully</h3>
                                <p>This certificate is authentic and issued by iWork4Sindh</p>
                            </div>
                            <div style="padding: 10px 25px;">
                                <table class="verify-table">
                                    <tr>
                                        <th><i class="fas fa-hashtag"></i> Certificate No.</th>
                                        <td><span class="cert-number-val">{{ $certificate->certificate_number }}</span></td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-user"></i> Recipient Name</th>
                                        <td>{{ $certificate->first_name }} {{ $certificate->last_name }}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-book"></i> Course Name</th>
                                        <td>{{ $certificate->course_name }}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-clock"></i> Duration</th>
                                        <td>{{ $certificate->duration }}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-calendar-alt"></i> Issue Date</th>
                                        <td>{{ $certificate->certificate_date ? $certificate->certificate_date->format('d F, Y') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-shield-alt"></i> Status</th>
                                        <td>
                                            <span class="status-badge">
                                                <i class="fas fa-check-circle"></i> Active & Authentic
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            {{-- Certificate Document Preview (always visible) --}}
                            <div class="certificate-preview-section">
                                <h5><i class="fas fa-certificate"></i> Official Certificate Document</h5>
                                <div class="certificate-wrapper">
                                    <div class="certificate-card" id="certificateCard">
                                        <!-- Ornate Border Layer -->
                                        <div class="border-layer"></div>
                                        
                                        <!-- Certificate Contents -->
                                        <div class="cert-content-inner">
                                            <!-- Top Metadata -->
                                            <div class="cert-meta-row">
                                                <span class="meta-left">IWORK4SINDH/IT/CERT/2026</span>
                                                <span class="meta-right">No. {{ $certificate->certificate_number }}</span>
                                            </div>

                                            <!-- Header Authority Section -->
                                            <div class="cert-authority-header">
                                                <img src="{{ asset('Certificate-Generator/assets/Logo.png') }}" alt="iWork4Sindh Logo" class="cert-agency-logo" />
                                                <div class="cert-vertical-divider"></div>
                                                <div class="cert-agency-text">
                                                    <h2 class="agency-title">Government Job Portal</h2>
                                                    <div class="agency-subtitle">GOVERNMENT OF SINDH</div>
                                                </div>
                                            </div>

                                            <div class="cert-divider-line"></div>

                                            <!-- Main Grant Title -->
                                            <div class="cert-title-section">
                                                <span class="grant-title">GRANT OF CERTIFICATE TO</span>
                                            </div>

                                            <!-- Recipient Name -->
                                            <div class="cert-recipient-name">
                                                <span id="nameDisplay">{{ $certificate->first_name }} {{ $certificate->last_name }}</span>
                                            </div>
                                            
                                            <div class="cert-subtitle-section">
                                                <span class="sub-licence">UNDER THE SINDH DIGITAL INITIATIVE SCHEME, 2026</span>
                                            </div>

                                            <!-- Legalistic/Formal Body Text -->
                                            <div class="cert-body-paragraphs">
                                                <p class="body-intro">
                                                    Whereas it has been proved to the satisfaction of the iWork4Sindh Governing Council that the candidate named above has successfully completed all necessary training, assignments, and examinations for the professional certification course:
                                                </p>
                                                
                                                <div class="course-focus-box">
                                                    <span class="course-title" id="courseDisplay">{{ $certificate->course_name }}</span>
                                                    <span class="course-duration-lbl">with a total duration of</span>
                                                    <span class="course-duration" id="durationDisplay">{{ $certificate->duration }}</span>
                                                </div>

                                                <p class="body-declaration">
                                                    "To empower the youth of Sindh with market-oriented technical capabilities, fostering sustainable digital livelihoods, entrepreneurship, and public service excellence."
                                                </p>

                                                <p class="body-conclusion">
                                                    Now, therefore, in recognition of the achievements and competency demonstrated, the Governing Body is pleased to award this Certificate of Completion.
                                                </p>
                                                
                                                <p class="cert-date-line">Given under my hand at Karachi this <span class="cert-date" id="dateDisplay">
                                                    @php
                                                        $day = $certificate->certificate_date->format('j');
                                                        $suffix = 'th';
                                                        if (!in_array(($day % 100), [11, 12, 13])) {
                                                            switch ($day % 10) {
                                                                case 1:  $suffix = 'st'; break;
                                                                case 2:  $suffix = 'nd'; break;
                                                                case 3:  $suffix = 'rd'; break;
                                                            }
                                                        }
                                                    @endphp
                                                    {{ $day }}{{ $suffix }} day of {{ $certificate->certificate_date->format('F, Y') }}
                                                </span>.</p>
                                            </div>

                                            <!-- Footer: Seal, Signature, QR Code -->
                                            <div class="cert-footer-row">
                                                <!-- Left: Golden Foil Seal -->
                                                <div class="foil-seal-wrapper">
                                                    <div class="golden-seal">
                                                        <svg viewBox="0 0 100 100" class="seal-svg">
                                                            <defs>
                                                                <path id="textPathTop" d="M 15.5 50 A 34.5 34.5 0 0 1 84.5 50" fill="none" />
                                                                <path id="textPathBottom" d="M 84.5 50 A 34.5 34.5 0 0 1 15.5 50" fill="none" />
                                                            </defs>
                                                            <circle cx="50" cy="50" r="41" stroke="#8a5600" stroke-width="1.2" fill="none" />
                                                            <circle cx="50" cy="50" r="38" stroke="#8a5600" stroke-width="0.8" stroke-dasharray="2 1.5" fill="none" />
                                                            <circle cx="50" cy="50" r="28" stroke="#8a5600" stroke-width="1" fill="none" />
                                                            <text font-family="'Cinzel', serif" font-size="6.2" font-weight="900" fill="#301500" letter-spacing="0.2">
                                                                <textPath href="#textPathTop" startOffset="50%" text-anchor="middle">SINDH INFORMATION</textPath>
                                                            </text>
                                                            <text font-family="'Cinzel', serif" font-size="6.2" font-weight="900" fill="#301500" letter-spacing="0.2">
                                                                <textPath href="#textPathBottom" startOffset="50%" text-anchor="middle">DEPARTMENT</textPath>
                                                            </text>
                                                            <text x="50" y="46" font-family="'Cinzel', serif" font-size="5" font-weight="bold" fill="#301500" text-anchor="middle">★ ★ ★</text>
                                                            <text x="50" y="54" font-family="'Outfit', sans-serif" font-size="5" font-weight="900" fill="#4a2700" text-anchor="middle" letter-spacing="0.5">IWORK4SINDH</text>
                                                            <text x="50" y="62" font-family="'Cinzel', serif" font-size="5" font-weight="bold" fill="#301500" text-anchor="middle">★ ★ ★</text>
                                                        </svg>
                                                    </div>
                                                </div>
                                                
                                                <!-- Center: Page Number -->
                                                <!-- <div class="footer-center-meta">
                                                    <span>Page 01 of 01</span>
                                                </div> -->

                                                <!-- Right: Signature and QR Verification -->
                                                <div class="verification-wrapper">
                                                    <div class="signature-section">
                                                        <div class="">
                                                        <div class="signature-line-box">
                                                            <span class="signature-img-placeholder">Moaawiz Malik</span>
                                                        </div>
                                                            <span class="sig-title">Program Director</span>
                                                            <span class="sig-dept">iWork4Sindh</span>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="qr-code-section">
                                                        <!-- Dynamic QR Code Container -->
                                                        <div id="verify-page-qrcode"></div>
                                                        <span class="qr-verify-label">Scan to Verify</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    {{-- Manual verify form JS (works on error page) --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('manualVerifyForm');
            if (form) {
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    const certNo = document.getElementById('certNumberInput').value.trim();
                    if (certNo) {
                        window.location.href = "{{ url('/certificate/verify') }}/" + encodeURIComponent(certNo);
                    }
                });
            }
        });
    </script>

    @if(isset($certificate) && $certificate)
        <!-- Include QR Code CDN library -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const qrcodeContainer = document.getElementById("verify-page-qrcode");
                if (qrcodeContainer) {
                    qrcodeContainer.innerHTML = '';
                    const verifyUrl = "{{ $certificate->verify_url }}";

                    new QRCode(qrcodeContainer, {
                        text: verifyUrl,
                        width: 128,
                        height: 128,
                        colorDark: "#0d47a1",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.L
                    });

                    // Ensure single visible QR code across all mobile & desktop browsers
                    const checkQr = () => {
                        const img = qrcodeContainer.querySelector('img');
                        const canvas = qrcodeContainer.querySelector('canvas');
                        if (img && img.getAttribute('src') && img.getAttribute('src').startsWith('data:image')) {
                            img.style.setProperty('display', 'block', 'important');
                            if (canvas) canvas.style.setProperty('display', 'none', 'important');
                        } else if (canvas) {
                            canvas.style.setProperty('display', 'block', 'important');
                            if (img) img.style.setProperty('display', 'none', 'important');
                        }
                    };

                    checkQr();
                    setTimeout(checkQr, 100);
                    setTimeout(checkQr, 500);
                }
            });
        </script>
    @endif
@endsection
