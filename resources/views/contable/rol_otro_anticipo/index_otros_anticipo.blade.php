@extends('contable.rol_otro_anticipo.base')
@section('action-content')

<style>
  div.dataTables_wrapper {
        width: 95%;
        margin: 0 auto;
  }

  .alerta_correcto{
    position: absolute;
    z-index: 9999;
    top: 100px;
    right: 10px;
  }

  .separator1 {
    width: 100%;
    height: 5px;
    clear: both;
  }

  .head-title {
    background-color: #888;
    margin-left: 0px;
    margin-right: 0px;
    height: 30px;
    line-height: 30px;
    color: #cccccc;
    text-align: center;
  }
	
</style>

<div class="modal fade" id="mod_val_anticipo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
  
  
  <div id="alerta_datos" class="alert alert-success alerta_correcto alert-dismissable" role="alert" style="display:none;">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    Guardado Correctamente
  </div>
  <section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item active" aria-current="page">Anticipo 1era Quincena
        </li>
      </ol>
    </nav>
    <div class="box">
      <div class="row head-title"> 
        <div class="col-md-12 cabecera">
            <label class="color_texto" for="title">ANTICIPOS 1ERA QUINCENA EMPLEADOS</label>
        </div>
      </div>

      <!-- /.box-header -->
      <div class="box-body dobra">
        <form method="POST" id="calculo_otro_anticipo" action="{{route('anticipos_quincena.reporte')}}"> 
            
          <!-- {{$fecha_actual}} -->
          <div class="form-group col-md-2 col-xs-3" style="padding-left: 0px;padding-right: 0px;">
            <label  for="year" class="texto col-md-2 control-label">Año:</label>
            <div class="col-md-9">
              <?php $anio = date("Y");  ?>
              <select id="year" name="year" class="form-control" onchange="imprimir_val_anticipos(1)">
                <!-- <option value="">Seleccione...</option> -->
                
                  @for($i=2019;$i<=$anio;$i++)
                    <option value='{{$i}}' selected>{{$i}}</option>;
                  @endfor
               
              </select>
            </div>
          </div>

          <div class="form-group col-md-2 col-xs-3" style="padding-left: 0px;padding-right: 0px;">
            <label for="mes" class="texto col-md-2 control-label">Mes:</label>
            <div class="col-md-9">
              <select id="mes" name="mes" class="form-control" onchange="imprimir_val_anticipos(1)">
               <!-- <option value="">Seleccione...</option> -->
                  @php
                    $Meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
                  @endphp         
                  @for ($i=1; $i<=12; $i++) 
                    <option value='{{$i}}' @if($i == $mes_actual) selected @endif > {{$Meses[($i)-1]}} </option>;
                  @endfor
              </select>
            </div>
          </div>
          <!--Fecha_Creacion
          {{ old('fecha_creacion') }}
          -->
          <div class="form-group col-md-4 col-xs-4">
            <label for="fecha_creacion" class="col-md-4 texto">Fecha:</label>
            <div class="col-md-7">
              <input id="fecha_creacion" type="date" class="form-control" name="fecha_creacion" value="{{$fecha_actual}}" required autofocus>
            </div>
          </div>

          <div class="form-group col-md-2 col-xs-2" style="padding-left: 0px;padding-right: 0px;">
            <button type="button" onclick="comprobar_empresa()" class="btn btn-primary" id="boton_buscar" style="display: none;">
                <span class="glyphicon glyphicon-floppy-save" aria-hidden="true"></span> Guardar
            </button>
          </div>

           <div class="form-group col-md-2 col-xs-2" style="padding-left: 0px;padding-right: 0px;">
            <a style="display: none;" onclick="imprimir_val_anticipos(2)" target="_blank" class="btn btn-primary" id="imprimir_rol">Imprimir Anticipos Quincena</a>
          </div>
        
          <div class="col-md-12" >
            <div class="col-md-12" style="text-align: center;display: none;" id="alert_existe_asiento">
              <label style="text-align: center; background-color: orange; color: white; " >&nbsp;&nbsp;YA EXISTEN REGISTROS DE ANTICIPOS PARA ESTE PERIODO&nbsp; &nbsp;</label>
            </div>
          </div>        

          <div class="separator1"></div>
          <div class="row head-title">
            <div class="col-md-12">
              <label class="color_texto" for="title">COMO FUE PAGADO EL ANTICIPO</label>
            </div>
          </div>
          <!--Tipo de Pago-->
          <div class="form-group col-xs-6">
            <label for="tipo_pago" class="col-md-3 texto">Tipo de Pago</label>
            <div class="col-md-6">
              <select class="form-control" id="tipo_pago" name="tipo_pago" onchange="obtener_seleccion()">
                @foreach($tipo_pago_rol as $value)
                <option value="{{$value->id}}">{{$value->tipo}}</option>
                @endforeach
              </select>
            </div>
          </div>
          <!-- Numero de Cuenta Beneficiario Cobra-->
          <div id="num_cuenta" class="form-group col-xs-6">
            <label for="numero_cuenta" class="col-md-3 texto">N# Cuenta</label>
            <div class="col-md-6">
              <input id="numero_cuenta" type="text" class="form-control" name="numero_cuenta" value="" autocomplete="off">
            </div>
          </div>
          <!--Banco Beneficiario Cobra-->
          <div id="id_banco" class="form-group col-xs-6">
            <label for="banco" class="col-md-3 texto">Banco</label>
            <div class="col-md-6">
              <select class="form-control" id="banco" name="banco">
                <option value="">Seleccione...</option>
                @foreach($lista_banco as $value)
                  <option value="{{$value->id}}">{{$value->nombre}}</option>
                @endforeach
              </select>
            </div>
          </div>
          <!--Fecha_Cheque-->
          <div id="fech_cheq" class="form-group col-xs-6">
            <label for="fecha_cheque" class="col-md-3 texto">Fecha Cheque:</label>
            <div class="col-md-6">
              <input id="fecha_cheque" type="date" class="form-control" name="fecha_cheque" value="{{ old('fecha_cheque') }}" required autofocus>
            </div>
          </div>
          <!--Numero de Cheque-->
          <div id="num_che" class="form-group col-xs-6">
            <label for="numero_cheque" class="col-md-3 texto">N # Cheque:</label>
            <div class="col-md-6">
              <input id="numero_cheque" type="text" class="form-control" name="numero_cheque" value="{{ old('numero_cheque') }}" onkeypress="return isNumberKey(event)" autocomplete="off">
            </div>
          </div>
          <!--Cuenta Saliente Paga-->
          <div id="id_cuenta_saliente" class="form-group col-xs-6">
            <label for="cuenta_saliente" class="col-md-3 texto">Cuent Saliente:</label>
            <div class="col-md-6">
              <select class="form-control" id="cuenta_saliente" name="cuenta_saliente">
                <option value="">Seleccione...</option>
                @foreach($bancos as $value)
                <option value="{{$value->cuenta_mayor}}">{{$value->nombre}}</option>
                @endforeach
              </select>
            </div>
          </div> 
          <!--<div class="form-group col-md-1 col-xs-1">
            <label class="texto" for="valor_porcent">% Quinc:</label>
          </div>
          <div class="form-group col-md-3 col-xs-10 container-4" style="padding-left: 15px;">
            <input class="form-control" type="text" id="valor_porcent" name="valor_porcent"  placeholder="Ingrese Porcentaje..."  value="" onkeypress="return isNumberKey(event)" onblur="checkformat(this);"/>
          </div>-->
          <div class="form-group col-md-12">
            <div class="form-row">
              <div id="contenedor">
                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                  <div class="row">
                    <div class="table-responsive col-md-12">
                      
                      <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                        <thead>
                          <tr class='well-dark'>
                            <!--<th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Seleccionar</th>-->
                            <th style="text-align: center;" width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Id Nomina</th>
                            <th style="text-align: center;" width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Identificacion</th>
                            <th style="text-align: center;" width="20%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Nombres</th>
                            <th style="text-align: center;" width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Fecha de Ingreso</th>
                            <th style="text-align: center;" width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Area</th>
                            <th style="text-align: center;" width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Cargo</th>
                            <th style="text-align: center;" width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Sueldo Mensual</th>
                            <th style="text-align: center;" width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Anticipo 1RA quinc</th>
                            <th style="text-align: center;" width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Accion</th>
                            <!--<th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Anticipo</th>-->
                            <!--<th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Acción</th>-->
                          </tr>
                        </thead>
                        <tbody id="tbl_otros_anticipos">
                        @php 
                        $sumaTotal = 0;
                        @endphp
                          @foreach ($empl_nomina as $value)
                            @php
                            $sumaTotal+=$value->val_anticip_quince;
                              $user = Sis_medico\User::find($value->id_user);
                              $obt_rol_pag = Sis_medico\Ct_Rol_Pagos::where('id_user',$value->id_user)
                                                                    ->where('id_empresa',$value->id_empresa)
                                                                    ->where('estado','1')->get()->last();
                            @endphp
                            <tr class="well">
                              <td>
                               @if(!is_null($value->id)){{$value->id}}@endif
                              </td>
                              <td>
                               @if(!is_null($user->id)){{$user->id}}@endif
                              </td>

                              <td>@if(!is_null($value->nombres)){{$value->user->apellido1}}  {{$value->user->apellido2}}  {{$value->user->nombre1}}  {{$value->user->nombre2}}@endif</td>

                              <td>@if(!is_null($value->fecha_ingreso)){{$value->fecha_ingreso}}@endif</td>
                              <td>
                                @if(!is_null($value->area))
                                  @if($value->area == '1')
                                    ADMINISTRATIVA
                                  @elseif($value->area == '2')
                                    MEDICA
                                  @endif
                                @endif
                              </td>
                              <td>@if(!is_null($value->cargo)){{$value->cargo}}@endif</td>
                              <td align=right>@if(!is_null($value->sueldo_neto)){{$value->sueldo_neto}}@endif</td>
                              <!--Validacion si tiene anticipo-->
                              @if(!is_null($value->val_anticip_quince))
                                <td><input readonly class=" form-control sum_anti" type="text" name="obs_anticipo" value="{{$value->val_anticip_quince}}" autocomplete="off" onkeypress="return isNumberKey(event)" onblur="checkformat(this);" onkeypress="return isNumberKey(event)" onblur="checkformat(this);"></td>
                              @else
                                <td><input readonly class="form-control sum_anti" type="text" name="val_anticip" id="val_anticip"  value="0.00" autocomplete="off" onkeypress="return isNumberKey(event)" onblur="checkformat(this);" onkeypress="return isNumberKey(event)" onblur="checkformat(this);"></td>
                              @endif
                              <td> 
                                <a class="btn btn-warning btn-xs" data-remote="{{route('update_anticipo_1era_quince', ['id_nomina' => $value->id])}}" data-toggle="modal" data-target="#mod_val_anticipo" style="float: center;"> <span class="glyphicon glyphicon-edit"></span></a>
                              </td>
                              <!--<td>
                                @if($value->estado == '1')
                                  <a href="#" target="_blank" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-eye-open"></span></a>
                                @endif
                              </td>-->
                             
                            </tr>
                          @endforeach
                          <tr>
                               <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td style="text-align: right;"><b>Total</b></td>
                              <td style="font-weight: bold;text-align:center">{{number_format($sumaTotal,2,'.','')}}</td>
                              </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-xs-2">
                      <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Total de registros  {{count($empl_nomina)}} </div>
                    </div> 
                  </div>
                  <br>
                  <!--<div class="form-group col-md-3 col-xs-6 pull-right" style="text-align: right;">
                    <a style="display: none;" onclick="imprimir_val_anticipos(2)" target="_blank" class="btn btn-primary" id="imprimir_rol">Imprimir Anticipos Quincena</a>
                  </div>
                  <div class="row">
                    <div class="col-sm-2">
                      <button type="button" id="buscarAnticipo" onclick="seleccionar_todo(true,'tbl_otros_anticipos')" class="btn btn-default">
                          <span class="glyphicon glyphicon-check" aria-hidden="true"></span>&nbsp; Marcar Todos
                      </button>
                    </div> 
                    <div class="col-xs-2">
                      <button type="button" id="buscarAnticipo" onclick="seleccionar_todo(false,'tbl_otros_anticipos')" class="btn btn-default">
                          <span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>&nbsp; Desmarcar Todos
                      </button>
                    </div>
                  </div>--> 
                </div>
              </div>
            </div>
          </div>
        </form>  
        <input type="hidden" name="nombre_empresa" id="nombre_empresa" value="{{$empresa->nombrecomercial}}"> 
        <input type="hidden" name="id_empresa" id="id_empresa" value="{{$empresa->id}}"> 
      </div>
    </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
  <script src="{{ asset ("/js/jquery-ui.js")}}"></script>
  <script type="text/javascript">
    
    $(document).ready(function(){
      @if($valida_anticipo)
        document.getElementById('imprimir_rol').style.display='block';
        document.getElementById('alert_existe_asiento').style.display='block';
        document.getElementById('boton_buscar').style.display='none';
      @else
        document.getElementById('imprimir_rol').style.display='none';
        document.getElementById('alert_existe_asiento').style.display='none';
        document.getElementById('boton_buscar').style.display='block';
      @endif  
      //imprimir_val_anticipos(1);
      document.getElementById("num_che").style.display='none';
      document.getElementById("fech_cheq").style.display='none';
      $('#fecha_cheque').val("");
    });

    //Borra Modal Actualiza Valor Anticipo Quincena
    $('#mod_val_anticipo').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
    });


    function imprimir_val_anticipos(id){
      
      var mes = parseInt($("#mes").val());
      var anio = parseInt($('#year').val());
      //var fech = $('#fecha_creacion').val();
      
      //Mensaje
      var msj = "";
      
      if(isNaN(anio)){
        msj = msj + "Por favor, Seleccione el Año<br/>";
      }
      if(isNaN(mes)){
        msj = msj + "Por favor, Seleccione el Mes<br/>";
      }

       // if(fech == ""){
       //    msj = msj + "Por favor, Seleccione la Fecha<br/>";
       // }
      
      if(msj != ""){
       // document.getElementById('imprimir_rol').style.display='none';
        alerta(msj);
      }else{

      }

      $.ajax({
        type: 'POST',
        url:"{{route('pdf_anticipos_quincena.descargar')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: {"year": anio, "mes": mes},
        success: function(data){
          //console.log(data);
          if(data.msj =='no'){
            mes =0;
            anio = 0; 
            //alerta('No existe anticipos 1era Quincena');
            document.getElementById('imprimir_rol').style.display='none';
            document.getElementById('alert_existe_asiento').style.display='none';
            document.getElementById('boton_buscar').style.display='block';
          }else if(data.msj =='si'){
            document.getElementById('imprimir_rol').style.display='block';
            document.getElementById('alert_existe_asiento').style.display='block';
            document.getElementById('boton_buscar').style.display='none';
            if (id == 2) {
                var ruta = ''+"{{route('descarga_pdf_anticipo.quincena')}}/"+mes+"/"+anio;
          
            window.open(ruta);
            location.reload();
            }
          }
        },
        error: function(){
          console.log(data);
        }
      }); 
    
    }

    function alerta(text){
      Swal.fire({
        icon: 'error',
        title: 'Error..!',
        html: `${text}`
      })
    }


    function obtener_seleccion(){

      var id_tipo = $("#tipo_pago").val();
      if (id_tipo == 1){ //ACREDITACION

        document.getElementById("id_cuenta_saliente").style.display='block';
        document.getElementById("id_banco").style.display='block';
        document.getElementById("num_cuenta").style.display='block';
        document.getElementById("num_che").style.display='none';
        document.getElementById("fech_cheq").style.display='none';

        $('#banco').val("");
        $('#numero_cuenta').val("");
        $('#numero_cheque').val("");
        $('#fecha_cheque').val("");

      }else if(id_tipo == 2){ //EFECTIVO

        document.getElementById("id_banco").style.display='none';
        document.getElementById("num_cuenta").style.display='none';
        document.getElementById("num_che").style.display='none';
        document.getElementById("fech_cheq").style.display='none';

        $('#numero_cheque').val("");
        $('#numero_cuenta').val("");
        $('#banco').val("");
        $('#cuenta_saliente').val("");

      }else if(id_tipo == 3){ //CHEQUE

        document.getElementById("id_banco").style.display='none';
        document.getElementById("num_cuenta").style.display='none';
        document.getElementById("num_che").style.display='block';
        document.getElementById("fech_cheq").style.display='block';

        $('#numero_cheque').val("");
        $('#numero_cuenta').val("");
        $('#banco').val("");
        $('#cuenta_saliente').val("");
        $('#fecha_cheque').val("");

      }

    }
    
    function seleccionar_todo(checked,tablename){
      var miTabla = document.getElementById(tablename);
      for (i=0; i<miTabla.rows.length; i++)
      {	
        miTabla.rows[i].getElementsByTagName("input")[0].checked = checked;
      }

      if(checked){
        calculo_anticipos_empleados();
      }else{
        //anular_calculo_anticipo();
      }
       
    } 
    
    $('body').on('click', '.relactivo', function () {
      if($(this).prop("checked") == true){
        $(this).parent().find('.veractivo').val($(this).val());
        //var tipo = $(this).parent().find('.totax').val();
        //alert(tipo);
        //calcula_anticipo(1);
      }else if($(this).prop("checked") == false){
        $(this).parent().find('.veractivo').val(0);
        //calcula_anticipo();
      }
    });

     function comprobar_empresa(){
        var id_empresa = $("#id_empresa").val();
        $.ajax({
          type: 'post',
          url:"{{route('nominaotrosanticipos.comprobar_empresa')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: "",
          success: function(data){
            if(id_empresa != data.id_empresa){
              Swal.fire({
                icon: 'error',
                title: 'Error no coincide la empresa!',
                html: "Empresa en sesion: "+data.nombre_empresa
              })
              location.reload();
            }else{
              comprobar();
            }
          },
          error: function(data){
             console.log(data);
          }
        });
    }

    function comprobar() {
      let anio = document.getElementById('year').value;
      let mes = document.getElementById('mes').value;

      let msj = "";
      if(anio == ""){
          msj = msj + "Por favor, Seleccione el Año<br/>";
        }
        
        if(mes == ""){
          msj = msj + "Por favor, Seleccione el Mes<br/>";
        }
        if(msj != ""){
            swal({
                  title: "Error!",
                  type: "error",
                  html: msj
                });
            return false;
        }else{
        $.ajax({
            url: "{{route('prestamo_empleado.busca_quincena')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: {
                'mes': mes,
                'anio':anio,
            },
            type: 'GET',
            dataType: 'json',
            success: function(data) {
               if(data == 'existe'){
                swal("Anticipos Repetidos!", "Ya existen registros de anticipos para este periodo !", "error");
                location.reload();
               }
               else{
                  registra_valor_anticipos();                
               }
            },
            error: function(xhr, status) {
                alert('Existió un problema');
            },
        });
      }
    }



    //Registra Valores de Anticipos Empleados
    function registra_valor_anticipos(){

      var formulario = document.forms["calculo_otro_anticipo"];
      var anio = formulario.year.value;
      var id_mes = formulario.mes.value;
      //var id_fecha = formulario.fecha_creacion.value;
      var fecha_crea = formulario.fecha_creacion.value;
      var tip_pago = formulario.tipo_pago.value;
      var num_cuenta = formulario.numero_cuenta.value;
      var banc = formulario.banco.value;
      var fech_cheq = formulario.fecha_cheque.value;
      var nume_cheq = formulario.numero_cheque.value;
      var cuent_sal = formulario.cuenta_saliente.value;

      var msj = "";
      if(fecha_crea == ""){
        msj = msj + "Por favor, Seleccione la Fecha de Creación<br/>";
      }

      if (cuent_sal == "") {
        msj = msj + "Por favor, Seleccione la Cuenta Saliente<br/>";
      }

      if(msj != ""){
        
        swal({
          title: "Error!",
          type: "error",
          html: msj
        });
        
        return false;
      }

      var txt_mes = "";

        if (id_mes == '12') {
            txt_mes = 'DICIEMBRE';}
        else if (id_mes == '11') {
            txt_mes = 'NOVIEMBRE';}
        else if (id_mes == '10') {
            txt_mes = 'OCTUBRE';}
        else if (id_mes == '9') {
            txt_mes = 'SEPTIEMBRE';}
        else if (id_mes == '8') {
            txt_mes = 'AGOSTO';}
        else if (id_mes == '7') {
            txt_mes = 'JULIO';}
        else if (id_mes == '6') {
            txt_mes = 'JUNIO';}
        else if (id_mes == '5') {
            txt_mes = 'MAYO';}
        else if (id_mes == '4') {
            txt_mes = 'ABRIL';}
        else if (id_mes == '3') {
            txt_mes = 'MARZO';}
        else if (id_mes == '2') {
            txt_mes = 'FEBRERO';}
        else if (id_mes == '1') {
            txt_mes = 'ENERO';}
        var nombre_empresa = document.getElementById('nombre_empresa').value;
       Swal.fire({
        title: "Empresa: "+nombre_empresa,
        text: "¿Guardar el Anticipo del Periodo Año: "+anio+"    Mes: "+txt_mes+ "?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '!Guardar!'
          }).then((result) => {
          if (result.isConfirmed) {

            id_nom = 0;
       
          var filas = $("#tbl_otros_anticipos").find("tr");

          var val_anticip= 0;
          var suma_anticipo = 0;
          $(".sum_anti").each(function(){
              val_anticip = parseFloat($(this).val());
              suma_anticipo += parseFloat($(this).val());
              id_nom= $(this).parent().prev().prev().prev().prev().prev().prev().prev().html().toString().trim();

              if(val_anticip > 0){
                
                registra_anticipo(id_nom,val_anticip,anio,id_mes,fecha_crea,tip_pago,num_cuenta,banc,fech_cheq,nume_cheq,cuent_sal);
                imprimir_val_anticipos(2);
               
              }
          });

          registra_asiento_anticipo(anio,id_mes,suma_anticipo,cuent_sal,fecha_crea);
          $("#alerta_datos").fadeIn(1000);
          $("#alerta_datos").fadeOut(3000); 

          }})

    }
    
    //Registra Anticipo 1era Quincema Empleado
    function registra_anticipo(id_nomina,valor,anio,id_mes,fecha_crea,tip_pago,num_cuenta,banc,fech_cheq,nume_cheq,cuent_sal){

      var cal = parseFloat(valor);
      var val_anticipo = (cal.toFixed(2));
      //console.log(val_anticipo);
      $.ajax({
          type: 'post',
          url:"{{route('anticipo_primera.quincena')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: {'val_anticip': val_anticipo,
                 'id_nom': id_nomina,
                 'anio': anio,
                 'mes': id_mes,
                 'fech_crea': fecha_crea,
                 'tip_pag': tip_pago,
                 'num_cuent': num_cuenta,
                 'banco': banc,
                 'fecha_cheq': fech_cheq,
                 'num_cheq': nume_cheq,
                 'cuent_salient': cuent_sal,
                 },
          success: function(data){
            console.log(data);
          },
          error: function(data){
            console.log(data);
          }
      });

    }

    //Registra Asiento Anticipo 1era Quincema Empleado
    function registra_asiento_anticipo(anio,id_mes,suma_anticipo,cuent_sal,fech_cre){

      var sum_ant = parseFloat(suma_anticipo);
      var red_sum_ant = (sum_ant.toFixed(2));

      $.ajax({
          type: 'post',
          url:"{{route('asiento_anticipo_primera.quincena')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: {'sum_anticip': red_sum_ant,
                 'anio': anio,
                 'mes': id_mes,
                 'cuent_sal': cuent_sal,
                 'fech_crea': fech_cre,
                },
          success: function(data){
            console.log(data);
          },
          error: function(data){
            console.log(data);
          }
      });

    }

    
    function calcula_anticipo(id_nomina){

      var formulario = document.forms["calculo_otro_anticipo"];
      var anio = formulario.year.value;
      var id_mes = formulario.mes.value;
      //alert(id_mes);
      var porcent_valor = formulario.valor_porcent.value;

      var msj = "";

      if(anio == ""){
        msj = msj + "Por favor, Seleccione el Año<br/>";
      }

      if(id_mes == ""){
        msj = msj + "Por favor, Seleccione el Mes<br/>";
      }

      if(porcent_valor == ""){
        msj = msj + "Por favor, Ingrese el valor de Porcentaje<br/>";
      }

      if(porcent_valor > 100){
        msj = msj + "El valor de Porcentaje no puede ser mayor al 100%<br/>";
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
          type: 'get',
          url:"{{ url('contable/nomina/anticipo/individual')}}/" + id_nomina,
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          success: function(data){
            location.reload();
          },
          error: function(data){
            console.log(data);
          }
      });
     
    }

    function isNumberKey(evt)
    {
     var charCode = (evt.which) ? evt.which : event.keyCode
     if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46){
      return false;

     }
      
     return true;
    }

    function checkformat(entry) { 
        
        var test = entry.value;

        if (!isNaN(test)) {
            entry.value=parseFloat(entry.value).toFixed(2);
        }

        if (isNaN(entry.value) == true){      
            entry.value='0.00';        
        }
        if (test < 0) {

            entry.value = '0.00';
        }
    
    }

    
    //Calcula el Anticipo de todos los empleados de la Empresa
    function calculo_anticipos_empleados(){
        
        var formulario = document.forms["calculo_otro_anticipo"];
        
        var anio = formulario.year.value;
        var id_mes = formulario.mes.value;
        var porcent_valor = formulario.valor_porcent.value;
        
        //Mensaje de Requeridos
        var msj = "";

        if(anio == ""){
          msj = msj + "Por favor, Seleccione el Año<br/>";
        }
        
        if(id_mes == ""){
          msj = msj + "Por favor, Seleccione el Mes<br/>";
        }

        if(porcent_valor == ""){
          msj = msj + "Por favor, Ingrese el valor de Porcentaje<br/>";
        }

        if(porcent_valor > 100){
          msj = msj + "El valor de Porcentaje no puede ser mayor al 100%<br/>";
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
        url:"{{route('anticipos_quincena.valor')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: $("#calculo_otro_anticipo").serialize(),
        success: function(data){
          //$("#area_trabajo").html(data);
        },
        error: function(data){
          console.log(data);
        }
      });
    
    }

    //Calculo de Anticipo Individual por Empleado
    function obtener_anticipos_individual(){

        var formulario = document.forms["calculo_otro_anticipo"];
        var anio = formulario.year.value;
        var id_mes = formulario.mes.value;
        var porcent_valor = formulario.valor_porcent.value;
        var selec_id = formulario.id_nomina.value;

        //alert(selec_id);
        
        //Mensaje de Requeridos
        var msj = "";

        if(anio == ""){
          msj = msj + "Por favor, Seleccione el Año<br/>";
        }
        
        if(id_mes == ""){
          msj = msj + "Por favor, Seleccione el Mes<br/>";
        }

        if(porcent_valor == ""){
          msj = msj + "Por favor, Ingrese el valor de Porcentaje<br/>";
        }

        if(porcent_valor > 100){
          msj = msj + "El valor de Porcentaje no puede ser mayor al 100%<br/>";
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
          url:"{{route('anticipos_quincena.valor')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#calculo_otro_anticipo").serialize(),
          success: function(data){
            //$("#area_trabajo").html(data);
          },
          error: function(data){
            console.log(data);
          }
        });
    }

    function isNumberKey(evt)
    {
     var charCode = (evt.which) ? evt.which : event.keyCode
     if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
        return false;

     return true;
    }

    function checkformat(entry) { 
        
        var test = entry.value;

        if (!isNaN(test)) {
            entry.value=parseFloat(entry.value).toFixed(2);
        }

        if (isNaN(entry.value) == true){      
            entry.value='0.00';        
        }
        if (test < 0) {

            entry.value = '0.00';
        }
    
    }

    //Anula Calculo de Anticipo de todos los empleados de la Empresa
    /*function anular_calculo_anticipo(){
        
        var formulario = document.forms["calculo_otro_anticipo"];
        
        var anio = formulario.year.value;
        var id_mes = formulario.mes.value;
        var porcent_valor = formulario.valor_porcent.value;
        
        //Mensaje de Requeridos
        var msj = "";

        if(anio == ""){
          msj = msj + "Por favor, Seleccione el Año<br/>";
        }
        
        if(id_mes == ""){
          msj = msj + "Por favor, Seleccione el Mes<br/>";
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
        url:"{{route('anticipos_quincena.valor')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: $("#calculo_otro_anticipo").serialize(),
        success: function(data){
          //$("#area_trabajo").html(data);
          //console.log(data);
        },
        error: function(data){
          console.log(data);
        }
      });
    
    }*/


    /*public function cambiarformatofecha($fecha)
    {
      $fecha     = str_replace('/', '-', $fecha);
      $timestamp = \Carbon\Carbon::parse($fecha)->timestamp;
      $fecha     = date('Y-m-d', $timestamp);
      return $fecha;
    }*/

  </script>
  </section>
@endsection
