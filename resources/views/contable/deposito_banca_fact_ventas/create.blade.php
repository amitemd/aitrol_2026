@extends('contable.deposito_banca_fact_ventas.base')
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
          padding: 4px;
          background: #3d7ba8;
          color: #FFF;
        }
        
        table tr:nth-child(odd){
          background: #FFF;
        }
        
        table td{
          padding: 4px;
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
            padding: 2px;
            padding-left:6px;
           
        }
        
</style>

<script src="{{ asset ("/js/jquery-ui.js")}}"></script>

<section class="content">
    <form class="form-vertical" id="crear_deposito_bancario" role="form" method="POST">
    {{ csrf_field() }}
        <input type="text" name="ndeposito" id="ndeposito" class="hidden" value="">
        <div class="box box-primary box-solid " style="background-color: white;">
            <div class="header box-header with-border" >
                <div class="col-12">    
                    <div class="row"> 
                        <div class="box-title col-md-6" >
                            
                             <label style="color: white">DEPÓSITO BANCARIO</label>
                        </div>
                        <div class="col-md-5" style="padding-right: 0px;right: 0px;">
                            <div class="row">
                                <div class="col-md-6">
                                </div>
                                <div class="col-md-3">
                                    <button  type="button" onclick="crear_depo_bancario()" class="btn btn-primary" style="color:white; border-radius: 5px; border: 2px solid white;" id="btn_guardar"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>Guardar
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
                        <div class="col-md-12" style="padding-left: 17px">
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
                                        <label for="id" class="control-label" class="control-label">ID:</label>
                                        <div class="input-group">
                                            <input id="id" name="id" type="text" class="form-control" value="{{old('id')}}" placeholder="id" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-xs-2">
                                        <label for="numero" class="control-label" class="control-label">Nùmero:</label>
                                        <div class="input-group">
                                            <input id="numero" name="numero" type="text" class="form-control"  value="{{old('numero')}}" placeholder="numero" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-xs-2">
                                        <label for="num_asiento" class="control-label" class="control-label">Asiento:</label>
                                        <div class="input-group">
                                            <input id="num_asiento" name="num_asiento"  type="text" class="form-control"  value="{{old('num_asiento')}}" placeholder="asiento" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-xs-2">
                                        <label for="num_comprobante" class="control-label">N# Comprobante:</label>
                                        <div class="input-group">
                                            <input id="num_comprobante" name="num_comprobante"  type="text" class="form-control"  value="{{old('num_comprobante')}}" placeholder="Numero Comprobante" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-xs-2">
                                        <label for="fecha_factura" class="control-label">Fecha Factura:</label>
                                        <div class="input-group">
                                            <input id="fecha_factura" name="fecha_factura"  type="text" class="form-control"  value="{{old('fecha_factura')}}" placeholder="Fecha Factura" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-2 col-xs-2" style="padding-left: 12px;padding-right: 2px;">
                                        <label  for="fecha_emision" class="control-label">Fecha:</label>
                                        <div class="input-group date">
                                            <input  type="date" class="form-control" name="fecha" id="fecha"
                                            value="" autocomplete="off">
                                            <div class="input-group-addon" style="padding-left: 4px;padding-right: 2px;">
                                                <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('fecha').value = '';"></i>
                                            </div>   
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-xs-2" style="padding-left: 16px;">
                                        <label for="tipo" class="control-label" class="control-label">Tipo:</label>
                                        <div class="input-group">
                                            <input id="tipo" maxlength="25" type="text" readonly class="form-control" name="tipo" value="BAN-DP">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" style="padding-top: 10px"></div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <!--Cta.Comision-->
                                        <div class="form-group col-xs-6">
                                            <label for="caja_origen" class="col-md-2 control-label">Caja de Origen</label>
                                            <div class="col-md-9">
                                                <select class="form-control select2_cuentas"  name="caja_origen" id="caja_origen">
                                                    <option value="">Seleccione...</option> 
                                                    @foreach($cuentas as $value)    
                                                        <option value="{{$value->id}}">{{$value->nombre}}</option>
                                                    @endforeach    
                                                </select>
                                            </div>
                                        </div>
                                        <!--Cta.Comision-->
                                        <div class="form-group col-xs-6">
                                            <label for="cta_destino" class="col-md-2 control-label" >Cuenta Destino</label>
                                            <div class="col-md-9">
                                                <select class="form-control select2_cuentas"  name="cta_destino" id="cta_destino">
                                                    <option value="">Seleccione...</option> 
                                                    @foreach($cuentas as $value)    
                                                        <option value="{{$value->id}}">{{$value->nombre}}</option>
                                                    @endforeach    
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive col-md-12" style="min-height: 100px; max-height: 250px;">
                            <table id="example2" role="grid" aria-describedby="example2_info">
                                <caption><b>Detalle de Valores Recibidos a Depositar</b></caption>
                                <thead style="background-color: #FFF3E3">
                                    <tr style="position: relative;">
                                      <th style="width: 2%; text-align: center;">Seleccionar</th>
                                      <th style="width: 7%; text-align: center;">Ingreso</th>
                                      <th style="width: 10%; text-align: center;">Tipo</th>
                                      <th style="width: 7%; text-align: center;">Fecha</th>
                                      <th style="width: 10%; text-align: center;">cheque</th>
                                      <th style="width: 7%; text-align: center;">Banco</th>
                                      <th style="width: 7%; text-align: center;">Cuenta</th>
                                      <th style="width: 10%; text-align: center;">Girador</th>
                                      <!-- <th style="width: 7%; text-align: center;">Div.</th>-->
                                      <th style="width: 7%; text-align: center;">Importe</th>
                                      <th style="width: 7%; text-align: center;">Valor</th>
                                      <th style="width: 7%; text-align: center;">Valor Base</th>
                                    </tr>
                                </thead>
                                <tbody id="crear">
                                    @php $cont=0; @endphp
                                    @foreach (range(1, 4) as $i)
                                    <tr>
                                        <td><input type="checkbox" id="selecciona" name="selecciona"></td>
                                        <td> <input class="form-control" type="text" name="ingreso_recib{{$cont}}" id="ingreso_recib{{$cont}}" readonly></td>
                                        <td> <input class="form-control" type="text" name="tipo_dep_recib{{$cont}}" id="tipo_dep_recib{{$cont}}" readonly></td>
                                        <td> <input class="form-control" type="text" name="fecha_recib{{$cont}}" id="fecha_recib{{$cont}}" readonly></td>
                                        <td> <input class="form-control"  type="text" name="num_cheque{{$cont}}" id="num_cheque{{$cont}}" readonly></td>
                                        <td> <input class="form-control" type="text" name="banc_recib{{$cont}}" id="banc_recib{{$cont}}" readonly></td>
                                        <td> <input class="form-control"  type="text" name="cuenta_recib{{$cont}}" id="cuenta_recib{{$cont}}" readonly></td>
                                        <td> <input class="form-control" type="text" name="girad_recib{{$cont}}" id="girad_recib{{$cont}}" readonly></td>
                                        <td> <input class="form-control" type="text" name="importe{{$cont}}" id="importe{{$cont}}" readonly></td>
                                        <td> <input class="form-control" type="text" name="valor{{$cont}}" id="valor{{$cont}}" readonly></td>
                                        <td> <input class="form-control" type="text" name="valor_base{{$cont}}" id="valor_base{{$cont}}" readonly></td>
                                    </tr>
                                    @php $cont = $cont +1; @endphp
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12" style="padding-top: 5px"></div>
                        <div class="col-md-12">
                            <div class="row"> 
                                <div class="col-md-2 col-xs-2">
                                    <label for="total_efectivo" class="control-label" class="control-label">Total Efectivo:</label>
                                    <div class="input-group">
                                        <input id="total_efectivo" name="total_efectivo" type="text" class="form-control" value="0.00" onchange =" redondea_total_efectivo(this,2)">
                                    </div>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <label for="totalpap_deposito" class="control-label" class="control-label">Total Pap.Depósito:</label>
                                    <div class="input-group">
                                        <input id="totalpap_deposito" name="totalpap_deposito" type="text" class="form-control" value="0.00" onchange ="redondea_totalpap_deposito(this,2)">
                                    </div>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <label for="total_cheques" class="control-label" class="control-label">Total Cheques:</label>
                                    <div class="input-group">
                                        <input id="total_cheques" name="total_cheques" type="text" class="form-control" value="0.00" onchange =" redondea_total_cheques(this,2)">
                                    </div>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <label for="total_tarjetas" class="control-label">Total Tarjetas:</label>
                                    <div class="input-group">
                                        <input id="total_tarjetas" name="total_tarjetas" type="text" class="form-control" value="0.00" onchange =" redondea_total_tarjetas(this,2)">
                                    </div>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <label for="total_deposito" class="control-label" class="control-label">Total Depósito:</label>
                                    <div class="input-group">
                                        <input id="total_deposito" name="total_deposito" type="text" class="form-control" value="0.00" onchange =" redondea_total_deposito(this,2)">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-top: 10px"></div>
                        <!--Concepto-->
                        <div class="col-md-12" style="padding-left: 5px">
                            <label for="concepto" class="col-md-12">Concepto: </label>
                            <div class="input-group" style="padding-left: 12px">
                                <textarea class="col-md-12" name="concepto" id="concepto" cols="200" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-top: 5px"></div>
                        <!--Nota-->
                        <div class="col-md-12" style="padding-left: 5px">
                            <label for="nota" class="col-md-12">Nota: </label>
                            <div class="input-group" style="padding-left: 12px">
                                <textarea class="col-md-12" name="nota" id="nota" cols="200" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

