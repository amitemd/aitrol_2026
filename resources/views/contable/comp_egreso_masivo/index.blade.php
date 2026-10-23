@extends('contable.comp_egreso_masivo.base')
@section('action-content')
@php
  $user = Auth::user()->id;
@endphp
<style type="text/css">
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

  * {
    font-family: var(--fuentePrincipal);
  }
</style>

<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<script type="text/javascript">
  $(function() {
    $(".clickable-row").click(function() {
      window.location = $(this).data("href");
    });
  });
</script>
<div class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="#">Comprobante de Egreso Masivo</a></li>
        <li class="breadcrumb-item active" aria-current="page">Registro Comprobante de Egreso Masivo</li>
    </ol>
  </nav>
  <div class="box">
    <div class="box-header header_new">
      <div class="col-md-9">
        <!--<h8 class="box-title size_text">Empleados</h8>-->
        <!--<label class="size_text" for="title">EMPLEADOS</label>-->
              <h3 class="box-title">Comprobante de Egreso Masivo</h3>
      </div>

      {{--<div class="col-md-1 text-right">
              <a href="{{route('comp_egreso_masivo.create')}}" class="btn btn-success btn-gray" >
      <i class="fa fa-file"></i>
      </a>
    </div>--}}
    <div class="col-md-1 text-right">
      <a href="{{route('comp_egreso_masivo.newCreate2')}}" class="btn btn-success">
        <i class="fa fa-file"></i> New Create
      </a>
    </div>
  </div>
  <div class="row head-title">
    <div class="col-md-12 cabecera">
              <label class="color_texto" for="title">BUSCADOR DE COMP DE EGRESO MASIVO</label>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body dobra">
    <form method="POST" id="reporte_master" action="{{ route('comp_egreso_masivo.buscar') }}">
      {{ csrf_field() }}
      <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="id">Id: </label>
      </div>
      <div class="form-group col-md-3 col-xs-10 container-4">
        <input class="form-control" type="text" id="id" name="id" value="@if(isset($searchingVals)){{$searchingVals['id']}}@endif" placeholder="Ingrese Id..." />

      </div>

      <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="acreedor">Concepto: </label>
      </div>
      <div class="form-group col-md-3 col-xs-10 container-4">
        <input class="form-control" type="text" id="descripcion" name="descripcion" value="@if(isset($searchingVals)){{$searchingVals['descripcion']}}@endif" placeholder="Ingrese concepto..." />
      </div>
      <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="girado">Girado a: </label>
      </div>
      <div class="form-group col-md-3 col-xs-10 container-4">
        <input class="form-control" type="text" id="girado_a" name="girado_a" value="@if(isset($searchingVals)){{$searchingVals['girado_a']}}@endif" placeholder="Ingrese girado a..." />
      </div>
      <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="detalle">Nº cheque: </label>
      </div>
      <div class="form-group col-md-3 col-xs-10 container-4">
        <input class="form-control" type="text" id="cheque" name="cheque" value="@if(isset($searchingVals)){{$searchingVals['no_cheque']}}@endif" placeholder="Ingrese número de cheque..." />
      </div>

      <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="acreedor">Asiento: </label>
      </div>
      <div class="form-group col-md-3 col-xs-10 container-4">
        <input class="form-control" type="text" id="id_asiento_cabecera" name="id_asiento_cabecera" value="@if(isset($searchingVals)){{$searchingVals['id_asiento_cabecera']}}@endif" placeholder="Ingrese ID ASIENTO..." />
      </div>

      <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="fecha">Fecha Cheque: </label>
      </div>
      <div class="form-group col-md-3 col-xs-10 container-4">
        <div class="input-group date">
          <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </div>
          <input type="text" name="fecha" class="form-control fecha" id="fecha" value="@if(isset($searchingVals)){{$searchingVals['fecha_cheque']}}@endif">
        </div>
      </div>
      <div class="col-xs-1">
        <button type="submit" id="buscarEmpleado" onclick="document.getElementById('excel').value=0" class="btn btn-primary">
                      <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
        </button>
      </div>
      <div class="text-center md:w-auto w-full">
        <input type="hidden" name="excel" id="excel">
        <button type="submit" onclick="document.getElementById('excel').value=1" class="bg-green btn btn-primary btn-xl" type="button">
          <span class="glyphicon glyphicon-download-alt"></span>
          Excel
        </button>
      </div>
    </form>
  </div>
  <div class="row head-title">
    <div class="col-md-12 cabecera">
                <label class="color_texto" >LISTADO DE COMPROBANTES</label>
    </div>
  </div>
  <div class="box-body dobra">

    <div class="row">
      <div class="table-responsive col-md-12">
        <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
          <thead>
            <tr class='well-dark'>
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >ID</th>
                                <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Concepto</th>
                                <th width="8%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Secuencia</th>
                                <th width="8%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >N° Asiento</th>
                                <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha Cheque</th>
                                <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Nro_Cheque</th>
                                <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Girado a</th>
                                <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Valor</th>
                                <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Creado por</th>
                                <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Estado</th>
                                <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Acción</th>
            </tr>
          </thead>
          <tbody>

            @foreach ($comprobante_egreso_m as $value)
            <tr class="well">
              <td>{{$value->id}}</td>
              <td>{{$value->descripcion}}</td>
              <td>{{$value->secuencia}}</td>
              <td>{{$value->id_asiento_cabecera}}</td>
              <td>{{$value->fecha_cheque}}</td>
              <td>{{$value->no_cheque}}</td>
              <td>{{$value->girado_a}}</td>
              <td>{{$value->valor_pago}} </td>
              <td>@if(isset($value->usuario)) {{$value->usuario->nombre1}} {{$value->usuario->nombre2}} @endif</td>
                                  <td>@if(($value->estado)==1) Activo @else Inactivo @endif</td>
              <td>
                <a class="btn btn-danger" target="_blank" href="{{route('pdf_egreso_masivo.pdf',['id'=>$value->id])}}"><i class="fa fa-file-pdf-o "></i></a>
                @if(($value->estado)==1)
                <a href="javascript:anular({{$value->id}});" class="btn btn-danger"><i class="fa fa-trash"> </i> </a>
                @endif
                <a class="btn btn-info" href="{{route('comp_egreso_masivo.edit',['id'=>$value->id])}}"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></a>
                <a class="btn btn-success" href="{{route('reporte_datos.compegresom',['id'=>$value->id, 'tipo'=>1])}}"><i class="fa fa-file-excel-o" aria-hidden="true"></i></a>
                <a class="btn btn-success btn-gray" onclick="enviar_correo({{$value->id}})"><i class="fa fa-envelope" aria-hidden="true"></i></a>
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

      </div>
      <div class="col-sm-7">
        <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">

        </div>
      </div>
    </div>


  </div>
