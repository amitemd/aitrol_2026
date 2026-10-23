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
<div class="modal-content" style="width: 100%;">
    <div class="modal-header" style="padding-top: 5px; padding-bottom: 1px;">
        <div class="col-md-5">
            <h4 class="modal-title">Detalle de Retenciones</h4>
        </div>
        <div class="col-md-7">
            <button type="button" id="cerrar" onclick="cerrar()" class="close" data-dismiss="modal">&times;</button>
        </div>
    </div>
    <div class="modal-body size_text">
        <form action="" id="form_guardado" method="post">
            <input type="hidden" value="{{$id_empresa}}" id="id_empresa" name="id_empresa">
            <input type="hidden" value="{{$ivatotal}}" id="iva_final" name="iva_final">
            <input type="text" name="cont" id="cont" value="1" class="hidden">
            <div class="col-md-6">
                <div class="row header">
                    <div class="col-md-12">
                        <label class="control-label label_header">DATOS DE LA FACTURA</label>
                    </div>
                    <div class="col-md-6">
                        <label class="control-label">SECUENCIAL</label>
                        <input type="text" class="form-control input-sm " name="secuencial" name="secuencial" value="{{$secuencia_factura}}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="control-label">N° AUTORIZACIÓN RETENCION</label>
                        <input type="text" class="form-control input-sm " name="nro_autorizacion" id="nro_autorizacion">
                    </div>
                    <div class="col-md-6">
                        <label class="control-label">N° VALOR DE LA FACTURA </label>
                        <input type="text" class="form-control input-sm " name="valor_factura" id="valor_factura" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="control-label">PROVEEDOR</label>
                        <input type="text" class="form-control input-sm " name="proveedor_modal" id="proveedor_modal" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="control-label">Sucursal</label>
                        <select class="form-control" name="sucursal" id="sucursal" onchange="obtener_caja()" required>
                            <option value="">Seleccione...</option>
                            @foreach($sucursales as $value)
                            <option value="{{$value->id}}">{{$value->codigo_sucursal}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="punto_emision" class="control-label">Punto de Emision</label>
                        <select class="form-control" name="punto_emision" id="punto_emision" required>
                            <option value="">Seleccione...</option>
                        </select>

                    </div>
                    <div class="col-md-4">
                        <label for="fecha_retencion" class="control-label">Fecha</label>
                        <input class="form-control" type="date" value="{{date('Y-m-d')}}" name="fecha_retencion" id="fecha_retencion" required>
                    </div>
                </div>
                <div class="col-md-6" style="padding: 0px;">
                    <div class="col-md-12 px-0">
                        <label for="empresa" class="label_header" style="background-color:transparent!important;color: black;text-align: left;">Electrónica</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <label class="switch">
                            <input class="electros" @if($empresa_sucurs->electronica==1)  @else disabled @endif id="toggleswitch" type="checkbox">
                            <span class="slider round"></span>
                            <input type="hidden" id="electronica" name="electronica" value="0">
                        </label>
                    </div>
                </div>

            </div>
            <div class="col-md-6" style="left: 3px;">
                <div class="row header">
                    <div class="col-md-12">
                        <label class="control-label label_header">DATOS DE LA RETENCIÓN</label>
                    </div>
                    <input class="form-control" type="hidden" name="id_proveedor" id="id_proveedor" value="{{$proveedor->id_proveedor}}">
                    <input class="form-control" type="hidden" name="id_compra" id="id_compra" value="@if(!is_null($id_compra)){{$id_compra}}@endif">
                    <div class="col-md-6">
                        <label class="control-label">Tipo Retención</label>
                        <select class="form-control input-sm" onchange="traer_retenciones()" name="tipo_retencion" id="tipo_retencion">
                            <option value="0">Seleccione...</option>
                            <option value="2">FUENTE</option>
                            <option value="1">IVA</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="control-label">PORCENTAJE RETENCIÓN</label>
                        <select class="form-control input-sm" name="porcentaje_retencionf" onchange="lista_valores(this)" id="porcentaje_retencionf">
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="control-label">CÓDIGO</label>
                        <input type="text" class="form-control input-sm" name="codigo" id="codigo" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="control-label">BASE RETENCIÓN</label>
                        <input class="form-control input-sm" type="text" name="base_retencion" id="base_retencion" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
                    </div>
                    <div class="col-md-6">
                        <label class="control-label">MONTO RETENIDO</label>
                        <input type="text" id="monto_retenido" class="form-control input-sm" name="monto_retenido" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
                        <input type="hidden" name="id_proveedor_modal" id="id_proveedor_modal" value="{{$proveedor->id_proveedor}}">
                        <input type="hidden" name="retencion_total" id="retencion_total">
                    </div>
                    <div class="col-md-6">
                        <label class="control-label" name="punto_final">SECUENCIAL FISICO</label>
                        <input type="number" onchange="verificar_secuencia(this)" id="punto_final" class="form-control input-sm" name="punto_final">
                    </div>


                    <div class="col-m-12">
                        <label class="control-label">Concepto:</label>
                        <input type="text" class="form-control input-sm" name="concepto" id="concepto">
                        <input type="hidden" name="valor_fuente" id="valor_fuente">
                        <input type="hidden" name="valor_iva" id="valor_iva">
                        <input type="hidden" name="tipo_rfir" id="tipo_rfir">
                        <input type="hidden" name="tipo_rfiva" id="tipo_rfiva">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="table-responsive col-md-12" style="top: 20px;">
                    <table id="example2" role="grid" aria-describedby="example2_info">
                        <thead class='well-dark'>
                            <tr style="position: relative;">
                                <th style="width: 22%; text-align: center;">BASE IMP RET</th>
                                <th style="width: 5%; text-align: center;">TIPO</th>
                                <th style="width: 5%; text-align: center;">COD</th>
                                <th style="width: 5%; text-align: center;">% DE RET</th>
                                <th style="width: 8%; text-align: center;">VALOR RETENIDO</th>
                                <th style="width: 8%; text-align: center;">ACCIÓN</th>
                            </tr>
                        </thead>
                        <tbody id="datos_a">
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                </div>
            </div>

            <input type="hidden" name="cuenta_renta" id="cuenta_renta">
            <input type="hidden" name="cuenta_iva" id="cuenta_iva">
            <input type="hidden" name="empresa_modal" id="empresa_modal">
            <input type="hidden" name="sucursal_modal" id="sucursal_modal">
            <input type="hidden" name="punto_emision_modal" id="punto_emision_modal">
            <input type="hidden" name="eliminados" id="eliminados" value="0">
        </form>
    </div>
    <div class="modal-footer">
        <div class="row">
            <div class="col-md-3" style="margin-top: 30px;">
                <a href="javascript:crea_tds();" class="btn btn-success btn-xs btn-gray" id="agregar_item">Nuevo</a>
            </div>
            <div class="col-md-6">
                &nbsp;
            </div>
            <div class="col-md-3" style="margin-top: 30px;">
                <a class="btn btn-primary btn-xs btn-gray" id="retener" href="javascript:guardar_retenciones();">INGRESAR</a>
            </div>
        </div>

    </div>
</div>
<script type="text/javascript">
    
        var input = document.getElementById('toggleswitch');
        let electronica = document.getElementById('electronica').value;
    

        input.addEventListener('change', function() {
            if (this.checked) {
               $("#electronica").val(1);
            } else {
               $("#electronica").val(0);
               
            }
        });
    
    
</script>
<script type="text/javascript">
    $(document).ready(function() {
        var total_factura = parseFloat($("#base1").val());
        var descuento = parseFloat($("#descuento1").val());
        var total_ivav = parseFloat($("#tarifa_iva1").val());
        var nombre_proveedor = $("#proveedor").val();
        var id_proveedor = $("#proveedor").val();
        var surcursal = $("#sucursal_final").val();
        var punto_emision = $("#punto_emision").val();
        var empresa = $("#id_empresa").val();
        if (descuento > 0) {
            total_factura = total_factura - descuento;
        }
        $("#id_proveedor_modal").val(id_proveedor);
        $("#proveedor_modal").val(nombre_proveedor);
        $("#valor_factura").val(total_factura);
        $("#empresa_modal").val(empresa);
        $("#sucursal_modal").val(surcursal);
        $("#punto_emision_modal").val(punto_emision);

    });

    function verificar_secuencia(gordo) {
        var empresa = $("#id_empresa").val();
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
    function cerrar() {
        if (confirm('¿Desea salir sin guardar las retenciones?')) {
            location.href = "{{route('compras_index')}}";
        } else {
            $('#calendarModal').modal().hide();
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
                var cond = '-';
                if (data.value != 'no') {
                    if (id_sucursal != 0) {
                        $("#punto_emision").empty();

                        $.each(data, function(key, registro) {
                            $("#punto_emision").append('<option value=' + registro.codigo_sucursal + cond + registro.codigo_caja + '>' + registro.codigo_sucursal + '-' + registro.codigo_caja + '</option>');

                        });
                    } else {
                        $("#punto_emision").empty();

                    }

                }
            },
            error: function(data) {
                console.log(data);
            }
        })

    }

    function validar_vacios() {
        var retencion_fuente = $("#retencion_fuente").val();
        var retencion_ivas = $("#retencion_ivas").val();
        var retencion_totales = $("#retencion_totales").val();
        var retencion_iva = $("#retencion_impuesto").val();
        var nuevo_saldo0 = $("#nuevo_saldo0").val();
        var id_compra = $("#id_compra").val();
        var id_proveedor = $("#id_proveedor").val();
        if (retencion_fuente != "" && retencion_ivas != "" && retencion_totales != "" && retencion_iva != "" && nuevo_saldo0 != "" && id_compra != "" && id_proveedor != "") {
            return 'ok';
        }
        return 'no';
    }

    function traer_retenciones() {
        //retenciones.buscartipo
        var id = $("#tipo_retencion").val();
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

                    $("#porcentaje_retencionf").empty();
                    $("#porcentaje_retencionf").append('<option value="0">Seleccione...</option>');
                    $.each(data, function(key, registro) {
                        $("#porcentaje_retencionf").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
                    });
                } else {
                    $("#porcentaje_retencionf").empty();
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
        var tipo_rfir = $("#tipo_rfir").val();
        var tipo_rfiva = $("#tipo_rfiva").val();
        var validacion = validar_vacios();
        //alert(validacion);
        //validaciones
        var formulario = document.forms["form_guardado"];
        var porcentaje_retencion = formulario.porcentaje_retencionf.value;
        var proveedor = formulario.proveedor_modal.value;
        var secuencia = formulario.secuencial.value;
        var no_autorizacion = formulario.nro_autorizacion.value;
        var no_factura = formulario.valor_factura.value;
        var valor_fuente = formulario.valor_fuente.value;
        var valor_iva = formulario.valor_iva.value;
        var cuenta_renta = formulario.cuenta_renta.value;
        var cuenta_iva = formulario.cuenta_iva.value;
        var sucursal = formulario.sucursal.value;
        var emision = formulario.punto_emision.value;
        var punto_final = formulario.punto_final.value;
        var msj = "";
        if (porcentaje_retencion == "") {
            msj += "Por favor, Porcentaje de retención \n";
        }
        if (proveedor == "") {
            msj += "No existe proveedor\n";
        }
        if (secuencia == "") {
            msj += "Por favor, Llene el campo de secuencia\n";
        }
        if (no_autorizacion == "") {
            msj += "Por favor, Falta la autorización\n";
        }
        if (no_factura == "") {
            msj += "Por favor, Llene el numero de la factura\n";
        }
        if (valor_fuente == "") {
            msj += "Por favor, Llene el valor de la fuente\n";
        }
        if (valor_iva == "") {
            msj += "Por favor, Llene el valor del IVA\n";
        }
        if (emision == "") {
            msj += "Por favor, Llene el campo de emision\n";
        }
        if (punto_final == "") {
            msj += "Por favor, Llene el campo del secuencial\n";
        }
        if (sucursal == "") {
            msj += "Por favor, Llene el campo sucursal\n";
        }
        if (msj == "") {
            $("#retener").attr("disabled", "disabled");
            $.ajax({
                type: 'post',
                url: "{{route('retenciones_store')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $('#form_guardado').serialize(),
                success: function(data) {
                    if(data.status == 'success'){
                        swal("Correcto!", "Retencion guardada correctamente", "success");
                        url = "{{ url('contable/compra/comprobante/retenciones/')}}/" + data.id;
                        window.open(url, '_blank');
                        $('#calendarModal').modal().hide();
                        $('body').removeClass('modal-open');
                        $('.modal-backdrop').remove();
                    }
                    else{
                        swal("¡Error!", data.msj, "error");
                    }
                },
                error: function(data) {
                    //console.log(data);
                }
            })
        } else {
            alert(msj);

        }
    }
    function lista_valores(id) {

        var variable_select = $("#porcentaje_retencionf").val();
        var tipo = $("#tipo_retencion").val();
        var total_factura = $("#base1").val();
        var total_ivav = parseFloat($("#iva_final").val());
        //alert(total_ivav);
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
                if (data.value != 'no') {
                    $("#codigo").val(data[0].codigo);
                    var codigo = parseFloat(data[0].valor);
                    //1 es iva 2 fuente
                    if (tipo == '1') {
                        var factura_total = parseFloat($("#subtotal_final").val());
                        var totales = total_ivav * (codigo / 100);
                        $("#monto_retenido").val(totales.toFixed(2, 2));
                        $("#base_retencion").val(total_ivav);
                    } else {
                        var totales = total_factura * (codigo / 100);
                        $("#monto_retenido").val(totales.toFixed(2, 2));
                        $("#base_retencion").val(total_factura);
                    }

                    /* total_abono()  */
                }
            },
            error: function(data) {
                //console.log(data);
            }
        })
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
                //alert(data[0].nombre);
                //console.log(data);
                if (data.value != 'no') {
                    $("#total_rfiva" + id).val(data[0].valor + '%');
                    $("#retencion_iva").val(data[0].valor);
                    var total_enrfiva = parseFloat(data[0].valor) / 100;
                    var retencion_iva = parseFloat($("#base_iva0").val());
                    var asiento_retencion_rfiva = total_enrfiva * retencion_iva;
                    $("#retencion_ivas").val(asiento_retencion_rfiva.toFixed(2));
                    total_abono()
                }
            },
            error: function(data) {
                //console.log(data);
            }
        })
    }

    function total_abono() {
        var retencion_fuente = parseFloat($ < ("#retencion_fuente").val());
        var retencion_iva = parseFloat($("#retencion_ivas").val());
        var total_retenciones = retencion_fuente + retencion_iva;
        if (total_retenciones != NaN) {
            $("#retencion_totales").val(total_retenciones.toFixed(2));
            $("#nuevo_saldo0").val(total_retenciones.toFixed(2));
        }
    }

    function crea_tds() {
        id = document.getElementById('cont').value;
        var tipo = $("#tipo_retencion").val();
        var total_factura = $("#base_retencion").val();
        var codigo = $("#codigo").val();
        var valor_retenido = $("#monto_retenido").val();
        var porcentaje = $("#porcentaje_retencionf").val();
        var total = $("#total_final").val();
        var eliminados = parseInt($("#eliminados").val());
        var total_final = 0;
        var conter = 0;
        var contaiva = 0;
        var cuenta_retenta = parseInt($("#cuenta_renta").val());
        if (isNaN(cuenta_retenta)) {
            cuenta_retenta = 0;
        }
        //alert(cuenta_retenta);
        var cuenta_iva = parseInt($("#cuenta_iva").val());
        var formulario = document.forms["form_guardado"];
        var tipo_retencion = formulario.tipo_retencion.value;
        var valor_factura = formulario.valor_factura.value;
        if (isNaN(cuenta_iva)) {
            cuenta_iva = 0;
        }
        if (tipo_retencion != "" && valor_factura != "") {
            var midiv = document.createElement("tr");
            if (tipo == '2') {
                tipo = 'RENTA';
                conter = cuenta_retenta + 1;
                if (conter <= 2 && conter > 0) {
                    $("#cuenta_renta").val(conter);
                } else {
                    $("#cuenta_renta").val(2);
                }


            } else {
                tipo = 'IVA';
                contaiva = cuenta_iva + 1;
                if (contaiva <= 1 && contaiva > 0) {
                    $("#cuenta_iva").val(contaiva);
                } else {
                    $("#cuenta_iva").val(1);
                }
            }
            //alert(contaiva);
            if (contaiva <= 1 && conter <= 2) {
                midiv.setAttribute("id", "dato" + id);
                midiv.innerHTML = '<td><input class="form-control input-sm" name="base_imp' + id + '" id="base_imp' + id + '"readonly></td> <td><input class="form-control input-sm" name="tipor' + id + '" id="tipor' + id + '" value="' + tipo + '" readonly></td> <td> <input class="form-control input-sm" name="codigor' + id + '" id="codigor' + id + '" readonly ></td> <td> <input class="form-control input-sm" name="porcentaje_retencion' + id + '" id="porcentaje_retencion' + id + '" readonly></td><td> <input class="form-control input-sm" name="valor_retenido' + id + '" id="valor_retenido' + id + '" readonly></td> <input type="hidden" name="id_porcentaje' + id + '" id="id_porcentaje' + id + '"> <input type="hidden" name="porcentaje' + id + '" id="porcentaje' + id + '"> <td><a id="eliminar_modal' + id + '" type="button" href="javascript:eliminar_registros(' + id + ')" class="btn btn-danger btn-gray btn-xs col-md-6">Eliminar</a></td> <input type="hidden" name="tipo_p' + id + '" id="tipo_p' + id + '"> ';
                document.getElementById('datos_a').appendChild(midiv);
                id = parseInt(id);
                //alert(codigo);
                $("#codigor" + id).val(codigo);
                $("#valor_retenido" + id).val(valor_retenido);
                $("#porcentaje_retencion" + id).val(porcentaje);
                $("#base_imp" + id).val(total_factura);
                $("#id_porcentaje" + id).val(porcentaje);
                $("#tipo_p" + id).val(tipo);
                $("#porcentaje" + id).val(codigo)
                id = id + 1;
                document.getElementById('cont').value = id;
                suma_seccion(id);
            } else {
                $("#agregar_item").attr("disabled", true);
                //alert("si funciona");
            }

        } else {
            swal("¡Error!", "Ingresa primero los datos", "error");
        }

    }

    function eliminar_registros(valor) {
        var dato1 = "dato" + valor;
        var total;
        var contador_verdadero = document.getElementById('cont').value;
        var contador = parseInt(contador_verdadero);
        var cuenta_retenta = parseInt($("#cuenta_renta").val());
        var cuenta_referencia = $("#tipor" + valor).val();
        var total_renta = 0;
        var total_iva = 0;
        if (isNaN(cuenta_retenta)) {
            cuenta_retenta = 0;
        }
        var cuenta_iva = parseInt($("#cuenta_iva").val());
        if (isNaN(cuenta_iva)) {
            cuenta_iva = 0;
        }
        if (contador_verdadero > 1) {
            total = valor;
        } else {
            total = 1;
        }
        if (cuenta_referencia != 'RENTA') {
            if (cuenta_iva > 0 && cuenta_iva <= 2) {
                total_iva = cuenta_iva - 1;
                $("#cuenta_iva").val(total_iva);
            }
        } else {
            if (cuenta_retenta > 0 && cuenta_retenta <= 2) {
                total_renta = cuenta_retenta - 1;
                $("#cuenta_renta").val(total_renta);
            }

        }
        document.getElementById('cont').value = total;
        $("#dato" + valor).remove();
        var valor_en = parseInt(valor);
        $("#eliminados").val(1);
        suma_seccion();
    }

    function suma_seccion(cont) {
        var tipo = parseFloat($("#tipo_retencion").val());
        //alert(tipo);
        var contador = parseInt($("#cont").val());
        //alert(contador);
        var sumador = 0;
        var sumador2 = 0;
        for (i = 1; i < contador; i++) {
            var totales = parseFloat($("#valor_retenido" + i).val());
            var tipo = $("#tipor" + i).val();
            if (tipo == 'RENTA') {
                if ((totales) != NaN) {
                    sumador += totales;
                    //alert(totales)
                } else {
                    sumador = 0;
                }
            } else if (tipo == 'IVA') {
                if ((totales) != NaN) {
                    sumador2 += totales;
                    //alert(totales)
                } else {
                    sumador2 = 0;
                }
            }
            //alert(sumador2);
        }
        $("#valor_iva").val(sumador2.toFixed(2));
        $("#valor_fuente").val(sumador.toFixed(2));
        var total = parseFloat(sumador2) + parseFloat(sumador);
        $("#retencion_total").val(total);

    }
</script>