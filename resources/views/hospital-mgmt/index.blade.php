@extends('hospital-mgmt.base')

@section('action-content')
<section class="content">
    @if(session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
    @endif

    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-sm-8">
                    <h3 class="box-title">Unidades de atención de la empresa activa</h3>
                    <p class="help-block">Hospitales, consultorios y salas quedan aislados por empresa.</p>
                </div>
                <div class="col-sm-4">
                    <a class="btn btn-primary" href="{{ route('hospital-management.create') }}">Agregar nueva unidad</a>
                </div>
            </div>
        </div>

        <div class="box-body">
            <form method="POST" action="{{ route('hospital-management.search') }}">
                @csrf
                @component('layouts.search', ['title' => 'Buscar'])
                    @component('layouts.two-cols-search-row', [
                        'items' => ['Nombre Unidad'],
                        'oldVals' => [$searchingVals['nombre_hospital'] ?? ''],
                    ])
                    @endcomponent
                    <br>
                @endcomponent
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Nombre unidad</th>
                            <th>Ciudad</th>
                            <th>Dirección</th>
                            <th>Uso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hospitales as $hospital)
                            <tr>
                                <td>{{ $hospital->nombre_hospital }}</td>
                                <td>{{ $hospital->ciudad }}</td>
                                <td>{{ $hospital->direccion }}</td>
                                <td>
                                    @if((int) $defaultHospitalId === (int) $hospital->id)
                                        <span class="label label-success">Predeterminado</span>
                                    @elseif((int) $hospital->estado === 1)
                                        <form method="POST" action="{{ route('hospital-management.default', ['hospital' => $hospital->id]) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-info btn-xs">Usar por defecto</button>
                                        </form>
                                    @else
                                        <span class="label label-default">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('hospital-management.edit', ['hospital_management' => $hospital->id]) }}" class="btn btn-warning btn-sm btn-margin">Actualizar</a>
                                    <a href="{{ route('sala-management.listasalas', ['hospital' => $hospital->id]) }}" class="btn btn-warning btn-sm btn-margin">Salas</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Esta empresa todavía no tiene unidades asignadas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="row">
                <div class="col-sm-5">
                    <div class="dataTables_info" role="status" aria-live="polite">
                        Mostrando {{ $hospitales->count() }} de {{ $hospitales->total() }} registros
                    </div>
                </div>
                <div class="col-sm-7">
                    <div class="dataTables_paginate">{{ $hospitales->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    @if($unassignedHospitals->isNotEmpty())
        <div class="box box-warning">
            <div class="box-header">
                <h3 class="box-title">Unidades heredadas sin empresa</h3>
                <p class="help-block">Solo Sistemas ve esta lista. Asigna únicamente las unidades que realmente pertenecen a la empresa activa.</p>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Ciudad</th>
                            <th>Dirección</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($unassignedHospitals as $unassignedHospital)
                            <tr>
                                <td>{{ $unassignedHospital->nombre_hospital }}</td>
                                <td>{{ $unassignedHospital->ciudad }}</td>
                                <td>{{ $unassignedHospital->direccion }}</td>
                                <td>
                                    <form method="POST" action="{{ route('hospital-management.assign', ['hospital' => $unassignedHospital->id]) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-warning btn-sm">Asignar a esta empresa</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</section>
@endsection
