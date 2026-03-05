@extends('layouts.app')

@section('title', 'Ranks')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h3 mb-0">Ranks</h1>

        <a href="{{ route('ranks.create') }}" class="btn btn-primary">
            New check
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if (session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    <div class="card app-card">
        <div class="card-header">Ranks</div>

        <div class="card-body p-0">
            @if (empty($ranks) || $ranks->count() === 0)
                <div class="p-3">
                    <div class="text-muted">
                        No ranks yet. Click <strong>New check</strong> to add the first one.
                    </div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="thead-light">
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Domain</th>
                            <th>Keyword</th>
                            <th>Location</th>
                            <th>Language</th>
                            <th style="width: 140px;">Status</th>
                            <th style="width: 220px;">Result</th>
                            <th style="width: 260px;" class="text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($ranks as $rank)
                            <tr>
                                <td>{{ $rank->id }}</td>
                                <td>{{ $rank->domain->domain }}</td>
                                <td class="font-weight-bold">{{ $rank->keyword }}</td>
                                <td>{{ $rank->location->location_name }}</td>
                                <td>{{ $rank->language->language_name }}</td>
                                <td>
                                <span class="badge badge-{{ $rank->status === 'done' ? 'success' : ($rank->status === 'failed' ? 'danger' : 'secondary') }}">
                                    {{ $rank->status }}
                                </span>
                                </td>
                                <td>
                                    @if ($rank->status === 'done' && $rank->rank_min !== null)
                                        <div class="font-weight-bold">
                                            Best: {{ $rank->rank_min }}
                                        </div>
                                        <div class="text-muted small">
                                            avg: {{ $rank->rank_avg ?? '—' }},
                                            matches: {{ $rank->matches_count ?? 0 }}
                                        </div>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('ranks.show', $rank) }}" class="btn btn-sm btn-outline-secondary">
                                        Details
                                    </a>

                                    @if ($rank->task_id)
                                        <form action="{{ route('ranks.fetch-results', $rank) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-primary">
                                                Get results
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('ranks.destroy', $rank) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Delete this record?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            @if ($rank->error_message)
                                <tr>
                                    <td></td>
                                    <td colspan="7" class="text-muted small">
                                        {{ $rank->error_message }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @if ($ranks->hasPages())
            <div class="card-footer d-flex justify-content-center">
                {{ $ranks->links() }}
            </div>
        @endif
    </div>
@endsection