@extends('layouts.adminlte')

@php
    $ruta = 'caja-bancos.'.$seccion;
    $titulo = match ($accion) {
        'editar' => 'Editar cuenta', 'cerrar' => 'Cerrar caja', 'conciliar' => 'Conciliar movimiento',
        'desconciliar' => 'Desconciliar movimiento', 'revertir' => 'Revertir operación',
        default => config('caja-bancos.secciones.'.$seccion.'.2'),
    };
    $url = $accion ? route($ruta.'.'.$accion, $registro['id']) : route($ruta.'.store');
    $esBanco = old('tipo', $registro['tipo'] ?? 'caja') === 'banco';
@endphp
@section('title', $titulo)
@section('content')
    <div class="app-content py-3"><div class="container-fluid">
        <form method="POST" action="{{ $url }}" class="card ficha-caja-bancos" enctype="multipart/form-data" data-ajax-form>
            @csrf
            @if ($accion === 'editar') @method('PUT') @endif
            <h1 class="card-header fs-5">{{ $titulo }}</h1>
            @if ($errors->any())
                <div class="alert alert-danger m-3" role="alert">
                    <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            <div class="card-body row g-3">
                @if ($seccion === 'cuentas')
                    <div class="col-sm-4">
                        <label for="cb-codigo" class="form-label">{{ __('Código') }}</label>
                        <input id="cb-codigo" name="codigo" value="{{ old('codigo', $registro['codigo'] ?? '') }}" class="form-control" maxlength="40" required autofocus>
                    </div>
                    <div class="col-sm-8">
                        <label for="cb-nombre" class="form-label">Nombre de la cuenta</label>
                        <input id="cb-nombre" name="nombre" value="{{ old('nombre', $registro['nombre'] ?? '') }}" class="form-control" maxlength="160" required>
                    </div>
                    @if (!$accion)
                        <div class="col-sm-6">
                            <label for="cb-tipo" class="form-label">Tipo de cuenta</label>
                            <select id="cb-tipo" class="form-select" name="tipo">
                                <option value="caja" @selected(!$esBanco)>Caja</option><option value="banco" @selected($esBanco)>Banco</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label for="cb-moneda" class="form-label">{{ __('Moneda') }}</label>
                            <select id="cb-moneda" class="form-select" name="moneda">
                                @foreach (config('caja-bancos.monedas') as $moneda)<option value="{{ $moneda }}" @selected(old('moneda', 'USD') === $moneda)>{{ $moneda }}</option>@endforeach
                            </select>
                        </div>
                    @else
                        <div class="col-12 text-body-secondary">{{ $esBanco ? 'Banco' : 'Caja' }} · {{ $registro['moneda'] }}</div>
                    @endif
                    @if (!$accion || $esBanco)
                        <div @class(['col-sm-8', 'cb-dato-banco' => !$accion])>
                            <label for="cb-banco" class="form-label">Institución bancaria</label>
                            <input id="cb-banco" name="banco" value="{{ old('banco', $registro['banco'] ?? '') }}" class="form-control" maxlength="120">
                        </div>
                        <div @class(['col-sm-4', 'cb-dato-banco' => !$accion])>
                            <label for="cb-digitos" class="form-label">Últimos cuatro dígitos</label>
                            <input id="cb-digitos" name="ultimos_digitos" value="{{ old('ultimos_digitos', $registro['ultimos_digitos'] ?? '') }}"
                                class="form-control" maxlength="4" inputmode="numeric" pattern="[0-9]{4}">
                        </div>
                    @endif
                @elseif ($accion === 'conciliar')
                    <p class="mb-0"><strong>{{ $registro['referencia'] }} · {{ $registro['importe'] }} {{ $registro['moneda'] }}</strong><br>{{ $registro['descripcion'] }}</p>
                    <div class="col-12">
                        @include('caja-bancos::selector', ['campo' => 'id_asiento', 'etiqueta' => 'Movimiento del libro', 'selectorUrl' => route('caja-bancos.conciliacion.candidatos', $registro['id'])])
                        <div class="form-text">Se muestran los movimientos pendientes de la misma cuenta con importe y signo iguales.</div>
                    </div>
                @elseif (in_array($accion, ['desconciliar', 'revertir'], true))
                    <div class="col-12">
                        <p class="text-body-secondary">{{ $accion === 'revertir' ? 'Se registrará una operación inversa y se conservará la original.' : 'La línea bancaria y el movimiento quedarán disponibles para una nueva conciliación.' }}</p>
                        <label for="cb-motivo" class="form-label">Motivo</label>
                        <textarea id="cb-motivo" class="form-control" name="motivo" maxlength="250" rows="3" required>{{ old('motivo') }}</textarea>
                    </div>
                @elseif ($accion === 'cerrar')
                    <p class="mb-0"><strong>{{ $registro['cuenta'] }}</strong> · Saldo en libros {{ $registro['saldo_actual'] }} {{ $registro['moneda'] }}</p>
                    <div class="col-sm-6">
                        <label for="cb-contado" class="form-label">Efectivo contado</label>
                        <input type="number" id="cb-contado" class="form-control" name="efectivo_contado" min="0" max="1000000000" step="0.01" value="{{ old('efectivo_contado') }}" required>
                    </div>
                    <div class="col-12">
                        <label for="cb-observacion" class="form-label">Observación</label>
                        <textarea id="cb-observacion" class="form-control" name="observacion" maxlength="500" rows="3">{{ old('observacion') }}</textarea>
                        <div class="form-text">Obligatoria si hay una diferencia. El cierre conserva el saldo del libro.</div>
                    </div>
                @else
                    @php
                        $tipoOrigen = in_array($seccion, ['cierres', 'depositos'], true) ? 'caja'
                            : (in_array($seccion, ['notas-debito', 'notas-credito', 'extractos'], true) ? 'banco' : 'todas');
                    @endphp
                    <div class="col-sm-6">@include('caja-bancos::selector', [
                        'campo' => 'id_cuenta', 'etiqueta' => in_array($seccion, ['transferencias', 'depositos'], true) ? 'Cuenta de origen' : 'Cuenta',
                        'selectorUrl' => route($ruta.'.opciones', $tipoOrigen),
                    ])</div>
                    @if ($seccion === 'extractos')
                        <div class="col-sm-6">
                            <label for="cb-extracto-nombre" class="form-label">Nombre del extracto</label>
                            <input id="cb-extracto-nombre" name="nombre" maxlength="160" class="form-control" value="{{ old('nombre') }}" required>
                        </div>
                        <div class="col-12">
                            @include('partials.campo-adjunto', [
                                'campo' => 'archivo', 'nombre' => 'archivo', 'titulo' => 'Extracto CSV',
                                'formatos' => '.csv,text/csv', 'iconoAdjunto' => 'bi-file-earmark-spreadsheet', 'accion' => 'Seleccionar CSV',
                                'ayuda' => 'Columnas: fecha,referencia,descripcion,importe. Usa AAAA-MM-DD y punto decimal. Los egresos van con signo negativo. Hasta 1.000 líneas.',
                                'requerido' => true,
                            ])
                        </div>
                    @elseif ($seccion !== 'cierres')
                        <input type="hidden" name="clave_operacion" value="{{ old('clave_operacion', (string) Str::uuid()) }}">
                        @if (in_array($seccion, ['transferencias', 'depositos'], true))
                            <div class="col-sm-6">@include('caja-bancos::selector', [
                                'campo' => 'id_destino', 'etiqueta' => 'Cuenta de destino', 'selectorUrl' => route($ruta.'.opciones', $seccion === 'depositos' ? 'banco' : 'todas'),
                            ])</div>
                        @elseif ($seccion === 'movimientos')
                            <div class="col-sm-6">
                                <label for="cb-tipo" class="form-label">Tipo de movimiento</label>
                                <select name="tipo" id="cb-tipo" class="form-select">
                                    <option value="ingreso">Ingreso</option><option value="egreso" @selected(old('tipo') === 'egreso')>Egreso</option>
                                </select>
                            </div>
                        @endif
                        <div class="col-sm-6">
                            <label for="cb-fecha" class="form-label">{{ __('Fecha') }}</label>
                            <input id="cb-fecha" name="fecha" type="date" class="form-control" value="{{ old('fecha', now()->toDateString()) }}" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="cb-importe" class="form-label">{{ __('Importe') }}</label>
                            <input id="cb-importe" name="importe" class="form-control" value="{{ old('importe') }}" type="number" min="0.01" max="1000000000" step="0.01" required>
                        </div>
                        <div class="col-12">
                            <label for="cb-concepto" class="form-label">Concepto</label>
                            <input id="cb-concepto" name="concepto" maxlength="250" value="{{ old('concepto') }}" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label for="cb-referencia" class="form-label">Referencia</label>
                            <input id="cb-referencia" name="referencia" maxlength="120" value="{{ old('referencia') }}" class="form-control">
                        </div>
                        @if ($seccion === 'movimientos')
                            <div class="col-sm-6">
                                <label for="cb-flujo" class="form-label">Actividad contable</label>
                                <select id="cb-flujo" name="flujo_contable" class="form-select">
                                    <option value="">Operación (predeterminada)</option>
                                    @foreach (['inversion' => 'Inversión', 'financiacion' => 'Financiación', 'apertura' => 'Saldos iniciales'] as $valor => $etiqueta)
                                        <option value="{{ $valor }}" @selected(old('flujo_contable') === $valor)>{{ __($etiqueta) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    @endif
                @endif
            </div>
            <div class="card-footer d-flex gap-2 flex-wrap align-items-center">
                @if ($seccion === 'cuentas')@include('partials.interruptor-activo', ['activo' => $registro['activo'] ?? true])@endif
                <div class="ms-auto d-flex gap-2">
                    <a href="{{ route($ruta.'.index') }}" class="btn btn-outline-secondary" data-ajax-close>{{ __('Cancelar') }}</a>
                    <button @class(['btn', 'btn-primary' => $accion !== 'revertir', 'btn-danger' => $accion === 'revertir'])>
                        {{ $accion ? 'Confirmar' : ($seccion === 'extractos' ? 'Importar extracto' : 'Guardar') }}
                    </button>
                </div>
            </div>
        </form>
    </div></div>
@endsection
