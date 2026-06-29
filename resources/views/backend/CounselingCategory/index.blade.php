@extends('backend.layouts.app')
@section('title')
    {{ __('Counseling Categories') }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title line-height-36">{{ __('Counseling Categories') }}</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap table-bordered">
                        <thead>
                            <tr>
                                <th>{{ __('name') }}</th>
                                <th>{{ __('Slug') }}</th>
                                <th width="10%">{{ __('action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->slug }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center align-items-center">
                                            <a href="{{ route('counseling-category.edit', $category->id) }}" class="btn btn-info btn-sm mr-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('counseling-category.destroy', $category->id) }}"
                                                method="POST" class="d-inline">
                                                @method('DELETE')
                                                @csrf
                                                <button onclick="return confirm('{{ __('are_you_sure_you_want_to_delete_this_item') }}');"
                                                    class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">{{ __('no_data_found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($categories->hasPages())
                    <div class="card-footer d-flex justify-content-center">
                        {{ $categories->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div class="col-md-4">
            @if (empty($counselingCategory))
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title line-height-36">{{ __('Create Category') }}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('counseling-category.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="name">{{ __('name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                    id="name" placeholder="{{ __('name') }}" value="{{ old('name') }}" required>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                        </form>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title line-height-36">{{ __('Edit Category') }}</h3>
                            <a href="{{ route('counseling-category.index') }}" class="btn btn-outline-dark btn-sm"><i
                                    class="fas fa-plus mr-1"></i> {{ __('Create') }}</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('counseling-category.update', $counselingCategory->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="name">{{ __('name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                    id="name" placeholder="{{ __('name') }}" value="{{ $counselingCategory->name }}"
                                    required>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
