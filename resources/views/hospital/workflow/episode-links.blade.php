@if(isset($solicitud) && in_array((int) $solicitud->estado_paso, [1, 2, 3, 5, 6, 7]))
<div class="d-flex flex-wrap mb-2" role="navigation" aria-label="Episodio hospitalario">
    <a href="{{route('hospital.documents.index', ['id_solicitud' => $solicitud->id])}}" class="btn btn-info btn-sm mr-2 mb-1"><i class="fa fa-folder-open"></i> Documentos</a>
    @if(in_array((int) $solicitud->estado_paso, [3, 6, 7], true))
        <a href="{{route('epicrisis.mostrar_final', ['id_solicitud' => $solicitud->id])}}" class="btn btn-success btn-sm mr-2 mb-1"><i class="fa fa-sign-out"></i> Alta y salida</a>
    @endif
    @if(in_array((int) $solicitud->estado_paso, [1, 2]))
    <a href="{{route('hospital.emergencia', ['solicitud' => $solicitud->id])}}" class="btn btn-outline-primary btn-sm mb-1"><i class="fa fa-arrow-left"></i> Emergencia</a>
    @endif
</div>
@endif
