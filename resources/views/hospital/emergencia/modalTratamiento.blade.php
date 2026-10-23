<div class="modal-content">

    <div class="modal-header">
        <h5 class="modal-title" id="mymodalTratamiento">Medicamento:</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <form action="{{ route('guardar.tratamiento')}}" method="post">
        {{ csrf_field() }}
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-md-5">
                    <label>Nombre</label>
                    <input type="text" class="form-control"name="nombre" id="nombre">
                </div>
                <div class="form-group col-md-5">
                    <label>Presentación</label>
                    <input type="text" class="form-control"name="presentacion" id="presentacion">
                </div>
                <div class="form-group col-md-2">
                    <label>Cantidad</label>
                    <input type="number" class="form-control" name="cantidad" id="cantidad">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-5">
                    <label>Concetración</label>
                    <input type="text" class="form-control"name="concentracion" id="concentracion">
                </div>
                <div class="form-group col-md-5">
                    <label>Dosis</label>
                    <input type="text" class="form-control" name="dosis" id="dosis">
                </div>
                <div class="form-group col-md-2">
                    <label>Unidad</label>
                    <select class="form-control" name="unidad" id="unidad">
                        <option>ml</option>
                        <option>g</option>
                        <option>mg</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Via</label>
                    <input type="text" class="form-control" name="via" id="via">
                </div>
                <div class="form-group col-md-6">
                    <label>Frencuencia</label>
                    <input type="text" class="form-control" name="frecuencia" id="frecuencia">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Duracion</label>
                    <input type="text" class="form-control" name="duracion" id="duracion">
                </div>
                <div class="form-group col-md-6">
                    <label>Indicaciones Medicinas</label>
                    <input type="text" class="form-control" name="indicaciones_medicinas" id="indicaciones_medicinas">
                </div>
            </div>
        </div>
    
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="far fa-trash-alt"></i> Cancelar</button>
            <button type="sumit" class="btn btn-primary active"><i class="far fa-edit"></i> Guardar</button>
        </div>

    </form>
    
</div>