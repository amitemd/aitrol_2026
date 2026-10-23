<div class="modal-content">
  <div class="modal-header">
    <h4 class="modal-title">AGREGAR PRODUCTOS</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <form  action="{{route('hospital_admin.agregarprodu')}}" enctype="multipart/form-data" method="POST">
    <div class="modal-body">
      {{ csrf_field() }}
      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Codigo</label>
        <div class="col-sm-9">
          <input type="text" class ="form-control" name="codigo" required maxlength="13">
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Nombre</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" name="nombre" require maxlength="50">
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Descripción</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" name="descripcion" require maxlength="200" >
        </div>
      </div>
      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Indicaciones Medicina</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" name="indicaciones_medicina" require maxlength="200" >
        </div>
      </div>
       <div class="form-group row">
        <label class="col-sm-3 col-form-label">Forma de Despacho</label>
        <div class="col-sm-9">
          <select name="estado" id="estado" class="form-control">
            <option value="0">Activo</option>
            <option value="1">Inactivo</option>
          </select>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Medida</label>
        <div class="col-sm-9">
          <select name="medida" id="medida" class="form-control">
            <option value="Uni">Unidad</option>
            <option value="Kg">Kilogramos</option>
            <option value="G">Gramos</option>
            <option value="Mg">Miligramos</option>
            <option value="Ml">Mililitros</option>
            <option value="L">Litros</option>
            <option value="Lb">Libras</option>
            <option value="m">Metros</option>
            <option value="cm">Centimetros</option>
            <option value="mm">Milimetros</option>
          </select>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-sm-label">Forma de Despacho</label>
        <div class="col-sm-9">
          <select name="despacho" id="despacho" class="form-control">
            <option value="1">Código de Serie</option>
            <option value="2">Código de Producto</option>
          </select>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-sm-label">Stock Minimo</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" name="minimo" require maxlength="13">
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-sm-label">Registro Sanitario</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" name="registro" require maxlength="13">
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-sm-label">Marcas</label>
        <div class="col-sm-9">
          <select name="marcas" id="marcas" class="form-control">
            @foreach($marcas as $value)
            <option value="{{$value->id}}">{{$value->nombre}}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-sm-label">Tipo de Producto</label>
        <div class="col-sm-9">
          <select name="tipop" id="tipop" class="form-control">
            @foreach($tipop as $value)
              <option value="{{$value->id}}">{{$value->nombre}}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-sm-label">Cantidad de Usos</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" name="usos" require maxlength="15">
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-sm-label">IVA</label>
        <div class="col-sm-9">
          <select name="iva" id="iva" class="form-control">
              <option value="0">SI</option>
              <option value="1">NO</option>
          </select>
        </div>
      </div>

    </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary active" style=" border-radius: 10px;">AGREGAR</button>
    </div>
  </form>
</div>
