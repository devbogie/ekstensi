@extends('layouts.app')

@section('title', 'Masuk Admin')

@section('content')
    <div class="auth-wrap">
        <div class="auth-card">
            <div class="d-flex flex-column align-items-center text-center mb-4">
                <span class="brand-mark mb-3"><i class="bi bi-journal-bookmark-fill"></i></span>
                <h1 class="mb-1">Masuk ke Panel Admin</h1>
                <p class="auth-sub mb-0">Akses khusus administrator {{ config('app.name') }}</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger py-2 px-3" style="font-size:.85rem" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-1"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.attempt') }}" novalidate>
                @csrf

                <div class="mb-3">
                    <label class="form-label" for="username">Username</label>
                    <input type="text" class="form-control @error('username') is-invalid @enderror" id="username"
                        name="username" value="{{ old('username') }}" autocomplete="username" required autofocus>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label" for="password">Kata Sandi</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                        name="password" autocomplete="current-password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
                        <label class="form-check-label" style="font-size:.85rem" for="remember">Ingat saya</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-box-arrow-in-right"></i>Masuk
                </button>
            </form>
        </div>
    </div>
@endsection
