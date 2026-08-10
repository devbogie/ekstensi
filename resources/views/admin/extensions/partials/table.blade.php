@php
    $paginator = $extensions;
    $hasFilter = (request('search') !== null && request('search') !== '')
        || request('department') || request('floor') || request('branch');
@endphp

@if ($paginator->isEmpty())
    <div class="card">
        <div class="empty-state">
            <div class="empty-icon"><i class="bi bi-telephone-x"></i></div>
            <h3>{{ $hasFilter ? 'Tidak ada hasil yang cocok' : 'Belum ada data ekstensi' }}</h3>
            <p>
                {{ $hasFilter
                    ? 'Coba ubah kata kunci atau hilangkan sebagian filter.'
                    : 'Klik "Tambah Ekstensi" atau gunakan fitur Import untuk mengisi data.' }}
            </p>
        </div>
    </div>
@else
    <div class="card table-card d-none d-xl-block">
        <div class="table-responsive">
            <table class="table table-hover align-middle" aria-label="Daftar ekstensi">
                <thead>
                    <tr>
                        <th scope="col">Nomor</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Departemen</th>
                        <th scope="col">Lantai</th>
                        <th scope="col">Cabang</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-end" style="width:110px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($paginator as $extension)
                        <tr>
                            <td class="ext-number">{{ $extension->number }}</td>
                            <td class="fw-medium">{{ $extension->name }}</td>
                            <td class="text-muted-2">{{ $extension->department?->name ?? '—' }}</td>
                            <td class="text-muted-2">{{ $extension->floor?->name ?? '—' }}</td>
                            <td class="text-muted-2">{{ $extension->branch?->name ?? '—' }}</td>
                            <td>
                                @if ($extension->status)
                                    <span class="badge text-teal"
                                        style="background:var(--app-primary-soft, #f0fdfa);border:1px solid #ccfbf1;border-radius:999px;font-weight:500">Aktif</span>
                                @else
                                    <span class="badge text-muted-2"
                                        style="background:#f3f4f6;border:1px solid #e5e7eb;border-radius:999px;font-weight:500">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="actions-cell">
                                    <button type="button" class="icon-btn edit" aria-label="Ubah ekstensi" data-tooltip="Ubah"
                                        data-edit-extension
                                        data-id="{{ $extension->id }}"
                                        data-number="{{ $extension->number }}"
                                        data-name="{{ $extension->name }}"
                                        data-department="{{ $extension->department_id }}"
                                        data-floor="{{ $extension->floor_id }}"
                                        data-branch="{{ $extension->branch_id }}"
                                        data-status="{{ $extension->status ? 1 : 0 }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="icon-btn delete" aria-label="Hapus ekstensi" data-tooltip="Hapus"
                                        data-delete-extension
                                        data-id="{{ $extension->id }}"
                                        data-number="{{ $extension->number }}"
                                        data-name="{{ $extension->name }}">
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

    <div class="d-xl-none d-flex flex-column gap-2">
        @foreach ($paginator as $extension)
            <article class="card p-3" style="border-radius:var(--app-radius-lg)">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div class="min-width-0">
                        <div class="ext-number" style="font-size:1.2rem">{{ $extension->number }}</div>
                        <div class="fw-semibold text-truncate">{{ $extension->name }}</div>
                    </div>
                    <div class="actions-cell">
                        <button type="button" class="icon-btn edit" aria-label="Ubah ekstensi" data-tooltip="Ubah"
                            data-edit-extension
                            data-id="{{ $extension->id }}"
                            data-number="{{ $extension->number }}"
                            data-name="{{ $extension->name }}"
                            data-department="{{ $extension->department_id }}"
                            data-floor="{{ $extension->floor_id }}"
                            data-branch="{{ $extension->branch_id }}"
                            data-status="{{ $extension->status ? 1 : 0 }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="icon-btn delete" aria-label="Hapus ekstensi" data-tooltip="Hapus"
                            data-delete-extension
                            data-id="{{ $extension->id }}"
                            data-number="{{ $extension->number }}"
                            data-name="{{ $extension->name }}">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>
                <div class="d-flex flex-column gap-1 mt-2 pt-2"
                    style="border-top:1px solid var(--app-border);font-size:.82rem">
                    <div class="d-flex justify-content-between gap-2">
                        <span class="text-muted-2">Departemen</span>
                        <span class="fw-medium text-truncate text-end">{{ $extension->department?->name ?? '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between gap-2">
                        <span class="text-muted-2">Lantai</span>
                        <span class="fw-medium">{{ $extension->floor?->name ?? '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between gap-2">
                        <span class="text-muted-2">Cabang</span>
                        <span class="fw-medium text-truncate text-end">{{ $extension->branch?->name ?? '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between gap-2">
                        <span class="text-muted-2">Status</span>
                        <span class="fw-medium">{{ $extension->status ? 'Aktif' : 'Tidak Aktif' }}</span>
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    @include('partials.pagination', ['paginator' => $paginator])
@endif
