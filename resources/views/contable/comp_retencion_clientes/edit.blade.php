@extends('contable.ventas.base')
@section('action-content')

<style type="text/css">

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


        .card {
                box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
                padding: 16px;
                background-color: white;
        }

        .card1 {
                box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
                padding: 16px;
                background-color: white;
        }

</style>

<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<script type="text/javascript">
    function check(e){
        tecla = (document.all) ? e.keyCode : e.which;

        //Tecla de retroceso para borrar, siempre la permite
        if (tecla == 8) {
            return true;
        }

        // Patron de entrada, en este caso solo acepta numeros y letras
        patron = /[A-Za-z0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    }

    function goBack() {
      window.history.back();
    }

</script>

<section class="content">
    <div class="box box-solid box-warning" style="background-color: white;">
        <div class="header box-header with-border" >
            <div class="box-title col-md-9" ><b style="font-size: 16px;">FACTURA DE VENTA</b></div>
            <div class="col-md-3" style="text-align: right;">
                <button onclick="goBack()" class="btn btn-primary" style="color:white; border-radius: 5px; border: 2px solid white;">
                   <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
        <div class="box-body" style="background-color: #ffffff;">
            <form class="form-vertical" id="crear_factura" role="form" method="POST">
                {{ csrf_field() }}
                <div class="col-md-12">
                    <div class="row">
                        @if($ventas->estado == 1)
                            <div class="col-md-2 col-xs-2">
                               <label style="padding-left: 0px;font-size: 13px" >Estado:</label>
                               <div style="background-color: green;" class="form-control col-md-1"></div>
                            </div>
                        @elseif($ventas->estado == 0)
                            <div class="col-md-2 col-xs-2">
                               <label style="padding-left: 0px;font-size: 13px" >Estado:</label>
                               <div style="background-color: red;" class="form-control col-md-1"></div>
                            </div> 
                        @endif
                        <!--Identificador Relacional del documento generado por el Sistema.-->
                        <div class="col-md-1 col-xs-1" style="padding-left: 2px;padding-right: 2px;">
                            <label class="control-label" style="font-size: 13px">ID:</label>
                            <div class="input-group">
                                <input id="id" name="id" type="text" maxlength="11"  class="form-control" value="@if(!is_null($ventas)){{$ventas->id}}@endif" disabled>
                            </div>
                        </div>
                        <!--Número del Documento generado por el Sistema-->
                        <div class="col-md-1 col-xs-1" style="padding-left: 2px;padding-right: 2px;">
                            <label class="control-label" style="font-size: 13px">Número:</label>
                            <div class="input-group">
                                <input id="numero" name="numero" type="text" maxlength="25" class="form-control" 
                                value="@if(!is_null($ventas)){{$ventas->numero}}@endif" disabled>
                            </div>
                        </div>
                        <!--Tipo del Documento generado por el Sistema-->
                        <div class="col-md-1 col-xs-1" style="padding-left: 2px;padding-right: 2px;">
                            <label class="control-label" style="font-size: 13px">Tipo:</label>
                            <div class="input-group">
                                <input id="tipo" name="tipo" type="text" class="form-control"  
                                value="@if(!is_null($ventas)){{$ventas->tipo}}@endif" disabled>
                            </div>
                        </div>
                        <!--Lista desplegable de divisas-->
                        <div class="col-md-2 col-xs-2" style="padding-left: 2px;padding-right: 2px;">
                            <label class="control-label" style="font-size: 13px">Divisas:</label>
                            <!--<div class="input-group">
                                <input id="divisas" name="divisas" type="text"  class="form-control" value="@if(!is_null($ventas)){{$ventas->divisas}}@endif" disabled>
                            </div>-->
                            <select id="divisas" name="divisas" class="form-control" style="width: 100%" disabled>
                                <option value="">Seleccione...</option> 
                                    @foreach($divisas as $value)    
                                        <option {{ $ventas->divisas == $value->id ? 'selected' : ''}} value="$value->id">{{$value->descripcion}}</option>
                                    @endforeach    
                            </select>
                        </div>
                        <!--Fecha de la transacción; puede ser modificada por el usuario-->
                        <div class="col-md-2 col-xs-2" style="padding-left: 2px;padding-right: 2px;">
                            <label class="control-label" style="font-size: 13px">Fecha de Emisión:</label>
                            <div class="input-group" >
                                <input id="fecha" name="fecha" type="date" class="form-control"  value="{{$ventas->fecha}}" disabled>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">&nbsp;</div>
                <div class="col-md-12">
                    <div class="card1">
                        <div class="row">
                            <div class="col-md-12">
                              <h8>DATOS CLIENTE</h8>
                            </div>  
                            <!--Código del Cliente-->
                            <div class="col-md-2 col-xs-2">
                                <label for="id" class="control-label" style="font-size: 13px">Cliente:</label>
                                <select id="cliente" name="cliente" class="form-control select2_cliente" style="width: 100%" disabled>
                                    <option value="">Seleccione...</option> 
                                    @foreach($clientes as $value)    
                                        <option {{ $ventas->id_cliente == $value->identificacion ? 'selected' : ''}} value="$value->identificacion">{{$value->nombre}}</option>
                                    @endforeach    
                                </select>
                            </div>
                            <!--Direccion del Cliente-->
                            <div class="col-md-3 col-xs-3" style="padding-left: 2px;padding-right: 2px;">
                                <label class="control-label" style="font-size: 13px">Dirección:</label>
                                <input id="direccion" name="direccion" type="text" class="form-control"  value="@if(!is_null($ventas)){{$ventas->direccion_cliente}}@endif" disabled>
                            </div>
                            <!--Ruc/Cid del Cliente-->
                            <div class="col-md-2 col-xs-2" style="padding-left: 2px;padding-right: 2px;">
                                <label class="control-label" style="font-size: 13px">RUC/CID:</label>
                                <div class="input-group">
                                    <input id="ruc_cedula" name="ruc_cedula" type="text" class="form-control"  value="@if(!is_null($ventas)){{$ventas->ruc_id_cliente}}@endif" disabled>
                                </div>
                            </div>
                            <!--Telefono del Cliente-->
                            <div class="col-md-2 col-xs-2" style="padding-left: 2px;padding-right: 2px;">
                                <label class="control-label" style="font-size: 13px">Teléfono:</label>
                                <div class="input-group">
                                    <input id="telefono" name="telefono" type="text" class="form-control"  value="@if(!is_null($ventas)){{$ventas->telefono_cliente}}@endif" disabled>
                                </div>
                            </div>
                            <!--Email del Cliente-->
                            <div class="col-md-2 col-xs-2" style="padding-left: 2px;padding-right: 2px;">
                                <label class="control-label" style="font-size: 13px">Email:</label>
                                <div class="input-group">
                                    <input id="email" name="email" type="text" class="form-control"  value="@if(!is_null($ventas)){{$ventas->email_cliente}}@endif" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">&nbsp;</div>
                @php
                    if(!is_null($ventas)){
                      $seguro = Sis_medico\Seguro::find($ventas->seguro_paciente); 
                    }
                @endphp
      
                <div class="col-md-12">
                    <div class="card">
                        <div class="row">
                            <div class="col-md-12">
                              <h8>DATOS PACIENTE</h8>
                            </div>
                            <!--Cedula Paciente-->
                            <div class="col-md-2 col-xs-2">
                                <label class="control-label" style="font-size: 13px">Cédula:</label>
                                <input id="ced_paciente" name="ced_paciente" type="number" class="form-control"  value="@if(!is_null($ventas)){{$ventas->id_paciente}}@endif" disabled>
                            </div>
                            <!--Nombre Paciente-->
                            <div class="col-md-3 col-xs-3" style="padding-left: 2px;padding-right: 2px;">
                                <label class="control-label" style="font-size: 13px">Paciente:</label>
                                  <input id="nomb_paciente" name="nomb_paciente" type="text" class="form-control"  value="@if(!is_null($ventas)){{$ventas->nombres_paciente}}@endif" disabled>
                            </div>
                            <!--Seguro Paciente-->
                            <div class="col-md-2 col-xs-2" style="padding-left: 2px;padding-right: 2px;">
                                <label class="control-label" style="font-size: 13px">Seguro:</label>
                                  <input id="seguro" name="seguro" type="text" class="form-control"  value="@if(!is_null($seguro)){{$seguro->nombre}}@endif" disabled>
                            </div>
                            <!--Procedimiento-->
                            <div class="col-md-2 col-xs-2" style="padding-left: 2px;padding-right: 2px;">
                                <label class="control-label" style="font-size: 13px">Procedimiento:</label>
                                  <input id="procedimiento" name="procedimiento" type="text" class="form-control"  value="@if(!is_null($ventas)){{$ventas->procedimientos}}@endif" disabled>
                            </div>
                            <!--Fecha de Procedimiento-->
                            <div class="col-md-2 col-xs-2" style="padding-left: 2px;padding-right: 2px;">
                                <label class="control-label" style="font-size: 13px">Fecha de Procedimiento:</label>
                                  <input id="fecha_proced" name="fecha_proced" type="date" class="form-control"  value="@if(!is_null($ventas)){{$ventas->fecha_procedimiento}}@endif" disabled>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">&nbsp;</div>
                @if(!is_null($forma_pago))
                <div class="form-group col-md-12">
                    <div class="card">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <h8>FORMAS DE PAGO</h8>
                            </div>
                            <div class="form-group col-md-3 col-sm-3">
                                <label for="efectivo" class = "col-form-label-sm">VALOR EFECTIVO</label>
                                <input  type="text" id ="valor_efectivo" name="valor_efectivo"  class = "form-control form-control-sm" placeholder ="EFECTIVO" value="@if(!is_null($forma_pago)){{$forma_pago->valor_efectivo}}@endif" disabled>
                            </div>
                            <div class="form-group col-md-3 col-sm-3">
                                <label for="cheque" class = "col-form-label-sm">VALOR CHEQUE</label>
                                <input  type="text" id="valor_cheque" name="valor_cheque"  class = "form-control form-control-sm" placeholder = "VALOR" value="@if(!is_null($forma_pago)){{$forma_pago->valor_cheque}}@endif" disabled>
                            </div>
                            <div class="form-group col-md-3 col-sm-3">
                                <label for="tarjetacredito" class = "col-form-label-sm">VALOR TARJETA DE CREDITO</label>
                                <input  type="text" id = "valor_tarjetacredito" name="valor_tarjetacredito" class ="form-control form-control-sm" placeholder= "VALOR" value="@if(!is_null($forma_pago)){{$forma_pago->valor_credito}}@endif" disabled>
                            </div>
                            <div class="form-group col-md-3 col-sm-3">
                                <label for="tarjetadebito" class = "col-form-label-sm">VALOR TARJETA DE DÉBITO</label>
                                <input  type="text" id = "valor_tarjetadebito" name="valor_tarjetadebito" class ="form-control form-control-sm" placeholder = "VALOR" value="@if(!is_null($forma_pago)){{$forma_pago->valor_debito}}@endif" disabled>
                            </div>
                            
                            @if(!is_null($forma_pago->valor_cheque))
                            <div class="col-md-12" id="datos_cheque">
                                <div class="card">
                                    <div class="row">
                                        <div class="col-md-12">
                                          <h8>DATOS CHEQUE</h8>
                                        </div>
                                      
                                        <div class="col-md-2 col-xs-2">
                                            <label for="banco_cheque" class="control-label" style="font-size: 13px">Banco:</label>
                                            <div class="input-group">
                                                <select name="banco_cheque" id="banco_cheque" class="form-control" disabled> 
                                                    <option value="">Seleccione...</option> 
                                                    @foreach($banco  as $value)    
                                                     <option {{ $forma_pago->banco_cheque == $value->id ? 'selected' : ''}} value="$value->id">{{$value->nombre}}</option>
                                                    @endforeach 
                                                </select>
                                            </div>
                                        </div>
                                      
                                        <div class="col-md-2 col-xs-2" style="padding-left: 4px;padding-right: 2px;">
                                            <label for="cuenta_cheque" class="control-label" style="font-size: 13px">Cuenta:</label>
                                            <div class="input-group">
                                                <input  type="number" class="form-control" name="cuenta_cheque" id="cuenta_cheque" value="@if(!is_null($forma_pago)){{$forma_pago->cuenta_cheque}}@endif" placeholder="Cuenta" style="text-transform:uppercase;"disabled>
                                            </div>
                                        </div>
                                       
                                        <div class="col-md-2 col-xs-2" style="padding-left: 4px;padding-right: 2px;">
                                            <label for="numero_cheque" class="control-label" style="font-size: 13px">Número de Cheque:</label>
                                            <div class="input-group">
                                                <input  type="number" class="form-control" name="numero_cheque" id="numero_cheque" value="@if(!is_null($forma_pago)){{$forma_pago->numero_cheque}}@endif" placeholder="Número de Cheque" style="text-transform:uppercase;" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="col-md-12">&nbsp;</div>
                            
                            @if(!is_null($forma_pago->valor_credito))
                            <div class="col-md-12" id="datos_tarjeta_credito">
                                <div class="card">
                                    <div class="row">
                                        <div class="col-md-12">
                                          <h8>DATOS TARJETA CRÈDITO</h8>
                                        </div>
                                        <div class="col-md-2 col-xs-2" style="padding-left: 4px;padding-right: 2px;">
                                            <label for="banco_credito" class="control-label" style="font-size: 13px">Banco:</label>
                                            <div class="input-group">
                                                <select name="banco_credito" id="banco_credito" class="form-control" disabled> 
                                                    <option value="">Seleccione...</option> 
                                                    @foreach($banco as $value)    
                                                      <option {{$forma_pago->banco_credito == $value->id ? 'selected' : ''}} value="$value->id">{{$value->nombre}}</option>
                                                    @endforeach 
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2 col-xs-2" style="padding-left: 4px;padding-right: 2px;">
                                            <label for="nombre_titular_credito" class="control-label" style="font-size: 13px">Nombre Titular:</label>
                                            <div class="input-group">
                                                <input  type="text" class="form-control" name="nombre_titular_credito" id="nombre_titular_credito" value="@if(!is_null($forma_pago)){{$forma_pago->titular_credito}}@endif" placeholder="Nombre del Titular" style="text-transform:uppercase;" disabled>
                                            </div>
                                        </div>  
                                  
                                        <div class="col-md-2 col-xs-2" style="padding-left: 4px;padding-right: 2px;">
                                            <label for="numero_tarjeta_credito" class="control-label" style="font-size: 13px">Número Tarjeta:</label>
                                            <div class="input-group">
                                                <input  type="text" class="form-control cc_number" name="numero_tarjeta_credito" id="numero_tarjeta_credito" value="@if(!is_null($forma_pago)){{$forma_pago->num_tarjeta_credito}}@endif" placeholder="Numero de Tarjeta" style="text-transform:uppercase;" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-xs-2" style="top: 30px; right: 10px;">
                                           
                                           <span class="form-control-cc"><img style="width:2.775rem"/></span>
                                        
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="col-md-12">&nbsp;</div>
                            
                            @if(!is_null($forma_pago->valor_debito))
                            <div class="col-md-12" id="datos_tarjeta_debito">
                                <div class="card">
                                    <div class="row">
                                        <div class="col-md-12">
                                          <h8>DATOS TARJETA DEBITO</h8>
                                        </div>
                                 
                                        <div class="col-md-2 col-xs-2" style="padding-left: 4px;padding-right: 2px;">
                                            <label for="banco_debito" class="control-label" style="font-size: 13px">Banco:</label>
                                            <div class="input-group">
                                                <select name="banco_debito" id="banco_debito" class="form-control" disabled> 
                                                    <option value="">Seleccione...</option> 
                                                    @foreach($banco as $value)    
                                                       <option {{$forma_pago->banco_debito == $value->id ? 'selected' : ''}} value="$value->id">{{$value->nombre}}</option>
                                                    @endforeach 
                                                </select>
                                            </div>
                                        </div>
                                
                                        <div class="col-md-2 col-xs-2" style="padding-left: 4px;padding-right: 2px;">
                                            <label for="nombre_titular_debito" class="control-label" style="font-size: 13px">Nombre Titular:</label>
                                            <div class="input-group">
                                                <input  type="text" class="form-control" name="nombre_titular_debito" id="nombre_titular_debito" value="@if(!is_null($forma_pago)){{$forma_pago->titular_debito}}@endif" placeholder="Nombre del Titular" style="text-transform:uppercase;" disabled>
                                            </div>
                                        </div>
                                       
                                        <div class="col-md-2 col-xs-2" style="padding-left: 4px;padding-right: 2px;">
                                            <label for="numero_tarjeta_debito" class="control-label" style="font-size: 13px">Número Tarjeta:</label>
                                            <div class="input-group">
                                                <input  type="text" class="form-control cc_number" name="numero_tarjeta_debito" id="numero_tarjeta_debito" value="@if(!is_null($forma_pago)){{$forma_pago->num_tarjeta_debito}}@endif" placeholder="Numero de Tarjeta" style="text-transform:uppercase;" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-xs-2" style="top: 30px; right: 10px;">
   
                                             <span class="form-control-cc2"><img style="width:2.775rem"/></span>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
                <div class="col-md-12">&nbsp;</div>
                <div class="col-md-12" style="padding-top: 8px">
                    <div class="row">
                        <!--Nota-->
                        <label class="col-md-1 control-label" style="font-size: 13px">Detalle Asiento:</label>
                        <div class="col-md-6">
                            <input id="nota" name="nota" type="text" class="form-control" value="@if(!is_null($ventas)){{$ventas->nota}}@endif" disabled>
                        </div>
                         
                    </div>
                </div>
                <div class="col-md-12">&nbsp;</div>
                <!--Formas de Pago -->
            </form>
            <div class="table-responsive col-md-12" style="min-height: 100px; max-height: 250px;">
                <table id="example2" role="grid" aria-describedby="example2_info">
                    <caption><b>Detalle de Productos y Servicios</b></caption>
                    <thead style="background-color: #FFF3E3">
                        <tr style="position: relative;">
                          <th style="width: 10%; text-align: center;">Código</th>
                          <th style="width: 15%; text-align: center;">Descripción del Producto/Servicio</th>
                          <!--<th style="width: 5%; text-align: center;">Bodega</th>-->
                          <th style="width: 5%; text-align: center;">Cantidad</th>
                          <!--<th style="width: 5%; text-align: center;">Empaque</th>-->
                          <!--<th style="width: 5%; text-align: center;">Total</th>-->
                          <th style="width: 5%; text-align: center;">Precio Unitario</th>
                          <th style="width: 5%; text-align: center;">Des%</th>
                          <th style="width: 5%; text-align: center;">Desc.</th>
                          <th style="width: 5%; text-align: center;">Precio Neto</th>
                          <th style="width: 1%; text-align: center;">Iva</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detalle_venta as $value)
                        <tr style="position: relative;">
                            <td style="width: 10%;text-align: center;">@if(!is_null($value->id_ct_productos)){{$value->id_ct_productos}}@endif</td>
                            <td style="width: 10%; text-align: center;">@if(!is_null($value->nombre)){{$value->nombre}}@endif</td>
                            <!--<td style="width: 5%; text-align: center;">@if(!is_null($value->bodega)){{$value->bodega}}@endif</td>-->
                            <td style="width: 5%; text-align: center;">@if(!is_null($value->cantidad)){{$value->cantidad}}@endif</td>
                            <!--<td style="width: 5%; text-align: center;">@if(!is_null($value->empaque)){{$value->empaque}}@endif</td>-->
                            <!--<td style="width: 5%; text-align: center;">@if(!is_null($value->total)){{$value->total}}@endif</td>-->
                            <td style="width: 5%; text-align: center;">@if(!is_null($value->precio)){{$value->precio}}@endif</td>
                            <td style="width: 5%; text-align: center;">@if(!is_null($value->descuento_porcentaje)){{$value->descuento_porcentaje}}@endif</td>
                            <td style="width: 5%; text-align: center;">@if(!is_null($value->descuento)){{$value->descuento}}@endif</td>
                            <td style="width: 5%; text-align: center;">@if(!is_null($value->extendido)){{$value->extendido}}@endif</td>
                            <td style="width: 5%; text-align: center;"><input type="checkbox"  @if($value->check_iva == '1') checked @endif disabled></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!--Calculo de Valores -->
                <div class="col-md-12" style="padding-top: 20px">
                    <div class="row">
                        <div class="col-md-9"></div>
                        <div class="col-md-3">
                            <div class="col-md-12" style="padding: 5px">
                                <div class="row">
                                    <label class="col-md-6">Subtotal sin IVA</label>
                                    <input class="col-md-6" type="text" name="subtotal" id="subtotal" value="@if(!is_null($ventas)){{$ventas->subtotal}}@endif" disabled>
                                    
                                </div>
                                
                            </div>
                            <div class="col-md-12" style="padding: 5px" >
                                <div class="row">
                                    <label class="col-md-6">Descuento</label>
                                    <input class="col-md-6" type="text" name="descuento" id="descuento" value="@if(!is_null($ventas)){{$ventas->descuento}}@endif" disabled>
                                    
                                </div>
                            </div>
                            <div class="col-md-12" style="padding: 5px" >
                                <div class="row">
                                    <label class="col-md-6">Base Imponible</label>
                                    <input class="col-md-6" type="text" name="descuento" id="descuento" value="@if(!is_null($ventas)){{$ventas->base_imponible}}@endif" disabled>
                                    
                                </div>
                            </div>
                            <div class="col-md-12" style="padding: 5px">
                                <div class="row">
                                    <label class="col-md-6">Tarifa Iva 12%</label>
                                    <input class="col-md-6" type="text" name="impuesto" id="impuesto" value="@if(!is_null($ventas)){{$ventas->impuesto}}@endif" disabled>
                                
                                </div>
                            </div>
                            <div class="col-md-12" style="padding: 5px">
                                <div class="row">
                                    <label class="col-md-6">Transporte</label>
                                    <input class="col-md-6" type="text" name="transporte" id="transporte" value="@if(!is_null($ventas)){{$ventas->transporte}}@endif" disabled>
                                   
                                </div>
                            </div>
                            <div class="col-md-12" style="padding: 5px">
                                <div class="row">
                                    <label class="col-md-6">Total</label>
                                    <input class="col-md-6" type="text" name="total" id="total" value="@if(!is_null($ventas)){{$ventas->total_final}}@endif" disabled>
                                  
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
//<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/hospital/cleave/dist/cleave.min.js")}}"></script>

