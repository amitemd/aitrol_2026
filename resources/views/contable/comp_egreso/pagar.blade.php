@extends('contable.comp_egreso.base')
@section('action-content')
<style type="text/css">
  .control_width{
    width: 90%;
    height: 60%;
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
  .cabecera{
      background-color: #3c8dbc;
      border-radius: 8px;
  }
  .color_cabecera{
      color: white;
  }
  .color_label{
        color: #ffffff;
  }
  table {
  border-collapse: collapse;
  width: 100%;
  }

    th, td {
    padding: 8px;
    text-align: left;
    border-bottom: 1px solid #ddd;
    }


</style>
<script type="text/javascript">
    function goBack() {
      window.history.back();
    }
    
</script>

<section class="content">
    <div class="box cabecera">
            <div class="header box-header with-border">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-9 col-sm-9 col-6">
                            <div class="box-title size_text" ><b style="font-size: 16px; color: white;">PAGO A FACTURA</b></div>
                        </div>
                        <div class="col-3" style="text-align: right;">
                            <button type="button" onclick="goBack()" class="btn btn-primary btn-sm" style="color:white; border-radius: 5px; border: 2px solid white;">
                            <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body" style="background-color: white;">
                <form id="guardar_anticipo" method="post">
                <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-12">
                                    @if($proveedor!=null)
                                        <div style="text-align:center;">
                                            <img  src="{{asset('/logo').'/'.$proveedor->logo}}"  style="text-align: center; width:350px;height: 100px">
                                        </div>
                                        
                                        @if($compras!=null)
                                        <div style="text-align: center; font-size:0.8em">
                                            R.U.C.: {{$proveedor->id}}<br/>
                                            Nombre Comercial: {{$proveedor->nombrecomercial}}<br/>
                                            Teléfono: {{$proveedor->telefono1}}<br/>
                                            Dir.Matriz: {{$proveedor->direccion}}<br/>
                                            <br/>
                                        </div>
                                        @else
                                        <div style="text-align: center; font-size:0.8em">
                                            R.U.C.: {{$proveedor->id}}<br/>
                                            Nombre Comercial: {{$proveedor->nombrecomercial}}<br/>
                                            Teléfono: {{$proveedor->telefono1}}<br/>
                                            Dir.Matriz: {{$proveedor->direccion}}<br/>
                                            <br/>
                                        </div>
                                        @endif
                                    @endif
                                    </div>
                                    <div class="table-responsive col-md-12">
                                         <input name="contador_pago" id="contador_pago" type="hidden" value="0">
                                        <table id="example1" role="grid" aria-describedby="example2_info" >
                                            <thead class="cabecera">
                                                <tr class="color_cabecera">
                                                    <th style="width: 4%; text-align: center;">Tipo</th>
                                                    <th style="width: 10%; text-align: center;">Fecha</th>
                                                    <th style="width: 10%; text-align: center;">Número</th>
                                                    <th style="width: 7%; text-align: center;">Banco</th>
                                                    <th style="width: 7%; text-align: center;">Cuenta</th>
                                                    <th style="width: 7%; text-align: center;">Valor</th>
                                                    <th style="width: 7%; text-align: center;">Valor Base</th>
                                                    <th style="width: 7%; text-align: center;">Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody id="agregar_pago">
                                            </tbody>
                                        </table>
                                     </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-2 col-xs-2">
                                                <div class="box-footer" style="background: #fffff;">
                                                    <button type="button" id="btn_pago" class="btn btn-primary size_text">
                                                    Agregar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4"> 
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="control-label">TOTAL</label>
                                        <input class="form-control input-sm" type="text" name="totalfinal" id="totalfinal" readonly>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="control-label">TOTAL DEUDA</label>
                                        <input type="text" class="form-control input-sm" name="total_deuda" id="total_deuda" readonly>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="control-label">SUPERAVIT</label>
                                        <input type="text" class="form-control input-sm" name="superavit" id="superavit">
                                    </div>
                                    <div class="col-md-12" style="top: 8px;">
                                        <textarea class="form-control input-sm" name="observacion" id="observacion" cols="15" rows="5"></textarea>
                                    </div>
                                    <div class="col-md-12" style="top: 12px;">
                                        <button type="button" id="pago_fact" class="btn btn-primary">REALIZAR PAGO A LA FACTURA</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6" id="enfactura" style="display:none;">
                                <label class="control-label">PAGO EN FACTURA</label>
                            </div>
                            <div class="col-md-6" id="enfectivo" style="display:none;">
                                <label class="control-label">PAGO CON EFECTIVO</label>
                                <input class="form-control input-sm" name="pago_efectivo" id="pago_efectivo">
                                <button style="margin-top: 7px;" type="button" class="btn btn-danger" id="boton_faltante" onclick="agregar_valor()">Agregar</button>
                            </div>
                            <div class="col-md-6" id="encredito" style="display:none;">
                                <label class="control-label">PAGO EN TARJETA DE CRÉDITO</label>
                                <input type="text" class="form-control input-sm" name="pago_credito" id="pago_credito">
                                <button type="button" class="btn btn-danger" id="boton_credito" name="boton_credito">Agregar</button>
                            </div>
                            <div class="col-md-6" id="encheque" style="display:none;">
                                <label class="control-label">PAGO EN CHEQUE</label>
                                <input type="text" class="form-control input-sm" name="pago_cheque" id="pago_cheque">
                                <button type="button" class="btn btn-danger" id="boton_credito" name="boton_credito">Agregar</button>
                            </div>
                            <div class="col-md-6" id="entransferencia" style="display:none;">
                                <label class="control-label">PAGO TRANSFERENCIA BANCARIA</label>
                                <input type="text" class="form-control input-sm" name="transferencia_bancaria" id="transferencia_bancaria">
                                <button type="button" class="btn btn-danger" id="boton_credito" name="boton_credito">Agregar</button>
                            </div>
                            <div class="col-md-6" id="endebito" style="display:none;">
                                <label class="control-label">PAGO TARJETA DE DÉBITO</label>
                                <input type="text" class="form-control input-sm" name="tarjeta_debito" id="tarjeta_debito">
                                <button type="button" class="btn btn-danger" id="boton_credito" name="boton_credito">Agregar</button>
                            </div>
                            <div class="col-md-6" id="endeposito" style="display:none;">
                                <label class="control-label">PAGO EN DEPOSITO</label>
                                <input type="text" class="form-control input-sm" name="pago_deposito" id="pago_deposito">
                                <button type="button" class="btn btn-danger" id="boton_credito" name="boton_credito">Agregar</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                            &nbsp;
                            </div>
                            <div class="col-md-12" style="text-align: center;">
                                <label class="control-label">ANTICIPO A PROVEEDORES</label>
                            </div>
                            <div class="col-md-12">          
                                            <div class="table-responsive col-md-12" >
                                                    <table id="example12" style="text-align: center;" role="grid" aria-describedby="example2_info">
                                                        <thead class="cabecera">
                                                        <tr class="color_cabecera" style="position: relative;">
                                                            <th style="width: 10%; text-align: center;">#</th>
                                                            <th style="width: 20%; text-align: center;">Proveedor</th>
                                                            <th style="width: 20%; text-align: center;">Empresa</th>
                                                            <th style="width: 20%; text-align: center;">Comprobante</th>
                                                            <th style="width: 10%; text-align: center;">Total</th>
                                                            <th style="width: 10%; text-align: center;">Fecha</th>
                                                            <th style="width: 10%; text-align: center;">Acción</th>
                                                        </tr>
                                                        </thead>
                                                                @php
                                                                    $contadore=0;
                                                                @endphp
                                                            <tbody id="datos_a">
                                                                @foreach($anticipos as $value)
                                                                    <tr>
                                                                        <td><input style="width: 91%;" type="text" name="id_anticipo" name="id_anticipo" class="form-control input-sm" value="{{$value->id}}" readonly></td>
                                                                        <td><input class="form-control input-sm" type="text" style="width: 92%;" name="nombre_proveedor" id="nombre_proveedor" value="{{$value->proveedor->nombrecomercial}}"readonly></td>
                                                                        <td><input class="form-control input-sm" type="text" style="width: 92%;" name="nombre_empresa" id="nombre_empresa" value="{{$value->id_empresa}}"readonly></td>
                                                                        <td> <input class="form-control input-sm" type="text" style="width: 92%;" name="nro_comprobante" id="nro_comprobante" value="{{$value->nro_comprobante}}-{{$value->secuencia}}"readonly></td>
                                                                        <td><input class="form-control input-sm" type="text" style="width: 92%;" name="total_anticipo{{$contadore}}" id="total_anticipo{{$contadore}}" value="{{$value->total}}"readonly></td>
                                                                        <td> <input class="form-control input-sm" type="text" style="width: 92%;" name="fecha_pago" id="fecha_pago" value="{{$value->fecha_pago}}"readonly></td>
                                                                        <td style="text-align: center;"><input class="checkValu" type="checkbox" name="checku{{$contadore}}" id="checku{{$contadore}}"readonly></td>
                                                                    </tr>
                                                                    @php $contadore = $contadore +1; @endphp
                                                                @endforeach
                                                            </tbody>
                                                        <tfoot>
                                                        </tfoot>
                                                    </table>
                                            </div>
                                        
                            </div>
                            <div class="col-md-12" style="text-align: center;">
                                <label class="control-label">FACTURAS PENDIENTE DE PAGOS</label>
                            </div>
                            <div class="col-md-12">          
                                            <div class="table-responsive col-md-12" style="min-height: 250px; max-height: 250px; top: 20px;" >
                                                    <table id="example20" role="grid" aria-describedby="example2_info">
                                                        <thead class="cabecera" >
                                                        <tr class="color_cabecera" style="position: relative;">
                                                            <th style="width: 10%; text-align: center;">#</th>
                                                            <th style="width: 20%; text-align: center;">Secuencia</th>
                                                            <th style="width: 20%; text-align: center;">Fecha</th>
                                                            <th style="width: 20%; text-align: center;">Total</th>
                                                            <th style="width: 10%; text-align: center;">Valor Contable</th>
                                                            <th style="width: 10%; text-align: center;">Acción</th>
                                                        </tr>
                                                        </thead>
                                                            <tbody id="datos_b">
                                                                @php
                                                                    $contador=0;
                                                                @endphp
                                                                @foreach($facturas as $value)
                                                                    <tr>
                                                                        <td style="text-align: center;"><input class="form-control input-sm" style="width: 92%;" type="text" name="id_factura" id="id_factura" value="{{$value->id}}"readonly></td>
                                                                        <td><input type="text" style="width: 92%;" name="numero_factura" id="numero_factura" class="form-control input-sm" value="{{$value->numero}}"readonly></td>
                                                                        <td><input type="text" style="width: 92%;" class="form-control input-sm" name="fecha_factura" id="fecha_factura" value="{{$value->fecha}}"readonly></td>
                                                                        <td> <input style="width: 92%;" class="form-control input-sm" type="text" name="total_final{{$contador}}" id="total_final{{$contador}}" value="{{$value->total_final}}" readonly></td>
                                                                        <td><input class="form-control input-sm" style="width: 92%;" type="text" name="valor_contable" id="valor_contable" value="{{$value->valor_contable}}" readonly></td>
                                                                        <td style="text-align: center;"><input class="checkVal" type="checkbox" name="check{{$contador}}" id="check{{$contador}}" ></td>
                                                                    </tr>
                                                                    @php $contador = $contador +1; @endphp
                                                                @endforeach
                                                            </tbody>
                                                        <tfoot>
                                                        </tfoot>
                                                    </table>
                                            </div>
                            </div>
                            <div class="col-md-12">
                                &nbsp;
                            </div>
                            
                        </div>    
                </div> 
                </form>

            </div>
    </div>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/hospital/cleave/dist/cleave.min.js")}}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">
     $(document).ready(function(){

        $('#example12').DataTable({
        'paging'      : false,
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : false,
        'info'        : true,
        'autoWidth'   : true,
        'sInfoEmpty':  true,
        'sInfoFiltered': true,
        'language': {
              "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
          }
        });
        $('#example20').DataTable({
        'paging'      : false,
        'lengthChange': true,   
        'searching'   : true,
        'ordering'    : false,
        'info'        : true,
        'autoWidth'   : true,
        'sInfoEmpty':  true,
        'sInfoFiltered': true,
        'language': {
              "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
          }
        });



    });
    function limpiar(){
        $("#encredito").hide();
        $("#encheque").hide();
        $("#enfactura").hide();
        $("#entransferencia").hide();
        $("#enefectivo").hide();
        $("#endebito").hide();
        $("#enfectivo").hide();
    }    
    $(".checkVal").click(function() {
        if ($(this).is(':checked')) {
            var item = $(this).attr('name');
            item = item.substring(5);
            var saldo = parseFloat($("#total_final"+item).val());
            var saldo_intermedio= parseFloat($("#totalfinal").val());
            var saldo1= parseFloat($("#total_deuda").val());
            var superavit= parseFloat($("#superavit").val());
            var total= saldo_intermedio-saldo;
            if(!isNaN(saldo1)){
                total= saldo1-saldo;
            }else{
                saldo1=0;
            }
            if(total<0){
                if(!isNaN(superavit)){
                    total= (superavit+saldo_intermedio)*-1;
                   //salert(total);
                }else{
                    total= total*-1;
                }
                //alert(total);
                if(Math.sign(total)!=1){
                    total= total*-1;
                }
                $("#superavit").val(total.toFixed(2,2));
                total=0;
            }
            $("#total_deuda").val(total.toFixed(2,2));
            
        }else{
            var item = $(this).attr('name');
            item = item.substring(5);
            var saldo = parseFloat($("#total_final"+item).val());
            var saldo_intermedio= parseFloat($("#totalfinal").val());
            var saldo1= parseFloat($("#total_deuda").val());
            var superavit= parseFloat($("#superavit").val());
            var total= saldo1+saldo;
            if(!isNaN(saldo1)){
               total= saldo1-saldo;
            }else{
                saldo1=0;
            }
            if(total<0){
                if(!isNaN(superavit)){
                    total= (superavit+total)*-1;
                }else{
                    total= total*-1;
                }
                $("#superavit").val(total.toFixed(2,2));
                total=0;

            }
            $("#total_deuda").val(total.toFixed(2,2));
        }
    });
    $(".checkValu").click(function() {
        if ($(this).is(':checked')) {
            var item = $(this).attr('name');
            item = item.substring(6);
            var saldo = parseFloat($("#total_anticipo"+item).val());
            var saldo_intermedio= parseFloat($("#totalfinal").val());
            var saldo1= parseFloat($("#total_deuda").val());
            var superavit= parseFloat($("#superavit").val());
            var total= saldo_intermedio-saldo;
            if(!isNaN(saldo1)){
                total= saldo1-saldo;
            }else{
                saldo1=0;
            }
            if(total<0){
                if(!isNaN(superavit)){
                    total= (superavit+saldo_intermedio)*-1;
                   //salert(total);
                }else{
                    total= total*-1;
                }
                //alert(total);
                if(Math.sign(total)!=1){
                    total= total*-1;
                }
                $("#superavit").val(total.toFixed(2,2));
                total=0;
            }
            $("#total_deuda").val(total.toFixed(2,2));
            
        }else{
            var item = $(this).attr('name');
            item = item.substring(5);
            var saldo = parseFloat($("#total_anticipo"+item).val());
            var saldo_intermedio= parseFloat($("#totalfinal").val());
            var saldo1= parseFloat($("#total_deuda").val());
            var superavit= parseFloat($("#superavit").val());
            var total= saldo1+saldo;
            if(!isNaN(saldo1)){
               total= saldo1-saldo;
            }else{
                saldo1=0;
            }
            if(total<0){
                if(!isNaN(superavit)){
                    total= (superavit+total)*-1;
                }else{
                    total= total*-1;
                }
                $("#superavit").val(total.toFixed(2,2));
                total=0;

            }
            $("#total_deuda").val(total.toFixed(2,2));
        }
    });
    function traer_cuentas_banco(){
        var opciones= $("#tipo_pago").val();
        $.ajax({
            type: 'post',
            url:"{{route('anticipo.bancos')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'opciones': opciones},
            success: function(data){
                //console.log(data);
                if(data.value!='no'){
                    if(opciones!=0){
                        $("#bancos").empty();
                        $("#bancos").show('slow');
                        $("#bancos").append('<option value="0">Seleccione...</option>');
                        $.each(data,function(key, registro) {
                            $("#bancos").append('<option value='+registro.id+'>'+registro.nombre+'</option>');
                        });
                    }else{
                        $("#bancos").empty();
                    }
                }
            },
            error: function(data){
                console.log(data);
            }
        });
    }
    $('#btn_pago').click(function(event){

        id= document.getElementById('contador_pago').value;


        var midiv_pago = document.createElement("tr")
            midiv_pago.setAttribute("id","dato_pago"+id);
        midiv_pago.innerHTML = '<td><select class="select2_cuentas" name="id_tip_pago'+id+'" id="id_tip_pago'+id+'" style="width: 175px;height:25px" onchange="desabilita_componente(this,'+id+');"><option value="">Seleccione</option> @foreach($tipo_pago as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select><input type="hidden" id="visibilidad_pago'+id+'" name="visibilidad_pago'+id+'" value="1"></td><td><input type="date" class="input-number" value="{{date('Y-m-d')}}" name="fecha'+id+'" id="fecha'+id+'" style="width: 110px;"></td><td><div><input type="text" name="numero'+id+'" id="numero'+id+'" style="width: 100px;" required></div></td><td><select class="select2_cuentas" name="id_banco'+id+'" id="id_banco'+id+'" style="width: 175px;height:25px"><option value="">Seleccione...</option>@foreach($lista_banco as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select></td><td><select class="select2_cuentas" id="id_cuenta'+id+'" name="id_cuenta'+id+'" style="width: 175px;height:25px"><option value="">Seleccione...</option>@foreach($cuentas as $value)<option value="{{$value->id}}" style="width: 175px;height:25px">{{$value->nombre}}</option>@endforeach</select></td><td><div><input type="text" id="valor'+id+'" name="valor'+id+'" style="width: 100px;" onchange="return redondea_valor(this,'+id+',2);" onkeyup="suma_valor_forma_pago()"></div></td><td><div><input type="text" id="valor_base'+id+'" name="valor_base'+id+'" style="width: 100px;" onchange="return redondea_valor_base(this,'+id+',2);"></div></td><td><button type="button" onclick="eliminar_form_pag('+id+')" class="btn btn-warning btn-margin">Eliminar </button></td>';
        document.getElementById('agregar_pago').appendChild(midiv_pago);
        
        id = parseInt(id);
        id = id+1;
        document.getElementById('contador_pago').value = id;
        $('.select2_cuentas').select2({
            tags: false
        });

    });
    function subir_sueldo(){
        var opciones= $("#tipo_pago").val();
        switch(opciones){
            case '1':
                document.getElementById("enfectivo").style.display = "block";
                document.getElementById("endebito").style.display = "hidden";
                document.getElementById("encheque").style.display = "hidden";
                document.getElementById("endeposito").style.display = "hidden";
                document.getElementById("encredito").style.display = "hidden";
                document.getElementById("entransferencia").style.display = "hidden";
                break;
            case '2':
                document.getElementById("encheque").style.display = "block";
                document.getElementById("enfectivo").style.display = "hidden";
                document.getElementById("endebito").style.display = "hidden";
                document.getElementById("endeposito").style.display = "hidden";
                document.getElementById("encredito").style.display = "hidden";
                document.getElementById("entransferencia").style.display = "hidden";
                break;
            case '3':
                document.getElementById("encheque").style.display = "hidden";
                document.getElementById("enfectivo").style.display = "hidden";
                document.getElementById("endebito").style.display = "hidden";
                document.getElementById("endeposito").style.display = "block";
                document.getElementById("encredito").style.display = "hidden";
                document.getElementById("entransferencia").style.display = "hidden";
                break;
            case '4':
                document.getElementById("encheque").style.display = "hidden";
                document.getElementById("enfectivo").style.display = "hidden";
                document.getElementById("endebito").style.display = "hidden";
                document.getElementById("endeposito").style.display = "hidden";
                document.getElementById("encredito").style.display = "block";
                document.getElementById("entransferencia").style.display = "hidden";
                break;
            case '5':
                document.getElementById("encheque").style.display = "hidden";
                document.getElementById("enfectivo").style.display = "hidden";
                document.getElementById("endebito").style.display = "hidden";
                document.getElementById("endeposito").style.display = "hidden";
                document.getElementById("encredito").style.display = "hidden";
                document.getElementById("entransferencia").style.display = "block";
                break;
            case '6':
                document.getElementById("encheque").style.display = "hidden";
                document.getElementById("enfectivo").style.display = "hidden";
                document.getElementById("endebito").style.display = "block";
                document.getElementById("endeposito").style.display = "hidden";
                document.getElementById("encredito").style.display = "hidden";
                document.getElementById("entransferencia").style.display = "hidden";
                break;
        }
        
    }                 
    function suma_valor_forma_pago(){
        contador_pag  =  0;
        sum_vbase = 0;


        $("#agregar_pago tr").each(function(){
           $(this).find('td')[0];

            visibilidad_pag = $(this).find('#visibilidad_pago'+contador_pag).val();
            val_p = parseFloat($(this).find('#valor'+contador_pag).val());

            if(visibilidad_pag == 1){

               sum_vbase = sum_vbase + val_p;

            }

            contador_pag = contador_pag+1;

        });


        //Campos Visibles
        if(isNaN(sum_vbase)){
            sum_vbase=0;
        }
        $('#totalfinal').val(sum_vbase.toFixed(2,2));

        //Campos Oculto
        $('#valor_pago').val(sum_vbase.toFixed(2));


    }                 
    function eliminar_form_pag(valor)
    {
        var dato_pago1 = "dato_pago"+valor;
        var nombre_pago2 = 'visibilidad_pago'+valor;
        document.getElementById(dato_pago1).style.display='none';
        document.getElementById(nombre_pago2).value = 0;
        suma_valor_forma_pago();

    }     
</script>

@endsection