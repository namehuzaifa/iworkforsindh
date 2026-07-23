@extends('backend.layouts.app')

@section('style')
    <link
        href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Pinyon+Script&family=Alex+Brush&family=Cinzel:wght@500;700;800&family=Lora:ital,wght@0,400;0,600;1,400&family=Outfit:wght@300;400;600&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('Certificate-Generator/extra.css') }}" />

    <style>
        .container-custom {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            padding: 1.5rem 0;
        }

        .form-section-custom {
            flex: 1 1 350px;
            max-width: 450px;
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            border: 2px solid #065cc4;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .form-section-custom h3 {
            margin-bottom: 1.5rem;
            color: #065cc4;
            font-weight: 600;
        }

        .preview-section-custom {
            flex: 2 1 500px;
            max-width: 700px;
            display: flex;
            justify-content: center;
            align-items: center;
            container-type: inline-size;
            container-name: certificate;
            width: 100%;
        }

        .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1px solid #ced4da !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }

        /* Adjusting preview signature positioning to ensure handwriting font styles show */
        .signature-img-placeholder {
            font-family: 'Great Vibes', 'Pinyon Script', 'Alex Brush', cursive !important;
            font-size: 21px !important;
        }

        .qr-code-section #preview-qrcode img {
            width: 9cqw;
            height: 9cqw;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Generate Certificate</h2>
            <a href="{{ route('admin.certificates.index') }}" class="btn btn-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="container-custom">
            <!-- Left side: Input form -->
            <section class="form-section-custom">
                <h3>Certificate Details</h3>
                <form id="certForm" action="{{ route('admin.certificates.store') }}" method="POST" autocomplete="off">
                    @csrf

                    <input type="hidden" name="user_id" id="user_id" value="">

                    <div class="form-group mb-3">
                        <label for="candidateEmail" class="form-label font-weight-bold">Candidate Email <small
                                class="text-muted">(Optional)</small></label>
                        <div class="input-group">
                            <input type="email" id="candidateEmail" class="form-control"
                                placeholder="Enter email to auto-fill name..." />
                            <div class="input-group-append">
                                <button type="button" id="lookupEmailBtn" class="btn btn-outline-primary">
                                    <i class="fas fa-search"></i> Find
                                </button>
                            </div>
                        </div>
                        <div id="emailFeedback" class="mt-1" style="font-size: 0.85rem;"></div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="firstName" class="form-label font-weight-bold">First Name</label>
                        <input type="text" name="first_name" id="firstName" class="form-control" placeholder="First Name"
                            required />
                    </div>

                    <div class="form-group mb-3">
                        <label for="lastName" class="form-label font-weight-bold">Last Name</label>
                        <input type="text" name="last_name" id="lastName" class="form-control" placeholder="Last Name"
                            required />
                    </div>

                    <div class="form-group mb-3">
                        <label for="course_select" class="form-label font-weight-bold">Select Existing Course
                            (Optional)</label>
                        <select id="course_select" class="form-control select2">
                            <option value="">-- Or type manually below --</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->title }}">
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="courseName" class="form-label font-weight-bold">Course Name</label>
                        <input type="text" name="course_name" id="courseName" class="form-control"
                            placeholder="e.g., Web Development Basics" required />
                    </div>

                    <div class="form-group mb-3">
                        <label for="duration" class="form-label font-weight-bold">Duration</label>
                        <input type="text" name="duration" id="duration" class="form-control" placeholder="e.g., 8 Weeks"
                            required />
                    </div>

                    <div class="form-group mb-3">
                        <label for="certDate" class="form-label font-weight-bold">Certificate Date</label>
                        <input type="date" name="certificate_date" id="certDate" class="form-control" required />
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-3 py-2 font-weight-bold">
                        <i class="fas fa-save"></i> Save & Generate Certificate
                    </button>
                </form>
            </section>

            <!-- Right side: Live certificate preview -->
            <section class="preview-section-custom">
                <div class="certificate-card" id="certificateCard">
                    <!-- Ornate Border Layer -->
                    <div class="border-layer"></div>

                    <!-- Certificate Contents -->
                    <div class="cert-content-inner">
                        <!-- Top Metadata -->
                        <div class="cert-meta-row">
                            <span class="meta-left">IWORK4SINDH/IT/CERT/2026</span>
                            <span class="meta-right" id="certNoDisplay">No. Pending</span>
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
                            <span id="nameDisplay">[First Name] [Last Name]</span>
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
                                <span class="course-title" id="courseDisplay">[Course Name]</span>
                                <span class="course-duration-lbl">with a total duration of</span>
                                <span class="course-duration" id="durationDisplay">[Duration]</span>
                            </div>

                            <p class="body-declaration">
                                "To empower the youth of Sindh with market-oriented technical capabilities, fostering
                                sustainable digital livelihoods, entrepreneurship, and public service excellence."
                            </p>

                            <p class="body-conclusion">
                                Now, therefore, in recognition of the achievements and competency demonstrated, the
                                Governing Body is pleased to award this Certificate of Completion.
                            </p>

                            <p class="cert-date-line">Given under my hand at Karachi this <span class="cert-date"
                                    id="dateDisplay">[Current Date]</span>.</p>
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
                                            <textPath href="#textPathBottom" startOffset="50%" text-anchor="middle">
                                                DEPARTMENT</textPath>
                                        </text>
                                        <text x="50" y="46" font-family="'Cinzel', serif" font-size="5" font-weight="bold"
                                            fill="#301500" text-anchor="middle">★ ★ ★</text>
                                        <text x="50" y="54" font-family="'Outfit', sans-serif" font-size="5"
                                            font-weight="900" fill="#4a2700" text-anchor="middle"
                                            letter-spacing="0.5">IWORK4SINDH</text>
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
                                    <div id="preview-qrcode"></div>
                                    <span class="qr-verify-label">Scan to Verify</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@section('script')
    <!-- Include QR Code CDN library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const userIdInput = document.getElementById('user_id');
            const emailInput = document.getElementById('candidateEmail');
            const lookupBtn = document.getElementById('lookupEmailBtn');
            const emailFeedback = document.getElementById('emailFeedback');
            const firstNameInput = document.getElementById('firstName');
            const lastNameInput = document.getElementById('lastName');
            const courseSelect = document.getElementById('course_select');
            const courseInput = document.getElementById('courseName');
            const durationInput = document.getElementById('duration');
            const certDateInput = document.getElementById('certDate');

            const nameDisplay = document.getElementById('nameDisplay');
            const courseDisplay = document.getElementById('courseDisplay');
            const durationDisplay = document.getElementById('durationDisplay');
            const dateDisplay = document.getElementById('dateDisplay');

            // Initialize QR Code inside preview
            const qrcodeContainer = document.getElementById("preview-qrcode");
            let qrCodeObj = null;

            function updateQrCode(text) {
                if (!qrCodeObj) {
                    qrCodeObj = new QRCode(qrcodeContainer, {
                        text: text,
                        width: 120,
                        height: 120,
                        colorDark: "#0d47a1",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.L
                    });
                } else {
                    qrCodeObj.clear();
                    qrCodeObj.makeCode(text);
                }
            }

            // Default placeholder QR pointing to verification path pattern
            updateQrCode("{{ url('/certificate/verify/placeholder-uuid') }}");

            // Helpers
            const getOrdinalSuffix = (day) => {
                if (day > 3 && day < 21) return 'th';
                switch (day % 10) {
                    case 1: return 'st';
                    case 2: return 'nd';
                    case 3: return 'rd';
                    default: return 'th';
                }
            };

            const formatFormalDate = (dateObj) => {
                const d = dateObj.getDate();
                const m = dateObj.toLocaleDateString('en-GB', { month: 'long' });
                const y = dateObj.getFullYear();
                return `${d}${getOrdinalSuffix(d)} day of ${m}, ${y}`;
            };

            const parseLocalDate = (str) => {
                const [y, m, d] = str.split('-').map(Number);
                return new Date(y, m - 1, d);
            };

            // Set default date to today
            const today = new Date();
            const pad = (n) => String(n).padStart(2, '0');
            const todayStr = `${today.getFullYear()}-${pad(today.getMonth() + 1)}-${pad(today.getDate())}`;

            certDateInput.value = todayStr;
            dateDisplay.textContent = formatFormalDate(today);

            // Live preview updates
            const updatePreview = () => {
                const fn = firstNameInput.value.trim() || '[First Name]';
                const ln = lastNameInput.value.trim() || '[Last Name]';
                const cn = courseInput.value.trim() || '[Course Name]';
                const dr = durationInput.value.trim() || '[Duration]';

                nameDisplay.textContent = `${fn} ${ln}`;
                courseDisplay.textContent = cn;
                durationDisplay.textContent = dr;
            };

            // Email lookup AJAX
            lookupBtn.addEventListener('click', () => {
                const email = emailInput.value.trim();
                if (!email) {
                    emailFeedback.innerHTML = '<span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Please enter an email first.</span>';
                    return;
                }

                lookupBtn.disabled = true;
                lookupBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
                emailFeedback.innerHTML = '';

                $.ajax({
                    url: "{{ route('admin.certificates.lookup-email') }}",
                    type: 'POST',
                    data: { email: email, _token: "{{ csrf_token() }}" },
                    success: function (res) {
                        if (res.found) {
                            userIdInput.value = res.user_id;
                            firstNameInput.value = res.first_name;
                            lastNameInput.value = res.last_name;
                            updatePreview();
                            emailFeedback.innerHTML = '<span class="text-success"><i class="fas fa-check-circle"></i> Candidate found! Name auto-filled.</span>';
                        } else {
                            userIdInput.value = '';
                            emailFeedback.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle"></i> No candidate found with this email. You can enter name manually.</span>';
                        }
                    },
                    error: function () {
                        emailFeedback.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle"></i> Error looking up email. Please try again.</span>';
                    },
                    complete: function () {
                        lookupBtn.disabled = false;
                        lookupBtn.innerHTML = '<i class="fas fa-search"></i> Find';
                    }
                });
            });

            // Allow Enter key in email field to trigger lookup
            emailInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    lookupBtn.click();
                }
            });

            // Autofill course name on selection
            $(courseSelect).on('change', function () {
                if (this.value) {
                    courseInput.value = this.value;
                    updatePreview();
                }
            });

            // Date picker change listener
            certDateInput.addEventListener('change', () => {
                if (certDateInput.value) {
                    dateDisplay.textContent = formatFormalDate(parseLocalDate(certDateInput.value));
                }
            });

            // Attach input listeners
            [firstNameInput, lastNameInput, courseInput, durationInput].forEach(inp => {
                inp.addEventListener('input', updatePreview);
            });

            // Initialize Select2 if function exists (for course select only)
            if (typeof $.fn.select2 !== 'undefined') {
                $('.select2').select2({
                    theme: 'bootstrap4',
                    width: '100%'
                });

                $('#course_select').on('select2:select', function (e) {
                    courseInput.value = this.value;
                    updatePreview();
                });
            }
        });
    </script>
@endsection