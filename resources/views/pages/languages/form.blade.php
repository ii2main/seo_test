@extends('layouts.app')

@php
    /** @var \App\Models\Language|null $language */
    $language = $language ?? null;
@endphp

@section('title', $language ? 'Edit language' : 'Create language')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h3 mb-0">{{ $language ? 'Edit language' : 'Create language' }}</h1>

        <a href="{{ route('languages.index') }}" class="btn btn-outline-secondary">
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
        <div class="card-header">{{ $language ? 'Update language' : 'New language' }}</div>

        <div class="card-body">
            <form method="POST" action="{{ $language ? route('languages.update', $language) : route('languages.store') }}">
                @csrf
                @if ($language)
                    @method('PUT')
                @endif

                <div class="form-row">
                    <div class="form-group col-md-8">
                        <label for="language_name">Language name <span class="text-danger">*</span></label>
                        <input
                                id="language_name"
                                type="text"
                                name="language_name"
                                value="{{ old('language_name', $language?->language_name) }}"
                                class="form-control @error('language_name') is-invalid @enderror"
                                placeholder="English"
                                required
                                autofocus
                        >
                        @error('language_name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label for="language_code">Language code <span class="text-danger">*</span></label>
                        <input
                                id="language_code"
                                type="text"
                                name="language_code"
                                value="{{ old('language_code', $language?->language_code) }}"
                                class="form-control @error('language_code') is-invalid @enderror"
                                placeholder="1"
                                required
                        >
                        @error('language_code')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
{{--                        <small class="form-text text-muted">--}}
{{--                            Some message--}}
{{--                        </small>--}}
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ $language ? 'Update' : 'Save' }}
                </button>
            </form>
        </div>
    </div>
@endsection