<script type="text/javascript">

    $(document).ready(function(){

        //Llamando a la Funcion Numero Deposito
        obtener_num_deposito();
        obtener_fecha();

        $('.select2_cuentas').select2({
            tags: false
        });
    
    });

    /*$('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : false,
      'info'        : false,
      'autoWidth'   : false,
      'order'       : [[ 1, "asc" ]]
    });*/

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

     function obtener_num_deposito(){
        $.ajax({
            url:"{{route('numero_deposito_bancario')}}",
            type: 'get',
            datatype: 'json',
            success: function(data){
               //console.log(data);
               $('#ndeposito').val(data);
            },
            error: function(data){
                console.log(data);
            }
        })
    }

    function crear_depo_bancario(){
        
        $.ajax({
            type: 'post',
            url:"{{route('deposito_bancario_store')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#crear_deposito_bancario").serialize(),
            success: function(data){
               location.href ="{{route('depo_bancario_factventas.index')}}";
            },
            error: function(data){
                   console.log(data);
            }
        })
                    
    }


    $(".factnumero").autocomplete({
        source: function( request, response ) {
            $.ajax( {
            url: "{{route('obtener_num_factura_vent')}}",
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

    //Completa 2 decimales a la izquierda
    function redondea_total_efectivo(elemento,nDec){

        var n = parseFloat(elemento.value);
        var s;

        n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
        s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
        s = s.substr(0, s.indexOf(".") + nDec + 1);

        $('#total_efectivo').val(s);

    }

    //Completa 2 decimales a la izquierda
    function redondea_totalpap_deposito(elemento,nDec){

        var n = parseFloat(elemento.value);
        var s;

        n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
        s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
        s = s.substr(0, s.indexOf(".") + nDec + 1);

        $('#totalpap_deposito').val(s);

    }

    //Completa 2 decimales a la izquierda
    function redondea_total_cheques(elemento,nDec){

        var n = parseFloat(elemento.value);
        var s;

        n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
        s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
        s = s.substr(0, s.indexOf(".") + nDec + 1);

        $('#total_cheques').val(s);

    }

    //Completa 2 decimales a la izquierda
    function redondea_total_tarjetas(elemento,nDec){

        var n = parseFloat(elemento.value);
        var s;

        n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
        s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
        s = s.substr(0, s.indexOf(".") + nDec + 1);

        $('#total_tarjetas').val(s);

    }

    //Completa 2 decimales a la izquierda
    function redondea_total_deposito(elemento,nDec){

        var n = parseFloat(elemento.value);
        var s;

        n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
        s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
        s = s.substr(0, s.indexOf(".") + nDec + 1);

        $('#total_deposito').val(s);

    }


    //Obtenemos Datos de la Factura de Venta Por Numero Ingresado
    function buscar_factura_venta(){

        $.ajax({
            type: 'post',
            url:"{{route('numero_fact_dep_bancario')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'num_factura':$("#numero_factura").val()},
            success: function(data){
                console.log(data);
                $("#id").val(data[0]);
                $("#numero").val(data[1]);
                $("#num_asiento").val(data[2]);
                $("#num_comprobante").val(data[3]); 
                $("#fecha_factura").val(data[4]); 
                //$("#total_deudas").val(data[8]); 

                for(var i=0;i<data[5].length;i++){
                     $("#tipo_dep_recib"+i).val(data[5][i].tipo_pago);
                     $("#fecha_recib"+i).val(data[5][i].fecha_recib);
                     $("#banc_recib"+i).val(data[5][i].banco_recib);
                     $("#cuenta_recib"+i).val(data[5][i].cuenta_recib);
                     $("#girad_recib"+i).val(data[5][i].girad_recib);
                     $("#importe"+i).val(data[5][i].valor_recib);
                     $("#valor"+i).val(data[5][i].valor_recib);
                     $("#valor_base"+i).val(data[5][i].valor_recib);
                }

            },
            error: function(data){
                console.log(data);
            }
        })
    
    }

  

</script>

</section>
@endsection
