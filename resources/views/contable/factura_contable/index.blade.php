@extends('contable.factura_contable.base')
@section('action-content')
<style>
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
  .button {
      transition-duration: 0.4s;
  }
  .button:hover {
      background-color: red;
      color: white!important;
  }
</style>
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<!-- Ventana modal editar -->

  <!-- Main content -->
  <section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
        <li class="breadcrumb-item"><a href="#">Factura de Gastos</a></li>
        <li class="breadcrumb-item active" aria-current="page">Registro Factura de Contable</li>
      </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-9">
              <!--<h8 class="box-title size_text">Empleados</h8>-->
              <!--<label class="size_text" for="title">EMPLEADOS</label>-->
              <h3 class="box-title">Factura Contable</h3>
            </div>

            <div class="col-md-1 text-right">
              <button onclick="location.href='{{route('fact_contable_crear')}}'" class="btn btn-success btn-gray" >
                <i class="fa fa-file"></i>
                </button>
            </div>
        </div>
        <div class="row head-title">
          <div class="col-md-12 cabecera">
              <label class="color_texto" for="title">BUSCADOR DE FACTURA CONTABLE</label>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body dobra">
          <form method="POST" id="reporte_master" action="{{ route('fact_contable_search') }}" >
            {{ csrf_field() }}
                 <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="id">{{trans('contableM.id')}}</label>
            </div>
            <div class="form-group col-md-3 col-xs-10 container-4">
              <input class="form-control" type="text" id="id" name="id" value="@if(isset($searchingVals)){{$searchingVals['ct_c.id']}}@endif" placeholder="Ingrese Id..." />
            </div>
            <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="id">{{trans('contableM.asiento')}}</label>
            </div>
            <div class="form-group col-md-3 col-xs-10 container-4">
              <input class="form-control" type="text" id="id_asiento_cabecera" name="id_asiento_cabecera" value="@if(isset($searchingVals)){{$searchingVals['id_asiento_cabecera']}}@endif" placeholder="Ingrese Asiento..." />
            </div>
            <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="nombre_proveedor">{{trans('contableM.proveedor')}}: </label>
            </div>
            <div class="form-group col-md-3 col-xs-10 container-4">
              <!--" />-->
              <select class="form-control select2_find_proveedor" name="proveedor" id="proveedor" style="width: 100%;">
                <!-- <option value="">Seleccione...</option>
                @foreach($proveedor as $value)
                    <option @if(isset($searchingVals))@if($searchingVals['proveedor']==$value->id) selected='selected' @endif @endif value="{{$value->id}}">{{$value->razonsocial}}</option>
                @endforeach -->
              </select>
            </div>
            <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="detalle">{{trans('contableM.detalle')}}: </label>
            </div>
            <div class="form-group col-md-3 col-xs-10 container-4">
              <input class="form-control" type="text" id="detalle" name="detalle" value="@if(isset($searchingVals)){{$searchingVals['observacion']}}@endif" placeholder="Ingrese detalle..." />
            </div>
            <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="secuencia_f">{{trans('contableM.secuencia')}}: </label>
            </div>
            @php 
              if(Auth::user()->id == "0957258056"){
                //dd($searchingVals) 
              }
            @endphp

            <div class="form-group col-md-3 col-xs-10 container-4">
              <input class="form-control" type="text" id="secuencia_f" name="secuencia_f" value="@if(isset($searchingVals)){{$searchingVals['ct_c.numero']}}@endif" placeholder="Ingrese Secuencia..." />
            </div>
            <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="tipo">{{trans('contableM.tipo')}}: </label>
            </div>
            <div class="form-group col-md-3 col-xs-10 container-4">
              <select class="form-control select2" style="width: 100%;"  type="text" id="tipo" name="tipo" value="@if(isset($searchingVals)){{$searchingVals['ct_c.tipo_comprobante']}}@endif" >
                <option value="">Seleccione...</option> 
                @foreach($tipo_comprobante as $value)
                        <option value="{{$value->codigo}}">{{$value->nombre}}</option>
                 @endforeach
              </select>
            </div>
             <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="fecha">{{trans('contableM.fecha')}}: </label>
            </div>
            <div class="form-group col-md-3 col-xs-10 container-4">
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text"  name="fecha" class="form-control fecha" id="fecha" value="@if(isset($searchingVals)){{$searchingVals['fecha']}}@endif">
                  </div>
            </div>
            <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="fac_crea">{{trans('contableM.Creolafactura')}}: </label>
            </div>
            <div class="form-group col-md-3 col-xs-10 container-4">
              <input class="form-control" type="text" id="fac_crea" name="fac_crea" value="@if(isset($searchingVals)){{$searchingVals['ct_c.id_usuariocrea']}}@endif" placeholder="Ingrese quien creo la factura..." />
            </div>
                <div class="col-xs-2">
                  <button type="submit" id="buscarEmpleado" class="btn btn-primary btn-gray">
                      <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
                  </button>
                </div>
          </form>
        </div>
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto" >LISTADO DE FACTURAS</label>
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
                          <table id="example2" style="width: 100%;" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                            <thead>
                              <tr class='well-dark'>
                                <th  tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Acción</th>
                                <th  tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.id')}}</th>
                                <th  tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Id Asiento</th>
                                <th  tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.secuencia')}}</th>
                                <th  tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.fecha')}}</th>
                                <th  tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.proveedor')}}</th>
                                <th  tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.autorizacion')}}</th>
                                <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.Comprobante')}}</th>
                                <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.detalle')}}</th>
                                <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.creadopor')}}</th>
                                <!--<th  tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Egresos</th>-->
                                <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.estado')}}</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach ($compras as $value)
                                @php 
                                    $nombre_comprobante = "";
                                    $nombre_comprobante = \Sis_medico\Ct_master_tipos::where('codigo', $value->tipo_comprobante)->where('tipo', '1')->where('estado', '1')->first();
                                @endphp 
                                <tr  class="well">
                                  <td style="text-align: center">
                                    <div class="dropdown">
                                    <button class="btn btn-gray btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-cog"></i> <i class="fa fa-caret-down"></i></button>
                                    <div style="width: auto;" class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @if(!is_null($value->doc_electronico))
                                          @if($value->doc_electronico == 0 and $value->electronica == 1)
                                          <a style="text-align: left; color: black; width: 100px; padding: 5px 0px 5px 15px;" class="btn btn-xs"><i class="fa fa-spinner" aria-hidden="true" title="En proceso"></i>En proceso</a>
                                          @elseif($value->doc_electronico == 5 and $value->electronica == 1)
                                          <a style="text-align: left; color: black; width: 100px; padding: 5px 0px 5px 15px;" href='{{asset("/")}}api_doc_electronico?opcion=descargarXML&clave={{$value->nro_autorizacion}}' class="btn-xs" type="button"><i class="fa fa-file-code-o"></i> Exportar XML </a>
                                          <a style="text-align: left; color: black; width: 100px; padding: 5px 0px 5px 15px;" href='{{asset("/")}}api_doc_electronico?opcion=generarPdf&clave={{$value->nro_autorizacion}}' class="btn-xs" type="button"><i class="fa fa-file-pdf-o"></i> Exportar PDF </a>
                                          @elseif($value->doc_electronico == 7 and $value->electronica == 1)
                                          <a style="text-align: left; color: red; width: 100px; padding: 5px 0px 5px 15px;" type="button" class="btn btn-xs text-center" onclick="verErrorXml(\'' + $value->id + '\')"><i class="fa fa-close" aria-hidden="true" title="Error"></i> Error</a>
                                          @elseif($value->doc_electronico == 9 and $value->electronica == 1)
                                          <a style="text-align: left; color: red; width: 100px; padding: 5px 0px 5px 15px;" type="button"  class="btn btn-xs text-center" onclick="verIErrorTributaria('<?= $value->nro_autorizacion; ?>')" title="Error"><i class="fa fa-close" aria-hidden="true"></i> Error</a>
                                          @endif
                                        @endif
                                        @if($value->rutapdf!='')
                                          <a style="text-align: left; color: black; width: 100px; padding: 5px 0px 5px 15px;" target="_blank" href="{{ route('compras.visualizar', ['id' => $value->id]) }}" class="btn btn-xs"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Exportar PDF</a>
                                        @endif
                                        <a style="text-align: left; color: black; padding: 5px 0px 5px 15px;" class="btn btn-xs" title="Visualizar" href="{{route('fact_contable_editar',['id'=>$value->id])}}"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i> Visualizar</a>
                                        <a  target="_blank" href="{{ asset('/')}}v3/compras/descargarComprobanteCompra?id={{$value->id}}" class="btn btn-xs" style="text-align: left; color: black; padding: 5px 0px 5px 15px;"><i class="fa fa-file-pdf-o"></i> Comprobante de compra</a>
                                        @if($value->estado >0) 
                                        <a style="text-align: left; color: red; width: 100px; padding: 5px 0px 5px 15px;" href="javascript:anular({{$value->id}});" class="btn btn-xs button"><i class="fa fa-trash"></i> Anular</a>
                                        @endif
                                      </div>
                                    </div>
                                  </td>
                                  <td>
                                    {{$value->id}}
                                  </td>
                                  <td>{{$value->id_asiento_cabecera}}</td>
                                  <td >{{$value->numero}}</td>
                                  <td >{{$value->fecha}}</td>
                                  <td >{{$value->razonsocial}}</td>
                                  <td >{{$value->autorizacion}}</td>
                                  <td>@if(!is_null($nombre_comprobante)) {{$nombre_comprobante->nombre}} @endif</td>
                                  <td>@if(!is_null($value->observacion)) {{$value->observacion}} @endif</td>      
                                  <td>{{$value->nombre1}} {{$value->apellido1}} </td>                            
                                  <td >@if($value->estado == '1') {{trans('contableM.activo')}} @elseif($value->estado =='0') Anulada @else Activo @endif</td>
                                </tr>
                              @endforeach
                            </tbody>
                            <tfoot>
                            </tfoot>
                          </table>
                        </div>
                      </div>
                  </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-5">
                  <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('contableM.mostrando')}} {{1 + (($compras->currentPage() - 1) * $compras->perPage())}} / {{count($compras) + (($compras->currentPage() - 1) * $compras->perPage())}} de {{$compras->total()}} {{trans('contableM.registros')}}</div>
                </div>
                <div class="col-sm-7">
                  <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                    {{ $compras->appends(Request::only(['proveedor','detalle' ,'observacion', 'secuencia_factura','ct_c.tipo_comprobante','fecha','id_asiento_cabecera', 'id', 'secuencia_f','fac_crea']))->links() }}
                  </div>
                </div>
            </div>
          </div>
        </div>
    </div>
  </section>
  <!-- /.content -->
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ('/plugins/datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>
<script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
<script type="text/javascript">
    
    $(document).ready(function(){
      $('#example2').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : false,
        'info'        : false,
        'autoWidth'   : false
      });
    });

   $("#buscarnombre").autocomplete({
    source: function( request, response ){
    $.ajax({
      method:'GET',
      url: "{{route('fact_contable_nombre_proveedor')}}",
      dataType: "json",
      data: { term: request.term },
      success: function( data ) {
      response(data);
      }

    });
    
  },
    minLength: 1,
    change: function( event, ui ){
    } 
    });

    $('#fecha').datetimepicker({
    format: 'YYYY-MM-DD',
    });

    $('.select2').select2({
            tags: false
        });

    function anular(id){

      Swal.fire({
          title: `¿{{trans('contableM.anularfactura')}}?`,
          text: `{{trans('contableM.norevertiraccion')}}!`,
          icon: 'error',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Si'
      }).then((result) => {
        if (result.isConfirmed) {
          var acumulate="";

          $.ajax({
            type: 'get',
            url:"{{ route('compras.verificar_anulacion')}}",
            datatype: 'json',
            //data: {'verificar':'1','id_compra': id},
            data: {'verificar':'4','id_compra': id},
            success: function(data){
              //console.log(data+" dsada "+id);
              console.log(data);
               
            if(data.respuesta=="si"){
              //swal("Error!",`Existen algunas ${data.tabla} generados con esta factura`,"error");
              let id_com_egreso = data.id_egreso;
              let enlace = `<a target="_blank" href="{{ url('contable/acreedores/documentos/comprobante/egreso/comprobante/buscar?id=${data.ids[0]}')}}"><b>${data.tablas[0]}</b></a>`;
              let enlace2 = `<a target="_blank" href="{{ url('contable/Banco/debito/acreedores/buscar?id=${data.ids[1]}')}}"> <b>${data.tablas[1]}</b> </a>`;
              let enlace3 = `<a target="_blank" href="{{ url('contable/cruce/valores/buscar?id=${data.ids[2]}')}}"><b>${data.tablas[2]}</b></a>`;
              let enlace4 = `<a target="_blank" href="{{ url('contable/acreedores/documentos/retenciones/buscar?id=${data.ids[3]}')}}"><b>${data.tablas[3]}</b></a>`;
              let texto = `Existen algunos ${enlace} ${enlace2} ${enlace3} ${enlace4} generados con esta factura`;
              
              alertas("error", "Error", texto);
            }else{
              test(id);
            }
            },
            error: function(data){
              console.log(data);
            }
          }); 

        }
    })
       

  }
  async function test(id) {
    try {
      const { value: text } = await Swal.fire({
                      input: 'textarea',
                      inputPlaceholder: 'Ingrese motivo de anulación...',
                      inputAttributes: {
                        'aria-label': 'Ingrese motivo de anulación'
                      },
                      showCancelButton: true
                    })

                    if (text) {
                      $.ajax({
                        type: 'get',
                        url:"{{ url('contable/fact_contable/factura/')}}/"+id,
                        datatype: 'json',
                        data: {'observacion':text},
                        success: function(data){
                          Swal.fire(`{{trans('contableM.correcto')}}!`,`{{trans('contableM.anulacioncorrecta')}}`,"success");
                          location.href ="{{route('fact_contable_index')}}";
                        },
                        error: function(data){
                          console.log(data);
                        }
                      }); 

                    }
                  
    } catch(err) {
      console.log(err);
    }
  }
 function alertas (icon, title, text){
    Swal.fire({
      icon: `${icon}`,
      title: `${title}`,
       html: `${text}`
    })
  }

  $('.select2_find_proveedor').select2({
      placeholder: "Seleccione proveedor",
       allowClear: true,
      ajax: {
          url: '{{route("fact_contable.buscar_proveedor")}}',
          data: function (params) {
          var query = {
              search: params.term,
              type: 'public'
          }
          return query;
          },
          processResults: function (data) {
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
