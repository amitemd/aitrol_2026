@extends('layouts.adminlte')

@section('title', 'Política de aprobación de compras')
@section('content')
    @include('partials.listado-cabecera', ['titulo' => 'Política de aprobación', 'subtitulo' => 'Compras', 'icono' => 'bi-sliders'])
    <div class="app-content">
        <div class="container-fluid">
            @if (session('status'))
                <p class="alert alert-success" role="status">{{ session('status') }}</p>
            @endif
            @include('comercial::errores')
            <form method="POST" action="{{ route('compras.aprobaciones.politica.update') }}" class="card">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <p>La política se aplica a las órdenes nuevas de todas las sucursales de la empresa. Asigna el permiso de aprobación a un usuario distinto del que registra las órdenes.</p>
                    <input type="hidden" name="activa" value="0">
                    <div class="form-check mb-3">
                        <input id="politica-compra-activa" class="form-check-input" type="checkbox" name="activa" value="1" @checked(old('activa', $politica['activa']))>
                        <label for="politica-compra-activa" class="form-check-label">Exigir aprobación según el importe</label>
                    </div>
                    <label for="politica-compra-monto" class="form-label">Total mínimo de la orden (USD)</label>
                    <input id="politica-compra-monto" class="form-control w-auto" type="number" min="0" step="0.01" name="monto_minimo"
                        value="{{ old('monto_minimo', $politica['monto_minimo']) }}" aria-describedby="politica-compra-ayuda" required>
                    <div id="politica-compra-ayuda" class="form-text">El total incluye IVA. Si activas la política con USD 0, se revisarán todas las órdenes nuevas.</div>
                </div>
                <div class="card-footer text-end">
                    <a class="btn btn-outline-secondary" href="{{ route('compras.aprobaciones.index') }}">Volver</a>
                    <button class="btn btn-primary" type="submit">Guardar política</button>
                </div>
            </form>
        </div>
    </div>
@endsection
