@extends('frontend.layouts.app')

@section('main')

<style>
    img.card-img-top.labor-image {
        width: 200px;
        height: 150px;
        object-fit: contain;
    }

    a.btn.btn-sm.btn-primary.edit-btn {
        padding: 5px 10px;
    }

    button.btn.btn-sm.btn-danger.delete-btn {
        padding: 5px 11px;
    }
    /* .select2-container .select2-selection--single {
        height: 38px !important;
        padding: 5px 10px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
    } */
</style>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Skilled Helpers</h1>
        @auth
        @if(Auth::user()->role === 'rider')
            <a href="{{ route('skilled-labour.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Labor
            </a>
        @endif
        @endauth
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" class="mb-4 row g-3">
        <div class="col-md-3">
            <select name="profession_id" class="form-control select2 profession">
                <option value="">-- Select Profession --</option>
                @foreach($professions as $profession)
                    <option value="{{ $profession->id }}" {{ request('profession_id') == $profession->id ? 'selected' : '' }}>
                        {{ $profession->name }}
                    </option>
                @endforeach
            </select>
        </div>
    
        <div class="col-md-3">
            <select name="skill_id" class="form-control select2 skill">
                <option value="">-- Select Skill --</option>
                @foreach($skills as $skill)
                    <option value="{{ $skill->id }}" {{ request('skill_id') == $skill->id ? 'selected' : '' }}>
                        {{ $skill->name }}
                    </option>
                @endforeach
            </select>
        </div>
    
        <div class="col-md-3">
            <input type="text" name="location" class="form-control" placeholder="Enter Location" value="{{ request('location') }}">
        </div>
    
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-50">Filter</button>
            <a href="{{ route('skilled-labour.index') }}" class="btn btn-secondary w-50">Clear</a>
        </div>
    </form>
    
    
    

    <div class="row">
        @foreach($labors as $labor)
        <div class="col-md-3 mb-3">
            <!-- Edit/Delete Icons -->
            @if ($labor->user_id == auth()->id())
                <div class="card-actions">
                    <a href="{{ route('skilled-labour.edit', $labor->id) }}" class="btn btn-sm btn-primary edit-btn">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('skilled-labour.destroy', $labor->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger delete-btn" onclick="return confirm('Are you sure you want to delete this labor?')">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
            @endif
            <div class="card labor-card" data-labor-id="{{ $labor->id }}" style="cursor: pointer;">
                <div class="card-img-top-container text-center">
                    <img src="{{ asset($labor->image) }}" class="card-img-top labor-image" alt="{{ $labor->name }}">
                </div>
                <div class="card-body">
                    <p> <strong>Status:</strong> {{ $labor->status == 1 ? 'Approved' : 'Pending' }}</p>
                    <h5 class="card-title">{{ $labor->name }}</h5>
                    <p class="card-text mb-1">
                        <strong>Profession:</strong> {{ $labor->profession->name }}
                    </p>
                    <p class="card-text mb-1">
                        <strong>Skill:</strong> {{ $labor->skill->name }}
                    </p>
                    @auth
                        <p class="card-text mb-1">
                            <strong>Status:</strong> {{ $labor->status ? "Active" : "Pending" }}
                        </p>
                    @endauth
                  
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="pagination" style="justify-content: center;">
        {{-- {{ $labors->links() }} --}}
        {{ $labors->appends(request()->query())->links() }}

    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="laborModal" tabindex="-1" aria-labelledby="laborModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="laborModalLabel">Labor Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="laborModalBody">
                <!-- Content will be loaded here via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .labor-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
    }
    
    .labor-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .card-img-top-container {
        height: 200px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
    }
    
    .labor-image {
        height: 100%;
        width: auto;
        object-fit: cover;
    }
    
    .detail-row {
        margin-bottom: 10px;
    }
    
    .detail-label {
        font-weight: bold;
        color: #495057;
    }
    
    .detail-value {
        color: #212529;
    }
</style>
@endpush

@push('frontend_scripts')
<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script>
    $(document).ready(function() {
        $('.labor-card').click(function() {
            const laborId = $(this).data('labor-id');
            
            $.ajax({
                url: `/skilled-helper/${laborId}`,
                method: 'GET',
                success: function(response) {
                    $('#laborModalBody').html(response);
                    $('#laborModal').modal('show');
                },
                error: function(xhr) {
                    alert('Error loading labor details');
                }
            });
        });

        $('.select2.profession').select2({
            placeholder: 'Select Profession',
            allowClear: true,
            width: '100%' // important for responsiveness
        });

        $('.select2.skill').select2({
            placeholder: 'Select Skill',
            allowClear: true,
            width: '100%' // important for responsiveness
        });

        // Delete confirmation
        function confirmDelete(e) {
            if(!confirm('Are you sure you want to delete this labor?')) {
                e.preventDefault();
            }
        }
        
        // Attach event listeners to all delete buttons
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', confirmDelete);
        });

        $(document).on('click', '.showbtn', function(e) {
            $('.labor-details').hide();
        });  

        // Enable/disable show button based on checkbox
        $(document).on('change', '#agreeTerms', function() {
            $('#showContactBtn').prop('disabled', !this.checked);
        });
        
        // Show contact info when button is clicked
        $(document).on('click', '#showContactBtn', function() {
            // Unmask phone number
            $('#maskedPhone').hide();
            $('#actualPhone').show();

            // Unmask CNIC
            $('#maskedCnic').hide();
            $('#actualCnic').show();
            
            // Close modal
            $('#termsModal').modal('hide');
            $('.showbtn').hide();
            $('.labor-details').show();
        });

        jQuery('#agreeTerms').change(function() {
            alert();
            if (jQuery(this).is(':checked')) {
                jQuery('#showContactBtn').removeAttr('disabled');
            } else {
                jQuery('#showContactBtn').attr('disabled', 'disabled');
            }
        });

    });
</script>
@endpush