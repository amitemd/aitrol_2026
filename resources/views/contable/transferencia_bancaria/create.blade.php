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
        å
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
<style>
    .text_der {
        text-align: right;
    }
</style>

<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css') }}">
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{ trans('contableM.contable') }}</a></li>
            <li class="breadcrumb-item"><a href="#">{{ trans('contableM.banco') }}</a></li>
            <li class="breadcrumb-item"><a href="../notacredito">{{ trans('contableM.transbancaria') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ trans('contableM.crear') }}</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-7">
                <h3 class="box-title">{{ trans('contableM.creartransfbancar') }}</h3>
            </div>
            <div class="col-md-1 print" id="imprimir">
                <a target="_blank" href="{{ route('notadebito.imprimir', ['id' => 3]) }}" class="btn btn-info btn-gray">
                    <i class="glyphicon glyphicon-print" aria-hidden="true"></i>
                    <!--&nbsp;&nbsp; Revisar Nota-->
                </a>
            </div>
            <div class="col-md-2 text-right">
                <a class="btn btn-success" data-remote="{{ route('transferenciabancaria.guardar_autorizacion') }}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#retenciones"><span class=" glyphicon glyphicon-floppy-disk"> Retenciones </span> </a>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="location.href='<?= route('transferenciabancaria.create'); ?>'" class="btn btn-primary btn-gray">
                    {{ trans('contableM.nuevo') }}
                </button>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="goBack()" class="btn btn-success btn-gray">
                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{ trans('contableM.regresar') }}
                </button>
            </div>
        </div>
        <form id="form" class="form-vertical" role="form">
            <div class="modal fade" id="retenciones" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                    </div>
                </div>
            </div>
            <div class="box-body dobra">
                {{ csrf_field() }}
                {{-- <input type="hidden" id="minDate" name="minDate" value="{{ isset($min_date) ? date('Y-m-d', strtotime($min_date.'+1 day')) : '' }}"> --}}
                <!--header row-->
                <div class="col-md-1">
                </div>
                <div class="header row col-md-11">
                    <div class="form-group col-xs-1 px-1">
                        <div class="col-md-12 px-0">
                            <label for="estado" class="label_header">{{ trans('contableM.estado') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="estado" type="text" class="form-control" name="estado" value="Activa" readonly autofocus>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id" class=" label_header">{{ trans('contableM.id') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" name="id" id="id" value="" readonly autofocus>
                            @if ($errors->has('id'))
                            <span class="help-block">
                                <strong>{{ $errors->first('id') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="numero" class=" label_header">{{ trans('contableM.numero') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" name="numero" id="numero" value="" readonly autofocus>
                            @if ($errors->has('numero'))
                            <span class="help-block">
                                <strong>{{ $errors->first('numero') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="asiento" class="label_header">{{ trans('contableM.asiento') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" name="asiento" id="asiento" value="" readonly autofocus>
                            @if ($errors->has('asiento'))
                            <span class="help-block">
                                <strong>{{ $errors->first('asiento') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="tipo" class="label_header">{{ trans('contableM.tipo') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" name="tipo" id="tipo" value="BAN-TR" readonly autofocus>
                            @if ($errors->has('tipo'))
                            <span class="help-block">
                                <strong>{{ $errors->first('tipo') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="fecha_asiento" class="label_header">{{ trans('contableM.fecha') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="fecha_asiento" type="date" class="form-control" name="fecha_asiento" onchange="validarCierre();" min="{{ isset($min_date) ? date('Y-m-d', strtotime($min_date.'+1 day')) : '' }}" value="{{ date('Y-m-d') }}" required>
                            @if ($errors->has('fecha_asiento'))
                            <span class="help-block">
                                <strong>{{ $errors->first('fecha_asiento') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-1 px-1">
                        <div class="col-md-12 px-0">
                            <label for="asiento" class="label_header">{{ trans('contableM.proyecto') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" value="0000" name="proyecto" id="proyecto" value="" readonly autofocus>
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
                            <label for="concepto" class="label_header">{{ trans('contableM.concepto') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="concepto" type="text" class="form-control" name="concepto" value="{{ old('concepto') }}" required autofocus>
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
                            <label for="numcheque" class="label_header">{{ trans('contableM.numcheque') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="numcheque" type="text" class="form-control" name="numcheque" value="">
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="fecha_cheque" class="label_header">{{ trans('contableM.fechacheque') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="fecha_cheque" type="date" class="form-control" name="fecha_cheque" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="form-group col-xs-6 px-1">
                        <div class="col-md-12 px-0">
                            <label for="beneficiario" class="label_header">{{ trans('contableM.beneficiario') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="observacion" type="text" class="form-control" name="beneficiario" value="{{ old('beneficiario') }}" maxlength="50" autofocus>
                            @if ($errors->has('beneficiario'))
                            <span class="help-block">
                                <strong>{{ $errors->first('beneficiario') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="no_increm_cheque" class="label_header">{{ trans('agenda.noincrecheque') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="checkbox" id="no_increm_cheque" class="form-check-input" name="no_increm_cheque" value="1" @if (@$no_increm_cheque=='1' ) checked @endif>
                        </div>
                    </div>
                    <!-- 4 row -->
                    <div class="col-md-4 px-1">
                        <label for="" class="control-label label_header">{{ trans('contableM.sucursal') }}</label>
                        <select name="sucursal" id="sucursal" class="form-control" required>
                            <option value="">{{ trans('agenda.seleccione') }}..</option>
                            @foreach ($sucursales as $sucursal)
                            <option value="{{ $sucursal->codigo_sucursal }}">{{ $sucursal->codigo_sucursal }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-xs-4 px-1">
                        <div class="col-md-12 px-0">
                            <label for="direccion" class="label_header">{{ trans('agenda.direccion') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="direccion" type="text" class="form-control" name="direccion" value="{{ old('direccion') }}" maxlength="150" autofocus>
                        </div>
                    </div>
                    <div class="form-group col-xs-4 px-1">
                        <div class="col-md-12 px-0">
                            <label for="ruc" class="label_header">R.U.C /
                                {{ trans('contableM.cedula') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="ruc" type="text" class="form-control" name="ruc" value="{{ old('ruc') }}" maxlength="13" autofocus>
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
                            <label for="id_cuenta_origen" class="label_header">{{ trans('contableM.CuentaOrigen') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select class="form-control" name="id_cuenta_origen" id="id_cuenta_origen" required>
                                <option value="">{{ trans('agenda.seleccione') }}...</option>
                                @foreach ($bancos as $value)
                                <option value="{{ $value->cuenta_mayor }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id_divisa" class="label_header">{{ trans('contableM.divisas') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select class="form-control" name="id_divisa_origen" id="id_divisa_origen" required>
                                @foreach ($divisas as $value)
                                <option value="{{ $value->id }}">{{ $value->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id_cambio_origen" class="label_header">{{ trans('contableM.cambio') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="id_cambio_origen" type="text" class="form-control text_der number" value="1.00" name="id_cambio_origen" required autofocus>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="valor_origen" class="label_header">{{ trans('contableM.valor') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="valor_origen" type="text" class="form-control text_der" onchange="calcular()" value="0.00" name="valor_origen" autofocus>
                        </div>
                    </div>
                    <!-- 5 row -->
                    <div class="form-group col-xs-6 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id_cuenta_destino" class="label_header">{{ trans('contableM.CuentaDestino') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select class="form-control" style="width: 100%;" name="id_cuenta_destino" id="id_cuenta_destino" required>
                                <option value="">{{ trans('agenda.seleccione') }}...</option>
                                @foreach ($bancos as $value)
                                <option value="{{ $value->cuenta_mayor }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id_divisa_destino" class="label_header">{{ trans('contableM.divisas') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select class="form-control" name="id_divisa_destino" id="id_divisa_destino" required>
                                @foreach ($divisas as $value)
                                <option value="{{ $value->id }}">{{ $value->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id_cambio_destino" class="label_header">{{ trans('contableM.cambio') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="id_cambio_destino" type="text" class="form-control text_der number" value="1.00" name="id_cambio_destino" required autofocus>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="valor_destino" class="label_header">{{ trans('contableM.valor') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="valor_destino" type="text" class="form-control text_der number" value="0.00" name="valor_destino" required readonly autofocus>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body dobra">
                <!--header row-->
                <div class="col-md-1">
                </div>
                <div class="header row  col-md-11">
                    <!-- 1 row -->
                    <div class="form-group col-xs-6 px-1">
                        <div class="col-md-12 px-0">
                            <label for="glosa" class="label_header">{{ trans('contableM.glosa') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <textarea id="glosa" rows="16" class="form-control" name="glosa" value="{{ old('glosa') }}" maxlength="250" required autofocus></textarea>
                            @if ($errors->has('glosa'))
                            <span class="help-block">
                                <strong>{{ $errors->first('glosa') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6 px-1">
                        <div class="col-md-12 px-0">
                            <label for="proveedor" class="label_header">Proveedor</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select class="form-control select2" name="id_proveedor" id="id_proveedor" style="width: 100%;" onchange="cargar_autorizacion(this)">
                                <option value="">Seleccione...</option>
                                @foreach ($proveedores as $x)
                                <option value="{{ $x->id }}">{{ $x->razonsocial }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-6 px-1">
                        <div class="col-md-12 px-0">
                            <label for="autorizacion" class="label_header"># Autorizacion</label>
                        </div>
                        <div class="col-md-11 px-0" id="div_autorizacion">
                            <select name="autorizacion" id="autorizacion" onkeypress="return isNumberKey(event)" maxlength="49" class="form-control" onchange="cargar_datos_de_autorizacion(this)">
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                        <div class="col-md-1 px-0"><button type="button" class="btn btn-success" id="cambiarObj"><i class="fa fa-plus"></i></button">
                        </div>
                    </div>
                    <!-- 2 row -->
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="comision" class="label_header">{{ trans('agenda.comision') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select class="form-control" name="id_comision" id="id_comision">
                                <option value="">{{ trans('agenda.seleccione') }}...</option>
                                <option value="0">{{ trans('contableM.ningun_') }}</option>
                                <option value="5">5 %</option>
                                <option value="7">7 %</option>
                                <option value="8">8 %</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="valor_comision" class="label_header">&nbsp;</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="valor_comision" type="text" class="form-control text_der " value="0.00" name="valor_comision" autofocus readonly>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="comision_tarjeta" class="label_header">{{ trans('contableM.comisidetarjetac') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="comision_tarjeta" type="text" class="form-control text_der " onchange="calcular()" value="0.00" name="comision_tarjeta" autofocus>
                        </div>
                    </div>
                    <!-- 3 row -->
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="baseiva" class="label_header">{{ trans('contableM.baseiva') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="baseiva" type="text" class="form-control text_der " value="0.00" name="baseiva" onchange="calcular()" autofocus>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id_base_iva" class="label_header">&nbsp;</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select class="form-control" name="id_base_iva" id="id_base_iva" onchange="calcular()">
                                <option value="">{{ trans('agenda.seleccione') }}...</option>
                                <option value="0" data-value="0">(Ninguno)</option>
                                @foreach ($retencionesR as $value)
                                <option value="{{ $value->cuenta_deudora }}" data-value="{{ $value->valor }}">
                                    {{ $value->valor }} %
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="iva_retenido" class="label_header">{{ trans('contableM.ivarete') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="iva_retenido" type="text" class="form-control text_der " value="0.00" name="iva_retenido" autofocus>
                        </div>
                    </div>
                    <!-- 4 row -->
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="base_ret_fuente" class="label_header">{{ trans('contableM.bretenfuente') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="base_ret_fuente" type="text" class="form-control text_der " onchange="calcular();cambiar_estado();" value="0.00" name="base_ret_fuente" autofocus>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id_base_ret_fuente" class="label_header">&nbsp;</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select class="form-control" name="id_base_ret_fuente" id="id_base_ret_fuente" onchange="calcular(); ">
                                <option value="">{{ trans('agenda.seleccione') }}...</option>
                                <option value="0" data-value="0">(Ninguno)</option>
                                @foreach ($retenciones as $value)
                                <option value="{{ $value->cuenta_deudora }}" data-value="{{ $value->valor }}">
                                    {{ $value->valor }} %
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="retencion_fuente" class="label_header">{{ trans('contableM.retenenlafuente') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="retencion_fuente" type="text" class="form-control text_der " value="0.00" name="retencion_fuente" readonly autofocus>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="" class="label_header">&nbsp;</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <label for="val_adicionales">{{ trans('contableM.valadicionales') }}</label>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="valores_adicionales" class="label_header">&nbsp;</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="valores_adicionales" type="text" class="form-control text_der " onchange="calcular()" value="0.00" name="valores_adicionales" autofocus>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="total" class="label_header">{{ trans('contableM.total') }}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="total" type="text" class="form-control text_der " value="0.00" name="total" readonly autofocus>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group col-xs-10 text-center">
                <div class="col-md-6 col-md-offset-4">
                    <button type="button" class="btn btn-default btn-gray btn_add">
                        <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;{{ trans('contableM.guardar') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('/js/jquery-ui.js') }}"></script>
<script src="{{ asset('/plugins/datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>
<script src="{{ asset('/js/bootstrap-datetimepicker.js') }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="{{ asset('plugins/iCheck/icheck.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script type="text/javascript">
    $('.select2').select2({
        tags: false
    });
    $('#retenciones').on('hidden.bs.modal', function() {
        $(this).removeData('bs.modal');
    });

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;
        return true;
    }
    $('.select2_cuenta_destino').select2({
        placeholder: `{{ trans('contableM.selecccuentadestino') }}`,
        allowClear: true,
        ajax: {
            url: '<?= route('transferenciabancaria.buscar_cuenta_destino'); ?>',
            data: function(params) {
                var query = {
                    search: params.term,
                    type: 'public'
                }
                return query;
            },
            processResults: function(data) {
                // Transforms the top-level key of the response object from 'items' to 'results'
                console.log(data);
                return {
                    results: data
                };
            }
        },
    });

    function cambiar_estado() {
        datos = parseFloat($('#base_ret_fuente').val());
        if (datos > 0) {
            $("#id_base_ret_fuente").prop('required', true);
        } else {
            $("#id_base_ret_fuente").prop('required', false);
        }
    }
    var fila = $("#mifila").html();
    $(".print").css('visibility', 'hidden');
    $(document).ready(function() {
        $('.select2_cuentas').select2({
            tags: false
        });
        $('input[type="checkbox"].flat-green').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass: 'iradio_flat-green'
        });
        $('input[type="checkbox"].flat-red').iCheck({
            checkboxClass: 'icheckbox_flat-red',
            radioClass: 'iradio_flat-red'
        });
        $('#cambiarObj').click(function() {
            $('#div_autorizacion').html('<input type="text" name="autorizacion" onkeypress="return isNumberKey(event)" maxlength="49" id="autorizacion" class="form-control" placeholder="Autorización">');
        });
    });
    $(function() {
        $('.number').keypress(function(event) {
            if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
                event.preventDefault(); //stop character from entering input
            }
        });

    });

    function nuevo() {
        // var nuevafila = $("#mifila").html();
        // var rowk = document.getElementById("agregar_cuentas").insertRow(-1);
        $("#form")[0].reset();
        $('.select2_cuentas').select2({
            tags: false
        });
    }
    function calcular() {
        var valor_origen = $("#valor_origen").val();
        var valor_destino = $("#valor_destino").val();
        var comision_tarjeta = $("#comision_tarjeta").val();
        var iva_retenido = $("#iva_retenido").val();
        var retencion_fuente = $("#retencion_fuente").val();
        var valores_adicionales = $("#valores_adicionales").val();
        var valores_destino = 0;
        var total = 0;
        var subtotal = 0;
        var base_ret_fuente = $("#base_ret_fuente").val();
        var base_iva = $("#baseiva").val();
        var id_base_ret_fuente = $("#id_base_ret_fuente option:selected").data('value');
        var id_base_iva = $("#id_base_iva option:selected").data('value');
        var retencion_iva = 0;
        var retencion_fuente = 0;
        if (base_ret_fuente != null && id_base_ret_fuente != null) {
            retencion_fuente = (parseFloat(base_ret_fuente) * parseFloat(id_base_ret_fuente)) / 100;
            retencion_fuente = redondea_precio(retencion_fuente, 2); //alert(retencion_fuente);
            $("#retencion_fuente").val(retencion_fuente);
        }
        if (id_base_iva != null && base_iva != null) {
            retencion_iva = (parseFloat(base_iva) * parseFloat(id_base_iva)) / 100;
            retencion_iva = redondea_precio(retencion_iva, 2);
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

    function devuelvefloat(cant, decimales) {
        var tmp = null;
        $.ajax({
            url: "{{ route('transferenciabancaria.devuelvefloat') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            type: 'POST',
            datatype: 'json',
            async: false,
            data: {
                cantidad: cant,
                decimales: decimales
            },
            success: function(data) {
                tmp = data.valor;
            },
            error: function(data) {
                console.error(data.responseText);
            }
        });
        return tmp;
    }
    $(".btn_add").click(function() {
        var sucursal = $("#sucursal").val();
        if (sucursal == 0 || sucursal == "") {
            alertas("error", "Error...", "Seleccione la sucursal")
            return;
        }
        document.querySelector(".btn_add").style.display = "none";
        if ($("#form").valid()) {
            $(".print").css('visibility', 'visible');
            $(".btn_add").attr("disabled", true);
            $.ajax({
                url: "{{ route('transferenciabancaria.store') }}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                type: 'POST',
                datatype: 'json',
                data: $("#form").serialize(),
                success: function(data) {
                    if (data.status == "error") {
                        Swal.fire({
                            title: data.status.toUpperCase(),
                            icon: data.status,
                            html: data.msj,
                        });
                        document.querySelector(".btn_add").style.display = "block";
                    }
                    if (data.status == 'success') {
                        $("#asiento").val(data.id_asiento);
                        $("#id").val(data.id);
                        $("#numero").val(data.numero);

                        Swal.fire(
                            data.status.toUpperCase(),
                            data.msj,
                            data.status
                        )
                        document.querySelector(".btn_add").style.display = "block";
                        location.href = "{{ route('transferenciabancaria.index') }}";
                    }
                },
                error: function(data) {
                    console.error(data.responseText);
                }
            });
        } else {
            document.querySelector(".btn_add").style.display = "initial";
        }
    });

    function redondea_precio(value, decimales = 3) {
        value = +value;
        if (isNaN(value)) return NaN; // Shift 
        value = value.toString().split('e');
        value = Math.round(+(value[0] + 'e' + (value[1] ? (+value[1] + 2) : 2))); // Shift back 
        value = value.toString().split('e');
        return (+(value[0] + 'e' + (value[1] ? (+value[1] - 2) : -2))).toFixed(2);
    }
    // $('#fecha_asiento').datetimepicker({
    //     format: 'YYYY/MM/DD',
    //     defaultDate: '<?= date('Y-m-d'); ?>',
    //     minDate: $('#minDate').val(),
    // });

    function cargar_autorizacion(e) {
        $.ajax({
            url: "{{ route('transferenciabancaria.traer_autorizaciones') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            type: 'POST',
            datatype: 'json',
            data: {
                id_proveedor: e.value,
            },
            success: function(data) {
                let array = data.autorizaciones;
                if (array.length == 0) {
                    $('#div_autorizacion').html('<input type="text" name="autorizacion" onkeypress="return isNumberKey(event)" maxlength="49" id="autorizacion" class="form-control" placeholder="Autorización">');
                } else {
                    $('#div_autorizacion').html('<select name="autorizacion" id="autorizacion" class="form-control" onchange="cargar_datos_de_autorizacion(this)"> <option value="">Seleccione</option></select>');
                    //cargar_autorizacion('id_proveedor');
                }
                $("#autorizacion").find('option:not(:first)').remove();
                let est = '';
                let emi = '';
                let num = '';
                $.each(array, function(key, value) {
                    est = value.autorizacion.substr(24, 3);
                    emi = value.autorizacion.substr(27, 3);
                    num = value.autorizacion.substr(30, 9);
                    $("#autorizacion").append('<option value=' + value.id + '>' + value.autorizacion + ' ' + est + '-' + emi + '-' + num + '</option>');
                });
            },
            error: function(data) {
                console.error(data.responseText);
            }
        });
    }

    function cargar_datos_de_autorizacion(e) {
        $.ajax({
            url: "{{ route('transferenciabancaria.traer_datos_genera') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            type: 'POST',
            datatype: 'json',
            data: {
                id: e.value,
            },
            success: function(data) {
                console.log(data);
                let datos = data.dato;
                if (datos != null) {
                    console.log(datos);
                    var porF = '';
                    var porI = '';
                    try {
                        var porF_ = datos.porcentaje_fuente.split('|');
                        porF = porF_[0];
                    } catch (e) {
                        porF = '';
                    }
                    try {
                        var porI_ = datos.porcentaje_iva.split('|');
                        porI = porI_[0];
                    } catch (e) {
                        porI = '';
                    }
                    $("#baseiva").val(datos.base_iva);
                    $("#base_ret_fuente").val(datos.base_retencion);
                    $("#id_base_iva").val(porI);
                    $("#id_base_ret_fuente").val(porF);
                } else {
                    $("#baseiva").val("");
                    $("#base_ret_fuente").val("");
                    $("#id_base_iva").val("");
                    $("#id_base_ret_fuente").val("");
                }
                calcular();
            },
            error: function(datid_base_iva) {
                console.error(data.responseText);
            }
        });
    }

    function validarCierre() {
        <?php
        if (isset($min_date) && $min_date != null) {
        ?>
            let fechaAc = new Date();
            var fecha1 = '<?= $min_date; ?>';
            var fecha2 = document.getElementById("fecha_asiento").value;
            const fechaInicial = new Date(fecha1);
            const fechaFinal = new Date(fecha2);
            if (fechaFinal <= fechaInicial) {
                alertas("error", "Recuerde!", 'Este registro no se puede modificar, ya que la fecha seleccionada corresponde a un período cerrado contablemente. Para hacer cambios, elija una fecha dentro del período contable vigente.')
                const fechaFormateada = fechaAc.toISOString().split('T')[0];
                document.getElementById("fecha_asiento").value = fechaFormateada;
            }
        <?php
        }
        ?>
    }
</script>
@endsection