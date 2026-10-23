@extends('contable.retenciones.base')
@section('action-content')
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
        location.href = "{{ route('retencion.cliente') }}";
    }
</script>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="#">Retenciones Cliente</a></li>
            <li class="breadcrumb-item"><a href="{{ route('retenciones_index') }}">Retenciones</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nueva Retención</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-9 col-sm-9 col-6">
                        <div class="box-title "><b>CLIENTE-COMP. DE RETENCIONES</b></div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
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
                            <label class="label_header">Buscador</label>
                            <select style="width: 100%;" class="form-control select2" onchange="changeProveedor(this)" name="acreedores" id="acreedores">
                                <option value="">Seleccione...</option>
                                @foreach($ventas as $c)
                                @php
                                $x= Sis_medico\Ct_Cliente_Retencion::where('id_factura',$c->id)->orderBy('id','DESC')->first();
                                $subtotal= $c->subtotal_0 + $c->subtotal_12 - $c->descuento;
                                @endphp
                                @if(is_null($x))
                                <option data-subtotal="{{$subtotal}}" data-cliente="{{$c->cliente->identificacion}}" data-iva="{{$c->impuesto}}" data-total="{{$c->total_final}}" data-concepto="{{$c->concepto}}" data-nombre="{{$c->cliente->nombre}}" data-autorizacion="{{$c->nro_autorizacion}}" value="{{$c->id}}">#: {{$c->id}} P: {{$c->cliente->nombre}} - {{$c->nro_comprobante}} | {{$c->concepto}} </option>
                                @else
                                @if($x->estado==0)
                                <option data-subtotal="{{$subtotal}}" data-cliente="{{$c->cliente->identificacion}}" data-iva="{{$c->impuesto}}" data-total="{{$c->total_final}}" data-concepto="{{$c->concepto}}" data-nombre="{{$c->cliente->nombre}}" data-autorizacion="{{$c->nro_autorizacion}}" value="{{$c->id}}">#: {{$c->id}} P: {{$c->cliente->nombre}} - {{$c->nro_comprobante}} | {{$c->concepto}} </option>
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
                            <input type="text" class="form-control" id="idventa" readonly>
                        </div>
                        <div class="col-md-4 px-1">
                            <label class="label_header">Cliente</label>
                            <input type="text" class="form-control" name="cliente" id="cliente" readonly>
                            <input type="hidden" name="id_cliente" id="id_cliente">
                            <input type="hidden" name="id_venta" id="id_venta">
                            <input type="hidden" name="subtotal" id="subtotal">
                            <input type="hidden" name="iva" id="iva">
                        </div>
                        <div class="col-md-4 px-1">
                            <label class="label_header">Concepto</label>
                            <input type="text" class="form-control" name="conceptof" id="conceptof" readonly>
                        </div>
                        <div class="col-md-3 px-1">
                            <label class="label_header">Autorizacion</label>
                            <input type="text" class="form-control" name="autorizacionc" id="autorizacionc" readonly>
                        </div>
                        <div class="col-md-12 px-1" style="margin-top: 10px;">
                            <label class="label label-default"> DATOS DE RETENCION </label>
                        </div>

                        <div class="col-md-3 px-1">
                            <label class="label_header">Fecha</label>
                            <input type="text" class="form-control" name="fecha" id="fecha" value="{{date('Y-m-d')}}" required>
                        </div>
                        <div class="col-md-6 px-1">
                            <label class="label_header">Concepto</label>
                            <input type="text" class="form-control" name="concepto" autocomplete="off" id="concepto" required placeholder="Concepto Retencion">
                        </div>
                        <div class="col-md-3 px-1">
                            <label class="label_header">Autorizacion</label>
                            <input type="text" class="form-control" autocomplete="off" name="autorizacion" id="autorizacion" required placeholder="Autorizacion Retención">
                        </div>
                        <div class="col-md-4 px-1">
                            <label class="label_header">Sucursal</label>
                            <input type="text" name="sucursal" class="form-control" autocomplete="off" maxlength="3" required placeholder="Sucursal">
                        </div>
                        <div class="col-md-4 px-1">
                            <label class="label_header">Punto Emisión</label>
                            <input type="text" name="pemision" class="form-control" autocomplete="off" maxlength="3" required placeholder="Punto de Emision">
                        </div>
                        <div class="col-md-4 px-1">
                            <label class="label_header">Secuencial</label>
                            <input type="text" class="form-control" name="secuencial" autocomplete="off" maxlength="9" required id="secuencial" placeholder="Ingrese Secuencial">
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
                                    <th>Tipo Retención</th>
                                    <th>Porcentaje Retencion</th>
                                    <th>Codigo</th>
                                    <th>Base de Retención</th>
                                    <th>%</th>
                                    <th>Monto</th>
                                    <th><button type="button" class="btn btn-primary btn-gray" onclick="add()"> <i class="fa fa-plus"></i> </button></th>
                                </tr>
                            </thead>
                            <tbody id="agregar_cuentas">
                                <tr id="mifila" style="display: none;">
                                    <td><select name="tipo_retencion[]" class="form-control select2 tretencion" onchange="traer_retenciones(this)" style="width: 90%; height: 90%;" required>
                                            <option value="">Seleccione...</option>
                                            <option value="2"> RENTA </option>
                                            <option value="1">IVA</option>
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
                                            <option value="2"> RENTA </option>
                                            <option value="1">IVA</option>
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
                    <label class="label_header"> RENTA </label>
                    <input type="text" class="form-control" id="valor_renta" name="valor_renta" value="0.00" readonly>
                </div>
                <div class="col-md-4 px-1">
                    <label class="label_header"> IVA </label>
                    <input type="text" class="form-control" id="valor_iva" name="valor_iva" value="0.00" readonly>
                    <input type="hidden" name="total" id="total" value="0.00">
                </div>
                <div class="col-md-4 px-1">
                    <label class="label_header"> TOTAL </label>
                    <input type="text" class="form-control" name="totalr" id="totalr" value="0.00" readonly>

                </div>
            </form>
        </div>

    </div>

    </div>


