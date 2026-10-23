@extends('contable.retenciones.base')
@section('action-content')
<script type="text/javascript">
    
    function goBack() {
      window.history.back();
    }
</script>
<section class="content">    
    <form class="form-vertical" method="post" id="form_guardado">
        <div class="box box-warning box-solid "  style=" background-color: white;">
                <div class="header box-header with-border">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-9 col-sm-9 col-6">
                                <div class="box-title" ><b style="font-size: 16px;">ACREEDORES-COMP. DE RETENCIONES</b></div>
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

                <div class="box-body" style="background-color: #ffffff;">
                    <div class="col-12 col-xs-12">
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label for="buscar" class = "col-form-label-sm">Buscar</label>
                                <input disabled type="text" id = "buscar" name="buscar" class = "form-control form-control-sm buscar" disabled onchange="buscar_factura()">
                                
                            </div>
                            <div class="form-group col-md-1">
                                <label style="padding-left: 0px">Estado</label>
                                <div style="background-color: green; " class="form-control col-md-1"></div>           
                            </div>
                            <div class="form-group col-md-2">
                                <div class="input-group">
                                    <label class="col-md-12" for="id_factura">ID:</label>
                                    <input  style="width: 80%;" type="text" name="id_factura" id="id_factura" disabled>    
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
                                    <input type="date" name="fecha_hoy" id="fecha_hoy" value="{{date('Y-m-d')}}" disabled>
                                </div>
                            </div>
                            <div class="form-group col-md-1" style="padding-left: 20px;">
                                <label for="tipo">Tipo: </label>
                                <select name="tipo" id="tipo" disabled="disabled">
                                <option value="0">CLI-RT</option>
                                </select>
                            </div>
                            <div class="form-group col-md-1" style="padding-left: 20px;">
                                <div class="input-group">
                                    <label class="col-md-12" for="proyecto">Proyecto: </label>
                                    <select name="proyecto" id="proyecto" disabled="disabled">
                                        <option value="0">0000</option>
                                    </select>
                                </div>
                            </div>
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
                                    <label class="col-md-12" for="direccion">Dirección:</label>
                                    <input type="text" name="direccion" id="direccion" disabled>
                                </div>
                            
                            </div>
                            <div class="form-group col-md-2">
                                <div class="input-group">
                                    <label class="col-md-12" for="ruc">Ruc:</label>
                                    <input type="text" name="ruc" id="ruc" style="width:115%;" disabled>
                                </div>
                            </div>
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
                            <div class="form-group col-md-8">
                                <div class="input-group">
                                    <label  class="col-md-12" for="concepto">Concepto:</label>
                                    <input type="text" name="concepto" id="concepto" style="width:200%;" disabled> 
                                </div>
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
                                                    @foreach (range(1, 5) as $i)
                                                        <tr>
                                                            <td> <input style="width: 90%;" type="text" name="numero_referencia{{$cont}}" id="numero_referencia{{$cont}}" disabled> </td>
                                                            <td> 
                                                            <select name="divisas{{$cont}}" id="divisas{{$cont}}" disabled>
                                                                <option value="0">Seleccione...</option>
                                                                @foreach($divisas as $value)
                                                                <option value="{{$value->id}}">{{$value->descripcion}}</option>
                                                                @endforeach
                                                            </select> 
                                                            
                                                            </td>
                                                            <td> <input style="width: 90%; text-align: center;" type="text" name="base_fuente{{$cont}}" id="base_fuente{{$cont}}" disabled></td>
                                                            <td> <select name="tipo_rfir{{$cont}}" id="tipo_rfir{{$cont}}" onchange="lista_valores({{$cont}})" disabled>
                                                                
                                                                 @foreach($rfir as $value)
                                                                    <option {{ $retenciones->rfir == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option>
                                                                 @endforeach
                                                                </select> </td>
                                                            <td> <input disabled  style="width: 90%; text-align: center;"type="text" name="total_rfir{{$cont}}" id="total_rfir{{$cont}}" > </td>
                                                            <td> <input disabled style="width: 90%; text-align: center;" type="text" name="base_iva{{$cont}}" id="base_iva{{$cont}}"> </td>
                                                            <td> <select  name="tipo_rfiva{{$cont}}"  id="tipo_rfiva{{$cont}}" onchange="lista_valores2({{$cont}})" disabled>
                                                                    
                                                                    @foreach($rfiva as $value)
                                                                        <option {{ $retenciones->rfiva == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option>
                                                                    @endforeach
                                                                 </select> 
                                                            </td>
                                                            <td> <input disabled style="width: 90%; text-align: center;" disabled type="text" name="total_rfiva{{$cont}}" id="total_rfiva{{$cont}}" value="{{$retenciones->total}}"> </td>
                                                        </tr>
                                                        @php $cont = $cont +1; @endphp
                                                    @endforeach
                                                        
                                                    </tbody>
                                                </table>
                        </div>
                        <div class="col-md-12" style="margin-top: 30px;">
                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <label for="retencion_impuesto">Ret. Imp. Renta </label>
                                </div>
                                <div class="form-group col-md-2">
                                    <input type="text" name="retencion_impuesto" id="retencion_impuesto" disabled>
                                </div>
                                <div class="form-group col-md-3" style="text-align: right;">
                                    <label for="">Ret. I.V.A</label>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <input type="text" name="retencion_iva" id="retencion_iva" disabled>
                                    </div>
                                </div>
                            </div>
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
                                        <input style="width: 90%;" type="text" name="total_egreso" id="total_egreso" disabled>
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label for="credito_aplicado">Debito Aplicado</label>
                                        <input style="width: 90%; color: red; text-align: right;" type="text" name="debito_aplicado" value="0.00" id="debito_aplicado" disabled>
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label for="total_deudas">Total Deudas</label>
                                        <input style="width: 90%;" type="text" name="total_deudas" id="total_deudas" disabled>
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label for="total_abonos">Total Abonos</label>
                                        <input style="width: 90%;" type="text" name="total_abonos" id="total_abonos" disabled>
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label for="nuevo_saldo">Nuevo Saldo</label>
                                        <input style="width: 90%;" type="text" name="nuevo_saldo" id="nuevo_saldo" disabled>
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
                    <div class="col-md-12">
                        <div class="input-group">
                            <label class="col-md-12" style="background-color: #bbb0ad;" for="nota">Nota: </label>
                            <textarea class="col-md-12" name="nota" id="nota" cols="200" rows="5" disabled value="{{$retenciones->descripcion}}"></textarea>
                        </div>
                    </div>
                </div>
               
        </div>

    </form>
</section>



@endsection