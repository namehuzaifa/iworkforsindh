@extends('backend.layouts.app')

@section('style')
    <link
        href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Pinyon+Script&family=Alex+Brush&family=Cinzel:wght@500;700;800&family=Lora:ital,wght@0,400;0,600;1,400&family=Outfit:wght@300;400;600&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('Certificate-Generator/extra.css') }}" />

    <style>
        .show-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.5rem 0;
        }

        .actions-bar {
            width: 100%;
            max-width: 650px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            background: #fff;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }

        .certificate-wrapper {
            width: 100%;
            max-width: 650px;
            container-type: inline-size;
            container-name: certificate;
        }

        .signature-img-placeholder {
            font-family: 'Great Vibes', 'Pinyon Script', 'Alex Brush', cursive !important;
            font-size: 21px !important;
        }

        .qr-code-section #qrcode img {
            width: 9cqw;
            height: 9cqw;
        }

        .custom-btn {
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 600;
            padding: 8px 18px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
            height: 40px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .custom-btn-secondary {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .custom-btn-secondary:hover {
            background-color: #e2e8f0;
            color: #0f172a;
            text-decoration: none;
        }

        .custom-btn-primary {
            background-color: #065cc4;
            color: #ffffff;
        }

        .custom-btn-primary:hover {
            background-color: #054fa8;
            color: #ffffff;
            text-decoration: none;
        }

        .custom-btn-success {
            background-color: #10b981;
            color: #ffffff;
        }

        .custom-btn-success:hover {
            background-color: #059669;
            color: #ffffff;
            text-decoration: none;
        }

        /* Print rules to hide admin panel structure and headers */
        @media print {
            @page {
                size: A4 portrait;
                margin: 0 !important;
            }

            body,
            html {
                background: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 210mm !important;
                height: 297mm !important;
                overflow: hidden !important;
            }

            body #sidebar,
            body #nav,
            body .main-sidebar,
            body .main-header,
            body .content-header,
            body footer.main-footer,
            body .actions-bar,
            body .pwa-install-btn,
            body .help-icon,
            body #installApp {
                display: none !important;
                visibility: hidden !important;
                opacity: 0 !important;
                height: 0 !important;
                width: 0 !important;
                overflow: hidden !important;
                padding: 0 !important;
                margin: 0 !important;
                border: none !important;
            }

            .wrapper,
            .content-wrapper,
            .content,
            .container-fluid,
            .show-container,
            .certificate-wrapper {
                margin: 0 !important;
                padding: 0 !important;
                width: 210mm !important;
                height: 297mm !important;
                max-width: 100% !important;
                max-height: 100% !important;
                position: absolute !important;
                top: 0 !important;
                left: 0 !important;
                border: none !important;
                box-shadow: none !important;
                background: transparent !important;
            }

            .certificate-card {
                box-shadow: none !important;
                width: 210mm !important;
                height: 297mm !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                position: absolute !important;
                top: 0 !important;
                left: 0 !important;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid show-container">
        <!-- Actions bar -->
        <div class="actions-bar">
            <a href="{{ route('admin.certificates.index') }}" class="custom-btn custom-btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            <div style="display: flex; gap: 10px; align-items: center;">
                <a href="{{ route('admin.certificates.print', $certificate) }}" target="_blank"
                    class="custom-btn custom-btn-primary">
                    <i class="fas fa-print"></i> Print / Save PDF
                </a>
                @if($certificate->status == 'issued')
                    <form action="{{ route('admin.certificates.send', $certificate) }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="custom-btn custom-btn-success">
                            <i class="fas fa-paper-plane"></i> Mark Sent
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Certificate Container -->
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
                        <img src="{{ asset('Certificate-Generator/assets/Logo.png') }}" alt="iWork4Sindh Logo"
                            class="cert-agency-logo" />
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
                            Whereas it has been proved to the satisfaction of the iWork4Sindh Governing Council that the
                            candidate named above has successfully completed all necessary training, assignments, and
                            examinations for the professional certification course:
                        </p>

                        <div class="course-focus-box">
                            <span class="course-title" id="courseDisplay">{{ $certificate->course_name }}</span>
                            <span class="course-duration-lbl">with a total duration of</span>
                            <span class="course-duration" id="durationDisplay">{{ $certificate->duration }}</span>
                        </div>

                        <p class="body-declaration">
                            "To empower the youth of Sindh with market-oriented technical capabilities, fostering
                            sustainable digital livelihoods, entrepreneurship, and public service excellence."
                        </p>

                        <p class="body-conclusion">
                            Now, therefore, in recognition of the achievements and competency demonstrated, the Governing
                            Body is pleased to award this Certificate of Completion.
                        </p>

                        <p class="cert-date-line">Given under my hand at Karachi this <span class="cert-date"
                                id="dateDisplay">
                                {{-- Format to formal text format --}}
                                @php
                                    $day = $certificate->certificate_date->format('j');
                                    $suffix = 'th';
                                    if (!in_array(($day % 100), [11, 12, 13])) {
                                        switch ($day % 10) {
                                            case 1:
                                                $suffix = 'st';
                                                break;
                                            case 2:
                                                $suffix = 'nd';
                                                break;
                                            case 3:
                                                $suffix = 'rd';
                                                break;
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
                                    <circle cx="50" cy="50" r="38" stroke="#8a5600" stroke-width="0.8"
                                        stroke-dasharray="2 1.5" fill="none" />
                                    <circle cx="50" cy="50" r="28" stroke="#8a5600" stroke-width="1" fill="none" />
                                    <text font-family="'Cinzel', serif" font-size="6.2" font-weight="900" fill="#301500"
                                        letter-spacing="0.2">
                                        <textPath href="#textPathTop" startOffset="50%" text-anchor="middle">SINDH
                                            INFORMATION</textPath>
                                    </text>
                                    <text font-family="'Cinzel', serif" font-size="6.2" font-weight="900" fill="#301500"
                                        letter-spacing="0.2">
                                        <textPath href="#textPathBottom" startOffset="50%" text-anchor="middle">DEPARTMENT
                                        </textPath>
                                    </text>
                                    <text x="50" y="46" font-family="'Cinzel', serif" font-size="5" font-weight="bold"
                                        fill="#301500" text-anchor="middle">★ ★ ★</text>
                                    <text x="50" y="54" font-family="'Outfit', sans-serif" font-size="5" font-weight="900"
                                        fill="#4a2700" text-anchor="middle" letter-spacing="0.5">IWORK4SINDH</text>
                                    <text x="50" y="62" font-family="'Cinzel', serif" font-size="5" font-weight="bold"
                                        fill="#301500" text-anchor="middle">★ ★ ★</text>
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
                                <div id="qrcode"></div>
                                <span class="qr-verify-label">Scan to Verify</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- Include QR Code CDN library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const printBtn = document.getElementById('printBtn');
            const qrcodeContainer = document.getElementById("qrcode");

            // Verification URL
            const verifyUrl = "{{ $certificate->verify_url }}";

            // Generate the QR code using the verifyUrl
            new QRCode(qrcodeContainer, {
                text: verifyUrl,
                width: 120,
                height: 120,
                colorDark: "#0d47a1",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.L
            });

            // Print button behavior
            printBtn.addEventListener('click', () => {
                window.print();
            });
        });
    </script>
@endsection