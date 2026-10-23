@extends('contable.egresos.base')
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
        

</style>
<script type="text/javascript">
    function goBack() {
      window.history.back();
    }
</script>

<section class="content">
    
    <form class="form-vertical" method="post" id="form_guardado">
        <div class="box box-success box-solid "  style=" background-color: white;">
                <div class="header box-header with-border">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-9 col-sm-9 col-6">
                                <div class="box-title" ><b style="font-size: 16px;">Nota de Débito Acreedores</b></div>
                            </div>
                            <div class="col-3">
                                <div class="row">
                                    <button type="button" onclick="crear_retenciones()" id="boton_guardar" class="btn btn-primary" style="color:white; border-radius: 5px; border: 2px solid white;"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
                                    </button>
                                    <button type="button" onclick="goBack()" class="btn btn-primary" style="color:white; border-radius: 5px; border: 2px solid white;">
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-body dobra">
                    <div class="col-12 col-xs-12">
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label for="buscar" class = "col-form-label-sm">Buscar</label>
                                <input type="text" id = "buscar" name="buscar" class = "form-control form-control-sm buscar" onchange="buscar_factura()">
                                
                            </div>
                            <div class="form-group col-md-1">
                                <label style="padding-left: 0px">Estado</label>
                                <div style="background-color: green; " class="form-control col-md-1"></div>           
                            </div>
                            <div class="form-group col-md-2">
                                <div class="input-group">
                                    <label class="col-md-12" for="id_factura">ID:</label>
                                    <input style="width: 80%;" type="text" name="id_factura" id="id_factura" disabled>    
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <div class="input-group">
                                    <label for="numero_factura">Número</label>
                                    <input style="width: 80%;" type="text" id="numero_factura" name="numero_factura" disabled>
                                </div>
                            </div>
                            <div class="form-group col-md-1">
                                <div class="input-group">
                                    <label for="asiento">Asiento</label>
                                    <input type="text" id="asiento" name="asiento" style="width: 125%;" disabled>
                                </div>
                               
                            </div>
                            <div class="form-group col-md-2" style="padding-left: 58px;">
                                <div class="input-group">
                                    <label class="col-md-12" for="fecha_hoy">Fecha: </label>
                                    <input type="date" name="fecha_hoy" id="fecha_hoy" value="{{date('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="form-group col-md-2" style="padding-left: 20px;">
                                <div class="input-group">
                                    <label class="col-md-12" for="fecha_caducidad">Fecha Caducidad: </label>
                                    <input type="date" name="fecha_caducidad" id="fecha_caducidad" value="{{date('Y-m-d')}}">
                                </div>
                            </div>
                          <!--  <div class="form-group col-md-1" style="padding-left: 20px;">
                                <div class="input-group">
                                    <label class="col-md-12" for="proyecto">Proyecto: </label>
                                    <select name="proyecto" id="proyecto" disabled="disabled">
                                        <option value="0">0000</option>
                                    </select>
                                </div>
                            </div>-->
                            <div class="form-group col-md-12">
                               &nbsp;
                            </div>
                            <div class="form-group col-md-3">
                                <div class="input-group">
                                    <label class="col-md-12" for="acreedor">Acreedor:</label>
                                    <input type="text" name="acreedor" id="acreedor" disabled>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <div class="input-group">
                                    <label class="col-md-12" for="direccion">Autorizacion:</label>
                                    <input type="text" name="direccion" id="direccion" disabled>
                                </div>
                            </div>
                            <!--<div class="form-group col-md-2">
                                <div class="input-group">
                                    <label class="col-md-12" for="ruc">Ruc:</label>
                                    <input type="text" name="ruc" id="ruc" style="width:115%;" disabled>
                                </div>
                            </div>-->
                            <div class="form-group col-md-2">
                                <div class="input-group">   
                                    <label class="col-md-12" for="serie">Serie:</label>
                                    <input type="text" name="serie" id="serie" style="width:115%;" disabled>
                                </div> 
                            </div>
                            <div class="form-group col-md-2">
                                <div class="input-group">
                                    <label class="col-md-12" for="secuencia">Secuencia:</label>
                                    <input type="text" name="secuencia" id="secuencia" style="width:115%;" disabled>
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <label class="col-md-12" for="credito_tributario">Credito Tributario</label>
                                <select name="credito_tributario" id="credito_tributario">
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                            <div class="form-group col-md-8">
                                <div class="input-group">
                                    <label  class="col-md-12" for="concepto">Concepto:</label>
                                    <input type="text" name="concepto" id="concepto" style="width:200%;" > 
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="col-md-12" for="tipo_comprobante">Tipo de comprobante</label>
                                <select name="tipo_comprobante" id="tipo_comprobante">
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                &nbsp;
                            </div>
                            <div class="form-group col-md-2" style="padding-left: 20px;">
                                <div class="input-group">
                                    <label class="col-md-12" for="fecha_factura">Fecha Fact: </label>
                                    <input type="date" name="fecha_factura" id="fecha_factura" value="{{date('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <div class="input-group">
                                    <label class="col-md-12" for="serie_fact">Serie Fact:</label>
                                    <input type="text" name="serie_fact" id="serie_fact" disabled>
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <div class="input-group">
                                    <label class="col-md-12" for="actuarizacion_fact">Autori. Fact:</label>
                                    <input type="text" name="actuarizacion_fact" id="actuarizacion_fact" disabled>
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <div class="input-group">
                                    <label class="col-md-12" for="actuarizacion_fact">Tipo comprobante Factura</label>
                                    <select name="tipo_comprobante" id="tipo_comprobante">
                                        @foreach($t_comprobante as $value)
                                            <option value="{{$value->id}}">{{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <input type="hidden" name="id_compra" id="id_compra">
                        <div class="table-responsive col-md-12" style="min-height: 250px; max-height: 250px; top: 20px;">
                                                <table id="example2" role="grid" aria-describedby="example2_info">
                                                    <thead style="background-color: #FFF3E3">
                                                    <tr style="position: relative;">
                                                        <th style="width: 8%; text-align: center;">Rubro</th>
                                                        <th style="width: 8%; text-align: center;">Detalle</th>
                                                        <th style="width: 8%; text-align: center;">Divisa</th>
                                                        <th style="width: 8%; text-align: center;">Valor</th>
                                                        <th style="width: 8%; text-align: center;">Total Base</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="crear">
                                                    @php $cont=0; @endphp
                                                    @foreach (range(1, 3) as $i)
                                                        <tr>
                                                           <td> <input type="text" name="rubro{{$cont}}" id="rubro{{$cont}}"></td>
                                                           <td> <input type="text" name="detalle{{$cont}}" id="detalle{{$cont}}"></td>
                                                           <td> <select name="divisas{{$cont}}" id="divisas{{$cont}}">
                                                                @foreach($divisas as $value)
                                                                    <option value="{{$value->id}}">{{$value->descripcion}}</option>
                                                                @endforeach
                                                                </select> </td>
                                                            <td><input type="text" name="valor{{$cont}}" id="valor{{$cont}}"></td>
                                                            <td><input type="text" name="total_base{{$cont}}" id="total_base{{$cont}}"></td>
                                                        </tr>
                                                        @php $cont = $cont +1; @endphp
                                                    @endforeach
                                                        
                                                    </tbody>
                                                    <tfoot>

                                                    </tfoot>
                                                </table>
                                                <div class="col-md-6">
                                                 &nbsp;
                                                </div>
                                                <table class="col-md-4" style="top: 20px;" id="example2" role="grid" aria-describedby="example2-info">
                                                                    <thead style="background-color: #FFF3E3">
                                                                        <tr style="position: relative;">
                                                                            <th style="width: 8%; text-align: center;">Subtotal</th>
                                                                            <th style="width: 8%; text-align: center;">Impuesto</th>
                                                                            <th style="width: 8%; text-align: center;">Total</th>
                                                                        </tr>
                                                                    
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                          <td><input type="text" name="subtotal" id="subtotal" disabled></td>
                                                                          <td><input type="text" name="impuesto" id="impuesto" disabled></td>
                                                                          <td><input type="text" name="total" id="total" disabled></td>
                                                                        </tr>                                                                    
                                                                    </tbody>
                                                </table>
                        </div>
                    </div>
                    <div class="col-12 ">
                        <label>Detalle de deudas del Proveedor</label>
                        <input type="hidden" name="total_factura" id="total_factura">
                    </div>
                    <div class="col-12 ">
                        <div class="table-responsive col-md-12" style="min-height: 250px; max-height: 250px; top: 20px;">
                                                
                                                <table id="example2" role="grid" aria-describedby="example2_info">
                                                    <thead style="background-color: #FFF3E3">
                                                    <tr style="position: relative;">
                                                        
                                                        <th style="width: 8%; text-align: center;">Vence</th>
                                                        <th style="width: 10%; text-align: center;">Tipo</th>
                                                        <th style="width: 10%; text-align: center;">Número</th>
                                                        <th style="width: 10%; text-align: center;">Concepto</th>
                                                        <th style="width: 6%; text-align: center;">Div</th>
                                                        <th style="width: 6%; text-align: center;">Saldo</th>
                                                        <th style="width: 6%; text-align: center;">Abono</th>
                                                        <th style="width: 6%; text-align: center;">Saldo Base</th>
                                                        <th style="width: 6%; text-align: center;">Abono Base</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="crear">
                                                    @php $cont=0; @endphp
                                                    @foreach (range(1, 5) as $i)
                                                        <tr>
                                                            <!-- GEORGE AQUI VA EL DETALLE DE LAS RETENCIONES -->
                                                            <td> <input type="text" name="vence{{$cont}}" id="vence{{$cont}}" disabled="disabled"> </td>
                                                            <td> <input type="text" name="tipo{{$cont}}" id="tipo{{$cont}}" disabled="disabled"> </td>
                                                            <td> <input style="width: 90%;" type="text" name="numero{{$cont}}" id="numero{{$cont}}" disabled="disabled"> </td>
                                                            <td> <input type="text" name="concepto{{$cont}}" id="concepto{{$cont}}" disabled="disabled"> </td>
                                                            <td> <input style="background-color: #c9ffe5; width: 150%;" type="text" name="div{{$cont}}" id="div{{$cont}}" value="$" disabled="disabled"> </td>
                                                            <td> <input style="background-color: #c9ffe5; width: 150% " type="text" name="saldo{{$cont}}" id="saldo{{$cont}}" disabled="disabled"> </td>
                                                            <td> <input style="background-color: #c9ffe5; width: 150%; text-align: right;" type="text" name="abono{{$cont}}" id="abono{{$cont}}" disabled="disabled"></td>
                                                            <td> <input style="width: 150%; text-align: left;" type="text" name="nuevo_saldo{{$cont}}" id="nuevo_saldo{{$cont}}" disabled="disabled"></td>
                                                            <td> <input style="width: 150%; text-align: center;" type="text" name="abono_base{{$cont}}" id="abono_base{{$cont}}" disabled="disabled"> </td>
                                                        </tr>
                                                        @php $cont = $cont +1; @endphp
                                                    @endforeach
                                                        
                                                    </tbody>
                                                    <tfoot>
                                                    </tfoot>
                                                </table>
                        </div>
                        
                        <div class="col-md-12" style="margin-top: 20px;">
                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label for="total_ingresos">Total Egreso</label>
                                        <input style="width: 90%;" type="text" name="total_egreso" id="total_egreso">
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label for="credito_aplicado">Debito Aplicado</label>
                                        <input style="width: 90%; color: red; text-align: right;" type="text" name="debito_aplicado" value="0.00" id="debito_aplicado">
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label for="total_deudas">Total Deudas</label>
                                        <input style="width: 90%;" type="text" name="total_deudas" id="total_deudas">
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label for="total_abonos">Total Abonos</label>
                                        <input style="width: 90%;" type="text" name="total_abonos" id="total_abonos">
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label for="nuevo_saldo">Nuevo Saldo</label>
                                        <input style="width: 90%;" type="text" name="nuevo_saldo" id="nuevo_saldo">
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label for="deficit">Déficit</label>
                                        <input style="width: 90%; color: red;" type="text" name="deficit" id="deficit" value="0.00" disabled>                                    
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label for="credito_favo">Crédito a Favor</label>
                                        <input style="width: 90%;" type="text" name="credito_favor" id="credito_favor" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="col-md-12">
                        &nbsp;
                    </div>

                </div>
               
        </div>

    </form>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">
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

</script>


@endsection