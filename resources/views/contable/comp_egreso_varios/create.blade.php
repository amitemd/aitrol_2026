@extends('contable.comp_egreso.base')
@section('action-content')
@php
$date = date('Y-m-d');
$h = date('Y-m',strtotime($date));
@endphp
<style type="text/css">
    .ui-autocomplete
        {
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
         .alerta_correcto{
            position: absolute;
            z-index: 9999;
            top: 100px;
            right: 10px;
        }
    }
        .container {
            display: block;
            position: relative;
            padding-left: 35px;
            margin-bottom: 12px;
            cursor: pointer;
            font-size: 18px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

/* Hide the browser's default checkbox */
        .container input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

/* Create a custom checkbox */
        .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 25px;
            width: 25px;
            background-color: #eee;
        }
       /* .form-control{
            background-color: #f5f5f5;
        }*/

/* On mouse-over, add a grey background color */
        .container:hover input ~ .checkmark {
        background-color: #ccc;
        }

        /* When the checkbox is checked, add a blue background */
        .container input:checked ~ .checkmark {
        background-color: #2196F3;
        }

        /* Create the checkmark/indicator (hidden when not checked) */
        .checkmark:after {
        content: "";
        position: absolute;
        display: none;
        }

        /* Show the checkmark when checked */
        .container input:checked ~ .checkmark:after {
        display: block;
        }

        /* Style the checkmark/indicator */
        .container .checkmark:after {
        left: 9px;
        top: 5px;
        width: 5px;
        height: 10px;
        border: solid white;
        border-width: 0 3px 3px 0;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
        }

        .text {
          color:  white;
          padding: 10px;
          background-color: green;
          font-size:15px;
          font-family:helvetica;
          font-weight:bold;
          text-transform:uppercase;
        }
        .parpadea {
          
          animation-name: parpadeo;
          animation-duration: 0.5s;
          animation-timing-function: linear;
          animation-iteration-count: infinite;

          -webkit-animation-name:parpadeo;
          -webkit-animation-duration: 4s;
          -webkit-animation-timing-function: linear;
          -webkit-animation-iteration-count: infinite;
        }

        @-moz-keyframes parpadeo{  
          0% { opacity: 1.0; }
          50% { opacity: 0.5; }
          100% { opacity: 1.0; }
        }

        @-webkit-keyframes parpadeo {  
          0% { opacity: 1.0; }
          50% { opacity: 0.5; }
           100% { opacity: 1.0; }
        }

        @keyframes parpadeo {  
          0% { opacity: 1.0; }
           50% { opacity: 0.5; }
          100% { opacity: 1.0; }
        }
  

</style>
<script type="text/javascript">
    function goBack() {
      location.href="{{route('egresosv.index')}}";
    }
    
</script>
<input type="hidden" id="fechita" value="{{$date}}">
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<section class="content">
<div id="alerta_datos" class="alert alert-success alerta_correcto alert-dismissable" role="alert" style="display:none;">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
  {{trans('contableM.GuardadoCorrectamente')}}
