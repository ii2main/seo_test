@extends('layouts.app')

@php
    /** @var \App\Models\Domain|null $domain */
    $domain = $domain ?? null;
@endphp

@section('title', $domain ? 'Edit domain' : 'Create domain')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h3 mb-0">{{ $domain ? 'Edit domain' : 'Create domain' }}</h1>

        <a href="{{ route('domains.index') }}" class="btn btn-outline-secondary">
            Back to list
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card app-card">
        <div class="card-header">{{ $domain ? 'Update domain' : 'New domain' }}</div>

        <div class="card-body">
            <form method="POST" action="{{ $domain ? route('domains.update', $domain) : route('domains.store') }}">
                @csrf
                @if ($domain)
                    @method('PUT')
                @endif

                <div class="form-group">
                    <label for="domain">Domain <span class="text-danger">*</span></label>
                    <input
                            id="domain"
                            type="text"
                            name="domain"
                            value="{{ old('domain', $domain?->domain) }}"
                            class="form-control @error('domain') is-invalid @enderror"
                            placeholder="example.com"
                            required
                            autofocus
                    >
                    @error('domain')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <small class="form-text text-muted">
                        Enter domain without protocol (e.g. <code>example.com</code>).
                    </small>
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ $domain ? 'Update' : 'Save' }}
                </button>
            </form>
        </div>
    </div>
@endsection