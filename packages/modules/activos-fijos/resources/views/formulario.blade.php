@extends('layouts.adminlte')

@php
    [$titulo, , $alta] = config('activos-fijos.secciones.'.$seccion);
    $ruta = 'activos-fijos.'.$seccion;
    $catalogo = in_array($seccion, config('activos-fijos.catalogos'), true);
    $editar = $registro !== null;
    $tituloFormulario = $editar ? ($seccion === 'ordenes' ? 'Actualizar orden' : 'Editar registro') : $alta;
@endphp
@section('title', $tituloFormulario)
@section('content')
    <div class="app-content py-3"><div class="container-fluid">
        <form method="POST" class="card ficha-activo-fijo" data-ajax-form
            action="{{ $editar ? route($ruta.'.update', $registro['id']) : route($ruta.'.store') }}">
            @csrf
            @if ($editar) @method('PUT') @endif
            <h1 class="card-header fs-5">{{ $tituloFormulario }}</h1>
            @if ($errors->any())
                <div class="alert alert-danger m-3" role="alert">
                    <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            <div class="card-body row g-3">
                @if ($catalogo || $seccion === 'activos')
                    <div class="col-sm-4">
                        <label for="af-codigo" class="form-label">{{ __('Código') }}</label>
                        <input class="form-control" id="af-codigo" name="codigo" maxlength="40" value="{{ old('codigo', $registro['codigo'] ?? '') }}" required autofocus>
                    </div>
                    <div class="col-sm-8">
                        <label for="af-nombre" class="form-label">{{ __('Nombre') }}</label>
                        <input class="form-control" id="af-nombre" name="nombre" maxlength="160" value="{{ old('nombre', $registro['nombre'] ?? '') }}" required>
                    </div>
                @endif
                @if ($catalogo)
                    @if ($origenes)
                        @php $padre = $origenes[0]; @endphp
                        <div class="col-sm-6">
                            @if ($editar)
                                <label class="form-label" for="af-padre-fijo">{{ $padre === 'grupos' ? 'Grupo' : 'Tipo' }}</label>
                                <input id="af-padre-fijo" class="form-control" value="{{ $registro['padre'] }}" readonly>
                                <input type="hidden" name="id_padre" value="{{ $registro['id_padre'] }}">
                            @else
                                @include('activos-fijos::selector', ['campo' => 'id_padre', 'origen' => $padre, 'etiqueta' => $padre === 'grupos' ? 'Grupo' : 'Tipo', 'requerido' => true])
                            @endif
                        </div>
                    @endif
                    @if ($seccion === 'subtipos')
                        <div class="col-sm-6">
                            <label for="af-vida_util_meses" class="form-label">Vida útil sugerida (meses)</label>
                            <input type="number" min="0" max="600" class="form-control" id="af-vida_util_meses" name="vida_util_meses" value="{{ old('vida_util_meses', $registro['vida_util_meses'] ?? '') }}">
                            <div class="form-text">Usa 0 para un bien no depreciable. Puedes dejarla sin definir.</div>
                        </div>
                    @endif
                @elseif ($seccion === 'activos')
                    @foreach (['marca' => ['Marca', 80], 'modelo' => ['Modelo', 80], 'serie' => ['Número de serie', 100]] as $campo => [$etiqueta, $max])
                        <div class="col-sm-4">
                            <label for="af-{{ $campo }}" class="form-label">{{ __($etiqueta) }}</label>
                            <input class="form-control" id="af-{{ $campo }}" name="{{ $campo }}" maxlength="{{ $max }}" value="{{ old($campo, $registro[$campo] ?? '') }}">
                        </div>
                    @endforeach
                    @unless ($editar)
                        @foreach (['id_subtipo' => ['Subtipo', 'subtipos', true], 'id_ubicacion' => ['Ubicación', 'ubicaciones', true], 'id_responsable' => ['Responsable', 'responsables', false], 'id_compra' => ['Documento de compra', 'compras', false]] as $campo => [$etiqueta, $origen, $requerido])
                            <div class="col-sm-6">@include('activos-fijos::selector', compact('campo', 'etiqueta', 'origen', 'requerido'))</div>
                        @endforeach
                        <div class="col-sm-6">
                            <label for="af-adquisicion" class="form-label">Fecha de adquisición</label>
                            <input type="date" class="form-control" id="af-adquisicion" name="fecha_adquisicion" value="{{ old('fecha_adquisicion', now()->toDateString()) }}" required>
                        </div>
                    @endunless
                    <div class="col-sm-6">
                        <label for="af-inicio" class="form-label">Inicio de depreciación</label>
                        <input type="date" class="form-control" id="af-inicio" name="inicio_depreciacion" value="{{ old('inicio_depreciacion', $registro['inicio_depreciacion'] ?? '') }}">
                        <div class="form-text">Si lo dejas vacío, se usa la fecha de adquisición.</div>
                    </div>
                    <div class="col-sm-4">
                        <label for="af-vida_util_meses" class="form-label">Vida útil (meses)</label>
                        <input type="number" min="0" max="600" class="form-control" id="af-vida_util_meses" name="vida_util_meses" value="{{ old('vida_util_meses', $registro['vida_util_meses'] ?? '') }}">
                        @unless ($editar)<div class="form-text">Si lo dejas vacío, se usa la vida útil del subtipo. Usa 0 para un bien no depreciable.</div>@endunless
                    </div>
                    @foreach (['costo' => 'Costo de adquisición', 'valor_residual' => 'Valor residual'] as $campo => $etiqueta)
                        <div class="col-sm-4">
                            <label for="af-{{ $campo }}" class="form-label">{{ __($etiqueta) }}</label>
                            <input type="number" min="0" max="1000000000" step="0.01" class="form-control" id="af-{{ $campo }}"
                                name="{{ $campo }}" value="{{ old($campo, $registro[$campo] ?? ($campo === 'valor_residual' ? '0.00' : '')) }}" @required($campo === 'costo')>
                        </div>
                    @endforeach
                @elseif ($seccion === 'ordenes' && $editar)
                    <div class="col-12"><p class="mb-0"><strong>{{ $registro['codigo_activo'] }} · {{ $registro['activo'] }}</strong></p></div>
                    <div class="col-sm-6">
                        <label for="af-estado-orden" class="form-label">{{ __('Estado') }}</label>
                        <select class="form-select" id="af-estado-orden" name="estado" required>
                            <option value="">{{ __('Seleccionar') }}</option>
                            @foreach (config('activos-fijos.estados_orden') as $valor => $etiqueta)
                                @continue($valor === 'pendiente' || $valor === $registro['estado'])
                                <option value="{{ $valor }}" @selected(old('estado') === $valor)>{{ __($etiqueta) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6 af-costo-orden">
                        <label for="af-costo-mantenimiento" class="form-label">Costo de mantenimiento</label>
                        <input type="number" min="0" max="1000000000" step="0.01" id="af-costo-mantenimiento" name="costo_mantenimiento" class="form-control" value="{{ old('costo_mantenimiento') }}">
                    </div>
                    <div class="col-12">
                        <label for="af-resultado" class="form-label">Resultado o motivo de cancelación</label>
                        <textarea id="af-resultado" name="resultado" class="form-control" maxlength="500" rows="3">{{ old('resultado') }}</textarea>
                    </div>
                @else
                    <div class="col-sm-6">
                        @include('activos-fijos::selector', ['campo' => 'id_activo', 'origen' => $seccion === 'depreciaciones' ? 'depreciables' : 'activos', 'etiqueta' => 'Activo', 'requerido' => true])
                    </div>
                    @if ($seccion === 'depreciaciones')
                        <div class="col-sm-6">
                            <label for="af-periodo" class="form-label">Período de depreciación</label>
                            <input type="month" name="periodo" id="af-periodo" class="form-control" value="{{ old('periodo', now()->format('Y-m')) }}" required>
                        </div>
                        <p class="form-text mb-0">Registra los meses consecutivamente desde el inicio de depreciación del activo. La cuota se calcula por línea recta hasta el valor residual.</p>
                    @else
                        <div class="col-sm-6">
                            <label for="af-tipo" class="form-label">{{ $seccion === 'movimientos' ? 'Tipo de movimiento' : 'Tipo de mantenimiento' }}</label>
                            <select id="af-tipo" name="tipo" class="form-select" required>
                                @foreach ($seccion === 'movimientos' ? config('activos-fijos.movimientos') : ['preventivo' => 'Preventivo', 'correctivo' => 'Correctivo'] as $valor => $etiqueta)
                                    <option value="{{ $valor }}" @selected(old('tipo') === $valor)>{{ __($etiqueta) }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($seccion === 'movimientos')
                            <div class="col-sm-6 af-destino-traslado">
                                @include('activos-fijos::selector', ['campo' => 'id_ubicacion_destino', 'origen' => 'ubicaciones', 'etiqueta' => 'Ubicación de destino', 'requerido' => false])
                            </div>
                            <div class="col-sm-6 af-destino-asignacion">
                                @include('activos-fijos::selector', ['campo' => 'id_responsable_destino', 'origen' => 'responsables', 'etiqueta' => 'Nuevo responsable', 'requerido' => false])
                                <div class="form-text">Déjalo vacío para retirar la asignación actual.</div>
                            </div>
                        @endif
                        @php
                            $fechaCampo = $seccion === 'movimientos' ? 'fecha' : 'fecha_programada';
                            $detalleCampo = $seccion === 'movimientos' ? 'motivo' : 'detalle';
                        @endphp
                        <div class="col-sm-6">
                            <label for="af-fecha-operacion" class="form-label">{{ $seccion === 'movimientos' ? 'Fecha' : 'Fecha programada' }}</label>
                            <input type="date" id="af-fecha-operacion" name="{{ $fechaCampo }}" class="form-control" value="{{ old($fechaCampo, now()->toDateString()) }}" required>
                        </div>
                        <div class="col-12">
                            <label for="af-detalle-operacion" class="form-label">{{ $seccion === 'movimientos' ? 'Motivo' : 'Trabajo solicitado' }}</label>
                            <textarea name="{{ $detalleCampo }}" id="af-detalle-operacion" class="form-control" rows="3" maxlength="{{ $seccion === 'movimientos' ? 250 : 500 }}" required>{{ old($detalleCampo) }}</textarea>
                        </div>
                    @endif
                @endif
                @if ($catalogo || $seccion === 'activos')
                    <div class="col-12">
                        <label for="af-descripcion" class="form-label">{{ __('Descripción') }}</label>
                        <textarea id="af-descripcion" name="descripcion" class="form-control" rows="2" maxlength="500">{{ old('descripcion', $registro['descripcion'] ?? '') }}</textarea>
                    </div>
                @endif
            </div>
            <div class="card-footer ficha-acciones d-flex flex-wrap gap-2 align-items-center">
                @if ($catalogo) @include('partials.interruptor-activo', ['activo' => $registro['activo'] ?? true]) @endif
                <div class="ms-auto d-flex gap-2">
                    <a href="{{ route($ruta.'.index') }}" class="btn btn-outline-secondary" data-ajax-close>{{ __('Cancelar') }}</a>
                    <button class="btn btn-primary"><i class="bi bi-check2 me-1" aria-hidden="true"></i>{{ $editar ? 'Guardar cambios' : 'Registrar' }}</button>
                </div>
            </div>
        </form>
    </div></div>
@endsection
