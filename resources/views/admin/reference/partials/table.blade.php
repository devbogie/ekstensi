@php
    $paginator = $items;
    $hasSearch = request('search') !== null && request('search') !== '';
@endphp

<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <span class="result-meta">
        @if ($paginator->total() > 0)
            Menampilkan <strong>{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}</strong> dari
            <strong>{{ $paginator->total() }}</strong> {{ mb_strtolower($singular) }}
        @else
            Tidak ada {{ mb_strtolower($singular) }} ditemukan
        @endif
    </span>
</div>

@if ($paginator->isEmpty())
    <div class="card">
        <div class="empty-state">
            <div class="empty-icon"><i class="bi bi-inbox"></i></div>
            <h3>{{ $hasSearch ? 'Tidak ada hasil yang cocok' : 'Belum ada data' }}</h3>
            <p>
                {{ $hasSearch
                    ? 'Coba kata kunci lain atau bersihkan pencarian.'
                    : 'Klik "Tambah ' . mb_strtolower($singular) . '" untuk menambahkan data pertama.' }}
            </p>
        </div>
    </div>
@else
    <div class="card table-card d-none d-lg-block">
        <div class="table-responsive">
            <table class="table align-middle" aria-label="Daftar {{ mb_strtolower($singular) }}">
                <thead>
                    <tr>
                        <th scope="col">Nama</th>
                        <th scope="col" style="width:180px">Jumlah Ekstensi</th>
                        <th scope="col" class="text-end" style="width:110px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($paginator as $item)
                        <tr>
                            <td class="fw-medium">{{ $item->name }}</td>
                            <td>
                                <span class="badge {{ $item->extensions_count > 0 ? 'text-teal' : 'text-muted-2' }}"
                                    style="background:{{ $item->extensions_count > 0 ? 'var(--app-primary-soft, #f0fdfa)' : '#f3f4f6' }};border:1px solid {{ $item->extensions_count > 0 ? '#ccfbf1' : '#e5e7eb' }};border-radius:999px;font-weight:500">
                                    {{ $item->extensions_count }} ekstensi
                                </span>
                            </td>
                            <td>
                                <div class="actions-cell">
                                    <button type="button" class="icon-btn edit" aria-label="Ubah" data-tooltip="Ubah"
                                        data-edit-item data-id="{{ $item->id }}" data-name="{{ $item->name }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="icon-btn delete" aria-label="Hapus" data-tooltip="Hapus"
                                        data-delete-item data-id="{{ $item->id }}" data-name="{{ $item->name }}">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-lg-none d-flex flex-column gap-2">
        @foreach ($paginator as $item)
            <article class="card p-3 d-flex align-items-center justify-content-between gap-2"
                style="border-radius:var(--app-radius-lg)">
                <div class="min-width-0">
                    <div class="fw-semibold text-truncate">{{ $item->name }}</div>
                    <div class="result-meta mt-1">
                        {{ $item->extensions_count }} ekstensi terhubung
                    </div>
                </div>
                <div class="actions-cell flex-shrink-0">
                    <button type="button" class="icon-btn edit" aria-label="Ubah" data-tooltip="Ubah"
                        data-edit-item data-id="{{ $item->id }}" data-name="{{ $item->name }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" class="icon-btn delete" aria-label="Hapus" data-tooltip="Hapus"
                        data-delete-item data-id="{{ $item->id }}" data-name="{{ $item->name }}">
                        <i class="bi bi-trash3"></i>
                    </button>
                </div>
            </article>
        @endforeach
    </div>

    @if ($paginator->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $paginator->links() }}
        </div>
    @endif
@endif
