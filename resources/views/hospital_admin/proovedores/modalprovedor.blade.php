<div class="modal-content">
  <div  class="modal-header">
    <h4 class="modal-title">Agregar Provedores</h4>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
  </div>
  <form  action="{{route('hospital_admin.registro')}}" enctype="multipart/form-data" method="POST">
    <div class="modal-body">
      {{ csrf_field() }}
      <div class="form-group row">
        <label class="col-sm-3 col-sm-label">Logo</label>
        <div class="col-sm-9">
          <input type="hidden" name="logo">    
          {{ csrf_field() }}
          <input type="file" name="imagen" id="imagen" class="archivo form-control" required>
          @if ($errors->has('archivo'))
            <span class="help-block">
              <strong>{{ $errors->first('archivo') }}</strong>
            </span>
          @endif
        </div>
      </div>
  
      <div class="form-group row">
        <label class="col-sm-3 col-sm-label">Ruc</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" name="ruc" required>
        </div>
      </div>
      
      <div class="form-group row">
        <label class="col-sm-3 col-sm-label">Razón Social</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" name="razon" required>
        </div>
      </div>
    
      <div class="form-group row">
        <label class="col-sm-3 col-sm-label">Nombre Comercial</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" name="nombre" required>
        </div>
      </div>
    
      <div class="form-group row">
        <label class="col-sm-3 col-sm-label">Email</label>
        <div class="col-sm-9">
          <input type="email" class="form-control" name="emails" required>
        </div>
      </div>
      
      <div class="form-group row">
        <label class="col-sm-3 col-sm-label">Tipo de Proveedores</label>
        <div class="col-sm-9">
          <select class="form-control" name="tipop" onchange="cargarinput(this.value);" required>
            <option value="0">TIPO...</option>
            @foreach($hospital_proovedor as $value)
            <option value="{{$value->id}}">{{$value->prescripcion_dr}}</option>
            @endforeach
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
