<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-shield"></i> Administración segura de medicamentos</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label>Paciente</label>
            <input type="text" class="form-control" disabled value="{{$pacienteid->apellido1}} {{$pacienteid->apellido2}} {{$pacienteid->nombre1}} {{$pacienteid->nombre2}}">
        </div>
        <div class="alert alert-info">
            La administración se registra por medicamento y dosis. El eMAR verificará la orden activa, identidad, dosis, vía, hora, responsable, resultado u omisión.
        </div>
        @if($emarUrl)
            <a href="{{$emarUrl}}" class="btn btn-primary btn-block">
                <i class="fa fa-medkit"></i> Abrir administración eMAR
            </a>
        @else
            <div class="alert alert-warning mb-0">
                No se encontró una receta activa vinculada al episodio. Solicite al médico que registre la orden antes de administrar.
            </div>
        @endif
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
    </div>
</div>
