@extends('contable.debito_acreedores.base')
@section('action-content')
<style type="text/css">

</style>
<script type="text/javascript">  

$(function () {    
    $(".clickable-row").click(function() {
        window.location = $(this).data("href");
    });
});    
</script>
<div class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="#">Saldos Iniciales</a></li>
        <li class="breadcrumb-item active" aria-current="page">Registro Saldos Iniciales</li>
      </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-9">
              <!--<h8 class="box-title size_text">Empleados</h8>-->
              <!--<label class="size_text" for="title">EMPLEADOS</label>-->
              <h3 class="box-title">SALDOS INICIALES</h3>
            </div>

            <div class="col-md-1 text-right">
              <button onclick="location.href='{{route('saldosinicialesp.index')}}'" class="btn btn-success btn-gray" >
                <i class="fa fa-file"></i>
                </button>
            </div>
        </div>
        <div class="row head-title">
          <div class="col-md-12 cabecera">
              <label class="color_texto" for="title">BUSCADOR DE SALDOS INICIALES</label>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body dobra">
          <form method="POST" id="reporte_master" action="{{ route('saldosinicialesp.search') }}" >
            {{ csrf_field() }}
                <div class="form-group col-md-1 col-xs-1">
                    <label class="texto" for="identificacion">Id</label>
                </div>
                <div class="form-group col-md-2 col-xs-4 container-4" style="padding-left: 15px;">
                      <input class="form-control" type="text" id="id" name="id" value="@if(isset($searchingVals)){{$searchingVals['id']}}@endif"  placeholder="Ingrese Identificación..."  />
                </div>
                <div class="form-group col-md-2 col-xs-2">
                    <label class="texto" for="identificacion">Proveedor: </label>
                   
                </div>
                <div class="form-group col-md-2 col-xs-4 container-4" style="padding-left: 15px;">
                  <select class="form-control select2_find_proveedor" name="proveedor" id="proveedor" style="width: 100%;">
                  </select>
                </div>
                <div class="form-group col-md-2 col-xs-2">
                    <label for="" >Concepto</label>
                </div>
                <div class="form-group col-md-2">
                    <input type="text" class="form-control" name="observacion" id="observacion" value="@if(isset($searchingVals)){{$searchingVals['observacion']}}@endif"  >
                </div>
                <div class="col-xs-1">
                  <button type="submit" id="buscarEmpleado" class="btn btn-primary btn-gray">
                      <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
                  </button>
                </div>
          </form>
        </div>
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto" >LISTADO DE SALDOS INICIALES</label>
            </div>
        </div> 
        <div class="box-body dobra">
          <div class="form-group col-md-12">
            <div class="form-row">
                <div id="resultados">
                </div>  
                <div id="contenedor">
                  <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                      <div class="row">
                        <div class="table-responsive col-md-12">
                          <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                            <thead>
                              <tr class='well-dark'>
                                <th width="10%"  tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Id</th>
                                <th width="40%"  tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Concepto</th>
                                <th width="20%"  tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha</th>
                                <th width="10%"  tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Proveedor</th>
                                <th width="10%"  tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Valor</th>
                                <th width="10%"  tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Acción</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach ($compras as $value)
                                @php $cabecera= DB::table('ct_asientos_cabecera')->where('id',$value->id_asiento_cabecera)->first(); @endphp
                                <tr  class="well">
                                    <td>{{$value->id}}</td>
                                  <td>  @if(!is_null($cabecera) && $cabecera!='[]') {{$cabecera->observacion}} @endif</td>
                                  <td> @if(!is_null($value->fecha)) {{$value->fecha}} @endif</td>
                                  <td>@if(isset($value->proveedorf)){{$value->proveedorf->nombrecomercial}}@endif</td>
                                  <td>{{$value->total_final}}</td>                                
                                  <td>  @if(($value->estado)!=-1) <a href="{{route('saldosinicialesp.anular',['id'=>$value->id])}}" class="btn btn-warning btn-gray"><i class="fa fa-trash"></i></a> @else Anulada @endif  <a href="{{ route('saldos_inicialesp.edit', ['id' => $value->id]) }}" class="btn btn-success btn-gray "><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></a> </td>
                                  
                                </tr>
                              @endforeach
                            </tbody>
                            <tfoot>
                            </tfoot>
                          </table>
                        </div>
                      </div>
                      <div class="row">
                          <div class="col-sm-5">
                            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($compras->currentPage() - 1) * $compras->perPage())}} / {{count($compras) + (($compras->currentPage() - 1) * $compras->perPage())}} de {{$compras->total()}} registros</div>
                          </div>
                          <div class="col-sm-7">
                            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                              {{ $compras->appends(Request::only(['proveedor', 'id', 'observacion','no_cheque','fecha_cheque']))->links() }}
                            </div>
                      </div>
                  </div>

                  </div>
                </div>
            </div>
          </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/hospital/cleave/dist/cleave.min.js")}}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">
   $(document).ready(function(){
      $('#example2').DataTable({
        'paging'      : false,
        'lengthChange': true,
        'searching'   : false,
        'ordering'    : false,
        'info'        : false,
        'autoWidth'   : true,
        'sInfoEmpty':  true,
        'sInfoFiltered': true,
        'language': {
              "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
          }
        });

  });
  $('.select2').select2({
            tags: false
        });
  $("#nombre_proveedor").autocomplete({
    source: function( request, response ) {
        $.ajax( {
        url: "{{route('compra_buscar_nombreproveedor')}}",
        dataType: "json",
        data: {
            term: request.term
        },
        success: function( data ) {
            response(data);
        }
        } );
    },
    minLength: 2,
    } );

  $('.select2_find_proveedor').select2({
      placeholder: "Escriba el nombre del proveedor",
      allowClear: true,
      ajax: {
              url: '{{ route('compras.proveedorsearch') }}',
          data: function(params) {
              var query = {
                  search: params.term,
                  type: 'public'
              }
              return query;
          },
          processResults: function(data) {
              // Transforms the top-level key of the response object from 'items' to 'results'
              console.log(data);
              return {
                  results: data
              };
          }
      }
  });

</script>

@endsection
