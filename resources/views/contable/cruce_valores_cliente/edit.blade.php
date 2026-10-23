@extends('contable.cruce_valores_cliente.base')
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
        .card {
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            padding: 16px;
            text-align: center;
            background-color: white;
        }
        .card2 {
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            padding: 16px;
            text-align: center;
            background-color: #f1f1f1;
        }
        .swal-title {
            margin: 0px;
            font-size: 16px;
            box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.21);
            margin-bottom: 28px;
        }
        .cabecera{
            background-color: #9E9E9E;
            border-radius: 2px;
            color: white;
        }
        .borde{
            border:2px solid #9E9E9E;
        }
        .hde{
            background-color: #888;
            width: 100%;
            height: 25px;
            margin: 0 auto;
            line-height: 25px;
            color: #FFF;
            text-align: center;
        }


</style>

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
      location.href="{{route('cruce_clientes.index')}}";
    }

</script>

<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<div class="modal fade bd-example-modal-lg" id="calendarModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="content">

        </div>
    </div>
</div>
<section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="{{route('clientes.index')}}">Clientes</a></li>
        <li class="breadcrumb-item"><a href="{{route('cruce_clientes.index')}}">Cruce de Valores a Favor</a></li>
        <li class="breadcrumb-item active" aria-current="page">Nuevo Cruce de Valores a Favor</li>
      </ol>
    </nav>
    <form class="form-vertical " id="crear_factura" role="form" method="post">
        {{ csrf_field() }}
            <div class="box box-solid">
                <div class="box-header with-border">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-9 col-sm-9 col-6">
                                <div class="box-title" ><b>VISUALIZADOR CLIENTES- CRUCE DE VALORES A FAVOR</b></div>
                            </div>
                            <div class="col-3">
                                <div class="row">
                                    <a class="btn btn-success btn-gray btn-xs" style="margin-left: 3px;" href="javascript:goBack()" >
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body dobra">
                    
                        <div class="row header">
                            <div class="col-md-12">
                                <div class="row  ">
                                    <div class="col-md-12">
                                        &nbsp;
                                    </div>

                                    <div class="col-md-2 col-xs-2 px-1">
                                        <label class="label_header" style="padding-left: 0px">Estado</label>
                                        <input class="col-md-12 form-control  col-xs-12" style=" @if(($cruce_valores->estado)==1) background-color: green; @else  background-color: red; @endif">
                                    </div>
                                    <div class="col-md-2 col-xs-2 px-1">
                                        <label class="control-label label_header">Id</label>
                                        <input id="idx" type="text" class="form-control  " value="@if(!is_null($cruce_valores)) {{$cruce_valores->id}} @endif" name="idx" readonly>
                                           
                                    </div>
                                    <div class="col-md-2 col-xs-2 px-1">
                                        <label class="control-label label_header">Número</label>
                                        <input class="form-control " type="text" name="numero" id="numero" value="@if(!is_null($cruce_valores)) {{$cruce_valores->secuencia}} @endif" readonly>
                                    </div>
                                    <div class="col-md-2 col-xs-2 px-1">
                                        <label class="control-label label_header">Tipo</label>
                                        <input class="form-control " type="text" name="tipo" id="tipo" readonly value="CLI-CR-AF">
                                    </div>
                                    <div class="col-md-2 col-xs-2 px-1">
                                        <label class="control-label label_header">Fecha</label>
                                        
                                            <input class="form-control  " id="fecha" type="text" value="@if(!is_null($cruce_valores)) {{$cruce_valores->fecha_pago}} @endif" name="fecha" readonly >
                                        
                                    </div>
                                    <div class="col-md-2 col-xs-2 px-1">
                                        <label class="control-label label_header">Asiento</label>
                                        <input class="form-control " type="text" name="asiento" id="asiento" value="@if(!is_null($cruce_valores)) {{$cruce_valores->id_asiento_cabecera}} @endif" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row  ">
                                    <div class="col-md-12 col-xs-4 px-1" >
                                        <label class="control-label label_header">Concepto</label> 
                                        <input id="concepto" type="text" class="form-control   col-md-12" autocomplete="off" value="@if(!is_null($cruce_valores->detalle)) {{$cruce_valores->detalle}} @endif" placeholder="Concepto" name="concepto" readonly>
                                            
                                        
                                    </div>
                                    <div class="col-md-12 col-xs-12 px-0" >
                                        <label class="control-label label_header" >Cliente</label>
                                    </div>
                                    <div class="col-md-6 col-xs-6 px-0" >
                                           
                                            
                                            <input type="text" id = "id_cliente" name="id_cliente" value="@if(!is_null($cruce_valores)) {{$cruce_valores->id_cliente}} @endif" readonly placeholder="Cédula" class= "form-control  form-control-sm id_cliente  col-md-12" readonly  >
                                    </div>
                                    <div class="col-md-6 col-xs-6 px-0" >
                                           
                                           
                                            <input type="text" value="@if(!is_null($cruce_valores->cliente)) {{$cruce_valores->cliente->nombre}} @endif" id = "nombre_cliente" name="nombre_cliente" placeholder="Nombre Cliente" class= "form-control form-control-sm nombre_cliente  col-md-12" readonly  >
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="col-md-12 px-1">
                            <input type="hidden" name="total_suma_a" id="total_suma_a">
                            <input type="hidden" name="saldoax" id="saldoax">
                            <label class="control-label label_header" for="">DETALLE DE VALORES A FAVOR</label>
                        </div>
                          <div class="table-responsive col-md-12 px-1">
                            <input type="hidden" name="contador_a" id="contador_a" value="0">
                            <table id="example3" role="grid" aria-describedby="example2_info">
                                <thead  class='well-dark'>
                                <tr style="position: relative;">
                                    <th style="width: 5%; text-align: center;">Emisión</th>
                                    <th style="width: 5%; text-align: center;">Tipo</th>
                                    <th style="width: 5%; text-align: center;">Número</th>
                                    <th style="width: 22%; text-align: center;">Concepto</th>
                                    <th style="width: 5%; text-align: center;">Div</th>
                                    <th style="width: 5%; text-align: center;">Saldo</th>
                                    <th style="width: 4%; text-align: center;">Abono</th>
                                    <th style="width: 2%; text-align: center;">Nuevo</th>

                                </tr>
                                </thead>
                                <tbody id="crear_a">
                                    @foreach($detalle_pago as $value)
                                        <tr>
                                            <td>{{$value->fecha}}</td>
                                            <td> <b>CLI-IN</b> </td>
                                            <td>{{$value->numero}}</td>
                                            <td>{{$value->concepto}}</td>
                                            <td>$</td>
                                            <td>{{$value->valor_ant}}</td>
                                            <td>{{$value->valor}}</td>
                                            @php $nuevo_saldo= $value->valor_ant-$value->valor; @endphp
                                            <td>{{number_format($nuevo_saldo,2)}}</td>
                                            
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                          </div>
                          <div class="col-md-12 px-1" style="">
                            <div class="row">
                                <div class="col-md-9">
                                </div>
                                <div class="col-md-3">
                                    <label class="label_header col-md-12">Total</label>
                                    <input class="form-control col-md-3" value="@if(!is_null($cruce_valores->total_disponible)) {{$cruce_valores->total_disponible}} @endif" type="text" name="total_anticipos" id="total_anticipos" class="col-md-12" readonly>

                                </div>
                            </div>
                          </div>

                        <input type="text" name="contador" id="contador" value="0" class="hidden">
                        <input type="hidden" name="total_suma" id="total_suma">
                                            
                    <div class="col-md-12 px-1" style="">
                        <label class="label_header" for="detalle_deuda">DETALLE DE DEUDAS DEL CLIENTE</label>
                    </div>
                    
                        <div class="table-responsive col-md-12 px-1" style="min-height: 250px; max-height: 250px; ">               
                            <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                                <thead style="background-color: #9E9E9E; color: white;" >
                                <tr style="position: relative;">
                                    <th style="width: 10%; text-align: center;">Vence</th>
                                    <th style="width: 8%; text-align: center;">Tipo</th>
                                    <th style="width: 10%; text-align: center;">Número</th>
                                    <th style="width: 20%; text-align: center;">Concepto</th>
                                    <th style="width: 2%; text-align: center;">Div</th>
                                    <th style="width: 5%; text-align: center;">Saldo</th>
                                    <th style="width: 5%; text-align: center;">Abono</th>
                                    
                                    
                                </tr>
                                </thead>
                                <tbody id="crear">
                                
                                @php $cont=0; @endphp
                                @foreach ($detalle_cruce as $values)
                                    <tr>
                                        <td>{{$values->fecha}}</td>
                                        <td> <b>VEN-FA</b> </td>
                                        <td> {{$values->secuencia_factura}}</b> </td>
                                        <td> {{$values->observaciones}}</td>
                                        <td> $ </td>
                                        <td> {{$values->total_factura}} </td>
                                        <td> {{$values->total}} </td>
                                      
                                        
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
                                    <div class="input-group">
                                        <input type="hidden" name="retencion_iva" id="retencion_iva" disabled>
                                        <input type="hidden" name="proveedor" id="proveedor"> 
                                        <input type="hidden" name="sobrante" id="sobrante">
                                        <input type="hidden" name="egreso_retenciones" id="egreso_retenciones">
                                    </div>
                                </div>
                            </div>
                        </div>
                     

                        </div>

                </div>
    </form>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/hospital/cleave/dist/cleave.min.js")}}"></script>

<script type="text/javascript">

   

</script>
</section>
@endsection
