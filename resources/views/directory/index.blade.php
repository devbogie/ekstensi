@extends('layouts.app')

@section('title', 'Direktori Ekstensi')

@section('content')
    <div class="container py-4 py-lg-5">
        <div class="page-heading mb-4">
            <h1>Direktori Ekstensi</h1>
            <p class="lead mb-0">Cari nomor ekstensi rekan kerja Anda. Hasil diperbarui otomatis saat Anda mengetik.</p>
        </div>

        <div class="toolbar mb-4" id="directory-toolbar">
            <div class="row g-2">
                <div class="col-12 col-lg-4">
                    <label class="visually-hidden" for="directory-search">Cari ekstensi</label>
                    <div class="search-wrap">
                        <i class="bi bi-search"></i>
                        <input type="search" class="form-control" id="directory-search" data-param="search"
                            placeholder="Cari nomor, nama, departemen…" autocomplete="off"
                            value="{{ request('search') }}">
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
                    <button type="button" class="btn btn-outline-secondary w-100" id="btn-reset-filters">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                    </button>
                </div>
            </div>
        </div>

        <div id="directory-region" class="list-region">
            @include('directory.partials.table')
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const region = document.querySelector('#directory-region');
        const toolbar = document.querySelector('#directory-toolbar');
        const searchInput = document.querySelector('#directory-search');
        const clearSearchBtn = document.querySelector('#btn-clear-search');
        const searchWrap = searchInput.closest('.search-wrap');

        async function load(page = 1) {
            const params = App.buildQuery(toolbar);
            params.set('page', page);
            await App.loadRegion('/direktori/data?' + params.toString(), '#directory-region');
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

        region.addEventListener('click', (event) => {
            const link = event.target.closest('.pagination a');
            if (link) {
                event.preventDefault();
                const page = new URL(link.href).searchParams.get('page') || 1;
                load(page);
                window.scrollTo({ top: toolbar.getBoundingClientRect().top + window.scrollY - 16, behavior: 'smooth' });
                return;
            }

            if (event.target.closest('#btn-empty-reset')) {
                toolbar.querySelectorAll('select[data-param]').forEach((select) => (select.value = ''));
                App.searchableSelect.syncAll(toolbar);
                searchInput.value = '';
                syncSearchState();
                load();
                return;
            }

            const removeBtn = event.target.closest('[data-remove-filter]');
            if (removeBtn) {
                const key = removeBtn.dataset.removeFilter;
                if (key === 'search') {
                    searchInput.value = '';
                    syncSearchState();
                } else {
                    const select = toolbar.querySelector(`select[data-param="${key}"]`);
                    if (select) {
                        select.value = '';
                    }
                }
                App.searchableSelect.syncAll(toolbar);
                load();
            }
        });

        syncSearchState();
        App.searchableSelect.init(toolbar);
    </script>
@endpush
