<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>

    @include('partials.theme-init')

    <link rel="icon" type="image/png" href="{{ asset_versioned('logo/icon.png') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="{{ asset_versioned('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="position-fixed top-0 end-0 p-3" style="z-index:1030;">
        @include('partials.theme-toggle')
    </div>

    <div class="auth-wrapper d-flex flex-column align-items-center justify-content-center px-3 py-5">
        <a href="{{ url('/') }}" class="brand fs-4 mb-4 text-body d-flex align-items-center gap-2">
            <img src="{{ asset_versioned('logo/icon.png') }}" alt="" class="brand-logo brand-logo-lg">
            {{ config('app.name') }}
        </a>

        <div class="auth-card card shadow-sm border-0">
            <div class="card-body p-4 p-sm-5">
                @hasSection('heading')
                    <h1 class="h4 mb-4">@yield('heading')</h1>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    @include('partials.alerts')
    @include('partials.theme-toggle-script')
    <script>
        // Slow requests (synchronous SMTP sends) can take seconds — show a
        // spinner on the submit button so the form doesn't look hung.
        document.addEventListener('submit', function (e) {
            var btn = e.target.querySelector('button[type="submit"]');
            if (!btn || btn.disabled) return;
            btn.dataset.originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>'
                + (btn.dataset.loadingText || 'Please wait…');
        });

        // Restore buttons when the page comes back from the back/forward cache.
        window.addEventListener('pageshow', function (e) {
            if (!e.persisted) return;
            document.querySelectorAll('button[type="submit"][data-original-html]').forEach(function (btn) {
                btn.innerHTML = btn.dataset.originalHtml;
                btn.disabled = false;
                delete btn.dataset.originalHtml;
            });
        });
    </script>
</body>
</html>
