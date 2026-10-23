@extends('contable.transferencia_bancaria.base')
@section('action-content')
<script type="text/javascript">
    function check(e) {
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
        //window.location.reload(history.back());
    }
    function goNew() {
        $(".btn_add").attr("disabled", false);
        $("#asiento").val("");
        $("#id").val("");
        $("#numero").val("");
        $(".print").css('visibility', 'hidden');
    }
</script>
<div class="modal fade" id="visualizar_estado" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
    <li class="breadcrumb-item"><a href="#">{{trans('contableM.banco')}}</a></li>
    <li class="breadcrumb-item"><a href="../notacredito">{{trans('contableM.transbancaria')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.Consulta')}}</li>
  </ol>
</nav>
    <div class="box" >
        <div class="box-header header_new">
            <div class="col-md-4">
              <h3 class="box-title">{{trans('contableM.transbancaria')}}</h3>
            </div>
            <div class="col-md-1 print" id="imprimir">
                <a target="_blank" href="{{ route('transferenciabancaria.imprimir', ['id' => $registro->id ]) }}" class="btn btn-info btn-gray">
                    <i class="glyphicon glyphicon-print"></i><!--&nbsp;&nbsp; Revisar Nota-->
                </a>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="goNew()" class="btn btn-primary btn-gray" >
                {{trans('contableM.nuevo')}}
                </button>
            </div>
            <div class="col-md-6 text-right" >
            <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',[$registro->id_asiento])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
        <i class="glyphicon glyphicon-eye-open"  aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.visualizarasiento')}}
</a>
<a class="btn btn-success btn-gray " style="margin-left: 3px;" href="{{route('librodiario.edit',['id'=>$registro->id_asiento])}}" target="_blank">
    <i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.EditarAsientodiaro')}}
</a>
                <button onclick="goBack()" class="btn btn-success btn-gray">
                   <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                </button>
            </div>
        </div>

        <form id="form"  class="form-vertical" role="form">
        <div class="box-body dobra">

                {{ csrf_field() }}
                   
                <!--header row-->
                <div class="header row">
                    <div class="form-group col-xs-1 px-1">
                        <div class="col-md-12 px-0">
                            <label for="estado" class="label_header">{{trans('contableM.estado')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                        <input id="estado" type="text" class="form-control"  name="estado" value="{{ $registro->estado == '1' ? 'Activa' : 'Inactiva' }}" readonly autofocus >
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id" class=" label_header">{{trans('contableM.id')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                        <input type="text" class="form-control"  name="id" id="id" value="{{ $registro->id }}" readonly autofocus>
                                @if ($errors->has('id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id') }}</strong>
                                    </span>
                                @endif
                            </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="numero" class=" label_header">{{trans('contableM.numero')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                                <input type="text" class="form-control"  name="numero" id="numero" value="{{ $registro->numero }}" readonly autofocus>
                                @if ($errors->has('numero'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('numero') }}</strong>
                                    </span>
                                @endif
                            </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="asiento" class="label_header">{{trans('contableM.asiento')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control"  name="asiento" id="asiento" value="{{ $registro->id_asiento }}" readonly autofocus>
                            @if ($errors->has('asiento'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('asiento') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="tipo" class="label_header">{{trans('contableM.tipo')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                                <input type="text" class="form-control" name="tipo" id="tipo" value="{{ $registro->tipo }}" readonly autofocus>
                            @if ($errors->has('tipo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('tipo') }}</strong>
                                </span>
                            @endif
                            </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="fecha_asiento" class="label_header">{{trans('contableM.fecha')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="fecha_asiento" type="text" class="form-control"  name="fecha_asiento" value="{{ $registro->fecha_asiento }}"   readonly >
                            @if ($errors->has('fecha_asiento'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('fecha_asiento') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-1 px-1">
                        <div class="col-md-12 px-0">
                            <label for="asiento" class="label_header">{{trans('contableM.proyecto')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                        <input type="text" class="form-control" value="0000"  name="proyecto" id="proyecto" value="{{ $registro->proyecto }}" readonly autofocus>
                            @if ($errors->has('proyecto'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('proyecto') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!-- 2 row -->
                    <div class="form-group col-xs-12 px-1">
                        <div class="col-md-12 px-0">
                            <label for="concepto" class="label_header">{{trans('contableM.concepto')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                        <input id="concepto" type="text" class="form-control"  name="concepto" value="{{ $registro->concepto }}" maxlength="45" readonly autofocus >
                            @if ($errors->has('concepto'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('concepto') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!-- 3 row -->
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="numcheque" class="label_header">{{trans('contableM.numcheque')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                        <input id="numcheque" type="text" class="form-control"  name="numcheque" value="{{ $registro->numcheque }}" readonly autofocus>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="fecha_cheque" class="label_header">{{trans('contableM.fechacheque')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="fecha_cheque" type="text" class="form-control"  name="fecha_cheque" value="{{ $registro->fecha_cheque }}" readonly autofocus>
                            @if ($errors->has('fecha_cheque'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('fecha_cheque') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6 px-1">
                        <div class="col-md-12 px-0">
                            <label for="beneficiario" class="label_header">{{trans('contableM.beneficiario')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                        <input id="observacion" type="text" class="form-control"  name="beneficiario" value="{{ $registro->beneficiario }}" maxlength="50" readonly autofocus >
                            @if ($errors->has('beneficiario'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('beneficiario') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="no_increm_cheque" class="label_header">{{trans('contableM.noincrecheque')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                                <input type="text" class="form-control" name="no_increm_cheque" id="no_increm_cheque" value="BAN-NC" readonly>
                            @if ($errors->has('tipo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('tipo') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!-- 4 row -->
                    <div class="form-group col-xs-6 px-1">
                        <div class="col-md-12 px-0">
                            <label for="direccion" class="label_header">{{trans('agenda.direccion')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                        <input id="direccion" type="text" class="form-control"  name="direccion" value="{{ $registro->direccion }}" maxlength="150" readonly autofocus >
                        </div>
                    </div>
                    <div class="form-group col-xs-6 px-1">
                        <div class="col-md-12 px-0">
                            <label for="ruc" class="label_header">R.U.C / {{trans('contableM.cedula')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                        <input id="ruc" type="text" class="form-control"  name="ruc" value="{{ $registro->ruc }}" maxlength="13" readonly autofocus >
                            @if ($errors->has('ruc'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('ruc') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!-- 5 row -->
                    <div class="form-group col-xs-6 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id_cuenta_origen" class="label_header">{{trans('contableM.CuentaOrigen')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select class="form-control" name="id_cuenta_origen" id="id_cuenta_origen" disabled>
                                <option value="">{{trans('agenda.seleccione')}}...</option>
                                @foreach($bancos as $value)
                                    <option value="{{$value->cuenta_mayor}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id_divisa" class="label_header">{{trans('contableM.divisas')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select class="form-control" name="id_divisa_origen" id="id_divisa_origen" disabled>
                                @foreach($divisas as $value)
                                    <option value="{{$value->id}}">{{$value->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id_cambio_origen" class="label_header">{{trans('contableM.cambio')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="id_cambio_origen" type="number" class="form-control"  value="1.00" name="id_cambio_origen" readonly autofocus >
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="valor_origen" class="label_header">{{trans('contableM.valor')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                        <input id="valor_origen" type="text" class="form-control" onchange="calcular()" value="{{ $registro->valor_origen }}" name="valor_origen" readonly autofocus >
                        </div>
                    </div>
                    <!-- 5 row -->
                    <div class="form-group col-xs-6 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id_cuenta_destino" class="label_header">{{trans('contableM.CuentaDestino')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select class="form-control" name="id_cuenta_destino" id="id_cuenta_destino" disabled>
                                <option value="">{{trans('agenda.seleccione')}}...</option>
                                @foreach($cuentas as $value)
                                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id_divisa_destino" class="label_header">{{trans('contableM.divisas')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select class="form-control" name="id_divisa_destino" id="id_divisa_destino" disabled>
                                @foreach($divisas as $value)
                                    <option value="{{$value->id}}">{{$value->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id_cambio_destino" class="label_header">{{trans('contableM.cambio')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="id_cambio_destino" type="number" class="form-control"  value="{{ $registro->id_cambio_destino }}" name="id_cambio_destino" readonly autofocus >
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="valor_destino" class="label_header">{{trans('contableM.valor')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="valor_destino" type="number" class="form-control"  value="{{ $registro->valor_destino }}" name="valor_destino" readonly autofocus >
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body dobra">
                <!--header row-->
                <div class="header row">
                    <!-- 1 row -->
                    <div class="form-group col-xs-6 px-1">
                        <div class="col-md-12 px-0">
                            <label for="glosa" class="label_header">Glosa</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <textarea id="glosa" rows="16" class="form-control" name="glosa" maxlength="250" disabled autofocus>{{$registro->glosa}}</textarea>
                            @if ($errors->has('glosa'))
                            <span class="help-block">
                                <strong>{{ $errors->first('glosa') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                @php 
                    $proveedor_nombre = null;
                    if(!is_null($registro->id_proveedor)){  
                        $proveedor_nombre = Sis_medico\Proveedor::where('id', $registro->id_proveedor)->first();  
                    }
                    @endphp
                    <div class="form-group col-xs-6 px-1">
                        <div class="col-md-12 px-0">
                            <label for="proveedor" class="label_header">Proveedor</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select class="form-control select2" name="id_proveedor" id="id_proveedor" style="width: 100%;" disabled>
                                <?php
                                if ($proveedor_nombre != '') {
                                ?>
                                    <option value="@if(!is_null($proveedor_nombre)) {{$proveedor_nombre->id}}  @endif">@if(!is_null($proveedor_nombre)) {{$proveedor_nombre->razonsocial}} @endif</option>
                                    <?php
                                } else {
                                    echo '<option value="">--Seleccionar--</option>';
                                    foreach ($proveedores as $x) {
                                    ?>
                                        <option value="{{ $x->id }}">{{ $x->razonsocial }}</option>
                                <?php
                                    }
                                }
                                ?>

                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-6 px-1">
                        <div class="col-md-12 px-0">
                            <label for="autorizacion" class="label_header"># Autorizacion</label>
                        </div>
                        <div class="col-md-11 px-0">
                            <input id="autorizacion" type="text" class="form-control" maxlength="49" name="autorizacion" value="{{$registro->autorizacion}}" disabled>
                        </div>
                        <?php
                        if ($registro->autorizacion == '') {
                        ?>
                            <div class="col-md-1 px-1">
                                <a href="javascript:;" id="btnActualizar" class="btn btn-info" onclick="guardarRetencion('<?= $registro->id; ?>')"><i id="iconActualizar" class="glyphicon glyphicon-edit" aria-hidden="true"></i></a>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                    <!-- 2 row -->
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="comision" class="label_header">Comisi&oacute;n</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select class="form-control" name="id_comision" id="id_comision" disabled>
                                <option value="">Seleccione...</option>
                                @foreach($bancos as $value)
                                <option value="{{$value->id}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="valor_comision" class="label_header">&nbsp;</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="valor_comision" type="number" class="form-control" value="{{$registro->valor_comision}}" name="valor_comision" disabled autofocus>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                        <label for="comision_tarjeta" class="label_header">{{trans('contableM.comisidetarjetac')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="comision_tarjeta" type="number" class="form-control" onchange="calcular()" value="{{$registro->comision_tarjeta}}" name="comision_tarjeta" disabled autofocus>
                        </div>
                    </div>
                    <!-- 3 row -->
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                        <label for="baseiva" class="label_header">{{trans('contableM.baseiva')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="baseiva" type="number" class="form-control" onchange="calcular()" value="{{$registro->baseiva}}" name="baseiva" disabled autofocus>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id_base_iva" class="label_header">&nbsp;</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select class="form-control" name="id_base_iva" id="id_base_iva" onchange="calcular()" disabled>
                            <option value="">{{trans('agenda.seleccione')}}...</option>
                                @foreach($retencionesR as $value)
                                <option value="{{$value->valor}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                        <label for="iva_retenido" class="label_header">{{trans('contableM.ivarete')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="iva_retenido" type="number" class="form-control" value="{{$registro->iva_retenido}}" name="iva_retenido" disabled autofocus>
                        </div>
                    </div>
                    <!-- 4 row -->
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                        <label for="base_ret_fuente" class="label_header">{{trans('contableM.bretenfuente')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="base_ret_fuente" type="number" class="form-control" value="{{$registro->base_ret_fuente}}" onchange="calcular();cambiar_estado();"  name="base_ret_fuente" disabled autofocus>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id_base_ret_fuente" class="label_header">&nbsp;</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select class="form-control" name="id_base_ret_fuente" id="id_base_ret_fuente" onchange="calcular();" disabled>
                            <option value="">{{trans('agenda.seleccione')}}...</option>
                                @foreach($retenciones as $value)
                                <option @if($registro->id_base_ret_fuente == $value->cuenta_deudora) selected @endif value="{{$value->valor}}"> {{$value->valor}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                        <label for="retencion_fuente" class="label_header">{{trans('contableM.retenenlafuente')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="retencion_fuente" type="number" class="form-control" value="{{$registro->retencion_fuente}}" name="retencion_fuente" autofocus disabled>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="" class="label_header">&nbsp;</label>
                        </div>
                        <div class="col-md-12 px-0">
                        <label for="val_adicionales">{{trans('contableM.valadicionales')}}</label>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="valores_adicionales" class="label_header">&nbsp;</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="valores_adicionales" type="number" class="form-control" onchange="calcular();" value="{{$registro->valores_adicionales}}" name="valores_adicionales" autofocus disabled>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                        <label for="total" class="label_header">{{trans('contableM.total')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="total" type="number" class="form-control" value="{{$registro->total}}" name="total" autofocus disabled>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group col-xs-10 text-center">
                <div class="col-md-6 col-md-offset-4">
                    <button type="button" class="btn btn-default btn-gray btn_add" disabled>
                <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.guardar')}}
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script src="{{ asset ('/plugins/datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>
<script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script type="text/javascript">
    var fila = $("#mifila").html();
    // $(".print").css('visibility', 'hidden');
    $(document).ready(function() {
        $('.select2_cuentas').select2({
            tags: false
        });
    });
    function guardarRetencion(id) {
        var clase = $('#btnActualizar').hasClass('btn-info');
        if (clase == true) {
            $('#btnActualizar').removeClass('btn-info')
                .addClass('btn-danger');
            $('#iconActualizar').removeClass('glyphicon-edit')
                .addClass('glyphicon-floppy-disk');
            $('#id_proveedor').prop('disabled', false);
            $('#autorizacion').prop('disabled', false);
            $('#id_proveedor').select2();
            $('#id_comision').prop('disabled', false);
            $('#id_comision').select2();
            $('#valor_comision').prop('disabled', false);
            $('#comision_tarjeta').prop('disabled', false);
            $('#baseiva').prop('disabled', false);
            $('#id_base_iva').prop('disabled', false);
            $('#id_base_iva').select2();
            $('#base_ret_fuente').prop('disabled', false);
            $('#id_base_ret_fuente').prop('disabled', false);
            $('#id_base_ret_fuente').select2();
            $('#valores_adicionales').prop('disabled', false);
        } else {
            if ($('#id_proveedor').val() == '') {
                Swal.fire({
                    title: 'Error',
                    icon: 'error',
                    html: 'Seleccione un proveedor...',
                });
                return;
            } else if ($('#autorizacion').val() == '') {
                Swal.fire({
                    title: 'Error',
                    icon: 'error',
                    html: 'Ingrese el número de autorización...',
                });
                return;
            }else if ($('#autorizacion').val().length != 49) {
                Swal.fire({
                    title: 'Error',
                    icon: 'error',
                    html: 'El número de autorización debe contenet 49 dígitos...',
                });
                return;
            } else {
                if ($('#base_ret_fuente').val() == '' ) {
                    Swal.fire({
                        title: 'Error',
                        icon: 'error',
                        html: 'Ingrese la Base Retencion Fuente...',
                    });
                    return;
                } else if ($('#id_base_ret_fuente').val() == '') {
                    Swal.fire({
                        title: 'Error',
                        icon: 'error',
                        html: 'Seleccione el porcentaje de retencion a la fuente...',
                    });
                    return;
                }
                $('#retencion_fuente').prop('disabled', false);
                $('#iva_retenido').prop('disabled', false);
                $.ajax({
                    url: '<?= route('transferenciabancaria.actualizarRetencionBancaria'); ?>',
                    type: 'post',
                    data: {
                        _token: $('input[name=_token]').val(),
                        id: id,
                        proveedor: $('#id_proveedor').val(),
                        autorizacion: $('#autorizacion').val(),
                        id_comision: $('#id_comision').val(),
                        valor_comision: $('#valor_comision').val(),
                        comision_tarjeta: $('#comision_tarjeta').val(),
                        baseiva: $('#baseiva').val(),
                        id_base_iva: $('#id_base_iva').val(),
                        iva_retenido: $('#iva_retenido').val(),
                        base_ret_fuente: $('#base_ret_fuente').val(),
                        id_base_ret_fuente: $('#id_base_ret_fuente').val(),
                        valores_adicionales: $('#valores_adicionales').val(),
                    },
                    dataType: 'json',
                    success: function(json) {
                        $('#btnActualizar').removeClass('btn-danger')
                            .addClass('btn-info');
                        $('#iconActualizar').removeClass('glyphicon-floppy-disk')
                            .addClass('glyphicon-edit');
                        document.getElementById("btnActualizar").style.display = "none";
                        $('#id_proveedor').prop('disabled', true);
                        $('#autorizacion').prop('disabled', true);
                        $('#id_comision').prop('disabled', true);
                        $('#valor_comision').prop('disabled', true);
                        $('#comision_tarjeta').prop('disabled', true);
                        $('#baseiva').prop('disabled', true);
                        $('#id_base_iva').prop('disabled', true);
                        $('#base_ret_fuente').prop('disabled', true);
                        $('#id_base_ret_fuente').prop('disabled', true);
                        $('#valores_adicionales').prop('disabled', true);

                        Swal.fire({
                            title: `{{trans('contableM.exito')}}`,
                            icon: 'success',
                            html: `{{trans('contableM.actcorrectamente')}}`,
                        });
                    }
                });
            }
            $('#retencion_fuente').prop('disabled', true);
            $('#iva_retenido').prop('disabled', true);
        }
    }
    $(function() {
        $('#id_cuenta_origen').val('{{ $registro->id_cuenta_origen }}');
        $('#id_cuenta_destino').val('{{ $registro->id_cuenta_destino }}');
    });
    function buscar_prod() {
        $.ajax({
            type: 'post',
            url: "{{route('contable_find_insumo')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            type: 'POST',
            datatype: 'json',
            data: $("#codigo_prod"),
            success: function(data) {
                console.log(data);
                if (data != 'no') {
                    //console.log("siii");
                    $('#codigo').val(data[0].codigo);
                    $('#nombre').val(data[0].nombre);
                    $('#descripcion').val(data[0].descripcion);
                    $('#stock_minimo').val(data[0].minimo);
                }
            },
            error: function(data) {
                console.log(data);
            }
        });
    };
    $('body').on('click', '.delete', function() {
        var borrar = $(this).parent().prev().children().closest('.haber').val();
        var total = $("#valor").val();
        var dc = $("#haber_contable").val();
        total = parseFloat(total) - parseFloat(borrar);
        total = parseFloat(total).toFixed(2)
        $("#valor").val(total);
        dc = parseFloat(dc) - parseFloat(borrar);
        dc = parseFloat(dc).toFixed(2)
        $("#hc").val(total);
        $(this).parent().parent().remove();
    });
    $('body').on('change', '.select2_cuentas', function() {
        var eas = $(this).val();
        console.log($(this).data("name"));
        var selectedText = $(".select2_cuentas option:selected").data("name");
        console.log(selectedText);
        var sdf = $(this).find('option:selected').data("name");
        console.log(sdf);
        console.log(eas);
        // $(".select2_cuentas").parent().parent().first().children().children().closest(".no_no").val($(".select2_cuentas option:selected").data("name"));
        $(this).parent().parent().first().children().children().closest(".no_no").val(sdf);
    });
    function nuevo() {
        var nuevafila = $("#mifila").html();
        var rowk = document.getElementById("agregar_cuentas").insertRow(-1);
        rowk.innerHTML = fila;
        rowk.className = "well";
        //  console.log(rowk);
        $('.select2_cuentas').select2({
            tags: false
        });
    }
    function agregar(elemento) {
        var anterior = $('#agregar_cuentas').html();
        var contador = $('#contador').val();
        contador = parseInt(contador) + 1;
        $.ajax({
            type: 'post',
            url: "{{route('notacredito.buscar_asiento')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            type: 'POST',
            datatype: 'json',
            data: {
                'nombre': $('#' + elemento).val(),
                'contador': contador
            },
            success: function(data) {
                console.log(data);
                $('#contador').val(contador);
                document.getElementById("agregar_cuentas").insertRow(contador - 1).innerHTML = data;
            },
            error: function(data) {
                console.log(data);
            }
        });
    }
    /*
        document.getElementById("enviar_nota").addEventListener("submit", function(event){
            var contador =  $('#contador').val();
            var tdebe= 0;
            var thaber=0;
            if(contador <= 0){
                alert('No se puede agregar el asiento, no tiene ninguna cuenta');
                event.preventDefault();
                return false;
            }
            for (var i = 1; contador >= i; i++) {
                debe = parseFloat($('#debe'+i).val());
                haber = parseFloat($('#haber'+i).val());
                tdebe = tdebe + debe;
                thaber = thaber + haber;
            }
            $('#total').val(thaber-tdebe);
        });*/
    $(".btn_add").click(function() {
        // var haberco = parseFloat($("#haber_contable").html());
        // var haber = parseFloat($("#valor").val());
        // if(haberco==haber){
        if ($("#form").valid()) {
            $(".print").css('visibility', 'visible');
            $(".btn_add").attr("disabled", true);
            $.ajax({
                url: "{{route('transferenciabancaria.store')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                type: 'POST',
                datatype: 'json',
                data: $("#form").serialize(),
                success: function(data) {
                    console.log(data);
                    $("#asiento").val(data.id_asiento);
                    $("#id").val(data.id);
                    $("#numero").val(data.numero);
                    // swal("Listo!", "Nota de Debito creada con exito!", "error");
                        // swal(`{{trans('contableM.correcto')}}!`,"Por favor ingrese correctamente los valores..","error");
                    // var url = '{{ route("notacredito.imprimir", ":id") }}';
                    // url = url.replace(':id', data.iddebito);
                    // console.log('url',url);
                    // $('#imprimir').html('<a href="'+url+'" target="_blank" class="btn btn-info btn-gray"><i class="glyphicon glyphicon-print" aria-hidden="true"></i></a>');
                },
                error: function(data) {
                    console.error(data.responseText);
                }
            });
        }
        // }else{
        //     alert("Hay un error en las cuentas");
        // }
    });
    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;
        return true;
    }
    function sumacontable() {
        var total = 0;
        $(".haber").each(function() {
            console.log("sc", $(this).parent().prev().children().closest(".select2_cuentas").val());
            var cuenta = $(this).parent().prev().children().closest(".select2_cuentas").val();
            if ($(this).val().length > 0 && cuenta != "") {
                total = parseFloat(total) + parseFloat($(this).val());
            }
        });
        total = parseFloat(total).toFixed(2)
        $("#haber_contable").html(total);
    }
    function addvalue() {
        sumacontable();
        var total = 0;
        $(".haber").each(function() {
            if ($(this).val().length > 0) {
                total = parseFloat(total) + parseFloat($(this).val());
            }
        });
        total = parseFloat(total).toFixed(2)
        $("#valor").val(total);
    }

    function calcular() {
        var valor_origen = $("#valor_origen").val();
        var valor_destino = $("#valor_destino").val();
        var comision_tarjeta = $("#comision_tarjeta").val()==''?0:$("#comision_tarjeta").val();
        var iva_retenido = $("#iva_retenido").val()==''?null:$("#iva_retenido").val();
        var retencion_fuente = $("#retencion_fuente").val()==''?null:$("#retencion_fuente").val();
        var valores_adicionales = $("#valores_adicionales").val()==''?0:$("#valores_adicionales").val();
        var valores_destino = 0;
        var total = 0;
        var subtotal = 0;
        var base_ret_fuente = $("#base_ret_fuente").val()==''?null:$("#base_ret_fuente").val();
        var base_iva = $("#baseiva").val()==''?null:$("#baseiva").val();
        var id_base_ret_fuente = $("#id_base_ret_fuente").val()==''?null:$("#id_base_ret_fuente").val();
        var id_base_iva = $("#id_base_iva").val()==''?null:$("#id_base_iva").val();
        var retencion_iva = 0;
        var retencion_fuente = 0;
        
        if (base_ret_fuente != null && id_base_ret_fuente != null) {
            retencion_fuente = (parseFloat(base_ret_fuente) * parseFloat(id_base_ret_fuente)) / 100;
            retencion_fuente = redondea_precio(retencion_fuente, 2); //alert(retencion_fuente);
            $("#retencion_fuente").val(retencion_fuente);
        }
        else{
            retencion_fuente = redondea_precio(0, 2); //alert(retencion_fuente);
            $("#retencion_fuente").val(retencion_fuente);
        }

        if (id_base_iva != null && base_iva != null) {
            retencion_iva = (parseFloat(base_iva) * parseFloat(id_base_iva)) / 100;
            retencion_iva = redondea_precio(retencion_iva, 2);
            $("#iva_retenido").val(retencion_iva);
        }
        else{
            retencion_iva = redondea_precio(0, 2);
            $("#iva_retenido").val(retencion_iva);
        }
        
        subtotal = redondea_precio(parseFloat(comision_tarjeta) + parseFloat(retencion_fuente) + parseFloat(retencion_iva) + parseFloat(valores_adicionales), 2);
        total = redondea_precio(parseFloat(valor_origen) - parseFloat(subtotal), 2);
        
        if (total == 'NaN') {
            total = 0;
        }
        // alert((retencion_fuente));
        $("#valor_destino").val(redondea_precio(total, 2));
        valores_destino = parseFloat(subtotal) + parseFloat(total);
        valores_destino = redondea_precio(valores_destino, 2);
        $("#total").val(redondea_precio(valores_destino, 2));
    }
    function redondea_precio(value, decimales = 3) {
        value = +value;
        if (isNaN(value)) return NaN; // Shift 
        value = value.toString().split('e');
        value = Math.round(+(value[0] + 'e' + (value[1] ? (+value[1] + 2) : 2))); // Shift back 
        value = value.toString().split('e');
        return (+(value[0] + 'e' + (value[1] ? (+value[1] - 2) : -2))).toFixed(2);
    }
    $('#id_base_iva').change(function() {
        if ($('#baseiva').val() == '') {
            Swal.fire({
                title: 'Alerta',
                text: "Ingrese el valor de la base de iva para continuar",
                icon: 'warning',
                confirmButtonColor: '#3085d6'
            })
            return;
        } else {
            if ($('#baseiva').val() > 0) {
                var porIva = $('#id_base_iva').val().split('|');
                var valIva = $('#baseiva').val() * (porIva[1] / 100);
                $('#iva_retenido').prop('disabled', false);
                $('#iva_retenido').val(valIva);
                $('#iva_retenido').prop('disabled', true);
            }
        }
    });
    $('#id_base_ret_fuente').change(function() {
        if ($('#base_ret_fuente').val() == '') {
            Swal.fire({
                title: 'Alerta',
                text: "Ingrese el valor de la base de iva para continuar",
                icon: 'warning',
                confirmButtonColor: '#3085d6'
            })
            return;
        } else {
            if ($('#base_ret_fuente').val() > 0) {
                var porIva = $('#id_base_ret_fuente').val().split('|');
                var valIva = $('#base_ret_fuente').val() * (porIva[1] / 100);
                $('#retencion_fuente').prop('readonly', false);
                $('#retencion_fuente').val(valIva);
                $('#retencion_fuente').prop('readonly', true);
            }
        }
    });
    function cambiar_estado() {
        datos = parseFloat($('#base_ret_fuente').val());
        if (datos > 0) {
            $("#id_base_ret_fuente").prop('required', true);
        } else {
            $("#id_base_ret_fuente").prop('required', false);
        }
    }
</script>
@endsection