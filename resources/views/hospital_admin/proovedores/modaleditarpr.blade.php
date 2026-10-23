<div class="modal-content">
  <div  class="modal-header">
    <h4 class="modal-title">Editar Provedores</h4>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
  </div>
  <form enctype="multipart/form-data" method="POST" action="{{route('hospital_admin.updatep', ['id' => $proovedorid->id])}}">
    <div class="modal-body">
      {{ csrf_field() }}
      <div class="form-group row">
        <label class="col-sm-4 col-sm-label">Logo</label>
        <div class="col-sm-8">
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
        <label class="col-sm-4 col-sm-label">RUC</label>
        <div class="col-sm-8">
          <input type="text" id="ruc" class="form-control" name="ruc" value="{{$proovedorid->ruc}}" required>
        </div>
      </div>
    
      <div class="form-group row">
        <label class="col-sm-4 col-sm-label">Razón Social</label>
        <div class="col-sm-8">
          <input type="text" id="razon" class="form-control" name="razon" value="{{$proovedorid->razonsocial}}" required>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-4 col-sm-label">Nombre Comercial</label>
        <div class="col-sm-8">
          <input type="text" id="nombre" name="nombre" class="form-control" value="{{$proovedorid->nombrecomercial}}" required>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-4 col-sm-label">Email</label>
        <div class="col-sm-8">
          <input type="email" class="form-control" name="emails" value="{{$proovedorid->email}}" required>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-4 col-sm-label">Tipo Proveedor</label>
        <div class="col-sm-8">
          <select name="tipop" id="tipop" class="form-control" onchange="cargarinput(this.value);" required>
            <option @if(($proovedorid->tipop)==1) Selected @endif value="1">Takeda Mexico</option>
            <option @if(($proovedorid->tipop)==2) Selected @endif value="2">Roche</option>
            <option @if(($proovedorid->tipop)==3) Selected @endif value="3">ICN Farmacéutica</option>
            <option @if(($proovedorid->tipop)==4) Selected @endif value="4"> ICN Farmacéutica</option>
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

