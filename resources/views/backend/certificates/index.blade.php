@extends('backend.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Certificates</h2>
            <a href="{{ route('admin.certificates.create') }}" class="btn btn-primary btn-sm rounded-pill px-3">
                <i class="fas fa-plus"></i> Generate Certificate
            </a>
        </div>

        <div class="card card-default">
            <div class="card-body">
                <form action="{{ route('admin.certificates.index') }}" method="GET" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <input type="text" name="keyword" class="form-control" placeholder="Search by number, name or course..." value="{{ request('keyword') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-control select2">
                            <option value="">All Statuses</option>
                            <option value="issued" {{ request('status') == 'issued' ? 'selected' : '' }}>Issued</option>
                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent to User</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.certificates.index') }}" class="btn btn-secondary w-100">Clear</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Certificate No.</th>
                                <th>Candidate Name</th>
                                <th>Course Name</th>
                                <th>Duration</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($certificates as $certificate)
                                <tr>
                                    <td><strong>{{ $certificate->certificate_number }}</strong></td>
                                    <td>{{ $certificate->first_name }} {{ $certificate->last_name }}</td>
                                    <td>{{ $certificate->course_name }}</td>
                                    <td>{{ $certificate->duration }}</td>
                                    <td>{{ $certificate->certificate_date ? $certificate->certificate_date->format('d M Y') : '-' }}</td>
                                    <td>
                                        @if($certificate->status == 'sent')
                                            <span class="badge badge-success">Sent</span>
                                        @else
                                            <span class="badge badge-warning">Issued</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.certificates.show', $certificate) }}" class="btn btn-info btn-sm mr-1">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="{{ route('admin.certificates.print', $certificate) }}" target="_blank" class="btn btn-primary btn-sm mr-1">
                                                <i class="fas fa-print"></i> Print PDF
                                            </a>
                                            @if($certificate->status == 'issued')
                                                <form action="{{ route('admin.certificates.send', $certificate) }}" method="POST" class="inline-block mr-1">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Mark this certificate as Sent?');">
                                                        <i class="fas fa-paper-plane"></i> Send
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('admin.certificates.destroy', $certificate) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this certificate?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No certificates found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $certificates->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
