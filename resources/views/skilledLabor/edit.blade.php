@extends('frontend.layouts.app')
@section('css')
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
<link rel="stylesheet" href="https://iwork4sindh.com/frontend/assets/css/bootstrap-datepicker.min.css">

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
    .image-preview {
        width: 200px;
        height: 140px;
        object-fit: contain;
        border: 1px dashed #ccc;
        margin-top: 5px;
        display: none;
    }

    .document input {
        display: none !important;
    }

    .document label{
        cursor: pointer;
    }
</style>

@endsection
@section('main')
   

<div class="container mt-5 mb-5">
    <div class="form-container bg-white">
        <div class="form-header">
            <h2>Edit Skilled Labour</h2>
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
        
        <form id="skilledLabourForm" action="{{ route('skilled-labour.update', $labor->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Personal Information Section -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="text-white" >Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="name" class="form-label required-field">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $labor->name) }}">
                        </div>
                        {{-- <div class="col-md-6 mb-3">
                            <label for="email" class="form-label required-field">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $labor->email) }}" required>
                        </div> --}}
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cnic" class="form-label required-field">CNIC</label>
                            <input type="text" class="form-control" id="cnic" name="cnic" value="{{ old('cnic', $labor->cnic) }}" placeholder="XXXXX-XXXXXXX-X" >
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label required-field">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone', $labor->phone) }}" >
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="gender" class="form-label required-field">Gender</label>
                            <select class="" id="gender" name="gender">
                                <option value="male" {{ old('gender', $labor->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $labor->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $labor->gender) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="marital_status" class="form-label">Marital Status</label>
                            <select class="" id="marital_status" name="marital_status">
                                <option value="">Select</option>
                                <option value="single" {{ old('marital_status', $labor->marital_status) == 'single' ? 'selected' : '' }}>Single</option>
                                <option value="married" {{ old('marital_status', $labor->marital_status) == 'married' ? 'selected' : '' }}>Married</option>
                                <option value="divorced" {{ old('marital_status', $labor->marital_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                <option value="widowed" {{ old('marital_status', $labor->marital_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="birth_date" class="form-label required-field">Date of Birth</label>
                            <input type="date" class="form-control" id="birth_date" name="birth_date" value="{{ old('birth_date', \Carbon\Carbon::parse($labor->birth_date)->format('Y-m-d')) }}">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Professional Information Section -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="text-white" >Professional Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="profession_id" class="form-label required-field">Profession</label>
                            <select class="" id="profession_id" name="profession_id">
                                <option value="">Select Profession</option>
                                @foreach($professions as $profession)
                                    <option value="{{ $profession->id }}" {{ old('profession_id', $labor->profession_id) == $profession->id ? 'selected' : '' }}>
                                        {{ $profession->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="skill_id" class="form-label required-field">Skill</label>
                            <select class="js-example-basic-single" id="skill_id" name="skill_id">
                                <option value="">Select Skill</option>
                                @foreach($skills as $skill)
                                    <option value="{{ $skill->id }}" {{ old('skill_id', $labor->skill_id) == $skill->id ? 'selected' : '' }}>
                                        {{ $skill->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="vage_per_day" class="form-label required-field">Vage per day</label>
                                <input type="text" class="form-control" id="vage_per_day" name="vage_per_day" value="{{ old('vage_per_day', $labor->vage_per_day) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="work_location" class="form-label required-field">Work location</label>
                                <input type="text" class="form-control" id="work_location" name="work_location" value="{{ old('work_location', $labor->work_location) }}" >
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label required-field">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="5" >{{ old('description', $labor->description) }}</textarea>

                    </div>
                </div>
            </div>
            
            <!-- Document Upload Section -->
            <div class="card mb-4 document">
                <div class="card-header bg-primary text-white">
                    <h5 class="text-white" >Document Upload</h5>
                    <small class="text-white">Leave blank to keep current files</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="image" class="form-label required-field">Profile Photo</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*" >
                            <div class="mt-2">
                                <small>Current:</small>
                                <img src="{{ asset($labor->image) }}" class="img-thumbnail" style="max-height: 100px;">
                            </div>
                            <img id="imagePreview" class="image-preview" alt="Profile Photo Preview">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="cnic_front_image" class="form-label required-field">CNIC Front Image</label>
                            <input type="file" class="form-control" id="cnic_front_image" name="cnic_front_image" accept="image/*" >
                            <div class="mt-2">
                                <small>Current:</small>
                                <img src="{{ asset($labor->cnic_front_image) }}" class="img-thumbnail" style="max-height: 100px;">
                            </div>
                            <img id="cnicFrontPreview" class="image-preview" alt="CNIC Preview">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="cnic_back_image" class="form-label required-field">CNIC Back Image</label>
                            <input type="file" class="form-control" id="cnic_back_image" name="cnic_back_image" accept="image/*" >
                            <div class="mt-2">
                                <small>Current:</small>
                                <img src="{{ asset($labor->cnic_back_image) }}" class="img-thumbnail" style="max-height: 100px;">
                            </div>
                            <img id="cnicBackPreview" class="image-preview" alt="CNIC Preview">
                        </div>
                        {{-- <div class="col-md-4 mb-3">
                            <label for="fingerprint_right_hand_image" class="form-label required-field">Fingerprint Right Hand Image</label>
                            <input type="file" class="form-control" id="fingerprint_right_hand_image" name="fingerprint_right_hand_image" accept="image/*" >
                            <div class="mt-2">
                                <small>Current:</small>
                                <img src="{{ asset($labor->fingerprint_right_hand_image) }}" class="img-thumbnail" style="max-height: 100px;">
                            </div>
                            <img id="fingerprintRightPreview" class="image-preview" alt="Fingerprint Preview">
                        </div> --}}
                        {{-- <div class="col-md-4 mb-3">
                            <label for="fingerprint_left_hand_image" class="form-label required-field">Fingerprint Left Hand Image</label>
                            <input type="file" class="form-control" id="fingerprint_left_hand_image" name="fingerprint_left_hand_image" accept="image/*" >
                            <div class="mt-2">
                                <small>Current:</small>
                                <img src="{{ asset($labor->fingerprint_left_hand_image) }}" class="img-thumbnail" style="max-height: 100px;">
                            </div>
                            <img id="fingerprintLeftPreview" class="image-preview" alt="Fingerprint Preview">
                        </div> --}}
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
    var max_days = '1'

    //init datepicker
    $("#birth_date").attr("autocomplete", "off");
    //init datepicker
    $('#birth_date').off('focus').datepicker({
        format: 'yyyy-mm-dd',
        endDate: `+${max_days}d`,
        isRTL: "",
        language: "en",
    }).on('click',
        function() {
            $(this).datepicker('show');
        }
    );

    $(document).ready(function () {
        $('#skilledLabourForm1').on('submit', function (e) {
            e.preventDefault();

            var isValid = true;

            $(this).find('select, input, textarea').each(function () {
                if ($(this).val() === "") {
                isValid = false;
                const label = $(this).prev('label').text();
                toastr.error('Please enter a value for "' + label + '"', 'Error!');
                return false;
                }
            });

            if (isValid) {
                $('#loader').css('display','flex');
                    this.submit();
            }
        });
    });

    $(document).ready(function() {
        $('.js-example-basic-single').select2();
        $('#profession_id').select2();
    });

    $('.document img').click(function() {
        $(this).parent().find('label').trigger('click');
    });
    // Image preview functionality
    document.getElementById('image').addEventListener('change', function(e) {
        const preview = document.getElementById('imagePreview');
        const file = e.target.files[0];
        if (file) {
            preview.style.display = 'block';
            preview.src = URL.createObjectURL(file);
        } else {
            preview.style.display = 'none';
        }
    });

    document.getElementById('cnic_front_image').addEventListener('change', function(e) {
        const preview = document.getElementById('cnicFrontPreview');
        const file = e.target.files[0];
        if (file) {
            preview.style.display = 'block';
            preview.src = URL.createObjectURL(file);
        } else {
            preview.style.display = 'none';
        }
    });

    document.getElementById('cnic_back_image').addEventListener('change', function(e) {
        const preview = document.getElementById('cnicBackPreview');
        const file = e.target.files[0];
        if (file) {
            preview.style.display = 'block';
            preview.src = URL.createObjectURL(file);
        } else {
            preview.style.display = 'none';
        }
    });

    // document.getElementById('fingerprint_right_hand_image').addEventListener('change', function(e) {
    //     const preview = document.getElementById('fingerprintRightPreview');
    //     const file = e.target.files[0];
    //     if (file) {
    //         preview.style.display = 'block';
    //         preview.src = URL.createObjectURL(file);
    //     } else {
    //         preview.style.display = 'none';
    //     }
    // });

    // document.getElementById('fingerprint_left_hand_image').addEventListener('change', function(e) {
    //     const preview = document.getElementById('fingerprintLeftPreview');
    //     const file = e.target.files[0];
    //     if (file) {
    //         preview.style.display = 'block';
    //         preview.src = URL.createObjectURL(file);
    //     } else {
    //         preview.style.display = 'none';
    //     }
    // });

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