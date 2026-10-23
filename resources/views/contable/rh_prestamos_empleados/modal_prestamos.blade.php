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
  <div class="box-body dobra ">
    <form id="guardar_prestamo" method="post">
      
      <input name="nombres" id="nombres" value="{{$nombre_completo}}" type="hidden" />
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
          @if(!is_null($cargos)){{$cargos}}@endif
        </div>
      </div>
      <div class="form-group col-xs-6">
        <label for="sueldo_empleado" class="col-md-4 texto">SUELDO</label>
        <div class="col-md-7">
          @if(!is_null($sueldo_neto)){{$sueldo_neto}}@endif
        </div>
      </div>
      <div class="separator1"></div>
      <div class="row head-title">
        <div class="col-md-12">
          <label class="color_texto" for="title">DATOS PRESTAMO</label>
        </div>
      </div>
      <div class="separator1"></div>
      <!--Monto Prestamo-->
      <div class="form-group col-xs-6">
        <label for="monto_prestamo" class="col-md-4 texto">MONTO PRESTAMO</label>
        <div class="col-md-7">
          <input id="monto_prestamo" type="text" class="form-control" name="monto_prestamo" value="0.00" onkeypress="return isNumberKey(event)" onblur="checkformat(this)" autocomplete="off" required autofocus>
        </div>
      </div>
      <!--Fecha_Creacion-->
      <div class="form-group col-xs-6">
        <label for="fecha_creacion" class="col-md-4 texto">FECHA CREACION</label>
        <div class="col-md-7">
          <input id="fecha_creacion" type="date" class="form-control" name="fecha_creacion"  onblur="validarCierre();" min="{{ isset($min_date) ? date('Y-m-d', strtotime($min_date.'+1 day')) : '' }}"  value="" required autofocus>
        </div>
      </div>
      <!--Cobrar en Tipo Rol-->
      <div class="form-group col-xs-6">
        <label for="tipo_rol" class="col-md-4 texto">DESCONTAR ROL</label>
        <div class="col-md-7">
          <input id="tipo_rol" type="text" class="form-control" name="tipo_rol" value="MENSUAL" readonly>
        </div>
      </div>
      <!--Cuotas-->
      <div class="form-group col-xs-6">
        <label for="cuotas" class="col-md-4 texto">CUOTAS MENSUALES</label>
        <div class="col-md-7">
          <input id="cuotas" type="text" class="form-control" name="cuotas" value="{{ old('cuotas') }}" onkeypress="return isNumberKey(event)" onchange="obtener_valor_cuota()" autocomplete="off" required autofocus>
        </div>
      </div>
      <!--Valor Cuota-->
      <div class="form-group col-xs-6">
        <label for="valor_cuotas" class="col-md-4 texto">VALOR CUOTA MENSUAL</label>
        <div class="col-md-7">
          <input id="valor_cuotas" type="text" class="form-control" name="valor_cuotas" onkeypress="return isNumberKey(event)" onblur="checkformat(this)" readonly>
        </div>
      </div>
      <!--Concepto del Anticipo-->
      <div class="form-group col-xs-6">
        <label for="concepto" class="col-md-4 texto">CONCEPTO</label>
        <div class="col-md-7">
          <textarea name="concepto" id="concepto" style="width: 100%; border: 2px solid #004AC1;" rows="3"></textarea>
        </div>
      </div>
      <div class="separator1"></div>
      <div class="row head-title">
        <div class="col-md-12">
          <label class="color_texto" for="title">FECHA DE INICIO DE LOS PAGOS</label>
        </div>
      </div>
      <div class="separator1"></div>
      <!--1 Mes Cobro-->
      <div class="form-group col-xs-6">
        <label for="pmes_cobro" class="col-md-4 texto">1ER MES COBRO</label>
        <div class="col-md-7">
          <select id="pmes_cobro" name="pmes_cobro" class="form-control" required>
            <option value="">Seleccione...</option>
            <?php
            $Meses = array(
              'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
              'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
            );

            for ($i = 1; $i <= 12; $i++) {

              echo '<option value="' . $i . '">' . $Meses[($i) - 1] . '</option>';
            }
            ?>
          </select>
        </div>
      </div>
      <!--AÑO 1 MES COBRO-->
      <div class="form-group col-xs-6">
        <label for="anio_pmes_cobro" class="col-md-4 texto">AÑO 1ER MES COBRO</label>
        <div class="col-md-7">
          <select id="anio_pmes_cobro" name="anio_pmes_cobro" class="form-control" onchange="setear_aniof_cobro()" required>
            <option value="">Seleccione...</option>
            <?php
            for ($i = 2020; $i <= 2040; $i++) {
              echo "<option value='" . $i . "'>" . $i . "</option>";
            }
            ?>
          </select>
        </div>
      </div>
      <!--Mes Fin Cobro-->
      <div class="form-group col-xs-6">
        <label for="mes_fcobro" class="col-md-4 texto">MES FIN COBRO</label>
        <div class="col-md-7">
          <select id="mes_fcobro" name="mes_fcobro" class="form-control">
            <option value="">Seleccione...</option>
            <?php
            $Meses = array(
              'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
              'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
            );

            for ($i = 1; $i <= 12; $i++) {

              echo '<option value="' . $i . '">' . $Meses[($i) - 1] . '</option>';
            }
            ?>
          </select>
        </div>
      </div>
      <!--AÑO FIN COBRO-->
      <div class="form-group col-xs-6">
        <label for="anio_fcobro" class="col-md-4 texto">AÑO FIN COBRO</label>
        <div class="col-md-7">
          <select id="anio_fcobro" name="anio_fcobro" class="form-control" required>
            <option value="">Seleccione...</option>
            <?php
            for ($i = 2020; $i <= 2040; $i++) {
              echo "<option value='" . $i . "'>" . $i . "</option>";
            }
            ?>
          </select>
        </div>
      </div>
      <div class="separator1"></div>
      <div class="row head-title">
        <div class="col-md-12">
          <label class="color_texto" for="title">COMO FUE PAGADO EL PRESTAMO</label>
        </div>
      </div>
      <!--Tipo de Pago-->
      <div class="form-group col-xs-6">
        <label for="tipo_pago" class="col-md-4 texto">TIPO DE PAGO</label>
        <div class="col-md-7">
          <select class="form-control" id="tipo_pago" name="tipo_pago" onchange="obtener_seleccion()">
            @foreach($tipo_pago_rol as $value)
            <option value="{{$value->id}}">{{$value->tipo}}</option>
            @endforeach
          </select>
        </div>
      </div>
      <!-- Numero de Cuenta Beneficiario Cobra-->
      <div id="num_cuenta" class="form-group col-xs-6">
        <label for="numero_cuenta" class="col-md-4 texto">N# CTA BENEFICIARIO(COBRA)</label>
        <div class="col-md-7">
          <input id="numero_cuenta" type="text" class="form-control" name="numero_cuenta" value="@if(!is_null($empl_nomina->numero_cuenta)){{$empl_nomina->numero_cuenta}}@endif" autocomplete="off">
        </div>
      </div>
      <!--Banco Beneficiario Cobra-->
      <div id="id_banco" class="form-group col-xs-6">
        <label for="banco" class="col-md-4 texto">BANCO BENEFICIARIO(COBRA)</label>
        <div class="col-md-7">
          <select class="form-control" id="banco" name="banco">
            <option value="">Seleccione...</option>
            @foreach($lista_banco as $value)
            <option value="{{$value->id}}" {{ $empl_nomina->banco == $value->id ? 'selected' : ''}}>{{$value->nombre}}</option>
            @endforeach
          </select>
        </div>
      </div>
      <!--Fecha_Cheque-->
      <div id="fech_cheq" class="form-group col-xs-6">
        <label for="fecha_cheque" class="col-md-4 texto">FECHA CHEQUE:</label>
        <div class="col-md-7">
          <input id="fecha_cheque" type="date" class="form-control" name="fecha_cheque" value="{{ old('fecha_cheque') }}" required autofocus>
        </div>
      </div>
      <!--Numero de Cheque-->
      <div id="num_che" class="form-group col-xs-6">
        <label for="numero_cheque" class="col-md-4 texto">N# CHEQUE:</label>
        <div class="col-md-7">
          <input id="numero_cheque" type="text" class="form-control" name="numero_cheque" value="{{ old('numero_cheque') }}" onkeypress="return isNumberKey(event)" autocomplete="off">
        </div>
      </div>
      <!--Cuenta Saliente Paga-->
      <div id="id_cuenta_saliente" class="form-group col-xs-6">
        <label for="cuenta_saliente" class="col-md-4 texto">CUENTA SALIENTE(PAGA)</label>
        <div class="col-md-7">
          <select class="form-control" id="cuenta_saliente" name="cuenta_saliente">
            <option value="">Seleccione...</option>
            @foreach($bancos as $value)
            <option value="{{$value->cuenta_mayor}}">{{$value->nombre}}</option>
            @endforeach
          </select>
        </div>
      </div>
    </form>
  </div>
  <div class="separator1"></div>
  <div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
    <button id="guarda_egr" class="btn btn-primary" onclick="store_prestamo();">Guardar</button>
  </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


