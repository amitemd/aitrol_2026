@extends('layouts.app-template')
@section('content')
<div class="container-fluid"><div class="box box-primary">
    <div class="box-header"><h3 class="box-title"><i class="fa fa-shield"></i> Accesos clínicos excepcionales</h3></div>
    <div class="box-body">
        @if(session('success'))<div class="alert alert-success">{{session('success')}}</div>@endif
        @if(session('error'))<div class="alert alert-danger">{{session('error')}}</div>@endif
        <div class="alert alert-warning"><strong>Uso de emergencia:</strong> el acceso dura 60 minutos, exige una razón clínica específica y será revisado por otra persona.</div>
        <form method="POST" action="{{route('clinical_access.break_glass')}}" class="row" style="margin-bottom:20px;">
            {{csrf_field()}}
            <div class="col-md-3"><input class="form-control" name="encounter_id" value="{{isset($encounterId)?$encounterId:''}}" placeholder="ID del episodio" required @if(isset($encounterId)&&$encounterId!=='') readonly @endif></div>
            <div class="col-md-2"><select class="form-control" name="action" required><option value="">Acción clínica</option><option value="ORDER_VIEW">Consultar órdenes</option><option value="ORDER_TRANSITION">Actualizar orden</option><option value="RESULT_CREATE">Registrar resultado</option><option value="CRITICAL_RESULT_NOTIFY">Notificar resultado crítico</option><option value="CRITICAL_RESULT_ACK">Acusar resultado crítico</option><option value="medication.administer">Administrar medicación</option><option value="medication.emergency">Medicación de emergencia</option><option value="medication.regularize">Regularizar medicación</option><option value="procedure.authorize">Autorizar procedimiento</option><option value="CONSULT_VIEW">Consultar interconsultas</option><option value="consult.update">Actualizar interconsulta</option><option value="encounter.close">Cerrar episodio</option><option value="SAFETY_EVENT_VIEW">Consultar seguridad clínica</option><option value="SAFETY_EVENT_CREATE">Registrar evento de seguridad</option><option value="SAFETY_EVENT_NOTIFY">Notificar evento de seguridad</option><option value="SAFETY_EVENT_TRANSITION">Actualizar evento de seguridad</option></select></div>
            <div class="col-md-5"><input class="form-control" name="reason" minlength="20" placeholder="Razón clínica urgente y específica" required></div>
            <div class="col-md-2"><button class="btn btn-danger"><i class="fa fa-unlock"></i> Activar 60 min</button></div>
        </form>
        <div class="table-responsive"><table class="table table-bordered">
            <thead><tr><th>Episodio</th><th>Usuario</th><th>Acción</th><th>Razón</th><th>Estado</th><th>Vence</th><th>Revisión</th></tr></thead>
            <tbody>@foreach($accesses as $access)<tr><td>{{$access->encounter_id}}</td><td>{{$access->user_id}}</td><td>{{$access->action}}</td><td>{{$access->reason}}</td><td>{{$access->status}}</td><td>{{$access->expires_at}}</td><td>
                @if(!$access->reviewed_at && $isReviewer && (string)$access->user_id!==(string)auth()->user()->id)
                <form method="POST" action="{{route('clinical_access.review',$access->id)}}">{{csrf_field()}}<select name="decision" class="form-control input-sm"><option value="APPROVED">Aprobar</option><option value="REJECTED">Rechazar</option></select><input name="notes" minlength="10" class="form-control input-sm" placeholder="Conclusión de revisión" required><button class="btn btn-primary btn-xs">Registrar revisión</button></form>
                @elseif($access->reviewed_at) {{$access->review_decision}} por {{$access->reviewed_by}}
                @else Pendiente de revisión independiente
                @endif
            </td></tr>@endforeach</tbody>
        </table></div>
        {{$accesses->appends(request()->query())->links()}}
    </div>
</div></div>
@endsection
