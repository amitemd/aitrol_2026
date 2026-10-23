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
        location.href="{{route('retencion.cliente')}}";
    }
</script>
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="#">Retenciones Clientes</a></li>
        <li class="breadcrumb-item"><a href="../retenciones">Retenciones</a></li>
        <li class="breadcrumb-item active" aria-current="page">Nueva Retención</li>
      </ol>
    </nav>
        <form class="form-vertical" method="post" id="form_guardado">
            <div class="box" style="background-color: #BCB8B0;">
                    <div class="box-header header_new">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-9 col-sm-9 col-6">
                                    <div class="box-title" ><b>CLIENTES-COMP. DE RETENCIONES</b></div>
                                </div>
                                <div class="col-md-3">
                                    <div class="row">
                                        <button type="button" onclick="guardar_retenciones()" id="boton_guardar" class="btn btn-success btn-gray btn-xs"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
                                        </button>
                                        <button type="button" class="btn btn-success btn-xs btn-gray" onclick="nuevo_comprobante()" style="margin-left: 10px;">
                                                <i class="glyphicon glyphicon-file" aria-hidden="true"></i>&nbsp;&nbsp;Nuevo
                                        </button>

                                        <button type="button" onclick="goBack()" class="btn btn-success btn-xs btn-gray">
                                        <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box-body  dobra">
                        <div class="col-12 col-xs-12">
                            <div class="form-row">                             
                                <form action="" id="form_guardado" method="post">
                                    <input type="text" name="cont" id="cont" value="1" class="hidden">
                                    <div class="row header">
                                       <div class="form-group col-md-12 px-0">
                                            <label class="control-label">BUSCADOR POR FACTURA </label>
                                        </div>
                                        <div class="form-group col-md-6 px-0">
                                            <label for="buscar" class = "control-label col-md-4 label_header">Buscar</label>
                                            <input type="text" id="buscar" name="buscar" class="form-control buscar" onchange="buscar_factura()">
                                        </div>
                                        <div class="form-group col-md-3 px-0">
                                            <label class="control-label label_header" >Buscar Cliente</label>
                                            <select class="form-control select2" name="id_clientex" onchange="grupo_clientes()" id="id_clientex">
                                                <option value="">Seleccione</option>
                                                @foreach($clientes as $value)
                                                    <option value="{{$value->identificacion}}">{{$value->nombre}}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                        <div class="form-group col-md-3 px-0">
                                            <label class="control-label label_header">&nbsp;</label>
                                            <select class="form-control select2" style="width:100%;" name="facturas" id="facturas" onchange="buscar_factura2()">

                                            </select>
                                        </div>
                                        <div class="form-group col-md-6 ">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label class="control-label ">DATOS DE LA FACTURA</label>
                                                </div>
                                                <div class="col-md-3 px-1">  
                                                    <label class="control-label label_header">SECUENCIAL</label>
                                                    <input type="text" class="form-control " name="secuencial" id="secuencial" >
                                                </div>
                                                <div class="col-md-3 px-1">
                                                    <label class="control-label label_header">N° AUTORIZACIÓN</label>
                                                    <input type="text" class="form-control " name="nro_autorizacion" id="nro_autorizacion">
                                                </div>
                                                <div class="col-md-6 px-1">
                                                    <label class="control-label label_header">VALOR DE LA FACTURA </label>
                                                    <input type="text" class="form-control " name="valor_factura" id="valor_factura" readonly>
                                                </div>
                                                <div class="col-md-3 px-1">
                                                    <label class="control-label label_header">IDENTIFICACIÓN</label>
                                                    <input type="text" class="form-control " name="cliente" id="cliente" readonly>
                                                </div>
                                                <div class="col-md-9 px-1"> 
                                                    <label class="control-label label_header">CLIENTE</label>
                                                    <input type="text" class="form-control " name="nombre_cliente" id="nombre_cliente" readonly>
                                                </div>
                                                <div class="col-md-12 px-1">
                                                    <label class="control-label label_header">CONCEPTO</label>
                                                    <input type="text" class="form-control " name="concepto" id="concepto" readonly>
                                                </div>
                                                <div class="col-md-6 px-1">
                                                    <label class="control-label label_header">FECHA AUTORIZACION</label>
                                                    <input type="date" class="form-control " name="fecha_aut" id="fecha_aut">
                                                </div>
                                                <div class="col-md-6 px-1">
                                                    <label class="control-label label_header">NÚMERO DE REFERENCIA</label>
                                                    <input type="text" class="form-control " name="numerorf" id="numerorf" >
                                                </div>

                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <div class="row ">
                                                <div class="col-md-12">
                                                    <label class="control-label">DATOS DE LA RETENCIÓN</label>
                                                </div>
                                                <div class="col-md-6 px-1">
                                                    <label class="control-label label_header">Tipo Retención</label>
                                                        <select class="form-control " onchange="traer_retenciones()" name="tipo_retencion" id="tipo_retencion">
                                                            <option value="0">Seleccione...</option>
                                                            <option value="2">FUENTE</option>
                                                            <option value="1">IVA</option>
                                                        </select>
                                                </div>
                                                <input type="hidden" name="subtotal_final" id="subtotal_final">
                                                <input type="hidden" name="iva_final" id="iva_final">
                                                <div class="col-md-6 px-1">
                                                    <label class="control-label label_header">PORCENTAJE RETENCIÓN</label>
                                                    <select class="form-control " name="porcentaje_retencionf" onchange="lista_valores(this)" id="porcentaje_retencionf">
                                                    </select>
                                                </div>
                                                <div class="col-md-4 px-1">
                                                    <label class="control-label label_header">CÓDIGO</label>
                                                    <input type="text" class="form-control " name="codigo" id="codigo" readonly>
                                                </div>
                                                <div class="col-md-4 px-1">
                                                    <label class="control-label label_header">BASE RETENCIÓN</label>
                                                    <input class="form-control " type="text" name="base_retencion" id="base_retencion">
                                                </div>
                                                <div class="col-md-4 px-1">
                                                    <label class="control-label label_header">MONTO RETENIDO</label>
                                                    <input type="text" id="monto_retenido" class="form-control " name="monto_retenido" >
                                                    <input type="hidden" name="id_proveedor_modal" id="id_proveedor_modal">
                                                    <input type="hidden" name="retencion_total" id="retencion_total">
                                                </div>
                                                <div class="col-md-12 px-1">
                                                    <label class="control-label col-md-12 label_header">CONCEPTO:</label>
                                                    <input type="text" class="form-control" name="concepto" id="concepto">
                                                    <input type="hidden" name="valor_fuente" id="valor_fuente">
                                                    <input type="hidden" name="valor_iva" id="valor_iva">
                                                    <input type="hidden" name="tipo_rfir" id="tipo_rfir">
                                                    <input type="hidden" name="tipo_rfiva" id="tipo_rfiva">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">          
                                        <div class="table-responsive col-md-12" >
                                                <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                                                    <thead class="cabecera">
                                                    <tr style="position: relative;">
                                                        <th>NÚMERO DE REF</th>
                                                        <th>NÚMERO FACTURA</th>
                                                        <th>FECHA</th>
                                                        <th >BASE IMP</th>
                                                        <th >TIPO</th>
                                                        <th >COD</th>
                                                        <th >% DE RET</th>
                                                        <th >VALOR RETENIDO</th>
                                                        <th >                                                                    
                                                            <button onclick="crea_tds()" type="button" class="btn btn-success btn-xs btn-gray" >
                                                                            <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                                            </button>
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                        <tbody id="datos_a">
                                                        </tbody>
                                                    <tfoot>
                                                    </tfoot>
                                                </table>
                                        </div>
                                    </div>
                                            <input type="hidden" name="cuenta_renta" id="cuenta_renta"> 
                                            <input type="hidden" name="cuenta_iva" id="cuenta_iva">
                                            <input type="hidden" name="eliminados" id="eliminados" value="0">
                                            <input  type="hidden" name="id_proveedor" id="id_proveedor" >
                                            <input type="hidden" name="id_venta" id="id_venta" value="0">
                                            <input type="hidden" name="id_fact_contable" id="id_fact_contable" value="0">
                                </form>
                                <div class="col-md-12" style="margin-top: 10px;">
                                        <div class="form-row">
                                            <div class="form-group col-md-3">
                                                &nbsp;
                                            </div>
                                            <div class="form-group col-md-3">
                                                &nbsp;
                                            </div>
                                            <div class="form-group col-md-3 px-0">
                                                
                                                    <label class="label_header">Total RFIR</label>
                                                    <input class="form-control "  type="text" name="total_rfirt" id="total_rfirt">
                                                
                                            </div>
                                            <div class="form-group col-md-3 px-0">
                                                
                                                    <label for="total_abonos" class="label_header">Total RFIVA</label>
                                                    <input class="form-control "  type="text" name="total_rfivat" id="total_rfivat">
                                                
                                            </div>
                                        </div>
                                    </div>
                                <div class="col-md-12" style="margin-top:20px">
                                    <label class="control-label">DETALLE DE DEUDAS DEL CLIENTE</label>
                                    <input type="hidden" name="total_factura" id="total_factura">

                                </div>
                                <div class="col-12 ">
                                    <div class="table-responsive col-md-12" >                     
                                        <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                                            <thead class='well-dark'>
                                            <tr style="position: relative;">
                                                
                                                <th style="width: 8%; text-align: center;">Vence</th>
                                                <th style="width: 10%; text-align: center;">Tipo</th>
                                                <th style="width: 10%; text-align: center;">Número</th>
                                                <th style="width: 10%; text-align: center;">Concepto</th>
                                                <th style="width: 6%; text-align: center;">Div</th>
                                                <th style="width: 6%; text-align: center;">Saldo</th>
                                                <th style="width: 6%; text-align: center;">Abono</th>
                                                <th style="width: 6%; text-align: center;">Saldo Base</th>
                                            </tr>
                                            </thead>
                                            <tbody id="crear">  
                                            @php $cont=0; @endphp
                                            @foreach (range(1, 4) as $i)
                                                <tr class="well">
                                                    <!--AQUI VA EL DETALLE DE LAS RETENCIONES -->
                                                    <td> <input class="form-control input-sm" type="text" name="vence{{$cont}}" id="vence{{$cont}}" onlyread> </td>
                                                    <td> <input class="form-control input-sm" type="text" name="tipo{{$cont}}" id="tipo{{$cont}}" onlyread> </td>
                                                    <td> <input class="form-control input-sm" type="text" name="numero{{$cont}}" id="numero{{$cont}}" onlyread> </td>
                                                    <td> <input class="form-control input-sm" type="text" name="concepto{{$cont}}" id="concepto{{$cont}}" onlyread> </td>
                                                    <td> <input class="form-control input-sm" style="background-color: #c9ffe5; " type="text" name="div{{$cont}}" id="div{{$cont}}" value="$" onlyread> </td>
                                                    <td> <input class="form-control input-sm" style="background-color: #c9ffe5;" type="text" name="saldo{{$cont}}" id="saldo{{$cont}}" onlyread> </td>
                                                    <td> <input class="form-control input-sm" style="background-color: #c9ffe5;  text-align: center;" type="text" name="abono{{$cont}}" id="abono{{$cont}}" onlyread></td>
                                                    <td> <input class="form-control input-sm" style="text-align: center; width: 85%;" type="text" name="abono_base{{$cont}}" id="abono_base{{$cont}}" onlyread> </td>
                                                </tr>
                                                @php $cont = $cont +1; @endphp
                                            @endforeach
                                                
                                            </tbody>
                                            <tfoot>
                                            </tfoot>
                                        </table>
                                    </div>
                                    
                                    <div class="col-md-12">
                                        <div class="form-row">
                                            <div class="form-group col-md-2 px-0">
                                                
                                                    <label for="total_ingresos" class="label_header">Total Egreso</label>
                                                    <input class="form-control input-sm" type="text" name="total_egreso" id="total_egreso" readonly>
                                                
                                            </div>
                                            <div class="form-group col-md-2 px-0">
                                                
                                                    <label for="credito_aplicado"  class="label_header">Debito Aplicado</label>
                                                    <input class="form-control input-sm" olor: red; text-align: right;" type="text" name="debito_aplicado" value="0.00" id="debito_aplicado" readonly>
                                                
                                            </div>
                                            <div class="form-group col-md-2 px-0">
                                                
                                                    <label for="total_deudas"  class="label_header">Total Deudas</label>
                                                    <input class="form-control input-sm" type="text" name="total_deudas" id="total_deudas" readonly>
                                                
                                            </div>
                                            <div class="form-group col-md-2 px-0">
                                                
                                                    <label for="total_abonos"  class="label_header">Total Abonos</label>
                                                    <input class="form-control input-sm" type="text" name="total_abonos" id="total_abonos" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-2 px-0">
                                                
                                                    <label for="nuevo_saldo"  class="label_header">Nuevo Saldo</label>
                                                    <input class="form-control input-sm" type="text" name="nuevo_saldo" id="nuevo_saldo" readonly>
                                                
                                            </div>
                                            <div class="form-group col-md-2">
                                                    <input type="hidden" name="retencion_fuente" id="retencion_fuente">
                                                    <input type="hidden" name="retencion_ivas" id="retencion_ivas">
                                                    <input type="hidden" name="retencion_totales" id="retencion_totales">
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="col-md-12">
                                    &nbsp;
                                </div>
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <label class="col-md-12 cabecera" for="nota">Nota: </label>
                                        <textarea class="col-md-12 form-control input-sm" name="nota" id="nota" cols="200" rows="5"></textarea>
                                    </div>
                                </div>

                            </div>  
                    </div>
                </div>

    </form>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">
    $(document).ready(function(){

        $('#fact_contable_check').iCheck({
        checkboxClass: 'icheckbox_flat-blue',
        increaseArea: '20%' // optional
        });
        $('.select2').select2({
            tags: false
        });
    });
    function nuevo_comprobante(){
        location.href ="{{route('retenciones.clientes.crear')}}";
    }
    function buscar_factura(){
        $("#buscar").next().remove();
        var validacion= $("#esfac_contable").val();
        $.ajax({
            type: 'post',
            url:"{{route('retenciones.clientes.buscar.codigo')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'id_factura':$("#buscar").val(),'tipo':$("#esfac_contable").val(), 'secuencia':$("#secuencial").val()},
            success: function(data){
                console.log(data);
                if(data.value!="no resultados"){
                    $("#secuencial").val(data[11]);
                    $("#valor_factura").val(data[10]);
                    $("#nro_autorizacion").val(data[17]);
                    $("#cliente").val(data[0]);
                    $("#nombre_cliente").val(data[2]);
                    $("#id_proveedor").val(data[0]);
                    $("#concepto").val(data[8]);
                    $("#total_factura").val(data[10]);
                    $("#subtotal_final").val(data[18]);
                    $("#iva_final").val(data[12]);
                    $("#id_venta").val(data[19]);
                    if(data[18]==0){
                        $("#subtotal_final").val(data[10]);
                    }
                    for(i=0;i<data[16].length; i++){
                        $("#vence"+i).val(data[16][i].fecha_asiento);                    
                        $("#tipo"+i).val(data[16][i].tipo);                    
                        $("#numero"+i).val(data[16][i].nro_comprobante);                    
                        $("#concepto"+i).val(data[16][i].procedimientos);                    
                        $("#saldo"+i).val(data[16][i].total_final);                  
                    }
                }        
                
            },
            error: function(data){
                console.log(data);
            }
        })
    }

    function buscar_factura2(){
        $("#buscar").next().remove();
        var validacion= $("#esfac_contable").val();
        $.ajax({
            type: 'post',
            url:"{{route('retenciones.clientes.buscar.codigo')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'id_factura':$("#facturas").val(),'tipo':$("#esfac_contable").val(), 'secuencia':$("#secuencial").val()},
            success: function(data){
                //console.log(data);
                console.log(data);
                if(data.value!="no resultados"){
                    $("#secuencial").val(data[11]);
                    $("#valor_factura").val(data[10]);
                    $("#nro_autorizacion").val(data[17]);
                    $("#cliente").val(data[0]);
                    $("#nombre_cliente").val(data[2]);
                    $("#id_proveedor").val(data[0]);
                    $("#concepto").val(data[8]);
                    $("#total_factura").val(data[10]);
                    $("#subtotal_final").val(data[18]);
                    if(data[18]==0){
                        $("#subtotal_final").val(data[10]);
                    }
                    $("#iva_final").val(data[12]);
                    $("#id_venta").val(data[19]);
                    
                    for(i=0;i<data[16].length; i++){
                        $("#vence"+i).val(data[16][i].fecha_asiento);                    
                        $("#tipo"+i).val(data[16][i].tipo);                    
                        $("#numero"+i).val(data[16][i].nro_comprobante);                    
                        $("#concepto"+i).val(data[16][i].procedimientos);                    
                        $("#saldo"+i).val(data[16][i].total_final);                  
                    }
                }        else{
                    alert("Ya tiene retencion");
                }                 
                
            },
            error: function(data){
                console.log(data);
            }
        })
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
            });        
        },
        minLength: 1,
     } );
    
    function validar_vacios(){
        var retencion_fuente= $("#retencion_fuente").val();
        var retencion_ivas= $("#retencion_ivas").val();
        var retencion_totales= $("#retencion_totales").val();
        var retencion_iva= $("#retencion_impuesto").val();
        var nuevo_saldo0= $("#nuevo_saldo0").val();
        var id_venta= $("#id_venta").val();
        var id_proveedor= $("#id_proveedor").val();
        if(retencion_fuente!="" && retencion_ivas!="" && retencion_totales!="" && retencion_iva!="" && nuevo_saldo0!="" && id_venta!="" && id_proveedor!=""){
            return 'ok';
        }
        return 'no';
    }

    function grupo_clientes(){

        var valor= $("#id_clientex").val();
        var tipo="";
        //alert(valor);
        $.ajax({
            type: 'post',
            url: "{{route('clientes.deudas')}}", 
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data:{'id_cliente':valor,'tipo':tipo},
            success: function(data){
                //alert(data[0].nombre);
                console.log(data);
                if(data.value!='No se encontraron resultados'){
                    if(valor!=0){
                        $("#facturas").empty();
                        $("#facturas").append('<option value='+""+'>'+"Seleccione # Factura..."+'</option>');
                        $.each(data[4],function(key, registro) {
                            $("#facturas").append('<option value='+registro.nro_comprobante+'>'+registro.nro_comprobante+'</option>');
                        }); 
                    }else{
                        $("#facturas").empty();
                        buscar_factura2()
                    }

                }else{
                    $("#facturas").empty();
                    buscar_factura2()
                }
            },
            error: function(data){
                console.log(data);
            }
        })
    }

    function traer_retenciones() {
        //retenciones.buscartipo
        var id= $("#tipo_retencion").val();
        //alert(id);
        $.ajax({
                type: 'get',
                url:"{{route('retenciones.buscartipo')}}",
                datatype: 'json',
                data: {'id':id},
                success: function(data){
                    if(data!=null){
                        //alert("dasda");
                       
                        $("#porcentaje_retencionf").empty();
                        $("#porcentaje_retencionf").append('<option value="0">Seleccione...</option>');
                        $.each(data,function(key, registro) {
                            $("#porcentaje_retencionf").append('<option value='+registro.id+'>'+registro.nombre+'</option>');
                        }); 
                    }else{
                        $("#porcentaje_retencionf").empty();
                    }
                   // console.log(data);  
                    //swal("Correcto!","Retencion guardada correctamente","success");
                    
                },
                error: function(data){
                    //console.log(data);
                }

            
        });
    }
    function guardar_retenciones(){
        var tipo_rfir= $("#tipo_rfir").val();
        var tipo_rfiva= $("#tipo_rfiva").val();
        var validacion= validar_vacios();

        var formulario = document.forms["form_guardado"];
        var porcentaje_retencion= formulario.porcentaje_retencionf.value;
        var cliente= formulario.cliente.value;
        var secuencia= formulario.secuencial.value;
        var fecha= formulario.fecha_aut.value;
        var no_autorizacion= formulario.nro_autorizacion.value;
        var no_factura= formulario.valor_factura.value;
        var valor_fuente= formulario.valor_fuente.value;
        var valor_iva= formulario.valor_iva.value;
        var cuenta_renta= formulario.cuenta_renta.value;
        var cuenta_iva= formulario.cuenta_iva.value;
        var msj = "";
        if(porcentaje_retencion==""){
            msj+="Por favor, Porcentaje de retención \n";
        }
        if(cliente==""){
            msj+="No existe cliente\n";
        }
        if(no_autorizacion==""){
            msj+="Por favor, Falta la autorización\n";
        }
        if(no_factura==""){
            msj+="Por favor, Llene el numero de la factura\n";
        }
        if(fecha==""){
            msj+="Por favor, Llene la fecha\n";
        }

        if(valor_fuente==""){
            msj+="Por favor, Llene el valor de la fuente\n";
        }
        if(valor_iva==""){
            msj+="Por favor, Llene el valor del IVA\n";
        }           
        if(msj==""){
            $("#boton_guardar").attr("disabled", "disabled");
            $.ajax({
                type: 'post',
                url:"{{route('retenciones.clientes.store')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'json',
                data: $('#form_guardado').serialize(),
                success: function(data){
                    //console.log(data);  
                    swal("Correcto!","Retencion guardada correctamente","success");
                    url="{{ url('contable/clientes/comprobante/retenciones/pdf/') }}/"+data;
                    window.open(url,'_blank');
                    $('#form_guardado input').attr('readonly', 'readonly');
                    $("#boton_guardar").attr("disabled", "disabled");
                    
                },
                error: function(data){
                    //console.log(data);
                }
            })
        }else{
            alert(msj);
        }
        

    }
    function lista_valores(id){

        var variable_select= $("#porcentaje_retencionf").val();
        var tipo= $("#tipo_retencion").val();
        var total_factura= $("#subtotal_final").val();
        var total_ivav= parseFloat($("#iva_final").val());
        if(isNaN(total_ivav)){
            total_ivav=0;
        }
        //alert(valor);
        $.ajax({
            type: 'post',
            url:"{{route('retenciones_query')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'opcion': variable_select,'tipo':tipo},
            success: function(data){
                //alert(data[0].nombre);
                //console.log(data);
                if(data.value!='no'){
                    $("#codigo").val(data[0].codigo);
                    console.log(data);
                    var codigo= parseFloat(data[0].valor);
                    //1 es iva 2 fuente
                    console.log(codigo);
                    console.log("aquiu");
                    if(tipo=='1'){
                        var factura_total= parseFloat($("#subtotal_final").val());
                        var totales= total_ivav*(codigo/100);
                        $("#monto_retenido").val(totales.toFixed(2,2));
                        $("#base_retencion").val(total_ivav);
                    }else{
                        var totales= total_factura*(codigo/100);
                        $("#monto_retenido").val(totales.toFixed(2,2));
                        $("#base_retencion").val(total_factura);
                    }

                   /* total_abono()  */                        
                }
            },
            error: function(data){
                //console.log(data);
            }
        })
    }
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
                            $("#punto_emision").append('<option value='+registro.codigo_sucursal+'-'+registro.codigo_caja+'>'+registro.codigo_sucursal+'-'+registro.codigo_caja+'</option>');
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
                //console.log(data);
            }
        })
    }
    function total_abono(){
        var retencion_fuente= parseFloat($("#retencion_fuente").val());
        var retencion_iva= parseFloat($("#retencion_ivas").val());
        var total_retenciones= retencion_fuente+retencion_iva;
        if(total_retenciones!=NaN){
            $("#retencion_totales").val(total_retenciones.toFixed(2));
            $("#nuevo_saldo0").val(total_retenciones.toFixed(2));
        }   
    }
    function validar_td(id){
        if((id)!=null){
            var valor= parseFloat($("#valor_cheque").val());
            var abono= parseFloat($("#abono"+id).val());
            var saldo= parseFloat($("#saldo"+id).val());
            suma_totales();
            var cantidad= parseFloat($("#total_suma").val());
            if(!isNaN(abono) && !isNaN(valor) && !isNaN(cantidad)){
                if(cantidad<=valor){
                    var uno=1;
                    $("#verificar_superavit").val(uno);
                    if(abono>saldo){
                        abono=saldo;
                    }
                    $("#abono"+id).val(abono.toFixed(2,2));
                }else{
                    valor=0;
                    $("#abono"+id).val(valor.toFixed(2,2));
                    swal("¡Error!","Error no puede superar al valor del cheque","error")
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
    function crea_tds(){
        id= document.getElementById('cont').value;
        var tipo= $("#tipo_retencion").val();
        var total_factura= $("#base_retencion").val();
        var codigo= $("#codigo").val();
        var valor_retenido= $("#monto_retenido").val();
        var porcentaje= $("#porcentaje_retencionf").val();
        var total= $("#total_final").val();
        var eliminados= parseInt($("#eliminados").val());
        var total_final= 0;
        var conter=0;
        var contaiva=0;
        var cuenta_retenta=parseInt($("#cuenta_renta").val());
        if(isNaN(cuenta_retenta)){ cuenta_retenta=0; }
        //alert(cuenta_retenta);
        var formulario = document.forms["form_guardado"];
        var tipo_retencion= formulario.tipo_retencion.value;
        var numero_ref= formulario.numerorf.value;
        var fecha_auto= formulario.fecha_aut.value;
        var numerofac= formulario.facturas.value;
        var valor_factura= formulario.valor_factura.value;
        var cuenta_iva=parseInt($("#cuenta_iva").val());
        
        if(isNaN(cuenta_iva)){ cuenta_iva=0; }
        if(tipo_retencion!="" && valor_factura!=""){
            var midiv = document.createElement("tr");
            if(tipo=='2'){
                tipo= 'RENTA';
                conter=cuenta_retenta+1;
                if(conter<=2 && conter>0){
                    $("#cuenta_renta").val(conter);
                }else{
                    $("#cuenta_renta").val(2);
                }

                                
            }else{
                tipo= 'IVA';
                contaiva=cuenta_iva+1;
                if(contaiva<=1 && contaiva>0){
                    $("#cuenta_iva").val(contaiva);
                }else{
                    $("#cuenta_iva").val(1);
                }

            }
           //alert(contaiva);
                midiv.setAttribute("id","dato"+id);
                midiv.innerHTML = ' <td> <input type="text" class="form-control" style="height: 80%;" name="numerorefs'+id+'" value="'+numero_ref+'"> </td> <td> <input class="form-control" style="height: 80%;" name="numerofact'+id+'" value="'+numerofac+'">  </td> <td><input class="form-control" type="text" style=" height: 80%;" name="fechauto'+id+'" value="'+fecha_auto+'"></td> <td><input class="form-control " style="height: 80%;" name="base_imp'+id+'" id="base_imp'+id+'"readonly></td> <td><input class="form-control "  style="height: 80%;" name="tipor'+id+'" id="tipor'+id+'" value="'+tipo+'" readonly></td> <td> <input class="form-control " name="codigor'+id+'" id="codigor'+id+'" style="height: 80%;" readonly ></td> <td> <input class="form-control " style="height: 80%;" name="porcentaje_retencion'+id+'" id="porcentaje_retencion'+id+'" readonly></td><td> <input style="width: 89%; height: 80%;" class="form-control " name="valor_retenido'+id+'" id="valor_retenido'+id+'" readonly></td> <input type="hidden" name="id_porcentaje'+id+'" id="id_porcentaje'+id+'"> <input type="hidden" name="porcentaje'+id+'" id="porcentaje'+id+'"> <td style="text-align:center;"><button style="text-align: center;" id="eliminar'+id+'" type="button" onclick="eliminar_registros('+id+')" class="btn btn-danger btn-xs btn-gray delete"> <i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td> ';
                document.getElementById('datos_a').appendChild(midiv);
                id = parseInt(id);
                //alert(codigo);
                $("#codigor"+id).val(codigo);
                $("#tipo_p"+id).val(tipo);
                $("#valor_retenido"+id).val(valor_retenido);
                $("#porcentaje_retencion"+id).val(porcentaje);
                $("#base_imp"+id).val(total_factura);
                $("#id_porcentaje"+id).val(porcentaje);
                $("#porcentaje"+id).val(codigo)
                id = id+1;
                document.getElementById('cont').value = id;
                suma_seccion(id);
            
                //alert("si funciona");
            

        }else{
            swal("¡Error!","Ingresa primero los datos","error");
        }
       
    }
    function eliminar_registros(valor)
    {
        var dato1 = "dato"+valor;
        var total;
        var contador_verdadero= document.getElementById('cont').value;
        var contador= parseInt(contador_verdadero);
        var cuenta_retenta=parseInt($("#cuenta_renta").val());
        var cuenta_referencia= $("#tipor"+valor).val();
        var total_renta=0;
        var total_iva=0;
        if(isNaN(cuenta_retenta)){ cuenta_retenta=0; }
        //alert(cuenta_retenta);
        var cuenta_iva=parseInt($("#cuenta_iva").val());
        if(isNaN(cuenta_iva)){ cuenta_iva=0; }
        if(contador_verdadero>1){
            total=valor;
        }else{
            total=1;
            //alert("aqui llega");
        }
        if(cuenta_referencia!='RENTA'){
            if(cuenta_iva>0&& cuenta_iva<=2){
            total_iva= cuenta_iva-1;
            $("#cuenta_iva").val(total_iva);
            }
        }else{
            if(cuenta_retenta>0 && cuenta_retenta <=2){
            total_renta=cuenta_retenta-1;
            $("#cuenta_renta").val(total_renta);
             }

        }
        document.getElementById('cont').value = total;
        $("#dato"+valor).remove();
        var valor_en= parseInt(valor);
        $("#eliminados").val(1);

        suma_seccion();
    }

    function suma_seccion(cont){
        var tipo= parseFloat($("#tipo_retencion").val());
        //alert(tipo);
        var contador=  parseInt($("#cont").val());
        //alert(contador);
        var sumador=0;
        var sumador2=0;
        for(i=1; i<contador; i++){
            var totales= parseFloat($("#valor_retenido"+i).val());
            var tipo= $("#tipor"+i).val();
            if(tipo=='RENTA'){
                if((totales)!=NaN){ 
                 sumador+=totales;
                //alert(totales)
                }
                else{ 
                    sumador=0;
                }
            }else if(tipo=='IVA'){
                if((totales)!=NaN){ 
                 sumador2+=totales;
                //alert(totales)
                }
                else{ 
                    sumador2=0;
                }
            }
            //alert(sumador2);
        }
            $("#valor_iva").val(sumador2.toFixed(2));
            $("#total_rfirt").val(sumador.toFixed(2));
            $("#total_rfivat").val(sumador2.toFixed(2));
            var totalx= sumador+sumador2;
            var find= parseFloat($("#saldo0").val());
            if(isNaN(find)){ find=0; }
            
            var total_f= find-totalx;
            if(!$.isNumeric(total_f)){
                total_f= 0;
            }
            $("#abono0").val(totalx);
            $("#abono_base0").val(total_f.toFixed(2,2));
            $("#valor_fuente").val(sumador.toFixed(2));
            var total= parseFloat(sumador2)+parseFloat(sumador);
            $("#total_egreso").val(total.toFixed(2,2));
            $("#nuevo_saldo").val(total_f.toFixed(2,2));
            $("#total_deudas").val(find.toFixed(2,2));
            $("#total_abonos").val(total.toFixed(2,2));
            $("#retencion_total").val(total);   
    }
</script>


@endsection