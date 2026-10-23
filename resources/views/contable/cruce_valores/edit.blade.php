@extends('contable.cruce_valores.base')
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
      location.href="{{route('cruce.index')}}";
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
<div class="modal fade" id="visualizar_estado" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>  
<section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="#">Acreedores</a></li>
        <li class="breadcrumb-item"><a href="../cruce_valores">Cruce de Valores a Favor</a></li>
        <li class="breadcrumb-item active" aria-current="page">Nuevo Cruce de Valores a Favor</li>
      </ol>
    </nav>
    <form class="form-vertical " id="crear_factura" role="form" method="post">
        {{ csrf_field() }}
            <div class="box box-solid">
                <div class="box-header with-border">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6 col-sm-7 col-4">
                                <div class="box-title" ><b>CRUCE DE VALORES A FAVOR</b></div>
                            </div>
                            <div class="col-4">
                                <div class="row">
                                    <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',[$cruce_valores->id_asiento_cabecera])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
                                       <i class="glyphicon glyphicon-eye-open"  aria-hidden="true"></i>&nbsp;&nbsp;Visualizar Asiento diaro
                                    </a>
                                    <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="{{route('librodiario.edit',['id'=>$cruce_valores->id_asiento_cabecera])}}" target="_blank">
                                          <i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;&nbsp;Editar Asiento diaro
                                    </a>
                                    
                                    <a class="btn btn-success btn-gray btn-xs" style="margin-left: 3px;padding: 7px 23px;" href="javascript:goBack()" >
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
                                        <input class="col-md-12 form-control input-sm col-xs-12" style="@if($cruce_valores->estado==1)background-color: green;@else background-color: red; @endif">
                                    </div>
                                    <div class="col-md-2 col-xs-2 px-1">
                                        <label class="control-label label_header">Id</label>
                                        <input id="id_proveedor" type="text" class="form-control  input-sm" name="id_proveedor" value="@if(!is_null($cruce_valores)) {{$cruce_valores->id}} @endif" onchange="cambiar_proveedor()" readonly>
                                           
                                    </div>
                                    <div class="col-md-2 col-xs-2 px-1">
                                        <label class="control-label label_header">Número</label>
                                        <input class="form-control input-sm" type="text" value="@if(!is_null($cruce_valores)) {{$cruce_valores->secuencia}} @endif"  name="numero" id="numero" readonly>
                                    </div>
                                    <div class="col-md-2 col-xs-2 px-1">
                                        <label class="control-label label_header">Tipo</label>
                                        <input class="form-control input-sm" type="text" name="tipo" id="tipo" readonly value="ACR-CR-AF">
                                    </div>
                                    <div class="col-md-2 col-xs-2 px-1">
                                        <label class="control-label label_header">Fecha</label>
                                        
                                            <input class="form-control input-sm " id="fecha" type="text" name="fecha" value="@if(!is_null($cruce_valores)) {{$cruce_valores->fecha_pago}} @endif" >
                                        
                                    </div>
                                    <div class="col-md-2 col-xs-2 px-1">
                                        <label class="control-label label_header">Asiento</label>
                                        <input class="form-control input-sm" type="text" name="asiento" id="asiento" value="@if(!is_null($cruce_valores)) {{$cruce_valores->id_asiento_cabecera}} @endif"  readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row  ">
                                    <div class="col-md-8 col-xs-4 px-1" >
                                        <label class="control-label label_header">Concepto</label>
                                        <input id="concepto" type="text" class="form-control  input-sm col-md-12" value="@if(!is_null($cruce_valores)) {{$cruce_valores->detalle}} @endif"  name="concepto" readonly>
                                            
                                        
                                    </div>
                                    <div class="col-md-4 col-xs-4 px-1" >
                                            <label class="col-md-12 label_header" for="acreedor">Acreedor: </label>
                                            <input type="text" id = "nombre_proveedor" value="@if(!is_null($cruce_valores->proveedor)) {{$cruce_valores->proveedor->nombrecomercial}} @endif"  name="nombre_proveedor" readonly class= "form-control form-control-sm nombre_proveedor input-sm col-md-12" >
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
                                            <td> <b>ACR-EG</b> </td>
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
                          <div class="col-md-12 px-1">
                            <div class="row">
                                <div class="col-md-9">
                                </div>
                                <div class="col-md-3">
                                    <label class="label_header col-md-12">Total</label>
                                    <input class="form-control col-md-3" type="text" name="total_anticipos" value="@if(!is_null($cruce_valores)) {{$cruce_valores->total_disponible}} @endif"  id="total_anticipos" class="col-md-12" readonly>

                                </div>
                            </div>
                          </div>

                        <input type="text" name="contador" id="contador" value="0" class="hidden">
                        <input type="hidden" name="total_suma" id="total_suma">
                                           
                    <div class="col-md-12 px-1">
                        <label class="label_header" for="detalle_deuda">DETALLE DE DEUDAS DEL PROVEEDOR</label>
                    </div>
                        <div class="table-responsive col-md-12 px-1 " style="min-height: 250px; max-height: 250px;">               
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
                                        <td> <b>{{$values->tipo}}</b></td>
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
                        </div>

                     

                </div>
    </form>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>

<script type="text/javascript">
</script>
</section>
@endsection
