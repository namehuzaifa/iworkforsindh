@extends('backend.layouts.app')
@section('title')
    {{ __('Team Members') }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title line-height-36">{{ __('Team Members') }}</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>{{ __('name') }}</th>
                                <th>{{ __('email') }}</th>
                                <th width="10%" class="text-center">{{ __('Jobs') }}</th>
                                <th width="12%" class="text-center">{{ __('status') }}</th>
                                <th width="20%" class="text-center">{{ __('action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($members as $member)
                                <form action="{{ route('team-member.update', $member->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <tr>
                                        <td>
                                            <input type="text" name="name" value="{{ $member->name }}"
                                                class="form-control form-control-sm" required>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <input type="email" name="email" value="{{ $member->email }}"
                                                    class="form-control form-control-sm mr-1">
                                                <button type="submit" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ $member->posting_logs_count }}</td>
                                        <td class="text-center">
                                            @if ($member->is_active)
                                                <span class="badge badge-success">{{ __('Active') }}</span>
                                            @else
                                                <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('team-member.toggle', $member->id) }}"
                                                class="btn btn-sm {{ $member->is_active ? 'btn-warning' : 'btn-success' }}">
                                                {{ $member->is_active ? __('Deactivate') : __('Activate') }}
                                            </a>
                                        </td>
                                    </tr>
                                </form>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center p-4">{{ __('no_data_found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $members->links() }}
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title line-height-36">{{ __('Add Team Member') }}</h3>
                </div>
                <form action="{{ route('team-member.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="member_name">{{ __('name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="member_name"
                                class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                required>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="member_email">{{ __('email') }}</label>
                            <input type="email" name="email" id="member_email"
                                class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <p class="text-muted mb-0">
                            {{ __('Someone who has already posted jobs cannot be deleted — deactivate them instead so their past postings stay attributed.') }}
                        </p>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">{{ __('save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
