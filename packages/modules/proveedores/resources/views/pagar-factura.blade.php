@extends('layouts.adminlte')

@section('title', 'Registrar pago a proveedor')

@section('content')
    @include('partials.listado-cabecera', ['titulo' => 'Registrar pago a proveedor', 'subtitulo' => 'Proveedores', 'icono' => 'bi-cash-stack'])
    <div class="app-content">
        <div class="container-fluid">
            <form method="POST" action="{{ route('proveedores.pagos.store', $factura->id) }}" class="card" data-ajax-form>
                @csrf
                <input type="hidden" name="clave_operacion" value="{{ old('clave_operacion', (string) Str::uuid()) }}">
                @include('comercial::errores')
                <div class="card-body">
                    <p>{{ $factura->tercero }} · {{ $factura->numero }}</p>
                    <p>Saldo por pagar <strong>USD {{ number_format(max(0, $factura->saldo) / 100, 2) }}</strong></p>
                    @if ($factura->saldo > 0)
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="pago-cuenta" class="form-label">Caja o banco de origen</label>
                                <select id="pago-cuenta" name="id_cuenta" class="form-select"
                                    data-select2-url="{{ route('caja-bancos.movimientos.opciones', ['tipo' => 'todas', 'moneda' => 'USD']) }}" data-select2-min="0" required>
                                    <option value="">Seleccionar cuenta</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="pago-fecha" class="form-label">Fecha</label>
                                <input id="pago-fecha" name="fecha" type="date" class="form-control" min="{{ $factura->fecha }}"
                                    max="{{ now('America/Guayaquil')->toDateString() }}" value="{{ old('fecha', now('America/Guayaquil')->toDateString()) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="pago-importe" class="form-label">Importe (USD)</label>
                                <input id="pago-importe" name="importe" type="number" min="0.01" step="0.01" class="form-control"
                                    max="{{ number_format($factura->saldo / 100, 2, '.', '') }}" value="{{ old('importe') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="pago-referencia" class="form-label">Referencia</label>
                                <input id="pago-referencia" name="referencia" class="form-control" maxlength="120" value="{{ old('referencia') }}" required>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a class="btn btn-outline-secondary" href="{{ route('proveedores.reportes.cuentas-por-pagar') }}" data-ajax-close>Volver a cartera</a>
                    @if ($factura->saldo > 0)
                        <button class="btn btn-primary">Confirmar pago</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection
