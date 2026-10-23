@extends('contable.deposito_banca_fact_ventas.base')
@section('action-content')

  <section class="content">
    <div class="box" style=" background-color: white;">
      <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';">
          <div class="col-md-6">
            <h3 class="box-title">Depósito Bancario</h3>
          </div>
          <div class="col-md-2">
            <button type="button" onclick="location.href='{{route('depo_bancario_factventas.crear')}}'" class="btn btn-danger" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;">
                 <i aria-hidden="true"></i>Crear Depósito Bancario
            </button>
          </div>
      </div>
      <div class="box-body">
      <div class="col-md-12">
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
          <div class="row">
            <div class="table-responsive col-md-12">
              <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                <thead>
                  <tr>
                    <th>Número</th>
                    <th>Tipo</th>
                    <th>Fecha</th>
                    <th>Acción</th>
                  </tr>
                </thead>
                @foreach($ct_deposito as $value)
                <tbody>
                  <td>@if(!is_null($value->numero)){{$value->numero}}@endif</td>
                  <td>@if(!is_null($value->tipo)){{$value->tipo}}@endif</td>
                  <td>@if(!is_null($value->fecha)){{$value->fecha}}@endif</td>
                  <td><a href="" class="btn btn-primary">Visualizar</a></td>
                </tbody>
                @endforeach
              </table>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-5">
              <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{count($ct_deposito)}}/ {{count($ct_deposito)}} de {{$ct_deposito->total()}} registros</div>
            </div>
            <div class="col-sm-7">
              <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                {{$ct_deposito->links()}}
              </div>
            </div>
          </div>
        </div>
      </div>
      </div>
    </div>
  </section>

<script type="text/javascript">
 
    $(document).ready(function(){

      $('#example2').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : false,
        'autoWidth'   : false
      });

    });
</script>
@endsection
