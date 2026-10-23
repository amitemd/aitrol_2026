@extends('archivo_plano.procedimientos.base')
@section('action-content')


<!-- Main content -->
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
        <div class="col-sm-6">
          <h3 class="box-title" style="margin-top: 7px">Crear Items</h3>
        </div><br>
        <hr>

      </div>
    </div>
    <!-- /.box-header -->
    <div class="content">
      <form method="POST" action="{{url('administracion/procedimientos/frmcrear')}}">
        {{ csrf_field() }}
        <div class="row">
          <div class="col-md-4">
            <label>Tipo</label><br>

            <select name="txttipo" class="form-control">
              @foreach($tipo as $value)
              <option value="{{$value->tipo}}">{{$value->tipo}}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label>Código</label><br>
            <input type="text" name="txtcodigo" placeholder="Código" class="form-control">
          </div>
          <div class="col-md-4">
            <label>Descripción</label><br>
            <input type="text" name="txtdescripcion" placeholder="Descripción" class="form-control">
          </div>
          <div class="col-md-4"><br>
            <label>Cantidad</label><br>
            <input type="number" name="txtcantidad" value="1" min="1" step="any" required class="form-control">
          </div>
          <div class="col-md-4"><br>
            <label>Valor</label><br>
            <input type="text" name="txtvalor" placeholder="Valor" class="form-control">
          </div>
          <div class="col-md-4"><br>
            <label>IVA</label><br>
            <select name="txtiva" class="form-control"><option value="0">No</option><option value="1">Sí</option></select>
          </div>
          <div class="col-md-6"><br>
            <label>Estado</label><br>
            <select name="txtestado" class="form-control">
              <option value="1">Activo</option>
              <option value="0">Inactivo</option>
            </select>
          </div>
          <div class="col-md-12"><br>
            <input type="submit" name="" value="Guardar" class="btn btn-success">
          </div>
        </div>
      </form>

    </div>
    <!-- /.box-body -->
  </div>
</section>
<!-- /.content -->
</div>

@endsection
