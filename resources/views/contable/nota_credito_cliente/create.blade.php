@extends('contable.nota_credito_cliente.base')
@section('action-content')
<style type="text/css">
.ui-autocomplete {
    overflow-x: hidden;
    max-height: 200px;
    width: 1px;
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

.checkmark {
    position: absolute;
    top: 0;
    left: 0;
    height: 25px;
    width: 25px;
    background-color: #eee;
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
.switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
}

    /* Hide default HTML checkbox */
.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>

<script type="text/javascript">
  function goBack() {
    location.href="{{ route('nota_credito_cliente.index') }}";
  }
</script>
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">

<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="{{route('nota_credito_cliente.index')}}">Nota Crédito Cliente</a></li>
            <li class="breadcrumb-item active" aria-current="page">Crear</li>
        </ol>
    </nav>
    <form class="form-vertical " method="post" id="form_nota_credito_cl">
        {{ csrf_field() }}
        <div class="box">
            <div class="box-header">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-9 col-sm-9 col-6">
                            <!--<div class="box-title "><b>Crear Nota de Crédito Clientes</b></div>-->
                            <h5><b>CREAR NOTA CRÉDITO CLIENTE</b></h5>
                        </div>
                        <div class="col-md-3">
                            <div class="row">
                                <a type="button" id="boton_guardar_nota" href="javascript:guarda_nota_credito()"
                                    class="btn btn-success btn-gray btn-xs"><i class="glyphicon glyphicon-floppy-disk"
                                    aria-hidden="true"></i>&nbsp;&nbsp;Guardar
                                </a>
                                <button type="button" class="btn btn-success btn-xs btn-gray"
                                    onclick="nueva_nota_credito()" style="margin-left: 10px;">
                                    <i class="glyphicon glyphicon-file" aria-hidden="true"></i>&nbsp;&nbsp;Nuevo
                                </button>
                                <button type="button" class="btn btn-success btn-xs btn-gray" onclick="goBack()"
                                    style="margin-left: 10px;">
                                    <i class="glyphicon glyphicon-arrow-left"
                                        aria-hidden="true"></i>&nbsp;&nbsp;Regresar
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
                            <!--<div class=" col-md-12">
                                <label for="buscar" class="control-label">Buscar:</label>
                                <input type="text" id="buscar" name="buscar" class="form-control-sm buscar  px0"
                                    onchange="buscar_factura()">
                            </div>-->
                            <div class=" col-md-1 px-1">
                                <label class="label_header">Estado</label>
                                <div style="background-color: green; " class="form-control col-md-1"></div>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="col-md-12 label_header" for="id_nota_credito">ID:</label>
                                <input class="form-control " type="text" name="id_nota_credito" id="id_nota_credito" readonly>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="label_header" for="numero_secuencia">Número:</label>
                                <input class="form-control " type="text" id="numero_secuencia" name="numero_secuencia"
                                    readonly>
                            </div>
                            <div class=" col-md-1 px-1">
                                <label class="col-md-12 label_header" for="tipo">Tipo:</label>
                                <input class="form-control " type="text" name="tipo" id="tipo" value="CLI-CR" readonly>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="label_header" for="asiento">Asiento:</label>
                                <input class="form-control " type="text" id="num_asiento" name="num_asiento" readonly>
                                @if(!is_null($iva_param))
                                    <input type="text" name="iva_par" id="iva_par" class="hidden"
                                    value="{{$iva_param->iva}}">
                                @endif
                            </div>
                            <div class="col-md-2 px-1">
                                <label class="col-md-12 label_header" for="fecha_hoy">Fecha: </label>
                                <input class="form-control " type="date" name="fecha_hoy" id="fecha_hoy"
                                    value="{{date('Y-m-d')}}">
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="col-md-12 label_header" for="nro_factura">No. Factura: </label>
                                <input class="form-control " type="text" name="nro_factura" id="nro_factura" readonly autocomplete="off">
                            </div>
                        </div>
                        




                        <div class="form-group col-xs-6  col-md-1 px-1">
                            <div class="col-md-12 px-0">
                                <label for="empresa" class="label_header">Electrónica</label>
                            </div>
                            <div class="col-md-12 px-0">
                                <label class="switch">
                                <input  class="electros" @if($empresa->electronica==1)  @else disabled @endif  id="toggleswitch" type="checkbox">
                                <span class="slider round"></span>
                                <input type="hidden" id="electronica" name="electronica" value="0">
                                </label>
                            </div>
                        </div>






                        <div class="col-md-7 px-1">
                            <input type="hidden" name="total_suma" id="total_suma">
                            <label class="label_header" for="concepto">Concepto:</label>
                            <input class="form-control  col-md-12" type="text" name="concepto" id="concepto" autocomplete="off">
                        </div>

                        <div class="col-md-2 px-1">
                            <label class="label_header" for="sucursal">Sucursal:</label>
                            <div class="col-md-12 px-0">
                                <select class="form-control" name="sucursal" id="sucursal" onchange="obtener_caja()" required>
                                    <option value="">Seleccione...</option>
                                    @foreach($sucursales as $value)    
                                        <option value="{{$value->id}}">{{$value->codigo_sucursal}}</option>
                                    @endforeach    
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 px-1">
                            <label class="label_header" for="punto_emision">Punto de Emision:</label>
                            <div class="col-md-12 px-0">
                                <select class="form-control" name="punto_emision" id="punto_emision" required>
                                  <option value="">Seleccione...</option> 
                                </select>
                            </div>
                        </div>
                        <div style="margin-left: -8%;" class=" col-md-4 px-0">
                            <label class="col-md-12 label_header" for="cliente">Cliente: </label>
                            <input type="text" id="id_cliente" name="id_cliente"
                                class="form-control form-control-sm id_cliente  col-md-12">
                        </div>
                        <div class="col-md-3 px-0">
                            <label class="col-md-12 label_header" for="nombre_cliente">&nbsp;</label>
                            <input type="text" id="nombre_cliente" name="nombre_cliente"
                                class="form-control form-control-sm nombre_cliente ">
                        </div>
                        <div class="col-md-2 px-0">
                            <label class="label_header">Secuencial</label>
                            <input type="text" class="form-control" name="secuencial" id="secuencial" placeholder="Ingrese secuencial">
                        </div>
                        <div class="col-md-2 px-0" style="padding-top: 13px">
                          <span style="font-family: 'Helvetica general';font-size: 16px;color: black;padding-left: 15px;">Archivo del SRI</span>
                          <input style="width:17px;height:17px;padding-top:13px" type="checkbox" id="check_archivo_sri" class="flat-green" name="check_archivo_sri" value="1"
                          @if(old('check_archivo_sri')=="1")
                            checked
                          @endif>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <label for="detalle_deuda">DETALLE DE RUBROS DE CRÉDITO</label>
                </div>
                <div class="col-md-12">
                    <input type="hidden" name="contador" id="contador" value="0">
                    <input type="hidden" name="contador_a" id="contador_a" value="0">
                    <div class="table-responsive col-md-12 ">
                        <table id="example3" class="table-bordered table-hover dataTable table-striped" role="grid"
                            aria-describedby="example2_info">
                            <thead style="background-color: #9E9E9E; color: white;">
                                <tr style="position: relative;">
                                    <th style="width: 10%; text-align: center;">Código</th>
                                    <th style="width: 12%; text-align: center;">Rubro</th>
                                    <th style="width: 20%; text-align: center;">Detalle</th>
                                    <th style="width: 8%; text-align: center;">Divisa</th>
                                    <th style="width: 8%; text-align: center;">Valor</th>
                                    <th style="width: 8%; text-align: center;">Total Base</th>
                                    <th style="width: 1%; text-align: center;">
                                        <button onclick="crea_td();" type="button"
                                            class="btn btn-success btn-gray btn-xs">
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
                            <div class="col-md-6">
                                &nbsp;
                            </div>
                            <div class="col-md-2 px-1">
                                <label class="label_header" for="subtotal">Subtotal:</label>
                                <input class="form-control  col-md-12" type="text" name="subtotal" id="subtotal" value='0.00' readonly>
                            </div>
                            <div class="col-md-2 px-1">
                                <label class="label_header" for="impuesto">Impuesto:</label>
                                <input class="form-control  col-md-12" type="text" name="impuesto" id="impuesto" value='0.00' onchange="sumar_impuesto()" autocomplete="off">
                            </div>
                            <div class="col-md-2 px-1">
                                <label class="label_header" for="total">Total Crédito:</label>
                                <input class="form-control  col-md-12" type="text" name="total" id="total" value='0.00'>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12" style="top: 20px;">
                    <label for="detalle_deuda">DETALLE DE DEUDAS DEL CLIENTE</label>
                </div>
                <div class="col-md-12">
                    <input type="hidden" name="tar_iva_12" id="tar_iva_12">
                    <input type="hidden" name="sub_sin_imp" id="sub_sin_imp">
                    <input type="hidden" name="id_asiento" id="id_asiento">
                    <input type="hidden" name="total_suma_a" id="total_suma_a">
                    <div class="table-responsive col-md-12 " style="min-height: 250px; max-height: 250px; top: 20px;">
                        <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid"
                            aria-describedby="example2_info">
                            <thead style="background-color: #9E9E9E; color: white;">
                                <tr style="position: relative;">
                                    <!--<th style="width: 10%; text-align: center;">DI</th>-->
                                    <th style="width: 10%; text-align: center;">Emisión</th>
                                    <th style="width: 10%; text-align: center;">Vence</th>
                                    <th style="width: 5%; text-align: center;">Tipo</th>
                                    <th style="width: 15%; text-align: center;">Numero</th>
                                    <th style="width: 25%; text-align: center;">Concepto</th>
                                    <th style="width: 5%; text-align: center;">Div</th>
                                    <th style="width: 10%; text-align: center;">Saldo</th>
                                    <th style="width: 10%; text-align: center;">Abono</th>
                                    <th style="width: 10%; text-align: center;">Nuevo Saldo</th>
                                </tr>
                            </thead>
                            <tbody id="crear">
                                @php $cont=0; @endphp
                                @foreach (range(1, 6) as $i)
                                <tr>
                                    <td> <input class="form-control" type="text" name="emision{{$cont}}"
                                            id="emision{{$cont}}" readonly> </td>
                                    <td> <input class="form-control" type="text" name="vence{{$cont}}"
                                            id="vence{{$cont}}" readonly> </td>
                                    <td> <input class="form-control" type="text" name="tipo{{$cont}}" id="tipo{{$cont}}"
                                            readonly> </td>
                                    <td> <input class="form-control" type="text" name="numero{{$cont}}"
                                            id="numero{{$cont}}" readonly> </td>
                                    <td> <input class="form-control" type="text" name="concepto{{$cont}}"
                                            id="concepto{{$cont}}" readonly> </td>
                                    <td> <input class="form-control" type="text" name="div{{$cont}}" id="div{{$cont}}" value="$" readonly> </td>
                                    <td> <input class="form-control" type="text" name="saldo{{$cont}}" id="saldo{{$cont}}" readonly> </td>
                                    <td> <input class="form-control" style="background-color: #c9ffe5; text-align: center;" type="text"
                                            name="abono{{$cont}}" id="abono{{$cont}}" readonly></td>
                                    <td> <input class="form-control" style="width: 75%" type="text" name="nuevo_saldo{{$cont}}" id="nuevo_saldo{{$cont}}" readonly></td>
                                </tr>
                                @php $cont = $cont +1; @endphp
                                @endforeach
                            </tbody>
                            <tfoot>
                              
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="col-md-12" style="margin-top: 30px;padding-left: 30px">
                    <div class=" col-md-2 px-1">
                      <label for="total_deudas">Total Deudas:</label>
                      <input class="form-control " style="align:left;" type="text" name="total_deudas" id="total_deudas" value='0.00' onkeypress="return isNumberKey(event)" onblur="checkformat(this);">
                    </div>
                    <div class=" col-md-2 px-1">
                      <label for="total_credito">Total Crédito:</label>
                      <input class="form-control " type="text" name="total_credito" id="total_credito" value='0.00'>
                    </div>
                    <div class=" col-md-2 px-1">
                      <label for="total_abonos">Total Abonos:</label>
                      <input class="form-control " type="text" name="total_abonos" id="total_abonos" value='0.00'>
                    </div>
                    <div class=" col-md-2 px-1">
                      <label for="total_nuevo_saldo">Nuevo Saldo:</label>
                      <input class="form-control " type="text" name="total_nuevo_saldo" id="total_nuevo_saldo" value='0.00'>
                    </div>
                    <div class=" col-md-2 px-1">
                      <label for="deficit_ingreso">Deficit de Ingresos:</label>
                      <input class="form-control " type="text" name="deficit_ingreso" id="deficit_ingreso" value='0.00' readonly>
                    </div>
                    <div class=" col-md-2 px-1">
                      <label for="superavit_favor">Superavit a favor:</label>
                      <input class="form-control " type="text" name="superavit_favor" id="superavit_favor" value='0.00' readonly>
                    </div>
                </div>
                <div class="col-md-12" style="padding-left: 30px">
                    <label  for="observaciones">Observaciones</label>
                    <textarea class="col-md-12" name="observaciones" id="observaciones" cols="150" rows="3"></textarea>
                </div>
            </div>
        </div>
    </form>
</section>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>



<script type="text/javascript">
    
        var input = document.getElementById('toggleswitch');
        //var outputtext = document.getElementById('status');
        let electronica = document.getElementById('electronica').value;
        // if(input == false){
        //     electronica.value = 0;
        // }else{
        //     electronica.value= 1;
        // }

        input.addEventListener('change', function() {
            if (this.checked) {
               $("#electronica").val(1);
            } else {
               $("#electronica").val(0);
               
            }
        });
    
    
</script>


<script type="text/javascript">

    $(document).ready(function(){
     
      $('#check_archivo_sri').attr('checked', true);

    });

    //BUSQUEDA POR IDENTIFICACION
    $(document).on("focus","#id_cliente",function(){

        $("#id_cliente").autocomplete({

            source: function( request, response ) {
                $.ajax( {
                type: 'GET',
                url: "{{route('notacredito.buscarclientexid')}}",
                dataType: "json",
                data: {term: request.term},
                success: function( data ) {
                    response(data);
                }
                } );
            },
            change:function(event, ui){
                $("#nombre_cliente").val(ui.item.nombre);
                  buscar_deudas_cliente();
                  suma_deudas_cliente();
                //obtener_total_deudas();
            },
            selectFirst: true,
            minLength: 1,
        
        });

    });

    //BUSQUEDA DE FACTURA POR ID CLIENTE Y EMPRESA
    $(document).on("focus","#nro_factura",function(){

        $("#nro_factura").autocomplete({

            source: function( request, response ) {
                $.ajax( {
                  type: 'GET',
                  url: "{{route('notacredito.obtener_num_fact')}}",
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
                $("#id_asiento").val(ui.item.num_asiento);
                $("#id_cliente").val(ui.item.cliente);
                $("#nombre_cliente").val(ui.item.nomb_client);
               obtener_detalle_deudas();
               obtener_total_deudas();
            },
            selectFirst: true,
            minLength: 1,
        });

    });

    //Detalle de Deudas del Cliente
    function obtener_detalle_deudas(){
        
        var id_client = $("#id_cliente").val();
        var num_fact = $("#nro_factura").val();

        $.ajax({
            type: "post",
            url: "{{route('nota_credito_cliente.deudas')}}", 
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: "json",
            data:{'id_cliente':id_client,'num_fact':num_fact},
            success: function(data){
               console.log(data);
                if(data.value!="false"){
                    $("#crear").empty();
                    var fila = 0;
                    var total = 0; 
                    for(i=0; i<data[4].length;i++){
                        console.log(i);
                        var row =addNewRow(i, data[4][i].fecha, data[4][i].valor_contable, 'VEN-FA', data[4][i].numero, data[4][i].nro_comprobante, data[4][i].valor_contable,data[4][i].id);
                        $('#example2').append(row);
                        fila = i;
                        //total = total+data[4][i].valor_contable;
                    }
                    $("#contador_a").val(fila);
                    //$("#total_deudas").val(total.toFixed(2));
                  
                }else{
                    $("#crear").empty();
                    $("#total_deudas").val('0.00');
                    $("#total_abonos").val('0.00');
                    $("#total_nuevo_saldo").val('0.00');
                }
            },
            error:  function(data){
               console.log(data);             
            }
        });

    }


    //Obtener Suma Total Deuda
    function obtener_total_deudas()
    {
        var id_client = $("#id_cliente").val();
        var num_fact = $("#nro_factura").val();
        
        $.ajax({
            type: "post",
            url: "{{route('obtener_total_deudas.clientes')}}", 
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: "json",
            data:{'id_cliente':id_client,'num_fact':num_fact},
            success: function(data){
               //console.log(data);
               if(data.value!="false"){
                $("#total_deudas").val(data.total_deuda);
               }else{
                $("#total_deudas").val('0.00');
               }
            },
            error:  function(data){
               console.log(data);             
            }
        });

    }

    function buscar_deudas_cliente(){

        var ced_cliente = $("#id_cliente").val();

        $.ajax({
            type: "post",
            url: "{{route('nota_cred_deudas.buscar')}}", 
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: "json",
            data:{'id_cliente':ced_cliente},
            success: function(data){
               //console.log(data);
                if(data.value!="false"){
                    $("#crear").empty();
                    var fila = 0;
                    var total = 0; 
                    for(i=0; i<data[4].length;i++){
                        var row =addNewRow(i, data[4][i].fecha, data[4][i].valor_contable, 'VEN-FA', data[4][i].numero, data[4][i].nro_comprobante, data[4][i].valor_contable,data[4][i].id);
                        $('#example2').append(row);
                        fila = i;
                        //total = total+data[4][i].valor_contable;
                    }
                    $("#contador_a").val(fila);
                    //$("#total_deudas").val(total.toFixed(2));
                  
                }else{
                    $("#crear").empty();
                    $("#total_deudas").val('0.00');
                    $("#total_abonos").val('0.00');
                    $("#total_nuevo_saldo").val('0.00');
                }
            },
            error:  function(data){
               console.log(data);             
            }
        });

    }

    function suma_deudas_cliente()
    {
        var id_client = $("#id_cliente").val();

        $.ajax({
            type: "post",
            url: "{{route('suma_deudas.clientes')}}", 
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: "json",
            data:{'id_cliente':id_client},
            success: function(data){
               if(data.value!="false"){
                $("#total_deudas").val(data.total_deuda);
               }else{
                $("#total_deudas").val('0.00');
               }
            },
            error:  function(data){
               console.log(data);             
            }
        });
     
    }

    //Calcula Total Deuda
    /*function obtener_total_deuda(contador){
        var total_deuda = 0;

        total_deuda = total_deuda+contador;
        alert(total);

        return total_deuda;

    }*/

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


    function isNumberKey(evt)
    {
     var charCode = (evt.which) ? evt.which : event.keyCode
     if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
        return false;

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
            if(!isNaN(s)){$('#tar_iva_12').val(s.toFixed(2));}
            $("#total_credito").val(total.toFixed(2,2));
        }else{
            total=subtotal;
            impuesto=0;
            $("#impuesto").val(impuesto.toFixed(2,2));
            $("#total").val(total.toFixed(2,2));
            if(!isNaN(s)){$('#tar_iva_12').val(s.toFixed(2));}
            $("#total_credito").val(total.toFixed(2,2));
           
        }
    }

    //Calcular Total Credito Ingresando Impuesto
    /*function calculartotalcredito(){

        var sb12 = 0;

        var subtotal = parseFloat($("#subtotal").val());
        var impuesto = parseFloat($("#impuesto").val());
        
        var totalimp = subtotal*impuesto;
        var total_final = subtotal+totalimp; 
       
        if(!isNaN(total_final)){ $('#total').val(total_final.toFixed(2));}
        if(!isNaN(totalimp)){ $('#tar_iva_12').val(totalimp.toFixed(2));}
        $("#total_credito").val(total_final.toFixed(2,2));

    }*/

    //ANADIR REGISTRO A LA TABLA DETALLE DE DEUDAS DEL CLIENTE
    function addNewRow(pos,fecha, valor, factura, fact_numero, observacion, valor_nuevo,id_final){
        var markup = "";
        var num= parseInt(pos)+1;
        //var fech_emision = fecha;
        //var fech_emis_inver = convertDateFormat(fech_emision);  

        markup = "<tr>"+
                "<td> <input class='form-control' type='text' name='emision"+pos+"' id='emision"+pos+"' readonly='' value='"+fecha+"'> <input type='hidden' name='id_actualiza"+pos+"' id='id_actualiza"+pos+"' value='"+id_final+"'></td>"+
                "<td> <input class='form-control' type='text' name='vence"+pos+"' id='vence"+pos+"' value='"+fecha+"' readonly=''> </td>"+ "<td> <input class='form-control' type='text' name='tipo_a"+pos+"' id='tipo_a"+pos+"' value='VEN-FA' readonly=''> </td>"+
                "<td> <input class='form-control' type='text' name='numero"+pos+"' id='numero"+pos+"' value='"+observacion+"' readonly=''> </td>"+
                "<td> <input class='form-control' type='text' name='observacion"+pos+"' id='observacion"+pos+"' value='Fact:"+fact_numero+" Ref: "+observacion+"' readonly=''> </td>"+
                "<td> <input class='form-control' type='text' name='div"+pos+"' id='div"+pos+"' value='$' readonly=''> </td>"+
                "<td> <input class='form-control' type='text' name='saldo_a"+pos+"' value='"+valor+"' id='saldo_a"+pos+"' readonly=''> </td>"+
                "<td> <input class='form-control' type='text' style='background-color: #c9ffe5;' name='abono_a"+pos+"' id='abono_a"+pos+"' value='0.00' onchange='validar_td2("+pos+")'></td>"+
                "<td> <input style='width: 77%;' class='form-control' type='text' style=' text-align: left;' name='nuevo_saldo"+pos+"' value='0.00' id='nuevo_saldo"+pos+"' readonly=''></td>"+
            "</tr>";
        return markup;
    }

    function convertDateFormat(string) {
        var info = string.split('-');
        return info[2] + '/' + info[1] + '/' + info[0];
    }

    //BUSQUEDA POR NOMBRE
    $(document).on("focus","#nombre_cliente",function() {
        
        $("#nombre_cliente").autocomplete({

            source: function( request, response ) {
                $.ajax( {
                  type: 'GET',
                  url: "{{route('notacredito.buscarcliente')}}",
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
                $("#id_cliente").val(ui.item.id);
                buscar_deudas_cliente();
                suma_deudas_cliente();
            },
            selectFirst: true,
            minLength: 1,
        } );

    });

    //ANADIR DETALLE DE RUBROS DE CREDITO
    function crea_td(contador){

        id = document.getElementById('contador').value;
        var midiv = document.createElement("tr");
        midiv.setAttribute("id", "dato" + id);

        //Creamos tabla temporal
        midiv.innerHTML = '<td><input required type="hidden" id="visibilidad'+id+'" name="visibilidad'+id+'" value="1"><input style="width: 98%;" onchange="set_codigo('+id+')" class="codigo" name="codigo' +id + '" id="codigo' + id +'"></td><td> <input style="width: 98%;" name="rubro' + id + '" id="rubro' + id +'" onchange="set_rubros('+id+')" class="rubro"></td><td><input style="width: 98%;" name="detalle' + id +'" id="detalle' + id +'" autocomplete="off"></td><td><input style="width: 98%;background-color: #c9ffe5" name="divisas" id="divisas" value="USD" readonly></td><td><input name="valor' +id + '" style="width: 98%;background-color: #c9ffe5" id="valor' + id + '" onchange="valor_rubro('+id+')" value="0.00" autocomplete="off"></td><td><input style="width: 90%;" name="total_base' + id + '" id="total_base' + id +'" value="0.00" readonly></td><td style="width: 40px;"><button type="button" onclick="eliminar_det_rubro('+id+')" class="btn btn-light"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';

        document.getElementById('det_recibido').appendChild(midiv);
        id = parseInt(id);
        id = id + 1;
        
        document.getElementById('contador').value = id;
        //Completa el codigo detalle rubro credito
        $(".codigo").autocomplete({
            source: function( request, response ) {
                $.ajax( {
                url: "{{route('rubros_cliente.codigo')}}",
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
        //Completa el nombre detalle rubro credito
        $(".rubro").autocomplete({
        source: function( request, response ) {
            $.ajax( {
            type: 'post',
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            url: "{{route('rubros_cliente.nombre')}}",
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

    }

    //ELIMINA REGISTRO DETALLE RUBRO CREDITO
    function eliminar_det_rubro(valor){
        
        var dato_item1 = "dato" + valor;
        var dato_item2 = "visibilidad" + valor;
        document.getElementById(dato_item1).style.display = 'none';
        document.getElementById(dato_item2).value = 0;
        //var contad = document.getElementById('contador').value;
        //contad = contad-1;
        //$("#contador").val(contad);
        sumar();
    }


    //SETEA NOMBRE A INGRESAR CODIGO
    function set_codigo(id){
        $.ajax({
            type: 'post',
            url:"{{route('rubros_cliente.codigo2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'codigo': $("#codigo"+id).val()},
            success: function(data){
                if(data.value!='no'){    
                    $("#rubro"+id).val(data[0]);
                }
            },
            error: function(data){
                console.log(data);
            }
        })
    }

    //SETEA CODIGO A INGRESAR NOMBRE
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
                   $("#codigo"+id).val(data[0]);
                }
            },
            error: function(data){
                console.log(data);
            }
        })
    }


    //GUARDA NOTA DE CREDITO CLIENTES
    function guarda_nota_credito(){
         let boton = document.getElementById('boton_guardar_nota');

            boton.style.display = "none"
       
        var formulario = document.forms["form_nota_credito_cl"];
        var concepto= formulario.concepto.value;
        var id_cliente= formulario.id_cliente.value;
        var nomb_cliente= formulario.nombre_cliente.value;
        var numero_factura= formulario.nro_factura.value;
        var sucursal = formulario.sucursal.value;
        var sumas= parseFloat($("#total_suma_a").val());
        
        var cont = formulario.contador.value;
        //var contador_a= formulario.contador_a.value;
        var total_credito= parseFloat($("#total").val());

        let electr = document.getElementById('toggleswitch').checked;
        let fact =0;
       
        //alert(electr);
        

        if(isNaN(sumas)){
            sumas = 0;
            
        }

        //var cont_tabla = document.getElementById('contador').value;
        var msj = "";
        if(concepto==""){
            msj+="Por favor ingrese el Concepto. <br>";
        }
        if(id_cliente==""){
            msj+="Por favor ingrese el Número de Identificación Cliente. <br>";
        }
        if(nomb_cliente==""){
            msj+="Por favor ingrese el Nombre del Cliente. <br>";
        }
        if(sucursal==""){
            msj+="Por favor Seleccione la Sucursal. <br>";
        }
        if(cont == 0){
            msj+="Por favor, Llene los datos Tabla Detalle Rubro Crédito. <br>";
        }
        /*if((sumas ==0)||(sumas<total_credito)){
            msj+="Por favor, La suma de los abonos debe ser igual al Total de Crédito.<br/>";
        }*/
        ///////aqui ando ¿
        if(msj==""){
           
                $.ajax({
                    type: 'post',
                    url:"{{route('nota_credito_cliente.store')}}",
                    headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                    datatype: 'json',
                    data:$('#form_nota_credito_cl').serialize(),
                    success: function(data){
                        console.log(data); 
                        if((data)!='false'){
                            swal("Correcto!","Nota de Crédito generada con exito","success");
                            $('#form_nota_credito_cl input').attr('readonly', 'readonly');
                           // $("#boton_guardar_nota").attr("disabled", true);
                            buscar_parametros(data);

                        }               
                    },
                    error: function(data){
                          boton.style.display = "block"
                        console.log(data);
                    }
                })
            
        }else{
             $('#boton_guardar_nota').removeAttr("disabled");
           swal("Error!",msj,"error");
        }

    }

    //VERIFICA QUE INGRESE VALORES EN LA TABLA DETALLE DE RUBROS CREDITO
    /*function validar_ing_det_rub(){

        cont_tabla = document.getElementById('contador').value;
        
        if(cont_tabla == 0){
            swal("¡Error!","Ingrese datos en la tabla","error");
        }
    
    }*/
    

    //SETEA VALORES VALOR,TOTAL BASE,SUBTOTAL
    function valor_rubro(id){
        
        var e= parseFloat($("#valor"+id).val());
        
        if(e==""){
           e=0;
        }
        if(isNaN(e)){
            e=0;
        }
        
        $("#valor"+id).val(parseFloat(e).toFixed(2,2));
        $("#total_base"+id).val(parseFloat(e).toFixed(2,2));
        $("#subtotal").val(parseFloat(e).toFixed(2,2));
        
        sumar();
        //sumar2();
        
    }

    //Funcion Sumar 
    function sumar(){
        
        var contador  =  0;
        var iva= parseFloat($("#iva_par").val());
        var ivan=0;
        //var total=0;
        //var totaal=0;
        var sub=0;
        var valor_d=0;
        var ivaf=0;

        $("#det_recibido tr").each(function(){
            $(this).find('td')[0];
            visibilidad = $(this).find('#visibilidad'+contador).val();

            if(visibilidad == 1){
              valor = parseFloat($(this).find('#valor'+contador).val());
              ivan = ivan + valor; 
              sub = sub + valor;
              //totaal= valor+ivan;
              //total= total+totaal; 
            }
            contador = contador+1;
        });

        //var totalsx = ivan*iva;
        var totalsx = ivan;
        //var total_final= totalsx+ivan; 
        var total_final= totalsx; 
        //if(!isNaN(totalsx)){ $('#impuesto').val(totalsx.toFixed(2));}
        //subtotal->Subtotal 0%
        if(!isNaN(sub)){ $('#subtotal').val(sub.toFixed(2));}
        if(!isNaN(total_final)){ $('#total').val(total_final.toFixed(2));}
        if(!isNaN(total_final)){ $("#total_credito").val(total_final.toFixed(2,2));}
        if(!isNaN(sub)){ $('#sub_sin_imp').val(sub.toFixed(2));}
        $('#impuesto').val('0.00');
        /*if(total_d>0){
            if(!isNaN(sub)){ $('#subtotal').val(sub.toFixed(2));   }
            if(!isNaN(total_final)){ $('#total').val(total_final.toFixed(2));   }
            if(!isNaN(total_final)){ $("#total_credito").val(total_final.toFixed(2,2));}
            if(total_final > total_d){
                swal("¡Error!","El Total de Crédito no debe superar al Total de la Deuda","error")
                $('#subtotal').val('0.00');
                $('#impuesto').val('0.00');
                $('#total').val('0.00');
            }
        }*/
    }

    //Funcion Sumar 2 
    function sumar2(){
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
            var totalsx= ivan*iva;
            var total_final= totalsx+ivan;
            if(!isNaN(total)){ $('#total_suma').val(total_final.toFixed(2));}
            //$("#total_credito").val(total.toFixed(2,2));
    }


    //BUSCAR PARAMETRO ID - NUMERO SECUENCIA - NUMERTO ASIENTO
    function buscar_parametros(id_nota_cred){

        $.ajax({
            type: 'post',
            url:"{{route('nota_credito_cliente.buscarparametros')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'id_nota': id_nota_cred},
            success: function(data){
                //console.log(data);
                $("#id_nota_credito").val(data.id_nota);
                $("#numero_secuencia").val(data.secuencia);
                $("#num_asiento").val(data.id_asiento);
            },
            error: function(data){
                console.log(data);
            }
        });
    
    }

    //CREACION NUEVA NOTA DE CREDITO
    function nueva_nota_credito(){
        location.href ="{{route('nota_credito_cliente.create')}}";
    }

    //VALIDAR ABONO
    function validar_td2(id){
        
        if((id)!=null){
           var valor= parseFloat($("#total").val());
           var saldo= parseFloat($("#saldo_a"+id).val());
           var abono= parseFloat($("#abono_a"+id).val());
           //var total_deuda = 0;
           suma_totales2()
           var cantidad= parseFloat($("#total_suma_a").val());
                
            if(valor > 0){ 
                if(!isNaN(abono) && !isNaN(valor) && !isNaN(cantidad)){

                    if(cantidad<=valor){

                        if(abono>saldo){
                            swal("¡Error!","El Abono no debe superar al Saldo","error")
                            //$("#abono_a"+id).val('0.00');
                            $("#abono_a"+id).val(saldo.toFixed(2,2));
                            suma_totales2();
                            //abono=saldo;
                        }else{
                          $("#abono_a"+id).val(abono.toFixed(2,2));
                          nuevo_saldo = saldo-abono;
                          $("#nuevo_saldo"+id).val(nuevo_saldo.toFixed(2,2));
                            suma_totales3();
                          //total_deuda = total_deuda+saldo;
                          //$("#total_deudas").val(total_deuda.toFixed(2));
                        }
                    
                    }else{
                      valor = 0;
                      //$("#abono_a"+id).val(valor.toFixed(2,2));
                      $("#abono_a"+id).val('0.00');
                      swal("¡Error!","La Suma de los Abonos no debe superar al Total Crédito","error")
                      
                    }
                
                }else{
                    abono=0;
                    valor=0;
                    $("#abono_a"+id).val(valor.toFixed(2,2));
                    

                }
            }else{
               swal("¡Error!","Debe calcular el Total Crédito","error")
               $("#abono_a"+id).val('0.00');

            }   
        }

    }


    function suma_totales2(){
        contador  =  0;
        cantidad = 0;
        total = 0;
        $("#crear tr").each(function(){
            $(this).find('td')[0];
            cantidad= parseFloat($("#abono_a"+contador).val());

            if(!isNaN(cantidad)){
                total+=cantidad;
            }

            contador = contador+1;
        
        });

        if(isNaN(total)){ 
            total=0;
        }
        
        $("#total_suma_a").val(total.toFixed(2,2));
        $("#total_abonos").val(total.toFixed(2,2));

    }

    function suma_totales3(){

        contador  =  0;
        cantidad = 0;
        total = 0;

        $("#crear tr").each(function(){
            $(this).find('td')[0];
            cantidad= parseFloat($("#nuevo_saldo"+contador).val());

            if(!isNaN(cantidad)){
                total+=cantidad;
            }

            contador = contador+1;

        });

        if(isNaN(total)){ 
            total=0;
        }

        $("#total_nuevo_saldo").val(total.toFixed(2,2));

    }

    //Obtener Caja Sucursal
    function obtener_caja(){

       var id_sucursal = $("#sucursal").val();

        $.ajax({
            type: 'post',
            url:"{{route('caja.sucursal')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'id_sucur': id_sucursal},
            success: function(data){
                //console.log(data);

                if(data.value!='no'){
                    if(id_sucursal!=0){
                        $("#punto_emision").empty();

                        $.each(data,function(key, registro) {
                            $("#punto_emision").append('<option value='+registro.id+'>'+registro.codigo_sucursal+'-'+registro.codigo_caja+'</option>');

                        });
                    }else{
                        $("#punto_emision").empty();

                    }

                }
            },
            error: function(data){
                console.log(data);
            }
        })

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

</script>

@endsection