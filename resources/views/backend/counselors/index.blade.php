@extends('backend.layouts.app')

@section('title')
    Counselors
@endsection

@section('breadcrumbs')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Counselors</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('dashboard') }}</a></li>
                <li class="breadcrumb-item active">Counselors</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                {{-- Search & Filter Bar --}}
                <div class="card-header">
                    <form action="{{ route('admin.counselors.index') }}" method="GET" class="d-flex flex-wrap gap-2 align-items-center">
                        <div class="input-group" style="max-width: 300px;">
                            <input type="text" name="keyword" value="{{ request('keyword') }}"
                                class="form-control" placeholder="Search by name or email...">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <select name="status" class="form-control" style="max-width: 160px;" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>

                        <select name="sort_by" class="form-control" style="max-width: 160px;" onchange="this.form.submit()">
                            <option value="latest" {{ request('sort_by') == 'latest' ? 'selected' : '' }}>Latest First</option>
                            <option value="oldest" {{ request('sort_by') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        </select>

                        @if(request()->anyFilled(['keyword', 'status', 'sort_by']))
                            <a href="{{ route('admin.counselors.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        @endif
                    </form>
                </div>

                {{-- Table --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-vcenter mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Registered</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($counselors->count() > 0)
                                    @foreach ($counselors as $counselor)
                                        <tr>
                                            <td>{{ $loop->iteration + ($counselors->currentPage() - 1) * $counselors->perPage() }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $counselor->image_url }}"
                                                        alt="{{ $counselor->name }}"
                                                        class="rounded-circle mr-2"
                                                        style="width:36px; height:36px; object-fit:cover;">
                                                    <span class="font-weight-medium">{{ $counselor->name }}</span>
                                                </div>
                                            </td>
                                            <td class="text-muted">{{ $counselor->email }}</td>
                                            <td class="text-muted">
                                                {{ $counselor->phone ?? ($counselor->contactInfo ? $counselor->contactInfo->phone : '-') }}
                                            </td>
                                            <td class="text-muted">
                                                {{ $counselor->created_at->diffForHumans() }}
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center align-items-center">
                                                    @if ($counselor->status)
                                                        <span class="badge badge-success mr-2">Active</span>
                                                    @else
                                                        <span class="badge badge-danger mr-2">Inactive</span>
                                                    @endif
                                                    {{-- Toggle Switch --}}
                                                    <div class="custom-control custom-switch">
                                                        <input
                                                            type="checkbox"
                                                            class="custom-control-input counselor-status-toggle"
                                                            id="counselorStatus{{ $counselor->id }}"
                                                            data-id="{{ $counselor->id }}"
                                                            data-url="{{ route('counselor.status.change') }}"
                                                            {{ $counselor->status ? 'checked' : '' }}>
                                                        <label class="custom-control-label"
                                                            for="counselorStatus{{ $counselor->id }}"></label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="empty py-4">
                                                <x-not-found message="No counselors found" />
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination --}}
                @if ($counselors->hasPages())
                    <div class="card-footer clearfix">
                        {{ $counselors->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    $(document).ready(function () {

        // Status toggle handler
        $(document).on('change', '.counselor-status-toggle', function () {
            const checkbox = $(this);
            const userId = checkbox.data('id');
            const url = checkbox.data('url');
            const newStatus = checkbox.is(':checked') ? 1 : 0;
            const row = checkbox.closest('tr');

            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    id: userId,
                    status: newStatus,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    // Update badge
                    const badge = row.find('.badge');
                    if (newStatus === 1) {
                        badge.removeClass('badge-danger').addClass('badge-success').text('Active');
                    } else {
                        badge.removeClass('badge-success').addClass('badge-danger').text('Inactive');
                    }
                    toastr.success(response.message ?? 'Status updated successfully');
                },
                error: function () {
                    // Revert toggle on error
                    checkbox.prop('checked', !checkbox.is(':checked'));
                    toastr.error('Failed to update status. Please try again.');
                }
            });
        });

    });
</script>
@endsection

@section('style')
<style>
    .table th {
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
    }
    .font-weight-medium {
        font-weight: 500;
    }
    .badge {
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 20px;
    }
</style>
@endsection
