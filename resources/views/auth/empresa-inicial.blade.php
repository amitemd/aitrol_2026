@extends('layouts.auth')

@section('title', 'Registrar empresa')

@section('content')
    <header class="access-heading">
        <h1 id="access-title">Registrar empresa</h1>
        <p>Completa estos datos para abrir el espacio de trabajo de tu empresa.</p>
    </header>

    <form method="POST" action="{{ route('empresa.inicial') }}" class="access-form" data-empresa-inicial>
        @csrf
        @foreach ([
            'ruc' => ['RUC', 'text', 13, true],
            'razon_social' => ['Razón social', 'text', 160, true],
            'nombre_comercial' => ['Nombre comercial', 'text', 160, false],
            'email' => ['Correo de la empresa', 'email', 254, false],
            'telefono' => ['Teléfono', 'tel', 30, false],
            'direccion' => ['Dirección', 'text', 255, false],
        ] as $campo => [$etiqueta, $tipo, $limite, $obligatorio])
            <div class="access-field">
                <label for="{{ $campo }}">{{ $etiqueta }}</label>
                <input id="{{ $campo }}" name="{{ $campo }}" type="{{ $tipo }}" value="{{ old($campo) }}"
                    maxlength="{{ $limite }}" @required($obligatorio)
                    @if ($campo === 'ruc') inputmode="numeric" pattern="[0-9]{13}" @endif
                    @error($campo) aria-invalid="true" aria-describedby="{{ $campo }}-error" @enderror>
                @error($campo) <p id="{{ $campo }}-error" class="access-error">{{ $message }}</p> @enderror
            </div>
        @endforeach
        <button class="btn btn-primary access-button" type="submit">Registrar empresa</button>
    </form>
    <form method="POST" action="{{ route('logout') }}" class="access-company-logout"
        data-accion-confirmar="¿Deseas salir del sistema?" data-confirmar-titulo="Cerrar sesión"
        data-confirmar-boton="Sí, cerrar sesión" data-confirmar-cancelar="Seguir trabajando">
        @csrf
        <button type="submit" class="btn access-logout-button">Cerrar sesión</button>
    </form>
@endsection
