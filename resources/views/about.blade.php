@extends('layouts.app')

@section('title', 'About')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h3 mb-0">About This Application</h1>
    </div>

    <div class="card about-card">
        <div class="card-header">About</div>

        <div class="card-body p-3">
            <section class="mb-3">
                <h2 class="h5">Description</h2>
                <p>
                    This is a simple Laravel application that provides SEO rank calculation for any domain using the DataForSeo service.
                    It is designed to be used as a test work for <a href="https://softoria.com/">Softoria</a>.
                </p>
            </section>

            <section>
                <h2 class="h5">Usage Instructions</h2>
                <ol>
                    <li>Add your domain names on the <a href="{{ route('domains.index') }}">Domains</a> page.</li>
                    <li>Get locations from DataForSeo service using the Get/Refresh button on the <a href="{{ route('locations.index') }}">Locations</a> page.</li>
                    <li>Get languages from DataForSeo service using the Get/Refresh button on the <a href="{{ route('languages.index') }}">Languages</a> page.</li>
                    <li>To get the rank, use the "New Check" button on <a href="{{ route('ranks.index') }}">Ranks</a> page. On the form that appears, select a domain, location, and language, and enter a keyword. After making a check task, you will be able to get the rank result in the ranks table using the "Get Result" button.</li>
                </ol>
            </section>
        </div>

        <div class="card-footer">
            &copy; 2026, Made by Ivan Broshchak for <a href="https://softoria.com/">Softoria</a> as test work
        </div>
    </div>
@endsection