</div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
        <li class="breadcrumb-item"><a href="{{route('egresosv.index')}}">{{trans('contableM.COMPROBANTEDEEGRESOSVARIOS')}}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.RegistroComprobantedeEgresoVarios')}}</li>
      </ol>
    </nav>   
    <form class="form-vertical " method="post" id="form_guardado">
        {{ csrf_field() }}
        
        <div class="box">
                    <div class="box-header header_new">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6 col-sm-9 col-6">
                                    <div class="box-title " ><b>{{trans('contableM.COMPROBANTEDEEGRESOSVARIOS')}}</b></div>
                                </div>
                                 <div class="col-md-2 text-left">
                                    <span class="parpadea text" id="boton" >{{$h}}</span>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <input type="hidden" id="minDate" name="minDate" value="@if(isset($min_date)){{$min_date}}@endif">
                                        <button type="button" id="guard" class="btn btn-success btn-gray" onclick="boton_deuda()" >
                                            <i class="glyphicon glyphicon-file" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.guardar')}}
                                        </button>
                                        <button type="button" class="btn btn-success btn-gray" onclick="nuevo_comprobante()" >
                                            <i class="glyphicon glyphicon-file" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.nuevo')}}</button>
                                        <button type="button" class="btn btn-success btn-gray" onclick="goBack()" >
                                            <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-body dobra">
                        <div class="row header">
                            <div class="col-md-12">
                                <div class="form-row "> 
                                    <div class=" col-md-2 px-1" >
                                        <label class="label_header">{{trans('contableM.estado')}}</label>
                                        <div style="background-color: green;" class="form-control "></div>           
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="col-md-12 label_header" for="id_factura">{{trans('contableM.id')}}:</label>
                                            <input class="form-control " type="text" name="id_factura" id="id_factura" readonly>    
                                        
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="label_header" for="numero_factura">{{trans('contableM.numero')}}</label>
                                            <input class="form-control " type="text" id="numero_factura" name="numero_factura" readonly>
                                        
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                        <label class="col-md-12 label_header" for="tipo">{{trans('contableM.tipo')}}</label>
                                        <input class="form-control " type="text" name="tipo" id="tipo" value="BAN-EG" readonly>    
                                    
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="label_header" for="asiento">{{trans('contableM.asiento')}}</label>
                                            <input class="form-control " type="text" id="asiento" name="asiento" readonly>                                                                            
                                    </div>
                                    <div class=" col-md-2 px-1">
                                            <label class="col-md-12 label_header" for="fecha_hoy">{{trans('contableM.fecha')}}: </label>
                                            <input class="form-control input-sm " id="fecha_hoy" onblur="validarPeriodo();validarCierre();" min="{{ isset($min_date) ? date('Y-m-d', strtotime($min_date)) : '' }}" type="date" name="fecha_hoy" value="{{date('Y-m-d')}}">
                                    </div>
                                </div>
                                <div class="col-md-2 px-1">
                                    <label for="" class="control-label label_header">{{trans('contableM.sucursal')}}</label>
                                    <select name="sucursal" id="sucursal" class="form-control" require>
                                        <option value="">{{trans('agenda.seleccione')}}..</option>
                                        @foreach($sucursales as $sucursal)
                                        <option value="{{$sucursal->codigo_sucursal}}">{{$sucursal->codigo_sucursal}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-row " id="no_visible">
                                    <div class=" col-md-8 px-1">
                                            <label class="label_header" for="acreedor">{{trans('contableM.concepto')}}:</label>
                                            <input class="form-control  col-md-12" type="text" name="concepto" autocomplete="off" id="concepto" >
                                    </div>

                                    <div class="col-md-2 px-1">
                                        <div style="padding: 0;" class="col-md-12">
                                            <label class="label_header" for="acreedor">{{trans('contableM.Chequeentregado')}}</label>
                                        </div>
                                        <div style="display: flex; justify-content: center;" class="col-md-12">
                                            <input style="width: 25px; height: 25px;" class="col-md-12" type="checkbox" id="cheque_entregado" name="cheque_entregado">
                                        </div>
                                    </div>


                                    <!-- <div class=" col-md-2 px-1">
                                        <p class="">{{trans('contableM.Chequeentregado')}} <input type="checkbox" id="cheque_entregado" name="cheque_entregado">  </p>
                                        
                                    </div> -->
                                    <div class=" col-md-4 visibilidad px-0">
                                            <label class="col-md-12 label_header" for="ruc">{{trans('contableM.banco')}}:</label>
                                            <select class="form-control " name="banco" id="banco" onchange="bloquear_cheque();" required>
                                            <option value="">Seleccione..</option>
                                            @foreach($banco as $value)
                                                <option value="{{$value->id}}">{{$value->nombre}}</option>
                                            @endforeach
                                            </select>
                                    </div>
                                    <div class=" col-md-2 visibilidad px-0">
                                            <label class="col-md-12 label_header" for="secuencia">{{trans('contableM.divisass')}}:</label>
                                            <select class="form-control col-md-12 "   name="divisasa" id="divisasa">
                                                <option value="0">Seleccione...</option>
                                                @foreach($divisas as $value)
                                                    <option value="{{$value->id}}">{{$value->descripcion}}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                    <div class=" col-md-2 visibilidad px-1">
                                            <label class="col-md-12 label_header" for="secuencia">{{trans('contableM.cambio')}}:</label>
                                            <input class="form-control " type="text" name="secuencia" id="secuencia" value="1.00" readonly>
                                    </div>
                                    <div class=" col-md-2 visibilidad px-1">
                                            <label  class="col-md-12 label_header control-label" for="numero_cheque">{{trans('contableM.NroCheque')}}</label>
                                            <input class="form-control " type="number" name="numero_cheque" id="numero_cheque"> 
                                    </div>
                                    <div class=" col-md-2 visibilidad px-1">
                                            <label class="col-md-12 label_header" for="fecha_cheque">{{trans('contableM.fechacheque')}}: </label>
                                            <input class="form-control " type="date" name="fecha_cheque" id="fecha_cheque" value="{{date('Y-m-d')}}">
                                    </div>
                                    <div class=" col-md-1 px-1">
                                        <label for="valor" class="label_header">{{trans('contableM.valor')}}</label>
                                        <input class="form-control " autocomplete="off" type="text" name="valor_cheque" id="valor_cheque" onblur="setNumber(this.value)" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
                                    
                                    </div>
                                    <div class=" col-md-3 px-1">
                                            {{ csrf_field() }} 
                                            <label class="col-md-12 label_header" for="acreedor">{{trans('contableM.beneficiario')}}</label>
                                            <input type="text" id = "beneficiario" name="beneficiario" class= "form-control form-control-sm " autocomplete="off">
                                    </div>
                                    <div class=" col-md-3 px-1">
                                        
                                            <label class="col-md-12 label_header" for="direccion">{{trans('contableM.direccion')}}: </label>
                                            <input class="form-control " type="text" name="direccion" id="direccion" >
                                    </div>
                                    <div class=" col-md-3 visibilidad px-1">
                                            <label class="col-md-12 label_header" for="girado"> {{trans('contableM.giradoa')}}:</label>
                                            <input type="hidden" name="verificar_cheque" id="verificar_cheque" value="0">
                                            <input class="form-control " type="text" name="giradoa" id="giradoa" autocomplete="off">
                                    </div>
                                    <div class="col-md-2 px-1">
                                        <label for="" class="control-label label_header">{{trans('contableM.formasdepago')}}</label>
                                        <select name="formas_pago" id="formas_pago" class="form-control">
                                            <option value="">Seleccione..</option>
                                            @foreach($formas_pago as $value)
                                            <option value="{{$value->id}}">{{$value->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        &nbsp;
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="detalle_deuda" class="control-label label_header">{{trans('contableM.DETALLEDELCOMPLEMENTOCONTABLE')}}</label>
                            
                                <input type="hidden" name="id_compra" id="id_compra">
                                <input type="hidden" name="contador" id="contador" value="0">
                                <div class="table-responsive" style="width: 100%;">               
                                    <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info" style="width: 100%;">
                                        <thead style="background-color: #9E9E9E; color: white;" >
                                        <tr style="position: relative;">
                                            <th style="text-align: center;">{{trans('contableM.Cuenta')}}</th>
                                            <th style="text-align: center;">{{trans('contableM.divisas')}}</th>
                                            <th style="text-align: center;">{{trans('contableM.Debe')}}</th>
                                            <th style="text-align: center;">{{trans('contableM.Haber')}}</th>
                                            <th style="text-align: center;">{{trans('contableM.ValorBase')}}</th>
                                            <th style=" text-align: center;">
                                                <button  type="button" onclick="crearFila();" class="btn btn-success btn-gray btn-xs">
                                                    <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                                </button>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody id="dt_recibido">
                                        @php $cont=0; @endphp                                            
                                        </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="col-md-12" style="margin-top: 30px;">
                                    <div class="form-row">
                                        <div class="form-group col-md-2">
                                            
                                            <input type="hidden" name="saldo_hidden0" id="saldo_hidden0">
                                            <input type="hidden" name="total_egreso" id="total_egreso" value="0">
                                        </div>
                                        <div class="form-group col-md-2">
                                            
                                        </div>
                                        <div class="form-group col-md-3" style="text-align: right;">
                                            
                                        </div>
                                        <div class="form-group col-md-2">
                                            <div class="input-group">
                                               
                                                <input type="hidden" name="proveedor" id="proveedor"> 
                                              
                                                <input type="hidden" name="sobrante" id="sobrante">
                                                <input type="hidden" name="egreso_retenciones" id="egreso_retenciones">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <div class="col-md-12">
                                <div class="row">
                                <div class=" col-md-6 form-group">
                                    <label class="control_label" for="debe_final">{{trans('contableM.Debe')}}</label>
                                    <input type="text" class="form-control" id="debe_final" readonly value="0.00">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="control_label" for="haber_final">{{trans('contableM.Haber')}}</label>
                                    <input type="text" class="form-control" id="haber_final" readonly value="0.00">
                                </div>  
                                </div>
                            </div>
                            <div class="col-md-12">
                                &nbsp;
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="input-group">
                                <label class="col-md-12 label_header" style="color: white;" for="nota">{{trans('contableM.nota')}}:</label>
                                <textarea class="col-md-12 " name="nota" id="nota" cols="250" rows="5"></textarea>
                                <input type="hidden" name="saldo_final" id="saldo_final">
                            </div>
                        </div>
                    </div>
            </div>
    </form>

</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script type="text/javascript">

 
    function validarCierre() {
        <?php
        if (isset($min_date) && $min_date != null) {
        ?>
         let fechaAc = new Date();
            var fecha1 = '<?= $min_date; ?>';
            var fecha2 = document.getElementById("fecha_hoy").value;
            const fechaInicial = new Date(fecha1);
            const fechaFinal = new Date(fecha2);
            if (fechaFinal <= fechaInicial) {
                swal("Recuerde!", 'Este registro no se puede modificar, ya que la fecha seleccionada corresponde a un período cerrado contablemente. Para hacer cambios, elija una fecha dentro del período contable vigente.', "error");
                const fechaFormateada = fechaAc.toISOString().split('T')[0];
                document.getElementById("fecha_hoy").value = fechaFormateada;
            }
        <?php
        }
        ?>
    }

    function validarPeriodo() {
        $fechaAc = new Date();
        $mes = $fechaAc.getUTCMonth() + 1;
        var fechaInput = document.getElementById("fecha_hoy").value;
        var partesFecha = fechaInput.split('/');
        var fechaFormateada = `${partesFecha[2]}-${partesFecha[1]}-${partesFecha[0]}`;
        var d = new Date(fechaFormateada);
        var month = d.getMonth() + 1;
        console.log(month);
        var ty = document.getElementById("fechita").value;
        if ($mes != month) {
            return swal("Recuerde!", '<?= trans('contableM.lafechaingreestafueradelperio'); ?>', "error")
        }
    }

    function bloquear_cheque(){
        var bco = $("#banco").val();
        $.ajax({
            type: 'get',
            url:"{{url('contable/acreedores/documentos/cuentas/egreso/varios/buscar/banco')}}/"+bco,
            datatype: 'json',
            success: function(data){
              if (data.clase_banco != '1') {
                document.getElementById("numero_cheque").disabled = true;
                $("#numero_cheque").val(' ');
              }else{
                document.getElementById("numero_cheque").disabled = false;
              }
            },
            error: function(data){
            }
        })
    }
   
    function buscar_factura(){
        $("#buscar").next().remove();
        $.ajax({
            type: 'post',
            url:"{{route('acreedores_buscar_codigo')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'id_factura':$("#buscar").val()},
            success: function(data){
                //console.log(data);
                var iva= (data[10]*0.12); 
                $("#id_factura").val(data[0]);
                $("#numero_factura").val(data[12]);
                $("#concepto").val(data[4]+'.'+' '+'REF :'+data[0]);
                $("#asiento").val(data[11]);
                $("#acreedor").val(data[0]+' '+data[2]);
                $("#direccion").val(data[3]);
                $("#total_factura").val(data[10]);
                $("#total_deudas").val(data[10]);
                //$("#id_compra").val(data[1]);
                $("#vence").val(data[6]);
                $("#tipo").val(data[7]);
                $("#base_fuente").val(data[10]);
                $("#id_proveedor").val(data[0]);
                $("#nombre_proveedor").val(data[2]);

                for(i=0;i<data[16].length; i++){
                    $("#vence"+i).val(data[16][i].fecha_asiento);
                    $("#tipo"+i).val('FACT-COMPRA')
                    $("#numero_referencia"+i).val('Id:'+(data[16][i].id)+'Sec:'+(data[16][i].fact_numero));
                    $("#base_fuente"+i).val(data[16][i].valor);
                    $("#nuevo_saldo"+i).val(data[16][i].valor_nuevo);
                    $("#divisas"+i).val(data[16][i].divisas); 
                    $("#numero"+i).val(data[16][i].fact_numero);
                    $("#concepto"+i).val(data[16][i].observacion); 
                    $("#saldo"+i).val((data[16][i].valor_nuevo));
                    $("#saldo_hidden"+i).val((data[16][i].valor_nuevo));
                    $("#tipo_rfiva"+i).val((data[16][i].id_porcentaje_iva));
                    $("#tipo_rfir"+i).val((data[16][i].id_porcentaje_ft));
                    var iva_base= parseFloat(data[16][i].valor);
                    var total_iva= iva_base*12/100;
                    $("#base_iva"+i).val(total_iva.toFixed(2));            
                }
             
                
            },
            error: function(data){
                //console.log(data);
            }
        })
    }
    function nuevo_comprobante(){
        location.href='{{route('egresov.create')}}';
    }
    $(".buscar").autocomplete({
        source: function( request, response ) {
            $.ajax( {
            url: "{{route('retenciones_codigo')}}",
            dataType: "json",
            data: {
                term: request.term
            },
            success: function( data ) {
                response(data);
                //console.log(data);
            }
            } );
        },
        minLength: 1,
    } );
    $('.spropety').on('ifChecked', function(event){
        console.log("dasda");
        $("#verificar_cheque").val(1);
    });
    $('.spropety').on('ifUnchecked', function (event) {
        $("#verificar_cheque").val(0);
    });
    function funciones_pago(){
        var formas_pago= parseInt($("#formas_pago").val());
        switch(formas_pago){
            case 1:
                [].forEach.call(document.querySelectorAll('.visibilidad'), function (el) {
                 //el.style.display = 'none';
                });
                break;
            case 2:
                [].forEach.call(document.querySelectorAll('.visibilidad'), function (el) {
                 //el.style.display = 'block';
                });
                break;
        }
    }
    function cambiar_nombre(id){
        $.ajax({
            type: 'get',
            url:"{{route('fact_contable_nombre2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'nombre':$("#nombre"+id).val()},
            success: function(data){
                $('#codigo'+id).val(data);
            },
            error: function(data){
                console.log(data);
            }
        })
    }
    
    function cambiar_nombre_proveedor(){
        $.ajax({
            type: 'post',
            url:"{{route('compra_buscar_proveedornombre')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'nombre':$("#nombre_proveedor").val()},
            success: function(data){
                //console.log(data);
                $("#id_proveedor").val(data.value);
                buscar_proveedor()
            },
            error: function(data){
                //console.log(data);
            }
        })
    }
    $(".nombre_proveedor").autocomplete({
        source: function( request, response ) {
            $.ajax( {
            url: "{{route('compra_buscar_nombreproveedor')}}",
            dataType: "json",
            data: {
                term: request.term
            },
            success: function( data ) {
                //console.log(data)
                response(data);
            }
            } );
        },
        minLength: 1,
    } );


    function abono_totales(){
        var valor= parseFloat($("#valor_cheque").val());
        var saldo= parseFloat($("#saldo0").val());
        if(!isNaN(valor)){
            $("#abono0").val(valor);
            $("#abono_base0").val(valor);
            var totales= saldo-valor;
            //alert(totales);
            if(totales>0){
                $("#saldo0").val(totales.toFixed(2));
                var saldo_hidden= parseFloat($("#saldo_hidden0").val());
                var total_sinresta=saldo_hidden.toFixed(2)-totales.toFixed(2);
                $("#saldo_final").val(total_sinresta);
            }else{
                var saldo_hidden= parseFloat($("#saldo_hidden0").val());
                var total_sinresta=saldo_hidden.toFixed(2)-totales.toFixed(2);
                $("#saldo_final").val(total_sinresta);
                $("#saldo0").val('0');
            }
            
        }else{
            var valor =parseFloat($("#saldo_hidden0").val());
            //alert(valor);
            $("#saldo0").val(valor);
            $("#abono_base0").val(valor);
        }
    }

    const crearFila = (seleccion = 0) => {
        let id = document.getElementById('contador').value;
        console.log(id)
        let cuenta_id = 0;
        if (parseInt(id) == 1) {
            cuenta_id = 1;
        }
        console.log(cuenta_id)
        let fila = `
            <tr >
                
                <td>
             
                    <select class="form-control select2_cuentas" name="codigo[]" id="codigo${id}" style="width: 100%;" required> 
                    <option value="">Seleccione...</option> 

                        @foreach($cuentas as $value)  
                            <option value="{{$value->id}}"> {{$value->plan}} - {{$value->nombre}} </option>
                        @endforeach 
                    </select> 
                    <input  type="hidden" id="visibilidad${id}" name="visibilidad[]" value="1">
                </td>
                <td>
                    <div> 
                        <select class="form-control" style=" width: 85%; height: 80%;" name="divisas[]" id="divisas${id}" > 
                            <option value="">Seleccione... </option> 
                            @foreach($divisas as $value) 
                                <option @if($value->id == 1) selected @endif value="{{$value->id}}">{{$value->descripcion}}</option> 
                            @endforeach  
                        </select>
                    </div>
                </td>

                <td>
                    <div>
                        <input class="form-control debe" style=" width: 88%; height: 80%;"   type="text" name="debe[]" id="debe${id}" onkeypress="return isNumberKey(event)" onblur="this.value= parseFloat(this.value) > 0 ? parseFloat(this.value).toFixed(2) : '0.00';addvalue();" value="0.00"   >
                    </div>
                </td>

                <td>
                    <div>
                        <input class="form-control haber" style=" width: 88%; height: 80%" id="haber${id}" name="haber[]"  onblur="this.value= parseFloat(this.value) > 0 ? parseFloat(this.value).toFixed(2) : '0.00';addvalue2();"  value="0.00"  >
                    </div>
                </td> 

                <td>
                    <div> 
                        <input style=" width: 79%; height: 80%" class="form-control" id="valor_base${id}" name="valor_base[]" value="0.00" readonly onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);addvalue();" >
                    </div>
                </td> 

                <td>
                    <button type="button" class="btn btn-danger btn-gray delete">
                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                    </button>
                </td>
            </tr>
        `

        $('#dt_recibido').append(fila);

        $('.select2_cuentas').select2({
            tags: false
        });


        envalor_base(id);
        envalor_base2(id);
        document.getElementById('dt_recibido').value = id;

        id++;
        document.getElementById('contador').value = id;

        $('.codigo').autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "{{route('fact_contable_codigo')}}",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            selectFirst: true,
            minLength: 1,
        });



    }

    function boton_deuda(){
        //swal("hassta aqui");
        var formulario = document.forms["form_guardado"];
        var valor_cheque= formulario.valor_cheque.value;
        var numero_cheque= formulario.numero_cheque.value;
        var formas_pago = formulario.formas_pago.value;
        var contador= formulario.contador.value;
        var beneficiario= formulario.beneficiario.value;
        var direccion = formulario.direccion.value;
        var banco = formulario.banco.value;
        var divisas= formulario.divisasa.value;  
        var concepto= formulario.concepto.value;
        var sucursal = $("#sucursal").val();
        var msj = "";
       
        if (sucursal == 0 || sucursal == "") {
            alertas("error", "Error...", "Seleccione la sucursal")
            return;
        }
        if (formas_pago == "2" && numero_cheque == "") {
            msj += "Por favor, Llene el numero del cheque<br/>";
        }
        if(valor_cheque==""){
            msj+="Por favor, Llene el valor<br/>";
        }
        if(contador==""){
            msj+="Por favor, Llene el campo de la tabla antes de guardar <br/>";
        }
        if(beneficiario==""){
            msj+="Por favor, Llene el campo de beneficiario <br/>";
        }
      
        if(banco==""){
            msj+="Por favor, Llene el campo de banco <br/>";
        }
        if(divisas==""){
            msj+="Por favor, Llene el campo de divisas <br/>";
        }
        if(concepto==""){
            msj+="Por favor, Llene el campo de concepto <br/>";
        }
        if(fecha==""){
            msj+="Por favor, Llene el campo de fecha <br/>";
        }
        if (formas_pago == "") {
            msj += "Por favor, Llene las formas de pago <br/>";
        }
        
        var vence= $("#vence0").val();
        var tipo= $("#tipo0").val();
        var numero= $("#numero0").val();
        var final_valor_cheque= $("#valor_cheque").val();
        var validacion= addvalue();
        var validacion2= addvalue2();
        var concepto= $("#concepto0").val();
        var saldo_final= $("#saldo_base0").val();
        if(msj==""){    
        /* if(validacion<final_valor_cheque){
                if(validacion2>0){
                    swal("Error!","El total del valor debe no cumple con el valor contable","error");
                }else{
                    if ($("#form_guardado").valid()) {
                        $("#guard").prop("disabled","disabled");
                        $.ajax({
                            type: 'post',
                            url:"{{route('egresov.store')}}",
                            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                            datatype: 'json',
                            data: $('#form_guardado').serialize(),
                            success: function(data){
                                if((data)!='false'){
                                    $("#vence0").val(vence);
                                    $("#tipo0").val(tipo);
                                    $("#numero0").val(numero);
                                    $("#saldo0").val(saldo_final);
                                    $("#nuevo_saldo0").val(saldo_final);
                                    $("#abono_base0").val(saldo_final);      
                                    //swal("Guardado correcto");
                                    $('#form_guardado input').attr('readonly', 'readonly');
                                    $("#guard").prop("disabled","disabled");
                                    buscarAsiento(data);
                                    url="{{ url('contable/compra/comprobante/egresovarios/pdf/')}}/"+data;
                                    window.open(url,'_blank');
                                    swal(`{{trans('contableM.correcto')}}!`,"Se creo el comprobante de egresos varios","success");             
                                }else{
                                    
                                }                    
                            },
                            error: function(data){
                            console.log(data);
                            }
                    })
                  }
                }
                
            }else{
                if ($("#form_guardado").valid()) {
                    $("#guard").prop("disabled","disabled");
                    $.ajax({
                        type: 'post',
                        url:"{{route('egresov.store')}}",
                        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                        datatype: 'json',
                        data: $('#form_guardado').serialize(),
                        success: function(data){
                            if((data)!='false'){
                                $("#vence0").val(vence);
                                $("#tipo0").val(tipo);
                                $("#numero0").val(numero);
                                $("#saldo0").val(saldo_final);
                                $("#nuevo_saldo0").val(saldo_final);
                                $("#abono_base0").val(saldo_final);      
                                //swal("Guardado correcto");
                                $('#form_guardado input').attr('readonly', 'readonly');
                                $("#guard").prop("disabled","disabled");
                                buscarAsiento(data);
                                url="{{ url('contable/compra/comprobante/egresovarios/pdf/')}}/"+data;
                                window.open(url,'_blank');
                                swal(`{{trans('contableM.correcto')}}!`,"Se creo el comprobante de egresos varios","success");             
                            }else{
                                
                            }                    
                        },
                        error: function(data){
                        console.log(data);
                        }
                  })
                }
            } */
            var debe=  parseFloat($('#debe_final').val());
            var haber= parseFloat($('#haber_final').val());
            var diferencia= (debe-haber) *(-1);
            if(debe==haber){
                if ($("#form_guardado").valid()) {
                        $("#guard").prop("disabled","disabled");
                        $.ajax({
                            type: 'post',
                            url:"{{route('egresov.store')}}",
                            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                            datatype: 'json',
                            data: $('#form_guardado').serialize(),
                            success: function(data){

                                if(data.status == "success"){
                                $("#vence0").val(vence);
                                $("#tipo0").val(tipo);
                                $("#numero0").val(numero);
                                $("#saldo0").val(saldo_final);
                                $("#nuevo_saldo0").val(saldo_final);
                                $("#abono_base0").val(saldo_final);      
                                //swal("Guardado correcto");
                                $('#form_guardado input').attr('readonly', 'readonly');
                                $("#guard").prop("disabled","disabled");
                                buscarAsiento(data.id);
                                url="{{ url('contable/compra/comprobante/egresovarios/pdf/')}}/"+data.id;
                                window.open(url,'_blank');
                                swal(`{{trans('contableM.correcto')}}!`,"Se creo el comprobante de egresos varios","success");             
                                               
                                }                               
                                if(data.status == 'error'){
                                     //alertas("error", "Error...", data.msj)
                                    alertas(data.status, `${data.status.toUpperCase()}...`, data.msj)
                                }
                            },
                            error: function(data){
                                console.log(data.status);
                            }
                    })
                }
            }else{
                swal("Mensaje","Tiene una diferencia de valores $"+diferencia,"error");
            }
        }else{
            swal({
                  title: "Error!",
                  type: "error",
                  html: msj
                });
        }
    }
    
    function superavit(){
        var valor_final= $("#saldo0").val();
        var bono= $("#abono0").val();    
        var proveedor= $("#id_proveedor").val();
        var pago= $("#formas_pago").val();
        var nuevo_saldo= $("#nuevo_saldo0").val();
        var saldo_final= $("#saldo_final").val();   
        var secuencia_factura= $("#asiento").val();
        if(valor_final==0){
            if (confirm('Existe un superávit de'+saldo_final+'en la cobertura de las deudas. \n Desea que éste valor sea considerado como un Débito a favor de la Empresa')) {
                    $.ajax({
                        type: "post",
                        url: "{{route('acreedores_superavit')}}", 
                        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                        datatype: "json",
                        data:{'asiento':secuencia_factura,'id_pago':pago,
                        'proveedor':proveedor,'nuevo_saldo0':nuevo_saldo,'saldo_final':saldo_final},
                        success: function(data){
                            url="{{ url('contable/compra/comprobante/egresovarios/pdf/')}}/"+data;
                            buscarAsiento(data);
                            window.open(url,'_blank');
                            swal("¡Correcto!", "Superavit creado correctamente", "success");                  
                        },
                        error:  function(){
                            alert('error al cargar');

                        }
                    });
    
            } else {
                swal("¡Correcto!", "Comprobante Guardado Correctamente", "success");
                location.href ="{{route('acreedores_cegreso')}}";
            }
        }
        else{
            swal("¡Correcto!",`{{trans('proforma.GuardadoCorrectamente')}}`,"success");
        }
        
        
    }
    function setNumber(e){
       // return parseFloat(e).toFixed(2);
       //if(e.length)
       if(e==""){
       e=0;
       }
        $("#valor_cheque").val(parseFloat(e).toFixed(2))

    }
    function isNumberKey(evt)
   {
     var charCode = (evt.which) ? evt.which : event.keyCode
     if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
        return false;

     return true;
   }
   function buscarAsiento(id_asiento){
        $.ajax({
            type: 'get',
            url: "{{route('buscar_asiento.diario')}}",
            datatype: 'json',
            data: {
                'id_asiento': id_asiento,
                'validacion': '6'
            },
            success: function(data) {
                if (data.value != 'No se encontraron resultados') {
                    $('#asiento').val(data[0]);
                    $('#numero_factura').val(data[1]);
                }
            },
            error: function(data) {
                console.log(data);
            }
        })
    }
   function addvalue(){
       //need to change
        var contador=0;
       var total = 0;
       $(".debe").each(function() {
           if($(this).val().length>0){
            
                total = parseFloat(total) + parseFloat($(this).val());
             
               contador++;
           }
       });
       total = parseFloat(total).toFixed(2,2)
       $("#debe_final").val(total);
       return total;
   }

   function addvalue2(){
    
        var total = 0;
        var contador=0;
        $(".haber").each(function() {
            if($(this).val().length>0){
            
                total = parseFloat(total) + parseFloat($(this).val());
             
             contador++;
            }
        });
        var cheque= parseFloat($('#valor_cheque').val());
        if(isNaN(cheque)){
            cheque=0;
        }
        var tos= parseFloat(total)+cheque;
        $("#haber_final").val(tos.toFixed(2,2));
        return total;
    }

    $('#busqueda').click(function(event){

        id= document.getElementById('contador').value;
        var midiv = document.createElement("tr");
        midiv.setAttribute("id","dato"+id);

        midiv.innerHTML = '<td><select class="form-control select2_cuentas" name="codigo'+id+'" id="codigo'+id+'" style="width: 100%;" required> <option value="">Seleccione...</option> @foreach($cuentas as $value)  <option value="{{$value->id}}"> {{$value->plan}} - {{$value->nombre}}</option>@endforeach </select> <input  type="hidden" id="visibilidad'+id+'" name="visibilidad'+id+'" value="1"></td><td><div> <select class="form-control" style=" width: 85%; height: 80%;" name="divisas" id="divisas" > <option> </option> @foreach($divisas as $value) <option value="{{$value->id}}">{{$value->descripcion}}</option> @endforeach  </select></div></td><td><div><input class="form-control debe" style=" width: 88%; height: 80%;"   type="text" name="debe'+id+'" id="debe'+id+'" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);addvalue();" value="0.00"   ></div></td><td><div><input class="form-control haber" style=" width: 88%; height: 80%" id="haber'+id+'" name="haber'+id+'"  onblur="this.value=parseFloat(this.value).toFixed(2);addvalue2();"  value="0.00"  ></div></td> <td><div> <input style=" width: 79%; height: 80%" class="form-control" id="valor_base'+id+'" name="valor_base'+id+'" value="0.00" readonly onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);addvalue();" ></div></td>  <td><button id="eliminar'+id+'" style="margin-left: 20px;" type="button" onclick="javascript:eliminar_registro('+id+')" class="btn btn-danger btn-gray btn-xs delete"> <i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';
        document.getElementById('dt_recibido').appendChild(midiv);
        id = parseInt(id);
        envalor_base(id);
        envalor_base2(id);
        id = id+1;
        
        document.getElementById('contador').value = id;
        $("#visibilidad"+id).val("1");
        $('.nombres').autocomplete({
            source: function( request, response ) {
            $.ajax( {
            url: "{{route('fact_contable_nombre')}}",
            dataType: "json",
            data: {
                term: request.term  
            },
            success: function( data ) {
                response(data);
            }
            } );
            },
            selectFirst: true,
            minLength: 3,
        } );
        $('.codigo').autocomplete({
            source: function( request, response ) {
                $.ajax( {
                url: "{{route('fact_contable_codigo')}}",
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function( data ) {
                    response(data);
                }
                } );
            },
            selectFirst: true,
            minLength: 1,
        } );
        $('.select2_cuentas').select2({
                tags: false
            });
    });

    
    function agregar_valor(id){
        var valor_cheque= parseFloat($("#valor_cheque").val());  
        var validacion= addvalue();
        var validacion2= addvalue2();
        if(validacion>valor_cheque){
            if(validacion2>0){
                
            }else{
                swal("¡Error!","El valor supera al monto del cheque","error");
                $("#debe"+id).val("0.00");
                return 'error';
            }

        }else{
            
        }
       

    }
    function agregar_valor2(id){
        var valor_cheque= parseFloat($("#valor_cheque").val());  
        var validacion= addvalue2();
        if(validacion>valor_cheque){
            swal("¡Error!","El valor supera al monto del cheque","error");
            $("#debe"+id).val("0.00");
            return 'error';
        }else{
            
        }
    }
    
    function cambiar_codigo(id){
        $.ajax({
            type: 'post',   
            url:"{{route('fact_contable_codigo2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'codigo':$("#codigo"+id).val()},
            success: function(data){
                
                $('#nombre'+id).val(data);
             
            },
            error: function(data){
                console.log(data);
            }
        })
    }

    function eliminar_registro(valor)
    { 
        var dato1 = "dato"+valor;
        var nombre2 = 'visibilidad'+valor;
        document.getElementById(dato1).style.display='none';
        document.getElementById(nombre2).value = 0;
        $(this).parent().parent().remove(); //posibility with mode protocal
        console.log("eliminando ...");
        console.log("eliminando ..");
    }
    function envalor_base(id){
        var debe= parseFloat($("#debe"+id).val());
        var validacion= agregar_valor(id);
        if(!isNaN(debe)){
            if(validacion!='error'){
                $("#valor_base"+id).val(debe.toFixed(2,2));
            }else{
                $("#valor_base"+id).val('0.00');
            }

        }else{
            $("#valor_base"+id).val('0.00');
        }

    }
    function envalor_base2(id){
        var debe= parseFloat($("#haber"+id).val());
        var validacion= agregar_valor2(id);
        if(!isNaN(debe)){
            if(validacion!='error'){
                $("#valor_base"+id).val(debe.toFixed(2,2));
            }else{
                $("#valor_base"+id).val('0.00');
            }

        }else{
            $("#valor_base"+id).val('0.00');
        }

    }
      
    

</script>

@endsection
