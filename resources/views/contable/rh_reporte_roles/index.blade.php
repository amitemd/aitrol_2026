@extends('contable.rh_reporte_roles.base')
@section('action-content')

<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

<div class="modal fade" id="detalle_rol" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div id="detalle_beneficios_div"></div>
    </div>
  </div>
</div>
  <section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="#">Nomina</a></li>
        <li class="breadcrumb-item active" aria-current="page">Reporte Rol Pago</li>
      </ol>
    </nav>
    <div class="box">
      <div class="row head-title">
        <div class="col-md-12 cabecera">
            <label class="color_texto" for="title">REPORTE ROL PAGO POR EMPRESA RRHH </label>
        </div>
      </div>
      <!-- /.box-header -->
    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="roles_pago" action="{{route('reporte_datos.rol')}}">
        {{ csrf_field() }}
        @php
        $id_empresa = Session::get('id_empresa');
        @endphp
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="id_empresa">Empresa: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <select class="form-control" id="id_empresa" name="id_empresa">
            @foreach($empresas as $value)
            @if($value->id == $id_empresa)
            <option @if($value->id == $id_empresa) selected @endif value="{{$value->id}}">{{$value->nombrecomercial}}</option>
            @endif
            @endforeach
          </select>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="year">Año</label>
        </div>
        <div class="form-group col-md-1 col-xs-10 container-4">
          <input type="text" name="year" id="year" class="form-control" placeholder="Año" value="{{ date('Y') }}" required>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="mes">Mes</label>
        </div>
        @php
        $Meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
        $mes_ac = date('m');
        @endphp
        <div class="form-group col-md-2 col-xs-10 container-4">
          <select id="mes" name="mes" class="form-control">
            <option value="">Seleccione...</option>
            @for ($i=1; $i<=12; $i++) <option value="{{$i}}" @if($i==$mes_ac) selected @endif>{{$Meses[($i)-1]}}</option>
              @endfor
          </select>
        </div>
        @php $cantidadSucursales = count($sucursales); @endphp
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="mes">{{trans('winsumos.sucursal')}}</label>
        </div>
        <div class="form-group col-md-2 col-xs-3" style="padding-left: 0px;padding-right: 0px;">

          <select id="sucursal" name="sucursal" class="form-control">
            @if($cantidadSucursales > 1)<option value="">Seleccione...</option>@endif
            @foreach ($sucursales as $sucursal)
            <option value="{{$sucursal->id}}">{{$sucursal->codigo_sucursal}}-{{$sucursal->nombre_sucursal}}</option>.
            @endforeach
          </select>

        </div>
        <div class="form-group col-md-6 col-xs-9">
          <button type="button" onclick="roles_empleados();" class="btn btn-primary" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar Roles de Pago
          </button>
          <button type="button" onclick="descargar_reporte();" class="btn btn-primary" id="btn_exportar">
            <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar Rol Pago
          </button>
          <button type="button" class="btn btn-success" onclick="detalle_beneficios()" id="btn_detalle_beneficios">
            Detalle Beneficios
          </button>
          <button type="submit" class="btn btn-primary btn-sm" formaction="{{route('nuevo_rol.subida_iess_horas_extras')}}"><span class="glyphicon glyphicon-triangle-right" aria-hidden="true"></span> Plantilla Carga Iess</button>

          
        </div>
      </form>
    </div>
    <div class="box box" style="border-radius: 8px;" id="area_trabajo">
    </div>
  </div>

  <!--script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script-->
  <!--script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script-->
  <!--script src="https://code.jquery.com/jquery-3.7.0.js"></script-->

  <!--script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script> 
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script-->


  <!-------->
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
    $(document).ready(function($) {
      roles_empleados();
      @php
      $fijos = [0, 1, 2, 47];
      $nomina = [3, 4, 5, 6, 7, 8, 9, 10];
      $nomina2 = [5, 6, 7, 8, 9, 10];
      $ingresos = [11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21];
      $egresos = [23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36];
      $resumen = [22, 37, 38];
      $acumulados = [3, 4, 38, 39, 40, 41, 42, 43, 44, 45, 46];
      $acumulados2 = [38, 39, 40, 41, 42, 43, 44, 45, 46];
      @endphp


    });


    const dataTable = () => {

      $('#example2').DataTable({
        'paging': false,
        'lengthChange': true,
        'searching': false,
        'ordering': true,
        'info': true,
        'autoWidth': false,
        'sInfoEmpty': true,
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
            customize: function(xlsx) {
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

    function roles_empleados() {

      var formulario = document.forms["roles_pago"];

      var id_emp = formulario.id_empresa.value;
      var id_anio = formulario.year.value;
      var id_mes = formulario.mes.value;

      //Mensaje 
      var msj = "";

      if (id_emp == "") {
        msj = msj + "Por favor, Seleccione la Empresa<br/>";
      }

      if (id_anio == "") {
        msj = msj + "Por favor, Seleccione el año<br/>";
      }

      if (id_mes == "") {
        //msj = msj + "Por favor, Seleccione el mes<br/>";
      }

      if (msj != "") {
        /*swal({
              title: "Error!",
              type: "error",
              html: msj
            });*/
        alert(msj);

        return false;
      }

        $.ajax({
        type: 'post',
        url:"{{route('buscador_roles.pago')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: $("#roles_pago").serialize(),
        success: function(data){
          $("#area_trabajo").html(data);
          //console.log(data);

          setTimeout(() => {
            dataTable();
          }, 1000);
        },
        error: function(data) {
          console.log(data);
        }
      });

    }

    function detalle_beneficios() {
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
      alert(msj);
      }
      else{
      $.ajax({
        type: 'POST',
        url: "{{route('contable.nuevo_rol.detalle_beneficios_rrhh')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: {"anio": anio, "mes": mes, "sucursal": sucursal},
        success: function(data) {
          $("#detalle_beneficios_div").html(data);  
          $("#detalle_rol").modal();
          },
          error: function() {
            console.log(data);
          }
        });
      }
    }

    function descargar_reporte() {
      $("#roles_pago").submit();
    }

    function cambiar_jornada(id, input) {
      console.log(id);
      console.log(input.value);
      $.ajax({
        type: 'post',
        url: "{{ route('contable.nuevo_rol.actualizar_jornada') }}",

        headers: {
              'X-CSRF-TOKEN': $('input[name=_token]').val()
          },
          datatype: 'json',
          data: {'id': id, 'value': input.value},
          success: function(data) {
              
              
          },
          error: function(data) {
          console.log(data);
        }
      });
    }

    function cambiar_pasante(id, input) {
      console.log(id, input);
      if ($('#pas' + id).is(':checked')) {
        cambiar_pasante2(id, '1');
      } else {
        cambiar_pasante2(id, '0');
      }

    }

    function cambiar_pasante2(id, val) {

      console.log(val);
      $.ajax({
        type: 'post',
        url: "{{ route('contable.nuevo_rol.actualizar_pasante') }}",

          headers: {
              'X-CSRF-TOKEN': $('input[name=_token]').val()
          },
          datatype: 'json',
          data: {'id': id, 'value': val},
          success: function(data) {
              
              
        },
        error: function(data) {
          console.log(data);
        }
      });
    }

    function recalcular_beneficios() {
      $.ajax({
        type: 'get',
        url: "{{ url('nrol_recalcular/decimo_cuarto') }}/" + $('#year').val() + "/" + $('#mes').val(),

        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        datatype: 'json',

        success: function(data) {

          location.reload();

        },
        error: function(data) {
          console.log(data);
        }
      });
    }

  </script>


</section>
@endsection