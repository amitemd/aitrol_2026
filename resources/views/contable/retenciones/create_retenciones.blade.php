@extends('contable.retenciones.base')
@section('action-content')
<?php

use Sis_medico\De_Empresa;

$deEmpresa = De_Empresa::getDatos($empresa->id);
?>
<style type="text/css">
    .ui-autocomplete {
        overflow-x: hidden;
        max-height: 200px;
        width: 1px;
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

    input {
        width: 96%;
        padding: 0 2%;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    /* Hide default HTML checkbox */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* The slider */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>
<script type="text/javascript">
    function goBack() {
        location.href = "{{ route('retenciones_index') }}";
    }
</script>
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<link rel="stylesheet" href="{{ asset('/css/icheck/all.css')}}">
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
            <li class="breadcrumb-item"><a href="#">Retenciones Proveedores</a></li>
            <li class="breadcrumb-item"><a href="{{ route('retenciones_index') }}">{{trans('contableM.retencion')}} </a></li>
            <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.nuevaretencion')}}</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-9 col-sm-9 col-6">
                        <div class="box-title "><b>ACREEDORES-COMP. DE RETENCIONES</b></div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            {{-- <input type="hidden" id="minDate" name="minDate" value="{{ isset($min_date) ? date('Y-m-d', strtotime($min_date.'+1 day')) : '' }}"> --}}
                            <button type="button" onclick="guardar_retenciones()" id="boton_guardar" class="btn btn-success btn-gray "><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
                            </button>
                            <button type="button" class="btn btn-success  btn-gray" onclick="nuevo_comprobante()" style="margin-left: 10px;">
                                <i class="glyphicon glyphicon-file" aria-hidden="true"></i>&nbsp;&nbsp;Nuevo
                            </button>
                            <button type="button" onclick="goBack()" class="btn btn-success  btn-gray">
                                <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                            </button>


                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-body dobra">
            <form action="#" method="POST" id="form_guardado">
                <div class="col-md-12">
                    <div class="header row">
                        <div class="form-group col-md-12 px-1">
                            <label class="label_header">{{trans('contableM.Buscador')}}</label>
                            <select style="width: 100%;" class="form-control select2" onchange="changeProveedor(this)" name="acreedores" id="acreedores">
                                <option value="">Seleccione...</option>
                                @foreach($compras as $c)
                                @php
                                $cruce = Sis_medico\Ct_Cruce_Cuentas::where('id_factura', $c->id)->where('estado', 1)->first();
                                @endphp
                                @if(is_null($cruce))
                                @php
                                $retencion= Sis_medico\Ct_Retenciones::where('id_compra',$c->id)->orderBy('id','DESC')->first();
                                @endphp
                                @if($retencion!='')
                                @if($retencion->estado==0 && $retencion->anulado==0)
                                <option data-subtotal="{{$c->subtotal}}" data-proveedor="{{$c->proveedor}}" data-iva="{{$c->iva_total}}" data-total="{{$c->total_final}}" data-concepto="{{$c->observacion}}" data-nombre="{{$c->proveedorf->razonsocial}}" data-autorizacion="{{$c->autorizacion}}" data-fecha="{{$c->fecha}}" value="{{$c->id}}"> #: {{$c->id}} P: {{$c->proveedorf->nombrecomercial}} - {{$c->numero}} | {{$c->observacion}} </option>
                                @elseif($retencion->estado==1 && $retencion->anulado==1)
                                <option data-subtotal="{{$c->subtotal}}" data-proveedor="{{$c->proveedor}}" data-iva="{{$c->iva_total}}" data-total="{{$c->total_final}}" data-concepto="{{$c->observacion}}" data-nombre="{{$c->proveedorf->razonsocial}}" data-autorizacion="{{$c->autorizacion}}" data-fecha="{{$c->fecha}}" value="{{$c->id}}"> #: {{$c->id}} P: {{$c->proveedorf->nombrecomercial}} - {{$c->numero}} | {{$c->observacion}} </option>
                                @else
                                @endif
                                @else
                                <option data-subtotal="{{$c->subtotal}}" data-proveedor="{{$c->proveedor}}" data-iva="{{$c->iva_total}}" data-total="{{$c->total_final}}" data-concepto="{{$c->observacion}}" data-nombre="{{$c->proveedorf->razonsocial}}" data-autorizacion="{{$c->autorizacion}}" data-fecha="{{$c->fecha}}" value="{{$c->id}}"> #: {{$c->id}} P: {{$c->proveedorf->nombrecomercial}} - {{$c->numero}} | {{$c->observacion}} </option>
                                @endif
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            &nbsp;
                        </div>
                        <div class="col-md-12 px-1">
                            <label class="label label-default"> DATOS DE FACTURA </label>
                        </div>
                        <div class="col-md-1 px-1">
                            <label class="label_header">#</label>
                            <input type="text" class="form-control" id="idcompra" readonly>
                        </div>
                        <div class="col-md-4 px-1">
                            <label class="label_header">{{trans('contableM.proveedor')}}</label>
                            <input type="text" class="form-control" name="proveedor" id="proveedor" readonly>
                            <input type="hidden" name="id_proveedor" id="id_proveedor">
                            <input type="hidden" name="id_compra" id="id_compra">
                            <input type="hidden" name="subtotal" id="subtotal">
                            <input type="hidden" name="iva" id="iva">
                        </div>
                        <div class="col-md-4 px-1">
                            <label class="label_header">{{trans('contableM.concepto')}}</label>
                            <input type="text" class="form-control" name="conceptof" id="conceptof" readonly>
                        </div>
                        <div class="col-md-3 px-1">
                            <label class="label_header">{{trans('contableM.autorizacion')}}</label>
                            <input type="text" class="form-control" name="autorizacionc" id="autorizacionc" required readonly>
                        </div>
                        <div class="col-md-12 px-1" style="margin-top: 10px;">
                            <label class="label label-default"> {{trans('contableM.datosretencion')}} </label>
                        </div>

                        <div class="col-md-3 px-1">
                            <label class="label_header">{{trans('contableM.fecha')}}</label>
                            <input type="date" class="form-control" name="fecha" id="fecha" onchange="validarCierre();" min="{{ isset($min_date) ? date('Y-m-d', strtotime($min_date.'+1 day')) : '' }}" value="{{date('Y-m-d')}}" required>
                        </div>
                        <div class="col-md-6 px-1">
                            <label class="label_header">{{trans('contableM.concepto')}}</label>
                            <input type="text" class="form-control" name="concepto" id="concepto" required placeholder="Concepto Retencion">
                        </div>
                        <div class="col-md-3 px-1">
                            <label class="label_header">{{trans('contableM.autorizacion')}}</label>
                            <input type="text" class="form-control" name="autorizacion" id="autorizacion" placeholder="Autorizacion Retención">
                        </div>
                        <div class="col-md-3 px-1">
                            <label class="label_header">{{trans('contableM.sucursal')}}</label>
                            <select name="sucursal" id="sucursal" onchange="obtener_caja()" class="form-control" required>
                                <option value="">Seleccione</option>
                                @foreach($sucursales as $s)
                                <option value="{{$s->id}}">{{$s->codigo_sucursal}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 px-1">
                            <label class="label_header">{{trans('contableM.puntoemision')}}</label>
                            <select name="pemision" id="pemision" class="form-control" required>
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                        <div class="col-md-3 px-1">
                            <label class="label_header">{{trans('contableM.secuencia')}}</label>
                            <input type="text" class="form-control" name="secuencial" id="secuencial" placeholder="Ingrese Secuencial">
                        </div>
                        <div class="col-md-1 px-1">
                            <label for="empresa" class="label_header">Electrónica</label>

                            <label class="switch">
                                <input class="electros" <?php $estado = '';
                                                        if ($empresa->electronica == 0) {
                                                            $estado = 'disabled';
                                                        }
                                                        if ($deEmpresa != '') {
                                                            $estado = 'checked';
                                                        }
                                                        echo $estado; ?> id="toggleswitch" type="checkbox">
                                <span class="slider round"></span>
                                <input type="hidden" name="electronica" id="electronica" value="<?php $elec = $empresa->electronica;
                                                                                                if ($elec == 0) {
                                                                                                    if ($deEmpresa != '') $elec = 1;
                                                                                                } else {
                                                                                                    $elec = 1;
                                                                                                }
                                                                                                echo $elec; ?>">
                            </label>
                        </div>
                        <div class="col-md-1 px-1">
                            <label class="label_header" for="empresa">Anulado &nbsp; &nbsp;</label>
                            <label class="switch">
                                <input class="electros" id="toggleswitch2" type="checkbox" disabled>
                                <span class="slider round"></span>
                                <input type="hidden" name="anulado" id="anulado" value="0">
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    &nbsp;
                </div>
                <div class="col-md-12 px-1">
                    <div class="table table-responsive">
                        <table id="example2" class="display compact" style="width: 100%;">
                            <thead style="text-align: center;">
                                <tr>
                                    <th>{{trans('contableM.tiporetencion')}}</th>
                                    <th>{{trans('contableM.porcentajeretencion')}}</th>
                                    <th>{{trans('contableM.codigo')}}</th>
                                    <th>{{trans('contableM.baseretencion')}}</th>
                                    <th>Codigo</th>
                                    <th>Base de Retención</th>
                                    <th>%</th>
                                    <th>{{trans('contableM.monto')}}</th>
                                    <th><button type="button" class="btn btn-primary btn-gray" onclick="add()"> <i class="fa fa-plus"></i> </button></th>
                                </tr>
                            </thead>
                            <tbody id="agregar_cuentas">
                                <tr id="mifila" style="display: none;">
                                    <td><select name="tipo_retencion[]" class="form-control select2 tretencion" onchange="traer_retenciones(this)" style="width: 90%; height: 90%;" required>
                                            <option value="">Seleccione...</option>
                                            <option value="2"> {{trans('contableM.renta')}} </option>
                                            <option value="1">{{trans('contableM.iva')}}</option>
                                        </select></td>
                                    <td><input type="hidden" name="id_porcentaje[]" class="pid"><select name="rporcentaje[]" onchange="lista_valores(this)" class="form-control porcentaje select2" style="width: 90%; height: 90%;" required>
                                            <option value="">Seleccione...</option>
                                        </select> </td>
                                    <td><input type="text" class="form-control c" name="codigo[]" style="width: 90%; height: 90%;" readonly></td>
                                    <td><input type="text" name="base_retencion[]" onchange="base(this)" class="form-control b" style="width: 90%; height: 90%;"></td>
                                    <td><input type="text" name="porcentaje[]" class="form-control p" style="width: 90%; height: 90%;" readonly></td>
                                    <td><input type="text" name="monto[]" class="form-control m" style="width: 90%; height: 90%;" value="0" readonly></td>
                                    <td><button class="btn btn-primary btn-gray" type="button" onclick="eliminar(this)"> <i class="fa fa-trash"></i> </button></td>
                                </tr>
                                <tr>
                                    <td> <input type="hidden" name="id_porcentaje[]" class="pid"> <select name="tipo_retencion[]" class="form-control select2 tretencion" onchange="traer_retenciones(this)" style="width: 100%; height: 90%;" required>
                                            <option value="">Seleccione...</option>
                                            <option value="2"> {{trans('contableM.renta')}} </option>
                                            <option value="1">{{trans('contableM.iva')}}</option>
                                        </select></td>
                                    <td><select name="rporcentaje[]" onchange="lista_valores(this)" class="form-control porcentaje select2" style="width: 90%; height: 90%;" required>
                                            <option value="">Seleccione...</option>
                                        </select></td>
                                    <td><input type="text" class="form-control c" name="codigo[]" style="width: 90%; height: 90%;" readonly></td>
                                    <td><input type="text" name="base_retencion[]" class="form-control b" onchange="base(this)" style="width: 90%; height: 90%;"></td>
                                    <td><input type="text" name="porcentaje[]" class="form-control p" style="width: 90%; height: 90%;" readonly></td>
                                    <td><input type="text" name="monto[]" class="form-control m" style="width: 90%; height: 90%;" value="0" readonly></td>
                                    <td><button class="btn btn-primary btn-gray" type="button" onclick="eliminar(this)"> <i class="fa fa-trash"></i> </button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-12">
                    &nbsp;
                </div>
                <div class="col-md-4 px-1">
                    <label class="label_header"> {{trans('contableM.renta')}} </label>
                    <input type="text" class="form-control" id="valor_renta" name="valor_renta" value="0.00" readonly>
                </div>
                <div class="col-md-4 px-1">
                    <label class="label_header"> {{trans('contableM.iva')}} </label>
                    <input type="text" class="form-control" id="valor_iva" name="valor_iva" value="0.00" readonly>
                    <input type="hidden" name="total" id="total" value="0.00">
                </div>
                <div class="col-md-4 px-1">
                    <label class="label_header"> {{trans('contableM.total')}} </label>
                    <input type="text" class="form-control" name="totalr" id="totalr" value="0.00" readonly>

                </div>
            </form>
        </div>

    </div>

    </div>


</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ('/js/icheck.js') }}"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script src="{{ asset ('/plugins/datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>
<script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>


<script type="text/javascript">
    var fila = $("#mifila").html();
    // $(function() {
    //     $('#fecha').datetimepicker({
    //         format: 'YYYY/MM/DD',
    //         defaultDate: "{{date('Y-m-d')}}",
    //         minDate: $('#fecha').val(),
    //     });
    // });

    function eliminar(e) {
        $(e).parent().parent().remove();
        suma_totales()
    }
    $(document).ready(function() {
        $('#example2').DataTable({
            'paging': false,
            dom: 'lBrtip',
            'lengthChange': false,
            'searching': true,
            'ordering': false,
            responsive: true,
            'info': false,
            'autoWidth': true,
        })
    });

    function base(e) {
        var total_factura = $("#subtotal").val();
        var base = parseFloat($(e).val());
        //var subtotal= parseFloat($("#subtotal").val());
        var codigo = parseFloat($(e).parent().parent().find('.p').val());
        var tipo = $(e).parent().parent().find('.tretencion').val();
        console.log(tipo)
        var total_ivav = parseFloat($("#iva").val());
        if (tipo == '1') {
            var factura_total = base;

            var totales = base * (codigo / 100);
            totales = redondeafinal(totales);
            $(e).parent().parent().find('.m').val(totales);
            total_ivav = redondeafinal(total_ivav);
            console.log(totales + " total");
            //$(e).parent().parent().find('.b').val(total_ivav);
        } else {
            var totales = base * (codigo / 100);
            console.log(totales + " total");
            totales = redondeafinal(totales);
            total_factura = redondeafinal(total_factura);
            $(e).parent().parent().find('.m').val(totales);
            //$(e).parent().parent().find('.b').val(total_factura);
        }
        suma_totales();
        $(e).val(redondeafinal(base));
    }

    function suma_totales() {
        var valor_iva = 0;
        var valor_renta = 0;
        $('.tretencion').each(function() {
            var tipo = $(this).val();
            if (tipo == 1) { //iva
                var incomplete = parseFloat($(this).parent().parent().find('.m').val());
                valor_iva = (incomplete) + parseFloat(valor_iva);
            } else {
                var incomplete = parseFloat($(this).parent().parent().find('.m').val());
                valor_renta = parseFloat((incomplete) + parseFloat(valor_renta));
            }
        });
        console.log(valor_iva, valor_renta);
        var total = valor_renta + valor_iva;
        $('#valor_renta').val(valor_renta.toFixed(2, 2));
        $('#valor_iva').val(valor_iva.toFixed(2, 2));
        $('#totalr').val(redondeafinal(total));
        $('#total').val(redondeafinal(total));
    }
    $('.select2').select2({
        tags: false
    });

    function add() {
        var nuevafila = $("#mifila").html();
        var rowk = document.getElementById("agregar_cuentas").insertRow(-1);
        rowk.innerHTML = fila;
        $('.select2').select2({
            tags: false
        });
    }

    function nuevo_comprobante() {
        // location.href = "{{route('retenciones_crear')}}";
        location.reload(true);
    }

    function grupos_acreedores() {
        var valor = $("#id_acreedor").val();
        //alert(valor);
        $.ajax({
            type: 'post',
            url: "{{route('retencionesa.buscarpro')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'opcion': valor
            },
            success: function(data) {
                //alert(data[0].nombre);
                console.log(data);
                if (data.value != 'No se encontraron resultados') {
                    if (valor != 0) {
                        $("#id_facturaf").empty();
                        $("#id_facturaf").append('<option value=' + "" + '>' + "Seleccione # Factura..." + '</option>');
                        $.each(data, function(key, registro) {
                            $("#id_facturaf").append('<option value=' + registro.id + '>' + registro.value + '</option>');
                        });
                    } else {
                        $("#id_facturaf").empty();
                        buscar_factura2();
                    }
                } else {
                    $("#id_facturaf").empty();
                    buscar_factura2()
                }
            },
            error: function(data) {
                console.log(data);
            }
        })
    }
    var input = document.getElementById('toggleswitch');
    //var outputtext = document.getElementById('status');
    input.addEventListener('change', function() {
        if (this.checked) {
            $("#electronica").val(1);
            $('#punto_final').attr('readonly', true);
            //$('#autorizacion').attr('required', false);
        } else {
            $("#electronica").val(0);
            $('#punto_final').attr('readonly', false);
            //$('#autorizacion').attr('required', true);
        }
    });
    var input2 = document.getElementById('toggleswitch2');
    //var outputtext = document.getElementById('status');
    input2.addEventListener('change', function() {
        if (this.checked) {
            $("#anulado").val(1);
            //$('#autorizacionc').attr('required',false);
            //$('#punto_final').attr('readonly', true);
            //$('#numero').attr('readonly', true);
        } else {
            $("#anulado").val(0);
            //$('#autorizacionc').attr('required',true);
            //$('#punto_final').attr('readonly', false);
            //$('#numero').attr('readonly', false);
        }
    });
    $("#buscar").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "{{route('retenciones_codigo')}}",
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        minLength: 2,
    });

    function traer_retenciones(e) {
        //retenciones.buscartipo
        var id = $(e).val();
        //alert(id);
        $.ajax({
            type: 'get',
            url: "{{route('retenciones.buscartipo')}}",
            datatype: 'json',
            data: {
                'id': id
            },
            success: function(data) {
                if (data != null) {
                    $(e).parent().parent().find('.porcentaje').empty();
                    $(e).parent().parent().find('.porcentaje').append('<option value="0">Seleccione...</option>');
                    $.each(data, function(key, registro) {
                        $(e).parent().parent().find('.porcentaje').append('<option value=' + registro.id + '>' + registro.codigo + ' - ' + registro.nombre + '</option>');
                    });
                } else {
                    $(e).parent().parent().find('.porcentaje').empty();
                }
                // console.log(data);  
                //swal(`{{trans('contableM.correcto')}}!`,"Retencion guardada correctamente","success");
            },
            error: function(data) {
                //console.log(data);
            }
        });
    }

    function guardar_retenciones() {
        var fechaFactura = $('option:selected', $("#acreedores")).data("fecha");
        var fecha1 = fechaFactura;
        var fechaRetencion = $('#fecha').val().replaceAll('/', '-');
        var fecha2 = fechaRetencion;
        fechaFactura = fechaFactura.split('-');
        fechaRetencion = fechaRetencion.split('-');
        var f1 = new Date(fechaFactura[0], fechaFactura[1], fechaFactura[2]);
        var f2 = new Date(fechaRetencion[0], fechaRetencion[1], fechaRetencion[2]);
        if (f1 > f2) {
            alertas('error', 'Fecha de la retención no puede ser menor a la de la factura\nFecha de fac: ' + fecha1 + '\nFecha de ret: ' + fecha2);
            return;
        }
        if ($('#form_guardado').valid()) {
            $("#boton_guardar").attr("disabled", "disabled");
            $.ajax({
                type: 'post',
                url: "{{route('contable.retenciones.newstore')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $('#form_guardado').serialize(),
                success: function(data) {
                    if (data.error == 'no') {
                        $("#boton_guardar").attr("disabled", "disabled");
                        swal("Correcto!", "Retencion guardada correctamente", "success");
                        $('#form_guardado input').attr('readonly', 'readonly');
                    } else {
                        $("#boton_guardar").prop("disabled", false);
                        var mensajeError = String(data.error || "");
                        var opcionesAlerta = {
                            icon: "warning",
                            title: "Advertencia",
                            text: mensajeError,
                            confirmButtonText: "OK"
                        };
                        if (mensajeError.trim().toLowerCase() === "el proveedor no tiene plan de cuentas asignado") {
                            var idProveedor = String($("#id_proveedor").val() || "").trim();
                            var urlProveedor =
                                "{{ url('contable/acreedores/mantenimiento/acreedor/editar') }}/" +
                                encodeURIComponent(idProveedor);
                            opcionesAlerta.footer =
                                '<a href="' + urlProveedor + '"' +
                                ' target="_blank" rel="noopener noreferrer">' +
                                '<i class="fa fa-external-link"></i> ' +
                                "Editar proveedor en una nueva ventana" +
                                "</a>";
                        }
                        Swal.fire(opcionesAlerta);
                    }
                },
                error: function(data) {
                    //console.log(data);
                }
            })
        }
    }

    function redondeafinal(value, decimales = 2) {
        value = +value;
        if (isNaN(value)) return NaN; // Shift 
        value = value.toString().split('e');
        value = Math.round(+(value[0] + 'e' + (value[1] ? (+value[1] + 2) : 2))); // Shift back 
        value = value.toString().split('e');
        return (+(value[0] + 'e' + (value[1] ? (+value[1] - 2) : -2))).toFixed(2);
    }

    function lista_valores(e) {

        var variable_select = $(e).val();
        var tipo = $(e).parent().parent().find('.tretencion').val();
        var total_factura = $("#subtotal").val();
        var total_ivav = parseFloat($("#iva").val());
        if (isNaN(total_ivav)) {
            total_ivav = 0;
        }
        //alert(valor);
        console.log(variable_select);
        $.ajax({
            type: 'post',
            url: "{{route('retenciones_query')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'opcion': variable_select,
                'tipo': tipo
            },
            success: function(data) {
                //alert(data[0].nombre);
                console.log(data);
                if (data.value != 'no') {
                    $(e).parent().parent().find('.c').val(data[0].codigo);
                    $(e).parent().parent().find('.pid').val(data[0].id);

                    var codigo = parseFloat(data[0].valor);
                    $(e).parent().parent().find('.p').val(data[0].valor);
                    //1 es iva 2 fuente
                    //console.log(codigo+"el codigo es ");

                    if (tipo == '1') {
                        var factura_total = parseFloat($("#subtotal").val());

                        var totales = total_ivav * (codigo / 100);
                        totales = redondeafinal(totales);
                        $(e).parent().parent().find('.m').val(totales);
                        total_ivav = redondeafinal(total_ivav);
                        console.log(totales + " total");
                        $(e).parent().parent().find('.b').val(total_ivav);
                    } else {
                        var totales = total_factura * (codigo / 100);
                        console.log(totales + " total");
                        totales = redondeafinal(totales);
                        total_factura = redondeafinal(total_factura);
                        $(e).parent().parent().find('.m').val(totales);
                        $(e).parent().parent().find('.b').val(total_factura);
                    }
                    suma_totales()
                    /* total_abono()  */
                }
            },
            error: function(data) {
                //console.log(data);
            }
        })
    }

    function changeProveedor(e) {
        var subtotal = $('option:selected', e).data("subtotal");
        var proveedor = $('option:selected', e).data("proveedor");
        var nombre = $('option:selected', e).data("nombre");
        var concepto = $('option:selected', e).data("concepto");
        var iva = $('option:selected', e).data("iva");
        var id = $(e).val();
        var total = $('option:selected', e).data("total");
        var autorizacion = $('option:selected', e).data("autorizacion");
        $('#subtotal').val(subtotal);
        $('#iva').val(iva);
        $('#id_compra').val(id);
        $('#idcompra').val(id);
        $('#id_proveedor').val(proveedor);
        $('#proveedor').val(nombre);
        $('#conceptof').val(concepto);
        $('#concepto').val(concepto);
        $('#autorizacionc').val(autorizacion);
    }

    function lista_valores2(id) {
        var variable_select = $("#tipo_rfiva" + id).val();
        var variable = parseFloat($("#total_factura").val());
        //alert(valor);
        $.ajax({
            type: 'post',
            url: "{{route('retenciones_query2')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'opcion': variable_select
            },
            success: function(data) {
                if (data.value != 'no') {
                    $("#total_rfiva" + id).val(data[0].valor + '%');
                    $("#retencion_iva").val(data[0].valor);
                    var total_enrfiva = parseFloat(data[0].valor) / 100;
                    var retencion_iva = parseFloat($("#base_iva0").val());
                    var asiento_retencion_rfiva = total_enrfiva * retencion_iva;
                    asiento_retencion_rfiva = redondeafinal(asiento_retencion_rfiva);
                    $("#retencion_ivas").val(asiento_retencion_rfiva);
                    total_abono()
                }
            },
            error: function(data) {
                //console.log(data);
            }
        })
    }

    function verificar_secuencia(gordo) {
        var empresa = "{{$empresa->id}}";
        console.log(empresa + "aqui va la empresa");
        var punto_emision = $("#punto_emision").val();
        if (empresa != "" && punto_emision != "") {
            $.ajax({
                type: 'get',
                url: "{{route('verificar_secuencia.contable')}}",
                datatype: 'json',
                data: {
                    'secuencia': gordo.value,
                    'id_empresa': empresa,
                    'punto_emision': punto_emision
                },
                success: function(data) {
                    if (data == 'ok') {
                        $("#punto_final").val(gordo.value);
                    } else {
                        swal("Ya existe registro con ese comprobante");
                        $("#punto_final").val('');
                    }
                },
                error: function(data) {
                    //console.log(data);
                }
            });
        } else {
            swal("ingrese punto de emision primero");
        }
    }

    function obtener_caja() {
        var id_sucursal = $("#sucursal").val();
        $.ajax({
            type: 'post',
            url: "{{route('caja.sucursal')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_sucur': id_sucursal
            },
            success: function(data) {
                if (data.value != 'no') {
                    if (id_sucursal != 0) {
                        $("#pemision").empty();
                        $.each(data, function(key, registro) {
                            var select = '';
                            if (registro.codigo_caja == 100)
                                select = 'selected';
                            $("#pemision").append('<option value=' + registro.codigo_sucursal + '-' + registro.codigo_caja + ' ' + select + '>' + registro.codigo_sucursal + '-' + registro.codigo_caja + '</option>');
                        });
                    } else {
                        $("#pemision").empty();
                    }
                }
            },
            error: function(data) {
                console.log(data);
            }
        })
    }

    function total_abono() {
        var retencion_fuente = parseFloat($("#retencion_fuente").val());
        var retencion_iva = parseFloat($("#retencion_ivas").val());
        var total_retenciones = retencion_fuente + retencion_iva;
        if (total_retenciones != NaN) {
            $("#retencion_totales").val(total_retenciones.toFixed(2));
            $("#nuevo_saldo0").val(total_retenciones.toFixed(2));
        }
    }

    function validarCierre() {
        <?php
        if (isset($min_date) && $min_date != null) {
        ?>
            let fechaAc = new Date();
            var fecha1 = '<?= $min_date; ?>';
            var fecha2 = document.getElementById("fecha").value;
            const fechaInicial = new Date(fecha1);
            const fechaFinal = new Date(fecha2);
            if (fechaFinal <= fechaInicial) {
                alertas("error", "Recuerde!", 'Este registro no se puede modificar, ya que la fecha seleccionada corresponde a un período cerrado contablemente. Para hacer cambios, elija una fecha dentro del período contable vigente.')
                const fechaFormateada = fechaAc.toISOString().split('T')[0];
                document.getElementById("fecha").value = fechaFormateada;
            }
        <?php
        }
        ?>
    }
</script>
@endsection