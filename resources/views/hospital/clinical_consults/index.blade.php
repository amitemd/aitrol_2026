@extends('layouts.app-template')
@section('content')
@php
$labels=array('REQUESTED'=>'Solicitada','ACCEPTED'=>'Aceptada','IN_REVIEW'=>'En revisión','COMPLETED'=>'Respondida','CANCELLED'=>'Cancelada','ESCALATED'=>'Escalada');
@endphp
<style>
.consult-page{padding:1.5rem}.consult-hero{padding:1.4rem 1.6rem;border-radius:16px;background:linear-gradient(120deg,#075985,#0f766e);color:#fff;margin-bottom:1.2rem}.consult-card{background:#fff;border:1px solid #d8e6ec;border-radius:14px;overflow:hidden;box-shadow:0 7px 24px rgba(15,55,75,.06)}.consult-head{background:#eef8fb;padding:1rem 1.2rem;border-bottom:1px solid #d8e6ec}.consult-table th{background:#f5f8fa;color:#164e63;font-size:.75rem;text-transform:uppercase}.consult-pill{display:inline-block;padding:.28rem .65rem;border-radius:999px;background:#e0f2fe;color:#075985;font-weight:700;font-size:.72rem}.consult-pill.danger{background:#fee2e2;color:#991b1b}.consult-pill.ok{background:#dcfce7;color:#166534}.consult-actions form{margin-bottom:.3rem}.consult-response{min-width:210px}
</style>
<div class="consult-page">
 <div class="consult-hero"><div class="text-uppercase small font-weight-bold">Coordinación asistencial</div><h2 class="text-white mb-25"><i class="fa fa-user-md"></i> Interconsultas clínicas</h2><div>Aceptación, revisión, respuesta y escalamiento por tiempo.</div></div>
 @if(session('success'))<div class="alert alert-success">{{session('success')}}</div>@endif
 @if(session('error'))<div class="alert alert-danger">{{session('error')}}</div>@endif
 @if($errors->any())<div class="alert alert-danger">{{$errors->first()}}</div>@endif
 <div class="consult-card">
  <div class="consult-head"><form method="GET" class="row">
   @if($encounterId!=='')<input type="hidden" name="encounter_id" value="{{$encounterId}}">@endif
   <div class="col-md-4"><select name="priority" class="form-control"><option value="">Todas las prioridades</option>@foreach(array('ROUTINE'=>'Rutina','URGENT'=>'Urgente','STAT'=>'Inmediata') as $v=>$l)<option value="{{$v}}" {{request('priority')==$v?'selected':''}}>{{$l}}</option>@endforeach</select></div>
   <div class="col-md-4"><select name="status" class="form-control"><option value="">Todos los estados</option>@foreach($labels as $v=>$l)<option value="{{$v}}" {{request('status')==$v?'selected':''}}>{{$l}}</option>@endforeach</select></div>
   <div class="col-md-4"><button class="btn btn-primary"><i class="fa fa-search"></i> Filtrar</button> <a class="btn btn-outline-secondary" href="{{route('clinical_consults.index', $encounterId!==''?array('encounter_id'=>$encounterId):array())}}">Limpiar</a></div>
  </form></div>
  <div class="table-responsive"><table class="table consult-table mb-0"><thead><tr><th>Paciente / episodio</th><th>Destino</th><th>Prioridad</th><th>Estado</th><th>Tiempo límite</th><th>Respuesta</th><th style="min-width:300px">Acciones</th></tr></thead><tbody>
  @forelse($consults as $consult)
   @php $overdue=!in_array($consult->status,array('COMPLETED','CANCELLED')) && $consult->due_at && $consult->due_at->isPast(); @endphp
   <tr>
    <td><strong>{{$consult->patient_id}}</strong><br><small>{{$consult->encounter_id}}</small></td>
    <td>{{$consult->target_service?:'Sin servicio'}}<br><small>{{$consult->target_specialty?:'Sin especialidad'}}</small></td>
    <td><span class="consult-pill {{$consult->priority!='ROUTINE'?'danger':''}}">{{$consult->priority}}</span></td>
    <td><span class="consult-pill {{$consult->status=='COMPLETED'?'ok':($consult->status=='ESCALATED'?'danger':'')}}">{{isset($labels[$consult->status])?$labels[$consult->status]:$consult->status}}</span>@if($consult->escalation_level)<br><small>Nivel {{$consult->escalation_level}}</small>@endif</td>
    <td class="{{$overdue?'text-danger font-weight-bold':''}}">{{$consult->due_at?$consult->due_at->format('d/m/Y H:i'):'N/D'}}<br><small>{{$overdue?'Tiempo vencido':'Dentro del tiempo'}}</small></td>
    <td>{{$consult->response_summary?:'Pendiente'}}</td>
    <td class="consult-actions">
     @if(in_array($consult->status,array('REQUESTED','ESCALATED')))<form method="POST" action="{{route('clinical_consults.transition',$consult->id)}}">{{csrf_field()}}<input type="hidden" name="status" value="ACCEPTED"><button class="btn btn-sm btn-primary"><i class="fa fa-check"></i> Aceptar</button></form>@endif
     @if($consult->status=='ACCEPTED')<form method="POST" action="{{route('clinical_consults.transition',$consult->id)}}">{{csrf_field()}}<input type="hidden" name="status" value="IN_REVIEW"><button class="btn btn-sm btn-outline-primary"><i class="fa fa-eye"></i> Iniciar revisión</button></form>@endif
     @if(in_array($consult->status,array('ACCEPTED','IN_REVIEW')))<form method="POST" action="{{route('clinical_consults.transition',$consult->id)}}" class="d-flex">{{csrf_field()}}<input type="hidden" name="status" value="COMPLETED"><input class="form-control form-control-sm consult-response mr-25" name="response_summary" placeholder="Respuesta clínica" required><button class="btn btn-sm btn-success">Responder</button></form>@endif
     @if(!in_array($consult->status,array('COMPLETED','CANCELLED')))<form method="POST" action="{{route('clinical_consults.transition',$consult->id)}}" class="d-flex">{{csrf_field()}}<input type="hidden" name="status" value="CANCELLED"><input class="form-control form-control-sm mr-25" name="reason" placeholder="Motivo de cancelación" required><button class="btn btn-sm btn-outline-danger">Cancelar</button></form>@endif
    </td>
   </tr>
  @empty<tr><td colspan="7" class="text-center text-muted p-2">No hay interconsultas para los filtros seleccionados.</td></tr>@endforelse
  </tbody></table></div><div class="p-1">{{$consults->appends(request()->except('page'))->links()}}</div>
 </div>
</div>
@endsection
