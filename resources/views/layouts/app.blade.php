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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset_versioned('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-body border-bottom sticky-top">
        <div class="container">
            <a class="navbar-brand fw-semibold d-flex align-items-center gap-2 me-4" href="{{ url('/home') }}">
                <img src="{{ asset_versioned('logo/icon.png') }}" alt="{{ config('app.name') }} logo" class="brand-logo">
                {{ config('app.name') }}
            </a>

            @auth
                <button class="navbar-toggler nav-burger collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#appNavbar"
                    aria-controls="appNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="nav-burger-bar"></span>
                    <span class="nav-burger-bar"></span>
                    <span class="nav-burger-bar"></span>
                </button>
            @endauth

            <div class="collapse navbar-collapse" id="appNavbar">
                @auth
                    <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-1 py-2 py-lg-0">
                        <a class="nav-pill @if (request()->routeIs('products.*') || request()->routeIs('categories.*')) active @endif"
                            href="{{ route('products.index') }}"><i class="bi bi-box-seam me-1"></i>Products</a>
                        <a class="nav-pill @if (request()->routeIs('stores.*')) active @endif"
                            href="{{ route('stores.index') }}"><i class="bi bi-shop me-1"></i>Stores</a>
                        <a class="nav-pill @if (request()->routeIs('bo.*')) active @endif"
                            href="{{ route('bo.index') }}"><i class="bi bi-clipboard2-minus me-1"></i>B.O</a>
                        @if (auth()->user()->is_admin)
                            <a class="nav-pill @if (request()->routeIs('users.*')) active @endif"
                                href="{{ route('users.index') }}"><i class="bi bi-people me-1"></i>Users</a>
                            <a class="nav-pill @if (request()->routeIs('audit.*')) active @endif"
                                href="{{ route('audit.index') }}"><i class="bi bi-journal-text me-1"></i>Audit</a>
                        @endif
                    </div>
                @endauth

                @auth
                    <div class="ms-lg-auto d-flex align-items-center gap-3 py-2 py-lg-0">
                        <a href="{{ route('profile') }}" title="My profile"
                            class="nav-pill @if (request()->routeIs('profile')) active @endif d-flex align-items-center gap-1">
                            <i class="bi bi-person-circle"></i>{{ auth()->user()->name }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="m-0 ms-auto ms-lg-0" data-logout-form>
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-box-arrow-right me-1"></i>Log out
                            </button>
                        </form>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <main class="@yield('main_class', 'container') py-4 py-lg-5">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    @include('partials.alerts')
    @include('partials.theme-toggle-script')
    @include('partials.settings-panel')
    <script>
        // Live search: fetch results in the background and swap only the
        // [data-search-results] region — no page reload, so fast typing or
        // backspacing never loses keystrokes.
        document.querySelectorAll('input[data-live-search]').forEach(function (input) {
            var form = input.form;
            var timer;
            var seq = 0;

            input.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(function () {
                    var target = document.querySelector('[data-search-results]');
                    if (!target || !window.fetch) {
                        form.submit();
                        return;
                    }

                    var params = new URLSearchParams(new FormData(form));
                    params.delete('page'); // new search -> back to page 1
                    var url = form.action + (params.toString() ? '?' + params.toString() : '');
                    var current = ++seq;

                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(function (response) { return response.text(); })
                        .then(function (html) {
                            if (current !== seq) return; // a newer search superseded this one
                            var fresh = new DOMParser().parseFromString(html, 'text/html')
                                .querySelector('[data-search-results]');
                            if (fresh) {
                                target.innerHTML = fresh.innerHTML;
                                history.replaceState(null, '', url);
                            }
                        })
                        .catch(function () { form.submit(); });
                }, 400);
            });
        });

        // SweetAlert confirmation for destructive forms (data-confirm="message").
        // Delegated so it also covers rows swapped in by the live search.
        document.addEventListener('submit', function (e) {
            var form = e.target.closest('form[data-confirm]');
            if (!form) return;
            e.preventDefault();
            if (typeof Swal === 'undefined') {
                if (confirm(form.dataset.confirm)) form.submit();
                return;
            }
            Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: form.dataset.confirm,
                showCancelButton: true,
                confirmButtonText: form.dataset.confirmButton || 'Delete',
                confirmButtonColor: form.dataset.confirmColor || '#dc3545',
            }).then(function (result) {
                if (result.isConfirmed) form.submit();
            });
        });

        // Confirm before logging out.
        document.querySelectorAll('[data-logout-form]').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                if (typeof Swal === 'undefined') {
                    if (confirm('Are you sure you want to log out?')) form.submit();
                    return;
                }
                Swal.fire({
                    icon: 'question',
                    title: 'Log out?',
                    text: 'Are you sure you want to sign out?',
                    showCancelButton: true,
                    confirmButtonText: 'Log out',
                    confirmButtonColor: '#dc3545',
                }).then(function (result) {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    </script>
</body>
</html>
