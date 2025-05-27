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
                            <a href="{{ route('admin-skilled-labour.index', $rider->id) }}">View Skilled Labors</a>
                        </td>                    
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
@section('script')
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
    });
</script>
@endsection