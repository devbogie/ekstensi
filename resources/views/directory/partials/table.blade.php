@php
    $paginator = $extensions;
    $hasFilter = (request('search') !== null && request('search') !== '')
        || request('department') || request('floor') || request('branch');
@endphp

<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <span class="result-meta">
        @if ($paginator->total() > 0)
            Menampilkan <strong>{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}</strong> dari
            <strong>{{ $paginator->total() }}</strong> ekstensi
        @else
            Tidak ada ekstensi ditemukan
        @endif
    </span>

    @if ($hasFilter)
        <div class="filter-badges d-flex flex-wrap gap-1" aria-label="Filter aktif">
            @if (request('search') !== null && request('search') !== '')
                <span class="badge">
                    Cari: &ldquo;{{ request('search') }}&rdquo;
                    <button type="button" class="btn-remove-badge" data-remove-filter="search"
                        aria-label="Hapus pencarian"><i class="bi bi-x-lg"></i></button>
                </span>
            @endif
            @foreach ([
                'department' => 'Departemen',
                'floor' => 'Lantai',
                'branch' => 'Cabang',
            ] as $key => $label)
                @php $value = (int) request($key); @endphp
                @if ($value > 0)
                    @php $name = $filterOptions[$key]->firstWhere('id', $value)?->name ?? '—'; @endphp
                    <span class="badge">
                        {{ $label }}: {{ $name }}
                        <button type="button" class="btn-remove-badge" data-remove-filter="{{ $key }}"
                            aria-label="Hapus filter {{ $label }}"><i class="bi bi-x-lg"></i></button>
                    </span>
                @endif
            @endforeach
        </div>
    @endif
</div>

@if ($paginator->isEmpty())
    <div class="card">
        <div class="empty-state">
            <div class="empty-icon"><i class="bi bi-search"></i></div>
            <h3>
                {{ $hasFilter ? 'Tidak ada hasil yang cocok' : 'Belum ada data ekstensi' }}
            </h3>
            <p>
                {{ $hasFilter
                    ? 'Coba ubah kata kunci pencarian atau hilangkan sebagian filter.'
                    : 'Data akan tampil di sini setelah admin menambahkan ekstensi.' }}
            </p>
            @if ($hasFilter)
                <button type="button" class="btn btn-outline-primary btn-sm mt-3"
                    id="btn-empty-reset">Tampilkan Semua</button>
            @endif
        </div>
    </div>
@else
    <div class="card table-card d-none d-lg-block">
        <div class="table-responsive">
            <table class="table table-hover align-middle" aria-label="Daftar ekstensi">
                <thead>
                    <tr>
                        <th scope="col" style="width:200px">Nomor Ekstensi</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Departemen</th>
                        <th scope="col" style="width:130px">Lantai</th>
                        <th scope="col">Cabang</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($paginator as $extension)
                        <tr>
                            <td>
                                <span class="ext-number">{{ $extension->number }}</span>
                            </td>
                            <td class="fw-medium">{{ $extension->name }}</td>
                            <td class="text-muted-2">{{ $extension->department?->name ?? '—' }}</td>
                            <td class="text-muted-2">{{ $extension->floor?->name ?? '—' }}</td>
                            <td class="text-muted-2">{{ $extension->branch?->name ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-lg-none d-flex flex-column gap-2">
        @foreach ($paginator as $extension)
            <article class="card p-3" style="border-radius:var(--app-radius-lg)">
                <div class="d-flex align-items-center gap-2 min-width-0">
                        <span class="ext-number" style="font-size:1.25rem">{{ $extension->number }}</span>
                    </div>
                <div class="fw-semibold mt-1 mb-2" style="font-size:1.02rem">{{ $extension->name }}</div>
                <dl class="row row-cols-1 gx-2 gy-1 mb-0" style="font-size:.83rem">
                    <div class="col d-flex gap-2">
                        <dt class="fw-medium text-muted-2" style="width:92px;flex:0 0 92px">Departemen</dt>
                        <dd class="mb-0">{{ $extension->department?->name ?? '—' }}</dd>
                    </div>
                    <div class="col d-flex gap-2">
                        <dt class="fw-medium text-muted-2" style="width:92px;flex:0 0 92px">Lantai</dt>
                        <dd class="mb-0">{{ $extension->floor?->name ?? '—' }}</dd>
                    </div>
                    <div class="col d-flex gap-2">
                        <dt class="fw-medium text-muted-2" style="width:92px;flex:0 0 92px">Cabang</dt>
                        <dd class="mb-0">{{ $extension->branch?->name ?? '—' }}</dd>
                    </div>
                </dl>
            </article>
        @endforeach
    </div>

    @include('partials.pagination', ['paginator' => $paginator])
@endif
