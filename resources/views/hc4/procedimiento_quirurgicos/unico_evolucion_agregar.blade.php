<div class="box hc4-surgical-evolution-card" id="evolucion_eliminar{{$evolucion->id}}">
    <div class="box-header with-border hc4-surgical-evolution-header">
        <div class="hc4-surgical-evolution-header-grid">
            <button type="button" class="hc4-surgical-action" onclick="editar_evolucion({{$evolucion->id}}, '{{$id_paciente}}');">
                <i class="fa fa-pencil-square-o" aria-hidden="true"></i><span>Editar</span>
            </button>
            <span class="hc4-surgical-header-title">Detalles de la evolución</span>
            <button type="button" class="hc4-surgical-action hc4-surgical-action--danger" onclick="eliminar_evolucion({{$evolucion->id}}, '{{$evolucion->hc_id_procedimiento}}');" title="Eliminar evolución" aria-label="Eliminar evolución">
                <i class="fa fa-trash" aria-hidden="true"></i><span class="sr-only">Eliminar</span>
            </button>
            <div class="box-tools">
                <button type="button" class="btn btn-info btn-sm hc4-surgical-toggle" data-widget="collapse" title="Alternar evolución" aria-label="Alternar evolución">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="box-body" style="font-size: 11px;font-family: 'Helvetica general3';" id="evolucion{{$evolucion->id}}">
        <div class="row">
            <div class="col-12"><span style="font-family: 'Helvetica general';">Motivo</span></div>
            <div class="col-12"><span>{{$evolucion->motivo}}</span></div>
            <div class="col-12">&nbsp;</div>
            <div class="col-12"><span style="font-family: 'Helvetica general';">Detalle</span></div>
            <div class="col-12"><span>{!! $evolucion->cuadro_clinico !!}</span></div>
        </div>
    </div>
</div>
<div class="col-md-12" style="height: 5px;"></div>
