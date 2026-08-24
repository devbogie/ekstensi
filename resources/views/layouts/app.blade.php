<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Direktori Ekstensi') &middot; {{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico?v=4') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css?v=9') }}">
    @stack('styles')
</head>

<body class="d-flex flex-column min-vh-100">
    <a href="#main-content" class="visually-hidden-focusable position-absolute top-0 start-0 m-2 btn btn-primary btn-sm">
        Lewati ke konten
    </a>

    <header class="site-header">
        <div class="container py-3 d-flex align-items-center justify-content-between gap-3">
            <a href="{{ route('home') }}" class="site-brand">
                <span class="brand-mark"><i class="bi bi-journal-bookmark-fill"></i></span>
                <span>{{ config('app.name') }}</span>
            </a>

            <a href="{{ route('admin.login') }}"
                class="btn btn-ghost btn-sm d-inline-flex align-items-center gap-1">
                <i class="bi bi-shield-lock"></i>
                <span class="d-none d-sm-inline">Masuk Admin</span>
            </a>
        </div>
    </header>

    <main id="main-content" class="flex-grow-1">
        @yield('content')
    </main>

    <footer class="py-4 mt-5">
        <div class="container">
            <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2"
                style="font-size:.8rem;color:var(--app-text-faint)">
                <span>&copy; {{ date('Y') }} {{ config('app.name') }}</span>
                <span class="d-inline-flex align-items-center gap-1">
                    <i class="bi bi-tools"></i> Dikelola oleh IT SIMRS
                </span>
            </div>
        </div>
    </footer>

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/app.js?v=2') }}"></script>
    @stack('scripts')
</body>

</html>
