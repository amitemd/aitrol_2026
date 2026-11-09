@if(isset($pendingHospitalTransfers) && $pendingHospitalTransfers->count())
<div class="table-responsive mb-3">
    <h4>Recepciones pendientes</h4>
    <table class="table table-sm table-bordered">
        <thead><tr><th>Paciente</th><th>Sala receptora</th><th>Solicitud</th><th></th></tr></thead>
        <tbody>@foreach($pendingHospitalTransfers as $pending)
            <tr>
                <td>{{ $pending->id_paciente }} - {{ $pending->paciente ? mb_strtoupper($pending->paciente->apellido1.' '.$pending->paciente->nombre1, 'UTF-8') : '' }}</td>
                <td>{{ $pending->sala ? mb_strtoupper($pending->sala->nombre_sala, 'UTF-8') : '' }}</td><td>{{ $pending->fecha }}</td>
                <td>@if((int)$pending->paso === 5)
                    <form method="post" action="{{ route('hospital.workflow.surgery.receive', ['transfer' => $pending->id]) }}">@csrf
                        <input type="hidden" name="id_paciente" value="{{ $pending->id_paciente }}">
                        <button class="btn btn-primary btn-sm"><i class="fas fa-check"></i> Confirmar recepcion</button>
                    </form>
                @else
                    <a class="btn btn-primary btn-sm" href="{{ route('hospital.gcuartos', ['traspaso' => $pending->id]) }}"><i class="fas fa-bed"></i> Asignar cama</a>
                @endif</td>
            </tr>
        @endforeach</tbody>
    </table>
</div>
@endif
