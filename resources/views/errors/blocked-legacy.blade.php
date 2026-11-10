@extends('auth.layouts.v3')

@section('title', $title)

@section('content')
    <div class="av3-icon av3-icon--warning" aria-hidden="true">!</div>
    <div class="av3-eyebrow">ACCESO SEGURO</div>
    <h1 id="av3-auth-title">{{ $heading }}</h1>
    <p class="av3-lead">{{ $message }}</p>

    <div class="av3-alert av3-alert--info" role="status">
        Tus datos no fueron enviados ni modificados. Puedes continuar usando el acceso oficial.
    </div>

    <a class="av3-submit av3-submit--link" href="{{ route('login') }}">Ir al inicio de sesión</a>
    <a class="av3-back" href="{{ route('password.request') }}">¿Necesitas recuperar tu contraseña?</a>
    <div class="av3-security"><span class="av3-lock" aria-hidden="true">●</span><span>Aitrol Hospitalario bloquea enlaces antiguos que podrían omitir los controles actuales de autenticación.</span></div>
@endsection
