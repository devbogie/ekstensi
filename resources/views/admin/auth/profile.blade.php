@extends('layouts.admin')

@section('title', 'Ubah Kata Sandi')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <div class="page-heading">
            <h1 class="mb-1">Ubah Kata Sandi</h1>
            <p class="lead mb-0">Perbarui kata sandi akun administrator Anda.</p>
        </div>
    </div>

    <div class="card" style="max-width:560px">
        <div class="card-body p-4">
            @if (session('success'))
                <div class="alert alert-success py-2 px-3 d-flex align-items-center gap-2" style="font-size:.85rem"
                    role="alert" data-flash-success="{{ session('success') }}">
                    <i class="bi bi-check-circle-fill"></i>{{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.profile.password') }}" id="password-form">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label" for="current_password">Kata Sandi Saat Ini</label>
                    <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                        id="current_password" name="current_password" autocomplete="current-password" required>
                    @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password">Kata Sandi Baru</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                        name="password" autocomplete="new-password" required>
                    <div class="form-hint mt-1">Minimal 8 karakter.</div>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label" for="password_confirmation">Ulangi Kata Sandi Baru</label>
                    <input type="password" class="form-control" id="password_confirmation"
                        name="password_confirmation" autocomplete="new-password" required>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Simpan Kata Sandi
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelector('#password-form').addEventListener('submit', async (event) => {
            event.preventDefault();

            const form = event.currentTarget;

            const data = await App.submitForm(form, {
                method: 'PUT',
                json: true,
                successMessage: 'Kata sandi berhasil diperbarui.',
                onSuccess: () => form.reset(),
            });

            if (data) {
                form.querySelectorAll('input').forEach((input) => (input.value = ''));
            }
        });
    </script>
@endpush
