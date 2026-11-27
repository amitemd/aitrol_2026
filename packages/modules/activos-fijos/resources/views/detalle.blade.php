@extends('layouts.adminlte')

@php
    $campos = match ($seccion) {
        'activos', 'adquisiciones' => [
            'codigo' => 'Código', 'nombre' => 'Nombre', 'descripcion' => 'Descripción', 'marca' => 'Marca',
            'modelo' => 'Modelo', 'serie' => 'Serie', 'grupo' => 'Grupo', 'tipo' => 'Tipo', 'subtipo' => 'Subtipo',
            'ubicacion' => 'Ubicación', 'responsable' => 'Responsable', 'fecha_adquisicion' => 'Fecha de adquisición',
            'factura' => 'Documento de compra', 'proveedor' => 'Proveedor', 'compra_anulada_at' => 'Compra anulada el',
            'vida_util_meses' => 'Vida útil', 'inicio_depreciacion' => 'Inicio de depreciación', 'costo' => 'Costo',
            'valor_residual' => 'Valor residual', 'depreciado' => 'Depreciación acumulada', 'valor_libros' => 'Valor en libros',
            'estado' => 'Estado', 'fecha_baja' => 'Fecha de baja',
        ],
        'movimientos' => ['codigo_activo' => 'Código', 'activo' => 'Activo', 'tipo' => 'Movimiento', 'fecha' => 'Fecha', 'motivo' => 'Motivo',
            'ubicacion_anterior' => 'Ubicación anterior', 'ubicacion_destino' => 'Ubicación final',
            'responsable_anterior' => 'Responsable anterior', 'responsable_destino' => 'Responsable final'],
        'depreciaciones' => ['codigo_activo' => 'Código', 'activo' => 'Activo', 'periodo' => 'Período', 'importe' => 'Cuota', 'acumulado' => 'Acumulado', 'valor_libros' => 'Valor en libros'],
        'ordenes' => ['codigo_activo' => 'Código', 'activo' => 'Activo', 'tipo' => 'Mantenimiento', 'detalle' => 'Trabajo solicitado',
            'fecha_programada' => 'Fecha programada', 'estado' => 'Estado', 'resultado' => 'Resultado',
            'finalizada_at' => 'Finalizada el', 'costo_mantenimiento' => 'Costo de mantenimiento'],
        default => ['codigo' => 'Código', 'nombre' => 'Nombre', 'descripcion' => 'Descripción', 'padre' => 'Clasificación superior', 'vida_util_meses' => 'Vida útil sugerida', 'activo' => 'Estado'],
    };
@endphp
@section('title', 'Detalle de activos fijos')
@section('content')
    <div class="app-content py-3"><div class="container-fluid">
        <section class="card" data-ajax-form>
            <h1 class="card-header fs-5">Detalle del registro</h1>
            <div class="card-body">
                <dl class="row mb-0">
                    @foreach (array_intersect_key($campos, $registro) as $campo => $etiqueta)
                        @continue($registro[$campo] === null)
                        <dt class="col-sm-5">{{ $etiqueta }}</dt>
                        <dd class="col-sm-7">@include('activos-fijos::valor', ['valor' => $registro[$campo]])</dd>
                    @endforeach
                    <dt class="col-sm-5">Creado el</dt><dd class="col-sm-7">{{ $registro['created_at'] }}</dd>
                </dl>
            </div>
            <div class="card-footer text-end"><a href="{{ route('activos-fijos.'.$seccion.'.index') }}" class="btn btn-outline-secondary" data-ajax-close>Cerrar</a></div>
        </section>
    </div></div>
@endsection
