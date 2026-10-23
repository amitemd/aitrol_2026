@extends('contable.comp_retencion_clientes.base')
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

        .alerta_guardado{
           position: absolute;
           z-index: 9999;
           bottom: 100px;
           right: 20px;
        } 

        .disableds{
            display: none;
        }
        .disableds2{
            display: none;
        }
        .disableds3{
            display: none;
        }
        .has-cc span img{
            width:2.775rem;
        }
        .has-cc .form-control-cc {
            z-index: 2;
            display: block;
            width: 2.375rem;
            height: 2.375rem;
            line-height: 1.8rem;
            text-align: center;
            pointer-events: none;
            color: #444;
            font-size: 1.5em;
            float: right;       
            margin-right: 1px;
            
        }
        .has-cc .form-control-cc2{
            z-index: 2;
            display: block;
            width: 2.375rem;
            height: 2.375rem;
            line-height: 1.8rem;
            text-align: center;
            pointer-events: none;
            color: #444;
            font-size: 1.5em;
            float: right;       
            margin-right: 1px;
        }
        .cvc_help{
            cursor: pointer;
        }

        .swal-title {
           margin: 0px;
           font-size: 5px;
          
        }

        table{
          border-collapse: collapse;
          font-size: 12pt;
          font-family: 'arial';
          width: 100%;
        }

        table th{
          text-align: left;
          padding: 2px;
          background: #3d7ba8;
          color: #FFF;
        }
        
        table tr:nth-child(odd){
          background: #FFF;
        }
        
        table td{
          padding: 3px;
        }

        .card {
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            padding: 6px;
            background-color: white;
        }
        
        .card-header{ 
            border-radius: 6px 6px 0 0;
            background-color: #3c8dbc;
            border-color: #b2b2b2;
            padding: 8px;
            font-family: 'Roboto', sans-serif;
        }
        
</style>


<script src="{{ asset ("/js/jquery-ui.js")}}"></script>

