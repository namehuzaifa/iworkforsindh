@extends('backend.layouts.app')

@section('content')
    <!-- DataTable CSS CDN -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css">

    <!-- DataTable Buttons CSS CDN -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.2/css/buttons.dataTables.css">
    
    <div class="container">

        <h3>Candidates from: {{ $region }}</h3>

        <table id="regionTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Region</th>
                    <th>District</th>
                    <th>Gender</th>
                    <th>Cv</th>
                    <th>Marital Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($candidates as $candidate)
                    <tr>
                        <td>{{ $candidate->id }}</td>
                        <td>{{ $candidate->first_name ?? 'Not Defined' }}</td>
                        <td>{{ $candidate->region ?? 'Not Defined' }}</td>
                        <td>{{ $candidate->district ?? 'Not Defined' }}</td>
                        <td>{{ $candidate->gender ?? 'Not Defined' }}</td>
                        <td>{{ $candidate->cv ?? 'Not Defined' }}</td>
                        <td>{{ $candidate->marital_status ?? 'Not Defined' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No candidates found in this region.</td>
                    </tr>
                @endforelse
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