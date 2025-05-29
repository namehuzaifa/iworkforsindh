@extends('backend.layouts.app')

@section('content')
    <!-- DataTable CSS CDN -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css">

    <!-- DataTable Buttons CSS CDN -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.2/css/buttons.dataTables.css">
    
    <div class="container">
        <h2 class="mb-4">Riders</h2>

        <table id="regionTable" class="table table-bordered table-striped">

            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>CNIC</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($riders as $rider)
                    <tr>
                        <td>{{ $rider->name }}</td>
                        <td>{{ $rider->email }}</td>
                        <td>{{ $rider->phone }}</td>
                        <td>{{ $rider->nic }}</td>
                        <td>
                            <a href="javascript:void(0)" class="active-status">
                                <label class="switch ">
                                    <input data-id="{{ $rider->id }}" type="checkbox"
                                        class="success status-switch change-active-status"
                                        {{ $rider->status == 1 ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                                <p class="{{ $rider->status == 1 ? 'active' : '' }}" id="status_{{ $rider->id }}">
                                    {{ $rider->status == 1 ? __('activated') : __('deactivated') }}</p>
                            </a>
                        </td>
                        
                        <td>
                            <a href="{{ route('admin-skilled-labour.index', $rider->id) }}">View Skilled Labors</a>
                        </td>                    
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
@section('style')
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 35px;
            height: 19px;
        }

        /* Hide default HTML checkbox */
        .switch input {
            display: none;
        }

        /* The slider */
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 15px;
            width: 15px;
            left: 3px;
            bottom: 2px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input.success:checked+.slider {
            background-color: #28a745;
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(15px);
            -ms-transform: translateX(15px);
            transform: translateX(15px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>
@endsection
@section('script')
<script src="{{ asset('backend') }}/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>

<!-- DataTable JS CDN -->
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>

<!-- DataTable Buttons JS CDN -->
<script src="https://cdn.datatables.net/buttons/3.2.2/js/dataTables.buttons.js"></script>

<!-- DataTable Buttons Extension for Export -->
<script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.dataTables.js"></script>

<!-- JSZip for Excel export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- PDFMake for PDF export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>

<!-- PDFMake Fonts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<!-- DataTable Buttons HTML5 for Export Options (CSV, Excel, PDF) -->
<script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.html5.min.js"></script>
<script>
    jQuery(document).ready(function() {
        jQuery('#regionTable').DataTable({
            "pageLength" : 25,
            "order": [[0, 'asc']], 
            layout: {
                topStart: {
                    buttons: ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdfHtml5']
                }
            }
        });

        $('.status-switch').on('change', function() {
            var status = $(this).prop('checked') == true ? 1 : 0;
            var id = $(this).data('id');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: '{{ route('riders.status.change') }}',
                data: {
                    'status': status,
                    'id': id
                },
                success: function(response) {
                    toastr.success(response.message, 'Success');
                }
            });

            if (status == 1) {
                $(`#status_${id}`).text("{{ __('activated') }}")
            }else{
                $(`#status_${id}`).text("{{ __('deactivated') }}")
            }
        });

    });
</script>
@endsection