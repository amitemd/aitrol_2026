@extends('contable.rol_pago.base')
@section('action-content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

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
        <div class="form-group col-md-3 col-xs-3" style="padding-left: 0px;padding-right: 0px;">
            <label for="year" class="texto  col-md-2 control-label">Año </label>
            <div class="col-md-6">
            <select id="year" name="year" class="form-control">
              <option value="">Seleccione...</option>
              <?php
              $anio_min = date('Y');
              $anio_min = date("Y", strtotime($anio_min . "- 4 year"));
              $anio_max = date("Y", strtotime($anio_min . "+ 5 year"));
              for ($i = $anio_min; $i <= $anio_max; $i++) {
                echo "<option value='" . $i . "'>" . $i . "</option>";
              }
              ?>
            </select>
          </div>
        </div>

        <div class="form-group col-md-3 col-xs-3" style="padding-left: 0px;padding-right: 0px;">
          <label for="mes" class="texto col-md-2 control-label">Mes</label>
            <div class="col-md-6">
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

        @php $cantidadSucursales = count($sucursales); @endphp
        <div class="form-group col-md-4 col-xs-3" style="padding-left: 0px;padding-right: 0px;">
          <label for="sucursal" class="texto col-md-2 control-label">{{trans('winsumos.sucursal')}}</label>
            <div class="col-md-6">
            <select id="sucursal" name="sucursal" class="form-control">
              @if($cantidadSucursales > 1)<option value="">Seleccione...</option>@endif
              @foreach ($sucursales as $sucursal)
              <option value="{{$sucursal->id}}">{{$sucursal->codigo_sucursal}}-{{$sucursal->nombre_sucursal}}</option>.
              @endforeach
            </select>
          </div>
        </div>

        <!--div class="form-group col-md-2 col-xs-3" style="padding-left: 0px;padding-right: 0px;">
          <button type="button" onclick="buscar_roles_pago();" class="btn btn-primary" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span>Buscar
          </button>
        </div-->

        <div class="form-group col-md-2 col-xs-3" style="padding-left: 0px;padding-right: 0px;">
          <button type="button" onclick="buscar_roles_pago_nuevo();" class="btn btn-primary" id="boton_buscar2">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span>Buscar
          </button>
        </div>

        <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
          <a onclick="imprimirTodo_sucursal()" target="_blank" class="btn btn-success" id="imprimir_rol_sucursal">Imprimir Rol Pago</a>
            <button type="button" onclick="Generar_asientos_diario_nuevo_src()" class="btn btn-success" id="boton_generar">Detalle de Cuentas </button>
          <button type="button" class="btn btn-primary" onclick="exportar_reporte()" id="btn_exportar">
            <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar
          </button>
        </div>


      </form>
    </div>
    <div class="box box" style="border-radius: 8px;" id="area_trabajo">
    </div>
  </div>
  <div id="existe_roles">

  </div>
</section>

<!--script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<!-- <script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<!--script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script-->
<!-- <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script> -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<!-- <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>  -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>




<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>


<script type="text/javascript">

//////// REQUISITO PARA EL GENERAL ////////
  $(document).ready(function($){
      //roles_empleados();  
      @php 
        $fijos = [0,1,2,47];
        $nomina = [3,4,5,6,7,8,9,10];
        $nomina2 = [5,6,7,8,9,10];
        $ingresos =  [11,12,13,14,15,16,17,18,19,20,21];
        $egresos  =  [23,24,25,26,27,28,29,30,31,32,33,34, 35, 36];
        $resumen  =  [22,37,38];
        $acumulados  =  [3,4,38 ,39,40,41,42,43,44,45,46];
        $acumulados2  =  [38 ,39,40,41,42,43,44,45,46];
      @endphp  


  }); 

  const dataTable = () => {

    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': true,
      'searching'   : false,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false,
      'sInfoEmpty':  true,
      'sInfoFiltered': true,
      'language': {
          "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
      },
      'columnDefs': [
        @foreach($ingresos as $i)
        {
            target: {{$i}},
            visible: false,
            
        },
        @endforeach
        @foreach($egresos as $i)
        {
            target: {{$i}},
            visible: false,
            
        },
        @endforeach
        @foreach($acumulados2 as $i)
        {
            target: {{$i}},
            visible: false,
            
        },
        @endforeach
      
      ],
      'dom': 'Bfrtip',
      'buttons': [
          {
              extend: 'colvisGroup',
              text: 'Detalle Ingresos',
              show: [ @foreach($fijos as $i) {{$i}}, @endforeach @foreach($ingresos as $i) {{$i}}, @endforeach @foreach($resumen as $i) {{$i}}, @endforeach ],
              hide: [ @foreach($nomina as $i) {{$i}}, @endforeach @foreach($egresos as $i) {{$i}}, @endforeach 37, 38, @foreach($acumulados as $i) {{$i}}, @endforeach ]
          },
          {
              extend: 'colvisGroup',
              text: 'Detalle Egresos',
              show: [ @foreach($fijos as $i) {{$i}}, @endforeach @foreach($egresos as $i) {{$i}}, @endforeach @foreach($resumen as $i) {{$i}}, @endforeach ],
              hide: [ @foreach($nomina as $i) {{$i}}, @endforeach @foreach($ingresos as $i) {{$i}}, @endforeach 22, 38, @foreach($acumulados as $i) {{$i}}, @endforeach ]
          },
          {
              extend: 'colvisGroup',
              text: 'Acumulados',
              show: [ @foreach($fijos as $i) {{$i}}, @endforeach  @foreach($resumen as $i) {{$i}}, @endforeach @foreach($acumulados as $i) {{$i}}, @endforeach ],
              hide: [ @foreach($nomina2 as $i) {{$i}}, @endforeach @foreach($ingresos as $i) {{$i}}, @endforeach @foreach($egresos as $i) {{$i}}, @endforeach ]
          },
          {
              extend: 'colvisGroup',
              text: 'Mostrar Todos',
              show: ':hidden'
          },
          {
              extend: 'excelHtml5',
              footer: true,
              title: 'Roles',
              customize: function (xlsx) {
                  var sheet = xlsx.xl.worksheets[0]; // Obtener la hoja de trabajo

                  // Obtener la última columna en la hoja de trabajo
                  var lastCol = $('col:last', sheet);

                  // Aplicar formato personalizado para la última columna (supongamos que es la columna W)
                  lastCol.attr('s', '55'); // 55 es el identificador de formato para 4 decimales
              }
          }
      ]
    });
    }

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
    var sucursal = formulario.sucursal.value;
    //alert(formulario.sucursal.value);

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

    if (sucursal == "") {
      msj = msj + "Por favor, Seleccione la sucursal <br/>";
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
      url: "{{route('nuevo_rol.buscador_roles_sucursal')}}",
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
    function Generar_asientos_diario_nuevo_src(){
      
      //var formulario = document.forms["buscad_rol_pago"];
      var formulario = document.forms["roles_pago"];
      var desc_mes = "";
      //Valores de Busqueda
      var sucursal = formulario.sucursal.value;
      //alert(formulario.sucursal.value);
      var anio = formulario.year.value;
      var id_mes = parseInt(formulario.mes.value);
      var msj = "";
      var verificacion = false;
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
        //default: desc_mes =  "Seleccione un mes";  
        //break;
      }
      //alert(msj);
      
      if(anio == ""){
        msj = msj + "Por favor, Seleccione el Año<br/>";
      }
      if(desc_mes == ""){
        msj = msj + "Por favor, Seleccione el Mes<br/>";
      }
      if(sucursal == ""){
        msj = msj + "Por favor, Seleccione la Sucursal <br/>";
        //alert(msj);
      }
  

      //let fecha = document.getElementById('fecha').value;
      //console.log(fecha);
      //alert(msj);
      if (msj != "") {
        swal.fire({
          title: "Error!",
          icon: "error",
          html: msj
        });
        return false;
      }
      var fecha_actual = new Date();
      var dia = fecha_actual.getDate().toString().padStart(2, '0');
      var mes = (fecha_actual.getMonth() + 1).toString().padStart(2, '0'); // Mes de 1 a 12
      var anio_actual = fecha_actual.getFullYear();
      var fecha_formato = `${dia}-${mes}-${anio_actual}`;

      $.ajax({
          type: 'get',
          url:"{{url('contable/verificar/rol/pago')}}/" + anio + "/" + id_mes + "/" + sucursal,
          datatype: 'json',
          success: function(data){
            //alert(data.status);
            //console.log(data.msj);
            if(data.status == 'success'){
                swal.fire({
                  title: `Alerta`,
                  text: `¿Esta seguro que desea crear la contabilización para ` + desc_mes + ` ` + anio + ` con  Fecha actual: ${fecha_formato} ?`,
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Si'
                }).then((result) => {
                    if (result.isConfirmed) {
                      $.ajax({
                          type: 'get',
                          url:"{{url('nuevos_asientos_roles/rol/sucursal')}}/"+ sucursal + "/"+ anio + "/" + id_mes,
                          datatype: 'json',
                          success: function(data){
                            if(data.msj == 'ok'){
                              var id_rol_asiento = data.id_rol_asiento;
                              window.location.href = "{{url('nuevos_asientos_roles/rol_por_sucursal/detalle')}}/" + id_rol_asiento;
                            }                           

                          },
                          error: function() {
                            console.log(data);
                          }
                      });
                    }
                });
            }
            else{
              //console.log(data.status+" "+data.msj);
              if(data.status == 'warning'){
                Swal.fire({
                  icon: 'warning',
                  title: 'Oops...',
                  text: `No existen roles para este periodo`
                });
              }else{
                $.ajax({
                type: 'get',
                url:"{{url('nuevos_asientos_roles/rol/sucursal')}}/"+ sucursal + "/"+ anio + "/" + id_mes,
                datatype: 'json',
                success: function(data){
                  if(data.msj == 'ok'){
                    var id_rol_asiento = data.id_rol_asiento;
                    window.location.href = "{{url('nuevos_asientos_roles/rol_por_sucursal/detalle')}}/" + id_rol_asiento;
                  }

                },
                error: function() {
                  console.log(data);
                }
              });
              }

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


  function imprimirTodo_sucursal() {
    var mes = parseInt($("#mes").val());
    var anio = parseInt($('#year').val());
    var sucursal = parseInt($('#sucursal').val());
    let msj = "";
    if (isNaN(anio)) {
      msj = msj + "Por favor, Seleccione el Año<br/>";
    }
    if (isNaN(mes)) {
      msj = msj + "Por favor, Seleccione el Mes<br/>";
    }
    if (isNaN(sucursal)) {
      msj = msj + "Por favor, Seleccione la sucursal<br/>";
    }
    if (msj != "") {
      alerta(msj);
      }
      else{
      $.ajax({
        type: 'POST',
        url: "{{route('nuevo_rol.existe_rol_pago_sucursal')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: {"year": anio, "mes": mes, "sucursal": sucursal},
        success: function(data) {
          if (data.msj == 'no') {
            mes = 0;
            anio = 0;
            sucursal = 0;
            alerta('No existe roles de pago');

          } else if (data.msj == 'si') {
            var ruta = '' + "{{route('nuevo_rol.imprimir_new_rol_pago_por_sucursal')}}/" + sucursal + "/" + mes + "/" + anio;

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

  function verificar_roles(){
    $.ajax({
      type: 'post',
      url: "{{route('buscador_roles.pago')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
      datatype: 'json',
      data: $("#roles_pago").serialize(),
      success: function(data) {
        $("#area_trabajo").html(data);

      },
      error: function(data) {
        console.log(data);
      }
    });
  }
  function buscar_roles_pago_nuevo() {

    var formulario = document.forms["roles_pago"];
    var anio = formulario.year.value;
    var id_mes = formulario.mes.value;
    var msj = "";
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

        setTimeout(() => {
            dataTable();
          }, 1000);

      },
      error: function(data) {
        console.log(data);
      }
    });
    
  }
</script>

@endsection