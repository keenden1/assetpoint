<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') · {{ config('app.name') }}</title>

    @include('partials.theme-init')

    <link rel="icon" type="image/png" href="{{ asset_versioned('logo/icon.png') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="{{ asset_versioned('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="d-flex flex-column align-items-center justify-content-center text-center px-3" style="min-height: 100vh;">
        <p class="display-1 fw-bold mb-0">@yield('code')</p>
        <h1 class="h4 mb-2">@yield('title')</h1>
        <p class="text-secondary mb-4">@yield('message')</p>
        <a href="{{ url('/') }}" class="btn btn-dark">Back to home</a>
    </div>
</body>
</html>
