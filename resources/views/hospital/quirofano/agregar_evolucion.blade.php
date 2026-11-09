<div class="surgery-evolution-card">
    <div class="evolution-card-header">
        <button class="btn btn-warning btn-xs" type="button" onclick="editar_evolucion({{$evolucion->id}}, '{{(string) $id_paciente}}');" aria-label="Editar evolución">
            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
        </button>
        <strong>{{trim(strip_tags($evolucion->motivo)) ?: 'Nueva evolución'}}</strong>
        <button class="btn btn-info btn-xs" type="button" data-toggle="collapse" data-target="#surgeryEvolution{{$evolucion->id}}" aria-expanded="true" aria-controls="surgeryEvolution{{$evolucion->id}}" title="Mostrar u ocultar evolución">
            <i class="fa fa-minus" aria-hidden="true"></i>
        </button>
    </div>
    <div class="collapse show evolution-card-body" id="surgeryEvolution{{$evolucion->id}}">
        <div id="evolucion{{$evolucion->id}}">
            <div><strong>Motivo</strong></div>
            <div>{{strip_tags($evolucion->motivo)}}</div>
            <div class="mt-1"><strong>Detalle</strong></div>
            <div>{!! strip_tags($evolucion->cuadro_clinico, '<p><br><strong><b><em><i><ul><ol><li>') !!}</div>
        </div>
    </div>
</div>
