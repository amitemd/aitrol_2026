@extends('contable.chequespost.base')
@section('action-content')
<style type="text/css">
  .autocomplete {
    z-index:999999 !important;
    z-index:999999999 !important;
    z-index:99999999999999 !important;
    position: absolute;
    top: 0px;
    left: 0px;
    float: left;
    display: block;
    min-width: 160px;   
    padding: 4px 0;
    margin: 0 0 10px 25px;
    list-style: none;
    background-color: #ffffff;
    border-color: #ccc;
    border-color: rgba(0, 0, 0, 0.2);
    border-style: solid;
    border-width: 1px;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -webkit-background-clip: padding-box;
    -moz-background-clip: padding;
    background-clip: padding-box; 
  }
  .ui-autocomplete {
    z-index: 5000;
  }
  .ui-autocomplete {
    z-index: 999999;
    list-style:none;
    background-color:#FFFFFF;
    width:300px;
    border:solid 1px #EEE;
    border-radius:5px;
    padding-left:10px;
    line-height:2em;
  }
</style>

<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

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
        <li class="breadcrumb-item"><a href="#">Clientes</a></li>
        <li class="breadcrumb-item active" aria-current="page">Cheques PostFechados</li>
      </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-9">
              <!--<h8 class="box-title size_text">Empleados</h8>-->
              <!--<label class="size_text" for="title">EMPLEADOS</label>-->
              <h3 class="box-title">Cheques Post Fechados</h3>
            </div>

            <div class="col-md-1 text-right">
              <button onclick="location.href='{{route('chequespost.create')}}'" class="btn btn-success btn-gray" >
                <i class="fa fa-file"></i>
                </button>
            </div>
        </div>
        <div class="row head-title">
          <div class="col-md-12 cabecera">
              <label class="color_texto" for="title">BUSCADOR DE CHEQUES</label>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body dobra">
          <form method="POST" id="reporte_master" action="{{ route('chequespost.search') }}" >
            {{ csrf_field() }}
            <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="id">Id: </label>
            </div>
            <div class="form-group col-md-3 col-xs-10 container-4">
              <input class="form-control" type="text" id="id" name="id" value="@if(isset($searchingVals)){{$searchingVals['id']}}@endif" placeholder="Ingrese Id..." />
     
            </div>
            
            <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="cliente">Clientes: </label>
            </div>
            <div class="form-group col-md-3 col-xs-10 container-4">
              <select class="form-control select2" style="width: 100%;" name="id_cliente" id="id_cliente" value="@if(isset($searchingVals)){{$searchingVals['id_cliente']}}@endif">
                <option value="">Seleccione...</option>
                 @foreach($cliente as $value)
                    <option value="{{$value->identificacion}}">{{$value->nombre}}</option>
                 @endforeach
              </select>
            </div>
            <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="secuencia">Secuencia: </label>
            </div>
            <div class="form-group col-md-3 col-xs-10 container-4">
              <input class="form-control" type="text" id="secuencia" name="secuencia" value="@if(isset($searchingVals)){{$searchingVals['secuencia']}}@endif" placeholder="Ingrese Secuencia..." />
            </div>
            <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="detalle">Detalle: </label>
            </div>
            <div class="form-group col-md-3 col-xs-10 container-4">
              <input class="form-control" type="text" id="concepto" name="concepto" value="@if(isset($searchingVals)){{$searchingVals['concepto']}}@endif" placeholder="Ingrese detalle..." />
            </div>

            <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="id_asiento_cabecera">Asiento: </label>
            </div>
            <div class="form-group col-md-3 col-xs-10 container-4">
              <input class="form-control" type="text" id="id_asiento_cabecera" name="id_asiento_cabecera" value="@if(isset($searchingVals)){{$searchingVals['id_asiento_cabecera']}}@endif" placeholder="Ingrese asiento..." />
            </div>


             <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="fecha">Fecha </label>
            </div>
            <div class="form-group col-md-3 col-xs-10 container-4">
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text"  name="fecha" class="form-control fecha" id="fecha" value="@if(isset($searchingVals)){{$searchingVals['fecha']}}@endif">
                  </div>
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
                <label class="color_texto" >LISTADO DE CHEQUES</label>
            </div>
        </div> 
        <div class="box-body dobra">   
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                <div class="table-responsive col-md-12">
                  <table id="example2" class="table-bordered table-hover dataTable table-striped col-md-12" role="grid" aria-describedby="example2_info">
                    <thead>
                      <tr class='well-dark'>
                        <th width="10%"  tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Secuencia</th>
                        <th width="10%"  tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha</th>
                        <th width="10%"  tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Asiento</th>
                        <th width="20%"  tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cliente</th>
                        <th width="22%"  tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Detalle</th>
                        <th width="10%"  tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Total</th>
                        <th width="15%"  tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Acción</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($cheques as $value)
                        <tr>
                          <td>{{$value->secuencia}}</td>
                          <td>{{$value->fecha}}</td>
                          <td>{{$value->id_asiento_cabecera}}</td>
                          <td> @if(($value->cliente)!=null){{$value->cliente->nombre}}@endif</td>
                          <td>{{$value->observaciones}}</td>
                          <td>{{$value->total_ingreso}}</td>
                          <td>
                            <a href="{{route('chequespost.pdf',['id'=>$value->id])}}" class="btn btn-warning btn-gray" target="_blank" rel="noopener noreferrer"><i class="fa fa-file-pdf-o "></i></a>
                            <a class="btn btn-danger btn-gray" href="{{route('comprobante_ingreso.anular',['id'=>$value->id])}}"><i class="fa fa-trash"></i></a>
                            <a class="btn btn-danger btn-gray" href="{{route('chequespost.edit',['id'=>$value->id])}}"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></a>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                    <tfoot>
                    </tfoot>
                  </table>
                  <div class="row">
                    <div class="col-sm-5">
                      <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($cheques->currentPage() - 1) * $cheques->perPage())}} / {{count($cheques) + (($cheques->currentPage() - 1) * $cheques->perPage())}} de {{$cheques->total()}} registros</div>
                    </div>
                    <div class="col-sm-7">
                      <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                        {{ $cheques->appends(Request::only(['id', 'id_cliente', 'secuencia','detalle','fecha']))->links() }}
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
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">
   $(document).ready(function(){
      $('#example2').DataTable({
        'paging'      : false,
        'lengthChange': true,
        'searching'   : true,
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
  $('#fecha').datetimepicker({
    format: 'YYYY-MM-DD',
    });
    $("#nombre_cliente").autocomplete({
      source: function( request, response ) {
          $.ajax( {
          url: "{{route('clientes.nombre_clientes')}}",
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
    function cambiar_nombre(){
      $.ajax({
            type: 'post',
            url:"{{route('clientes.datos_cliente2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'nombre':$("#nombre_cliente").val()},
            success: function(data){
                if(data.value != "no"){
                    $('#id_cliente').val(data.value);
                    $('#direccion').val(data.direccion);
                    buscar_vendedor()
                }else{
                    $('#id_cliente').val("");
                    $('#direccion').val("");
                }

            },
            error: function(data){
                console.log(data);
            }
        });
    }

</script>

@endsection
