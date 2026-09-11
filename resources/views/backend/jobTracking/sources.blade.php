@extends('backend.layouts.app')
@section('title')
    {{ __('Job Sources') }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title line-height-36">{{ __('Job Sources') }}</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>{{ __('name') }}</th>
                                <th width="12%" class="text-center">{{ __('Jobs') }}</th>
                                <th width="14%" class="text-center">{{ __('status') }}</th>
                                <th width="22%" class="text-center">{{ __('action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sources as $source)
                                <tr>
                                    <td>
                                        <form action="{{ route('job-source.update', $source->id) }}" method="POST"
                                            class="d-flex align-items-center">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="name" value="{{ $source->name }}"
                                                class="form-control form-control-sm mr-1" required>
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                <i class="fas fa-save"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-center">{{ $source->posting_logs_count }}</td>
                                    <td class="text-center">
                                        @if ($source->is_active)
                                            <span class="badge badge-success">{{ __('Active') }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('job-source.toggle', $source->id) }}"
                                            class="btn btn-sm {{ $source->is_active ? 'btn-warning' : 'btn-success' }}">
                                            {{ $source->is_active ? __('Deactivate') : __('Activate') }}
                                        </a>
                                        @if (! $source->posting_logs_count)
                                            <form action="{{ route('job-source.destroy', $source->id) }}" method="POST"
                                                class="d-inline">
                                                @method('DELETE')
                                                @csrf
                                                <button
                                                    onclick="return confirm('{{ __('are_you_sure_you_want_to_delete_this_item') }}');"
                                                    class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center p-4">{{ __('no_data_found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $sources->links() }}
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title line-height-36">{{ __('Add Job Source') }}</h3>
                </div>
                <form action="{{ route('job-source.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">{{ __('name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                placeholder="{{ __('e.g. LinkedIn') }}" required>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <p class="text-muted mb-0">
                            {{ __('A source that is already used by posted jobs cannot be deleted — deactivate it instead so old reports keep reading correctly.') }}
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
