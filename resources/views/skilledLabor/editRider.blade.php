@extends('frontend.layouts.app')
@section('css')
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
<style>
     .form-container {
        max-width: 1000px;
        margin: 30px auto;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }
    .form-header {
        text-align: center;
        margin-bottom: 30px;
        color: #2c3e50;
    }
    .required-field::after {
        content: " *";
        color: red;
    }
   
</style>

@endsection
@section('main')
   

<div class="container mt-5 mb-5">
    <div class="form-container bg-white">
        <div class="form-header">
            <h2>Edit Rider</h2>
            <p class="text-muted">Please fill all required fields</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('rider.update', $rider->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="text-white" >Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label required-field">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $rider->name) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label required-field">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $rider->email) }}" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cnic" class="form-label required-field">CNIC</label>
                            <input type="text" class="form-control" id="cnic" name="nic" value="{{ old('cnic', $rider->nic) }}" placeholder="XXXXX-XXXXXXX-X" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label required-field">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone', $rider->phone) }}" required>
                        </div>
                    </div>
                    
                </div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="{{ route('skilled-labour.index') }}" class="btn btn-secondary me-md-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('frontend_scripts')
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
<script>

    // CNIC format validation
    document.getElementById('cnic').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 5) {
            value = value.substring(0, 5) + '-' + value.substring(5);
        }
        if (value.length > 13) {
            value = value.substring(0, 13) + '-' + value.substring(13);
        }
        e.target.value = value.substring(0, 15);
    });

    // Phone number validation
    document.getElementById('phone').addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/\D/g, '').substring(0, 11);
    });

    // Form submission handling
    document.getElementById('skilledLabourForm').addEventListener('submit', function(e) {
        // You can add additional validation here if needed
        console.log('Form submitted');
    });
</script>
@endpush