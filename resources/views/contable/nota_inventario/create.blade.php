@extends('contable.nota_inventario.base')
@section('action-content')
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
</style>
<script type="text/javascript">
    function goBack() {
        location.href="{{ route('notainventario.index') }}";
        
    }
    
</script>
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="{{route('notainventario.index')}}">Nota de Ingreso Inventario</a></li>
        <li class="breadcrumb-item active" aria-current="page">Nueva nota de Ingreso Inventario</li>
      </ol>
    </nav>
<div id="alerta_datos" class="alert alert-success alerta_correcto alert-dismissable" role="alert" style="display:none;">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
  Guardado Correctamente 
</div>   
    <form class="form-vertical " method="post" id="form_guardado">
            {{ csrf_field() }}
            <div class="box">
                    <div class="box-header header_new">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-9 col-sm-9 col-6">
                                    <div class="box-title"><b>Nota de Ingreso Inventario</b></div>
                                </div>
                                <div class="col-md-3">
                                    <div class="row">
                                        <a type="button" id="boton_guardar" href="javascript:boton_deuda()" class="btn btn-success btn-gray btn-xs"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
                                        </a>
                                        <button type="button" class="btn btn-success btn-xs btn-gray" onclick="nuevo_comprobante()" style="margin-left: 10px;">
                                            <i class="glyphicon glyphicon-file" aria-hidden="true"></i>&nbsp;&nbsp;Nuevo
                                        </button>
                                        <button type="button" class="btn btn-success btn-xs btn-gray" onclick="goBack()" style="margin-left: 10px;">
                                            <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-body dobra">
                        <div class="row header">
                            <div class="col-md-12 px-1">
                                <div class="form-row ">
                                  
                                    <div class="col-md-12">
                                        &nbsp;
                                    </div>
                                    <div class=" col-md-2 px-1" >
                                        <label class="label_header">Estado</label>
                                        <input style="background-color: green;" readonly class="form-control col-md-1">           
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="col-md-12 label_header" for="id_factura">ID:</label>
                                            <input class="form-control" type="text" name="id_factura" id="id_factura" readonly>    
                                        
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="label_header" for="numero_factura">Número</label>
                                            <input class="form-control" type="text" id="numero_factura" name="numero_factura" readonly>
                                        
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                        <label class="col-md-12 label_header" for="tipo">Tipo:</label>
                                        <input class="form-control" type="text" name="tipo" id="tipo" value="INV-IN" readonly>    
                                    
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="label_header" for="asiento">Asiento</label>
                                            <input class="form-control" type="text" id="asiento" name="asiento" readonly>
                                            @if(isset($iva_param))

                                                <input type="text" name="iva_par" id="iva_par" class="hidden" value="{{$iva_param->iva}}">
                                            @endif
                                    
                                    </div>
                                    
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="col-md-12 label_header" for="fecha_hoy">Fecha: </label>
                                            <input class="form-control" type="date" name="fecha_hoy" id="fecha_hoy" value="{{date('Y-m-d')}}">
                                        
                                    </div>
                                </div>
                                <div class="form-row " id="no_visible">
                                    <div class="col-md-8 px-1">
                                            <input type="hidden" name="total_suma" id="total_suma">
                                            <label class="label_header" for="concepto">Concepto:</label>
                                            <input class="form-control col-md-12" autocomplete="off" type="text" name="concepto" id="concepto" >
                                    </div>
                                    <div class="col-md-4 px-1">
                                        <label class="label-control label_header" for="">Bodega</label>
                                        <select name="id_bodega" id="id_bodega" class="form-control select2" style="width: 100%;">
                                            <option value="">Seleccione...</option>
                                            @foreach($bodega as $value)
                                             <option value="{{$value->id}}">{{$value->nombre}}</option>
                                            @endforeach
                                        </select>   
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 px-1">
                                <label class="label_header" for="detalle_deuda">DETALLE DE LOS PRODUCTOS QUE INGRESAN</label>
                            </div>
                            <div class="col-md-12 px-1">
                                <input type="hidden" name="contadora" id="contadora" value="0">
                                <div class="table-responsive col-md-12 px-1" style="width: 100%;" >               
                                    <table id="example3" role="grid" aria-describedby="example2_info" style="width: 100%;">
                                        <thead style="background-color: #9E9E9E; color: white;" >
                                        <tr>
                                            <th style="width: 10%; text-align: center;">Código</th>
                                            <th style="width: 20%; text-align: center;">Nombre</th>
                                            <th style="width: 15%; text-align: center;">Bodega</th>
                                            <th style="width: 15%; text-align: center;">Cantidad</th>
                                            <th style="width: 10%; text-align: center;">Divisa</th>
                                            <th style="width: 10%; text-align: center;">Costo</th>
                                            <th style="width: 10%; text-align: center;">Costo Base</th>
                                            <th style="width: 10%; text-align: center;">
                                                <button onclick="crea_td()" type="button" class="btn btn-success btn-gray btn-xs" >
                                                    <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                                </button> 
                                             </th>
                                        </tr>
                                        </thead>
                                            <tbody id="det_x">
                                            </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            &nbsp;
                                        </div>
                                        <div class="col-md-2 px-1">
                                            &nbsp;
                                        </div>
                                        <div class="col-md-2 px-1">
                                            &nbsp;
                                        </div>
                                        <div class="col-md-2 px-1">
                                            <label class="label_header" for="total">Total</label>
                                            <input class="form-control  col-md-12" value="0.00" type="text" name="total" id="total" readonly >
                                        </div>
                                    </div>
                                </div>

                            </div> 
                            <div class="col-md-12 px-1">
                                <label class="label_header" for="detalle_deuda">JUSTIFICACION CONTABLE</label>
                            </div>
                            <div class="col-md-12 px-1">
                                <input type="hidden" name="id_compra" id="id_compra">
                                <input type="hidden" name="contador" id="contador" value="0">
                                <div class="table-responsive col-md-12 px-1" style="width: 100%;" >               
                                    <table id="example3" role="grid" aria-describedby="example2_info" style="width: 100%;">
                                        <thead style="background-color: #9E9E9E; color: white;" >
                                        <tr>
                                            <th style="width: 10%; text-align: center;">Código</th>
                                            <th style="width: 20%; text-align: center;">Nombre</th>
                                            <th style="width: 20%; text-align: center;">Detalle</th>
                                            <th style="width: 15%; text-align: center;">Divisa</th>
                                            <th style="width: 15%; text-align: center;">Valor</th>
                                            <th style="width: 10%; text-align: center;">Total Base</th>
                                            <th style="width: 8%; text-align: center;">
                                                <button onclick="crea_td2()" type="button" class="btn btn-success btn-gray btn-xs" >
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
                                    <div class="row">
                                        <div class="col-md-8">
                                            &nbsp;
                                        </div>
                                       
                                        <!--
                                        <div class="col-md-2 px-1">
                                            <label class="label_header" for="total">Total</label>
                                            <input class="form-control  col-md-12" value="0.00" type="text" name="total" id="total" readonly >
                                        </div>-->
                                        <div class="col-md-2 px-1">
                                            <label class="label_header" for="subtotal">Total Debe</label>
                                            <input class="form-control  col-md-12" value="0.00" type="text" name="total_debe" readonly id="total_debe" >
                                        </div>
                                        <div class="col-md-2 px-1">
                                            <label class="label_header" for="impuesto">Total Haber</label>
                                            <input class="form-control  col-md-12" value="0.00" type="text" autocomplete="off" onchange="sumar_impuesto()" name="total_haber" id="total_haber" >
                                        </div>
                                    </div>
                                </div>

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
<script type="text/javascript">
    $(document).ready(function(){
        $('#fact_contable_check').iCheck({
        checkboxClass: 'icheckbox_flat-blue',
        increaseArea: '20%' // optional
        });
    });
    $('.select2').select2({
            tags: false
        });
    $("#id_cliente").autocomplete({
        source: function( request, response ) {
            $.ajax( {
            type: 'GET',
            url: "{{route('ventas.buscarclientexid')}}",
            dataType: "json",
            data: {
                term: request.term
            },
            success: function( data ) {
                response(data);
            
            //console.log("identificacion_cliente", data.id);
                if(data.id==""){
                    //swal("el cliente no existe");
                    existeCliente= false; 
                }else{
                    existeCliente = true;
                }
            }
            } );
        },
        change:function(event, ui){
            $("#crear").empty();
            $("#nombre_cliente").val(ui.item.nombre);          
           
        },
        selectFirst: true,
        minLength: 1,
    } );
    function validacion_campos(){
        var contador  =  0;
        var iva= parseFloat($("#iva_par").val());
        var validar1=0;
        var validar2=0;
        var validar3=0;
        var validar4=0;
        var ivan=0;
        var total=0;
        var totaal=0;
        var sub=0;
        var valor_d=0;
        var ivaf=0;
        $("#det_recibido tr").each(function(){
            $(this).find('td')[0];
            visibilidad = $(this).find('#visibilidad'+contador).val();
            var fecha1= $("#fecha"+contador).val();
            var fecha2= $("#vencimiento"+contador).val();
            var valor= parseFloat($("#valor"+contador).val());
            var codigo= $("#codigo"+contador).val();
            
            if(visibilidad == 1){   
                if(fecha1==""){
                    validar1++;
                }
                if(fecha2==""){
                    validar2++;
                }
                if(codigo==""){
                    validar4++;
                }
                if(valor==0){
                    validar3++;
                }
            }   
            contador = contador+1;
        });
        var totales= validar1+validar2+validar3+validar4;
        if(totales>0){
            return false;
        }
        return true;
    }
    function sumar_impuesto(){
        var subtotal= parseFloat($("#subtotal").val());
        if( isNaN(subtotal)){
            subtotal=0;
        }
        var impuesto= parseFloat($("#impuesto").val());
        if(isNaN(impuesto)){
            impuesto=0;
        }
        $("#impuesto").val(impuesto.toFixed(2,2));
        var s= subtotal*0.12;
        var t= s+subtotal;
        var total= subtotal+impuesto;
        if(total==t){
            $("#total").val(total.toFixed(2,2));
        }else{
            total=subtotal;
            impuesto=0;
            $("#impuesto").val(impuesto.toFixed(2,2));
            $("#total").val(total.toFixed(2,2));
           
        }
      

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
    $('.buscar').autocomplete({
         source: function( request, response ) {
            
            $.ajax( {
            url: "{{route('retenciones.clientes.autocomplete')}}",
            dataType: "json",
            data: {
                term: request.term
            },
            success: function( data ) {
                console.log(data);
                response(data);
            }
        } );        
        },
        minLength: 1,
    } );
    function buscarAsiento(id_asiento){
        $.ajax({
            type: 'get',
            url: "{{route('buscar_asiento.diario')}}",
            datatype: 'json',
            data: {
                'id_asiento': id_asiento,
                'validacion': '1'
            },
            success: function(data) {

                if (data.value != 'No se encontraron resultados') {
                    $('#asiento').val(data[0]);
                    $("#numero_factura").val(data[1]);
                }


            },
            error: function(data) {
                console.log(data);
            }
        })
    }
    function boton_deuda(){
        //swal("hassta aqui");
        var formulario = document.forms["form_guardado"];
        var concepto= formulario.concepto.value;
        var bodega= formulario.id_bodega.value;
        var total_debe= formulario.total_debe.value;
        var total_haber= formulario.total_haber.value;
        //var total= formulario.total.value;
        //var validacion=validacion_campos();
        //console.log(validacion);
        var msj = "";
        if(concepto==""){
            msj+="Por favor ingrese el concepto. <br>";
        }
        if(bodega==""){
            msj+="Por favor ingrese la bodega. <br>";
        }
    
        if(total_debe!=total_haber){
            msj+= "Existe un descuadre del deber contra el haber. <br>";
        }
        if(msj==""){
                $.ajax({
                type: 'post',
                url:"{{route('notainventario.store')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'json',
                data: $('#form_guardado').serialize(),
                success: function(data){
                    console.log(data);
                    if((data)!='error'){
                        $("#id_factura").val(data[0]);
                        $("#numero_factura").val(data[2]);
                        $("#asiento").val(data[1]);
                        swal("Correcto!","Nota de ingreso generada con exito","success");
                        $('#form_guardado input').attr('readonly', 'readonly');
                        $("#boton_guardar").attr("disabled", true);
                        //buscarAsiento(data);
                    }else{
                        swal("Error!","Ya existe nota de débito para ésta factura","error");
                    }                    
                },
                error: function(data){
                    console.log(data);
                    swal("Informe","No se guardaron los datos correctamente","warning");
                }
                })

        }else{
            swal("Error!",msj,"error");
        }

    }
    function set_rubros(id){
        $.ajax({
            type: 'post',
            url:"{{route('rubros_cliente.nombre2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'codigo': $("#rubro"+id).val()},
            success: function(data){
                console.log(data);
                if(data.value!='no'){    
                    $("#id_codigo"+id).val(data[0]);
                    $("#codigo"+id).val(data[0]);
                }
            },
            error: function(data){
                console.log(data);
            }
        })
    }
    
    $("#nombre_cliente").autocomplete({

        source: function( request, response ) {
            $.ajax( {
            type: 'GET',
            url: "{{route('ventas.buscarcliente')}}",
            dataType: "json",
            data: {
                term: request.term
            },
            success: function( data ) {
                response(data);
            }
            } );
        },
        change:function(event, ui){
            $("#crear").empty();
            $("#id_cliente").val(ui.item.id);
            //buscar_vendedor();
        
        },
        selectFirst: true,
        minLength: 1,
    } );
    function set_codigo(id){
        $.ajax({
            type: 'post',
            url:"{{route('compra_codigo2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'codigo':$("#codigo"+id).val()},
            success: function(data){
                console.log(data);
                if(data.value != "no"){
                    $('#nombre'+id).val(data.value);
                    if(data.iva == '1'){
                        document.getElementById('iva'+id).checked = true;
                        document.getElementById('iva'+id).disabled = true;
                        $('#ivaver'+id).val(1);
                    }
                }else{
                    $('#nombre'+id).val(" ");
                }
            },
            error: function(data){
                console.log(data);
            }
        })
    }
    function setNumber(e){
       // return parseFloat(e).toFixed(2);
       //if(e.length)
       if(e==""){
       e=0;
       }
        $("#valor_cheque").val(parseFloat(e).toFixed(2))

    }
    function nuevo_comprobante(){
        location.href ="{{route('notainventario.create')}}";
    }
    function traer_cliente(){
      
        $.ajax({
            type: 'get',
            url:"{{route('nota_cliente_debito.buscar_cliente')}}",
            datatype: 'json',
            data: {'secuencia': $("#facturano").val(),'validacion':'1'},
            success: function(data){
                if(data!='error'){    
                   $("#id_cliente").val(data[0]);
                   $("#nombre_cliente").val(data[1]);
                   console.log(data[2]);
                   if(data[2]==1){
                        $("#facturano").val(data[3]);
                   }else{
                        $("#facturan").val(data[3]);
                   }
                }else{
                    $("#facturano").val('');
                    $("#facturan").val('');
                    $("#id_cliente").val('');
                    $("#nombre_cliente").val('');
                }
                
            },
            error: function(data){
                console.log(data);
            }
        })
    }
    
    function traer_cliente2(){
        $.ajax({
            type: 'get',
            url:"{{route('nota_cliente_debito.buscar_cliente')}}",
            datatype: 'json',
            data: {'validacion':'0','id_factura': $("#facturan").val()},
            success: function(data){
                if(data!='error'){    
                   $("#id_cliente").val(data[0]);
                   $("#nombre_cliente").val(data[1]);
                   console.log(data[2]);
                   if(data[2]==1){
                        $("#facturano").val(data[3]);
                   }else{
                        $("#facturan").val(data[3]);
                   }
                }else{
                    $("#facturano").val('');
                    $("#facturan").val('');
                    $("#id_cliente").val('');
                    $("#nombre_cliente").val('');
                }
                
            },
            error: function(data){
                console.log(data);
            }
        })
    }
    function crea_td(contador){

    id= document.getElementById('contador').value;
    var midiv = document.createElement("tr")
    midiv.setAttribute("id","dato"+id);
    midiv.innerHTML = '<td><input   style=" width: 100%; height: 80%;" name="codigo'+id+'" class="codigo form-control" id="codigo'+id+'" onchange="cambiar_codigo('+id+')"/></td> <td><input type="hidden" id="visibilidad'+id+'" name="visibilidad'+id+'" value="1"><input name="nombre'+id+'"  class="nombre form-control" id="nombre'+id+'" onchange="cambiar_nombre('+id+')" style="width: 100%; height: 80%;"></td><td><select class="form-control select2" id="bodega'+id+'" name="bodega'+id+'" style="width: 100%; height: 78%;"> @foreach($bodega as $value) <option value="{{$value->id}}">{{$value->nombre}}</option>  @endforeach  </select> </td><td> <input class="form-control" style=" width: 100%; height: 80%;" type="text" id="cantidad'+id+'" value="0.00" onkeyup="total_calculo('+id+')" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="cantidad'+id+'" onchange="validarcantidad(this, '+id+'); redondea_cantidad(this, '+id+',2)"> </td> <td> <select class="form-control" id="empaque'+id+'" name="empaque'+id+'" style="width: 100%; height: 80%;"> <option value="unidad">Unidad</option> </select></td><td><input class="form-control" type="text" id="precio'+id+'" name="precio'+id+'" value="0.00" onkeyup="total_calculo('+id+');" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" style="width: 100%; height: 80%;"  onchange="validarprecio(this,'+id+'); redondea_precio(this,'+id+',2)"></td> <td> <input class="form-control" name="extendido'+id+'" id="extendido'+id+'" value="0.00" style="width: 100%; height: 80%;" readonly > <input type="text" class="hidden" name="extendido_'+id+'" id="extendido_'+id+'"> </td> <td><button id="eliminar1'+id+'" type="button" onclick="javascript:eliminar_registro('+id+')" class="btn btn-danger btn-gray delete btn-xs"> <i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';
    document.getElementById('det_x').appendChild(midiv);
    id = parseInt(id);
    id = id+1;
    document.getElementById('contador').value = id;

    $(".nombre").autocomplete({
    source: function( request, response ) {
        $.ajax( {
        type: 'post',
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        url: "{{route('compra_nombre')}}",
        dataType: "json",
        data: {
            term: request.term
        },
        success: function( data ) {
            response(data);
        }
        } );
    },
    minLength: 2,
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

}

    function crea_td2(contador){
        id= document.getElementById('contadora').value;
        var midiv = document.createElement("tr");
        midiv.setAttribute("id","dato1"+id);
        midiv.innerHTML = '<td><input style="width: 100%;" onchange="cambiar_codigo1('+id+')" class="codigo2 form-control" name="codigo_'+id+'" id="codigo_'+id+'" ></td> <td> <input style="width: 100%;" name="nombre_'+id+'" id="nombre_'+id+'" onchange="cambiar_nombre1('+id+')" class="nombre2 form-control"> <input type="hidden" name="id_codigo'+id+'" id="id_codigo'+id+'"></td>  <td> <input class="form-control" style="width: 100%;" autocomplete="off" name="detalle_rubro'+id+'" id="detalle_rubro" ></td> <td><input style="width: 100%;" name="divisas" id="divisas" class="form-control" value="USD" readonly ></td> <td> <input class="valortotal form-control" name="valor'+id+'" style="width: 100%;" id="valor'+id+'" onchange="valor_rubro('+id+')" value="0.00" ></td><input class="visibilidad" type="hidden" name="visibilidad_'+id+'" id="visibilidad_'+id+'" value="1"><td><input class="form-control" style="width: 100%;" name="total_base'+id+'" id="total_base'+id+'" value="0.00" readonly></td><td style="text-align: center;"><button id="eliminar'+id+'" type="button" onclick="javascript:eliminar_registro2('+id+')" class="btn btn-danger btn-gray delete btn-xs"> <i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';
        document.getElementById('det_recibido').appendChild(midiv);
        id = parseInt(id);
        id = id+1;
        document.getElementById('contadora').value = id;

    $(".nombre2").autocomplete({
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
    $(".codigo2").autocomplete({
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

    }
    function total_calculo(id){

        total = 0;
        descuento_total = 0;
        dec=0;
        cantidad = parseInt($("#cantidad"+id).val());
        precio = parseFloat($("#precio"+id).val());
        //descuento = parseFloat($("#desc_porcentaje"+id).val());

        //alert(descuento);
        total = cantidad * precio;
        //descuento_total = (total * descuento)/100;
        /*$('#desc1'+id).val(descuento_total.toFixed(2));
        dec= parseFloat($("#desc1"+id).val());
        if(descuento_total>0){
            total= total-descuento_total;
        }*/
        //alert(descuento_total);
        //$('#desc'+id).val(descuento_total.toFixed(2));
        $('#extendido'+id).val(total.toFixed(2));

        $('#extendido_'+id).val(total.toFixed(2));
        suma_totales();
    }
    function cambiar_codigo(id){
        $.ajax({
            type: 'post',
            url:"{{route('compra_codigo2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'codigo':$("#codigo"+id).val()},
            success: function(data){
                console.log(data);
                if(data.value != "no"){
                    $('#nombre'+id).val(data.value);
                    if(data.iva == '1'){
                        document.getElementById('iva'+id).checked = true;
                        document.getElementById('iva'+id).disabled = true;
                        $('#ivaver'+id).val(1);
                    }
                }else{
                    $('#nombre'+id).val(" ");
                }
            },
            error: function(data){
                console.log(data);
            }
        })
    }
    function cambiar_nombre(id){
        $.ajax({
            type: 'post',
            url:"{{route('compra_nombre2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'nombre':$("#nombre"+id).val()},
            success: function(data){
                if(data.value != "no"){
                    $('#codigo'+id).val(data.value);
                    if(data.iva == '1'){
                        $('#ivaver'+id).val(1);
                        document.getElementById('iva'+id).checked = true;
                        document.getElementById('iva'+id).disabled = true;
                    }
                }else{
                    $('#codigo'+id).val(" ");
                }
            },
            error: function(data){
                console.log(data);
            }
        })
    }
    function cambiar_nombre1(id){
        $.ajax({
            type: 'get',
            url:"{{route('fact_contable_nombre2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'nombre':$("#nombre1"+id).val()},
            success: function(data){
                $('#codigo_'+id).val(data);
            },
            error: function(data){
                console.log(data);
            }
        })
    }
    function cambiar_codigo1(id){
        $.ajax({
            type: 'post',   
            url:"{{route('fact_contable_codigo2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'codigo':$("#codigo_"+id).val()},
            success: function(data){
                
                $('#nombre_'+id).val(data);
             
            },
            error: function(data){
                console.log(data);
            }
        })
    }
    function eliminar_registro(valor)
    {
        var dato1 = "dato"+valor;
        var nombre2 = "visibilidad"+valor;
        document.getElementById(nombre2).value = 0;
        document.getElementById(dato1).style.display='none';
        suma_totales();
    }
    function eliminar_registro2(valor)
    {
        var dato1 = "dato1"+valor;
        var nombre2 = "visibilidad_"+valor;
        document.getElementById(nombre2).value = 0;
        document.getElementById(dato1).style.display='none';
        sumar();
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
        sumar();
        var validacion = parseFloat($("#total_suma").val());
        var valor_total= parseFloat($("#total").val());
        console.log(validacion+"dadasda"+valor_total);
        if(validacion<=valor_total){
            $("#total_haber").val(validacion.toFixed(2,2));
        }else{
            swal("Error!","No puede superar al total","error");
            $("#valor"+id).val('');
            $("#total_base"+id).val('');
            cero=0;
            $("#total_haber").val(cero.toFixed(2,2));
           
        }
        
        //$("#subtotal").val(parseFloat(e).toFixed(2,2));        
        
        //sumar2();
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

        $("#det_recibido tr").each(function(){
            $(this).find('td')[0];
            visibilidad = $(this).find('#visibilidad_'+contador).val();
            if(visibilidad == 1){
                valor = parseFloat($(this).find('#valor'+contador).val());
                //ivan = ivan + valor;
                console.log(valor);
                sub = sub + valor;
                //totaal= valor+ivan;
                total= total+totaal;
            }
            contador = contador+1;
        });
            console.log("el contador" +sub);
            var totalsx= ivan*iva;
            var total_final= ivan;
            $("#total_suma").val(sub.toFixed(2,2));
            //var cero=0;
            $("#total_haber").val(sub.toFixed(2,2));
            /*if(!isNaN(ivan)){ $('#impuesto').val(totalsx.toFixed(2));   }*/
           /* if(!isNaN(sub)){ $('#subtotal').val(sub.toFixed(2));   }
            if(!isNaN(total)){ $('#total').val(total_final.toFixed(2));   }*/
    }
    function sumar2(){
        var contador  =  0;
        var iva= parseFloat($("#iva_par").val());
        var ivan=0;
        var total=0;
        var totaal=0;
        var sub=0;
        var valor_d=0;
        var ivaf=0;
        $("#det_x tr").each(function(){
            $(this).find('td')[0];
            visibilidad = $(this).find('#visibilidad'+contador).val();
            if(visibilidad == 1){
                valor = parseFloat($(this).find('#total_base'+contador).val());
                ivan = ivan + valor;
                sub = sub + valor;
                totaal= valor+ivan;
                total= total+totaal;
            }
            contador = contador+1;
        });
            /*var totalsx= ivan*iva;
            var total_final= totalsx+ivan;
            if(!isNaN(total)){ $('#total_suma').val(total_final.toFixed(2));   }*/
    }
    function validarcantidad(elemento, id){

        var cod = $('#codigo'+id).val();
        var nomb = $('#nombre'+id).val()
        if((cod.length == 0)&&(nomb.length == 0)){
            swal("Debe ingresar el código","Nombre del producto","error");
            $('#cantidad'+id).val("0");
            $('#desc'+id).val("0");
            $('#extendido'+id).val("0");
            return false;
        }
        var num = elemento.value;
        if (num.length == 0){
            swal("Cantidad no Permitida","Por favor ingrese de nuevo","error");
            $('#cantidad'+id).val("0");
            $('#desc'+id).val("0");
            $('#extendido'+id).val("0");
            suma_totales();
            return false;
        }


        var numero = parseInt(elemento.value,10);
        if(numero<-1 || numero>999999999){
            swal("Cantidad no Permitida","Por favor ingrese de nuevo","error");
            $('#cantidad'+id).val("0");
            return false;
        }
        return true;
    }
    function validarprecio(elemento, id){
        var cod = $('#codigo'+id).val();
        var nomb = $('#nombre'+id).val()

        if((cod.length == 0)&&(nomb.length == 0)){
            swal("Error!","Debe ingresar el código, nombre, cantidad del producto","error");
            $('#precio'+id).val("0");
            $('#desc'+id).val("0");
            $('#extendido'+id).val("0");
            return false;
        }

        var prec = elemento.value;
        if (prec.length == 0){

            swal("¡Error!","Ingrese de nuevo","error");
            $('#precio'+id).val("0");
            $('#desc'+id).val("0");
            $('#extendido'+id).val("0");
            total_calculo(id);
            //suma_totales();
            return false;
        }

        var numero = parseInt(elemento.value,10);
        //Validamos que se cumpla el rango
        if(numero<-1 || numero>999999999){
            swal("Precio no Permitido");
            $('#precio'+id).val("0");
            return false;
        }
        return true;
    }
    function validar_td(id){
        if((id)!=null){
            var valor= parseFloat($("#total").val());
            var abono= parseFloat($("#abono"+id).val());
            var saldo= parseFloat($("#saldo"+id).val());
            suma_totales();
            var cantidad= parseFloat($("#total_suma").val());
            if(!isNaN(abono) && !isNaN(valor) && !isNaN(cantidad)){
                if(cantidad<=valor){
                    if(abono>saldo){
                        abono=saldo;
                    }
                    $("#abono"+id).val(abono.toFixed(2,2));
                }else{
                    valor=0;
                    $("#abono"+id).val(valor.toFixed(2,2));
                    swal("¡Error!","Error no puede superar al valor total","error")
                }
            }else{
                abono=0;
                valor=0;
                $("abono"+id).val(valor.toFixed(2,2));
            }
        }else{
            alert("error");
        }
    }

    function redondea_precio(elemento,id,nDec){

        var n = parseFloat(elemento.value);
        var s;
        var d= elemento.value;
        var en= String(d);
        arr = en.split(".");  // declaro el array
        entero= arr[0];
        decimal = arr[1];
        //alert(decimal);
            if(decimal!=null){
                if((decimal.length)>2){
                    n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
                    s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
                    s = s.substr(0, s.indexOf(".") + nDec + 1);
                    $('#precio'+id).val(s);
                }
            }else{
                $('#precio'+id).val(n.toFixed(2,2));
            }
    }
    function redondea_cantidad(elemento,id,nDec){
        var n = parseFloat(elemento.value);
        var s;
        var d= elemento.value;
        var en= String(d);
        arr = en.split(".");  // declaro el array
        entero= arr[0];
        decimal = arr[1];
        n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
        s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
        s = s.substr(0, s.indexOf(".") + nDec + 1);
            if(decimal!=null){
                if((decimal.length)>2){
                    n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
                    s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
                    s = s.substr(0, s.indexOf(".") + nDec + 1);
                    $('#cantidad'+id).val(s);
                }else{
                    $('#cantidad'+id).val(n.toFixed(2,2));
                }
            }
        $('#cantidad'+id).val(s);
    }
 function suma_totales(){

        contador  =  0;
        iva1 = 0;
        iva = 0;
        total = 0;
        sub = 0;
        descu1 = 0;
        total_fin = 0;
        descu = 0;
        trans = 0;
        subtotal_0 = 0;
        subtotal_12 = 0;
        base_imponible = 0;
        var iva_par = $('#iva_par').val();
        //var iva_par = $('#iva_par').val();

        $("#det_x tr").each(function(){
      
            $(this).find('td')[0];
            cantidad = parseInt($(this).find('#cantidad'+contador).val());
            valor = parseInt($(this).find('#precio'+contador).val());
            descu = parseFloat($(this).find('#desc1'+contador).val());
            pre_neto = parseInt($(this).find('#extendido_'+contador).val());
            visibilidad = $(this).find('#visibilidad'+contador).val();
            total = cantidad * valor;
        
            //iva = $(this).find('#iva'+cosssssssssntador).val();
            if(visibilidad==1){
                sub = sub + total;
            }
            if(isNaN(sub)){
                sub=0;
            }
            /*
                if($('#iva'+contador).prop('checked') ) {
                    var iva_par = 0.12;
                    var cod = $('#codigo'+contador).val();
                    var nomb = $('#nombre'+contador).val();
                    if((cod.length == 0)&&(nomb.length == 0)){
                        swal("Error!","Debe ingresar el código, nombre del Producto o Servicio","error");
                        $('#iva'+contador).prop('checked',false); 
                    }  
                    if(total>0){
                    sub = sub + total;
                    }
                    //iva = sub * 0.12;
                    
                    if(descu>0){
                    iva1 = (pre_neto-descu) * (iva_par);
                    iva = iva + iva1;
                    }else{
                    iva1 = pre_neto * iva_par;
                    iva = iva + iva1;
                    }
                
                }else{
                    if(total>0){
                    sub = sub + total;
                    }
                }
            */
            /*if(iva == 1){
                sub = sub + total;
                iva = sub * 0.12;
            }*/
            //sub = sub + iva;
              
           
            
            contador = contador+1;

        });

        //iva = (sub- descu) * iva_par;

        base_imponible = sub;
        //iva = sub * iva_par;
        //iva = subtotal_12 * iva_par;
        //subtotal final es el valor sin el iva
        if(!isNaN(sub)){$('#total').val(sub.toFixed(2,2));}
        if(!isNaN(sub)){$('#total_debe').val(sub.toFixed(2,2));}
        // aqui 
        //if(!isNaN(base_imponible)){$('#base_imponible').val(base_imponible);}
        
        //$('#total1').val(total_fin);

        //evaluar();


    }
    function rellena_ceros(){
        contador=0;
        cero=0;
        $("#det_recibido tr").each(function(){
            $(this).find('td')[0];
            visibilidad = $(this).find('#visibilidad'+contador).val();
            if(visibilidad == 1){
                $(this).find('#valor'+contador).val(cero.toFixed(2,2));
            }
            contador = contador+1;
        });
    }
    function addNewRow(pos,fecha, valor, factura, fact_numero, observacion, valor_nuevo){
        var markup = "";
        var num= parseInt(pos)+1;
        markup = "<tr>"+
                "<td> <input style='width: 100%;' type='text' name='vence"+pos+"' id='vence"+pos+"' readonly='' value='"+fecha+"'> </td>"+
                "<td> <input style='width: 100%;' type='text' name='tipo"+pos+"' id='tipo"+pos+"' value='"+factura+"' readonly=''> </td>"+
                "<td> <input style='width: 100%;' type='text' name='numero"+pos+"' id='numero"+pos+"' value='"+fact_numero+"' readonly=''> </td>"+
                "<td> <input style='width: 100%;' type='text' name='concepto"+pos+"' id='concepto"+pos+"' value='Fact #:"+fact_numero+" Prov: "+observacion+"' readonly=''> </td>"+
                "<td> <input style='width: 100%;' type='text' style='background-color: #c9ffe5;' name='div"+pos+"' id='div"+pos+"' value='$' readonly=''> </td>"+
                "<td> <input style='width: 100%;' type='text' style='background-color: #c9ffe5; ' name='saldo"+pos+"' value='"+valor+"' id='saldo"+pos+"' readonly=''> </td>"+
                "<td> <input style='width: 100%;' type='text' style='background-color: #c9ffe5; text-align: center;' name='abono"+pos+"' id='abono"+pos+"' onchange='validar_td("+pos+")'></td>"+
                "<td> <input style='width: 100%;' type='text' style=' text-align: left;' name='nuevo_saldo"+pos+"' value='"+valor+"' id='nuevo_saldo"+pos+"' readonly=''></td>"+
                "<td> <input style='width: 100%;' type='text' style='text-align: center;' name='abono_base"+pos+"' id='abono_base"+pos+"' readonly=''> </td>"+
            "</tr>";
        return markup;

    }


</script>

@endsection
