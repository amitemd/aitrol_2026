@extends('activosfijos.documentos.factura.base')
@section('action-content')
<style>
  .autocomplete {
    z-index: 999999 !important;
    z-index: 999999999 !important;
    z-index: 99999999999999 !important;
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
    list-style: none;
    background-color: #FFFFFF;
    width: 300px;
    border: solid 1px #EEE;
    border-radius: 5px;
    padding-left: 10px;
    line-height: 2em;
  }
</style>

<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Activos Fijos</a></li>
      <li class="breadcrumb-item"><a href="#">Documentos</a></li>
      <li class="breadcrumb-item"><a href="#">Factura Activo Fijo</a></li>
      <li class="breadcrumb-item">Listado</li>
    </ol>
  </nav>
  <div class="box">
    <div class="box-header header_new">
      <div class="col-md-8">
        <!--<h8 class="box-title size_text">Empleados</h8>-->
        <!--<label class="size_text" for="title">EMPLEADOS</label>-->
        <h3 class="box-title">Factura Activo Fijo</h3>
      </div>
     
      <div class="col-md-1">
        <button onclick="location.href='{{route('documentofactura.new_factura')}}'" class="btn btn-success btn-gray">
          <i aria-hidden="true"></i>Nueva Factura
        </button>
      </div>
      

      <!--div class="col-md-1 text-right">
        <button onclick="location.href='{{route('afDocumentoFactura.create')}}'" class="btn btn-success btn-gray">
          <i aria-hidden="true"></i>Agregar Nueva Factura Activo Fijo
        </button>
      </div-->

    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">BUSCADOR DE FACTURA ACTIVO FIJO</label>
      </div>
    </div>
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('activosfijos.documentofactura.search') }}">
        {{ csrf_field() }}
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="id">Id: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="id" name="id" value="@if(isset($searchingVals)){{$searchingVals['id']}}@endif" placeholder="Ingrese Id..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="nombre_proveedor">Proveedor: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          {{-- <input class="form-control" type="text"  id="buscarnombre" name="nombre_proveedor" value="@if(isset($searchingVals)){{$searchingVals['proveedor']}}@endif" placeholder="Ingrese nombre de proveedor..." /> --}}
          <select class="form-control" id="nombre_proveedor" name="nombre_proveedor">
            <option></option>
            @foreach ($proveedores as $value)
            <option value="{{ $value->id }}">{{ $value->nombrecomercial }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="detalle">Detalle: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="detalle" name="detalle" value="@if(isset($searchingVals)){{$searchingVals['observacion']}}@endif" placeholder="Ingrese detalle..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="secuencia_f">Secuencia: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="secuencia_f" name="secuencia_f" value="@if(isset($searchingVals)){{$searchingVals['secuencia']}}@endif" placeholder="Ingrese Secuencia..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="fecha">Fecha: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <div class="input-group date">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" name="fecha" class="form-control fecha" id="fecha" value="@if(isset($searchingVals)){{$searchingVals['fecha_compra']}}@endif">
          </div>
        </div>

        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="asiento">Asiento: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="asiento" name="asiento" value="@if(isset($searchingVals)){{$searchingVals['id_asiento']}}@endif" placeholder="Ingrese asiento..." />
        </div>

        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="fac_crea">Creo la factura: </label>
        </div>

        <div class="form-group col-md-3 col-xs-10 container-4">
          {{-- <input class="form-control" type="text" id="id_usuariocrea" name="id_usuariocrea" value="@if(isset($searchingVals)){{$searchingVals['id_usuariocrea']}}@endif" placeholder="Ingrese quien creo la factura..." /> --}}
          <select class="form-control" id="id_usuariocrea" name="id_usuariocrea">
            <option></option>
            @foreach ($usuarios as $value)
            <option value="{{ $value->id }}">{{ $value->nombre1 }} {{ $value->apellido1 }}</option>
            @endforeach
          </select>
        </div>



        <div class="col-md-offset-5 col-xs-2">
          <button type="submit" id="buscarEmpleado" class="btn btn-primary">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
          </button>
        </div>
      </form>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto">LISTADO DE FACTURAS</label>
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
                  <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                    <thead>
                      <tr class='well-dark'>
                        <th width="5%" >ID</th>
                        <th width="20%" >Secuencia</th>
                        <th width="8%" >Asiento</th>
                        <th width="10%" >Fecha Compra</th>
                        <th width="10%" >RUC</th>
                        <th width="20%" >Proveedor</th>
                        <th width="15%" >Autorizacion</th>
                        <th width="5%" >Tipo Comprobante</th>
                        <th width="20%" >Detalle</th>
                        <!--<th width="20%" >Egresos</th>-->
                        <th width="12%" >Estado</th>
                        <th width="15%" >Acción</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($documentos as $value)
                      <tr class="well">
                        <td>{{$value->id}}</td>
                        <td>{{$value->serie}}-{{$value->secuencia}}</td>
                        <td>{{$value->id_asiento}}</td>
                        <td>{{ date('d/m/Y',strtotime($value->fecha_compra))}}</td>
                        <td>{{$value->proveedor}}</td>
                        <td>{{$value->datosproveedor->razonsocial}}</td>
                        <td>{{$value->nro_autorizacion}}</td>
                        <td>{{$value->tipo_comprobante}}</td>
                        <td>@if(!is_null($value->observacion)) {{$value->observacion}} @endif</td>
                        <td>@if($value->estado == '1') Activo @elseif($value->estado =='0') Anulada @else Activo @endif</td>
                        <td>
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <!--a href="{{ route('afDocumentoFactura.show', ['id' => $value->id]) }}" class="btn btn-success btn-gray">
                            <i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i>
                          </a-->
                          
                          <a href="{{ route('documentofactura.edit_new_factura', ['id' => $value->id]) }}" class="btn btn-success btn-gray btn-xs">
                            <i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i>
                          </a>
                          
                          
                        @if($value->estado == '1')
                          <a href="javascript:confirmar({{$value->id}})" class="btn btn-danger btn-gray btn-xs">
                            <i  class="glyphicon glyphicon-trash"aria-hidden="true"></i>
                          </a>
                        @endif

                          <a href="{{ route('documentofactura.subir_archivo', ['id' => $value->id]) }}" class="btn btn-success btn-gray btn-xs">
                            <i class="fa fa-upload" aria-hidden="true"></i>
                          </a>
                        </td>
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
                  <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($documentos->currentPage() - 1) * $documentos->perPage())}} / {{count($documentos) + (($documentos->currentPage() - 1) * $documentos->perPage())}} de {{$documentos->total()}} registros</div>
                </div>
                <div class="col-sm-7">
                  <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                    {{ $documentos->appends(Request::only(['proveedor', 'observacion', 'secuencia','tipo_comprobante','fecha','id','secuencia_f','asiento']))->links() }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-5">

          </div>
          <div class="col-sm-7">
            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</section>
<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
  $(document).ready(function() {

    $("#nombre_proveedor").select2();
    $("#id_usuariocrea").select2();

    $('#fecha').datetimepicker({
      format: 'DD/MM/YYYY'
    });

    $('.confirmation').on('click', function() {
      // return confirm('Are you sure?');

      Swal.fire({
        title: 'Do you want to save the changes?',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: `Save`,
        denyButtonText: `Don't save`,
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
          Swal.fire('Saved!', '', 'success');
          return true;
        } else if (result.isDenied) {
          return false;
          // Swal.fire('Changes are not saved', '', 'info')
        }
      })

    });

   

  });

  function confirmar(id) {
      //alert();
      Swal.fire({
        title: '¿Desea Anular esta factura?',
        text: "No puedes revertir esta acccion!",
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si',
      }).then((result) => {
       
        if (result.isConfirmed) {
          test(id);
        }
      })

    }
  async function test(id) {
    try {
      const {
        value: text
      } = await Swal.fire({
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
          url: `{{ url('activojifos/documentofactura/anular/${id}')}}`  ,
          datatype: 'json',
          data: {
            'observacion': text
          },
          success: function(data) {
            Swal.fire("Correcto!", "Anulación Correcta", "success");
            setTimeout(function() {
              location.reload();

            } , 1000)
           
           // location.href = "{{route('afDocumentoFactura.index')}}";
          },
          error: function(data) {
            console.log(data);
          }
        });
      }

    } catch (err) {
      console.log(err);
    }
  }
</script>
@endsection