@extends('layouts.adminlte')

@php
    $ruta = 'tributario.'.$seccion;
    $titulo = match ($accion) {
        'revisar' => 'Marcar revisado', 'reabrir' => 'Reabrir borrador', 'registrar-entrega' => 'Registrar constancia externa',
        default => $seccion === 'nomina' ? 'Datos del trabajador' : config('tributario.secciones.'.$seccion.'.2'),
    };
@endphp
@section('title', $titulo)
@section('content')
    <div class="app-content py-3"><div class="container-fluid">
        <form method="POST" enctype="multipart/form-data" class="card" data-ajax-form action="{{ $accion ? route($ruta.'.'.$accion, $registro->id) : route($ruta.'.store') }}">
            @csrf
            <h1 class="card-header fs-5">{{ $titulo }}</h1>
            @include('contabilidad::errores')
            <div class="card-body row g-3">
                @if ($accion)
                    <p class="mb-0">{{ config('tributario.secciones.'.$registro->formato.'.0') }} · {{ $registro->desde }} a {{ $registro->hasta }}</p>
                    @if ($accion === 'registrar-entrega')
                        <p class="form-text mb-0">Adjunta el acuse o la constancia en Gestión documental antes de registrar la fecha y la referencia externas.</p>
                        <div class="col-sm-6"><label for="tr-referencia" class="form-label">Referencia externa</label><input id="tr-referencia" name="referencia_externa" maxlength="100" class="form-control" required></div>
                        <div class="col-sm-6"><label for="tr-fecha" class="form-label">Fecha externa</label><input id="tr-fecha" name="fecha_externa" type="date" class="form-control" value="{{ now()->toDateString() }}" required></div>
                    @else
                        <div class="col-12"><label for="tr-motivo" class="form-label">Observación de revisión</label><textarea id="tr-motivo" name="motivo" maxlength="250" rows="3" class="form-control" required></textarea></div>
                    @endif
                @elseif ($seccion === 'documentos')
                    <div class="col-sm-8">
                        <label for="tr-declaracion" class="form-label">Papel de trabajo</label>
                        <select id="tr-declaracion" name="id_declaracion" data-select2-url="{{ route('tributario.documentos.opciones') }}" data-select2-min="0" class="form-select" required><option value="">Seleccionar</option></select>
                    </div>
                    <div class="col-sm-4"><label for="tr-tipo" class="form-label">Tipo de respaldo</label>
                        <select id="tr-tipo" name="tipo" class="form-select" required>
                            @foreach (config('tributario.archivos') as $tipo => $texto)<option value="{{ $tipo }}">{{ $texto }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-12"><label for="tr-nombre" class="form-label">Nombre</label><input id="tr-nombre" name="nombre" maxlength="180" class="form-control" required></div>
                    <div class="col-12">
                        @include('partials.campo-adjunto', ['campo' => 'archivo', 'nombre' => 'archivo', 'titulo' => 'Archivo de respaldo',
                            'formatos' => '.pdf,.xml,.csv,.xlsx', 'iconoAdjunto' => 'bi-file-earmark-text',
                            'accion' => 'Seleccionar archivo', 'ayuda' => 'PDF, XML, CSV o XLSX. Máximo 10 MB.', 'requerido' => true])
                    </div>
                @else
                    <div class="col-sm-4">
                        <label for="tr-anio" class="form-label">Ejercicio</label>
                        <input id="tr-anio" name="ejercicio" class="form-control" type="number" min="1900" max="{{ now()->year }}" value="{{ old('ejercicio', now()->year) }}" required>
                    </div>
                    @if ($seccion === 'nomina')
                        <div class="col-sm-8"><label for="tr-nombre" class="form-label">Nombres y apellidos</label><input id="tr-nombre" name="nombre" maxlength="180" class="form-control" required></div>
                        <div class="col-sm-6"><label for="tr-identificacion" class="form-label">Cédula o pasaporte</label><input id="tr-identificacion" name="identificacion" maxlength="30" class="form-control" required></div>
                        @foreach (['ingresos_gravados' => 'Ingresos gravados', 'ingresos_exentos' => 'Ingresos exentos', 'aporte_personal' => 'Aporte personal', 'retencion' => 'Retención practicada'] as $campo => $texto)
                            <div class="col-sm-6"><label for="tr-{{ $campo }}" class="form-label">{{ $texto }}</label><input id="tr-{{ $campo }}" name="{{ $campo }}" type="number" min="0" max="1000000000" step="0.01" value="0.00" class="form-control" required></div>
                        @endforeach
                    @elseif ($seccion !== 'formulario-107')
                        <div class="col-sm-4"><label for="tr-mes" class="form-label">Mes inicial</label>
                            <select id="tr-mes" name="mes" class="form-select" required>
                                @foreach (['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $indice => $mes)
                                    <option value="{{ $indice + 1 }}" @selected((int) old('mes', now()->month) === $indice + 1)>{{ $mes }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($seccion === 'formulario-104')
                            <div class="col-sm-4"><label for="tr-periodicidad" class="form-label">Periodicidad del papel</label><select id="tr-periodicidad" name="semestral" class="form-select">
                                <option value="0">Mensual</option><option value="1" @selected(old('semestral'))>Semestral</option>
                            </select></div>
                        @elseif ($seccion === 'formulario-103')
                            @foreach (['base_nomina' => 'Base de nómina informada', 'retencion_nomina' => 'Retención de nómina informada'] as $campo => $texto)
                                <div class="col-sm-6"><label for="tr-{{ $campo }}" class="form-label">{{ $texto }}</label><input id="tr-{{ $campo }}" type="number" min="0" max="1000000000" step="0.01" name="{{ $campo }}" value="{{ old($campo, '0.00') }}" class="form-control"></div>
                            @endforeach
                        @endif
                    @endif
                @endif
            </div>
            <div class="card-footer d-flex justify-content-end gap-2">
                <a href="{{ route($ruta.'.index') }}" data-ajax-close class="btn btn-outline-secondary">Cancelar</a><button class="btn btn-primary">{{ $accion || in_array($seccion, ['documentos', 'nomina'], true) ? 'Guardar' : 'Preparar borrador' }}</button>
            </div>
        </form>
    </div></div>
@endsection
