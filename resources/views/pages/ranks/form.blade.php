@extends('layouts.app')

@php
    $rank = $rank ?? null;
@endphp

@section('title', 'SEO Rank Checker')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h3 mb-0">SEO Rank Checker</h1>
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
        <div class="card-header">Check rank</div>

        <div class="card-body">
            <form method="POST" action="{{ $rank ? route('ranks.update', $rank) : route('ranks.store') }}">
                @csrf
                @if ($rank)
                    @method('PUT')
                @endif

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="keyword">Keyword <span class="text-danger">*</span></label>
                        <input
                                id="keyword"
                                type="text"
                                name="keyword"
                                value="{{ old('keyword', $rank?->keyword) }}"
                                class="form-control @error('keyword') is-invalid @enderror"
                                required
                                autofocus
                        >
                        @error('keyword')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label for="domain_id">Domain <span class="text-danger">*</span></label>
                        <select
                                id="domain_id"
                                name="domain_id"
                                class="form-control @error('domain_id') is-invalid @enderror"
                                required
                        >
                            <option value="" disabled {{ old('domain_id', $rank?->domain_id) ? '' : 'selected' }}>
                                — Select domain —
                            </option>

                            @foreach (($domains ?? collect()) as $domain)
                                <option value="{{ $domain->id }}"
                                        {{ (string)old('domain_id', $rank?->domain_id) === (string)$domain->id ? 'selected' : '' }}>
                                    {{ $domain->domain }}
                                </option>
                            @endforeach
                        </select>
                        @error('domain_id')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="location_id">Location <span class="text-danger">*</span></label>
                        <select
                                id="location_id"
                                name="location_id"
                                class="form-control @error('location_id') is-invalid @enderror"
                                required
                                style="width: 100%;"
                        ></select>
                        @error('location_id')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label for="language_id">Language <span class="text-danger">*</span></label>
                        <select
                                id="language_id"
                                name="language_id"
                                class="form-control @error('language_id') is-invalid @enderror"
                                required
                                style="width: 100%;"
                        ></select>
                        @error('language_id')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    Search
                </button>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
    <style>
        .select2-container--bootstrap4 .select2-selection--single { height: calc(1.5em + .75rem + 2px); }
        .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered { line-height: calc(1.5em + .75rem); }
        .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow { height: calc(1.5em + .75rem + 2px); }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript">
      $(function () {
        $('#location_id').select2({
          theme: 'bootstrap4',
          placeholder: '— Select location —',
          allowClear: true,
          ajax: {
            url: '{{ route('locations.for-select') }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
              return { q: params.term || '', page: params.page || 1 };
            },
            processResults: function (data, params) {
              params.page = params.page || 1;
              return {
                results: data.results || [],
                pagination: data.pagination || { more: false }
              };
            },
            cache: true
          }
        });

        $('#language_id').select2({
          theme: 'bootstrap4',
          placeholder: '— Select language —',
          allowClear: true,
          ajax: {
            url: '{{ route('languages.for-select') }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
              return { q: params.term || '', page: params.page || 1 };
            },
            processResults: function (data, params) {
              params.page = params.page || 1;
              return {
                results: data.results || [],
                pagination: data.pagination || { more: false }
              };
            },
            cache: true
          }
        });
      });
    </script>
@endpush