@extends('layouts.app-template')

@section('content')
<style>
.episode-select{padding:1.5rem}.episode-hero{background:linear-gradient(120deg,#075985,#0f9b94);color:#fff;border-radius:16px;padding:1.4rem 1.6rem;margin-bottom:1.2rem}.episode-card{background:#fff;border:1px solid #d7e5ec;border-radius:14px;overflow:hidden;box-shadow:0 7px 24px rgba(15,55,75,.06)}.episode-filter{padding:1rem 1.2rem;background:#eef8fb;border-bottom:1px solid #d7e5ec}.episode-table th{background:#f5f8fa;color:#164e63;font-size:.75rem;text-transform:uppercase}.episode-pill{display:inline-block;border-radius:999px;padding:.3rem .65rem;background:#e0f2fe;color:#075985;font-size:.72rem;font-weight:700}.episode-empty{text-align:center;color:#64748b;padding:2.5rem}.episode-patient{font-weight:700;color:#123b55}@media(max-width:767px){.episode-select{padding:.75rem}.episode-table{min-width:760px}}
</style>
@php $settings=array('AMBULATORY'=>'Ambulatorio','EMERGENCY'=>'Emergencia','INPATIENT'=>'Hospitalización'); @endphp
<div class="episode-select">
 <div class="episode-hero">
  <div class="text-uppercase small font-weight-bold" style="letter-spacing:.08em">Selección segura de episodio</div>
  <h2 class="text-white mb-25"><i class="{{ $icon }}"></i> {{ $title }}</h2>
  <div>{{ $description }}</div>
 </div>
 <div class="episode-card">
  <form method="GET" action="{{ route($targetRoute) }}" class="episode-filter">
   <div class="row align-items-end">
    <div class="col-md-5 mb-50"><label>Paciente</label><input class="form-control" name="patient" value="{{ request('patient') }}" placeholder="Cédula, nombres o apellidos"></div>
    <div class="col-md-4 mb-50"><label>Área clínica</label><select class="form-control" name="care_setting"><option value="">Todas</option>@foreach($settings as $value=>$label)<option value="{{ $value }}" {{ request('care_setting')===$value?'selected':'' }}>{{ $label }}</option>@endforeach</select></div>
    <div class="col-md-3 mb-50"><button class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button> <a class="btn btn-outline-secondary" href="{{ route($targetRoute) }}">Limpiar</a></div>
   </div>
  </form>
  <div class="table-responsive">
   <table class="table episode-table mb-0"><thead><tr><th>Paciente</th><th>Área</th><th>Inicio</th><th>Episodio</th><th>Acción</th></tr></thead><tbody>
   @forelse($encounters as $encounter)
    @php $patientName=trim($encounter->nombre1.' '.$encounter->nombre2.' '.$encounter->apellido1.' '.$encounter->apellido2); @endphp
    <tr>
     <td><span class="episode-patient">{{ $patientName!==''?$patientName:'Paciente sin nombres registrados' }}</span><br><small>{{ $encounter->id_paciente }}</small></td>
     <td><span class="episode-pill">{{ isset($settings[$encounter->care_setting])?$settings[$encounter->care_setting]:$encounter->care_setting }}</span></td>
     <td>{{ $encounter->started_at }}</td><td><small>{{ $encounter->id }}</small></td>
     <td><a class="btn btn-primary btn-sm" href="{{ route($targetRoute,array('encounter_id'=>$encounter->id)) }}">Seleccionar <i class="fa fa-chevron-right"></i></a></td>
    </tr>
   @empty
    <tr><td colspan="5" class="episode-empty"><i class="fa fa-folder-open-o fa-2x"></i><br>No hay episodios activos disponibles para tu alcance clínico.</td></tr>
   @endforelse
   </tbody></table>
  </div>
  <div class="p-1">{{ $encounters->appends(request()->except('page'))->links() }}</div>
 </div>
</div>
@endsection
