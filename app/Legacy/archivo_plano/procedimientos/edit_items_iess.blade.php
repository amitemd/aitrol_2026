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
              <a class="btn btn-light" onclick="update_item_iess()">
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
    <div class="box-body">
      <br> 
      <form id="frm_upd_item_iess" method="post">
          <input type="hidden" name="idplanodetalle" value="{{$det_plano->id}}">
          <input type="hidden" name="porcent_10" id="porcent_10"  value="{{$det_plano->porcent_10}}">
          <input type="hidden" name="orden_plantilla" id="orden_plantilla"  value="{{$det_plano->orden_plantilla_item}}">
          <input type="hidden" name="nivel" id="nivel" value="{{$det_plano->id_nivel}}">
          <div class="form-group col-md-6">
            <label for="nivel_convenio" class="col-md-3 control-label">Id:</label>
            <div class="col-md-7">
		          {{$indice}}
	          </div>
          </div>
          <div class="form-group col-md-6">
              <label for="fecha" class="col-md-3 control-label">Fecha:</label>
              <div class="col-md-7">
                  <div class="input-group date">
                      <div class="input-group-addon">
                          <i class="fa fa-calendar"></i>
                      </div>
                      <input type="text"  class="form-control input-sm" id="fecha" name="fecha" value="{{ old('fecha') }}" autocomplete="off">
                      <div class="input-group-addon">
                       <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = '';"></i>
                      </div> 
                  </div>
              </div>
          </div>
          <div class="form-group col-md-6 col-xs-6">
            <label for="tipo" class="col-md-3 control-label">Tipo:</label>
            <div class="col-md-7">
              <input id="tipo" type="text" class="form-control input-sm" name="tipo" value="@if(!is_null($det_plano)){{$det_plano->tipo}}@endif">
            </div>
          </div>
          <div class="form-group col-md-6 col-xs-6">
              <label for="codigo" class="col-md-3 control-label">Código:</label>
              <div class="col-md-7">
                  <input id="codigo" maxlength="40" type="text" class="form-control input-sm" name="codigo" value="@if(!is_null($det_plano)){{$det_plano->codigo}}@endif">
              </div>
          </div>
          <div class="form-group col-md-6 col-xs-6">
              <label for="codigo" class="col-md-3 control-label"></label>
              <div class="col-md-7">
              </div>
          </div>
          <div class="form-group col-md-9 col-xs-12">
              <label for="descripcion1" class="col-md-2 control-label">Descripción:</label>
              <div class="col-md-7">
                <textarea id="descripcion1" name="descripcion1"  style="width: 200%; border: 1px solid #004AC1;" rows="3" readonly>@if(!is_null($det_plano))<?php echo $det_plano->descripcion ?>@endif</textarea>
              </div>
          </div>
          <div class="form-group col-md-4 col-xs-6">
              <label for="cantidad" class="col-md-3 control-label">Cantidad:</label>
              <div class="col-md-7">
                  <input id="cantidad" type="text" class="form-control input-sm" name="cantidad" value="@if(!is_null($det_plano)){{$det_plano->cantidad}}@endif"> 
              </div>
          </div>
          <div class="form-group col-md-4 col-xs-6">
              <label for="precio" class="col-md-3 control-label">Precio:</label>
              <div class="col-md-7">
                  <input id="precio" type="text" class="form-control input-sm" name="precio" value="@if(!is_null($det_plano)){{$det_plano->valor}}@endif">
              </div>
          </div>
          <div class="form-group col-md-4 col-xs-6">
              <label for="iva" class="col-md-3 control-label">Iva:</label>
              <div class="col-md-7">
                  <input id="iva" type="text" class="form-control input-sm" name="iva" value="@if(!is_null($det_plano)){{$det_plano->porcentaje_iva}}@endif">
              </div>
          </div> 
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

        @if($det_plano->fecha !=null)
              defaultDate: '{{$det_plano->fecha}}',
	      @endif

        
      });
    
    });
    
    
    /*$( document ).ready(function() {
      $("#descripcion1").autocomplete({
        source: function(request,response){
          
          $.ajax({
            url:"{{route('item_iess.buscardescripcion')}}",
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
          $("#precio").val(ui.item.precio);
          $("#iva").val(ui.item.iva);
          $("#porcent_10").val(ui.item.porcent10);
          
        },
        minLength: 4,
      } );

    });*/
    
    
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

    /*Update Iess IESS*/
    function update_item_iess(){

      $.ajax({
            type: 'post',
            url:"{{route('store_item_modal.iess')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#frm_upd_item_iess").serialize(),
            success: function(data){
                location.reload();
            },
            error: function(data){
                console.log(data);

            }
      });
 
    }




</script>