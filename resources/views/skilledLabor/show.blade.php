<div class="container-fluid labor-details">
    <div class="row">
        <div class="col-md-4">
            <div class="text-center mb-4">
                <img src="{{ asset($labor->image) }}" class="img-fluid rounded" alt="{{ $labor->name }}" style="max-height: 300px;">
                <h3 class="mt-3">{{ $labor->name }}</h3>
                <p class="text-muted">{{ $labor->profession->name }} - {{ $labor->skill->name }}</p>
            </div>
            
            <div class="mb-3">
                <h5>Contact Information</h5>
                {{-- <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value">{{ $labor->email }}</span>
                </div> --}}
                <div class="detail-row">
                    <span class="detail-label">Phone:</span>
                    <span class="detail-value">
                        <!-- Masked phone number -->
                        <span id="maskedPhone">{{ substr($labor->phone, 0, 2) . str_repeat('*', strlen($labor->phone)-4) . substr($labor->phone, -2) }}</span>
                        
                        <!-- Actual phone number (hidden initially) -->
                        <span id="actualPhone" style="display:none">{{ $labor->phone }}</span>
                        
                        <!-- Show button with eye icon -->
                        <button type="button" class="btn btn-sm btn-link p-0 ms-2 showbtn" data-bs-toggle="modal" data-bs-target="#termsModal">
                            show
                        </button>
                    </span>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            @auth
            <div class="mb-4">
                <h5>Personal Details</h5>
                <div class="row">
                    <div class="col-md-6 detail-row">
                        <span class="detail-label">CNIC:</span>
                        <!-- Masked CNIC -->
                        <span id="maskedCnic">{{ substr($labor->cnic, 0, 2) . str_repeat('*', strlen($labor->cnic)-4) . substr($labor->cnic, -2) }}</span>
                        
                        <!-- Actual CNIC (hidden initially) -->
                        <span id="actualCnic" style="display:none">{{ $labor->cnic }}</span>

                        <!-- <span class="detail-value">{{ $labor->cnic }}</span> -->
                    </div>
                    <div class="col-md-6 detail-row">
                        <span class="detail-label">Gender:</span>
                        <span class="detail-value">{{ ucfirst($labor->gender) }}</span>
                    </div>
                    <div class="col-md-6 detail-row">
                        <span class="detail-label">Birth Date:</span>
                        <span class="detail-value">{{ \Carbon\Carbon::parse($labor->birth_date)->format('d M Y') }}</span>
                    </div>
                    <div class="col-md-6 detail-row">
                        <span class="detail-label">Marital Status:</span>
                        <span class="detail-value">{{ $labor->marital_status ?? 'Not specified' }}</span>
                    </div>
                </div>
            </div>
            @endauth
            
            <div class="mb-4">
                <h5>Professional Details</h5>
                <div class="detail-row">
                    <span class="detail-label">Profession:</span>
                    <span class="detail-value">{{ $labor->profession->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Skill:</span>
                    <span class="detail-value">{{ $labor->skill->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Vage Per Day:</span>
                    <span class="detail-value">{{ $labor->vage_per_day ?? 'Not specified' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Work Location:</span>
                    <span class="detail-value">{{ $labor->work_location ?? 'Not specified' }}</span>
                </div>
            </div>
            
            <div class="mb-4">
                <h5>Description</h5>
                <p>{{ $labor->description }}</p>
            </div>
            @auth
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">CNIC Front Image</div>
                        <div class="card-body p-2 text-center">
                            <img src="{{ asset($labor->cnic_front_image) }}" class="img-fluid" style="max-height: 150px;">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">CNIC Back Image</div>
                        <div class="card-body p-2 text-center">
                            <img src="{{ asset($labor->cnic_back_image) }}" class="img-fluid" style="max-height: 150px;">
                        </div>
                    </div>
                </div>
                {{-- <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">Fingerprint Right Hand Image</div>
                        <div class="card-body p-2 text-center">
                            <img src="{{ asset($labor->fingerprint_right_hand_image) }}" class="img-fluid" style="max-height: 150px;">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">Fingerprint Left Hand Image</div>
                        <div class="card-body p-2 text-center">
                            <img src="{{ asset($labor->fingerprint_left_hand_image) }}" class="img-fluid" style="max-height: 150px;">
                        </div>
                    </div>
                </div> --}}
            </div>
            @endauth
        </div>
    </div>
</div>

<!-- Terms and Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>By viewing this contact information, you agree to:</p>
                <ul>
                    <li>IWork4Sindh is a platform for the people of Sindh. We verify worker data, including CNIC images and photographs, before listing them. However, final verification is the responsibility of the user. Please confirm all details independently before hiring anyone. The Sindh Information Department will not be responsible for any fraud, theft, or misconduct.</li>
                    <li>IWork4Sindh is a public service project by the Sindh Information Department. It helps connect skilled workers with the general public across Sindh so essential services can be accessed quickly—often with just one phone call.</li>
                    <li>This service is completely free of cost. IWork4Sindh does not charge any fee from either the laborer or the skilled professional.</li>
                </ul>
                
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="agreeTerms">
                    <label class="form-check-label" for="agreeTerms">
                        I agree to these terms and conditions
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="showContactBtn" disabled>Show Contact</button>
            </div>
        </div>
    </div>
</div>