@extends('contable.rh_reporte_roles.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<style type="text/css">
  .table-bordered>tbody>tr>td{
    font-size: 12px;
  }
  .form-group{
    margin-bottom: 1px;
  }
  .treeview-menu>li.active>a, .skin-blue .treeview-menu>li>a:hover
  {
    color: blue !important;
  }
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
  
<section class="content">
  <!-- Ventana modal editar -->
  <div class="modal fade" id="detalle_proceso" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">

      </div>
    </div>
  </div>
  <div class="box">
    <div class="row head-title">
      <div class="col-md-12 cabecera">
          <label class="color_texto" for="title">CONTROL DE ROLES POR EMPRESA</label>
      </div>
    </div>
    <span style="color: red;">{{$err}}</span>
    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="roles_pagos_anos" action="{{route("nuevo_rol.reporte_por_anio")}}">
          {{ csrf_field() }}
          <input type="hidden" name="id_empresa_reporte" id="id_empresa_reporte">
          <input type="hidden" name="anio_reporte" id="anio_reporte">
      </form>
      <form method="POST" id="roles_pago"  action="{{route('nuevo_rol.masivo_search')}}"> 
        {{ csrf_field() }}
          
        <div class="form-group col-md-3 col-xs-2" style="padding: 1px;">
          <label class="form-group col-md-12" for="id_empresa">Empresa: </label>
          <div class="form-group col-md-12 col-xs-10">
            <select class="form-control input-sm" id="id_empresa" name="id_empresa">
                <!--option value="">Seleccione...</option--> 
                @foreach($empresas as $value)
                  <option value="{{$value->id}}" @if($id_empresa==$value->id) selected @endif>{{$value->nombrecomercial}}</option>
                @endforeach
            </select>
          </div>
        </div>  
        <div class="form-group col-md-1 col-xs-2" style="padding: 1px;">
          <label class="form-group col-md-12" for="anio">Año</label>
          <div class="form-group col-md-12 col-xs-10">
            <select id="anio" name="anio" class="form-control input-sm">
              <!--option value="">Seleccione...</option-->  
                @for($i=2019;$i<=date('Y');$i++)
                  <option value="{{$i}}" @if($anio==$i) selected @endif>{{$i}}</option>"
                @endfor
            </select>
          </div>
        </div>  
        @php  
          $meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio','Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
        @endphp
        <div class="form-group col-md-2 col-xs-2" style="padding: 1px;">
          <label class="form-group col-md-12" for="mes">Mes</label>
          <div class="form-group col-md-12 col-xs-10">
            <select id="mes" name="mes" class="form-control input-sm">
              <!--option value="">Seleccione...</option-->  
                @for ($i=1; $i<=12; $i++)
                  <option value="{{$i}}" @if($i==$mes) selected @endif>{{$meses[($i)-1]}}</option>
                @endfor
            </select>
          </div>
        </div>
        <div class="form-group col-md-3 col-xs-2" style="padding: 1px;">
          <label class="form-group col-md-12" for="mes">Empleado</label>
          <div class="form-group col-md-12 col-xs-10">
            <input class="form-control input-sm" type="text" name="empleado" id="empleado" value="{{$empleado}}">
          </div>
        </div>  
        <div class="form-group col-md-1 col-xs-2"> 
          <br>
          <button type="submit"> Buscar </button>
          <!--button type="button" onclick="descargar_reporte();"  class="btn btn-primary" id="btn_exportar">
            <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar Rol Pago
          </button-->
        </div>
        <div class="form-group col-md-2 col-xs-2"> 
          <br>
          <button type="button" onclick="generar_roles();"><span class="glyphicon glyphicon-triangle-right" aria-hidden="true"></span> Generar Roles</button>
        </div>
        <div class="form-group col-md-12 col-xs-2"> </div>
        <div class="form-group col-md-3 col-xs-2" style="padding: 1px;">
          <label class="form-group col-md-12" for="mes">Archivo</label>
          <div class="form-group col-md-12 col-xs-10">
            <input name="archivo" id="archivo" type="file" class="archivo form-control input-sm" />
          </div>
        </div> 
        <div class="form-group col-md-2 col-xs-2" > 
          <br>
          <button type="button" onclick="subir_horas_extras();"><span class="glyphicon glyphicon-triangle-right" aria-hidden="true"></span> Subir Horas Extras</button>
        </div>
        
        <div class="form-group col-md-2 col-xs-2" style="padding: 1px;">
          <label for="prestamos" class="form-group col-md-12">Prestamos</label>
          <div class="form-group col-md-12 col-xs-10">
            <select class="form-control input-sm" name="prestamos" id="prestamos">
              <option value="1">Quirografario</option>
              <option value="2">Hipotecario</option>
            </select>
          </div>
        </div> 

        <div class="form-group col-md-2 col-xs-2" > 
          <br>
          <button type="button" onclick="subir_prestamos();"><span class="glyphicon glyphicon-triangle-right" aria-hidden="true"></span> Subir Prestamos</button>
        </div>
        <div class="form-group col-md-2 col-xs-2" > 
          <br>
          <button type="button" onclick="reporte_roles_anio();"><span class="glyphicon glyphicon-triangle-right" aria-hidden="true"></span> {{trans('nomina.roles_por_año')}}</button>
        </div>
        <div class="form-group col-md-2 col-xs-2" > 
          <br>
          <button type="submit" class="btn btn-primary" formaction="{{route('nuevo_rol.excel_plantilla_nomina')}}"> 
            <span class="glyphicon glyphicon-download" aria-hidden="true"></span> {{trans('nomina.plantilla_excel')}}
          </button>
        </div>
      </form> 
    </div>
       
      
      @if($anio!=null && $mes!=null)

        <ul data-widget="tree">
          <li class="treeview">
            <!--a href="#">{{$anio}}-{{$meses[$mes - 1]}}</a-->
            <a href="javascript:mostrar_detalle_prop()" id="id_det" class="ocultar">{{$anio}}-{{$meses[$mes - 1]}}</b></a>
            <ul class="treeview-menu">
              @foreach($procesos as $proceso)
              @php $usuario_crea = Sis_medico\User::find($proceso->id_usuariocrea); @endphp
              <li class="detail" style="display: none;">
                @if($proceso->tipo_proceso=='HORAS_EXTRAS_POR_EMPRESA')
                <a href="{{route('nuevo_rol.detalle_he_rol',['id' => $proceso->id])}}" data-toggle="modal" data-target="#detalle_proceso" >
                @elseif($proceso->tipo_proceso=='PRESTAMOS_QUIRO_POR_EMPRESA')
                <a href="{{route('nuevo_rol.detalle_iess_rol',['id' => $proceso->id])}}" data-toggle="modal" data-target="#detalle_proceso" >
                @elseif($proceso->tipo_proceso=='PRESTAMOS_HIPOT_POR_EMPRESA')
                <a href="{{route('nuevo_rol.detalle_iess_rol',['id' => $proceso->id])}}" data-toggle="modal" data-target="#detalle_proceso" >  
                @else
                <a href="#" >
                @endif 
                {{$proceso->tipo_proceso}}:</a> Procesados:{{$proceso->procesados}} - No Procesados:{{$proceso->no_procesados}} {{$proceso->observacion}} <b>Creado por: </b> 
                {{$usuario_crea->nombre1}} {{$usuario_crea->apellido1}} {{$usuario_crea->apellido2}} <b>Fecha: </b> {{$proceso->created_at}}
              </li>
              @endforeach
            </ul>
          </li>
        </ul>
      @endif

      <div class="box-body dobra" style="margin: 0 auto; width: fit-content;">
        <label>  Sueldo Básico: </label> <label style="color: red;">@if(!is_null($sueldo_basico)) {{$sueldo_basico->valor}} @endif</label> 
        @if(!is_null($sueldo_basico))
          <a href="{{ route('configuracion_valores.editar', ['id' => $sueldo_basico->id]) }}" target="_blank" class="btn btn-success btn-gray">
          <i class="glyphicon glyphicon-edit" aria-hidden="true">Cambiar</i>
          </a>
        @endif
      </div>

      <div class="box box" style="border-radius: 8px;" id="area_trabajo">
        
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
          <div class="row">
            <div class="table-responsive col-md-12">
              <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info" style="width: 100%">
                <thead>
                  <tr class='well-dark'>
                    <th width="10%">Empresa</th>
                    <th width="5%">Año/Mes</th>
                    <th width="15%">Empleado</th>
                    <th width="5%">FR</th>
                    <th width="5%">Sueldo</th>
                    <th width="5%">Extras 50%</th>
                    <th width="5%">Extras 100%</th>
                    <th width="5%">T.Ingresos</th>
                    <th width="5%">Prestamos</th>
                    <th width="5%">Saldos</th>
                    <th width="5%">Quirogra.</th>
                    <th width="5%">Hipoteca.</th>
                    <th width="5%">T.Egresos</th>
                    <th width="5%">Neto</th>
                    <th width="5%" style="text-align: center;">Cert. <input type="checkbox" name="cert_mas" id="cert_mas" onclick="certificar_masivo()"></th>
                    <th width="10%" style="text-align: center;">Accion &nbsp;<a  class="btn btn-primary  btn-xs" onclick="reenviar_mail_masivo()"><span class="fa fa-envelope"></span></a></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($roles as $rol)
                    @php 
                      $detalle = $rol->detalle->first(); 
                      if($rol->mes == '1') $rmes = 'Enero';
                      elseif($rol->mes == '2') $rmes = 'Febrero';
                      elseif($rol->mes == '3') $rmes = 'Marzo';
                      elseif($rol->mes == '4') $rmes = 'Abril';
                      elseif($rol->mes == '5') $rmes = 'Mayo';
                      elseif($rol->mes == '6') $rmes = 'Junio';
                      elseif($rol->mes == '7') $rmes = 'Julio';
                      elseif($rol->mes == '8') $rmes = 'Agosto';
                      elseif($rol->mes == '9') $rmes = 'Septiembre';
                      elseif($rol->mes == '10') $rmes = 'Octubre';
                      elseif($rol->mes == '11') $rmes = 'Noviembre';
                      elseif($rol->mes == '12') $rmes = 'Diciembre';

                      $fecha = date("Y-m-d");
                      $fec   = new DateTime($fecha);
                      $fec2  = new DateTime($rol->ct_nomina->fecha_ingreso);
                      $diff  = $fec->diff($fec2);
                      $days  = $diff->days;

                      $intervalMeses = $diff->format("%m");      
                      $intervalAnos  = $diff->format("%y")*12;
                      $intervalDias  = $diff->format("%d");

                      $meses_totales = $intervalMeses + $intervalAnos;

                      if($meses_totales >= 11 ){
                        $txt = 'X';
                      }else{
                        $txt = ' ';  
                      }

                    @endphp
                    <tr>
                      <td>{{$rol->empresa->nombrecomercial}}</td>
                      <td>{{$rol->anio}}-{{$rmes}}</td>
                      <td>{{$rol->usuario->apellido1}} {{$rol->usuario->apellido2}} {{$rol->usuario->nombre1}} {{$rol->usuario->nombre2}} <span style="font-size: 9px;color: blue;">mail:@if(is_null($rol->ct_nomina->mail_opcional)){{$rol->usuario->email}}@else{{$rol->ct_nomina->mail_opcional}}@endif</span></td>
                      <td style="text-align: right;text-align: center;">@if($txt == 'X') <i class="fa fa-solid fa-check" style="font-size: 24px;color: green;"></i> @else <span style="font-size: 20px;color: red;">X</span> @endif</td>
                      <td style="text-align: right;">@if(!is_null($detalle)){{$detalle->sueldo_mensual}}@endif</td>
                      <td style="text-align: right;">@if(!is_null($detalle)){{$detalle->sobre_tiempo50}}@endif</td>
                      <td style="text-align: right;">@if(!is_null($detalle)){{$detalle->sobre_tiempo100}}@endif</td>
                      <td style="text-align: right;">@if(!is_null($detalle)){{$detalle->total_ingresos}}@endif</td>
                      <td style="text-align: right;">@if(!is_null($detalle)){{$detalle->prestamos_empleado}}@endif</td>
                      <td style="text-align: right;">@if(!is_null($detalle)){{$detalle->saldo_inicial_prestamo}}@endif</td>
                      <td style="text-align: right;">@if(!is_null($detalle)){{$detalle->total_quota_quirog}}@endif</td>
                      <td style="text-align: right;">@if(!is_null($detalle)){{$detalle->total_quota_hipot}}@endif</td>
                      <td style="text-align: right;">@if(!is_null($detalle)){{$detalle->total_egresos}}@endif</td>
                      <td style="text-align: right;">@if(!is_null($detalle)){{$detalle->neto_recibido}}@endif</td>
                      <td style="text-align: center;"><input type="checkbox" name="certificado{{$rol->id}}" id="certificado{{$rol->id}}" @if($rol->certificado) checked @endif onclick="certificar('{{$rol->id}}')">
                      </td>
                      @if(Auth::user()->id_tipo_usuario == '1')
                      <td>
                        @if($rol->estado == '1')
                          <!--a href="{{route('rol_pago.editar', ['id' => $rol->id])}}" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-eye-open"></span></a-->
                          <a target="_blank" href="{{route('rol_pago.imprimir', ['id' => $rol->id])}}" class="btn btn-success  btn-xs"><span class="glyphicon glyphicon-download-alt"></span></a>
                          <button class="btn btn-danger btn-xs" onclick="eliminar_rol('{{$rol->id}}')"><span class="glyphicon glyphicon-trash"></span></button>
                          <a  class="btn btn-primary  btn-xs" onclick="reenviar_mail('{{$rol->id}}')"><span class="fa fa-envelope"></span></a>
                          <a href="{{route('nuevo_rol.editar_nuevo_rol', ['id' => $rol->id])}}" class="btn btn-warning btn-xs"><span class="glyphicon glyphicon-eye-open"></span></a>
                          <a href="{{route('nuevo_rol.index_log_nomina',['id_rol' => $rol->id])}}" class="btn btn-info btn-xs" target="_blanck" id="log_nomina">
                          <span class="glyphicon glyphicon-eye-open" aria-hidden="true" style="font-size: 16px">Log</span></a>
                        @endif
                      </td>
                      @else
                        @php 
                          $validar_asiento = Sis_medico\RolAsiento::where('mes', $rol->mes)->where('anio', $rol->anio)->where('id_empresa', $rol->empresa->id)->where('estado',1)->first();
                        @endphp
                        <td>
                          @if($rol->estado == '1')
                            <!--a href="{{route('rol_pago.editar', ['id' => $rol->id])}}" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-eye-open"></span></a-->
                            <a target="_blank" href="{{route('rol_pago.imprimir', ['id' => $rol->id])}}" class="btn btn-success  btn-xs"><span class="glyphicon glyphicon-download-alt"></span></a>
                            @if(is_null($validar_asiento))<button class="btn btn-danger btn-xs" onclick="eliminar_rol('{{$rol->id}}')"><span class="glyphicon glyphicon-trash"></span></button>@endif
                            <a  class="btn btn-primary  btn-xs" onclick="reenviar_mail('{{$rol->id}}')"><span class="fa fa-envelope"></span></a>
                            @if(is_null($validar_asiento))<a href="{{route('nuevo_rol.editar_nuevo_rol', ['id' => $rol->id])}}" class="btn btn-warning btn-xs"><span class="glyphicon glyphicon-eye-open"></span></a>@endif
                            @if(!is_null($validar_asiento)) <spam>!Asiento ya generado!</spam> @endif
                            <a href="{{route('nuevo_rol.index_log_nomina',['id_rol' => $rol->id])}}" class="btn btn-info btn-xs" target="_blanck" id="log_nomina">
                            <span class="glyphicon glyphicon-eye-open" aria-hidden="true" style="font-size: 16px">Log</span></a>
                          @endif
                        </td>
                      @endif
                    </tr>
                  @endforeach
                </tbody>
                <tfoot>
                </tfoot>

              </table>
            </div>
          </div>
          <div class="row">
            
          </div>
        </div>
      </div>
    </div>
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
  <script src="{{ asset ("/js/jquery-ui.js")}}"></script>

  <script type="text/javascript">

    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      'order'       : [[ 1, "desc" ]]
    })

    function mostrar_detalle_prop() {
      var clase = $('#id_det').hasClass('ocultar');
      if(clase){
        $('.detail').show();
        $('#id_det').removeClass('ocultar');
        $('#id_det').addClass('muestralo');
      }else{
        $('.detail').hide();
        $('#id_det').removeClass('muestralo');
        $('#id_det').addClass('ocultar');
      }
      
      
    }

    function reporte_roles_anio(){
      document.getElementById("anio_reporte").value = document.getElementById("anio").value;
      document.getElementById("id_empresa_reporte").value = document.getElementById("id_empresa").value;
      $("#roles_pagos_anos").submit();
    }
    function roles_empleados(){

        var formulario = document.forms["roles_pago"];

        var id_emp = formulario.id_empresa.value;
        var id_anio = formulario.year.value;
        var id_mes = formulario.mes.value;

        //Mensaje 
        var msj = "";

        if(id_emp == ""){
          msj = msj + "Por favor, Seleccione la Empresa<br/>";
        }

        if(id_anio == ""){
          msj = msj + "Por favor, Seleccione el año<br/>";
        }
        
        if(id_mes == ""){
          msj = msj + "Por favor, Seleccione el mes<br/>";
        }

        if(msj != ""){
            swal({
                  title: "Error!",
                  type: "error",
                  html: msj
                });
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
          },
          error: function(data){
            alert("Ha ocurrido un error");
          }
        });

    }

    function descargar_reporte() {
      $( "#roles_pago" ).submit(); 
    }

    function certificar(id){
      if ($('#certificado'+id).is(':checked')) {
        var cert = '1';
      } else {
        var cert = '0';
      }
      //alert(cert);

      $.ajax({
        type: 'get',
        url:"{{url('masivos_nuevo_rol/certificar')}}/"+id+'/'+cert,
        
        datatype: 'json',
        
        success: function(data){
          
        },
        error: function(data){
          alert("Ha ocurrido un error"); 
        }
      });

    }

    function generar_roles() {
      var mes = $('#mes option:selected').text();
      var anio = $("#anio").val();
      var empresa = $('#id_empresa option:selected').text();
      var msnerr = '';
      if(mes == ''){
        msnerr = msnerr + "Ingrese el mes\n";
      }
      if(anio == ''){
        msnerr = msnerr + "Ingrese el año\n";
      }
      if(msnerr==''){
        Swal.fire({
          title: 'Desea Generar Roles para : '+ empresa +': '+ anio +' - '+ mes,
          showDenyButton: true,
          showCancelButton: true,
          confirmButtonText: 'Enviar',
          denyButtonText: 'No Enviar',
          showLoaderOnConfirm: true,
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
              $.ajax({
                type: 'post',
                url:"{{route('nuevo_rol.masivo_genera_roles')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'json',
                data: { id_empresa : $('#id_empresa').val(), anio : $('#anio').val(), mes : $('#mes').val()},
                success: function(data){
                  location.reload();
                  //console.log(data);
                },
                error: function(data){
                  alert("Ha ocurrido un error");
                }
              });   
            }
        });
        
      }else{
        alert(msnerr);
      }  
    }

    function subir_horas_extras(){
      var form = $('#roles_pago')[0];
      var data = new FormData(form);
      $.ajax({
        enctype: 'multipart/form-data',
        type: "post",
        url: "{{ route('nuevo_rol.he_valida_ejecutado')}}",
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        datatype: "html",
        data: data,
        processData: false,
        contentType: false,
        cache: false,
        timeout: 600000,
        success: function(datahtml) {
          //console.log(datahtml);
          if (datahtml.estado == 'ok') {
            subir_horas_extras2();
          }
          if (datahtml.estado == 'wrn') { 
            var confirmar = confirm(datahtml.mensaje);
            if(confirmar){
              subir_horas_extras2();  
            }  
          }
        },
        error: function(datahtml) {
          alert("ocurrio un error");
        }
      });  
    }
    
    function subir_horas_extras2() {
      
      var file = $('#archivo').val();
      var mes = $('#mes option:selected').text();
      var anio = $("#anio").val();
      var empresa = $('#id_empresa option:selected').text();
      var msnerr = '';
      if(mes == ''){
        msnerr = msnerr + "Ingrese el mes\n";
      }
      if(anio == ''){
        msnerr = msnerr + "Ingrese el año\n";
      }
      if(file == ''){
        msnerr = msnerr + "Archivo no subido\n"
      }
      if(msnerr==''){
        Swal.fire({
          title: 'Desea Subir Horas Extras para : '+ empresa +': '+ anio +' - '+ mes,
          showDenyButton: true,
          showCancelButton: true,
          confirmButtonText: 'Enviar',
          denyButtonText: 'No Enviar',
          showLoaderOnConfirm: true,
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
              event.preventDefault();
              var form = $('#roles_pago')[0];
              var data = new FormData(form);
              $.ajax({
                enctype: 'multipart/form-data',
                type: "post",
                url: "{{ route('nuevo_rol.masivos_horario_extra')}}",
                headers: {
                  'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: "html",
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                success: function(datahtml) {
                  //console.log(datahtml);
                  if (datahtml.tipo == 'ok') {
                    swal(datahtml.mensaje, "Correcto", "success");
                    setTimeout(function() {
                      location.reload();
                    }, 2000);
                  }
                  if (datahtml.tipo == 'err') {
                    swal(datahtml.mensaje, "Atencion", "warning");
                    setTimeout(function() {
                      location.reload();
                    }, 2000);
                    
                  }
                },
                error: function(datahtml) {
                  //console.log(datahtml);
                }
              });  
            }
        });
        
      }else{
        alert(msnerr);
      }  
     
    }

    function subir_prestamos(){
      var form = $('#roles_pago')[0];
      var data = new FormData(form);
      $.ajax({
        enctype: 'multipart/form-data',
        type: "post",
        url: "{{ route('nuevo_rol.p_valida_ejecutado')}}",
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        datatype: "html",
        data: data,
        processData: false,
        contentType: false,
        cache: false,
        timeout: 600000,
        success: function(datahtml) {
          //console.log(datahtml);
          if (datahtml.estado == 'ok') {
            subir_prestamos2();
          }
          if (datahtml.estado == 'wrn') { 
            var confirmar = confirm(datahtml.mensaje);
            if(confirmar){
              subir_prestamos2();  
            }  
          }
        },
        error: function(datahtml) {
          alert("ocurrio un error");
        }
      });  
    }

    function subir_prestamos2() {
      var file = $('#archivo').val();
      var mes = $('#mes option:selected').text();
      var anio = $("#anio").val();
      var empresa = $('#id_empresa option:selected').text();
      var prestamo = $('#prestamos option:selected').text();
      
      var msnerr = '';
      if(mes == ''){
        msnerr = msnerr + "Ingrese el mes\n";
      }
      if(anio == ''){
        msnerr = msnerr + "Ingrese el año\n";
      }
      if(file == ''){
        msnerr = msnerr + "Archivo no subido\n"
      }
      if(msnerr==''){
        Swal.fire({
          title: 'Desea Subir Prestamo ' + prestamo + ' para : ' + empresa +': '+ anio +' - '+ mes,
          showDenyButton: true,
          showCancelButton: true,
          confirmButtonText: 'Enviar',
          denyButtonText: 'No Enviar',
          showLoaderOnConfirm: true,
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
              event.preventDefault();
              var form = $('#roles_pago')[0];
              var data = new FormData(form);
              $.ajax({
                enctype: 'multipart/form-data',
                type: "post",
                url: "{{ route('nuevo_rol.masivos_prestamos')}}",
                headers: {
                  'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: "html",
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                success: function(datahtml) {
                  //console.log(datahtml);
                  if (datahtml.tipo == 'ok') {
                    swal(datahtml.mensaje, "Correcto", "success");
                    setTimeout(function() {
                      location.reload();
                    }, 2000);
                  }
                  if (datahtml.tipo == 'err') {
                    swal(datahtml.mensaje, "Atencion", "warning");
                    setTimeout(function() {
                      location.reload();
                    }, 2000);
                    
                  }
                },
                error: function(datahtml) {
                  //console.log(datahtml);
                }
              });  
            }
        });
        
      }else{
        alert(msnerr);
      }  
    }

    function certificar_masivo(){

      var mes = $('#mes option:selected').text();
      var anio = $("#anio").val();
      var empresa = $('#id_empresa option:selected').text();
      var msnerr = '';
      if(mes == ''){
        msnerr = msnerr + "Ingrese el mes\n";
      }
      if(anio == ''){
        msnerr = msnerr + "Ingrese el año\n";
      }
      if(msnerr==''){
        Swal.fire({
          title: 'Desea Certificar los Roles para : '+ empresa +': '+ anio +' - '+ mes,
          showDenyButton: true,
          showCancelButton: true,
          confirmButtonText: 'Enviar',
          denyButtonText: 'No Enviar',
          showLoaderOnConfirm: true,
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
              $.ajax({
                type: 'post',
                url:"{{route('nuevo_rol.masivo_certificar_mes')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'json',
                data: { id_empresa : $('#id_empresa').val(), anio : $('#anio').val(), mes : $('#mes').val()},
                success: function(data){
                  location.reload();
                  //console.log(data);
                },
                error: function(data){
                  alert("Ha ocurrido un error");
                }
              });   
            }
        });

      }  
    }

    function reenviar_mail(id){
      Swal.fire({
        title: 'Estas Seguro de Reenviar el Mail de Rol?',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: 'Enviar',
        denyButtonText: 'No Enviar',
        showLoaderOnConfirm: true,
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
          $.ajax({
            url:"{{asset('contable/rol/pago/envio/correo/')}}/"+id,
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            type: 'GET',
            success: function(data){
                if(data == 'ok'){
                  Swal.fire('Enviado Correctamente!', '', 'success');
                }

            },
            error: function(data){
                Swal.fire('Error al Enviar el Correo!', '', 'error');
            }
          });

        }
      });
    }

    function reenviar_mail_sin_nt(id){
      
      $.ajax({
        url:"{{asset('contable/rol/pago/envio/correo/')}}/"+id,
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        type: 'GET',
        success: function(data){

        },
        error: function(data){
            Swal.fire('Error al Enviar el Correo!', '', 'error');
        }
      });

       
    }

    function reenviar_mail_masivo(){

      var mes = $('#mes option:selected').text();
      var anio = $("#anio").val();
      var empresa = $('#id_empresa option:selected').text();

      Swal.fire({
        title: 'Estas Seguro de Reenviar el Mail a ' + empresa + ': '+ anio + '-' + mes + '?',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: 'Enviar',
        denyButtonText: 'No Enviar',
        showLoaderOnConfirm: true,
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
          @foreach ($roles as $rol)
            reenviar_mail_sin_nt('{{$rol->id}}');
          @endforeach  

        }
      });
    }

    

    function eliminar_rol(id){
      Swal.fire({
        title: 'Estas Seguro de Eliminar el Rol?',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: 'Enviar',
        denyButtonText: 'No Enviar',
        showLoaderOnConfirm: true,
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
          $.ajax({
            url:"{{asset('nuevo_rol_e/pago/eliminar')}}/"+id,
            type: 'GET',
            success: function(data){
                location.reload();

            },
            error: function(data){
                Swal.fire('Error al Enviar el Correo!', '', 'error');
            }
          });

        }
      });
    }
  
    function plantilla_excel(){
      var mes = $('#mes option:selected').val();
      var anio = $("#anio").val();
      var empresa = $('#id_empresa option:selected').val();
      $.ajax({
        url:"{{route('nuevo_rol.excel_plantilla_nomina')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        type: 'post',
        data: {
          'mes': mes,
          'anio': anio,
          'empresa': empresa,
        },
        success: function(data){
          
        },
        error: function(data){
            Swal.fire(`{{trans('contableM.erroralenvcorreo')}}`, '', 'error');
        }
      });
    }
  </script>

  
  </section>
@endsection
