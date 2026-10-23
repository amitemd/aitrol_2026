<div class="modal-content">
  <div class="modal-header">
    <h4 class="modal-title">Editar Marcas</h4>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
  </div>
  <form class="form-vertical" role="form" method="GET" action="{{route('hospital_admin.updatema', ['id' => $marcasid->id])}}">
    <div class="modal-body">
      {{ csrf_field() }}
      <div class="form-group row">
        <label class="col-sm-3 col-sm-label">Nombre</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" name="nombre" value="{{$marcasid->nombre}}">
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-sm-label">Descripción</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" name="descripcion" required maxlength="200" value="{{$marcasid->descripcion}}">
        </div>
      </div>
     
      <div class="from-group row">
        <label class="col-sm-3 col-sm-label">Estado</label>
        <div class="col-sm-9">
          <select name="estado" id="estado" class="form-control">
            <option @if(($marcasid->estado)==1) Selected @endif value="1">ACTIVO</option>
            <option @if(($marcasid->estado)==2) Selected @endif value="2">INACTIVO</option>
          </select>
        </div>
      </div>

    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="far fa-trash-alt"></i> Cancelar</button>
      <button type="submit" class="btn btn-primary"><i class="far fa-save"></i> Guardar</button>
    </div>
  </form>
</div>
