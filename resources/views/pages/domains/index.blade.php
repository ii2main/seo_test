@extends('layouts.app')

@section('title', 'Domains')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h3 mb-0">Domains</h1>

        <a href="{{ route('domains.create') }}" class="btn btn-primary">
            Add domain
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card app-card">
        <div class="card-header">Your domains</div>

        <div class="card-body p-0">
            @if ($domains->count() === 0)
                <div class="p-3">
                    <div class="text-muted">
                        No domains yet. Click <strong>Add domain</strong> to add the first one.
                    </div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="thead-light">
                        <tr>
                            <th style="width: 90px;">ID</th>
                            <th>Domain</th>
                            <th style="width: 210px;">Added</th>
                            <th style="width: 220px;" class="text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($domains as $domain)
                            <tr>
                                <td>{{ $domain->id }}</td>
                                <td class="font-weight-bold">{{ $domain->domain }}</td>
                                <td class="text-muted">{{ optional($domain->created_at)->format('Y-m-d H:i') }}</td>
                                <td class="text-right">
                                    <a href="{{ route('domains.edit', $domain) }}" class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>

                                    <form action="{{ route('domains.destroy', $domain) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Delete this domain?')">
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

        @if ($domains->hasPages())
            <div class="card-footer">
                {{ $domains->links() }}
            </div>
        @endif
    </div>
@endsection