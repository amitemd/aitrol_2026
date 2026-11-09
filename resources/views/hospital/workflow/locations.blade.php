@extends('layouts.app-template-h')
@section('content')
<div class="content">
    <section class="content-header"><h3>Destinos hospitalarios</h3></section>
    <a class="btn btn-secondary btn-sm mb-2" href="{{ route('hospital.gcuartos') }}"><i class="fas fa-arrow-left"></i> Gestion de camas</a>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    @if(!$available)
        <div class="alert alert-warning">La configuracion de destinos hospitalarios no esta instalada.</div>
    @else
        <h4>Servicios clinicos</h4>
        <div class="table-responsive"><table class="table table-sm table-bordered">
            <thead><tr><th>Sala</th><th>Area</th><th></th></tr></thead>
            <tbody>@forelse($salas as $sala)
                <tr><td>{{ $sala->nombre_sala }}</td><td colspan="2">
                    <form class="form-inline" method="post" action="{{ route('hospital.workflow.locations.save') }}">@csrf
                        <input type="hidden" name="kind" value="sala"><input type="hidden" name="sala_id" value="{{ $sala->id }}">
                        <select name="step" class="form-control form-control-sm mr-2" required>
                            <option value="">Seleccione...</option>
                            @foreach($areas as $step => $name)<option value="{{ $step }}" @if($destinations->has($sala->id) && (int)$destinations[$sala->id]->step === $step) selected @endif>{{ $name }}</option>@endforeach
                        </select>
                        <button class="btn btn-primary btn-sm" title="Guardar area" aria-label="Guardar area"><i class="fas fa-save"></i></button>
                    </form>
                </td></tr>
            @empty<tr><td colspan="3">No hay salas activas para el hospital de esta empresa.</td></tr>@endforelse</tbody>
        </table></div>
        <h4>Habitaciones y camas</h4>
        <div class="table-responsive"><table class="table table-sm table-bordered">
            <thead><tr><th>Habitacion</th><th>Servicio receptor</th><th></th></tr></thead>
            <tbody>@foreach($rooms as $room)
                <tr><td>{{ $room->piso ? $room->piso->nombre_piso : '' }} / {{ $room->codigo }}</td><td colspan="2">
                    <form class="form-inline" method="post" action="{{ route('hospital.workflow.locations.save') }}">@csrf
                        <input type="hidden" name="kind" value="habitacion"><input type="hidden" name="habitacion_id" value="{{ $room->id }}">
                        <select name="sala_id" class="form-control form-control-sm mr-2" required><option value="">Seleccione...</option>
                            @foreach($bedSalas as $sala)<option value="{{ $sala->id }}" @if($roomLocations->has($room->id) && (int)$roomLocations[$room->id]->sala_id === (int)$sala->id) selected @endif>{{ $sala->nombre_sala }}</option>@endforeach
                        </select>
                        <button class="btn btn-primary btn-sm" title="Guardar habitacion" aria-label="Guardar habitacion"><i class="fas fa-save"></i></button>
                    </form>
                </td></tr>
            @endforeach</tbody>
        </table></div>
    @endif
</div>
@endsection
