@extends('adminlte::auth.auth-page', ['auth_type' => 'register'])

@section('auth_header', 'Új fiók regisztrálása')

@section('auth_body')
    <form action="{{ route('register') }}" method="post">
        @csrf

        {{-- Név mező --}}
        <div class="input-group mb-3">
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}" placeholder="Teljes név" required autofocus>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-user"></span></div>
            </div>
            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        {{-- Email mező --}}
        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" placeholder="Email cím" required>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-envelope"></span></div>
            </div>
            @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        {{-- Jelszó mező --}}
        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                   placeholder="Jelszó" required>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-lock"></span></div>
            </div>
            @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        {{-- Jelszó megerősítése --}}
        <div class="input-group mb-3">
            <input type="password" name="password_confirmation" class="form-control"
                   placeholder="Jelszó újra" required>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-lock"></span></div>
            </div>
        </div>

        <button type="submit" class="btn btn-block btn-flat btn-primary">
            <span class="fas fa-user-plus"></span> Regisztráció
        </button>
    </form>
@stop

@section('auth_footer')
    <p class="my-0">
        <a href="{{ route('login') }}">Már van fiókom, bejelentkezem</a>
    </p>
@stop