<script type="text/javascript">
  $(document).ready(function() {

    //document.getElementById("num_che").style.visibility = "hidden";
    //document.getElementById("fech_cheq").style.visibility = "hidden";

    document.getElementById("num_che").style.display='none';
    document.getElementById("fech_cheq").style.display='none';
    $('#fecha_cheque').val("");

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

  function obtener_valor_cuota() {

    var mont_prest = $("#monto_prestamo").val();
    var num_cuota = $("#cuotas").val();

    var val_cuota = (mont_prest / num_cuota);

    $('#valor_cuotas').val(val_cuota.toFixed(2));


    /*if(num_cuota > 12){

      swal("Error","El Numero de Cuotas No debe ser Mayor a 12","error");

      $('#cuotas').val("");

    }else if((num_cuota >=1) && (num_cuota<=12)){
      
      $('#valor_cuotas').val(val_cuota.toFixed(2));

    }*/

  }

  /*onchange="setear_mesf_cobro()"*/
  /*function setear_mesf_cobro(){

    var val = 1;
    var val_13 = 13;
    var mont_prest = $("#monto_prestamo").val();
    var id_mesini_cob = parseInt($("#pmes_cobro").val());
    var cuot = $("#cuotas").val();


    var msj = "";

    if(mont_prest <= 0){
      msj = msj + "Por favor,Ingrese el Monto del Prestamo<br/>";
      $('#pmes_cobro').val("");
    }

    if(cuot == ""){
      msj = msj + "Por favor,Ingrese las Cuotas Mensuales<br/>";
    }

    if(msj != ""){
        swal({
              title: "Error!",
              type: "error",
              html: msj
            });
        return false;
    }

    var num_cuota = parseInt($("#cuotas").val());
   
    sum_mes = (num_cuota)+(id_mesini_cob);

    if(sum_mes <= val_13){
      mes_fin = (sum_mes)-(val);
      $('#mes_fcobro').val(mes_fin);
    }else if(sum_mes>val_13){
      mes_fin = (sum_mes)-(val_13);
      $('#mes_fcobro').val(mes_fin);
    }
    
  }*/

  /*function setear_aniof_cobro(){
    
    var val = 1;
    var val_13 = 13;
    var anio_fin = 0;

    var cuot = $("#cuotas").val();
    var mont_prest = $("#monto_prestamo").val();
    var mes_ini = $("#pmes_cobro").val();

    var msj = "";

    if(mont_prest <= 0){
      msj = msj + "Por favor,Ingrese el Monto del Prestamo<br/>";
      $('#pmes_cobro').val("");
    }

    if(cuot == ""){
      msj = msj + "Por favor,Ingrese las Cuotas Mensuales<br/>";
    }

    if(mes_ini == ""){
      msj = msj + "Por favor,Seleccione el Primer de Cobro<br/>";
    }

    if(msj != ""){
        swal({
              title: "Error!",
              type: "error",
              html: msj
            });
        return false;
    }

    var num_cuota = parseInt($("#cuotas").val());
    var id_mesini_cob = parseInt($("#pmes_cobro").val());
    var inio_ini = parseInt($("#anio_pmes_cobro").val());

    sum_mes = (num_cuota)+(id_mesini_cob);
    if(sum_mes <= val_13){

      $('#anio_fcobro').val(inio_ini);

    }else if(sum_mes>val_13){

      anio_fin = (inio_ini)+(val);
      if(anio_fin <= inio_ini ){
        $('#anio_fcobro').val(anio_fin);
      }
    }
  
  }*/


  function setear_aniof_cobro() {

    var val = 1;
    var val_13 = 13;
    var anio_fin = 0;
    var mont_prest = $("#monto_prestamo").val();
    var id_mesini_cob = parseInt($("#pmes_cobro").val());
    var cuot = $("#cuotas").val();
    var mes_ini = $("#pmes_cobro").val();

    var msj = "";

    if (mont_prest <= 0) {
      msj = msj + "Por favor,Ingrese el Monto del Prestamo<br/>";
      $('#pmes_cobro').val("");
    }

    if (cuot == "") {
      msj = msj + "Por favor,Ingrese las Cuotas Mensuales<br/>";
    }

    if (mes_ini == "") {
      msj = msj + "Por favor,Seleccione el Primer Mes de Cobro<br/>";
    }

    if (msj != "") {

      $("#anio_pmes_cobro").val("");

      Swal.fire({
        title: "Error!",
        type: "error",
        html: msj
      });
      return false;


    }

    var num_cuota = parseInt($("#cuotas").val());
    var inio_ini = parseInt($("#anio_pmes_cobro").val());
    //console.log('paso1');

    sum_mes = (num_cuota) + (id_mesini_cob);

    if (sum_mes <= val_13) {
      mes_fin = (sum_mes) - (val);
      $('#mes_fcobro').val(mes_fin);
      $('#anio_fcobro').val(inio_ini);
    } else if (sum_mes > val_13) {
      mes_fin = ((sum_mes) % (12)) -1;
      $('#mes_fcobro').val(mes_fin);

      val = Math.trunc(sum_mes/12); 

      anio_fin = (inio_ini) + (val);
      $('#anio_fcobro').val(anio_fin);


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

  function store_prestamo() {
    
    var formulario = document.forms["guardar_prestamo"];
    var mont_prestamo = formulario.monto_prestamo.value;
    var fech_creacion = formulario.fecha_creacion.value;
    var cuotas = formulario.cuotas.value;
    var val_cuotas = formulario.valor_cuotas.value;
    var pmes_cobro = formulario.pmes_cobro.value;
    var anio_pmes_cobro = formulario.anio_pmes_cobro.value;
    var mes_fcobro = formulario.mes_fcobro.value;
    var anio_fcobro = formulario.anio_fcobro.value;
    var tipo_pag = formulario.tipo_pago.value;
    var concept = formulario.concepto.value;
    var cuent_saliente = formulario.cuenta_saliente.value;

    var msj = "";

    if (mont_prestamo <= 0) {
      msj = msj + "Por favor,Ingrese el Monto del Prestamo<br/>";
    }

    if (fech_creacion == "") {
      msj = msj + "Por favor,Seleccione la Fecha de Creacion Prestamo<br/>";
    }

    if (cuotas == "") {
      msj = msj + "Por favor,Ingrese el Numero de Cuotas Mensuales<br/>";
    }

    if (val_cuotas == "") {
      msj = msj + "Por favor,Ingrese el Valor de la Cuota Mensual<br/>";
    }

    if (pmes_cobro == "") {
      msj = msj + "Por favor,Seleccione el Primer mes de cobro<br/>";
    }

    if (anio_pmes_cobro == "") {
      msj = msj + "Por favor,Seleccione el Año del Primer mes de cobro<br/>";
    }

    if (mes_fcobro == "") {
      msj = msj + "Por favor,Seleccione el Mes fin de cobro<br/>";
    }

    if (anio_fcobro == "") {
      msj = msj + "Por favor,Seleccione el Año fin de cobro<br/>";
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
      Swal.fire({
        title: "Error!",
        type: "error",
        html: msj
      });
      return false;
    }
    document.getElementById("guarda_egr").disabled = true;
    $.ajax({
      type: 'post',
      url: "{{route('prestamos_empleado.store')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: $('#guardar_prestamo').serialize(),
      success: function(data) {
        
        if(data.respuesta == 'error'){
          Swal.fire({
            title: "Error!",
            type: "error",
            html: data.msj,
          });   
        }else{
          url = "{{ url('contable/nomina/prestamos/empleados/pdf_prestamos/')}}/" + data.id_prestamo;
          window.open(url, '_blank');
          location.href = "{{route('nomina.index')}}";

        }
        //var variable = $("#id_empl").val();
        //window.open("{{asset('/contable/nomina/prestamos/empleados/pdf_prestamos')}}/"+variable, '_blank ');
        
        

      },
      error: function(data) {
        console.log(data);
      }
    })

  }
  function validarCierre() {
        <?php
        if (isset($min_date) && $min_date != null) {
        ?>
            let fechaAc = new Date();
            var fecha1 = '<?= $min_date; ?>';
            var fecha2 = document.getElementById("fecha_creacion").value;
            const fechaInicial = new Date(fecha1);
            const fechaFinal = new Date(fecha2);
            if (fechaFinal <= fechaInicial) {
                swal("Recuerde!", 'No es posible modificar este registro debido a que pertenece a un período con cierre contable anterior.', "error");
                const fechaFormateada = fechaAc.toISOString().split('T')[0];
                document.getElementById("fecha_creacion").value = fechaFormateada;
            }
        <?php
        }
        ?>
  }
</script>