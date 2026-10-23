<style type="text/css">
  .h3 {
    font-family: 'BrixSansBlack';
    font-size: 8pt;
    display: block;
    background: #3d7ba8;
    color: #FFF;
    text-align: center;
    padding: 3px;
    margin-bottom: 5px;
  }

  .info_nomina {
    width: 69%;
  }

  .round {
    border-radius: 10px;
    border: 1px solid #3d7ba8;
    overflow: hidden;
    padding-bottom: 15px;
  }

  .datos_nomina {
    font-size: 0.8em;
  }

  .mValue {
    width: 79%;
    display: inline-block;
    vertical-align: top;
    padding-left: 7px;
    font-size: 0.9em;
  }

  #rol_pago {
    width: 100%;
    margin-bottom: 10px;
  }


  .info_nomina .col-xs-8 {
    padding-left: 10px;
    font-size: 0.9em;
  }

  .info_nomina .round {
    padding-top: 10px;
  }

  .titulo-wrapper {
    width: 100%;
    text-align: center;
  }

  .modal-body .form-group {
    margin-bottom: 0px;
  }

  .h3.modal_h3 {
    font-family: 'BrixSansBlack';
    font-size: 8pt;
    display: block;
    background: #3d7ba8;
    color: #FFF;
    text-align: center;
    padding: 3px;
    margin-bottom: 5px;
    padding: 7px;
    font-size: 1em;
    margin-bottom: 15px;
  }

  .h3.modal_h3_2 {
    margin-top: -20px !important;
    margin-bottom: 25px !important;
    padding: 7px;
    font-size: 1em;
  }

  .swal-title {
    margin: 0px;
    font-size: 16px;
    box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.21);
    margin-bottom: 28px;
  }

  .separator {
    width: 100%;
    height: 20px;
    clear: both;
  }

  .separator1 {
    width: 100%;
    height: 5px;
    clear: both;
  }


  /* Nuevo CSS*/

  .mLabel {
    color: #777;
    font-size: 0.9rem;
    margin-bottom: 0;
    line-height: 10px;
  }

  .texto {
    color: #777;
    font-size: 0.9rem;
    margin-bottom: 0;
    line-height: 15px;
  }

  .color_texto {
    color: #FFF;
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

  .t9 {
    font-size: 0.9rem;
  }

  .well-dark {
    background-color: #cccccc;
  }
</style>

<div class="modal-content" style="width: 100%;">
  <div class="modal-header" style="padding-top: 5px; padding-bottom: 1px;">
    <div class="row head-title">
      <div class="col-md-12">
        <label class="color_texto" for="title">DATOS EMPLEADO</label>
        <button type="button" id="cerrar" class="close" data-dismiss="modal">&times;</button>
      </div>
    </div>
  </div>
  @php
               $tipo_pago = Sis_medico\Ct_Rh_Tipo_Pago::where('id',$otros_anticipos->id_tipo_pago)->where('estado','1')->first();
               $banco = Sis_medico\Ct_Bancos::where('id',$otros_anticipos->banco_beneficiario)->where('estado','1')->first();
               $cuenta_saliente = Sis_medico\Ct_Caja_Banco::where('cuenta_mayor',$otros_anticipos->cuenta_saliente)->where('estado','1')->first();
            @endphp
  <div class="box-body dobra ">
    <form id="ver_otros_anticipo" method="get">
      <input name="nombre" id="nombre" type="hidden" value="{{$nombre_completo}}" />
      <input name="id_empl" id="id_empl" type="text" class="hidden" value="@if(!is_null($empl_nomina->id_user)){{$empl_nomina->id_user}}@endif">
      <input name="id_empresa" id="id_empresa" type="text" class="hidden" value="@if(!is_null($empl_nomina->id_empresa)){{$empl_nomina->id_empresa}}@endif">
      <input name="id_ban" id="id_ban" type="text" class="hidden" value="@if(!is_null($empl_nomina->banco)){{$empl_nomina->banco}}@endif">
      <input name="num_cuent" id="num_cuent" type="text" class="hidden" value="@if(!is_null($empl_nomina->numero_cuenta)){{$empl_nomina->numero_cuenta}}@endif">
        
      <div class="form-group col-xs-6">
        <label for="nombre_empleado" class="col-md-4 texto">NOMBRE</label>
        <div class="col-md-7">
          @if(!is_null($nombre_completo)){{$nombre_completo}}@endif
        </div>
      </div>
      <div class="form-group col-xs-3">
        <label for="cedula_empleado" class="col-md-4 texto">CEDULA</label>
        <div class="col-md-7">
          @if(!is_null($id_i)){{$id_i}}@endif
        </div>
      </div>
      <div class="form-group col-xs-3">
        <label for="cargo_empleado" class="col-md-4 texto">CARGO</label>
        <div class="col-md-7">
          @if(!is_null($empl_nomina->cargo)){{$empl_nomina->cargo}}@endif
        </div>
      </div>
      <div class="separator1"></div>
      <div class="row head-title">
        <div class="col-md-12">
          <label class="color_texto" for="title">OTROS ANTICIPOS</label>
        </div>
      </div>
      <div class="separator1"></div>
      <!--Fecha_Creacion-->
      <div class="form-group col-xs-6">
        <label for="fecha_creacion" class="col-md-4 texto">FECHA CREACION</label>
        <div class="col-md-7">
          <input id="fecha_creacion" type="date" class="form-control" name="fecha_creacion" value="@if(!is_null($otros_anticipos)){{$otros_anticipos->fecha_creacion}}@endif" required autofocus readonly>
        </div>
      </div>
      <!--Cobrar en Tipo Rol-->
      <div class="form-group col-xs-6">
        <label for="tipo_rol" class="col-md-4 texto">DESCONTAR ROL</label>
        <div class="col-md-7">
          <input id="tipo_rol" type="text" class="form-control" name="tipo_rol" value="MENSUAL" readonly>
        </div>
      </div>
      <!--Sueldo Empleado-->
      <div class="form-group col-xs-6">
        <label for="sueldo_empleado" class="col-md-4 texto">SUELDO</label>
        <div class="col-md-7">
          <input id="sueldo_empleado" type="text" class="form-control" name="sueldo_empleado" value="@if(!is_null($empl_nomina->sueldo_neto)){{$empl_nomina->sueldo_neto}}@endif" onkeypress="return isNumberKey(event)" onblur="checkformat(this)" readonly>
        </div>
      </div>
      <!--Monto Prestamo-->
      <div class="form-group col-xs-6">
        <label for="monto_anticipo" class="col-md-4 texto">MONTO ANTICIPO</label>
        <div class="col-md-7">
           
          <input id="monto_anticipo" type="text" class="form-control" name="monto_anticipo" value="@if(!is_null($otros_anticipos)){{$otros_anticipos->monto_anticipo}}@endif" required autofocus readonly>
        </div>
      </div>
      <!--Concepto del Anticipo-->
      <div class="form-group col-xs-12">
        <label for="concepto" class="col-md-2 texto">CONCEPTO</label>
        <div class="col-md-9">
          <input name="concepto" id="concepto" type="text" class="form-control"  value="@if(!is_null($otros_anticipos)){{$otros_anticipos->concepto}}@endif" style="width: 100%;" rows="3"  readonly>
        </div>
      </div>
      <div class="separator1"></div>
      <div class="row head-title">
        <div class="col-md-12">
          <label class="color_texto" for="title">AÑO/MES DE COBRO DEL ANTICIPO</label>
        </div>
      </div>
      <div class="separator1"></div>
      <!--AÑO 1 MES COBRO-->
      <div class="form-group col-xs-6">
        <label for="anio_pmes_cobro" class="col-md-4 texto">AÑO COBRO ANTICIPO</label>
        <div class="col-md-7">
        <input name="anio_pmes_cobro" id="anio_pmes_cobro" type="text" class="form-control"  value="@if(!is_null($otros_anticipos)){{$otros_anticipos->anio_cobro_anticipo}}@endif" readonly>
  
        </div>
      </div>
      <!--1 Mes Cobro-->
      <div class="form-group col-xs-6">
        <label for="pmes_cobro" class="col-md-4 texto">MES COBRO ANTICIPO</label>
        <div class="col-md-7">
        <input name="pmes_cobro" type="text"  class="form-control" id="pmes_cobro" value="@if($otros_anticipos->mes_cobro_anticipo == '1' ) Enero 
        @elseif($otros_anticipos->mes_cobro_anticipo == '2') Febrero
        @elseif($otros_anticipos->mes_cobro_anticipo == '3') Marzo
        @elseif($otros_anticipos->mes_cobro_anticipo == '4' ) Abril
        @elseif($otros_anticipos->mes_cobro_anticipo == '5') Mayo 
        @elseif($otros_anticipos->mes_cobro_anticipo == '6') Junio
        @elseif($otros_anticipos->mes_cobro_anticipo == '7') Julio 
        @elseif($otros_anticipos->mes_cobro_anticipo == '8') Agosto 
        @elseif($otros_anticipos->mes_cobro_anticipo == '9') Septiembre 
        @elseif($otros_anticipos->mes_cobro_anticipo == '10') Octubre
        @elseif($otros_anticipos->mes_cobro_anticipo == '11') Noviembre
        @elseif($otros_anticipos->mes_cobro_anticipo == '12' ) Diciembre @endif" readonly >
  
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
        <label for="tipo_pago" class="col-md-4 texto">TIPO DE PAGO</label>
        <div class="col-md-7">
        <input name="tipo_pago" id="tipo_pago" type="text" class="form-control"  value="@if(!is_null($tipo_pago)){{$tipo_pago->tipo}}@endif" readonly>
        </div>
      </div>
      <!--Fecha_Cheque-->
      <!--div id="fech_cheq" class="form-group col-xs-6">
        <label for="fecha_cheque" class="col-md-4 texto">FECHA CHEQUE:</label>
        <div class="col-md-7">
          <input id="fecha_cheque" type="text" class="form-control" name="fecha_cheque" value="@if(!is_null($otros_anticipos)){{$otros_anticipos->fecha_cheque}}@endif"required autofocus>
        </div>
      </div-->
      <!--Numero de Cheque-->
      <!--div id="num_che" class="form-group col-xs-6">
        <label for="numero_cheque" class="col-md-4 texto">N# CHEQUE2:</label>
        <div class="col-md-7">
          <input id="numero_cheque" type="date" class="form-control" name="numero_cheque" value="@if(!is_null($otros_anticipos)){{$otros_anticipos->num_cheque }}@endif" onkeypress="return isNumberKey(event)" autocomplete="off" readonly>
        </div>
      </div-->
      <!-- Numero de Cuenta Beneficiario Cobra-->
      <div id="num_cuenta" class="form-group col-xs-6">
        <label for="numero_cuenta" class="col-md-4 texto">N# CTA BENEFICIARIO(COBRA)</label>
        <div class="col-md-7">
          <input id="numero_cuenta" type="text" class="form-control" name="numero_cuenta" value="@if(!is_null($empl_nomina->numero_cuenta)){{$empl_nomina->numero_cuenta}}@endif" autocomplete="off" readonly>
        </div>
         </div>
      <!--Banco Beneficiario Cobra-->
      <div id="id_banco" class="form-group col-xs-6">
        <label for="banco" class="col-md-4 texto">BANCO BENEFICIARIO(COBRA)</label>
        <div class="col-md-7">
        <input name="banco" id="banco" type="text" class="form-control"  value="@if(!is_null($otros_anticipos->banco_beneficiario)){{$otros_anticipos->banco->nombre}}@endif" readonly>
        </div>
      </div>
      <!--Cuenta Saliente Paga-->
      <div id="id_cuenta_saliente" class="form-group col-xs-6">
        <label for="cuenta_saliente" class="col-md-4 texto">CUENTA SALIENTE(PAGA)</label>
        <div class="col-md-7">
        <input name="cuenta_saliente" id="cuenta_saliente" type="text" class="form-control"  value="@if(!is_null($cuenta_saliente)){{$cuenta_saliente->nombre}}@endif" readonly>
        </div>
      </div>
    </form>
  </div>
  <div class="separator1"></div>
  <div  class="modal-footer" style ="text-align: center">
    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
  </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


<script type="text/javascript">
  $(document).ready(function() {

    //document.getElementById("num_che").style.visibility = "hidden";
    //document.getElementById("fech_cheq").style.visibility = "hidden";
    
    
    
  });

  function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
      return false;

    return true;
  }

  function checkformat(entry) {

    var test = entry.value;

    if (!isNaN(test)) {
      entry.value = parseFloat(entry.value).toFixed(2);
    }

    if (isNaN(entry.value) == true) {
      entry.value = '0.00';
    }
    if (test < 0) {

      entry.value = '0.00';
    }

  }

  function obtener_seleccion() {

    var id_tipo = $("#tipo_pago").val();

    if (id_tipo == 1) {

      //document.getElementById("id_cuenta_saliente").style.visibility = "visible";
      //document.getElementById("id_banco").style.visibility = "visible";
      //document.getElementById("num_cuenta").style.visibility = "visible";
      //document.getElementById("num_che").style.visibility = "hidden";
      //document.getElementById("fech_cheq").style.visibility = "hidden";

      document.getElementById("id_cuenta_saliente").style.display='block';
      document.getElementById("id_banco").style.display='block';
      document.getElementById("num_cuenta").style.display='block';
      document.getElementById("num_che").style.display='none';
      document.getElementById("fech_cheq").style.display='none';

      var id_ban = $("#id_ban").val();
      var num_cuent = $("#num_cuent").val();

      $('#banco').val(id_ban);
      $('#numero_cuenta').val(num_cuent);
      $('#numero_cheque').val("");
      $('#fecha_cheque').val("");


    } else if (id_tipo == 2) {

      //document.getElementById("id_banco").style.visibility = "hidden";
      //document.getElementById("num_cuenta").style.visibility = "hidden";
      //document.getElementById("num_che").style.visibility = "hidden";
      //document.getElementById("fech_cheq").style.visibility = "hidden";

      document.getElementById("id_banco").style.display='none';
      document.getElementById("num_cuenta").style.display='none';
      document.getElementById("num_che").style.display='none';
      document.getElementById("fech_cheq").style.display='none';
      
      $('#numero_cheque').val("");
      $('#numero_cuenta').val("");
      $('#banco').val("");
      $('#cuenta_saliente').val("");

    } else if (id_tipo == 3) {

      //document.getElementById("id_banco").style.visibility = "hidden";
      //document.getElementById("num_cuenta").style.visibility = "hidden";
      //document.getElementById("num_che").style.visibility = "visible";
      //document.getElementById("fech_cheq").style.visibility = "visible";

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

  function store_otros_anticipo() {

    var formulario = document.forms["guardar_otros_anticipo"];
    var mont_anticip = formulario.monto_anticipo.value;
    var fech_creacion = formulario.fecha_creacion.value;
    var pmes_cobro = formulario.pmes_cobro.value;
    var anio_pmes_cobro = formulario.anio_pmes_cobro.value;
    var tipo_pag = formulario.tipo_pago.value;
    var concept = formulario.concepto.value;
    var cuent_saliente = formulario.cuenta_saliente.value;

    var msj = "";

    if (mont_anticip <= 0) {
      msj = msj + "Por favor,Ingrese el Monto del Anticipo<br/>";
    }

    if (fech_creacion == "") {
      msj = msj + "Por favor,Seleccione la Fecha de Creacion Anticipo<br/>";
    }

    if (pmes_cobro == "") {
      msj = msj + "Por favor,Seleccione el Mes de Cobro del Anticipo<br/>";
    }

    if (anio_pmes_cobro == "") {
      msj = msj + "Por favor,Seleccione el Año de Cobro del Anticipo<br/>";
    }

    if (tipo_pag == "") {
      msj = msj + "Por favor,Seleccione el Tipo de Pago<br/>";
    }


    if (concept == "") {
      msj = msj + "Por favor, Ingrese el Concepto<br/>";
    }

    if (cuent_saliente == "") {
      msj = msj + "Por favor, Seleccione la Cuenta Saliente<br/>";
    }

    if (msj != "") {
      swal({
        title: "Error!",
        type: "error",
        html: msj
      });
      return false;
    }

    $.ajax({
      type: 'post',
      url: "{{route('otros_anticipo_empleado.store')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: $('#guardar_otros_anticipo').serialize(),
      success: function(data) {
        //console.log(data);
        url = "{{ url('contable/nomina/otros_anticipos/empleados/pdf_otros_anticipo/')}}/" + data;
        window.open(url, '_blank');
        location.href = "{{route('nomina.index')}}";
      },
      error: function(data) {
        console.log(data);
      }
    })

  }
</script> 

