@extends('hospital_admin.base')
@section('action-content')

<a href="{{route('hospital_admin.ingresopedido')}}" class="btn btn-sm btn-info my-2"><i class="fas fa-plus"></i> Ingreso de Pedido</a>
<a href="{{route('hospital_admin.farmacia') }}" class="btn btn-sm btn-primary my-2"><i class="far fa-arrow-alt-circle-left"></i> Regresar</a>

<div class="row">
  <div class="col-md-12">

    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Lista de Productos</h6>
      </div>
      <div class="card-body">

        <div class="row">
          <div class="col-md-12">
            <form>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Buscar pedido</label>
                <div class="col-sm-4">
                  <input type="password" class="form-control" id="inputPassword" placeholder="Número de pedido">
                </div>
                <a type="button" href="#" class="btn btn-sm btn-primary"><i class="fas fa-search"></i> Buscar</a>
              </div>
            </form>
          </div>
        </div>

        <div class="row">

          <div class="table-responsive col-md-12">
            <table id="dtBasicExample" class="table table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr>
                <th> Fecha</th>
                  <th >Proveedor</th>
                  <th >Numero de Pedido</th>
                  <th >Realizado por</th>
                  <th >Items Totales</th>
                  <th >Total de Productos Restantes</th>
                  <th >Acción</th>
                </tr>
              </thead>
              <tbody>

              </tbody>
            </table>
          </div>
          
        </div>

      </div>
    </div>

  </div>
</div>

<script type="text/javascript">
  $('#dtBasicExample').DataTable({
    'paging'      : false,
    'lengthChange': false,
    'searching'   : false,
    'ordering'    : true,
    'info'        : false,
    'autoWidth'   : false,
  })
</script>
@endsection