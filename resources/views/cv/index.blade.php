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
    {{-- <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Skilled Labors</h1>
    </div> --}}

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="cvmaker_div">
        <iframe width="100%" height="3300px" src="https://iwork4sindh.com/public/CVBulider/index.html"></iframe>
    </div>
</div>

@endsection

@push('frontend_scripts')
<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

@endpush