<section class="content">
    <form class="form-vertical" id="crear_comprobante_retencion" role="form" method="POST">
    {{ csrf_field() }}
        <input type="text" name="num_com_retenc_cliente" id="num_com_retenc_cliente" class="hidden" value="">
        <input type="text" name="impuesto_cobrad" id="impuesto_cobrad" class="hidden" value="">
        <input type="text" name="total_factura" id="total_factura" class="hidden" value="">
        <input type="text" name="suma_subtotal" id="suma_subtotal" class="hidden" value="">
        <input type="text" name="cuen_cl_iva" id="cuen_cl_iva" class="hidden" value="">
        <input type="text" name="cuen_cl_fuent" id="cuen_cl_fuent" class="hidden" value="">
        <div class="box box-primary box-solid " style="background-color: white;">
            <div class="header box-header with-border" >
                <div class="col-12">    
                    <div class="row"> 
                        <div class="box-title col-md-6" >
                            <label style="color: white">CLIENTES - COMPROBANTE DE RETENCIÓN</label>
                        </div>
                        <div class="col-md-5" style="padding-right: 0px;right: 0px;">
                            <div class="row">
                                <div class="col-md-6">
                                </div>
                                <div class="col-md-3">
                                    <button  type="button" onclick="crear_comprobante_retencion_cliente()" class="btn btn-primary" style="color:white; border-radius: 5px; border: 2px solid white;" id="btn_guardar"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>Guardar
                                    </button>
                                </div>
                                <div class="col-md-3" style="text-align: right;">
                                    <button type="button" onclick="goBack()" class="btn btn-primary" style="color:white; border-radius: 5px; border: 2px solid white;">
                                       <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body" style="background-color: #ededed;">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-sm-12" style="padding-left: 17px">
                            <div class="card-header">
                                 <label style="color: white">DATOS GENERALES</label>
                            </div>
                            <div class="card-body">
                                    <div class="col-md-12">
                                        <div class="col-md-2 col-xs-2">
                                            <label for="numero_factura" class="control-label">Buscar:</label>
                                            <div class="input-group">
                                                <input id="numero_factura" name="numero_factura" type="text" class="factnumero" onchange="buscar_factura_venta()">
                                            </div>
                                        </div>
                                        <div class="col-md-1 col-xs-1">
                                            <label for="id" class="control-label">ID:</label>
                                            <div class="input-group">
                                                <input id="id" name="id" type="text" class="form-control" value="{{old('id')}}" placeholder="id" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                            </div>
                                        </div>
                                        <div class="col-md-1 col-xs-1">
                                            <label for="numero" class="control-label">Nùmero:</label>
                                            <div class="input-group">
                                                <input id="numero" name="numero" type="text" class="form-control"  value="{{old('numero')}}" placeholder="numero" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                            </div>
                                        </div>
                                        <div class="col-md-1 col-xs-1">
                                            <label for="num_asiento" class="control-label">Asiento:</label>
                                            <div class="input-group">
                                                <input id="num_asiento" name="num_asiento"  type="text" class="form-control"  value="{{old('num_asiento')}}" placeholder="asiento" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-xs-2">
                                            <label for="num_comprobante" class="control-label">N# Comprobante:</label>
                                            <div class="input-group">
                                                <input id="num_comprobante" name="num_comprobante"  type="text" class="form-control"  value="{{old('num_comprobante')}}" placeholder="Numero Comprobante" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-xs-2">
                                            <label for="fecha_factura" class="control-label">Fecha Factura:</label>
                                            <div class="input-group">
                                                <input id="fecha_factura" name="fecha_factura"  type="text" class="form-control"  value="{{old('fecha_factura')}}" placeholder="Fecha Factura" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="col-md-2 col-xs-2">
                                            <label  for="fecha_emision" class="control-label">Fecha:</label>
                                            <div class="input-group date">
                                                <input  type="date" class="form-control" name="fecha" id="fecha"
                                                value="" autocomplete="off">
                                                <div class="input-group-addon" style="padding-left: 4px;padding-right: 2px;">
                                                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('fecha').value = '';"></i>
                                                </div>   
                                            </div>
                                        </div>
                                        <div class="col-md-1 col-xs-1" style="padding-left: 16px;">
                                            <label for="tipo" class="control-label">Tipo:</label>
                                            <div class="input-group">
                                                <input id="tipo" maxlength="25" type="text" readonly class="form-control" name="tipo" value="CLI-RT">
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-xs-2" style="padding-left: 2px;padding-right: 2px;">
                                            <label for="divisas" class="control-label">Proyecto:</label>
                                            <select id="divisas" name="divisas" class="form-control">
                                                <option value="0000">0000</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 col-xs-2" style="padding-left: 2px;padding-right: 2px;">
                                            <label for="divisas" class="control-label">Caja:</label>
                                            <select id="caja" name="caja" class="form-control">
                                                <option value="0000">10101101</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">&nbsp;</div>
                                     <!--Concepto-->
                                    <div class="col-md-7 col-xs-7">
                                        <label for="concepto" class="col-md-2 control-label">Concepto:</label>
                                        <div class="input-group col-md-9">
                                            <input id="concepto" name="concepto" type="text" class="form-control" value="{{ old('concepto') }}" placeholder="Concepto" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                            <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                               <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('concepto').value = '';"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12" style="padding-top: 3px"></div>
                                    <div class="col-md-7 col-xs-7">
                                        <label for="cliente" class="col-md-2 control-label">Cliente:</label>
                                        <div class="input-group col-md-9">
                                            <select id="cliente" name="cliente" class="form-control select2_cliente" style="width: 100%" >
                                                <option value="">Seleccione...</option> 
                                                @foreach($clientes as $value)    
                                                    <option value="{{$value->identificacion}}">{{$value->nombre}}</option>
                                                @endforeach    
                                            </select>
                                        </div>
                                    </div>
                                    <!--Ruc/Cid del Cliente-->
                                    <div class="col-md-5 col-xs-5" style="padding-top: 3px"> 
                                        <label for="ruc_cedula" class="col-md-2 control-label">RUC/CID:</label>
                                        <div class="input-group col-md-9">
                                            <input id="ruc_cedula" name="ruc_cedula" type="text" class="form-control" maxlength="13"  value="{{ old('ruc_cedula') }}" placeholder="RUC/CID" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                            <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                               <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('ruc_cedula').value = '';"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <!--Autorizacion-->
                                    <div class="col-md-7 col-xs-7">
                                        <label for="autorizacion" class="col-md-2 control-label">Autorización:</label>
                                        <div class="input-group col-md-9">
                                            <input id="autorizacion" name="autorizacion" type="text" class="form-control" maxlength="13"  value="{{ old('autorizacion') }}" placeholder="Autorizacion" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                            <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                               <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('autorizacion').value = '';"></i>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                        <div class="table-responsive col-md-12" style="min-height: 100px; max-height: 250px;">
                            <input name='contador' type="hidden" value="0" id="contador">
                            <table id="example1" role="grid" aria-describedby="example2_info">
                                <caption><b>Detalle de Retenciones</b></caption>
                                <thead style="background-color: #FFF3E3">
                                    <tr style="position: relative;">
                                      <!--h style="width: 7%; text-align: center;">Número RF.</th>-->
                                      <th style="width: 10%; text-align: center;">Número Fact.</th>
                                      <th style="width: 7%; text-align: center;">Fecha</th>
                                      <th style="width: 7%; text-align: center;">Divisa</th>
                                      <th style="width: 7%; text-align: center;">Base Fuente</th>
                                      <th style="width: 7%; text-align: center;">Tipo RFIR</th>
                                      <th style="width: 10%; text-align: center;">Total RFIR</th>
                                      <th style="width: 7%; text-align: center;">Base I.V.A</th>
                                      <th style="width: 7%; text-align: center;">Tipo RFIVA</th>
                                      <th style="width: 7%; text-align: center;">Total RFIVA</th>
                                      <th style="width: 7%; text-align: center;">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="det_retencion">
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-2 col-xs-2">
                                    <button type="button" id="agregar_det_ret" class="btn btn-primary">
                                        Agregar
                                    </button>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <label for="ret_imp_renta" class="control-label" class="control-label">Ret. Imp.Renta:</label>
                                    <input id="ret_imp_renta" name="ret_imp_renta" type="text" class="form-control" value="0.00">
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <label for="retencion_iva" class="control-label" class="control-label">Ret.I.V.A:</label>
                                    <input id="retencion_iva" name="retencion_iva" type="text" class="form-control" value="0.00">
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive col-md-12" style="min-height: 250px; max-height: 250px;padding-top:  20px">
                            <table id="example2" role="grid" aria-describedby="example2_info">
                                <caption><b>Detalle de deudas del Cliente</b></caption>
                                <thead style="background-color: #FFF3E3,color: white;">
                                    <tr style="position: relative;">
                                      <th style="width: 3%; text-align: center;">Emisión</th>
                                      <th style="width: 5%; text-align: center;">Vence</th>
                                      <th style="width: 5%; text-align: center;">Tipo</th>
                                      <th style="width: 7%; text-align: center;">Número</th>
                                      <th style="width: 7%; text-align: center;">Concepto</th>
                                      <th style="width: 7%; text-align: center;">Saldo</th>
                                      <th style="width: 7%; text-align: center;">Abono</th>
                                      <th style="width: 7%; text-align: center;">Nuevo Saldo</th>
                                    </tr>
                                </thead>
                                <tbody id="det_deudas">
                                    @php $cont=0; @endphp
                                    @foreach (range(1, 6) as $i)
                                        <tr>
                                            <td> <input class="form-control" type="text" name="fech_emision{{$cont}}" id="fech_emision{{$cont}}" readonly> </td>
                                            <td> <input class="form-control" type="text" name="fech_vence{{$cont}}" id="fech_vence{{$cont}}" readonly> </td>
                                            <td> <input class="form-control" type="text" name="tipo{{$cont}}" id="tipo{{$cont}}" readonly> </td>
                                            <td> <input class="form-control"  type="text" name="numero{{$cont}}" id="numero{{$cont}}" readonly> </td>
                                            <td> <input class="form-control" type="text" name="concepto{{$cont}}" id="concepto{{$cont}}" readonly> </td>
                                            <td> <input class="form-control"  type="text" name="saldo{{$cont}}" id="saldo{{$cont}}" readonly> </td>
                                            <td> <input class="form-control" type="text" name="abono{{$cont}}" id="abono{{$cont}}" readonly>
                                            </td>
                                            <td> <input class="form-control" type="text" name="n_saldo{{$cont}}" id="n_saldo{{$cont}}" readonly></td>
                                        </tr>
                                        @php $cont = $cont +1; @endphp
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12" style="padding-top: 12px"></div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-2 col-xs-2">
                                    <label for="total_ingresos" class="control-label" class="control-label">Total Ingresos:</label>
                                    <div class="input-group">
                                        <input id="total_ingresos" name="total_ingresos" type="text" class="form-control" value="{{old('total_ingresos')}}" placeholder="Total Ingresos" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                    </div>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <label for="credito_aprobado" class="control-label" class="control-label">Crédito Aprobado:</label>
                                    <div class="input-group">
                                        <input id="credito_aprobado" name="credito_aprobado" type="text" class="form-control" value="{{old('credito_aprobado')}}" placeholder="Credito Aprobado" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                    </div>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <label for="total_deudas" class="control-label" class="control-label">Total Deudas:</label>
                                    <div class="input-group">
                                        <input id="total_deudas" name="total_deudas" type="text" class="form-control" value="{{old('total_deudas')}}" placeholder="Total Deudas" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                    </div>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <label for="total_abonos" class="control-label" class="control-label">Total Abonos:</label>
                                    <div class="input-group">
                                        <input id="total_abonos" name="total_abonos" type="text" class="form-control" value="{{old('total_abonos')}}" placeholder="Total Abonos" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                    </div>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <label for="nuevo_saldo" class="control-label" class="control-label">Nuevo Saldo:</label>
                                    <div class="input-group">
                                        <input id="nuevo_saldo" name="nuevo_saldo" type="text" class="form-control" value="{{old('nuevo_saldo')}}" placeholder="Nuevo Saldo" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                    </div>
                                </div>
                                 <div class="col-md-2 col-xs-2">
                                    <label for="deficit" class="control-label" class="control-label">Déficit:</label>
                                    <div class="input-group">
                                        <input id="deficit" name="deficit" type="text" class="form-control" value="{{old('deficit')}}" placeholder="Deficit" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                    </div>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <label for="credito_favor" class="control-label" class="control-label">Crédito a Favor:</label>
                                    <div class="input-group">
                                        <input id="credito_favor" name="credito_favor" type="text" class="form-control" value="{{old('credito_favor')}}" placeholder="Credito a Favor" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                    </div>
                                </div>
                                <div class="col-md-12"></div>
                                <div class="col-md-12">
                                    <div class="input-group">
                                         <textarea class="col-md-12" name="obs_doctor" id="obs_doctor" cols="200" rows="3"></textarea>
                                    </div>
                                </div>   
                            </div>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </form>

