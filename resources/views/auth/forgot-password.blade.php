{{--
    Elfelejtett jelszó (visszaállítási link igénylése) nézet.
    A felhasználó az e-mail címe megadásával kérhet jelszó-visszaállítási linket.
--}}
@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('auth_header', 'Jelszó emlékeztető')

@section('auth_body')
    {{-- Sikeres küldés visszajelzés --}}
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <p class="login-box-msg">Add meg az e-mail címed, és küldünk egy linket a visszaállításhoz.</p>

    {{-- Link igénylő űrlap --}}
    <form action="{{ route('password.email') }}" method="post">
        @csrf
        {{-- E-mail cím mező ikonnal --}}
        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" placeholder="Email" required autofocus>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-envelope"></span></div>
            </div>
            @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        {{-- Küldés gomb --}}
        <button type="submit" class="btn btn-block btn-flat btn-primary">
            Link küldése
        </button>
    </form>
@stop

@section('auth_footer')
    {{-- Visszalépés a bejelentkezéshez --}}
    <p class="my-0 mt-2">
        <a href="{{ route('login') }}">Vissza a bejelentkezéshez</a>
    </p>
@stop
