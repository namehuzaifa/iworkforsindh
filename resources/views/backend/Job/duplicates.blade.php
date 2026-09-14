@extends('backend.layouts.app')

@section('title')
    {{ __('Duplicate Jobs') }}
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title line-height-36">{{ __('Duplicate Jobs') }}</h3>

            <div class="card-tools">
                <form action="{{ route('duplicate-job.toggle') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="enabled" value="{{ $checkEnabled ? 0 : 1 }}">
                    <button type="submit" class="btn btn-sm {{ $checkEnabled ? 'btn-success' : 'btn-outline-secondary' }}">
                        <i class="fas fa-shield-alt"></i>
                        {{ $checkEnabled ? __('Pre-publish check: ON') : __('Pre-publish check: OFF') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="card-body">
            <div class="alert alert-light border">
                <i class="fas fa-info-circle text-primary"></i>
                {{ __('Two jobs count as duplicates when the same company account posts the same title with the same description. Re-posting the same role months later is not a duplicate.') }}
                <br>
                <strong>{{ __('Expire') }}</strong>
                {{ __('hides a duplicate from the site but keeps every application, bookmark and message. Delete is permanent and cannot be undone.') }}
            </div>

            @if ($pendingScan > 0)
                <div class="alert alert-warning d-flex justify-content-between align-items-center">
                    <span>
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ __(':count older job(s) have not been scanned yet. Existing duplicates stay hidden until they are.', ['count' => number_format($pendingScan)]) }}
                    </span>
                    <form action="{{ route('duplicate-job.scan') }}" method="POST" class="mb-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-warning">
                            <i class="fas fa-search"></i> {{ __('Scan next 2,000') }}
                        </button>
                    </form>
                </div>
            @endif

            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link {{ $tab === 'held' ? 'active' : '' }}"
                        href="{{ route('duplicate-job.index', ['tab' => 'held']) }}">
                        {{ __('Held for review') }}
                        <span class="badge badge-danger">{{ $heldCount }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $tab === 'groups' ? 'active' : '' }}"
                        href="{{ route('duplicate-job.index', ['tab' => 'groups']) }}">
                        {{ __('Existing duplicates') }}
                    </a>
                </li>
            </ul>

            {{-- ============ Held for review ============ --}}
            @if ($tab === 'held')
                <form method="GET" class="form-inline mb-3">
                    <input type="hidden" name="tab" value="held">
                    <label class="mr-2">{{ __('Job title') }}</label>
                    <input type="text" name="title" value="{{ request('title') }}"
                        class="form-control form-control-sm mr-3" placeholder="{{ __('Search title') }}">
                    <button class="btn btn-sm btn-primary">{{ __('Filter') }}</button>

                    @if (request('title'))
                        <a href="{{ route('duplicate-job.index', ['tab' => 'held']) }}"
                            class="btn btn-sm btn-outline-secondary ml-2">{{ __('Reset') }}</a>
                    @endif
                </form>

                @if ($held->isEmpty())
                    <div class="text-center py-5 text-muted">
                        @if (request('title'))
                            <i class="fas fa-search fa-2x mb-2 d-block"></i>
                            {{ __('No held job matches that title.') }}
                        @else
                            <i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>
                            {{ __('Nothing is waiting. New postings that match an existing job will appear here.') }}
                        @endif
                    </div>
                @else
                    <form action="{{ route('duplicate-job.resolve') }}" method="POST" class="js-resolve-form">
                        @csrf
                        <input type="hidden" name="action" value="expire">

                        <div class="mb-2">
                            <button type="submit" class="btn btn-sm btn-primary" data-action="publish">
                                <i class="fas fa-check"></i> {{ __('Publish selected') }}
                            </button>
                            <button type="submit" class="btn btn-sm btn-warning" data-action="expire">
                                <i class="fas fa-archive"></i> {{ __('Expire selected') }}
                            </button>
                            @if (userCan('job.delete'))
                                <button type="submit" class="btn btn-sm btn-danger" data-action="delete">
                                    <i class="fas fa-trash"></i> {{ __('Delete selected') }}
                                </button>
                            @endif
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="30"><input type="checkbox" class="js-check-all"></th>
                                        <th>{{ __('Job') }}</th>
                                        <th>{{ __('Company') }}</th>
                                        <th>{{ __('Posted By') }}</th>
                                        <th>{{ __('Copy of') }}</th>
                                        <th>{{ __('Applications') }}</th>
                                        <th>{{ __('Posted') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($held as $job)
                                        <tr>
                                            <td><input type="checkbox" name="ids[]" value="{{ $job->id }}"
                                                    class="js-row-check"></td>
                                            <td>
                                                <a href="{{ route('website.job.details', $job->slug) }}" target="_blank">
                                                    {{ $job->title }}
                                                </a>
                                                <small class="d-block text-muted">#{{ $job->id }}</small>
                                            </td>
                                            <td>{{ $job->company?->user?->name ?? '-' }}</td>
                                            <td>{{ $job->postingLog?->teamMember?->name ?? '-' }}</td>
                                            <td>
                                                @if ($job->duplicateOf)
                                                    <a href="{{ route('website.job.details', $job->duplicateOf->slug) }}"
                                                        target="_blank">#{{ $job->duplicateOf->id }}</a>
                                                    <small
                                                        class="d-block text-muted">{{ $job->duplicateOf->created_at?->format('d M Y') }}</small>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if ($job->applications_count > 0)
                                                    <span
                                                        class="badge badge-danger">{{ $job->applications_count }}</span>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>
                                            <td>{{ $job->created_at?->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{ $held->links() }}
                    </form>
                @endif
            @endif

            {{-- ============ Existing duplicates ============ --}}
            @if ($tab === 'groups')
                <form method="GET" class="form-inline mb-3">
                    <input type="hidden" name="tab" value="groups">

                    <label class="mr-2">{{ __('Job title') }}</label>
                    <input type="text" name="title" value="{{ request('title') }}"
                        class="form-control form-control-sm mr-3" placeholder="{{ __('Search title') }}">

                    <label class="mr-2">{{ __('Company') }}</label>
                    <select name="company_id" class="form-control form-control-sm mr-3">
                        <option value="">{{ __('All') }}</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}"
                                {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->user?->name }}
                            </option>
                        @endforeach
                    </select>

                    <button class="btn btn-sm btn-primary">{{ __('Filter') }}</button>

                    @if (request('title') || request('company_id'))
                        <a href="{{ route('duplicate-job.index', ['tab' => 'groups']) }}"
                            class="btn btn-sm btn-outline-secondary ml-2">{{ __('Reset') }}</a>
                    @endif
                </form>

                @if ($hashes->isEmpty())
                    <div class="text-center py-5 text-muted">
                        @if (request('title') || request('company_id'))
                            <i class="fas fa-search fa-2x mb-2 d-block"></i>
                            {{ __('No duplicates match this filter.') }}
                        @else
                            <i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>
                            {{ __('No duplicate groups found.') }}
                        @endif
                    </div>
                @else
                    @foreach ($hashes as $hash)
                        @php
                            $jobs = $groupedJobs->get($hash->duplicate_hash);
                        @endphp

                        @continue(! $jobs || $jobs->count() < 2)

                        @php
                            $keeper = $duplicateService->suggestedKeeper($jobs);
                            $totalApps = $jobs->sum('applications_count');
                        @endphp

                        <form action="{{ route('duplicate-job.resolve') }}" method="POST"
                            class="card card-outline card-secondary js-resolve-form">
                            @csrf
                            <input type="hidden" name="action" value="expire">

                            <div class="card-header">
                                <h3 class="card-title">
                                    {{ $jobs->first()->title }}
                                    <span class="badge badge-secondary">{{ $jobs->count() }} {{ __('copies') }}</span>
                                    @if ($totalApps > 0)
                                        <span class="badge badge-danger">{{ $totalApps }}
                                            {{ __('applications') }}</span>
                                    @endif
                                </h3>
                                <small class="d-block text-muted">{{ $jobs->first()->company?->user?->name }}</small>
                            </div>

                            <div class="card-body table-responsive p-0">
                                <table class="table table-sm table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th width="60">{{ __('Keep') }}</th>
                                            <th width="40"></th>
                                            <th>{{ __('Job') }}</th>
                                            <th>{{ __('Posted By') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Applications') }}</th>
                                            <th>{{ __('Bookmarks') }}</th>
                                            <th>{{ __('Posted') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($jobs as $job)
                                            @php $isKeeper = $keeper && $keeper->id === $job->id; @endphp
                                            <tr class="{{ $isKeeper ? 'table-success' : '' }}">
                                                <td>
                                                    <input type="radio" name="keep_id" value="{{ $job->id }}"
                                                        class="js-keep" {{ $isKeeper ? 'checked' : '' }}>
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="ids[]" value="{{ $job->id }}"
                                                        class="js-row-check" {{ $isKeeper ? '' : 'checked' }}
                                                        {{ $isKeeper ? 'disabled' : '' }}>
                                                </td>
                                                <td>
                                                    <a href="{{ route('website.job.details', $job->slug) }}"
                                                        target="_blank">#{{ $job->id }}</a>
                                                    @if ($isKeeper)
                                                        <span class="badge badge-success">{{ __('suggested') }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $job->postingLog?->teamMember?->name ?? '-' }}</td>
                                                <td>{{ ucfirst($job->status) }}</td>
                                                <td>
                                                    @if ($job->applications_count > 0)
                                                        <span
                                                            class="badge badge-danger">{{ $job->applications_count }}</span>
                                                    @else
                                                        <span class="text-muted">0</span>
                                                    @endif
                                                </td>
                                                <td>{{ $job->bookmarks_count }}</td>
                                                <td>{{ $job->created_at?->format('d M Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-sm btn-warning" data-action="expire">
                                    <i class="fas fa-archive"></i> {{ __('Expire the others') }}
                                </button>

                                @if (userCan('job.delete'))
                                    <button type="submit" class="btn btn-sm btn-outline-danger" data-action="delete">
                                        <i class="fas fa-trash"></i> {{ __('Delete the others') }}
                                    </button>

                                    @if ($totalApps > 0)
                                        <label class="ml-3 mb-0 text-danger small">
                                            <input type="checkbox" name="force" value="1">
                                            {{ __('Also delete copies that candidates applied to') }}
                                        </label>
                                    @endif
                                @endif
                            </div>
                        </form>
                    @endforeach

                    {{ $hashes->links() }}
                @endif
            @endif
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            // Keep the header checkbox and the rows in step.
            $('.js-check-all').on('change', function() {
                $(this).closest('form').find('.js-row-check:not(:disabled)').prop('checked', this.checked);
            });

            // The kept job can never also be selected for removal, so moving
            // the radio releases the old row and locks the new one.
            $('.js-keep').on('change', function() {
                var form = $(this).closest('form');
                var keptId = $(this).val();

                form.find('.js-row-check').each(function() {
                    var isKept = $(this).val() === keptId;
                    $(this).prop('disabled', isKept).prop('checked', !isKept);
                });
            });

            // Every button shares one form, so it stamps its own action on the
            // way out, and delete asks first.
            $('.js-resolve-form button[type=submit]').on('click', function(e) {
                var form = $(this).closest('form');
                var action = $(this).data('action');
                var count = form.find('.js-row-check:checked:not(:disabled)').length;

                if (count === 0) {
                    e.preventDefault();
                    alert('{{ __('Select at least one job first.') }}');
                    return;
                }

                if (action === 'delete') {
                    var forced = form.find('input[name=force]').is(':checked');
                    var message = '{{ __('Permanently delete') }} ' + count + ' {{ __('job(s)? This cannot be undone.') }}';

                    if (forced) {
                        message += '\n\n{{ __('WARNING: candidate applications on those jobs will be deleted too.') }}';
                    }

                    if (!confirm(message)) {
                        e.preventDefault();
                        return;
                    }
                }

                form.find('input[name=action]').val(action);
            });
        });
    </script>
@endsection
