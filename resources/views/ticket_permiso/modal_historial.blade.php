<input type="hidden" name="id_user" name="id_user" value="{{ $permiso->id }}">
<div class="modal-header" style="text-align:center">
    <h5 class="modal-title" style="font-weight: bold">HISTORIAL DOCUMENTACIÓN</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="form-group row">
        <label for="nombre" class="col-sm-2 col-form-label">Nombre</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="nombre" name="nombre" readonly
                value="{{ $permiso->nombre->nombre1 }} {{ $permiso->nombre->nombre2 }} {{ $permiso->nombre->apellido1 }} {{ $permiso->nombre->apellido2 }}">
        </div>
    </div>

    @php
        $contador = 0;
    @endphp
    @if($permiso->detelle != null)
    @foreach ($permiso->detelle as $value)
        @if ($contador <= 3)
            <div class="form-group row">
                <label for="observaciones" class="col-sm-2 col-form-label">Observación</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="nombre" name="nombre"
                        value="{{ $value->observacion_anterior }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label for="observaciones" class="col-sm-2 col-form-label">Justifiación</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="nombre" name="nombre"
                        value="{{ $value->justificacion_de_rrhh }}" readonly>
                </div>
            </div>

            <div class="form-group row">
                <label for="fecha" class="col-sm-2 col-form-label">Fecha</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="fecha" name="fecha"
                        value="{{ $value->created_at }}" readonly>
                </div>
            </div>
            @if (!is_null($value->path_anterior))
                <div class="form-group row">
                    <label for="imagen" class="col-sm-2 col-form-label">Imagen</label>
                    <div class="col-sm-10">
                        <a target="_blank" class="btn btn-warning"
                            href="{{ route('ticketpermisos.ticketpermisos', ['id' => $value->id]) }}">Documentación
                            Respaldo</a>
                    </div>
                </div>
            @endif
            <hr>
        @endif
        @php
            $contador++;
        @endphp
    @endforeach
    @endif


</div>
