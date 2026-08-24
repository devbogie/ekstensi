<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Admin') &middot; {{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico?v=4') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css?v=9') }}">
    @stack('styles')
</head>

<body>
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <a href="{{ route('admin.dashboard') }}" class="site-brand">
                <span class="brand-mark"><i class="bi bi-journal-bookmark-fill"></i></span>
                <span class="brand-text text-truncate">Ekstensi</span>
            </a>

            @include('admin.partials.sidebar-nav')

            <div class="sidebar-footer">
                <span class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                <div class="flex-grow-1 min-width-0">
                    <div class="user-name text-truncate">{{ Auth::user()->name }}</div>
                    <div class="user-email text-truncate">{{ Auth::user()->username }}</div>
                </div>
            </div>
        </aside>

        <div class="offcanvas offcanvas-start" tabindex="-1" id="adminOffcanvas" aria-labelledby="offcanvasLabel">
            <div class="offcanvas-header" style="border-bottom:1px solid var(--app-border)">
                <span class="site-brand">
                    <span class="brand-mark"><i class="bi bi-journal-bookmark-fill"></i></span>
                    <span class="fw-semibold text-truncate">Ekstensi</span>
                </span>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
            </div>
            <div class="offcanvas-body p-0">
                @include('admin.partials.sidebar-nav')
            </div>
        </div>

        <main class="admin-main">
            <header class="admin-topbar">
                <button class="icon-btn btn-toggle-sidebar" type="button" id="btn-toggle-sidebar"
                    aria-label="Buka menu samping" aria-expanded="false" data-tooltip="Menu">
                    <i class="bi bi-layout-sidebar-inset" style="transition:transform .18s ease"></i>
                </button>
                <span class="fw-semibold text-truncate min-width-0" style="font-size:.95rem">@yield('title', 'Panel Admin')</span>
                <span class="ms-auto flex-shrink-0">
                    <a href="{{ route('home') }}" target="_blank" rel="noopener"
                        class="btn btn-ghost btn-sm d-inline-flex align-items-center gap-1">
                        <i class="bi bi-box-arrow-up-right"></i>
                        <span>Lihat Situs</span>
                    </a>
                </span>
            </header>
            <div class="admin-content">
                @yield('content')
            </div>
        </main>
    </div>

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/app.js?v=2') }}"></script>
    <script>
        App.initSidebarCollapse();
    </script>
    @stack('scripts')

    {{-- Modal konfirmasi logout --}}
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalTitle">Keluar dari Panel Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Anda yakin ingin keluar dari panel admin?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-box-arrow-right me-1"></i>Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
