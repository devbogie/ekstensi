@php
    $paginator = $items;
    $hasSearch = request('search') !== null && request('search') !== '';
    $iconName = $icon ?? 'bi-folder2';
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
            <div class="empty-icon"><i class="bi {{ $iconName }}"></i></div>
            <h3>{{ $hasSearch ? 'Tidak ada hasil yang cocok' : 'Belum ada data ' . mb_strtolower($singular) }}</h3>
            <p>
                {{ $hasSearch
                    ? 'Coba kata kunci lain atau bersihkan pencarian.'
                    : 'Klik "Tambah ' . $singular . '" untuk menambahkan data pertama.' }}
            </p>
        </div>
    </div>
@else
    <div class="card table-card d-none d-lg-block">
        <div class="table-responsive">
            <table class="table align-middle" aria-label="Daftar {{ mb_strtolower($singular) }}">
                <thead>
                    <tr>
                        <th scope="col">Nama {{ $singular }}</th>
                        <th scope="col" style="width:200px">Jumlah Ekstensi</th>
                        <th scope="col" class="text-end" style="width:110px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($paginator as $item)
                        <tr>
                            <td>
                                <span class="fw-semibold text-dark">{{ $item->name }}</span>
                            </td>
                            <td>
                                @if ($item->extensions_count > 0)
                                    <span class="badge text-teal d-inline-flex align-items-center"
                                        style="background:var(--app-primary-soft, #f0fdfa);border:1px solid #ccfbf1;border-radius:999px;font-weight:500;padding:.35em .75em">
                                        {{ $item->extensions_count }} ekstensi
                                    </span>
                                @else
                                    <span class="badge text-muted-2 d-inline-flex align-items-center"
                                        style="background:#f3f4f6;border:1px solid #e5e7eb;border-radius:999px;font-weight:500;padding:.35em .75em">
                                        0 ekstensi
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="actions-cell">
                                    <button type="button" class="icon-btn edit" aria-label="Ubah {{ $item->name }}" data-tooltip="Ubah"
                                        data-edit-item data-id="{{ $item->id }}" data-name="{{ $item->name }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="icon-btn delete" aria-label="Hapus {{ $item->name }}" data-tooltip="Hapus"
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

    <div class="d-lg-none">
        <div class="card ref-list-card">
            @foreach ($paginator as $item)
                <div class="ref-list-item">
                    <div class="ref-item-content">
                        <div class="ref-item-info">
                            <div class="ref-item-name" title="{{ $item->name }}">{{ $item->name }}</div>
                            <div class="ref-item-meta">
                                @if ($item->extensions_count > 0)
                                    <span class="badge text-teal ref-badge-pill">
                                        {{ $item->extensions_count }} ekstensi
                                    </span>
                                @else
                                    <span class="badge text-muted-2 ref-badge-pill ref-badge-empty">
                                        0 ekstensi
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="actions-cell">
                        <button type="button" class="icon-btn edit" aria-label="Ubah {{ $item->name }}" data-tooltip="Ubah"
                            data-edit-item data-id="{{ $item->id }}" data-name="{{ $item->name }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="icon-btn delete" aria-label="Hapus {{ $item->name }}" data-tooltip="Hapus"
                            data-delete-item data-id="{{ $item->id }}" data-name="{{ $item->name }}">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @include('partials.pagination', ['paginator' => $paginator])
@endif
