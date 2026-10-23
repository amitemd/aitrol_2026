@extends('contable.retenciones.base')
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
        <div class="box box-info box-solid "  style=" background-color: white;">
                <div class="header box-header with-border">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-9 col-sm-9 col-6">
                                <div class="box-title size_text" ><b style="font-size: 16px;">ACREEDORES-COMP. DE RETENCIONES</b></div>
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

                <div class="box-body size_text" style="background-color: #fff;">
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
                                    <label class="col-md-12" for="id_factura">ID:</label>
                                    <input class="form-control" style="width: 80%;" type="text" name="id_factura" id="id_factura" onlyread>    
                            </div>
                            
                            <div class="form-group col-md-2">
                                    <label for="numero_factura">Número</label>
                                    <input class="form-control" style="width: 80%;" type="text" id="numero_factura" name="numero_factura" onlyread>
                            </div>
                            <div class="form-group col-md-1">
                                    <label for="asiento">Asiento</label>
                                    <input class="form-control" type="text" id="asiento" name="asiento" style="width: 125%;" onlyread>
                            </div>
                            <div class="form-group col-md-2" style="padding-left: 58px;">
                                    <label class="col-md-12" for="fecha_hoy">Fecha: </label>
                                    <input class="form-control" type="date" name="fecha_hoy" id="fecha_hoy" value="{{date('Y-m-d')}}">
                            </div>
                            <div class="form-group col-md-12">
                               &nbsp;
                            </div>
                            <div class="form-group col-md-3">
                                    <label class="col-md-12" for="acreedor">Acreedor:</label>
                                    <input class="form-control" type="text" name="acreedor" id="acreedor" onlyread>
                                
                            </div>
                            <div class="form-group col-md-3">
                                    <label class="col-md-12" for="direccion">Dirección:</label>
                                    <input class="form-control" type="text" name="direccion" id="direccion" onlyread>                            
                            </div>
                            <div class="form-group col-md-8">
                                    <label  class="col-md-12" for="concepto">Concepto:</label>
                                    <input class="form-control" type="text" name="concepto" id="concepto" > 
                            </div>
                        </div>
                    </div>
                    <div class="col-12 ">
                        <input type="hidden" name="id_compra" id="id_compra">
                        <div class="table-responsive col-md-12" style="min-height: 250px; max-height: 250px; top: 20px;">
                                                <table id="example2" role="grid" aria-describedby="example2_info">
                                                    <thead style="background-color: #FFF3E3">
                                                    <tr style="position: relative;">
                                                        <th style="width: 8%; text-align: center;">Factura</th>
                                                        <th style="width: 8%; text-align: center;">Divisa</th>
                                                        <th style="width: 8%; text-align: center;">Base Fuente</th>
                                                        <th style="width: 8%; text-align: center;">Tipo RFIR</th>
                                                        <th style="width: 8%; text-align: center;">Total RFIR</th>
                                                        <th style="width: 8%; text-align: center;">Base I.V.A</th>
                                                        <th style="width: 8%; text-align: center;">Tipo RFIVA</th>
                                                        <th style="width: 8%; text-align: center;">Total RFIVA</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="crear">
                                                    @php $cont=0; @endphp
                                                    @foreach (range(1, 2) as $i)
                                                        <tr>
                                                            <td> <input class="form-control" style="width: 90%;" type="text" name="numero_referencia{{$cont}}" id="numero_referencia{{$cont}}"> </td>
                                                            <td> 
                                                            <select class="form-control" name="divisas{{$cont}}" id="divisas{{$cont}}">
                                                                @foreach($divisas as $value)
                                                                <option value="{{$value->id}}">{{$value->descripcion}}</option>
                                                                @endforeach
                                                            </select> 
                                                            
                                                            </td>
                                                            <td> <input class="form-control" readonly style="width: 90%; text-align: center;" type="text" name="base_fuente{{$cont}}" id="base_fuente{{$cont}}"></td>
                                                            <td> <select class="form-control" name="tipo_rfir{{$cont}}" id="tipo_rfir{{$cont}}" onchange="lista_valores({{$cont}})">
                                                                 @foreach($rfir as $value)
                                                                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                                                                 @endforeach
                                                                </select> </td>
                                                            <td> <input onlyread class="form-control" style="width: 90%; text-align: center;"type="text" name="total_rfir{{$cont}}" id="total_rfir{{$cont}}" > </td>
                                                            <td> <input class="form-control" readonly style="width: 90%; text-align: center;" type="text" name="base_iva{{$cont}}" id="base_iva{{$cont}}"> </td>
                                                            <td> <select class="form-control" name="tipo_rfiva{{$cont}}"  id="tipo_rfiva{{$cont}}" onchange="lista_valores2({{$cont}})">
                                                                    @foreach($rfiva as $value)
                                                                        <option value="{{$value->id}}">{{$value->nombre}}</option>
                                                                    @endforeach
                                                                 </select> 
                                                            </td>
                                                            <td> <input class="form-control" style="width: 90%; text-align: center;" onlyread type="text" name="total_rfiva{{$cont}}" id="total_rfiva{{$cont}}"> </td>
                                                        </tr>
                                                        @php $cont = $cont +1; @endphp
                                                    @endforeach
                                                        
                                                    </tbody>
                                                    <tfoot>
                                                    </tfoot>
                                                </table>
                        </div>
                        <div class="col-md-12" style="margin-top: 30px;">
                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <label for="retencion_impuesto">Ret. Imp. Renta </label>
                                </div>
                                <div class="form-group col-md-2">
                                    <input class="form-control" value="0" type="text" name="retencion_impuesto" id="retencion_impuesto" onlyread >
                                </div>
                                <div class="form-group col-md-3" style="text-align: right;">
                                    <label for="">Ret. I.V.A</label>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <input class="form-control" value="0" type="text" name="retencion_iva" id="retencion_iva" onlyread>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 ">
                        <label>Detalle de deudas del Proveedor</label>
                        <input type="hidden" name="total_factura" id="total_factura">
                        <input type="hidden" name="id_proveedor" id="id_proveedor">
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
                                                    @foreach (range(1, 3) as $i)
                                                        <tr>
                                                            <!-- GEORGE AQUI VA EL DETALLE DE LAS RETENCIONES -->
                                                            <td> <input class="form-control" type="text" name="vence{{$cont}}" id="vence{{$cont}}" onlyread> </td>
                                                            <td> <input class="form-control" type="text" name="tipo{{$cont}}" id="tipo{{$cont}}" onlyread> </td>
                                                            <td> <input class="form-control" style="width: 90%;" type="text" name="numero{{$cont}}" id="numero{{$cont}}" onlyread> </td>
                                                            <td> <input class="form-control" type="text" name="concepto{{$cont}}" id="concepto{{$cont}}" onlyread> </td>
                                                            <td> <input class="form-control" style="background-color: #c9ffe5; width: 150%;" type="text" name="div{{$cont}}" id="div{{$cont}}" value="$" onlyread> </td>
                                                            <td> <input class="form-control" style="background-color: #c9ffe5; width: 150% " type="text" name="saldo{{$cont}}" id="saldo{{$cont}}" onlyread> </td>
                                                            <td> <input class="form-control" style="background-color: #c9ffe5; width: 150%; text-align: center;" type="text" name="abono{{$cont}}" id="abono{{$cont}}" onlyread></td>
                                                            <td> <input class="form-control" style="width: 150%; text-align: center;" type="text" name="nuevo_saldo{{$cont}}" id="nuevo_saldo{{$cont}}" onlyread></td>
                                                            <td> <input class="form-control" style="width: 150%; text-align: center;" type="text" name="abono_base{{$cont}}" id="abono_base{{$cont}}" onlyread> </td>
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
                                        <input class="form-control" style="width: 90%;" type="text" name="total_egreso" id="total_egreso">
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label for="credito_aplicado">Debito Aplicado</label>
                                        <input class="form-control" style="width: 90%; color: red; text-align: right;" type="text" name="debito_aplicado" value="0.00" id="debito_aplicado">
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label for="total_deudas">Total Deudas</label>
                                        <input class="form-control" style="width: 90%;" type="text" name="total_deudas" id="total_deudas">
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label for="total_abonos">Total Abonos</label>
                                        <input class="form-control" style="width: 90%;" type="text" name="total_abonos" id="total_abonos">
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label for="nuevo_saldo">Nuevo Saldo</label>
                                        <input class="form-control" style="width: 90%;" type="text" name="nuevo_saldo" id="nuevo_saldo">
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label for="deficit">Déficit</label>
                                        <input class="form-control" style="width: 90%; color: red;" type="text" name="deficit" id="deficit" value="0.00" disabled>                                    
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label for="credito_favo">Crédito a Favor</label>
                                        <input class="form-control" style="width: 90%;" type="text" name="credito_favor" id="credito_favor" disabled>
                                        <input type="hidden" name="retencion_fuente" id="retencion_fuente">
                                        <input type="hidden" name="retencion_ivas" id="retencion_ivas">
                                        <input type="hidden" name="retencion_totales" id="retencion_totales">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="col-md-12">
                        &nbsp;
                    </div>
                    <div class="col-md-12">
                        <div class="input-group">
                            <label class="col-md-12" style="background-color: #bbb0ad;" for="nota">Nota: </label>
                            <textarea class="col-md-12" name="nota" id="nota" cols="200" rows="5"></textarea>
                        </div>
                    </div>
                </div>
               
        </div>

    </form>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
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
                var iva= (data[10]*0.12); 
                $("#id_factura").val(data[0]);
                $("#numero_factura").val(data[12]);
                $("#concepto").val(data[4]+'.'+' '+'REF :'+data[0]);
                $("#asiento").val(data[16]);
                $("#acreedor").val(data[0]+' '+data[2]);
                $("#direccion").val(data[3]);
                $("#total_factura").val(data[10]);
                $("#nuevo_saldo0").val(data[10]);
                $("#total_deudas").val(data[10]);
                $("#id_compra").val(data[17]);
                $("#vence").val(data[6]);
                $("#tipo").val(data[7]);
                $("#base_fuente").val(data[10]);
                $("#id_proveedor").val(data[0]);
                for(i=0;i<data[18].length; i++){
                    $("#vence"+i).val(data[18][i].fecha_asiento);
                    $("#tipo"+i).val(data[18][i].id_tipoproveedor);
                    $("#numero_referencia"+i).val((data[18][i].fact_numero)+'-'+data[1]);
                    $("#base_fuente"+i).val(data[18][i].valor);
                    $("#divisas"+i).val(data[18][i].divisas); 
                    $("#numero"+i).val(data[18][i].fact_numero);
                    $("#concepto"+i).val(data[18][i].observacion); 
                    $("#saldo"+i).val((data[18][i].valor));
                    $("#tipo_rfiva"+i).val((data[18][i].id_porcentaje_iva));
                    $("#tipo_rfir"+i).val((data[18][i].id_porcentaje_ft));
                    var iva_base= parseFloat(data[18][i].valor);
                    var total_iva= iva_base*12/100;
                    $("#base_iva"+i).val(total_iva.toFixed(2));            
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
               //console.log(data);
                if(data.value!='no'){
                    $("#total_rfir"+id).val(data[0].valor+'%');
                    var total_enrfir= parseFloat(data[0].valor)/100;
                    var base_fuente= parseFloat($("#base_fuente0").val());
                    var asiento_retencion_rfir= total_enrfir*base_fuente;
                    $("#retencion_fuente").val(asiento_retencion_rfir.toFixed(2));
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
             //console.log(data);
                if(data.value!='no'){
                    
                    $("#total_rfiva"+id).val(data[0].valor+'%');     
                    $("#retencion_iva").val(data[0].valor);
                    var total_enrfiva= parseFloat(data[0].valor)/100;
                    var retencion_iva= parseFloat($("#base_iva0").val());
                    var asiento_retencion_rfiva= total_enrfiva*retencion_iva;
                    $("#retencion_ivas").val(asiento_retencion_rfiva.toFixed(2));
                    
                    total_abono()                                        
                }
            },
            error: function(data){
                console.log(data);
            }
        })
    }
    function total_abono(){
        var variable1= parseFloat($("#retencion_impuesto").val());
        var variable2= parseFloat($("#retencion_iva").val());
        var retencion_fuente= parseFloat($("#retencion_fuente").val());
        var retencion_iva= parseFloat($("#retencion_ivas").val());
        var totales= variable1+variable2;
        var total_retenciones= retencion_fuente+retencion_iva;
        var vad= parseFloat($("#total_factura").val());
        var total_da= vad-totales;
        if(totales!=NaN && total_da!=NaN){
            $("#abono0").val(totales);
            $("#abono_base0").val(totales);
            $("#total_egreso").val(totales);
            $("#nuevo_saldo0").val(total_da.toFixed(2));
            $("#retencion_totales").val(total_retenciones.toFixed(2));
        }
        
        
    }

    function crear_retenciones(){
        var buscar= $("#buscar").val();
        var  tipo_rfiva= $("#tipo_rfiva").val();

        if(buscar!=0){
            
            $.ajax({
                type: 'post',
                url:"{{route('retenciones_store')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'json',
                data: $('#form_guardado').serialize(),
                success: function(data){
                    //console.log(data);  
                    // sweet alert que funciona asi sin el success swal("Correcto!","Retencion guardada correctamente","success");
                    swal({
                        type: 'success',
                        title: 'Retencion guardada correctamente',
                        showConfirmButton: false,
                        timer: 3000 // es ms (mili-segundos)
                    })
                    location.href ="{{route('retenciones_index')}}";
                },
                error: function(data){
                    console.log(data);
                }
            })
        }else{
            $("#buscar").next().remove();
            $("#buscar").after('<span class="validationMessage" style="color:red;">Inserte la serie en la factura</span>');
            swal("Error!","Existen campos vacios en la retención","warning");
        } 

    }

</script>


@endsection