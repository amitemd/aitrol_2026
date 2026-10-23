@extends('contable.rol_pago.base')
@section('action-content')
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">

<!--Modal Editar Tipo de Pago
<div class="modal fade" id="modal_edit_pago" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>-->
<style>
  .desabilitado {
    display: none;
  }
</style>
<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item active" aria-current="page">Generar Asientos Mensual</li>
    </ol>
  </nav>
  <div class="box">
    <div class="box-header">
      <div class="col-md-7">
        <h5><b>ROL DE PAGOS</b></h5>
      </div>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">BUSCADOR ROL DE PAGO</label>
      </div>
    </div>
    <div class="box-body dobra">
      <form method="POST" id="roles_pago" action="{{ route('exportar_reporte') }}">
        {{ csrf_field() }}
          <!--<div class="form-group col-md-1 col-xs-2">
            <label class="texto" for="id_empresa">Empresa: </label>
          </div>
          <div class="form-group col-md-3 col-xs-10 container-4">
            <select class="form-control" id="id_empresa" name="id_empresa">
              @foreach($empresas as $value)
                <option value="{{$value->id}}" @if($id_empresa ==  $value->id) selected="selected" @endif>{{$value->nombrecomercial}}</option>
              @endforeach

            </select>
          </div>-->

          <div class="form-group col-md-5 col-xs-3" style="padding-left: 0px;padding-right: 0px;">
            <label for="year" class="texto  col-md-1 control-label">Año</label>

            <div class="col-md-9">
            <select id="year" name="year" class="form-control">
              <option value="">Seleccione...</option>
              <?php
              $anio_min = date('Y');
              $anio_min = date("Y", strtotime($anio_min . "- 1 year"));
              $anio_max = date("Y", strtotime($anio_min . "+ 5 year"));
              for ($i = $anio_min; $i <= $anio_max; $i++) {
                echo "<option value='" . $i . "'>" . $i . "</option>";
              }
              ?>
            </select>
          </div>
        </div>

          <div class="form-group col-md-5 col-xs-3" style="padding-left: 0px;padding-right: 0px;">
          <label for="mes" class="texto col-md-1 control-label">Mes</label>
            <div class="col-md-9">
            <select id="mes" name="mes" class="form-control">
              <option value="">Seleccione...</option>
              <?php
                  $Meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                      'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

              for ($i = 1; $i <= 12; $i++) {

                echo '<option value="' . $i . '">' . $Meses[($i) - 1] . '</option>';
              }
              ?>
            </select>
          </div>
        </div>

          <div class="form-group col-md-2 col-xs-3" style="padding-left: 0px;padding-right: 0px;">
          <button type="button" onclick="buscar_roles_pago();" class="btn btn-primary" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span>Buscar
          </button>
        </div>
          <!--div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="cuenta_destino" class="texto col-md-3 control-label">Cuenta Destino: </label>
          <div class="col-md-9">
            <select class="form-control select2_cuentas" id="id_cuenta_destino" name="id_cuenta_destino">

            </select>
          </div>
          </div-->
        <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
          <a onclick="imprimirTodo()" target="_blank" class="btn btn-success" id="imprimir_rol">Imprimir Rol Pago</a>
            <!--button type="button" onclick="Generar_asientos_diario()" class="btn btn-success" id="boton_generar">Generar Asientos
            </button-->
            <button type="button" onclick="Generar_asientos_diario_nuevo()" class="btn btn-success" id="boton_generar">Detalle de Cuentas
          </button>
          <button type="button" class="btn btn-primary" onclick="exportar_reporte()" id="btn_exportar">
            <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar
          </button>
        </div>
          <!--div class="col-md-12 col-xs-12">
          <div class="form-group col-md-3 col-xs-10 ">
            <label for="">Fecha Asiento</label>
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
                    <input autocomplete="off" type="text"  name="fecha" class="form-control fecha" id="fecha" value="">
            </div>
          </div>
          </div-->

      </form>
    </div>
    <div class="box box" style="border-radius: 8px;" id="area_trabajo">
    </div>
  </div>
  <div id="existe_roles">

  </div>
