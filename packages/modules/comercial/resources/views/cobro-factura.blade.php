@extends('layouts.adminlte')

@section('title', 'Registrar cobro')
@section('content')
    <div class="app-content py-3">
        <div class="container-fluid">
            @if ($saldo > 0 && $factura->id_asiento)
                <form class="card" method="POST" action="{{ route('ventas.documentos.cobro.store', $factura->id) }}" data-ajax-form>
                    @csrf
                    <input type="hidden" name="clave_operacion" value="{{ old('clave_operacion', (string) Str::uuid()) }}">
                    <h1 class="card-header fs-5">Registrar cobro</h1>
                    @include('comercial::errores')
                    <div class="card-body">
                        <p>{{ $factura->numero }} · {{ $factura->tercero }}</p>
                        <p class="fw-semibold">Saldo por cobrar · USD {{ \Aitrol\Inventarios\Domain\CantidadInventario::decimal($saldo, 2) }}</p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="cobro-cuenta" class="form-label">Caja o banco que recibió el pago</label>
                                <select id="cobro-cuenta" name="id_cuenta" class="form-select"
                                    data-select2-url="{{ route('caja-bancos.movimientos.opciones', 'todas') }}" data-select2-min="0" required>
                                    <option value="">Seleccionar cuenta</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="cobro-fecha" class="form-label">Fecha del pago</label>
                                <input id="cobro-fecha" name="fecha" type="date" class="form-control"
                                    min="{{ $factura->fecha }}" max="{{ now('America/Guayaquil')->toDateString() }}"
                                    value="{{ old('fecha', now('America/Guayaquil')->toDateString()) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="cobro-importe" class="form-label">Importe recibido</label>
                                <input id="cobro-importe" name="importe" type="number" min="0.01" step="0.01" class="form-control"
                                    max="{{ \Aitrol\Inventarios\Domain\CantidadInventario::decimal($saldo, 2) }}" value="{{ old('importe') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="cobro-referencia" class="form-label">Referencia o número de recibo</label>
                                <input id="cobro-referencia" name="referencia" class="form-control" maxlength="120" value="{{ old('referencia') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end gap-2">
                        <a class="btn btn-outline-secondary" href="{{ route('ventas.documentos.index') }}" data-ajax-close>Cancelar</a>
                        <button class="btn btn-primary" type="submit">Confirmar cobro</button>
                    </div>
                </form>
            @else
                <section class="card" data-ajax-form>
                    <h1 class="card-header fs-5">Registrar cobro</h1>
                    <p class="card-body mb-0">{{ $saldo <= 0 ? 'La factura no tiene saldo por cobrar.' : 'Genera primero el asiento contable de la factura.' }}</p>
                    <div class="card-footer text-end">
                        <a class="btn btn-outline-secondary" href="{{ route('ventas.documentos.index') }}" data-ajax-close>Cerrar</a>
                    </div>
                </section>
            @endif
        </div>
    </div>
@endsection
