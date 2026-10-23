@extends('contable.comp_retencion_clientes.base')
@section('action-content')

  <section class="content">
    <div class="box" style=" background-color: white;">
      <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';">
          <div class="col-md-6">
            <h3 class="box-title">CLIENTES - COMPROBANTE DE RETENCIÓN</h3>
          </div>
          <div class="col-md-2">
            <button type="button" onclick="location.href='{{route('comp_retencion_cliente.crear')}}'" class="btn btn-danger" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;">
                 <i aria-hidden="true"></i>Crear Comprobante de Rentencion Clientes
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
                  <tr >
                    <th>Número_Comprobante</th>
                    <th>Fecha Emision</th>
                    <th>Total Deuda</th>
                    <th>Nuevo Saldo</th>
                  </tr>
                </thead>
                @foreach($ct_comp_retencion as $value) 
                  <tbody>
                      <td>@if(!is_null($value->numero)){{$value->numero}}@endif</td>
                      <td>@if(!is_null($value->fecha)){{$value->fecha}}@endif</td>
                      <td>@if(!is_null($value->total_deudas)){{$value->total_deudas}}@endif</td>
                      <td>@if(!is_null($value->nuevo_saldo)){{$value->nuevo_saldo}}@endif</td>
                  </tbody>
                @endforeach
              </table>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-5">
              <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{count($ct_comp_retencion)}} / {{count($ct_comp_retencion)}} de {{$ct_comp_retencion->total()}} registros</div>
            </div>
            <div class="col-sm-7">
              <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                {{$ct_comp_retencion->links()}}
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