</section>
<script src="{{ asset ('/plugins/datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>
<!--<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>-->


<script type="text/javascript">
  /*------VICTOR BORRO
  $('#fecha').datetimepicker({
    format: 'YYYY-MM-DD',
  });
  window.onload = () => {
    $("#fecha").focus();
    $("#fecha").blur();
  }*/

  $('.select2_cuentas').select2({
    placeholder: "Escriba la cuenta",
    allowClear: true,
    ajax: {
      url: '{{route("rol_pago.buscar_cuentas")}}',
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

  function buscar_roles_pago() {

    var formulario = document.forms["roles_pago"];
    //Valores de Busqueda
        //var id_emp = formulario.id_empresa.value;
    var anio = formulario.year.value;
    var id_mes = formulario.mes.value;

    //console.log(`Año ${anio} Mes ${id_mes}`);
    //Mensaje
    var msj = "";

    /*if(id_emp == ""){
      msj = msj + "Por favor, Seleccione la Empresa<br/>";
    }*/



    if (anio == "") {
      msj = msj + "Por favor, Seleccione el Año<br/>";
    }

    if (id_mes == "") {
      msj = msj + "Por favor, Seleccione el Mes<br/>";
    }

    if (msj != "") {
      swal.fire({
        title: "Error!",
        icon: "error",
        html: msj
      });
      return false;
    }

    $.ajax({
      type: 'post',
      url: "{{route('buscador_roles.pago')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
      datatype: 'json',
      data: $("#roles_pago").serialize(),
      success: function(data) {
        $("#area_trabajo").html(data);

      },
      error: function() {
        console.log(data);
      }
    });
  }

  function Generar_asientos_diario() {

    //var formulario = document.forms["buscad_rol_pago"];
    var formulario = document.forms["roles_pago"];
    var desc_mes = 0;
    //Valores de Busqueda

    var anio = formulario.year.value;
    var id_mes = parseInt(formulario.mes.value);
    var cuenta_destino = formulario.id_cuenta_destino.value;

    switch (id_mes) {
      case 1:
        desc_mes = 'Enero';
        break;

      case 2:
        desc_mes = 'Febrero';
        break;

      case 3:
        desc_mes = 'Marzo';
        break;

      case 4:
        desc_mes = 'Abril';
        break;

      case 5:
        desc_mes = 'Mayo';
        break;

      case 6:
        desc_mes = 'Junio';
        break;

      case 7:
        desc_mes = 'Julio';
        break;

      case 8:
        desc_mes = 'Agosto';
        break;

      case 9:
        desc_mes = 'Septiembre';
        break;

      case 10:
        desc_mes = 'Octubre';
        break;

      case 11:
        desc_mes = 'Noviembre';
        break;

      case 12:
        desc_mes = 'Diciembre';
        break;

    }

    var msj = "";

    /*if(id_emp == ""){
      msj = msj + "Por favor, Seleccione la Empresa<br/>";
    }*/

    if (anio == "") {
      msj = msj + "Por favor, Seleccione el Año<br/>";
    }

    if (id_mes == "") {
      msj = msj + "Por favor, Seleccione el Mes<br/>";
    }

    if (cuenta_destino == "") {
      msj = msj + "Por favor, Seleccione la Cuenta Destino<br/>";
    }


    let fecha = document.getElementById('fecha').value;
    console.log(fecha);


    if (msj != "") {
      swal.fire({
        title: "Error!",
        icon: "error",
        html: msj
      });
      return false;
    }
    if (fecha == "") {
      swal.fire({
        title: "Error!",
        icon: "error",
        html: "Debe seleccionar la fecha para crear el asiento"
      });
      return false;
    }

    $.ajax({
      type: 'post',
      url: "{{route('asientos_rolpago.store')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
      datatype: 'json',
      data: $("#roles_pago").serialize(),
      success: function(data) {

        if (data.msj == 'ok') {
          console.log(data.msj)
              swal("Error!", data.mensaje ,"error" );

        } else if (data.opcion_store == '1') {
            swal("OK","Registro de Asientos");
          $("#boton_generar").attr("disabled", true);
          }
        },
        error: function(){
          console.log(data);
        }
      });
    }
    function Generar_asientos_diario_nuevo(){
      //var formulario = document.forms["buscad_rol_pago"];
      var formulario = document.forms["roles_pago"];
      var desc_mes = 0;
      //Valores de Busqueda
      var anio = formulario.year.value;
      var id_mes = parseInt(formulario.mes.value);
      //var cuenta_destino = formulario.id_cuenta_destino.value;
      switch(id_mes){
        case 1:
          desc_mes = 'Enero';
          break;
        case 2:
          desc_mes = 'Febrero';
          break;
        case 3:
          desc_mes = 'Marzo';
          break;
        case 4:
          desc_mes = 'Abril';
          break;
        case 5:
          desc_mes = 'Mayo';
          break;
        case 6:
          desc_mes = 'Junio';
          break;
        case 7:
          desc_mes = 'Julio';
          break;
        case 8:
          desc_mes = 'Agosto';
          break;
        case 9:
          desc_mes = 'Septiembre';
          break;
        case 10:
          desc_mes = 'Octubre';
          break;
        case 11:
          desc_mes = 'Noviembre';
          break;
        case 12:
          desc_mes = 'Diciembre';
          break;
      }
      var msj = "";
      if(anio == ""){
        msj = msj + "Por favor, Seleccione el Año<br/>";
      }
      if(id_mes == ""){
        msj = msj + "Por favor, Seleccione el Mes<br/>";
      }
      /*if(cuenta_destino == ""){
        msj = msj + "Por favor, Seleccione la Cuenta Destino<br/>";
      }
      let fecha = document.getElementById('fecha').value;
      console.log(fecha);*/
      if(msj != ""){
          swal({
                title: "Error!",
                type: "error",
                html: msj
              });
          return false;
      }
      /*if(fecha == ""){
        swal({
                title: "Error!",
                type: "error",
                html: "Debe seleccionar la fecha para crear el asiento"
              });
          return false;
      }*/
      $.ajax({
        type: 'get',
        url:"{{url('nuevos_asientos_roles/rol')}}/"+ anio + "/" + id_mes,
        datatype: 'json',
        success: function(data){
          if(data.msj =='error'){
              swal("Error!", data.mensaje ,"error" );
          }
          if(data.msj == 'ok'){
            var id_rol_asiento = data.id_rol_asiento;
            location.href = "{{url('nuevos_asientos_roles/rol')}}/" + id_rol_asiento;
        }

      },
      error: function() {
        console.log(data);
      }
    });

  }

  function exportar_reporte() {
    $("#roles_pago").submit();
  }


  function imprimirTodo() {
    var mes = parseInt($("#mes").val());
    var anio = parseInt($('#year').val());
    let msj = "";
    if (isNaN(anio)) {
      msj = msj + "Por favor, Seleccione el Año<br/>";
    }
    if (isNaN(mes)) {
      msj = msj + "Por favor, Seleccione el Mes<br/>";
    }
    if (msj != "") {
      alerta(msj);
      }
      else{
      $.ajax({
        type: 'POST',
        url: "{{route('rol_pago.comprobar')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: {"year": anio, "mes": mes},
        success: function(data) {
          if (data.msj == 'no') {
            mes = 0;
            anio = 0;
            alerta('No existe roles de pago');

          } else if (data.msj == 'si') {
            var ruta = '' + "{{route('rol_pago.imprimir_2_2')}}/" + mes + "/" + anio;

            window.open(ruta);
          }
        },
        error: function() {
          console.log(data);
        }
      });
    }
  }

  function alerta(text) {
    Swal.fire({
      icon: 'error',
      title: 'Error..!',
      html: `${text}`
    })
  }
</script>

@endsection