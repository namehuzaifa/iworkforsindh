<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - {{ $certificate->certificate_number }}</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Pinyon+Script&family=Alex+Brush&family=Cinzel:wght@500;700;800&family=Lora:ital,wght@0,400;0,600;1,400&family=Outfit:wght@300;400;600&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('Certificate-Generator/extra.css') }}" />

    <style>
        @page {
            size: A4 portrait;
            margin: 0 !important;
        }

        html,
        body {
            margin: 0 !important;
            padding: 0 !important;
            width: 210mm !important;
            height: 297mm !important;
            background: #ffffff !important;
            font-family: 'Outfit', sans-serif;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .print-page-wrapper {
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            position: relative;
            box-sizing: border-box;
            background: #ffffff;
            container-type: inline-size;
            container-name: certificate;
        }

        .certificate-card {
            width: 210mm !important;
            height: 297mm !important;
            max-width: 210mm !important;
            max-height: 297mm !important;
            aspect-ratio: auto !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            margin: 0 !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
        }

        .signature-img-placeholder {
            font-family: 'Great Vibes', 'Pinyon Script', 'Alex Brush', cursive !important;
            font-size: 21px !important;
        }

        .qr-code-section #print-page-qrcode img {
            width: 9cqw;
            height: 9cqw;
        }

        /* Top control bar (hidden when printing) */
        .no-print-bar {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 99999;
            display: flex;
            gap: 12px;
            background: rgba(15, 23, 42, 0.85);
            padding: 10px 16px;
            border-radius: 50px;
            backdrop-filter: blur(8px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        .no-print-bar button,
        .no-print-bar a {
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 600;
            padding: 8px 18px;
            border-radius: 30px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-print-now {
            background: #065cc4;
            color: #ffffff;
        }

        .btn-print-now:hover {
            background: #024393;
        }

        .btn-close-window {
            background: #334155;
            color: #ffffff;
        }

        .btn-close-window:hover {
            background: #475569;
        }

        @media print {
            .no-print-bar {
                display: none !important;
            }

            .print-page-wrapper {
                width: 210mm !important;
                height: 297mm !important;
                position: absolute !important;
                top: 0 !important;
                left: 0 !important;
            }
        }
    </style>
</head>

<body>

    <!-- Top floating Action Bar -->
    <div class="no-print-bar">
        <button type="button" class="btn-print-now" onclick="window.print()">
            🖨️ Print / Save PDF
        </button>
        <a href="{{ route('admin.certificates.show', $certificate) }}" class="btn-close-window">
            ✖ Back to Details
        </a>
    </div>

    <!-- Standalone Certificate Print Page -->
    <div class="print-page-wrapper">
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
                            <div id="print-page-qrcode"></div>
                            <span class="qr-verify-label">Scan to Verify</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include QR Code CDN library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const qrcodeContainer = document.getElementById("print-page-qrcode");
            const verifyUrl = "{{ $certificate->verify_url }}";

            new QRCode(qrcodeContainer, {
                text: verifyUrl,
                width: 120,
                height: 120,
                colorDark: "#0d47a1",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.L
            });

            // Automatically open browser print dialog after short delay to let QR load
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>

</html>