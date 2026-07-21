@extends('frontend.layouts.app')

@section('title', 'Certificate Verification - ' . config('app.name'))

@section('style')
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Pinyon+Script&family=Alex+Brush&family=Cinzel:wght@500;700;800&family=Lora:ital,wght@0,400;0,600;1,400&family=Outfit:wght@300;400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('Certificate-Generator/extra.css') }}" />
    
    <style>
        .verification-portal-container {
            padding: 3rem 0;
            background-color: #f8fafc;
            min-height: 70vh;
        }
        .portal-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: none;
            overflow: hidden;
            margin-bottom: 2rem;
        }
        .portal-header {
            padding: 2rem;
            text-align: center;
            color: #fff;
        }
        .portal-header.success-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        .portal-header.error-header {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        .portal-header i {
            font-size: 3.5rem;
            margin-bottom: 1rem;
        }
        .portal-header h3 {
            font-weight: 700;
            margin: 0;
            font-size: 1.8rem;
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
            padding: 1rem 1.5rem;
        }
        .verification-details-table td {
            color: #0f172a;
            font-weight: 500;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.5rem;
        }
        .certificate-preview-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px dashed #cbd5e1;
        }
        .certificate-wrapper {
            width: 100%;
            max-width: 650px;
            container-type: inline-size;
            container-name: certificate;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
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
    </style>
@endsection

@section('main')
    <div class="verification-portal-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    @if(isset($error) || !$certificate)
                        <!-- Invalid Certificate -->
                        <div class="portal-card">
                            <div class="portal-header error-header">
                                <i class="fas fa-times-circle"></i>
                                <h3>Verification Failed</h3>
                            </div>
                            <div class="card-body text-center p-5">
                                <h4 class="text-danger mb-3 font-weight-bold">Invalid Certificate Link</h4>
                                <p class="text-muted mb-4">
                                    The certificate you are trying to verify does not exist, has been revoked, or the link is invalid. Please check the QR code or contact administration for verification queries.
                                </p>
                                <a href="{{ route('website.home') }}" class="btn btn-primary px-4 py-2 rounded-pill">
                                    Go to Homepage
                                </a>
                            </div>
                        </div>
                    @else
                        <!-- Valid Certificate -->
                        <div class="portal-card">
                            <div class="portal-header success-header">
                                <i class="fas fa-check-circle"></i>
                                <h3>Officially Verified Certificate</h3>
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
                        </div>

                        <!-- Action/Toggle View Original Button -->
                        <div class="text-center mb-4">
                            <button type="button" class="btn btn-primary px-4 py-2 rounded-pill" data-toggle="collapse" data-target="#certPreviewCollapse" aria-expanded="false" aria-controls="certPreviewCollapse">
                                <i class="fas fa-certificate"></i> View Ornate Certificate Document
                            </button>
                        </div>

                        <!-- Certificate View Collapse -->
                        <div class="collapse" id="certPreviewCollapse">
                            <div class="certificate-preview-box">
                                <div class="certificate-wrapper mb-4">
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
                    // Verification URL
                    const verifyUrl = "{{ $certificate->verify_url }}";

                    // Generate the QR code
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
