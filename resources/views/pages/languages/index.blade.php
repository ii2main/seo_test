@extends('layouts.app')

@section('title', 'Languages')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h3 mb-0">Languages</h1>

        <div class="d-flex align-items-center" style="gap: .5rem;">
            <form id="refresh-form" action="{{ route('languages.refresh') }}" method="POST" class="m-0">
                @csrf
                <button id="refresh-btn" type="submit" class="btn btn-outline-primary">
                    <span class="btn-label">Refresh from service</span>
                    <span class="btn-loading d-none">
                        <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
                        Refreshing...
                    </span>
                </button>
            </form>

            <a href="{{ route('languages.create') }}" class="btn btn-primary">
                Create language
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card app-card">
        <div class="card-header">Languages</div>

        <div class="card-body p-0">
            @if (empty($languages) || $languages->count() === 0)
                <div class="p-3">
                    <div class="text-muted">
                        No languages yet. Click <strong>Create language</strong> to add the first one.
                    </div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="thead-light">
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Name</th>
                            <th style="width: 160px;">Code</th>
                            <th style="width: 210px;">Created</th>
                            <th style="width: 220px;" class="text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($languages as $language)
                            <tr>
                                <td>{{ $language->id }}</td>
                                <td class="font-weight-bold">{{ $language->language_name }}</td>
                                <td><code>{{ $language->language_code }}</code></td>
                                <td class="text-muted">{{ optional($language->created_at)->format('Y-m-d H:i') }}</td>
                                <td class="text-right">
                                    <a href="{{ route('languages.edit', $language) }}" class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>

                                    <form action="{{ route('languages.destroy', $language) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Delete this language?')">
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

        @if (!empty($languages) && $languages->hasPages())
            <div class="card-footer">
                {{ $languages->links() }}
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
          if (!confirm('Refresh languages from service? This will update the table.')) {
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

