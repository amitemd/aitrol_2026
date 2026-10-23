<div class="modal-content">
  <div class="modal-header">
    <h4 class="modal-title">Editar tipo de prodocto</h4>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
  </div>
  <form class="form-vertical" role="form" method="GET" action="{{route('hospital_admin.updatematipo', ['id' => $tipop->id])}}">
    <div class="modal-body">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">

      <div class="form-group row">
        <label class="col-sm-2 col-form-label col-form-label-sm">Nombre</label>
        <div class="col-sm-10">
          <input type="text" class="form-control form-control-sm" name="nombre" id="nombre" value="{{$tipop->nombre}}">
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-2 col-form-label col-form-label-sm">Descripci&oacute;n</label>
        <div class="col-sm-10">
          <input type="text" class="form-control form-control-sm" name="descripcion" id="descripcion" value="{{$tipop->descripcion}}"">
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-2 col-form-label col-form-label-sm">Estado</label>
        <div class="col-sm-10">
          <select class="select form-control" id="estado" name="estado">
            <option @if(($tipop->estado)==1) Selected @endif value="1">ACTIVO</option>
            <option @if(($tipop->estado)==2) Selected @endif value="2">INACTIVO</option>
          </select>
        </div>
      </div>

    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="far fa-trash-alt"></i> Cancelar</button>
      <button type="submit" class="btn btn-primary"><i class="far fa-edit"></i> Editar</button>
    </div>
  </form>
</div>
