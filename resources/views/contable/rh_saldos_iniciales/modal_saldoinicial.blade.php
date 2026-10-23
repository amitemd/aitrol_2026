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
    <form id="guardar_saldo_inicial" method="post">
      <input  name="id_empl" id="id_empl" type="text" class="hidden" value="@if(!is_null($empl_nomina->id_user)){{$empl_nomina->id_user}}@endif">
      <input name="nombres" id="nombres" value="{{$nombre_completo}}" type="hidden" />
      <input name="id_empresa" id="id_empresa" type="text" class="hidden" value="@if(!is_null($empl_nomina->id_empresa)){{$empl_nomina->id_empresa}}@endif">
      <div class="form-group col-xs-6">
        <label for="nombres_actual" class="col-md-4 texto">NOMBRE:</label>
        <div class="col-md-7">
          @if(!is_null($nombre_completo)){{$nombre_completo}}@endif
        </div>
      </div>
      <div class="form-group col-xs-3">
        <label for="cedula_empl" class="col-md-4 texto">CEDULA:</label>
        <div class="col-md-7">
          @if(!is_null($empl_nomina->id_user)){{$empl_nomina->id_user}}@endif
        </div>
      </div>
      <div class="form-group col-xs-3">
        <label for="cargo_empl" class="col-md-4 texto">CARGO:</label>
        <div class="col-md-7">
          @if(!is_null($empl_nomina->cargo)){{$empl_nomina->cargo}}@endif
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
          <label class="color_texto" for="title">INFORMACIÓN COBRO DE SALDO INICIAL</label>
        </div>
      </div>
      <div class="separator1"></div>
      <!--VALOR SALDO INICIAL-->
      <div class="form-group col-xs-6">
        <label for="valor_saldo" class="col-md-4 texto">VALOR SALDO INICIAL:</label>
        <div class="col-md-7">
            <input id="valor_saldo" type="text" class="form-control"  name="valor_saldo" value="" onkeypress="return isNumberKey(event)" onblur="checkformat(this)">
        </div>
      </div>
      <!--FECHA CREACION-->
      <div class="form-group col-xs-6">
        <label for="fecha_creacion" class="col-md-4 texto">FECHA CREACION:</label>
        <div class="col-md-7">
            <input id="fecha_creacion" type="date" class="form-control" name="fecha_creacion" onblur="validarCierre();" min="{{ isset($min_date) ? date('Y-m-d', strtotime($min_date.'+1 day')) : '' }}" value="" required autofocus>
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
          <input id="cuotas" type="text" class="form-control" name="cuotas" value="{{ old('cuotas') }}" onkeypress="return isNumberKey(event)"  onchange="obtener_valor_cuota()" autocomplete="off" required autofocus>
        </div>
      </div>
      <!--Valor Cuota-->
      <div class="form-group col-xs-6">
        <label for="valor_cuotas" class="col-md-4 texto">VALOR CUOTA MENSUAL</label>
        <div class="col-md-7">
          <input id="valor_cuotas" type="text" class="form-control" name="valor_cuotas" onkeypress="return isNumberKey(event)" onblur="checkformat(this)" readonly>
        </div>
      </div>
      <!--Observacion Saldo Inicial-->
      <div class="form-group col-xs-6">
        <label for="observ_saldo" class="col-md-4 texto">OBSERVACIÓN</label>
        <div class="col-md-7">
          <textarea name="observ_saldo" id="observ_saldo" style="width: 100%; border: 2px solid #004AC1;" rows="3"></textarea>
        </div>
      </div>
      <div class="separator1"></div>
      <div class="row head-title">
        <div class="col-md-12">
          <label class="color_texto" for="title">FECHA DE INICIO DE LOS PAGOS DE SALDOS</label>
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
            for ($i = 2020; $i <= 2030; $i++) {
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
            for ($i = 2020; $i <= 2030; $i++) {
              echo "<option value='" . $i . "'>" . $i . "</option>";
            }
            ?>
          </select>
        </div>
      </div>
    </form>
  </div>
  <div class="separator1"></div>
  <div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
    <button id="guarda_saldo" class="btn btn-primary" onclick="store_saldo_inicial();">Guardar</button>
  </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


<script type="text/javascript">
 

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

  function obtener_valor_cuota(){

    var val_saldo = $("#valor_saldo").val();
    var num_cuota = $("#cuotas").val();

    var val_cuota = (val_saldo / num_cuota);

    $('#valor_cuotas').val(val_cuota.toFixed(2));


  }


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

      swal({
        title: "Error!",
        type: "error",
        html: msj
      });
      return false;
    
    }

    var num_cuota = parseInt($("#cuotas").val());
    var inio_ini = parseInt($("#anio_pmes_cobro").val());

    sum_mes = (num_cuota) + (id_mesini_cob);

    if(sum_mes <= val_13){
      
      mes_fin = (sum_mes) - (val);
      $('#mes_fcobro').val(mes_fin);
      $('#anio_fcobro').val(inio_ini);
    
    }else if(sum_mes > val_13){
      
      mes_fin = (sum_mes) - (val_13);
      $('#mes_fcobro').val(mes_fin);
      anio_fin = (inio_ini) + (val);
      $('#anio_fcobro').val(anio_fin);
    
    }


}

  function store_saldo_inicial() {

    var formulario = document.forms["guardar_saldo_inicial"];
    var fech_creacion = formulario.fecha_creacion.value;
    var valor_saldo = formulario.valor_saldo.value;

    var cuotas = formulario.cuotas.value;
    var val_cuotas = formulario.valor_cuotas.value;
    var pmes_cobro = formulario.pmes_cobro.value;
    var anio_pmes_cobro = formulario.anio_pmes_cobro.value;
    var mes_fcobro = formulario.mes_fcobro.value;
    var anio_fcobro = formulario.anio_fcobro.value;
    
    //var anio_cobro_saldo = formulario.anio_cobro_saldo.value;
    //var mes_cobro_saldo = parseInt(formulario.mes_cobro_saldo.value);

    /*switch(mes_cobro_saldo){

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

    }*/

    var msj = "";

    if (fech_creacion == "") {
      msj = msj + "Por favor,Seleccione la Fecha de Creación<br/>";
    }

    if (cuotas == "") {
      msj = msj + "Por favor,Ingrese el Numero de Cuotas Mensuales<br/>";
    }

    if (val_cuotas == "") {
      msj = msj + "Por favor,Ingrese el Valor de la Cuota Mensual<br/>";
    }
    
    if (valor_saldo <= 0) {
      msj = msj + "Por favor,Ingrese el Valor del Saldo Inicial<br/>";
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

    /*if (anio_cobro_saldo == "") {
      msj = msj + "Por favor,Ingrese el Año de Cobro del Saldo<br/>";
    }

    if (mes_cobro_saldo == "") {
      msj = msj + "Por favor,Ingrese el Mes de Cobro del Saldo<br/>";
    }*/

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
      url: "{{route('saldo_inicial_empleado.store')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: $('#guardar_saldo_inicial').serialize(),
      success: function(data){

        if(data.status =='error'){
          swal("Error!","trans('contableM.yaexiunsalinicicreadoenelanio')"+" : "+anio_cobro_saldo+" "+"trans('agenda.mes')"+" : "+desc_mes,"error" );
        }else{
          swal(data.status+"!",data.msj,data.status).then(() => location.href = "{{ route('nomina.index') }}");
        }
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