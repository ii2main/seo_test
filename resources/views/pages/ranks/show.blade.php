@extends('layouts.app')

@section('title', 'Rank details')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h1 class="h3 mb-1">Rank #{{ $rank->id }}</h1>
            <div class="text-muted">
                <strong>{{ $rank->domain->domain }}</strong> — "{{ $rank->keyword }}"
            </div>
        </div>

        <div class="d-flex" style="gap: .5rem;">
            <a href="{{ route('ranks.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>

    <div class="card app-card mb-3">
        <div class="card-body">
            <div><strong>Status:</strong> {{ $rank->status }}</div>
            <div><strong>Location:</strong> {{ $rank->location->location_name }}</div>
            <div><strong>Language:</strong> {{ $rank->language->language_name }}</div>
            <div><strong>Items:</strong> {{ $rank->items_count ?? 0 }}</div>
            <div>
                <strong>Min/Avg/Max:</strong>
                @if ($rank->rank_avg !== null)
                    {{ $rank->rank_min }} / {{ $rank->rank_avg }} / {{ $rank->rank_max }}
                @else
                    —
                @endif
            </div>
            @if ($rank->error_message)
                <div class="text-danger mt-2">{{ $rank->error_message }}</div>
            @endif
        </div>
    </div>

    <div class="d-flex align-items-center justify-content-between mb-2">
        <div class="text-muted small">
            Filter by type:
        </div>

        <div class="d-flex" style="gap: .5rem; flex-wrap: wrap;">
            <a class="btn btn-sm {{ empty($type) ? 'btn-primary' : 'btn-outline-primary' }}"
               href="{{ route('ranks.show', $rank) }}">
                All
            </a>

            @foreach ($types as $t)
                <a class="btn btn-sm {{ $type === $t ? 'btn-primary' : 'btn-outline-primary' }}"
                   href="{{ route('ranks.show', $rank) }}?type={{ urlencode($t) }}">
                    {{ $t }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="card app-card">
        <div class="card-header">Items</div>

        <div class="card-body p-0">
            @if ($details->count() === 0)
                <div class="p-3 text-muted">No items.</div>
            @else
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="thead-light">
                        <tr>
                            <th style="width: 130px;">Type</th>
                            <th style="width: 110px;">Abs rank</th>
                            <th>Title</th>
                            <th style="width: 240px;">Domain</th>
                            <th style="width: 260px;">URL</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($details as $d)
                            <tr class="{{ $d->is_match ? 'table-success' : '' }}">
                                <td>
                                    <code>{{ $d->type }}</code>
                                    @if ($d->is_match)
                                        <span class="badge badge-success ml-2">Match</span>
                                    @endif
                                </td>
                                <td>{{ $d->rank_absolute ?? '—' }}</td>
                                <td class="font-weight-bold">{{ $d->title ?? '—' }}</td>
                                <td class="text-muted">{{ $d->domain ?? '—' }}</td>
                                <td class="text-muted">
                                    @if ($d->url)
                                        <a href="{{ $d->url }}" target="_blank" rel="noreferrer">{{ $d->url }}</a>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                            @if ($d->description)
                                <tr>
                                    <td></td>
                                    <td colspan="4" class="text-muted small">
                                        {{ $d->description }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @if ($details->hasPages())
            <div class="card-footer d-flex justify-content-center">
                {{ $details->links() }}
            </div>
        @endif
    </div>
@endsection