</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>


<script type="text/javascript">
    var fila = $("#mifila").html();
    $(function() {
        $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: "{{date('Y-m-d')}}",
        });

    });

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

    //var input = document.getElementById('toggleswitch');
    //var outputtext = document.getElementById('status');

    /*   input.addEventListener('change', function() {
          if (this.checked) {
              $("#electronica").val(1);
              $('#punto_final').attr('readonly', true);
              //$('#numero').attr('readonly', true);
          } else {
              $("#electronica").val(0);
              $('#punto_final').attr('readonly', false);
              //$('#numero').attr('readonly', false);
          }
      }); */
    /*     var input2= document.getElementById('toggleswitch2');
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
        }); */
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
                    //alert("dasda");

                    $(e).parent().parent().find('.porcentaje').empty();
                    $(e).parent().parent().find('.porcentaje').append('<option value="0">Seleccione...</option>');
                    $.each(data, function(key, registro) {
                        $(e).parent().parent().find('.porcentaje').append('<option value=' + registro.id + '>' + registro.codigo + ' - ' + registro.nombre + '</option>');
                    });
                } else {
                    $(e).parent().parent().find('.porcentaje').empty();
                }
                // console.log(data);  
                //swal("Correcto!","Retencion guardada correctamente","success");

            },
            error: function(data) {
                //console.log(data);
            }


        });
    }

    function guardar_retenciones() {
        if ($('#autorizacion').val() != '') {
            if ($('#autorizacion').val().length != 10 && $('#autorizacion').val().length != 49) {
                swal("Advertencia", 'El número de autorización de la retención debe tener 10 o 49 dígitos', "error");
                return;
            }
        } else {
            swal("Advertencia", 'Ingrese el número de autorización de la retención del documento', "error");
            return;
        }
        if ($('#form_guardado').valid()) {
            $("#boton_guardar").attr("disabled", "disabled");
            $.ajax({
                type: 'post',
                url: "{{route('retenciones.clientes.newstore')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $('#form_guardado').serialize(),
                success: function(data) {
                    //console.log(data);
                    if (data.error == 'no') {
                        $("#boton_guardar").attr("disabled", "disabled");
                        swal("Correcto!", "Retencion guardada correctamente", "success");
                        //url = "{{ url('contable/compra/comprobante/retenciones/')}}/" + data.id;
                        //window.open(url, '_blank');
                        $('#form_guardado input').attr('readonly', 'readonly');


                    } else {
                        swal("Advertencia", data.error, "warning");
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
        var proveedor = $('option:selected', e).data("cliente");
        var nombre = $('option:selected', e).data("nombre");
        var concepto = $('option:selected', e).data("concepto");
        if (concepto == '') {
            concepto = 'FC-' + $(e).val() + ' ' + nombre;
        }
        var iva = $('option:selected', e).data("iva");
        var id = $(e).val();
        var total = $('option:selected', e).data("total");
        var autorizacion = $('option:selected', e).data("autorizacion");
        $('#subtotal').val(subtotal);
        $('#iva').val(iva);
        $('#id_venta').val(id);
        $('#idventa').val(id);
        $('#id_cliente').val(proveedor);
        $('#cliente').val(nombre);
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
                //console.log(data);

                if (data.value != 'no') {
                    if (id_sucursal != 0) {
                        $("#pemision").empty();

                        $.each(data, function(key, registro) {
                            $("#pemision").append('<option value=' + registro.codigo_sucursal + '-' + registro.codigo_caja + '>' + registro.codigo_sucursal + '-' + registro.codigo_caja + '</option>');

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
</script>


@endsection