<script type="text/javascript">

    
    $(document).ready(function(){

        $('.select2_cliente').select2({
            tags: false
        });

        $('#iva').iCheck({
           checkboxClass: 'icheckbox_flat-blue',
           increaseArea: '20%' // optional
        });
    
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

    /*function crear_factura_venta(){
        $('#crear_factura').submit();
    }*/
   
</script>

<script type="text/javascript">
    $(document).ready(function(){
        //CREDIT CARD PREVIEW INPUT
        initCreditCardPreview();
        initCreditCardPreview2();
        //NUMERIC INPUTS
        var cleave = new Cleave('.numeric1', {
          numeral: true
        });
        var cleave = new Cleave('.numeric2', {
          numeral: true
        });
        var cleave = new Cleave('.numeric3', {
          numeral: true
        });
        //CVC NUMBER HELP MODAL
        $('[data-toggle="tooltip"]').tooltip();
    });

    function initCreditCardPreview(){

        var path_to_icon='http://192.168.75.109/sis_medico_prb/public/hospital/assets/images/credit-cards/';
        var icon='generic-mono.svg';
        $('.form-control-cc img').attr('src',path_to_icon+icon);
        var cleaveCreditCard = new Cleave('#numero_tarjeta_credito', {
        creditCard: true,
        onCreditCardTypeChanged: function(type) {
            switch(type){
                case 'visa':
                icon='visa.svg';
                break;
                case 'mastercard':
                icon='mastercard.svg';
                break;
                case 'diners':
                icon='diners.svg';
                break;
                case 'discover':
                icon='discover.svg';
                break;
                case 'maestro':
                icon='maestro.svg';
                break;
                case 'jcb':
                icon='jcb.svg';
                break;
                case 'alipay':
                icon='alipay.svg';
                break;
                case 'amex':
                icon='amex.svg';
                break;
                case 'elo':
                icon='elo.svg';
                break;
                case 'hipercard':
                icon='hipercard.svg';
                break;
                case 'paypal':
                icon='paypal.svg';
                break;
                case 'unionpay':
                icon='unionpay.svg';
                break;
                case 'unknown':
                icon='generic-mono.svg';
                break;
            }      
            $('.form-control-cc img').attr('src',path_to_icon+icon);          
        }
        });
    }
    
    function initCreditCardPreview2(){

    var path_to_icon='http://192.168.75.109/sis_medico_prb/public/hospital/assets/images/credit-cards/';
    var icon='generic-mono.svg';
    $('.form-control-cc2 img').attr('src',path_to_icon+icon);
    var cleaveCreditCard = new Cleave('#numero_tarjeta_debito', {
    creditCard: true,
    onCreditCardTypeChanged: function(type) {
        switch(type){
            case 'visa':
            icon='visa.svg';
            break;
            case 'mastercard':
            icon='mastercard.svg';
            break;
            case 'diners':
            icon='diners.svg';
            break;
            case 'discover':
            icon='discover.svg';
            break;
            case 'maestro':
            icon='maestro.svg';
            break;
            case 'jcb':
            icon='jcb.svg';
            break;
            case 'alipay':
            icon='alipay.svg';
            break;
            case 'amex':
            icon='amex.svg';
            break;
            case 'elo':
            icon='elo.svg';
            break;
            case 'hipercard':
            icon='hipercard.svg';
            break;
            case 'paypal':
            icon='paypal.svg';
            $("#tipo_tarjeta_credito2").val('10');
            break;
            case 'unionpay':
            icon='unionpay.svg';
            break;
            case 'unknown':
            icon='generic-mono.svg';
            break;
        }      
        $('.form-control-cc2 img').attr('src',path_to_icon+icon);          
    }
    });
    }
</script>

</section>
@endsection
