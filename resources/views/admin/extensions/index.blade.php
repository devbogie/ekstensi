@extends('layouts.admin')

@section('title', 'Data Ekstensi')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <div class="page-heading">
            <h1 class="mb-1">Ekstensi</h1>
            <p class="lead mb-0">Kelola nomor ekstensi, atau impor massal dari file Excel.</p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1"
                data-bs-toggle="modal" data-bs-target="#import-modal">
                <i class="bi bi-upload"></i>Import
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1"
                id="btn-export">
                <i class="bi bi-download"></i>Export
            </button>
            <button type="button" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1"
                id="btn-add-extension">
                <i class="bi bi-plus-lg"></i>Tambah Ekstensi
            </button>
        </div>
    </div>

    <div class="toolbar mb-3" id="extensions-toolbar">
        <div class="row g-2">
            <div class="col-12 col-lg-4">
                <label class="visually-hidden" for="extension-search">Cari ekstensi</label>
                <div class="search-wrap">
                    <i class="bi bi-search"></i>
                    <input type="search" class="form-control" id="extension-search" data-param="search"
                        placeholder="Cari nomor, nama, departemen…" autocomplete="off" value="{{ request('search') }}">
                    <button type="button" class="btn-clear-search" id="btn-clear-search" aria-label="Bersihkan pencarian">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    <span class="spinner-border spinner-border-sm search-spinner" role="status" aria-hidden="true"></span>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-2">
                    @include('partials.searchable-select', [
                        'id' => 'filter-department',
                        'param' => 'department',
                        'label' => 'Departemen',
                        'placeholder' => 'Semua Departemen',
                        'items' => $departments,
                        'selected' => request('department'),
                    ])
                </div>

                <div class="col-12 col-sm-6 col-lg-2">
                    @include('partials.searchable-select', [
                        'id' => 'filter-floor',
                        'param' => 'floor',
                        'label' => 'Lantai',
                        'placeholder' => 'Semua Lantai',
                        'items' => $floors,
                        'selected' => request('floor'),
                    ])
                </div>

                <div class="col-12 col-sm-6 col-lg-2">
                    @include('partials.searchable-select', [
                        'id' => 'filter-branch',
                        'param' => 'branch',
                        'label' => 'Cabang',
                        'placeholder' => 'Semua Cabang',
                        'items' => $branches,
                        'selected' => request('branch'),
                    ])
                </div>

            <div class="col-12 col-sm-6 col-lg-2 d-flex">
                <button type="button" class="btn btn-outline-secondary w-100 btn-sm text-nowrap"
                    id="btn-reset-filters">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                </button>
            </div>
        </div>
    </div>

    <div id="extensions-region" class="list-region">
        @include('admin.extensions.partials.table')
    </div>

    {{-- Modal form ekstensi --}}
    <div class="modal fade" id="extension-modal" tabindex="-1" aria-labelledby="extension-modal-title"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="extension-form" novalidate>
                    <div class="modal-header">
                        <h2 class="modal-title" id="extension-modal-title">Tambah Ekstensi</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id">

                        <div class="mb-3">
                            <label class="form-label" for="ext-number">Nomor Ekstensi</label>
                            <input type="text" class="form-control" id="ext-number" name="number" required
                                maxlength="32" placeholder="cth: 1010" autocomplete="off">
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="ext-name">Nama</label>
                            <input type="text" class="form-control" id="ext-name" name="name" required
                                maxlength="255" placeholder="Nama lengkap pengguna ekstensi" autocomplete="off">
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="ext-department">Departemen</label>
                            <div data-searchable-select-wrap>
                                @include('partials.searchable-select', [
                                    'id' => 'ext-department',
                                    'param' => 'department',
                                    'label' => 'Departemen',
                                    'placeholder' => '— Pilih (opsional) —',
                                    'items' => $departments,
                                    'selected' => '',
                                    'name' => 'department_id',
                                ])
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label" for="ext-floor">Lantai</label>
                                <select class="form-select" id="ext-floor" name="floor_id">
                                    <option value="">— Pilih —</option>
                                    @foreach ($floors as $floor)
                                        <option value="{{ $floor->id }}">{{ $floor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label" for="ext-branch">Cabang</label>
                                <select class="form-select" id="ext-branch" name="branch_id">
                                    <option value="">— Pilih —</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="ext-status">Status</label>
                            <select class="form-select" id="ext-status" name="status">
                                <option value="1" selected>Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                            <div class="form-hint mt-1">Ekstensi hanya tampil di direktori public jika statusnya "Aktif".</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-check-lg me-1"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal import --}}
    <div class="modal fade" id="import-modal" tabindex="-1" aria-labelledby="import-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="import-form" action="{{ route('admin.extensions.import') }}" method="POST" enctype="multipart/form-data" novalidate>
                    <div class="modal-header">
                        <h2 class="modal-title" id="import-modal-title">Import dari Excel</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-border py-2 px-3 mb-3"
                            style="font-size:.83rem;background:var(--app-primary-soft, #f0fdfa);border:1px solid #ccfbf1;color:var(--bs-primary-darker);border-radius:var(--app-radius)">
                            <div class="d-flex gap-2">
                                <i class="bi bi-info-circle-fill mt-1"></i>
                                <div class="flex-grow-1">
                                    <strong>Ketentuan import:</strong>
                                    <ul class="mb-0 ps-3 mt-1">
                                        <li>Departemen, Lantai, dan Cabang bersifat opsional.</li>
                                        <li>Nama yang belum terdaftar akan dibuat otomatis.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="import-file">File Excel (.xlsx atau .xls)</label>
                            <input type="file" class="form-control" id="import-file" name="file"
                                accept=".xlsx,.xls" required>
                        </div>

                        <div class="d-flex align-items-center gap-2" style="font-size:.83rem">
                            <i class="bi bi-file-earmark-excel text-teal"></i>
                            <span class="text-muted-2">Belum punya format? </span>
                            <a href="{{ route('admin.extensions.import-template') }}" class="fw-medium">
                                Unduh template Excel
                            </a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-upload me-1"></i>Mulai Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal konfirmasi hapus --}}
    <div class="modal fade" id="delete-modal" tabindex="-1" aria-labelledby="delete-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body p-4 text-center">
                    <div class="empty-icon mb-3"
                        style="width:52px;height:52px;border-radius:16px;background:#fef2f2;border:1px solid #fecaca;color:#dc2626;display:inline-flex;align-items:center;justify-content:center;font-size:1.35rem">
                        <i class="bi bi-trash3"></i>
                    </div>
                    <h3 class="mb-1" style="font-size:1rem;font-weight:650">Hapus data ini?</h3>
                    <p class="mb-0" style="font-size:.85rem;color:var(--app-text-muted)" id="delete-modal-text">
                        Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
                <div class="modal-footer justify-content-center border-0 pt-0 pb-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger btn-sm" id="btn-confirm-delete">
                        <i class="bi bi-trash3 me-1"></i>Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const region = document.querySelector('#extensions-region');
        const toolbar = document.querySelector('#extensions-toolbar');
        const searchInput = document.querySelector('#extension-search');
        const clearSearchBtn = document.querySelector('#btn-clear-search');
        const searchWrap = searchInput.closest('.search-wrap');
        const extensionModalEl = document.querySelector('#extension-modal');
        const extensionModal = new bootstrap.Modal(extensionModalEl);
        const extensionForm = document.querySelector('#extension-form');
        const deleteModal = new bootstrap.Modal(document.querySelector('#delete-modal'));

        let deleteTarget = null;
        let deleting = false;

        async function load(page = 1) {
            const params = App.buildQuery(toolbar);
            params.set('page', page);
            await App.loadRegion('/admin/extensions/data?' + params.toString(), '#extensions-region');
            searchWrap.classList.remove('is-loading');
        }

        function syncSearchState() {
            const hasValue = searchInput.value.trim() !== '';
            searchWrap.classList.toggle('has-value', hasValue);
            searchWrap.classList.remove('is-loading');
        }

        searchInput.addEventListener('input', App.debounce(() => {
            searchWrap.classList.add('is-loading');
            load();
        }, 300));

        searchInput.addEventListener('keyup', syncSearchState);

        clearSearchBtn.addEventListener('click', () => {
            searchInput.value = '';
            syncSearchState();
            load();
        });

        toolbar.querySelectorAll('select[data-param]').forEach((select) => {
            select.addEventListener('change', () => load());
        });

        document.querySelector('#btn-reset-filters').addEventListener('click', () => {
            toolbar.querySelectorAll('select[data-param]').forEach((select) => (select.value = ''));
            App.searchableSelect.syncAll(toolbar);
            searchInput.value = '';
            syncSearchState();
            load();
        });

        document.querySelector('#btn-export').addEventListener('click', () => {
            const params = App.buildQuery(toolbar);
            window.location.href = '/admin/extensions/export?' + params.toString();
        });

        /* ---------- Form ekstensi (tambah/ubah) ---------- */

        document.querySelector('#btn-add-extension').addEventListener('click', () => {
            resetExtensionForm();
        });

        region.addEventListener('click', (event) => {
            const link = event.target.closest('.pagination a');
            if (link) {
                event.preventDefault();
                const page = new URL(link.href).searchParams.get('page') || 1;
                load(page);
                window.scrollTo({ top: toolbar.getBoundingClientRect().top + window.scrollY - 16, behavior: 'smooth' });
                return;
            }

            const editBtn = event.target.closest('[data-edit-extension]');
            if (editBtn) {
                resetExtensionForm();
                const data = {
                    id: editBtn.dataset.id,
                    number: editBtn.dataset.number,
                    name: editBtn.dataset.name,
                    department: editBtn.dataset.department || '',
                    floor: editBtn.dataset.floor || '',
                    branch: editBtn.dataset.branch || '',
                    status: editBtn.dataset.status || '1',
                };
                extensionForm.querySelector('[name="id"]').value = data.id;
                extensionForm.querySelector('[name="number"]').value = data.number;
                extensionForm.querySelector('[name="name"]').value = data.name;
                extensionForm.querySelector('[name="department_id"]').value = data.department;
                extensionForm.querySelector('[name="floor_id"]').value = data.floor;
                extensionForm.querySelector('[name="branch_id"]').value = data.branch;
                extensionForm.querySelector('[name="status"]').value = data.status;
                App.searchableSelect.syncAll(extensionForm);
                extensionForm.action = '/admin/extensions/' + data.id;
                extensionForm.dataset.method = 'PUT';
                document.querySelector('#extension-modal-title').textContent = 'Ubah Ekstensi';
                extensionModal.show();
                return;
            }

            const deleteBtn = event.target.closest('[data-delete-extension]');
            if (deleteBtn) {
                deleteTarget = deleteBtn.dataset.id;
                document.querySelector('#delete-modal-text').textContent =
                    `Nomor ${deleteBtn.dataset.number} (${deleteBtn.dataset.name}) akan dihapus permanen.`;
                document.querySelector('#btn-confirm-delete').innerHTML =
                    '<i class="bi bi-trash3 me-1"></i>Ya, Hapus';
                deleting = false;
                deleteModal.show();
            }
        });

        function resetExtensionForm() {
            App.clearFormErrors(extensionForm);
            extensionForm.reset();
            App.searchableSelect.syncAll(extensionForm);
            extensionForm.action = '/admin/extensions';
            extensionForm.dataset.method = 'POST';
            document.querySelector('#extension-modal-title').textContent = 'Tambah Ekstensi';
            extensionModal.show();
        }

        extensionForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const form = event.currentTarget;

            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = form.dataset.method || 'POST';
            form.appendChild(methodField);

            await App.submitForm(form, {
                onSuccess: () => {
                    extensionModal.hide();
                    load();
                },
            });

            methodField.remove();
        });

        /* ---------- Import ---------- */

        const importForm = document.querySelector('#import-form');
        const importModal = new bootstrap.Modal(document.querySelector('#import-modal'));

        importForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const form = event.currentTarget;

            const result = await App.submitForm(form, {
                onSuccess: () => {
                    form.reset();
                    importModal.hide();
                    load();
                },
            });

            if (result && result.error_rows > 0) {
                App.toast(`${result.message} (${result.error_rows} baris gagal)`, 'error');
            }
        });

        /* ---------- Hapus ---------- */

        document.querySelector('#btn-confirm-delete').addEventListener('click', async () => {
            if (!deleteTarget || deleting) {
                return;
            }

            deleting = true;
            const btn = document.querySelector('#btn-confirm-delete');
            const original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menghapus...';

            try {
                const response = await fetch('/admin/extensions/' + deleteTarget, {
                    method: 'DELETE',
                    headers: App.headers(),
                });

                const data = await response.json();

                if (!response.ok) {
                    App.toast(data.message || 'Gagal menghapus data.', 'error');
                    return;
                }

                App.toast(data.message);
                deleteModal.hide();
                load();
            } catch (error) {
                App.toast('Terjadi kesalahan jaringan.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = original;
                deleteTarget = null;
            }
        });

        syncSearchState();
        App.searchableSelect.init(toolbar);
        App.searchableSelect.init(extensionForm);
    </script>
@endpush