</div>
</div>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">
  $(document).ready(function() {
    $('#example2').DataTable({
      'paging': false,
      'lengthChange': true,
      'searching': false,
      'ordering': false,
      'info': false,
      'autoWidth': true,
        'sInfoEmpty':  true,
        'sInfoFiltered': true,
        'language': {
              "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
          }
        });

  });
  function enviar_correo(id){
     //swal("Editado!", "Correcto", "success");
     var confirmar_correo = confirm("¿Desea enviar el correo?");
      if (confirmar_correo) {
        $.ajax({
          type: 'get',
          url: "{{route('envio_correo_pro.acreedores')}}",
          headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
          },
          data:{
            'id':id
          },
          datatype: 'json',
          success: function(data) {
            if (data.statusy) {
              alert(data.msj);
              location.reload();
            }else{
              alert(data.msj);
            }
          },
        });
      }
  }
  $('#fecha').datetimepicker({
    format: 'YYYY-MM-DD',
  });
  $(".nombre_proveedor").autocomplete({
    source: function(request, response) {
      $.ajax({
        url: "{{route('compra_buscar_nombreproveedor')}}",
        dataType: "json",
        data: {
          term: request.term
        },
        success: function(data) {
          response(data);
        }
      });
    },
    minLength: 2,
  });

  function cambiar_nombre_proveedor() {
    $.ajax({
      type: 'post',
      url: "{{route('compra_buscar_proveedornombre')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: {
        'nombre': $("#nombre_proveedor").val()
      },
      success: function(data) {
        if (data.value != "no") {
          $('#id_proveedor').val(data.value);
          $('#proveedor').val(data.value);
          $('#direccion_id_proveedor').val(data.direccion);
        } else {
          $('#id_proveedor').val("");
          $('#proveedor').val("");
          $('#direccion_proveedor').val("");
        }

      },
      error: function(data) {
        console.log(data);
      }
    });
  }
  $('.select2').select2({
    tags: false
  });

  function anular(id) {
    /*if (confirm('¿Desea Anular Factura  ?')) {
      $.ajax({
          type: 'get',
          url:"{{ url('contable/compras/factura/')}}/"+id,
          datatype: 'json',
          data: $("#fecha_enviar").serialize(),
          success: function(data){
            swal("Correcto!","Anulación Correcta","success");
            location.href ="{{route('compras_index')}}";
          },
          error: function(data){
            console.log(data);
          }
        }); 
    }else{
      compras.verificar_anulacion
       location.href ="{{route('compras_index')}}";
    }*/

    Swal.fire({
        title: '¿Desea Anular esta comprobante?',
        text: "No puedes revertir esta acccion!",
      icon: 'error',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si'
    }).then((result) => {
      if (result.isConfirmed) {
        var acumulate = "";
        test(id);
        // $.ajax({
        //   type: 'get',
        //   url:"{{ route('compras.verificar_anulacion')}}",
        //   datatype: 'json',
        //   data: {'verificar':'5','id_compra': id},
        //   success: function(data){
        //     //console.log(data+" dsada "+id);
        //     console.log(data);
        //       //console.log(acumulate);
        //       if(data.respuesta=='si'){
        //         //Swal.fire("Error!","Existen algunos comprobantes generados con esta factura, observaciones encontradas: <br> "+acumulate,"error");
        //         let enlace = `<a target="_blank" href="{{ url('contable/cruce/valores/buscar?id=${data.ids[0]}')}}"><b>${data.tablas[0]}</b></a>`;
        //         let texto = `Existen algunos ${enlace} generados con esta factura`;
        //         alertas("error", "Error", texto);
        //       }else{
        //           console.log("entra aqui"+id);
        //           test(id);
        //       }


        //   },
        //   error: function(data){
        //     console.log(data);
        //   }
        // }); 

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
          url:"{{ url('contable/acreedores/documentos/cuentas/egreso/masivo/anular/')}}/"+id,
          datatype: 'json',
          data: {'concepto':text},
          success: function(data){
            Swal.fire("Correcto!","Anulación Correcta","success");
            location.href ="{{route('comp_egreso_masivo.index')}}";
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

  function alertas(icon, title, text) {
    Swal.fire({
      icon: `${icon}`,
      title: `${title}`,
      html: `${text}`
    })
  }



</script>

@endsection
