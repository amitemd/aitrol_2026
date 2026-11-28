@extends('layouts.auth')

@section('title', 'Sucursal de trabajo')

@section('content')
    @php($nombreEmpresa = $empresa->nombre_comercial ?: $empresa->razon_social)
    <p class="text-body-secondary mb-2">{{ $nombreEmpresa }}</p>
    @if ($sucursales->isEmpty())
        <h1 id="access-title">Configura tu primera sucursal</h1>
        @can('administrar-configuracion')
            <p class="text-body-secondary mb-4">Registra la oficina o el establecimiento donde operarás.</p>
            <form method="POST" action="{{ route('configuracion.sucursales.store') }}">
                @csrf
                <label class="form-label" for="inicio-sucursal-codigo">{{ __('Código') }}</label>
                <input id="inicio-sucursal-codigo" class="form-control mb-3" name="codigo" maxlength="30" value="{{ old('codigo', 'PRINCIPAL') }}" required>
                <label class="form-label" for="inicio-sucursal-nombre">{{ __('Nombre') }}</label>
                <input id="inicio-sucursal-nombre" class="form-control mb-3" name="nombre" maxlength="120" value="{{ old('nombre') }}" required>
                <label class="form-label" for="inicio-sucursal-direccion">{{ __('Dirección') }}</label>
                <input id="inicio-sucursal-direccion" class="form-control mb-3" name="direccion" maxlength="255" value="{{ old('direccion') }}">
                <button type="submit" class="btn btn-primary w-100">Crear sucursal y entrar</button>
            </form>
        @else
            <p class="text-body-secondary">Pide al administrador de tu empresa que registre la primera sucursal.</p>
        @endcan
    @else
        <h1 id="access-title">Elige tu sucursal</h1>
        <p class="text-body-secondary mb-4">Selecciona el establecimiento y el año en el que vas a trabajar.</p>
        <form method="POST" action="{{ route('configuracion.contexto.update') }}">
            @csrf
            <label class="form-label" for="inicio-contexto-sucursal">{{ __('Sucursal') }}</label>
            <select id="inicio-contexto-sucursal" class="form-select mb-3" name="id_sucursal" required>
                <option value="">Selecciona una sucursal</option>
                @foreach ($sucursales as $sucursal)
                    <option value="{{ $sucursal->id }}" @selected((int) old('id_sucursal') === $sucursal->id)>
                        {{ $sucursal->nombre }} · {{ $sucursal->codigo }}
                    </option>
                @endforeach
            </select>
            <label class="form-label" for="inicio-contexto-periodo">{{ __('Año del período') }}</label>
            <input id="inicio-contexto-periodo" class="form-control mb-3" type="number" name="periodo" min="1900"
                max="{{ $ultimoPeriodo }}" value="{{ old('periodo', $periodo) }}" required>
            <button type="submit" class="btn btn-primary w-100">Ingresar al sistema</button>
        </form>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger mt-3" role="alert">{{ $errors->first() }}</div>
    @endif
    @if (request()->query('desde') === 'importaciones')
        @can('administrar-configuracion')
            <a class="btn btn-outline-secondary w-100 mt-3" href="{{ route('configuracion.importaciones.index') }}">Volver a Importaciones</a>
        @endcan
    @endif
    <form method="POST" class="mt-3 text-center" action="{{ route('logout') }}"
        data-accion-confirmar="¿Deseas salir del sistema?" data-confirmar-titulo="{{ __('Cerrar sesión') }}"
        data-confirmar-boton="Sí, cerrar sesión" data-confirmar-cancelar="Seguir trabajando">
        @csrf
        <button type="submit" class="btn btn-link">{{ __('Cerrar sesión') }}</button>
    </form>
@endsection