<script type="text/javascript">

    $(document).ready(function(){

        obtener_numero_comp_retencion();
        obtener_fecha();
    
    });

    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : false,
      'info'        : false,
      'autoWidth'   : false,
      'order'       : [[ 1, "asc" ]]
    });

    function goBack() {
      window.history.back();
    }


    function obtener_fecha(){

        //obtenemos la fecha actual
        var now = new Date();
        var day =("0"+now.getDate()).slice(-2);
        var month =("0"+(now.getMonth()+1)).slice(-2);
        var today =now.getFullYear()+"-"+(month)+"-"+(day);
        $("#fecha").val(today);

    }

    function obtener_numero_comp_retencion(){
        $.ajax({
            url:"{{route('numero_comprobante_retencion.cliente')}}",
            type: 'get',
            datatype: 'json',
            success: function(data){
               //console.log(data);
               $('#num_com_retenc_cliente').val(data);
            },
            error: function(data){
                console.log(data);
            }
        })
    }

    //Obtengo la Cedula del Cliente
    $("#cliente").change(function(){
        //alert("busca");
        $.ajax({
            type: 'post',
            url:"{{route('buscar_identificacion_client')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#cliente"),
            success: function(data){
                //console.log(data);
                $('#ruc_cedula').val(data.client_identificacion);
            },
            error: function(data){
                console.log(data);
            }
        })
    });

     //Obtenemos Datos de la Factura de Venta Por Numero Ingresado
   function buscar_factura_venta(){

        $.ajax({
            type: 'post',
            url:"{{route('buscar_fact.numero')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'num_factura':$("#numero_factura").val()},
            success: function(data){
                //console.log(data);
                $("#id").val(data[0]);
                $("#numero").val(data[1]);
                $("#num_asiento").val(data[2]);
                $("#ruc_cedula").val(data[3]);
                $("#cliente").val(data[3]);
                $("#num_comprobante").val(data[4]); 
                $("#fecha_factura").val(data[5]); 
                $("#total_deudas").val(data[8]);
                $("#suma_subtotal ").val((data[9])+(data[10]));
                $("#impuesto_cobrad ").val(data[11]);
                $("#total_factura ").val(data[8]); 

                for(i=0;i<1; i++){
                     $("#fech_emision"+i).val(data[5]);
                     $("#fech_vence"+i).val(data[5]);
                     $("#tipo"+i).val(data[6]);
                     $("#numero"+i).val(data[1]);
                     $("#concepto"+i).val(('Fact #: '+data[4])+' - Proced:'+data[7]);
                     $("#saldo"+i).val(data[8]);

                }

            },
            error: function(data){
                console.log(data);
            }
        })
    
    }

    $(".factnumero").autocomplete({
        source: function( request, response ) {
            $.ajax( {
            url: "{{route('obtener_datos_factura')}}",
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


    $('#agregar_det_ret').click(function(event){

        id= document.getElementById('contador').value;
        
        var midiv = document.createElement("tr")
            midiv.setAttribute("id","dato"+id);

        midiv.innerHTML = "<td><div><input type='text' name='numero_fact"+id+"' required></div></td><td><input type='date' class='input-number' value='{{date("Y-m-d")}}' name='fecha_retencion"+id+"'></td><td><select name='id_divisa"+id+"'>@foreach($divisas as $value)<option value='{{$value->id}}'>{{$value->descripcion}}</option>@endforeach</select></td><td><div><input type='text' name='base_fuente"+id+"' id='base_fuente"+id+"' required></div></td><td><select name='id_tipo_rfir"+id+"' id='id_tipo_rfir"+id+"' onchange='obtener_valor_rete_fuente("+id+")'>@foreach($porce_rete_fuente as $value)<option value='{{$value->id}}' >{{$value->nombre}}</option>@endforeach</select></td><td><div><input type='text' name='total_rfir"+id+"' id='total_rfir"+id+"' value='0' required></div></td><td><div><input type='text' name='base_iva"+id+"' id='base_iva"+id+"' required></div></td><td><select name='id_tipo_rfiva"+id+"' id='id_tipo_rfiva"+id+"' onchange='obtener_valor_rete_iva("+id+")'>@foreach($porce_rete_iva as $value)<option value='{{$value->id}}'>{{$value->nombre}}</option>@endforeach</select></td><td><div><input type='text' name='total_rfiva"+id+"' id='total_rfiva"+id+"' value='0' required></div></td><td><button type='button' onclick='eliminar_registro("+id+")' class='btn btn-warning btn-margin'>Eliminar</button></td>";

        
        document.getElementById('det_retencion').appendChild(midiv);
        id = parseInt(id);
        id = id+1;
        document.getElementById('contador').value = id;
      
    });


    function eliminar_registro(valor)
    {
      var dato1 = "dato"+valor;
      document.getElementById(dato1).style.display='none';
    }

    
    function obtener_valor_rete_iva(id){

        var id_tipo_riva = $('#id_tipo_rfiva'+id).val();
        var imp_iva = parseFloat($("#impuesto_cobrad").val());
        var tot_fact = parseFloat($("#total_factura").val()); 

        $.ajax({
            type: 'post',
            url:"{{route('obt_retenc_porcent_iva')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'id_tip_ret_iva': id_tipo_riva},
            success: function(data){
                
                if(data.value!='no resultados'){
                   
                    var porcent_ret_iva = parseFloat(data[0])/100;
                    var total_reten_iva = (imp_iva)*(porcent_ret_iva);
                    $('#total_rfiva'+id).val(total_reten_iva.toFixed(2));
                    $('#base_iva'+id).val(imp_iva.toFixed(2));
                    $('#retencion_iva').val(total_reten_iva.toFixed(2));
                    $("#cuen_cl_iva").val(data[1]);

                    obtener_nuevo_saldo(id);
    
                    
                }
            },
            error: function(data){
                console.log(data);
            }
        })   
      
    }


    function obtener_valor_rete_fuente(id){

        var id_tipo_rfuente = $('#id_tipo_rfir'+id).val();
        var sum_Subtot = parseFloat($("#suma_subtotal").val());
        var tot_fact = parseFloat($("#total_factura").val()); 


        $.ajax({
            type: 'post',
            url:"{{route('obt_retenc_porc.fuent')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'id_tip_ret_fuente': id_tipo_rfuente},
            success: function(data){
                //console.log(data);
                if(data.value!='no resultados'){
                   
                    var porcent_ret_fuente = parseFloat(data[0])/100;
                    var total_reten_fuente = (sum_Subtot)*(porcent_ret_fuente);
                    $('#total_rfir'+id).val(total_reten_fuente.toFixed(2));
                    $('#base_fuente'+id).val(sum_Subtot.toFixed(2));
                    $('#ret_imp_renta').val(total_reten_fuente.toFixed(2));
                    $("#cuen_cl_fuent").val(data[1]);


                     obtener_nuevo_saldo(id);
    
                    
                }
            },
            error: function(data){
                console.log(data);
            }
        })
        
    }

     //Calculo de Nuevo Saldo
    function obtener_nuevo_saldo(id){

        var valor_rfiva = parseFloat($('#total_rfiva'+id).val());
        
        var valor_rf_renta = parseFloat($('#total_rfir'+id).val());
        
        var tot_fact = parseFloat($("#total_factura").val());

        var total_val_ret = valor_rfiva+valor_rf_renta;
        var val_nuevo_saldo = tot_fact-total_val_ret;

        var id = 0;
        if(val_nuevo_saldo!=NaN){
           $("#nuevo_saldo").val(val_nuevo_saldo);
           $("#n_saldo"+id).val(val_nuevo_saldo);
           $("#total_ingresos").val(total_val_ret);
           $("#total_abonos").val(total_val_ret);
           $("#abono"+id).val(total_val_ret);

        
        }
    }

   
    function crear_comprobante_retencion_cliente(){
        
        $.ajax({
            type: 'post',
            url:"{{route('comp_retencion_cliente.store')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#crear_comprobante_retencion").serialize(),
            success: function(data){
                //console.log(data);
               location.href ="{{route('comp_retencion_cliente.index')}}";
            },
            error: function(data){
                   console.log(data);
            }
        })
                    
    }




</script>

</section>
@endsection
