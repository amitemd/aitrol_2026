@extends('layouts.adminlte')

@php
    use Aitrol\Rrhh\Application\DatosRrhh;
    $ruta = 'rrhh.'.$seccion;
    $pestanas = match ($seccion) {
        'empleados' => ['general' => ['Datos personales', 'bi-person'], 'laboral' => ['Información laboral', 'bi-diagram-3']],
        'contratos' => ['general' => ['Contrato', 'bi-file-earmark-text'], 'laboral' => ['Condiciones laborales', 'bi-wallet2']],
        default => [],
    };
    $laborales = array_flip($seccion === 'empleados'
        ? ['ingreso', 'salida', 'id_departamento', 'id_cargo']
        : ['sueldo', 'horas_semanales']);
    $grupos = $pestanas ? ['general' => array_diff_key($campos, $laborales), 'laboral' => array_intersect_key($campos, $laborales)] : ['general' => $campos];
@endphp
@section('title', $registro ? 'Editar registro | Recursos humanos' : 'Nuevo registro | Recursos humanos')
@section('content')
    @include('rrhh::cabecera')
    <div class="app-content">
        <div class="container-fluid">
            <form class="card" method="POST" data-ajax-form data-usuario-form action="{{ $registro ? route($ruta.'.update', $registro->id) : route($ruta.'.store') }}">
                @csrf
                @if ($registro)
                    @method('PUT')
                @endif
                <div class="card-header"><h2 class="card-title">{{ $registro ? 'Editar registro' : 'Nuevo registro' }}</h2></div>
                <div class="card-body">
                    @if ($seccion === 'nomina')
                        <p class="text-body-secondary mb-0">Selecciona el mes. El cierre toma los contratos vigentes y las novedades y beneficios aprobados.</p>
                    @endif
                    @if ($seccion === 'conceptos')
                        <p class="text-body-secondary mb-0">Según el tipo, ingresa un importe fijo o un porcentaje. La nómina aplica los conceptos aprobados que estén vigentes al último día del mes.</p>
                    @endif
                    @if ($pestanas)
                        @include('partials.pestanas-ficha', ['pestanas' => $pestanas, 'contexto' => 'rrhh'])
                    @endif
                    @foreach ($grupos as $grupo => $camposGrupo)
                    <section class="ficha-bloque" @if ($pestanas) id="rrhh-{{ $grupo }}" role="tabpanel" aria-labelledby="rrhh-tab-{{ $grupo }}" data-usuario-panel="{{ $grupo }}" @endif>
                        @include('partials.ficha-bloque-titulo', ['tituloBloque' => $pestanas[$grupo][0] ?? config('rrhh.secciones.'.$seccion.'.0'), 'iconoBloque' => $pestanas[$grupo][1] ?? config('rrhh.secciones.'.$seccion.'.1')])
                        <div class="row g-3">
                    @foreach ($camposGrupo as $clave => $campo)
                        @php
                            [$etiqueta, $tipo, $obligatorio] = $campo;
                            $valor = $registro->{$clave} ?? '';
                            if ($valor !== '' && in_array($clave, ['importe', 'sueldo'], true)) {
                                $valor = DatosRrhh::importe((int) $valor);
                            }
                            $valor = old($clave, $valor);
                        @endphp
                        <div class="{{ $tipo === 'textarea' ? 'col-12' : 'col-md-6 col-xl-4' }}">
                            <label class="form-label" for="rrhh-{{ $clave }}">{{ $etiqueta }}</label>
                            @if ($tipo === 'textarea')
                                <textarea class="form-control @error($clave) is-invalid @enderror" id="rrhh-{{ $clave }}" name="{{ $clave }}" rows="3" maxlength="3000">{{ $valor }}</textarea>
                            @elseif ($tipo === 'select')
                                <select class="form-select @error($clave) is-invalid @enderror" id="rrhh-{{ $clave }}" name="{{ $clave }}" @required($obligatorio)>
                                    <option value="">Seleccionar</option>
                                    @foreach ($campo[3] as $id => $nombre)
                                        <option value="{{ $id }}" @selected((string) $valor === (string) $id)>{{ $nombre }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input class="form-control @error($clave) is-invalid @enderror" type="{{ $tipo }}" id="rrhh-{{ $clave }}"
                                    name="{{ $clave }}" value="{{ $valor }}" @required($obligatorio) @if ($tipo === 'number') step="0.01" min="0" @endif>
                            @endif
                            @error($clave)<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    @endforeach
                        </div>
                    </section>
                    @endforeach
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a class="btn btn-outline-secondary" href="{{ route($ruta.'.index') }}" data-ajax-close>Cancelar</a>
                    <button class="btn btn-primary" type="submit">Guardar</button>
                </div>
            </form>
        </div>
    </div>
@endsection
