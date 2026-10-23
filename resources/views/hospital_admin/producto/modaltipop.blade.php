<div class="modal-content">
  <div class="modal-header">
    <h4 class="modal-title">Agregar tipo de medicina</h4>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
  </div>
  <form action="{{route('hospital_admin.agregartipo')}}" enctype="multipart/form-data" method="POST">
    <div class="modal-body">
      {{ csrf_field() }}

      <div class="form-group row">
        <label class="col-sm-3 col-sm-label">Nombre</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" name="nombre" required maxlength="50">
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-sm-label">Descripción</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" name="descripcion">
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-sm-label">Estado</label>
        <div class="col-sm-9">
          <select name="estado" id="estado" class="form-control">
            <option value="1">ACTIVO</option>
            <option value="2">INACTIVO</option>
          </select>
        </div>
      </div>

    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="far fa-trash-alt"></i> Cancelar</button>
      <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Agregar</button>
    </div>
  </form>
</div>