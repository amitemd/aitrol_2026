@extends('layouts.app-template')
@section('content')
<div class="container-fluid">
    <div class="box box-danger">
        <div class="box-header"><h3 class="box-title"><i class="fa fa-shield"></i> Seguridad clínica transversal</h3></div>
        <div class="box-body">
            @if(session('success'))<div class="alert alert-success">{{session('success')}}</div>@endif
            @if(session('error'))<div class="alert alert-danger">{{session('error')}}</div>@endif
            <form method="POST" action="{{route('clinical_safety.store')}}" class="row">
                {{csrf_field()}}
                <div class="col-md-3"><input class="form-control" name="encounter_id" value="{{isset($encounterId)?$encounterId:''}}" placeholder="ID del episodio" required @if(isset($encounterId)&&$encounterId!=='') readonly @endif></div>
                <div class="col-md-2"><select class="form-control" name="category"><option value="ADVERSE_EVENT">Evento adverso</option><option value="HAI">IAAS</option><option value="ISOLATION">Aislamiento</option><option value="CUSTODY">Custodia</option></select></div>
                <div class="col-md-2"><select class="form-control" name="severity"><option value="LOW">Baja</option><option value="MODERATE">Moderada</option><option value="HIGH">Alta</option><option value="CRITICAL">Crítica</option></select></div>
                <div class="col-md-2"><input type="datetime-local" class="form-control" name="occurred_at" required></div>
                <div class="col-md-3"><input class="form-control" name="location" placeholder="Ubicación" required></div>
                <div class="col-md-4"><textarea class="form-control" name="description" placeholder="Descripción clínica" required></textarea></div>
                <div class="col-md-4"><textarea class="form-control" name="immediate_actions" placeholder="Acciones inmediatas" required></textarea></div>
                <div class="col-md-2"><input class="form-control" name="assigned_to" placeholder="Responsable" required></div>
                <div class="col-md-2"><button class="btn btn-danger">Registrar evento</button></div>
                <div class="col-md-4"><input class="form-control" name="pathogen" placeholder="Agente IAAS confirmado/sospechoso"></div>
                <div class="col-md-4"><input class="form-control" name="isolation_type" placeholder="Tipo de aislamiento"></div>
                <div class="col-md-4"><input class="form-control" name="custody_reference" placeholder="Referencia de custodia"></div>
            </form>
            <hr>
            <div class="table-responsive"><table class="table table-bordered">
                <thead><tr><th>Episodio</th><th>Categoría</th><th>Severidad</th><th>Descripción</th><th>Estado</th><th>Responsable</th><th>Acciones</th></tr></thead>
                <tbody>@foreach($events as $event)<tr><td>{{$event->encounter_id}}</td><td>{{$event->category}}</td><td>{{$event->severity}}</td><td>{{$event->description}}</td><td>{{$event->status}}</td><td>{{$event->assigned_to}}</td><td>
                    <form method="POST" action="{{route('clinical_safety.notify',$event->id)}}">{{csrf_field()}}<input class="form-control input-sm" name="recipient" placeholder="Receptor"><button class="btn btn-warning btn-xs">Notificar</button></form>
                    <form method="POST" action="{{route('clinical_safety.transition',$event->id)}}">{{csrf_field()}}<select class="form-control input-sm" name="status"><option value="UNDER_REVIEW">En revisión</option><option value="MITIGATED">Mitigado</option><option value="CLOSED">Cerrar</option></select><textarea class="form-control input-sm" name="root_cause" placeholder="Causa raíz"></textarea><textarea class="form-control input-sm" name="corrective_actions" placeholder="Acciones correctivas"></textarea><button class="btn btn-primary btn-xs">Actualizar</button></form>
                </td></tr>@endforeach</tbody>
            </table></div>
            {{$events->appends(request()->query())->links()}}
        </div>
    </div>
</div>
@endsection
