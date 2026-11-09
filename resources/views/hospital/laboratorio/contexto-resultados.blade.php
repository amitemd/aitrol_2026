@if(isset($hospitalEpisode))
    <div class="alert alert-info" style="margin:15px">
        <strong>Laboratorio del episodio #{{ $hospitalEpisode->id }} · Orden #{{ $orden->id }}</strong>
        <p>Los resultados guardados se muestran automáticamente en el formulario 010B, con su estado de certificación.</p>
        <a class="btn btn-primary btn-sm" style="color:#fff" href="{{ route('hospital.laboratory.index', ['id_solicitud' => $hospitalEpisode->id, 'form' => '010B']) }}">Volver al laboratorio del episodio</a>
        <a class="btn btn-default btn-sm" target="_blank" rel="noopener" href="{{ route('hospital.laboratory.pdf', ['id_solicitud' => $hospitalEpisode->id, 'order' => $orden->id, 'code' => '010B']) }}">Ver informe 010B actualizado</a>
    </div>
@endif
