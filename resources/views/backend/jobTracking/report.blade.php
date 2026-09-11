@extends('backend.layouts.app')
@section('title')
    {{ __('Job Posting Report') }}
@endsection
@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title line-height-36">{{ __('Job Posting Report') }}</h3>
            <div class="card-tools">
                <a href="{{ route('job-posting-report.export', request()->query()) }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel"></i> {{ __('Export to Excel') }}
                </a>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('job-posting-report.index') }}" method="GET" class="row">
                <div class="col-md-2 mb-2">
                    <label>{{ __('Posted By') }}</label>
                    <select name="team_member_id" class="form-control">
                        <option value="">{{ __('All') }}</option>
                        @foreach ($members as $member)
                            <option value="{{ $member->id }}"
                                {{ request('team_member_id') == $member->id ? 'selected' : '' }}>{{ $member->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label>{{ __('Source') }}</label>
                    <select name="job_source_id" class="form-control">
                        <option value="">{{ __('All') }}</option>
                        @foreach ($sources as $source)
                            <option value="{{ $source->id }}"
                                {{ request('job_source_id') == $source->id ? 'selected' : '' }}>{{ $source->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label>{{ __('Company Account') }}</label>
                    <select name="company_id" class="form-control">
                        <option value="">{{ __('All') }}</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}"
                                {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->user?->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label>{{ __('From') }}</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                </div>
                <div class="col-md-2 mb-2">
                    <label>{{ __('To') }}</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                </div>
                <div class="col-md-1 mb-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-block">{{ __('filter') }}</button>
                </div>
            </form>

            @if ($summary->count())
                <div class="row mt-3">
                    @foreach ($summary as $row)
                        <div class="col-md-2 col-sm-4 mb-2">
                            <div class="info-box mb-0">
                                <div class="info-box-content">
                                    <span class="info-box-text">{{ $row->teamMember?->name ?? __('Not recorded') }}</span>
                                    <span class="info-box-number">{{ $row->total }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th>{{ __('date') }}</th>
                        <th>{{ __('Job Title') }}</th>
                        <th>{{ __('Company Account') }}</th>
                        <th>{{ __('Posted By') }}</th>
                        <th>{{ __('Source') }}</th>
                        <th>{{ __('IP') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ $log->created_at?->format('d M Y, h:i A') }}</td>
                            <td>
                                @if ($log->job)
                                    <a href="{{ route('website.job.details', $log->job->slug) }}" target="_blank">
                                        {{ $log->job->title }}
                                    </a>
                                @else
                                    <span class="text-muted">{{ __('Job deleted') }}</span>
                                @endif
                            </td>
                            <td>{{ $log->company?->user?->name }}</td>
                            <td>{{ $log->teamMember?->name ?? '-' }}</td>
                            <td>
                                {{ $log->source?->name ?? '-' }}
                                @if ($log->source_note)
                                    <br><small class="text-muted">{{ $log->source_note }}</small>
                                @endif
                            </td>
                            <td><small>{{ $log->ip_address }}</small></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center p-4">{{ __('no_data_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $logs->links() }}
        </div>
    </div>
@endsection
