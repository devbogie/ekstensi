@extends('layouts.admin')

@php
    $resource = request()->segment(2);
@endphp

@section('title', $title)

@section('content')
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
        <div class="page-heading">
            <h1 class="mb-1">{{ $title }}</h1>
            <p class="lead mb-0">Kelola data {{ mb_strtolower($title) }} yang dipakai pada ekstensi.</p>
        </div>

        <button type="button" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1 btn-add-ref flex-shrink-0"
            id="btn-add-item">
            <i class="bi bi-plus-lg"></i><span>Tambah {{ $singular }}</span>
        </button>
    </div>

    <div class="toolbar mb-3" id="reference-toolbar">
        <div class="row g-2">
            <div class="col-12">
                <label class="visually-hidden" for="reference-search">Cari {{ mb_strtolower($title) }}</label>
                <div class="search-wrap">
                    <i class="bi bi-search"></i>
                    <input type="search" class="form-control" id="reference-search" data-param="search"
                        placeholder="Cari {{ mb_strtolower($title) }}…" autocomplete="off"
                        value="{{ request('search') }}">
                    <button type="button" class="btn-clear-search" id="btn-clear-search" aria-label="Bersihkan pencarian">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    <span class="spinner-border spinner-border-sm search-spinner" role="status" aria-hidden="true"></span>
                </div>
            </div>
        </div>
    </div>

    <div id="reference-region" class="list-region">
        @include('admin.reference.partials.table')
    </div>

    {{-- Modal form --}}
    <div class="modal fade" id="item-modal" tabindex="-1" aria-labelledby="item-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="item-form" novalidate>
                    <div class="modal-header">
                        <h2 class="modal-title mb-0" id="item-modal-title">Tambah {{ $singular }}</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id">

                        <div class="mb-2">
                            <label class="form-label" for="item-name">Nama {{ $singular }}</label>
                            <input type="text" class="form-control" id="item-name" name="name" required
                                maxlength="255" placeholder="cth: {{ $singular == 'Lantai' ? 'Lantai 1' : ($singular == 'Departemen' ? 'Keuangan' : 'Cabang Utama') }}" autocomplete="off">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm px-3">
                            <i class="bi bi-check-lg me-1"></i>Simpan
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
                    <h3 class="mb-1" style="font-size:1rem;font-weight:650">Hapus {{ mb_strtolower($singular) }} ini?</h3>
                    <p class="mb-0" style="font-size:.85rem;color:var(--app-text-muted)" id="delete-modal-text">
                        Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
                <div class="modal-footer justify-content-center border-0 pt-0 pb-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger btn-sm px-3" id="btn-confirm-delete">
                        <i class="bi bi-trash3 me-1"></i>Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const resource = '{{ $resource }}';
        const singular = '{{ $singular }}';
        const region = document.querySelector('#reference-region');
        const toolbar = document.querySelector('#reference-toolbar');
        const searchInput = document.querySelector('#reference-search');
        const clearSearchBtn = document.querySelector('#btn-clear-search');
        const searchWrap = searchInput.closest('.search-wrap');
        const itemModalEl = document.querySelector('#item-modal');
        const itemModal = new bootstrap.Modal(itemModalEl);
        const itemForm = document.querySelector('#item-form');
        const deleteModal = new bootstrap.Modal(document.querySelector('#delete-modal'));

        let deleteTarget = null;
        let deleting = false;

        async function load(page = 1) {
            const params = App.buildQuery(toolbar);
            params.set('page', page);
            await App.loadRegion('/admin/' + resource + '/data?' + params.toString(), '#reference-region');
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

        document.querySelector('#btn-add-item').addEventListener('click', resetItemForm);

        region.addEventListener('click', (event) => {
            const link = event.target.closest('.pagination a');
            if (link) {
                event.preventDefault();
                const page = new URL(link.href).searchParams.get('page') || 1;
                load(page);
                window.scrollTo({ top: toolbar.getBoundingClientRect().top + window.scrollY - 16, behavior: 'smooth' });
                return;
            }

            const editBtn = event.target.closest('[data-edit-item]');
            if (editBtn) {
                resetItemForm();
                itemForm.querySelector('[name="id"]').value = editBtn.dataset.id;
                itemForm.querySelector('[name="name"]').value = editBtn.dataset.name;
                itemForm.action = '/admin/' + resource + '/' + editBtn.dataset.id;
                itemForm.dataset.method = 'PUT';
                document.querySelector('#item-modal-title').textContent = 'Ubah ' + singular;
                itemModal.show();
                return;
            }

            const deleteBtn = event.target.closest('[data-delete-item]');
            if (deleteBtn) {
                deleteTarget = deleteBtn.dataset.id;
                document.querySelector('#delete-modal-text').textContent =
                    `"${deleteBtn.dataset.name}" akan dihapus permanen.`;
                document.querySelector('#btn-confirm-delete').innerHTML =
                    '<i class="bi bi-trash3 me-1"></i>Ya, Hapus';
                deleting = false;
                deleteModal.show();
            }
        });

        function resetItemForm() {
            App.clearFormErrors(itemForm);
            itemForm.reset();
            itemForm.action = '/admin/' + resource;
            itemForm.dataset.method = 'POST';
            document.querySelector('#item-modal-title').textContent = 'Tambah ' + singular;
            itemModal.show();
        }

        itemForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const form = event.currentTarget;

            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = form.dataset.method || 'POST';
            form.appendChild(methodField);

            await App.submitForm(form, {
                onSuccess: () => {
                    itemModal.hide();
                    load();
                },
            });

            methodField.remove();
        });

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
                const response = await fetch('/admin/' + resource + '/' + deleteTarget, {
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
    </script>
@endpush
