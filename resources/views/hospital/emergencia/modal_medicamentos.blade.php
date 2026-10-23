<div class="modal-content">

    <div class="modal-header">
        <h5 class="modal-title" id="mymodalTratamiento">Medicamento:</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <form action="{{route('hospital.editar_medi',$dato->id)}}" method="POST"  id="formulario">
        {{ csrf_field() }}
        <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                  <div class="form-row">
                    <div class="form-group col-md-4">
                      <label>Medicamento:</label>
                      <input type="text" class="form-control" name="medicamento" id="medicamento" value="{{$dato->medicamento}}">
                    </div>
                    <div class="form-group col-md-4">
                      <label >Posologia:</label>
                      <input type="text" class="form-control" name="posologia" id="posologia" value="{{$dato->posologia}}">
                    </div>
                    <div class="form-group col-md-4">
                      <label >Indicaciones Medicinas:</label>
                      <input type="text" class="form-control" name="indicaciones_medicinas" id="indicaciones_medicinas" value="{{$dato->indicaciones_medicinas}}">
                    </div>
                  </div>
                    <div class="form-row">
                    <div class="form-group col-md-3">
                      <label>Cantidad:</label>
                      <input type="text" class="form-control" name="cantidad_medicinas" id="cantidad_medicinas" value="{{$dato->cantidad_medicinas}}">
                    </div>
                    <div class="form-group col-md-3">
                      <label >Nombre:</label>
                      <input type="text" class="form-control" name="nombre_medicina" id="nombre_medicina" value="{{$dato->nombre_medicina}}">
                    </div>
                    <div class="form-group col-md-3">
                      <label >Presentaci&oacute;n:</label>
                      <input type="text" class="form-control" name="presentacion_medicamento" id="presentacion_medicamento"  value="{{$dato->presentacion_medicamento}}">
                    </div>
                     <div class="form-group col-md-3">
                      <label >Concentraci&oacute;n:</label>
                      <input type="text" class="form-control" name="concentracion_medicamento" id="concentracion_medicamento" value="{{$dato->concentracion_medicamento}}">
                    </div>
                  </div>
                   <div class="form-row">
                    <div class="form-group col-md-2">
                      <label>Dosis:</label>
                      <input type="text" class="form-control" name="dosis_medicamento" id="dosis_medicamento" value="{{$dato->dosis_medicamento}}">
                    </div>
                    <div class="form-group col-md-2">
                      <label >Unidad:</label>
                      <input type="text" class="form-control"name="unidad_medicamento" id="unidad_medicamento" value="{{$dato->unidad_medicamento}}">
                    </div>
                    <div class="form-group col-md-2">
                      <label >Via:</label>
                      <input type="text" class="form-control" name="via_medicamento" id="via_medicamento" value="{{$dato->via_medicamento}}">
                    </div>
                     <div class="form-group col-md-3">
                      <label >Frecuencia:</label>
                      <input type="text" class="form-control" name="frecuencia_medicamento" id="frecuencia_medicamento" value="{{$dato->frecuencia_medicamento}}">
                    </div>
                    <div class="form-group col-md-3">
                      <label >Duraci&oacute;n:</label>
                      <input type="text" class="form-control" name="duracion_medicamento" id="duracion_medicamento" value="{{$dato->duracion_medicamento}}">
                    </div>
                  </div>
                  </div>
              </div>
            </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="far fa-trash-alt"></i> Cancelar</button>
            <button type="sumit" class="btn btn-primary active"><i class="far fa-edit"></i> Editar</button>
        </div>
    </form>
   
</div>