@extends('layouts.app')

@section('title', 'Locations')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h3 mb-0">Locations</h1>

        <div class="d-flex align-items-center" style="gap: .5rem;">
            <form id="refresh-form" action="{{ route('locations.refresh') }}" method="POST" class="m-0">
                @csrf
                <button id="refresh-btn" type="submit" class="btn btn-outline-primary">
                    <span class="btn-label">Refresh from service</span>
                    <span class="btn-loading d-none">
                        <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
                        Refreshing...
                    </span>
                </button>
            </form>

            <a href="{{ route('locations.create') }}" class="btn btn-primary">
                Create location
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card app-card">
        <div class="card-header">Locations</div>

        <div class="card-body p-0">
            @if (empty($locations) || $locations->count() === 0)
                <div class="p-3">
                    <div class="text-muted">
                        No locations yet. Click <strong>Create location</strong> to add the first one.
                    </div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="thead-light">
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th style="width: 140px;">Code</th>
                            <th>Name</th>
                            <th style="width: 110px;">ISO</th>
                            <th style="width: 160px;">Parent</th>
                            <th style="width: 160px;">Type</th>
                            <th style="width: 220px;" class="text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($locations as $location)
                            <tr>
                                <td>{{ $location->id }}</td>
                                <td class="font-weight-bold">{{ $location->location_code }}</td>
                                <td>{{ $location->location_name }}</td>
                                <td><code>{{ $location->country_iso_code }}</code></td>
                                <td class="text-muted">{{ $location->location_code_parent ?? '—' }}</td>
                                <td class="text-muted">{{ $location->location_type ?? '—' }}</td>
                                <td class="text-right">
                                    <a href="{{ route('locations.edit', $location) }}" class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>

                                    <form action="{{ route('locations.destroy', $location) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Delete this location?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @if (!empty($locations) && $locations->hasPages())
            <div class="card-footer">
                {{ $locations->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
      (function () {
        var form = document.getElementById('refresh-form');
        var btn = document.getElementById('refresh-btn');
        if (!form || !btn) return;

        form.addEventListener('submit', function (e) {
          if (!confirm('Refresh locations from service? This will update the table.')) {
            e.preventDefault();
            return;
          }

          btn.disabled = true;

          var label = btn.querySelector('.btn-label');
          var loading = btn.querySelector('.btn-loading');

          if (label) label.classList.add('d-none');
          if (loading) loading.classList.remove('d-none');
        });
      })();
    </script>
@endpush