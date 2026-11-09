<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-file-text-o"></i> Prescripción médica estructurada</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label>Paciente</label>
            <input type="text" class="form-control" disabled value="{{$pacienteid->apellido1}} {{$pacienteid->apellido2}} {{$pacienteid->nombre1}} {{$pacienteid->nombre2}}">
        </div>
        <div class="alert alert-info">
            Las órdenes se registran en la receta del episodio para que enfermería pueda verificar medicamento, dosis y vigencia antes de administrar.
        </div>
        @if($prescriptionUrl)
            <a href="{{$prescriptionUrl}}" class="btn btn-primary btn-block">
                <i class="fa fa-file-text-o"></i> Abrir receta y prescripción
            </a>
        @else
            <div class="alert alert-warning mb-0">No se encontró un episodio clínico activo para este paciente.</div>
        @endif
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
    </div>
</div>
