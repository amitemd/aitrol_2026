<form method="post" action="{{ route('ticketpermisos.subir_documentacion') }}" enctype="multipart/form-data">
    {{ csrf_field() }}
    <input type="hidden" name="id_user" name="id_user" value="{{$permiso->id}}">
    <div class="modal-header" style="text-align:center">
        <h5 class="modal-title">SUBIR DOCUMENTACIÓN</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">

        <div class="form-group row">
            <label for="motivo" class="col-sm-2 col-form-label">Motivo Devolución</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="motivo" name="motivo" readonly
                    value="{{$permiso->justificacion_final}}">
            </div>
        </div>

        <div class="form-group row">
            <label for="nombre" class="col-sm-2 col-form-label">Nombre</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="nombre" name="nombre" readonly
                    value="{{ $permiso->nombre->nombre1 }} {{ $permiso->nombre->nombre2 }} {{ $permiso->nombre->apellido1 }} {{ $permiso->nombre->apellido2 }}">
            </div>
        </div>

        <div class="form-group row">
            <label for="justificacion" class="col-sm-2 col-form-label">Justificación</label>
            <div class="col-sm-10">
                <textarea type="text" class="form-control" name="justificacion" id="justificacion" required></textarea>
            </div>
        </div>
        <div class="form-group row">
            <label for="respaldo" class="col-sm-2 col-form-label">Subir Respaldo</label>
            <div class="col-sm-10">
                <input type="file" class="form-control" id="respaldo" name="respaldo" accept="image/png, image/jpeg">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">
            Guardar
        </button>
    </div>
</form>
