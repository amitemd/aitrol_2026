@extends('layouts.app-template-h')

@section('content')
<div class="container-fluid" style="padding:24px">
    <div class="card mb-3">
        <div class="card-header" style="background:linear-gradient(90deg,#075486,#17b4c5);color:#fff">
            <h3 style="margin:0"><i class="fa fa-medkit"></i> Administración de medicamentos por horas</h3>
        </div>
        <div class="card-body">
            <p style="margin-bottom:6px"><strong>Episodio:</strong> #{{ $solicitud->id }} · <strong>Paciente:</strong> {{ $solicitud->id_paciente }}</p>
            <p class="text-muted" style="margin:0">Seleccione la hora programada y la hora real. Cada dosis administrada u omitida queda auditada en eMAR y las administradas se trasladan automáticamente al Formulario 022.</p>
        </div>
    </div>

    @include('hospital.hospitalizacion.detalle')
</div>
@endsection
