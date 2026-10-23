@extends('contable.comp_egreso.base')
@section('action-content')
<style type="text/css">
       .ui-autocomplete
        {
            overflow-x: hidden;
            max-height: 400px;
            width:1px;
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            float: left;
            display: none;
            min-width: 160px;
            width: 460px;
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
        .alerta_correcto{
            position: absolute;
            z-index: 9999;
            top: 100px;
            right: 10px;
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
        .cabecera{
            background-color: #9E9E9E;
            border-radius: 2px;
        }
        .label_header2{
        background-color: #a8a9ad;
        width: 100%;
        height: 25px;
        margin: 0 auto;
        line-height: 25px;
        color: #FFF;
        text-align: center;
    }
    .px-0{
        padding-left:0 !important;
        padding-right:0 !important;
    }
    .px-1{
        padding-left:1px !important;
        padding-right:1px !important;
        margin-bottom:2px;
    }

</style>
<div class="modal fade" id="visualizar_estado" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<script type="text/javascript">
    function goBack() {
       location.href="{{route('egresosv.index')}}";
    }
    
</script>
<section class="content">
<div id="alerta_datos" class="alert alert-success alerta_correcto alert-dismissable" role="alert" style="display:none;">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
  Guardado Correctamente 
</div>   
    <form class="form-vertical" action="{{route('egresoacreedor.egresov_update_observacion',['id'=>$varios->id])}}" method="post" id="form_guardado">
        {{ csrf_field() }}
        <div class="box">
                    <div class="box-header header_new">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-7 col-sm-7 col-4">
                                    <div class="box-title " ><b>COMP. DE EGRESOS VARIOS ACREEDORES</b></div>
                                </div>
                                <div class="col-md-5">
                                    <div class="row">
                                        <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',[$varios->id_asiento_cabecera])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
                                            <i class="glyphicon glyphicon-eye-open"  aria-hidden="true"></i>&nbsp;&nbsp;Visualizar Asiento diaro
                                        </a>
                                        <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="{{route('librodiario.edit',['id'=>$varios->id_asiento_cabecera])}}" target="_blank">
                                            <i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;&nbsp;Editar Asiento diario
                                        </a>
                                        <button type="button" class="btn btn-success btn-xs btn-gray" onclick="goBack()" style="margin-left: 3px;padding: 7px 20px;">
                                            <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
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
                                        <label class="label_header">Estado</label>
                                        <div style=" @if(($varios->estado)==1) background-color: green; @else background-color: red; @endif " class="form-control "></div>           
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="col-md-12 label_header" for="id_factura">ID:</label>
                                            <input class="form-control " type="text" name="id_factura" value="@if(!is_null($varios)) {{$varios->id}} @endif" id="id_factura" readonly>    
                                        
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="label_header" for="numero_factura">Número</label>
                                            <input class="form-control " type="text" id="numero_factura" value="@if(!is_null($varios)) {{$varios->secuencia}} @endif" name="numero_factura" readonly>
                                        
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                        <label class="col-md-12 label_header" for="tipo">Tipo:</label>
                                        <input class="form-control " type="text" name="tipo" id="tipo" value="BAN-EG" readonly>    
                                    
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="label_header" for="asiento">Asiento</label>
                                            <input class="form-control " type="text" id="asiento" name="asiento" value="@if(!is_null($varios)) {{$varios->id_asiento_cabecera}} @endif" readonly>
                                        
                                    
                                    </div>
                                    
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="col-md-12 label_header" for="fecha_hoy">Fecha: </label>
                                            <input disabled class="form-control " type="date" name="fecha_hoy" id="fecha_hoy" value="@if(!is_null($varios)){{$varios->fecha_comprobante}}@endif">
                                        
                                    </div>
                                </div>
                                <div class="form-row " id="no_visible">
                                    <div class=" col-md-10 px-1">
                                            <label class="label_header" for="acreedor">Concepto:</label>
                                            <input class="form-control  col-md-12" type="text" name="concepto" autocomplete="off" value="@if(!is_null($varios)) {{$varios->descripcion}} @endif" id="concepto">
                                    </div>
                                    <div class=" col-md-2 px-1 visibilidad">
                                        
                                        <label class="container label_header ">Cheque entregado
                                            <input disabled type="checkbox" id="cheque_entregado" name="cheque_entregado" @if(!is_null($varios)) @if($varios->check==1) checked @endif @endif>
                                            <span class="checkmark"></span>

                                        </label>
                                    </div>
                                    @php
                                        $bancos = Sis_medico\Ct_Caja_Banco::where('id', $varios->id_caja_banco)->first();
                                    @endphp
                                    <div class=" col-md-4  px-0">
                                            <label class="col-md-12 label_header" for="ruc">Banco:</label>
                                            <select disabled class="form-control " name="banco" id="banco">
                                            <option value="0">@if(!is_null($bancos)) {{$bancos->nombre}} @endif</option>
                                            </select>
                                    </div>
                                    <div class=" col-md-2 visibilidad px-0">
                                            <label class="col-md-12 label_header" for="secuencia">Divisas:</label>
                                            <input type="text" class="form-control" value="DOLARES" readonly>
                                    </div>
                                    <div class=" col-md-2 visibilidad px-1">
                                            <label class="col-md-12 label_header" for="secuencia">Cambio:</label>
                                            <input class="form-control " type="text" name="secuencia" id="secuencia" value="1.00" readonly>
                                    </div>
                                    <div class=" col-md-2 visibilidad px-1">
                                            <label  class="col-md-12 label_header control-label" for="numero_cheque">Cheque No:</label>
                                            <input class="form-control " type="text" name="numero_cheque" id="numero_cheque" value="@if(!is_null($varios)) {{$varios->nro_cheque}} @endif"> 
                                    </div>
                                    <div class=" col-md-2 visibilidad px-1">
                                            <label class="col-md-12 label_header" for="fecha_cheque">Fecha Cheque: </label>
                                            <input class="form-control " type="date" name="fecha_cheque" id="fecha_cheque" value="@if(!is_null($varios)){{$varios->fecha_cheque}}@endif" disabled>
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        <label for="valor" class="label_header" >Valor</label>
                                        <input disabled class="form-control " type="text" name="valor_cheque" id="valor_cheque" onblur="setNumber(this.value)" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="@if(!is_null($varios)) {{$varios->valor}} @endif">
                                    
                                    </div>
                                    <div class=" col-md-3 px-1">
                                            {{ csrf_field() }} 
                                            <label class="col-md-12 label_header" for="acreedor">Beneficiario: </label>
                                            <input disabled type="text" id = "beneficiario" name="beneficiario" class= "form-control form-control-sm " value="@if(!is_null($varios)) {{$varios->beneficiario}} @endif" autocomplete="off">
                                    </div>
                                    <div class=" col-md-3 visibilidad px-1">
                                            <label class="col-md-12 label_header" for="girado"> Girado a:</label>
                                            <input disabled class="form-control " type="text" name="giradoa" id="giradoa" value="@if(!is_null($varios)) {{$varios->beneficiario}} @endif" autocomplete="off">
                                    </div>
                                    <div class="col-md-12">
                                        &nbsp;
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="detalle_deuda" class="control-label label_header">DETALLE DEL COMPLEMENTO CONTABLE</label>
                            
                                <input type="hidden" name="id_compra" id="id_compra">
                             
                                <div class="table-responsive col-md-12 px-1">               
                                    <table id="example2" style="width: 100%;" role="grid" aria-describedby="example2_info">
                                        <thead style="background-color: #9E9E9E; color: white;" >
                                        <tr style="position: relative;">
                                            <th style="text-align: center;">Código</th>
                                            <th style=" text-align: center;">Debe</th>
                                            <th style="text-align: center;">Haber</th>
                                            <th style="text-align: center;">Valor Base</th>
                                            <!-- <th style=" text-align: center;">
                                                <button id="busqueda" type="button" class="btn btn-success btn-gray btn-xs" >
                                                    <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                                </button>
                                            </th> -->
                                            
                                        </tr>
                                        </thead>
                                        <tbody id="dt_recibido">
                                        @php $contador=0; @endphp      
                                            @foreach($detalle_egreso as $value)
                                                <tr>
                                                    <td> <select disabled name="codigo{{$contador}}" id="codigo{{$contador}}" class="form-control select2_cuentas" style="width:100%"> 
                                                     <option value="">Seleccione</option>
                                                     @foreach($cuentas as $values)
                                                        <option @if($values->id==$value->codigo) selected="selected" @endif value="{{$values->id}}"> {{$values->plan}} - {{$values->nombre}}</option>
                                                     @endforeach
                                                    </select> <input type="hidden" name="visibilidad{{$contador}}" id="visibilidad{{$contador}}" value="1"> </td>
                                                    <td style="text-align: center;"> <input disabled style="width:100%" type="text" name="debe{{$contador}}" id="debe{{$contador}}" class="form-control" value="@if(!is_null($value->debe)) {{$value->debe}} @endif" onchange="envalor_base({{$contador}})"> </td>
                                                    <td style="text-align: center;"> 
                                                        <input disabled style="width:100%" class="form-control" type="text" id="haber{{$contador}}" name="haber{{$contador}}" value="0.00">
                                                    </td>
                                                    <td style="text-align: center;"><input disabled style=" width: 79%; height: 80%" class="form-control" id="valor_base{{$contador}}" name="valor_base{{$contador}}" value="0.00" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);addvalue();"></td>
                                                    <!-- <td> <button id="eliminar{{$contador}}" style="margin-left: 20px;" type="button" onclick="javascript:eliminar_registro(this)" class="btn btn-danger btn-gray btn-xs delete"> <i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button> </td> -->
                                                </tr>
                                                @php 
                                                    $contador++;
                                                @endphp
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            
                                        </tfoot>
                                    </table>
                                </div>
                                <input type="hidden" name="contador" id="contador" value="{{$contador}}">
                                <div class="col-md-12 px-1">
                                    <label class="label_header">Nota: </label>
                                    <textarea class="form-control" name="nota" id="nota" cols="5" rows="5">@if(!is_null($varios)) {{$varios->comentarios}} @endif</textarea>
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
                            
                            <!-- <div class="col-md-12" style="text-align: center;">
                                <button type="submit" class="btn btn-success btn-gray"> <i class="fa fa-save"></i> &nbsp; Actualizar </button>
                            </div> -->
                            <div class="col-md-12" style="text-align: center;">
                                <button type="submit" class="btn btn-success btn-gray"> <i class="fa fa-save"></i> &nbsp; Actualizar </button>
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
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script type="text/javascript">
   
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
    function boton_deuda(){
        //swal("hassta aqui");
        var formulario = document.forms["form_guardado"];
        var valor_cheque= formulario.valor_cheque.value;
        var numero_cheque= formulario.numero_cheque.value;
        var contador= formulario.contador.value;
        var beneficiario= formulario.beneficiario.value;
        var direccion = formulario.direccion.value;
        var banco = formulario.banco.value;
        var divisas= formulario.divisasa.value;
        var concepto= formulario.concepto.value;
        var msj = "";
        if(valor_cheque==""){
            msj+="Por favor, Llene el valor del cheque<br/>";
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
        
        var vence= $("#vence0").val();
        var tipo= $("#tipo0").val();
        var numero= $("#numero0").val();
        var final_valor_cheque= $("#valor_cheque").val();
        var validacion= addvalue();
        var concepto= $("#concepto0").val();
        var saldo_final= $("#saldo_base0").val();
        if(msj==""){
            if(validacion>final_valor_cheque){
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
                                swal("Correcto!","Se creo el comprobante de egresos varios","success");             
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
            swal("¡Correcto!","Guardado Correctamente","success");
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
            if($("#visibilidad"+contador)==1){
                total = parseFloat(total) + parseFloat($(this).val());
             }
             contador++;
           }
       });
       total = parseFloat(total).toFixed(2)
       $("#valor_base").val(total);
       return total;
   }

   function addvalue2(){
    
        var total = 0;
        var contador=0;
        $(".haber").each(function() {
            if($(this).val().length>0){
             if($("#visibilidad"+contador)==1){
                total = parseFloat(total) + parseFloat($(this).val());
             }

             contador++;
            }
        });
        total = parseFloat(total).toFixed(2)
        $("#valor_base").val(total);
        return total;
    }
    
    $('#busqueda').click(function(event){

        id= document.getElementById('contador').value;
        var midiv = document.createElement("tr");
        midiv.setAttribute("id","dato"+id);

        midiv.innerHTML = '<td><select class="form-control select2_cuentas" name="codigo'+id+'" id="codigo'+id+'" style="width: 100%;" required> <option value="">Seleccione...</option> @foreach($cuentas as $value)  <option value="{{$value->id}}"> {{$value->id}} - {{$value->nombre}}</option>@endforeach </select> <input  type="hidden" id="visibilidad'+id+'" name="visibilidad'+id+'" value="1"></td><td><div><input class="form-control debe" style=" width: 88%; height: 80%;"   type="text" name="debe'+id+'" id="debe'+id+'" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);addvalue();" onchange="envalor_base('+id+');" value="0.00"   ></div></td><td><div><input class="form-control haber" style=" width: 88%; height: 80%" id="haber'+id+'" name="haber'+id+'"  onblur="this.value=parseFloat(this.value).toFixed(2);addvalue2();"  value="0.00" onchange="envalor_base2('+id+');"  ></div></td> <td><div> <input style=" width: 79%; height: 80%" class="form-control" id="valor_base'+id+'" name="valor_base'+id+'" value="0.00" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);addvalue();" ></div></td>  <td><button id="eliminar'+id+'" style="margin-left: 20px;" type="button" onclick="javascript:eliminar_registro(this)" class="btn btn-danger btn-gray btn-xs delete"> <i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';
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
        if(validacion>valor_cheque){
            swal("¡Error!","El valor supera al monto del cheque","error");
            $("#debe"+id).val("0.00");
            return 'error';
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
       //document.getElementById(dato1).style.display='none';
        //document.getElementById(nombre2).value = 0;
        $(valor).parent().parent().remove(); //posibility with mode protocal
        console.log("eliminando ...");
        id= document.getElementById('contador').value;
        id=id-1;
        if(id<0){
            id=0;
        }
        document.getElementById('contador').value=id;
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
