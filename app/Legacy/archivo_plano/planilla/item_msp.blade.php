<style type="text/css">

    ul.ui-autocomplete {
        z-index: 1100;
    }

    .ui-corner-all
    {
        -moz-border-radius: 4px 4px 4px 4px;
    }

    .ui-widget
    {
        font-family: Verdana,Arial,sans-serif;
        font-size: 12px;
    }
    .ui-menu
    {
        display: block;
        float: left;
        list-style: none outside none;
        margin: 0;
        padding: 2px;
        opacity : 1;
    }
    .ui-autocomplete
    {
        opacity : 1;
        overflow-x: hidden;
        max-height: 200px;
        width:1px;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        float: left;
        display: none;
        min-width: 160px;
        _width: 160px;
        padding: 4px 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #fff;
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
        *border-right-width: 2px;
        *border-bottom-width: 2px;
    }
    .ui-menu .ui-menu-item
    {
        clear: left;
        float: left;
        margin: 0;
        padding: 0;
        width: 100%;
    }
    .ui-menu .ui-menu-item a
    {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        cursor: pointer;
        background-color: #ffffff;
    }
    .ui-menu .ui-menu-item a:hover
    {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        color: White;
        cursor: pointer;
        background-color: #006699;
    }
    .ui-widget-content a
    {
        color: #222222;
    }
  
    .h3{
      font-family: 'BrixSansBlack';
      font-size: 8pt;
      display: block;
      background: #3d7ba8;
      color: #FFF;
      text-align: center;
      padding: 3px;
      margin-bottom: 5px;
    }

    .info_nomina{
      width: 69%;
    }

    .round{
      border-radius: 10px;
      border: 1px solid #3d7ba8;
      overflow: hidden;
      padding-bottom: 15px;
    }

    .datos_nomina
    {
      font-size: 0.8em;
    }

    .mValue{
      width:79%;
      display: inline-block;
      vertical-align: top;
      padding-left:7px;
      font-size: 0.9em;
    }

    #rol_pago{
      width: 100%;
      margin-bottom: 10px;
    }


    .info_nomina .col-xs-8 {
        padding-left:10px;
        font-size: 0.9em;
    }
    .info_nomina .round{
        padding-top:10px;
    }

    .titulo-wrapper{
        width: 100%;
        text-align: center;
    }

    .modal-body .form-group {
        margin-bottom: 0px;
    }

    .h3.modal_h3{
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
    .h3.modal_h3_2{
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

    .separator{
      width:100%;
      height:20px;
      clear: both;
    }

    .separator1{
      width:100%;
      height:5px;
      clear: both;
    }

    
    /* Nuevo CSS*/

    .mLabel{
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

    .color_texto{
      color:#FFFFFF;
    }

    .head-title{
      background-color: #4682B4;
      margin-left: 0px;
      margin-right: 0px;
      height: 30px;
      line-height: 30px;
      color: #cccccc;
      text-align: center;
    }

    .t9{
      font-size: 0.9rem;
    }

    .well-dark{
      background-color: #cccccc;
    }

</style>

<div class="modal-content" style="width: 100%;">
    <div class="modal-header" style="padding-top: 5px; padding-bottom: 1px;">
      <div class="row" style="border-bottom: 1px solid black;">
        <div class="col-md-2">
          <a class="btn btn-light" onclick="store_item_msp()">
            <h1 style="font-size: 12px; margin:0;">
                <img style="width: 30px;height:23px" src="{{asset('/')}}hc4/img/guardar.png">
                <label style="font-size: 14px">Guardar</label>
            </h1> 
          </a>
        </div>
        <div class="col-md-2">
          <a class="btn btn-light" data-dismiss="modal">
            <h1 style="font-size: 12px; margin:0;">
                <img style="width: 30px;height:23px" src="{{asset('/')}}hc4/img/exit.png">
                <label style="font-size: 14px">Cerrar</label>
            </h1> 
          </a>
        </div>
        <div class="col-md-5">
        </div>
        <div class="col-md-3">
          <span id="Label8" style="color:#003366;font-family:Arial;font-size:14pt;font-weight:bold;">Ingreso de Items</span>
        </div>
      </div>
    </div>
    <div class="box-body" style="padding-bottom: 180px;">
      <form id="guardar_item_msp" method="post">
          <input type="hidden" id="val_tmp_anest" name="val_tmp_anest">
          <input type="hidden" name="id_cabecera" id="id_cabecera"  value="@if(!is_null($idcabecera)){{$idcabecera}}@endif">
          <input type="hidden" name="porcent_10" id="porcent_10"  value="">
          <input type="hidden" name="porcent_clasificado" id="porcent_clasificado"  value="">
          <input type="hidden" name="nivel_convenio" id="nivel_convenio" value="@if(!is_null($archivo_plano_cab->id_nivel)){{$archivo_plano_cab->id_nivel}}@endif">
          <div class="form-group col-md-7 col-xs-7">
            <label for="nivel_convenio" class="col-md-3 control-label">Nivel de Convenio:</label>
            <div class="col-md-7">
		          {{$archivo_plano_cab->id_nivel}}
	          </div>
          </div>
          <div class="form-group col-md-6">
            <label for="fecha" class="col-md-2 control-label">Fecha:</label>
            <div class="col-md-7">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm"  id="fecha" name="fecha" value="{{ old('fecha') }}" autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = '';"></i>
                </div> 
              </div>
            </div>  
          </div> 
          <div class="form-group col-md-3 col-xs-4">
            <label for="tipo" class="col-md-3 control-label">Tipo:</label>
            <div class="col-md-7">
                <input id="tipo" type="text" class="form-control input-sm" name="tipo"   autocomplete="off"> 
            </div>
          </div>
          <div class="form-group col-md-3 col-xs-4">
              <label for="codigo" class="col-md-3 control-label">Código:</label>
              <div class="col-md-7">
                <input id="codigo" maxlength="40" type="text" class="form-control input-sm" name="codigo"   autocomplete="off">
              </div>
          </div>
          <div class="form-group col-md-6 col-xs-6">
              <label for="codigo" class="col-md-3 control-label"></label>
              <div class="col-md-7">
              </div>
          </div>
          <div class="form-group col-md-10 col-xs-9">
              <label for="descripcion" class="col-md-2 control-label">Descripción:</label>
              <div class="col-md-10">
                <input id="descripcion" type="text" class="form-control input-sm"  name="descripcion" value="{{old('descripcion')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase(); " autocomplete="off">
              </div>
          </div>
          <div class="form-group col-md-3 col-xs-6">
              <label for="cantidad" class="col-md-4 control-label">Cantidad:</label>
              <div class="col-md-7">
                  <input id="cantidad" type="text" class="form-control input-sm" name="cantidad"   autocomplete="off">
              </div>
          </div>
          <div class="form-group col-md-3 col-xs-6">
              <label for="precio" class="col-md-4 control-label">Precio:</label>
              <div class="col-md-7">
                  <input id="precio" type="text" class="form-control input-sm" name="precio"   autocomplete="off">
              </div>
          </div>
          <div class="form-group col-md-3 col-xs-6">
              <label for="iva" class="col-md-4 control-label">Iva:</label>
              <div class="col-md-7">
                  <input id="iva" type="text" class="form-control input-sm" name="iva" autocomplete="off">
              </div>
          </div> 
         <div class="form-group col-md-3 col-xs-6">
              <label for="clasificador" class="col-md-4 control-label">Clasificador:</label>
              <div class="col-md-7">
                  <input id="clasificador" type="text" class="form-control input-sm" name="clasificador"   autocomplete="off">
              </div>
          </div> 
          <div class="form-group col-md-3 col-xs-6">
              <label for="hono_Anest" class="col-md-4 control-label">Hon. Anestesiologo:</label>
              <div class="col-md-7">
                  <input id="hono_Anest" type="text" class="form-control input-sm" name="hono_Anest" autocomplete="off">
              </div>
          </div>
          <div class="form-group col-md-3 col-xs-6">
              <label for="tiempo_Anest" class="col-md-4 control-label">Tiempo Anestesia:</label>
              <div class="col-md-7">
                  <input id="tiempo_Anest" type="text" class="form-control input-sm" name="tiempo_Anest" value="0" autocomplete="off">
              </div>
          </div> 
          <div class="form-group col-md-2 col-xs-6">
              <label for="proceso_separ" class="col-md-4 control-label">Proced. Separado:</label>
              <div class="col-md-7">
                  <input id="proceso_separ" type="text" class="form-control input-sm" name="proceso_separ" autocomplete="off">
              </div>
          </div>
          <div class="form-group col-md-3 col-xs-6">
              <label for="orden_proced" class="col-md-4 control-label">Orden Procedimiento:</label>
              <div class="col-md-7">
                <select id="orden_proced" name="orden_proced" class="form-control input-sm" >
                  <option value="100">100 %</option>
                  <option value="50">50 %</option>
                </select>
              </div>
          </div>
          <div class="form-group col-md-1 col-xs-6">
            <a class="btn btn-light" onclick="crea_detalle_items_msp()">
              <h1 style="font-size: 12px; margin:0;">
                  <img style="width: 30px;height:23px" src="{{asset('/')}}hc4/img/agregar_item.png">
              </h1> 
            </a>
          </div>
          <div class="table-responsive col-md-12">
            <table cellspacing="0" cellpadding="3" rules="all" id="grdgitem" style="background-color:White;border-color:#CCCCCC;border-width:1px;border-style:None;font-family:Arial;font-size:10px;width:100%;border-collapse:collapse;">
              <thead>
                <tr style="color:White;background-color:#006699;font-weight:bold;">
                  <th>CLASIFICADOR</th>
                  <th>TIPO</th>
                  <th>CODIGO</th>
                  <th>DESCRIPCION</th>
                  <th>CANTIDAD</th>
                  <th>VALOR</th>
                  <th>IVA</th>
                  <th>TOTAL</th>
                  <th>ACCION</th>
                </tr>
              </thead>
              <tbody id="detalle_items_msp">
              </tbody>
            </table>
          </div>
          <input type="hidden" name="contador_item_msp" id="contador_item_msp"  value="0">    
      </form>
    </div>
  </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


<script type="text/javascript">

    $(function () {

      $('#fecha').datetimepicker({
          useCurrent: false,
          format: 'DD/MM/YYYY',
          @if($archivo_plano_cab->fecha_ing !=null)
              defaultDate: '{{$archivo_plano_cab->fecha_ing}}',
	        @endif
      });

    });


    $( document ).ready(function() {

      $("#descripcion").autocomplete({
        source: function(request,response){
          $.ajax({
            url:"{{route('item_msp.buscardescripcion')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            data: {
                term: request.term
                  },
            dataType: "json",
            type: 'post',
            success: function(data){
              response(data);
            }

          });

        },
        change:function(event, ui){
          $("#tipo").val(ui.item.tipo);
          $("#codigo").val(ui.item.codigo);
          $("#cantidad").val(ui.item.cantidad);
          $("#iva").val(ui.item.iva);
          $("#porcent_10").val(ui.item.porcent10);
          
          if((ui.item.clasificado != null)&&(ui.item.porcent_clasificado > 0)){
            $("#clasificador").val(ui.item.clasificado);
            $("#porcent_clasificado").val(ui.item.porcent_clasificado);
          }else{
            obtener_clasificador(ui.item.tipo);
          }
          obtener_data(ui.item.id_ap_proced,ui.item.tipo);
          
        },
        minLength: 4,
      });

      $("#codigo").autocomplete({
      
        source: function(request,response){
          var nivel_conve =$("#nivel_convenio").val();
          var term = request.term;
          $.ajax({
            url:"{{route('item_msp.buscarxcodigo')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            data: {'term': term,'conv': nivel_conve},
            dataType: "json",
            type: 'post',
            success: function(data){
              response(data);
            }

          });

        },

        change:function(event, ui){
          $("#tipo").val(ui.item.tipo);
          $("#descripcion").val(ui.item.descripcion);
          $("#cantidad").val(ui.item.cantidad);
          $("#iva").val(ui.item.iva);
          $("#porcent_10").val(ui.item.porcent10);

          if((ui.item.clasificado != null)&&(ui.item.porcent_clasificado>0)){
            $("#clasificador").val(ui.item.clasificado);
            $("#porcent_clasificado").val(ui.item.porcent_clasificado);
          }else{
            obtener_clasificador(ui.item.tipo);
          }
          
          obtener_data(ui.item.id_ap_proced,ui.item.tipo);

          
        },
        minLength: 2,
        appendTo: "#item_msp",  //Linea nueva, agrego el id del modal
      });
    
    });

    function obtener_clasificador(tip){

        $.ajax({
            type: "GET",
            url: "{{url('archivo_plano/planilla/obtener/clasificador')}}/"+tip,
            dataType: "json",
            success: function(data){
              $('#clasificador').val(data.clasificador);
              $("#porcent_clasificado").val(data.porcent_clasificado);

            },
            error:  function(){
                alert('error al cargar');
            }
        });

    }

    function obtener_data(id_ap_proced,tip){
        
      var nivel_conve =$("#nivel_convenio").val();
        
        $.ajax({
            type: "GET",
            url: "{{url('archivo_plano/planilla/obtener/precio/item')}}/"+id_ap_proced+ "/"+ nivel_conve+ "/"+ tip,
            dataType: "json",
            success: function(data){
              console.log(data);
              $('#precio').val(data.precio);
              $('#hono_Anest').val(data.hono_anast);
              $('#proceso_separ').val(data.separ);
              $("#val_tmp_anest").val(data.val_tiemp_anest);
            },
            error:  function(){
                alert('error al cargar');
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

    function crea_detalle_items_msp(contador){

      var formulario = document.forms["guardar_item_msp"];

      var fech = formulario.fecha.value;
      var tip = formulario.tipo.value;
      var cod = formulario.codigo.value;
      var desc = formulario.descripcion.value;
      var cant = formulario.cantidad.value;
      var pre = formulario.precio.value;
      var hon_anes = formulario.hono_Anest.value;
      var tiemp_anes = formulario.tiempo_Anest.value;

      var msj = "";

      if(fech == ""){
          msj = msj + "Por favor, Seleccione la Fecha<br/>";
      }
      if(tip == ""){
          msj = msj + "Por favor, Ingrese el Tipo<br/>";
      }
      if(cod == ""){
          msj = msj + "Por favor, Ingrese el Codigo<br/>";
      }
      if(desc == ""){
          msj = msj + "Por favor, Ingrese la descripcion<br/>";
      }
      if(cant <= 0){
          msj = msj + "Por favor, Ingrese la Cantidad<br/>";
      }
      if(pre == ""){
          msj = msj + "Por favor, Ingrese el precio<br/>";
      }
      if(hon_anes == ""){
          msj = msj + "Por favor, Ingrese Honorario Anestesiologo<br/>";
      }
      if(tiemp_anes == ""){
          msj = msj + "Por favor, Ingrese el Tiempo de Anestesia<br/>";
      }

      if(msj != ""){
        swal({
          title: "Error!",
          type: "error",
          html: msj
        });
        return false;
      }
      
      //Obtenemos el Valor del Contador de la Tabla
      id= document.getElementById('contador_item_msp').value;

      //Inicializamos Variables a Cero
      var cal = 0;
      var div_dos = 0;
      var const1= 2;
      var const2= 50;
      var const3= 100;
      var const4= 1.1;
      var total = 0;
      var val = 0;
      var total_anest = 0;
      var tip_an = 'AN';
      var tip_t_anest = 'TA';
      var clasif_an = 'SA19-84';
      var clasif_ta = 'SA19-56';

      //Obtenemos Valores de los Campos de para pasarlo por el DIV
      var cabecera = $("#id_cabecera").val();
      var porce_10 = $("#porcent_10").val();
      var fecha = $("#fecha").val();
      var tipo = $("#tipo").val();
      var niv_conv = $("#nivel_convenio").val();
	    var codigo = $("#codigo").val();
      var descripcion = $("#descripcion").val()
      var cantidad = $("#cantidad").val();
      var precio = $("#precio").val();
      var iva = $("#iva").val();
      var clasif = $("#clasificador").val();
      var porcentaje_clas = $("#porcent_clasificado").val();
      var honorario_anest = $("#hono_Anest").val();
      var tiempo_anest = $("#tiempo_Anest").val();
      var ord_proced = $("#orden_proced").val();
      var val_t_ane = $("#val_tmp_anest").val();

      //Verificamos si el valor de Campo Honorario Anestesiologo es mayor a Cero y porcentaje de Honorario es
      //igual a 100%
      if((honorario_anest>0)&&(ord_proced == const2)){

        div_dos = precio/const1;
        total = ((cantidad*div_dos).toFixed(2));

        //Creamos la Tabla Temporal
        var midiv_item = document.createElement("tr")
        midiv_item.setAttribute("id","dato"+id);

        midiv_item.innerHTML = '<td><input required type="hidden" id="visibilidad_item_msp'+id+'" name="visibilidad_item_msp'+id+'" value="1"><input type="hidden" id="porce_10'+id+'" name="porce_10'+id+'" value="'+porce_10+'"><input type="hidden" id="idcabecera'+id+'" name="idcabecera'+id+'" value="'+cabecera+'"><input type="hidden" id="fecha'+id+'" name="fecha'+id+'" value="'+fecha+'"><input type="hidden" id="porcen_clasifi'+id+'" name="porcen_clasifi'+id+'" value="'+porcentaje_clas+'"><input type="hidden" name="niv_convenio'+id+'" id="niv_convenio'+id+'" value="'+niv_conv+'" readonly><input type="hidden" name="clasificador'+id+'" id="clasificador'+id+'" value="'+clasif+'" readonly><p>'+clasif+'</p></td><td><input type="hidden" name="tipo'+id+'" id="tipo'+id+'" value="'+tipo+'" readonly><p>'+tipo+'</p></td><td><input type="hidden" name="codigo'+id+'" id="codigo'+id+'"  value="'+codigo+'" readonly><p>'+codigo+'</p></td> <td> <input type="hidden" name="descripcion'+id+'" id="descripcion'+id+'" value="'+descripcion+'" readonly><p>'+descripcion+'</p></td><td> <input type="hidden" name="cantidad'+id+'" id="cantidad'+id+'" value="'+cantidad+'" readonly><p>'+cantidad+'</p></td><td> <input type="hidden" name="precio'+id+'" id="precio'+id+'" value="'+div_dos+'" readonly><p>'+div_dos+'</p></td><td> <input type="hidden" name="iva'+id+'" id="iva'+id+'" value="'+iva+'" readonly><p>'+iva+'</p></td><td> <input type="hidden" name="total'+id+'" id="total'+id+'" value="'+total+'" readonly><p>'+total+'</p></td><td style="width: 40px;"><button type="button" onclick="eliminar_item_msp('+id+')" class="btn btn-light"><img style="width: 15px;height:15px" src="{{asset('/')}}hc4/img/eliminar_item.png"></button></td>';
      
        document.getElementById('detalle_items_msp').appendChild(midiv_item);

        id = parseInt(id);
      
        id = id+1;

        $('#tipo').val("");
        $('#codigo').val("");
        $('#descripcion').val("");
        $('#cantidad').val("");
        $('#precio').val("");
        $('#iva').val("");
        $('#clasificador').val("");
        $('#porcent_10').val("");
        $('#hono_Anest').val("");
        $('#tiempo_Anest').val("");
        $('#proceso_separ').val("");
      
      }else if((honorario_anest>0)&&(ord_proced == const3)){

        if((cantidad>0)&&(precio>0)){
        total = ((cantidad*precio).toFixed(2));
        //Creamos la Tabla Temporal
        var midiv_item = document.createElement("tr")
        midiv_item.setAttribute("id","dato"+id);

        midiv_item.innerHTML = '<td><input required type="hidden" id="visibilidad_item_msp'+id+'" name="visibilidad_item_msp'+id+'" value="1"><input type="hidden" id="porce_10'+id+'" name="porce_10'+id+'" value="'+porce_10+'"><input type="hidden" id="idcabecera'+id+'" name="idcabecera'+id+'" value="'+cabecera+'"><input type="hidden" id="fecha'+id+'" name="fecha'+id+'" value="'+fecha+'"><input type="hidden" id="porcen_clasifi'+id+'" name="porcen_clasifi'+id+'" value="'+porcentaje_clas+'"><input type="hidden" name="niv_convenio'+id+'" id="niv_convenio'+id+'" value="'+niv_conv+'" readonly><input type="hidden" name="clasificador'+id+'" id="clasificador'+id+'" value="'+clasif+'" readonly><p>'+clasif+'</p></td><td><input type="hidden" name="tipo'+id+'" id="tipo'+id+'" value="'+tipo+'" readonly><p>'+tipo+'</p></td><td><input type="hidden" name="codigo'+id+'" id="codigo'+id+'"  value="'+codigo+'" readonly><p>'+codigo+'</p></td> <td> <input type="hidden" name="descripcion'+id+'" id="descripcion'+id+'" value="'+descripcion+'" readonly><p>'+descripcion+'</p></td><td> <input type="hidden" name="cantidad'+id+'" id="cantidad'+id+'" value="'+cantidad+'" readonly><p>'+cantidad+'</p></td><td> <input type="hidden" name="precio'+id+'" id="precio'+id+'" value="'+precio+'" readonly><p>'+precio+'</p></td><td> <input type="hidden" name="iva'+id+'" id="iva'+id+'" value="'+iva+'" readonly><p>'+iva+'</p></td><td> <input type="hidden" name="total'+id+'" id="total'+id+'" value="'+total+'" readonly><p>'+total+'</p></td><td style="width: 40px;"><button type="button" onclick="eliminar_item_msp('+id+')" class="btn btn-light"><img style="width: 15px;height:15px" src="{{asset('/')}}hc4/img/eliminar_item.png"></button></td>';
        
        document.getElementById('detalle_items_msp').appendChild(midiv_item);

        id = parseInt(id);
      
        id = id+1;
      }

      if((cantidad>0)&&(honorario_anest>0)){
        total_anest = ((cantidad*honorario_anest).toFixed(2));
        //Creamos la Tabla Temporal
        var midiv_item = document.createElement("tr")
        midiv_item.setAttribute("id","dato"+id);

        midiv_item.innerHTML = '<td><input required type="hidden" id="visibilidad_item_msp'+id+'" name="visibilidad_item_msp'+id+'" value="1"><input type="hidden" id="porce_10'+id+'" name="porce_10'+id+'" value="'+porce_10+'"><input type="hidden" id="idcabecera'+id+'" name="idcabecera'+id+'" value="'+cabecera+'"><input type="hidden" id="fecha'+id+'" name="fecha'+id+'" value="'+fecha+'"><input type="hidden" id="porcen_clasifi'+id+'" name="porcen_clasifi'+id+'" value="'+porcentaje_clas+'"><input type="hidden" name="niv_convenio'+id+'" id="niv_convenio'+id+'" value="'+niv_conv+'" readonly><input type="hidden" name="clasificador'+id+'" id="clasificador'+id+'" value="'+clasif_an+'" readonly><p>'+clasif_an+'</p></td><td><input type="hidden" name="tipo'+id+'" id="tipo'+id+'" value="'+tip_an+'" readonly><p>'+tip_an+'</p></td><td> <input type="hidden" name="codigo'+id+'" id="codigo'+id+'"  value="'+codigo+'" readonly ><p>'+codigo+'</p></td><td> <input type="hidden" name="descripcion'+id+'" id="descripcion'+id+'" value="'+descripcion+'" readonly><p>'+descripcion+'</p></td><td> <input type="hidden"  name="cantidad'+id+'" id="cantidad'+id+'" value="'+cantidad+'" readonly><p>'+cantidad+'</p></td><td> <input type="hidden" name="precio'+id+'" id="precio'+id+'" value="'+honorario_anest+'" readonly><p>'+honorario_anest+'</p></td><td> <input type="hidden" name="iva'+id+'" id="iva'+id+'" value="'+iva+'" readonly><p>'+iva+'</p></td><td> <input type="hidden" name="total'+id+'" id="total'+id+'" value="'+total_anest+'" readonly><p>'+total_anest+'</p></td><td style="width: 40px;"><button type="button" onclick="eliminar_item_msp('+id+')" class="btn btn-light"><img style="width: 15px;height:15px" src="{{asset('/')}}hc4/img/eliminar_item.png"></button></td>';
      
        document.getElementById('detalle_items_msp').appendChild(midiv_item);

        id = parseInt(id);
    
        id = id+1;
      }

      if((tiempo_anest>0)&&(val_t_ane>0)){
        
        var descrip = 'TIEMPO DE ANESTESIA'

        total_t_anes = ((tiempo_anest*val_t_ane).toFixed(2));

        var midiv_item = document.createElement("tr")
        midiv_item.setAttribute("id","dato"+id);

        midiv_item.innerHTML = '<td><input required type="hidden" id="visibilidad_item_msp'+id+'" name="visibilidad_item_msp'+id+'" value="1"><input type="hidden" id="porce_10'+id+'" name="porce_10'+id+'" value="'+porce_10+'"><input type="hidden" id="idcabecera'+id+'" name="idcabecera'+id+'" value="'+cabecera+'"><input type="hidden" id="fecha'+id+'" name="fecha'+id+'" value="'+fecha+'"><input type="hidden" id="porcen_clasifi'+id+'" name="porcen_clasifi'+id+'" value="'+porcentaje_clas+'"><input type="hidden" name="niv_convenio'+id+'" id="niv_convenio'+id+'" value="'+niv_conv+'" readonly><input type="hidden" name="clasificador'+id+'" id="clasificador'+id+'" value="'+clasif_ta+'" readonly><p>'+clasif_ta+'</p></td> <td><input type="hidden" name="tipo'+id+'" id="tipo'+id+'" value="'+tip_t_anest+'" readonly><p>'+tip_t_anest+'</p></td><td><input type="hidden" name="codigo'+id+'" id="codigo'+id+'"  value="'+codigo+'" readonly ><p>'+codigo+'</p></td> <td> <input type="hidden" name="descripcion'+id+'" id="descripcion'+id+'" value="'+descrip+'" readonly><p>'+descrip+'</p></td><td> <input type="hidden"  name="cantidad'+id+'" id="cantidad'+id+'" value="'+tiempo_anest+'" readonly><p>'+tiempo_anest+'</p></td><td> <input type="hidden" name="precio'+id+'" id="precio'+id+'" value="'+val_t_ane+'" readonly><p>'+val_t_ane+'</p></td><td> <input type="hidden" name="iva'+id+'" id="iva'+id+'" value="'+iva+'" readonly><p>'+iva+'</p></td><td> <input type="hidden" name="total'+id+'" id="total'+id+'" value="'+total_t_anes+'" readonly><p>'+total_t_anes+'</p>  </td><td style="width: 40px;"><button type="button" onclick="eliminar_item_msp('+id+')" class="btn btn-light"><img style="width: 15px;height:15px" src="{{asset('/')}}hc4/img/eliminar_item.png"></button></td>';

        document.getElementById('detalle_items_msp').appendChild(midiv_item);

        id = parseInt(id);
    
        id = id+1;

      }
     
        $('#tipo').val("");
        $('#codigo').val("");
        $('#descripcion').val("");
        $('#cantidad').val("");
        $('#precio').val("");
        $('#iva').val("");
        $('#clasificador').val("");
        $('#porcent_10').val("");
        $('#hono_Anest').val("");
        $('#tiempo_Anest').val("");
        $('#proceso_separ').val("");

      }

      if((honorario_anest == 0)&&(tiempo_anest == 0)&&(iva == 0)){

        total = ((cantidad*precio).toFixed(2));
        //Creamos la Tabla Temporal
        var midiv_item = document.createElement("tr")
        midiv_item.setAttribute("id","dato"+id);

        midiv_item.innerHTML = '<td><input required type="hidden" id="visibilidad_item_msp'+id+'" name="visibilidad_item_msp'+id+'" value="1"><input type="hidden" id="porce_10'+id+'" name="porce_10'+id+'" value="'+porce_10+'"><input type="hidden" id="idcabecera'+id+'" name="idcabecera'+id+'" value="'+cabecera+'"><input type="hidden" id="fecha'+id+'" name="fecha'+id+'" value="'+fecha+'"><input type="hidden" id="porcen_clasifi'+id+'" name="porcen_clasifi'+id+'" value="'+porcentaje_clas+'"><input type="hidden" name="niv_convenio'+id+'" id="niv_convenio'+id+'" value="'+niv_conv+'" readonly><input type="hidden" name="clasificador'+id+'" id="clasificador'+id+'" value="'+clasif+'" readonly><p>'+clasif+'</p></td><td><input type="hidden" name="tipo'+id+'" id="tipo'+id+'" value="'+tipo+'" readonly><p>'+tipo+'</p></td><td><input type="hidden" name="codigo'+id+'" id="codigo'+id+'"  value="'+codigo+'" readonly><p>'+codigo+'</p></td> <td> <input type="hidden" name="descripcion'+id+'" id="descripcion'+id+'" value="'+descripcion+'" readonly><p>'+descripcion+'</p></td><td> <input type="hidden" name="cantidad'+id+'" id="cantidad'+id+'" value="'+cantidad+'" readonly><p>'+cantidad+'</p></td><td> <input type="hidden" name="precio'+id+'" id="precio'+id+'" value="'+precio+'" readonly><p>'+precio+'</p></td><td> <input type="hidden" name="iva'+id+'" id="iva'+id+'" value="'+iva+'" readonly><p>'+iva+'</p></td><td> <input type="hidden" name="total'+id+'" id="total'+id+'" value="'+total+'" readonly><p>'+total+'</p></td><td style="width: 40px;"><button type="button" onclick="eliminar_item_msp('+id+')" class="btn btn-light"><img style="width: 15px;height:15px" src="{{asset('/')}}hc4/img/eliminar_item.png"></button></td>';

        document.getElementById('detalle_items_msp').appendChild(midiv_item);

        id = parseInt(id);

        id = id+1;

        $('#tipo').val("");
        $('#codigo').val("");
        $('#descripcion').val("");
        $('#cantidad').val("");
        $('#precio').val("");
        $('#iva').val("");
        $('#clasificador').val("");
        $('#porcent_10').val("");
        $('#hono_Anest').val("");
        $('#tiempo_Anest').val("");
        $('#proceso_separ').val("");

      }

      if((honorario_anest == 0)&&(tiempo_anest == 0)&&(iva>0)){
    
        val = precio/const4;
        subtotal = cantidad*val;
        valor10 = subtotal*porce_10;
        valor_iva = subtotal*iva;
        total_ant = subtotal+valor10+valor_iva;
      
        total = ((total_ant).toFixed(2));

        //Creamos la Tabla Temporal
        var midiv_item = document.createElement("tr")
        midiv_item.setAttribute("id","dato"+id);

        midiv_item.innerHTML = '<td><input required type="hidden" id="visibilidad_item_msp'+id+'" name="visibilidad_item_msp'+id+'" value="1"><input type="hidden" id="porce_10'+id+'" name="porce_10'+id+'" value="'+porce_10+'"><input type="hidden" id="idcabecera'+id+'" name="idcabecera'+id+'" value="'+cabecera+'"><input type="hidden" id="fecha'+id+'" name="fecha'+id+'" value="'+fecha+'"><input type="hidden" id="porcen_clasifi'+id+'" name="porcen_clasifi'+id+'" value="'+porcentaje_clas+'"><input type="hidden" name="niv_convenio'+id+'" id="niv_convenio'+id+'" value="'+niv_conv+'" readonly><input type="hidden" name="clasificador'+id+'" id="clasificador'+id+'" value="'+clasif+'" readonly><p>'+clasif+'</p></td><td><input type="hidden" name="tipo'+id+'" id="tipo'+id+'" value="'+tipo+'" readonly><p>'+tipo+'</p></td><td><input type="hidden" name="codigo'+id+'" id="codigo'+id+'"  value="'+codigo+'" readonly><p>'+codigo+'</p></td> <td> <input type="hidden" name="descripcion'+id+'" id="descripcion'+id+'" value="'+descripcion+'" readonly><p>'+descripcion+'</p></td><td> <input type="hidden" name="cantidad'+id+'" id="cantidad'+id+'" value="'+cantidad+'" readonly><p>'+cantidad+'</p></td><td> <input type="hidden" name="precio'+id+'" id="precio'+id+'" value="'+precio+'" readonly><p>'+precio+'</p></td><td> <input type="hidden" name="iva'+id+'" id="iva'+id+'" value="'+iva+'" readonly><p>'+iva+'</p></td><td> <input type="hidden" name="total'+id+'" id="total'+id+'" value="'+total+'" readonly><p>'+total+'</p></td><td style="width: 40px;"><button type="button" onclick="eliminar_item_msp('+id+')" class="btn btn-light"><img style="width: 15px;height:15px" src="{{asset('/')}}hc4/img/eliminar_item.png"></button></td>';

        document.getElementById('detalle_items_msp').appendChild(midiv_item);

        id = parseInt(id);

        id = id+1;

        $('#tipo').val("");
        $('#codigo').val("");
        $('#descripcion').val("");
        $('#cantidad').val("");
        $('#precio').val("");
        $('#iva').val("");
        $('#clasificador').val("");
        $('#porcent_10').val("");
        $('#hono_Anest').val("");
        $('#tiempo_Anest').val("");
        $('#proceso_separ').val("");
      }

      document.getElementById('contador_item_msp').value = id;
    
    }
  

  function eliminar_item_msp(valor)
  {
    var dato_item1 = "dato"+valor;
    var dato_item2 = 'visibilidad_item_msp'+valor;
    document.getElementById(dato_item1).style.display='none';
    document.getElementById(dato_item2).value = 0;
  }

    /*Guardado de Item MSP*/
    function store_item_msp(){

      cont_tabla = document.getElementById('contador_item_msp').value;

      if(cont_tabla == 0)
      {
        swal("¡Error!","Ingrese datos en la tabla","error");

        return false;
      }

      $.ajax({
            type: 'post',
            url:"{{route('msp_store.item')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#guardar_item_msp").serialize(),
            success: function(data){
              location.reload();
            },
            error: function(data){
                console.log(data);

            }
      });
 
    }




</script>