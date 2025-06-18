@extends('backend.layouts.app')
@section('content')
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
                    <span class="detail-value">{{ $labor->phone }}</span></span>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            
            <div class="mb-4">
                <h5>Personal Details</h5>
                {{-- <div class="row"> --}}
                    <div class="detail-row">
                        <span class="detail-label">CNIC:</span>
                        <span class="detail-value">{{ $labor->cnic }}</span></span>
                        
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Gender:</span>
                        <span class="detail-value">{{ ucfirst($labor->gender) }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Birth Date:</span>
                        <span class="detail-value">{{ \Carbon\Carbon::parse($labor->birth_date)->format('d M Y') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Marital Status:</span>
                        <span class="detail-value">{{ $labor->marital_status ?? 'Not specified' }}</span>
                    </div>
                    
                {{-- </div> --}}
            </div>
            
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
                    <span class="detail-label">Experience:</span>
                    <span class="detail-value">{{ $labor->experience ?? 'Not specified' }}</span>
                </div>
            </div>
            
            <div class="mb-4">
                <h5>Description</h5>
                <p>{{ $labor->description }}</p>
            </div>
            
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
                        <div class="card-header">Fingerprint Image</div>
                        <div class="card-body p-2 text-center">
                            <img src="{{ asset($labor->fingerprint_image) }}" class="img-fluid" style="max-height: 150px;">
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
</div>
@endsection