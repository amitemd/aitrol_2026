@extends('contable.credito_acreedores.base')
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
        location.href="{{ route('creditoacreedores.index') }}";
    }
</script>
<input type="hidden" id="fechita" value="{{$date}}">
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('creditoacreedores.index') }}">{{trans('contableM.notacredito')}} {{trans('contableM.acreedor')}}</a></li>
        <li class="breadcrumb-item active" aria-current="page"{{trans('contableM.nuevo')}} {{trans('contableM.notacredito')}}</li>
      </ol>
    </nav> 
<form class="form-vertical " method="post" id="form_guardado">
            {{ csrf_field() }}
            
            <input  name="id_comp" id="id_comp" type="text" class="hidden" value="">
           
            <div class="box">
                    <div class="box-header header_new">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-3 col-sm-9 col-6">
                                    <div class="box-title " ><b>{{trans('contableM.notacredito')}} {{trans('contableM.acreedor')}}</b></div>
                                </div>
                                  <div class="col-md-6 text-left">
                                    <span class="parpadea text" id="boton" >{{$h}}</span>
                                </div>
                                <div class="col-md-3">
                                    <div class="row">
                                        <a type="button" id="boton_guardar" href="javascript:boton_deuda()" class="btn btn-success btn-gray btn-xs"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
                                        </a>
                                        <button type="button" class="btn btn-success btn-xs btn-gray" onclick="nuevo_comprobante()" style="margin-left: 10px;">
                                            <i class="glyphicon glyphicon-file" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.nuevo')}}
                                        </button>
                                        <button type="button" class="btn btn-success btn-xs btn-gray" onclick="goBack()" style="margin-left: 10px;">
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
                                    
                                    <div class=" col-md-1 px-1" >
                                        <label class="label_header">{{trans('proforma.estado')}}</label>
                                        <div style="background-color: green; " class="form-control col-md-1"></div>           
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="col-md-12 label_header" for="id_factura">{{trans('contableM.id')}}</label>
                                            <input class="form-control " type="text" name="id_factura" id="id_factura" readonly>    
                                        
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="label_header" for="numero_factura">{{trans('contableM.numero')}}</label>
                                            <input class="form-control " type="text" id="numero_factura" name="numero_factura" readonly>
                                        
                                    </div>
                                    <div class=" col-md-1 px-1">
                                        
                                        <label class="col-md-12 label_header" for="tipo">{{trans('contableM.tipo')}}:</label>
                                        <input class="form-control " type="text" name="tipo" id="tipo" value="ACR-DB" readonly>    
                                    
                                    </div>
                                    <div class=" col-md-2 px-1">
                                     
                                            <label class="label_header" for="asiento">{{trans('contableM.asiento')}}</label>
                                            <input class="form-control " type="text" id="asiento" name="asiento" readonly>
                                            @if(!is_null($iva_param))

                                                <input type="text" name="iva_par" id="iva_par" class="hidden" value="{{$iva_param->iva}}">
                                            @endif
                                     
                                    </div>
                                    
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="col-md-12 label_header" for="fecha_hoy">{{trans('contableM.fecha')}}: </label>
                                            <input class="form-control " type="date" name="fecha_hoy" id="fecha_hoy" value="{{date('Y-m-d')}}">
                                        
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                        <label class="col-md-12 label_header" for="fecha_caducidad">{{trans('contableM.fechacaducidad')}}: </label>
                                        <input class="form-control " type="date" name="fecha_caducidad" id="fecha_caducidad" value="{{date('Y-m-d')}}">
                                    
                                    </div>
                                </div>
                                
                                    <div class=" col-md-2 px-1">
                                            {{ csrf_field() }} 
                                            <input type="hidden" name="superavit" id="superavit" value="0">
                                            <label class="col-md-12 label_header" for="acreedor">{{trans('contableM.acreedor')}} </label>
                                            <input type="hidden" name="id_proveedor" id="id_proveedor">
                                            <input type="text" id = "nombre_proveedor" name="nombre_proveedor" class= "form-control form-control-sm nombre_proveedor " onchange="cambiar_nombre_proveedor()" >
                                    </div>
                                    <div class="col-md-2 px-1">
                                        <label class="control-label label_header" for="autorizacion">{{trans('contableM.autorizacion')}} </label>
                                        <input type="text" name="autorizacion" id="autorizacion" class="form-control">
                                    </div>
                                    <div class="col-md-2 px-1">
                                        <label class="col-md-12 label_header control-label" for="serie">{{trans('contableM.serie')}}:NC</label>
                                        <input type="text" class="form-control" id="serie" name="serie"  onkeyup="agregar_serie()">
                                    </div>
                                    <div class="col-md-2 px-1">
                                        <label for="secuencia" class="label_header col-md-12 control-label">{{trans('contableM.secuencia')}} NC</label>
                                        <input type="text" class="form-control" id="secuencia" name="secuencia" onchange="ingresar_cero();">
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        <label class="col-md-12 label_header" for="fechand">Fecha N/D: </label>
                                        <input class="form-control " type="date" name="fechand" id="fechand" value="{{date('Y-m-d')}}">
                                    </div>
                                    <div class="col-md-2 col-xs-2 px-1" >
                                        <label class="control-label label_header">{{trans('contableM.creditotributario')}}</label>
                                        <select name="credito_tributario" id="cred_tributario" class="form-control  select2_cuentas " style="width: 100%; ">
                                            <option value="">{{trans('proforma.seleccion')}}...</option>

                                            @foreach($c_tributario as $value)
                                                <option value="{{$value->codigo}}">{{$value->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-4 px-1">
                                            <label class="label_header" for="concepto">{{trans('contableM.concepto')}}:</label>
                                            <input class="form-control  col-md-12" type="text" name="concepto" id="concepto" >
                                    </div>
                                    <div class=" col-md-2 px-1">
                                      <label class="col-md-12 label_header" for="nro_factura">{{trans('contableM.valorcontable')}}: </label>
                                      <input class="form-control " type="text" name="val_contable" id="val_contable" autocomplete="off">
                                    </div>

                                    <div class=" col-md-4 px-1">
                                      <label class="col-md-12 label_header" for="nro_factura"># {{trans('contableM.factura')}} </label>
                                    
                                      <select class="form-control select2" name="nro_factura" onchange="traetodo(this)" id="nro_factura"> 
                                         <option value="">Seleccione</option>
                                         @foreach($compraid as $compra)

                                            <option data-id="{{$compra->id}}" data-serie="{{$compra->serie}}" data-secuencia="{{$compra->secuencia_f}}" data-id_proveedor="{{$compra->proveedor}}" data-nombre_proveedor="{{$compra->proveedorf->nombrecomercial}}" data-valor_contable="{{$compra->valor_contable}}" data-autorizacion="{{$compra->autorizacion}}"  value="{{$compra->id}}">{{$compra->id}} -- {{$compra->observacion}} | {{$compra->numero}}</option>

                                         @endforeach

                                      </select>
                                    </div>
                                    
                                    <div class="col-md-2 col-xs-2 px-1" >
                                        <label class="control-label label_header">{{trans('contableM.tipocomprobante')}}</label>
                                        <select name="tipo_comprobante" id="tipo_comprobante" class="form-control  select2_cuentas " style="width: 100%;">
                                            <option value="">{{trans('proforma.seleccion')}}...</option>

                                            @foreach($t_comprobante as $value)
                                           
                                                <option value="{{$value->codigo}}">{{$value->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>                                    
                                
                            </div>
                            <div class="col-md-12">
                            <label class="label_header" for="detalle_deuda">DETALLE DE RUBROS</label>
                            </div>
                            <div class="col-md-12">
                                <input type="hidden" name="id_compra" id="id_compra">
                                <input type="hidden" name="contador" id="contador" value="0">
                                <div class="table-responsive col-md-12 px-1">               
                                    <table id="example3" style="width: 100%;" role="grid" aria-describedby="example2_info">
                                        <thead style="background-color: #9E9E9E; color: white;" >
                                        <tr style="position: relative;">
                                            <th style="width: 8%; text-align: center;">Rubro</th>
                                            <th style="width: 20%; text-align: center;">Detalle</th>
                                            <th style="width: 10%; text-align: center;">Divisa</th>
                                            <th style="width: 10%; text-align: center;">{{trans('contableM.valor')}}</th>
                                            <th style="width: 6%; text-align: center;"{{trans('contableM.total')}}Base</th>
                                            <th style="width: 10%; text-align: right;">
                                                <button onclick="crea_td()" type="button" class="btn btn-success btn-gray btn-xs" >
                                                    <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                                </button>
                                             </th>
                                        </tr>
                                        </thead>
                                        <tbody id="det_recibido">
                                        </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <div class="row sumas">
                                        <div class="col-md-2">
                                            &nbsp;
                                        </div>
                                        <!--aqui ando-->
                                        <div class="col-md-2 px-1">
                                            <label class="label_header" for="subtotal">{{trans('contableM.subtotal0')}}</label>
                                            <input onchange="recalcular()" class="form-control  col-md-12" type="text" name="subtotal0" id="subtotal0" >
                                        </div>
                                        <div class="col-md-2 px-1">
                                            <label class="label_header" for="subtotal">{{trans('contableM.subtotal12')}}</label>
                                            <input onchange="recalcular()" class="form-control col-md-12" type="text" name="subtotal12" id="subtotal12" >
                                        </div>
                                        <div class="col-md-2 px-1">
                                            <label class="label_header" for="subtotal">{{trans('contableM.subtotal')}}</label>
                                            <input onchange="recalcular()" class="form-control  col-md-12" type="text" name="subtotal" id="subtotal" >
                                        </div>
                                        <div class="col-md-2 px-1">
                                            <label class="label_header" for="impuesto">{{trans('contableM.impuesto')}}</label>
                                            <input onchange="recalcular()" class="form-control  col-md-12" type="text" onchange="sumar_impuesto()" name="impuesto" id="impuesto" >
                                        </div>
                                        <div class="col-md-2 px-1">
                                            <label class="label_header" for="total">{{trans('contableM.total')}}</label>
                                            <input class="form-control  col-md-12" type="text" name="total" id="total" >
                                        </div>
                                    </div>
                                </div>


                        </div>                     
                        <div class="col-md-12" style="margin-top: 30px;">
                            <div class="input-group">
                                <label class="col-md-12 cabecera" style="color: black;" for="nota">{{trans('contableM.nota')}}: </label>
                                <textarea class="col-md-12 " name="nota" id="nota" cols="200" rows="5"></textarea>
                                <input type="hidden" name="saldo_final" id="saldo_final">
                            </div>
                        </div>

                        </div>

            </div>
    </form>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>



<script type="text/javascript">
        $('.select2').select2({
            tags: false
        });
    function buscar_factura(){
        $("#buscar").next().remove();
        $.ajax({
            type: 'post',
            url:"{{route('retenciones_buscar_codigo')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'id_factura':$("#buscar").val()},
            success: function(data){
                console.log(data);
                $("#id_factura").val(data[0]);
                $("#numero_factura").val(data[1]);
                $("#concepto").val(data[4]+'.'+' '+'REF :'+data[0]);
                $("#asiento").val(data[1]);
                $("#acreedor").val(data[0]+' '+data[2]);
                $("#direccion").val(data[3]);
                $("#total_factura").val(data[10]);
                $("#nuevo_saldo0").val(data[10]);
                $("#total_deudas").val(data[10]);
                $("#id_compra").val(data[14]);
                for(i=0;i<data[6].length; i++){
                    $("#vence"+i).val(data[6][i].fecha);
                    $("#tipo"+i).val(data[6][i].id_tipoproveedor);
                    $("#numero_referencia"+i).val((data[6][i].serie)+'-'+data[1]);
                    $("#base_fuente"+i).val(data[6][i].valor);
                    $("#divisas"+i).val(data[6][i].divisas); 
                    $("#numero"+i).val(data[6][i].id);
                    $("#concepto"+i).val((data[6][i].id)+'-'+data[0]); 
                    $("#saldo"+i).val((data[6][i].valor));
                    $("#tipo_rfiva"+i).val((data[6][i].id_porcentaje_iva));
                    $("#tipo_rfir"+i).val((data[6][i].id_porcentaje_ft));
                    var iva_base= parseFloat(data[6][i].valor);
                    var total_iva= iva_base*12/100;
                    $("#base_iva"+i).val(total_iva);          
                }
                
                
            },
            error: function(data){
                console.log(data);
            }
        })
    }
    function sumar_impuesto(){
        var subtotal= parseFloat($("#subtotal").val());
        if( isNaN(subtotal)){
            subtotal=0;
        }
        var subtotal12= parseFloat($("#subtotal12").val());
        if( isNaN(subtotal12)){
            subtotal12=0;
        }

        var subtotal0= parseFloat($("#subtotal0").val());
        if( isNaN(subtotal0)){
            subtotal0=0;
        }
        var impuesto= parseFloat($("#impuesto").val());
        if(isNaN(impuesto)){
            impuesto=0;
        }

        subtotal.val(parseFloat(subtotal0 + subtotal12).toFixed(2));


        $("#impuesto").val(impuesto.toFixed(2,2));
        
       
        var s= subtotal+impuesto;
        $("#total").val(s.toFixed(2,2));      

    }
    function buscarAsiento(id_asiento){
        $.ajax({
            type: 'get',
            url: "{{route('buscar_asiento.diario')}}",
            datatype: 'json',
            data: {
                'id_asiento': id_asiento,
                'validacion': '4'
            },
            success: function(data) {

                if (data.value != 'No se encontraron resultados') {
                    $('#asiento').val(data[0]);
                    $('#numero').val(data[1]);
                }


            },
            error: function(data) {
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
    function set_rubros(id){
        $.ajax({
            type: 'post',
            url:"{{route('rubrosa.nombre')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'codigo': id.value},
            success: function(data){
                if(data.value!='no'){    
                    $("#id_codigo"+id).val(data[0]);
                }
            },
            error: function(data){
                console.log(data);
            }
        })
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
                console.log(data);
            }
            } );
        },
        minLength: 1,
    } );
    function ingresar_cero(){
      var secuencia_factura= $('#secuencia').val();
      var digitos= 9;
      var ceros=0;
      var varos='0';
      var secuencia=0;
       if(secuencia_factura>0){
           var longitud= parseInt(secuencia_factura.length);
           if(longitud>10){
               swal("Error!","Valor no permitido","error");
               $('#secuencia').val(''); 

           }else{
               
               var concadenate= parseInt(digitos-longitud);
                switch(longitud){
                    case 1:
                        secuencia='000000000';
                        break;
                    case 2:
                        secuencia= '00000000';
                        break;
                    case 3:
                        secuencia= '0000000';
                        break;
                    case 4:
                        secuencia= '000000';
                        break;
                    case 5:
                        secuencia='00000';
                        break;
                    case 6:
                        secuencia='0000';
                        break;
                    case 7:
                        secuencia='000';
                        break;
                    case 8:
                        secuencia='00';
                        break;
                    case 9:
                        secuencia='0';
                }
                $('#secuencia').val(secuencia+secuencia_factura);
           }
           
            
       }else{
           swal("Error!","Valor no permitido","error");
           $('#secuencia').val('');
       }      
    }
    function agregar_serie(){
        var serie= $('#serie').val(); 
            if((serie.length)==3){
                $('#serie').val(serie+'-');
            }else if((serie.length)>7){
                $('#serie').val('');
                alert("Error!","Ingrese la serie de la factura correctamente","error");
            }
    }
    function lista_valores(id){

        var variable_select= $("#tipo_rfir"+id).val();
        var variable= parseFloat($("#total_factura").val());
        //alert(valor);
        $.ajax({
            type: 'post',
            url:"{{route('retenciones_query')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'opcion': variable_select},
            success: function(data){
                //alert(data[0].nombre);
               console.log(data);
                if(data.value!='no'){
                   
                    $("#total_rfir"+id).val(data[0].valor);
                    $("#retencion_impuesto").val(data[0].valor);
                    var totales= parseFloat($("#numero_factura").val());
                    var final= totales*0.12;
                    total_abono()
                    $("#base_iva"+id).val();
                    /*if(final!= NaN){
                        $("#base_iva"+id).val(final.toFixed(2));
                    }*/                 
                                        
                }
            },
            error: function(data){
                console.log(data);
            }
        })
    }
    function lista_valores2(id){

        var variable_select= $("#tipo_rfiva"+id).val();
        var variable= parseFloat($("#total_factura").val());
    //alert(valor);
        $.ajax({
            type: 'post',
            url:"{{route('retenciones_query2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'opcion': variable_select},
            success: function(data){
                //alert(data[0].nombre);
             console.log(data);
                if(data.value!='no'){
                    
                    $("#total_rfiva"+id).val(data[0].valor);     
                    $("#retencion_iva").val(data[0].valor);
                    total_abono()                                        
                }
            },
            error: function(data){
                console.log(data);
            }
        })
    }
    function total_abono(){
        var variable1= parseFloat($("#retencion_impuesto").val())
        var variable2= parseFloat($("#retencion_iva").val())
        var totales= variable1+variable2;
        var vad= parseFloat($("#total_factura").val());
        var total_da= vad-totales;
        if(totales!=NaN){
            $("#abono0").val(totales);
            $("#abono_base0").val(totales);
            $("#total_egreso").val(totales);
            $("#nuevo_saldo").val(total_da);
        }
        
        
    }
    function nuevo_comprobante(){
        location.href ="{{route('creditoacreedores.create')}}";
    }
    function valor_rubro(id){
        var e= parseFloat($("#valor"+id).val());
        
        var coniva=0;
        var total=0;
        if(e==""){
           e=0;
        }
        if(isNaN(e)){
            e=0;
        }
        $("#valor"+id).val(parseFloat(e).toFixed(2,2));
        $("#total_base"+id).val(parseFloat(e).toFixed(2,2));
        $("#subtotal0").val(parseFloat(e).toFixed(2,2));
        $("#subtotal").val(parseFloat(e).toFixed(2,2));      
        $("#subtotal12").val(parseFloat(0.00).toFixed(2,2));      
        
        sumar();
    }
    function sumar(){
        var contador  =  0;
        var iva= parseFloat($("#iva_par").val());
        var ivan=0;
        var total=0;
        var totaal=0;
        var sub=0;
        var valor_d=0;
        var ivaf=0;
        let sub0 =0;
        let sub12=0;
        $("#det_recibido tr").each(function(){
            $(this).find('td')[0];
            visibilidad = $(this).find('#visibilidad'+contador).val();
            if(visibilidad == 1){
                valor = parseFloat($(this).find('#valor'+contador).val());
                console.log(valor);


                ivan = ivan + valor;

                sub0 = sub + valor;

                //sub12= 
                
                sub = sub + valor;
                totaal= valor+ivan;
                total= total+totaal;


            }
            contador = contador+1;
        });
            /*var totalsx= ivan*iva;*/
            var totalsx=0;
            var total_final= totalsx+ivan;
            if(!isNaN(ivan)){ $('#impuesto').val(totalsx.toFixed(2));   }
            if(!isNaN(sub)){ $('#subtotal0').val(sub0.toFixed(2));   }
            if(!isNaN(sub)){ $('#subtotal').val(sub.toFixed(2));   }
            if(!isNaN(total)){ $('#total').val(total_final.toFixed(2));   }
    }
    
    
    function crea_td(contador){
        id= document.getElementById('contador').value;
        var midiv = document.createElement("tr")
        
        midiv.setAttribute("id","dato"+id);
        midiv.innerHTML = '<td> <select style="width:100%"  class="form-control select2" name="id_codigo'+id+'" id="id_codigo'+id+'"> <option value="">{{trans('proforma.seleccion')}}...</option> @foreach($rubros as $value) <option value="{{$value->codigo}}">{{$value->nombre}}</option> @endforeach  </select></td><td> <input style="width: 98%;" name="detalle_rubro'+id+'" id="detalle_rubro'+id+'" required></td> <td><input style="width: 98%;" name="divisas" id="divisas" value="USD" readonly ></td> <td> <input class="valortotal" name="valor'+id+'" style="width: 98%;" id="valor'+id+'" onchange="valor_rubro('+id+')" value="0.00" ></td><input class="visibilidad" type="hidden" name="visibilidad'+id+'" id="visibilidad'+id+'" value="1"><td><input style="width: 90%;" name="total_base'+id+'" id="total_base'+id+'" value="0.00" readonly></td><td><button id="eliminar'+id+'" type="button" onclick="javascript:eliminar_registro('+id+')" class="btn btn-danger btn-gray delete btn-xs"> <i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';
        
        /*<input name="rubro'+id+'" id="rubro'+id+'" onchange="set_rubros('+id+')" class="rubrosa" style="width: 98%;" required>
            <input type="hidden" name="id_codigo'+id+'" id="id_codigo'+id+'" required> */
        
        document.getElementById('det_recibido').appendChild(midiv);
        id = parseInt(id);
        id = id+1;
        document.getElementById('contador').value = id;
        $(".rubrosa").autocomplete({
            source: function( request, response ) {
                $.ajax( {
                url: "{{route('rubrosa.searchcode')}}",
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function( data ) {
                    response(data);
                    console.log(data);
                }
                } );
            },
            minLength: 1,
        } );

        $(".codigo").autocomplete({
            source: function( request, response ) {
                $.ajax( {
                url: "{{route('compra_codigo')}}",
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function( data ) {
                    response(data);
                }
                } );
            },
            minLength: 1,
        } );
        $('.select2').select2({
                tags: false
            });

    }
    function eliminar_registro(valor)
    {
        var dato1 = "dato"+valor;
        var nombre2 = "visibilidad"+valor;
        document.getElementById(nombre2).value = 0;
        document.getElementById(dato1).style.display='none';
        sumar();
    }
    function boton_deuda(){
        //swal("hassta aqui");
        var formulario = document.forms["form_guardado"];
        var superavit= parseInt($("#verificar_superavit").val());
        var msj = "";
        var vence= $("#vence0").val();
        var tipo= $("#tipo0").val();
        var numero= $("#numero0").val();
        var final_valor_cheque= parseFloat($("#valor_cheque").val());
        var concepto= $("#concepto0").val();
        var saldo_final= $("#saldo_base0").val();
        var proveedor= formulario.nombre_proveedor.value;
        var autorizacion= formulario.autorizacion.value;
        var serie= formulario.serie.value;
        var secuencia= formulario.secuencia.value;
        var tipo_comprobante= formulario.tipo_comprobante.value;
        var credito_tributario= formulario.credito_tributario.value;
        var fecha= formulario.fecha_hoy.value; 
        var num_fact = formulario.nro_factura.value;   
        let subtotales = document.getElementById("subtotal").value;
        let subtotal12 = document.getElementById("subtotal12").value;
        let impuestos = document.getElementById("impuesto").value;

        if(num_fact==""){
            msj+="Por favor, Ingrese el Numero de Factura <br/>";
        }
        if(proveedor==""){
            msj+="Por favor, Llene el campo proveedor <br/>";
        }
        if(autorizacion==""){
            msj+="Por favor, Llene el campo autorizacion <br/>";
        }
        if(serie==""){
            msj+="Por favor, Llene el campo serie <br/>";
        }
        if(secuencia==""){
            msj+="Por favor, Llene el campo secuencia <br/>";
        }
        if(tipo_comprobante==""){
            msj+="Por favor, Llene el campo tipo_comprobante <br/>";
        }
        if(credito_tributario==""){
            msj+="Por favor, Llene el campo credito tributario <br/>";
        }
        if(fecha==""){
            msj+="Por favor, Llene el campo fecha <br/>";
        }
        
        if(impuestos>0){
            if(subtotal12<=0 || subtotal12 ==""){
                msj+="Por favor, Incorrecto el campo de subtotal 12 <br/>";
            }
        }else{
            if(subtotales < 0 || subtotales ==""){
                msj+="Por favor, Llene el campo de subtotal <br/>";
            }  
        }
        id= document.getElementById('contador').value;
        //alert(id);
        for(i = 0 ; i < id ; i++){
            if( $('#id_codigo'+i).val()==""){
                msj="Falta seleccionar Rubro";
            }    
        }

        if(msj==""){
            if($("#form_guardado").valid()){
                $("#boton_guardar").css("display", "none");
                $.ajax({
                    type: 'post',
                    url:"{{route('creditoacreedores.store')}}",
                    headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                    datatype: 'json',
                    data: $('#form_guardado').serialize(),
                    success: function(data){
                        console.log(data);
                        if((data)!='false'){
                            $("#id_factura").val(data);
                            buscarAsiento(data);
                            $('#form_guardado input').attr('readonly', 'readonly');
                        
                            swal("Correcto!","{{trans('contableM.notacredito')}} generada con exito","success");
                        }else{
                            swal("Mensaje:",data,"info")
                        }                    
                    },
                    error: function(data){
                        swal("Error!",data,"error");
                    }
                })
            }
          
        }else{
            swal({
                  title: "Error!",
                  type: "error",
                  html: msj
                });
        }

    }

    function crear_retenciones(){
        var buscar= $("#buscar").val();

        if(buscar!=0){
            
            $.ajax({
                type: 'post',
                url:"{{route('retenciones_store')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'json',
                data: $('#form_guardado').serialize(),
                success: function(data){
                    console.log(data);  
                    alert("Retencion guardada correctamente");
                    location.href ="{{route('retenciones_index')}}";
                },
                error: function(data){
                    console.log(data);
                }
            })
        }else{
            $("#buscar").next().remove();
            $("#buscar").after('<span class="validationMessage" style="color:red;">Inserte la serie en la factura</span>');
            alert("Existen campos vacios en la retención");
        } 

    }

    //BUSQUEDA DE FACTURA COMPRA
   
    function traetodo(e){
        console.log(e);
       var id_compra= $('option:selected', e).data("id");
       var serie= $('option:selected', e).data("serie");
       var secuencia= $('option:selected', e).data("secuencia");
       var id_proveedor= $('option:selected', e).data("id_proveedor");
       var nombre_proveedor= $('option:selected', e).data("nombre_proveedor");
       var valor_contable= $('option:selected', e).data("valor_contable");
       var autorizacion= $('option:selected', e).data("autorizacion");
        $("#id_comp").val(id_compra);
        //$("#serie").val(serie);
        $("#autorizacion").val(autorizacion);
        //$("#secuencia").val(secuencia);
        $("#nombre_proveedor").val(nombre_proveedor);
        $("#id_proveedor").val(id_proveedor);
        $("#val_contable").val(valor_contable);
    }

     document.getElementById("fecha_hoy").addEventListener('blur',function(){

        $fechaAc = new Date();
        $mes = $fechaAc.getUTCMonth() + 1;
        var d = new Date( this.value );
        var month = d.getMonth()+1;

        var ty = document.getElementById("fechita").value;
        console.log(ty);
       if($mes != month){


          swal("Recuerde!","La fecha que ingresa está fuera del periodo ","error")
         //document.getElementById("fecha_hoy").value = ty;
         //location.reload();
       }

        
    });



</script>

<script>
    function recalcular(){
        var subtotal0 = $("#subtotal0").val();
        var subtotal12 = $("#subtotal12").val();
        var subtotal = $("#subtotal").val();
        var sub = (parseFloat(subtotal0) + parseFloat(subtotal12));

        $("#subtotal").val(sub);
        
        var impuesto = $("#impuesto").val();

        var totales =0;

        var total  = $("#total").val();

        totales = (parseFloat(sub)+ parseFloat(impuesto));

        $("#total").val(totales);

    }
    //aqui ando
</script>


@endsection