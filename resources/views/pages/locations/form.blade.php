@extends('layouts.app')

@php
    /** @var \App\Models\Location|null $location */
    $location = $location ?? null;
@endphp

@section('title', $location ? 'Edit location' : 'Create location')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h3 mb-0">{{ $location ? 'Edit location' : 'Create location' }}</h1>

        <a href="{{ route('locations.index') }}" class="btn btn-outline-secondary">
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
        <div class="card-header">{{ $location ? 'Update location' : 'New location' }}</div>

        <div class="card-body">
            <form method="POST" action="{{ $location ? route('locations.update', $location) : route('locations.store') }}">
                @csrf
                @if ($location)
                    @method('PUT')
                @endif

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="location_code">Location code <span class="text-danger">*</span></label>
                        <input
                                id="location_code"
                                type="number"
                                name="location_code"
                                value="{{ old('location_code', $location?->location_code) }}"
                                class="form-control @error('location_code') is-invalid @enderror"
                                required
                                autofocus
                        >
                        @error('location_code')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="form-group col-md-8">
                        <label for="location_name">Location name <span class="text-danger">*</span></label>
                        <input
                                id="location_name"
                                type="text"
                                name="location_name"
                                value="{{ old('location_name', $location?->location_name) }}"
                                class="form-control @error('location_name') is-invalid @enderror"
                                required
                        >
                        @error('location_name')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="country_iso_code">Country ISO code <span class="text-danger">*</span></label>
                        <input
                                id="country_iso_code"
                                type="text"
                                name="country_iso_code"
                                value="{{ old('country_iso_code', $location?->country_iso_code) }}"
                                class="form-control @error('country_iso_code') is-invalid @enderror"
                                required
                                maxlength="2"
                        >
                        @error('country_iso_code')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                        <small class="form-text text-muted">ISO 3166-1 alpha-2 (e.g. <code>UA</code>, <code>US</code>).</small>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="location_code_parent">Parent code</label>
                        <input
                                id="location_code_parent"
                                type="number"
                                name="location_code_parent"
                                value="{{ old('location_code_parent', $location?->location_code_parent) }}"
                                class="form-control @error('location_code_parent') is-invalid @enderror"
                        >
                        @error('location_code_parent')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label for="location_type">Location type</label>
                        <input
                                id="location_type"
                                type="text"
                                name="location_type"
                                value="{{ old('location_type', $location?->location_type) }}"
                                class="form-control @error('location_type') is-invalid @enderror"
                        >
                        @error('location_type')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                        <small class="form-text text-muted">Example: Country, State, City.</small>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ $location ? 'Update' : 'Save' }}
                </button>
            </form>
        </div>
    </div>
@endsection