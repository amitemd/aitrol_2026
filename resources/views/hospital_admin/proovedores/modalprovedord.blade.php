<div class="modal-content">
  <div  class="modal-header">
    <h4 class="modal-title">Crear Proveedores</h4>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
  </div>
  <form action="{{route('hospital_admin.registropro')}}" enctype="multipart/form-data" method="POST">
    <div class="modal-body">
        {{ csrf_field() }}
        <div class="form-group row">
          <label class="col-sm-3 col-sm-label">Nombre</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" name="name" required>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-sm-label">Descripción</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" name="descri">
          </div>
        </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="far fa-trash-alt"></i> Cancelar</button>
      <button type="submit" class="btn btn-primary"><i class="far fa-save"></i> Guardar</button>
    </div>
  </form>
</div>