@extends('frontend.layouts.app')

@section('title', 'Certificate Verification - ' . config('app.name'))

@section('css')
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Pinyon+Script&family=Alex+Brush&family=Cinzel:wght@500;700;800&family=Lora:ital,wght@0,400;0,600;1,400&family=Outfit:wght@300;400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('Certificate-Generator/extra.css') }}" />
    
    <style>
        .verification-portal-container {
            padding: 3rem 0;
            background-color: #f1f5f9;
            min-height: 70vh;
        }

        /* ── Success Card ── */
        .portal-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            border: none;
            overflow: hidden;
            margin-bottom: 2rem;
        }
        .portal-header {
            padding: 2rem 2rem 1.5rem;
            text-align: center;
            color: #fff;
        }
        .portal-header.success-header {
            background: linear-gradient(135deg, #065cc4 0%, #002d62 100%);
        }
        .portal-header i {
            font-size: 3rem;
            margin-bottom: 0.75rem;
            display: block;
        }
        .portal-header h3 {
            font-weight: 700;
            margin: 0 0 0.25rem;
            font-size: 1.6rem;
        }
        .portal-header p {
            margin: 0;
            font-size: 0.95rem;
            opacity: 0.85;
        }

        .verification-details-table {
            margin: 0;
        }
        .verification-details-table th {
            width: 35%;
            color: #475569;
            font-weight: 600;
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.85rem 1.5rem;
            font-size: 0.9rem;
        }
        .verification-details-table td {
            color: #0f172a;
            font-weight: 500;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.85rem 1.5rem;
            font-size: 0.9rem;
        }

        .certificate-preview-section {
            padding: 2rem;
            background: #f8fafc;
            border-top: 1px dashed #cbd5e1;
        }
        .certificate-preview-section h5 {
            text-align: center;
            font-weight: 700;
            color: #065cc4;
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
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
            font-size: 21px !important;
        }
        .qr-code-section #verify-page-qrcode img {
            width: 9cqw;
            height: 9cqw;
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
    </style>
@endsection

@section('main')
    <div class="verification-portal-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
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
                                    <div class="error-actions">
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
                                <i class="fas fa-check-circle"></i>
                                <h3>Certificate Verified Successfully</h3>
                                <p>This certificate is authentic and issued by iWork4Sindh</p>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table verification-details-table">
                                        <tbody>
                                            <tr>
                                                <th>Certificate Number</th>
                                                <td><span class="text-primary font-weight-bold">{{ $certificate->certificate_number }}</span></td>
                                            </tr>
                                            <tr>
                                                <th>Recipient Name</th>
                                                <td>{{ $certificate->first_name }} {{ $certificate->last_name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Course Name</th>
                                                <td>{{ $certificate->course_name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Duration</th>
                                                <td>{{ $certificate->duration }}</td>
                                            </tr>
                                            <tr>
                                                <th>Issue Date</th>
                                                <td>{{ $certificate->certificate_date ? $certificate->certificate_date->format('d F, Y') : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Verification Status</th>
                                                <td>
                                                    <span class="badge bg-success text-white py-1 px-3 rounded-pill">
                                                        <i class="fas fa-check"></i> Active & Authentic
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
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
                                                        <div class="signature-line-box">
                                                            <span class="signature-img-placeholder">Moaawiz Malik</span>
                                                            <span class="sig-title">Program Director</span>
                                                            <span class="sig-dept">Information Technology Division</span>
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
    @if(isset($certificate) && $certificate)
        <!-- Include QR Code CDN library -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const qrcodeContainer = document.getElementById("verify-page-qrcode");
                if (qrcodeContainer) {
                    const verifyUrl = "{{ $certificate->verify_url }}";

                    new QRCode(qrcodeContainer, {
                        text: verifyUrl,
                        width: 120,
                        height: 120,
                        colorDark: "#0d47a1",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.L
                    });
                }
            });
        </script>
    @endif
@endsection
