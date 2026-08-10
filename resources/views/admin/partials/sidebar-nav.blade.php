@php
    $menu = [
        [
            'label' => 'Ekstensi',
            'icon' => 'bi-telephone-outbound',
            'route' => 'admin.dashboard',
            'count' => \App\Models\Extension::count(),
        ],
    ];

    $references = [
        ['label' => 'Departemen', 'icon' => 'bi-diagram-3', 'route' => 'admin.departments.index', 'count' => \App\Models\Department::count()],
        ['label' => 'Lantai', 'icon' => 'bi-layers', 'route' => 'admin.floors.index', 'count' => \App\Models\Floor::count()],
        ['label' => 'Cabang', 'icon' => 'bi-buildings', 'route' => 'admin.branches.index', 'count' => \App\Models\Branch::count()],
    ];
@endphp

<nav class="sidebar-nav" aria-label="Navigasi admin">
    @foreach ($menu as $item)
        <a href="{{ route($item['route']) }}"
            class="nav-link {{ request()->routeIs($item['route']) ? 'active' : '' }}"
            data-tt="{{ $item['label'] }}">
            <i class="bi {{ $item['icon'] }}"></i>
            <span class="nav-label">{{ $item['label'] }}</span>
            <span class="count-pill">{{ $item['count'] }}</span>
        </a>
    @endforeach

    <div class="nav-group-label">Referensi</div>

    @foreach ($references as $item)
        <a href="{{ route($item['route']) }}"
            class="nav-link {{ request()->routeIs($item['route'].'.*', $item['route']) ? 'active' : '' }}"
            data-tt="{{ $item['label'] }}">
            <i class="bi {{ $item['icon'] }}"></i>
            <span class="nav-label">{{ $item['label'] }}</span>
            <span class="count-pill">{{ $item['count'] }}</span>
        </a>
    @endforeach

    <div class="nav-group-label">Akun</div>

    <a href="{{ route('home') }}" target="_blank" rel="noopener" class="nav-link" data-tt="Lihat Situs">
        <i class="bi bi-box-arrow-up-right"></i>
        <span class="nav-label">Lihat Situs</span>
    </a>

    <a href="{{ route('admin.profile') }}"
        class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}" data-tt="Ubah Kata Sandi">
        <i class="bi bi-key"></i>
        <span class="nav-label">Ubah Kata Sandi</span>
    </a>

    <button type="button" class="nav-link w-100 text-start border-0 bg-transparent"
        data-bs-toggle="modal" data-bs-target="#logoutModal" data-tt="Keluar"
        style="font-size:.875rem;font-weight:500;padding:.5rem .65rem;border-radius:var(--app-radius);color:#4b5563">
        <i class="bi bi-box-arrow-right"></i>
        <span class="nav-label">Keluar</span>
    </button>
</nav>
