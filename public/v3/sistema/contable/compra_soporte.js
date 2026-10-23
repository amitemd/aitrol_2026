

function crearTransporte() {
    var id = document.getElementById('contador_items').value;
    let fila = `
        <tr >
             <td style="width:3%;text-align:center">
            </td>
            <td>
                <input type="hidden" name="transporte" value="TRANS"/>
                <input id="codigo${id}" class="form-control validar" type="text"  name="codigo_trans[]" value="TRANS" readonly>
            </td>
            <td>
                <input id="descrip_prod${id}" class="form-control validar" type="text" name="descrip_prod_trans[]" value="TRANS | TRANSPORTE" readonly>
                <textarea  rows="3" name="observacion_trans[]" id="observacion${id}" class="form-control px-1 " placeholder="Observacion"></textarea>
            </td>
            <td>
                <input onchange="totalProducto(${id})" id="cantidad${id}" class="form-control cant validar text-right cneto" type="text"  onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="1" name="cantidad_trans[]" >

            </td>
            <td>
                <input onblur="this.value=parseFloat(this.value).toFixed(2);" onchange="totalProducto(${id});" id="precio${id}" value="0.00" type="text" class="pneto form-control text-right" name="precio_trans[]"  placeholder="0.00">
            </td>

            <td>
                <input onchange="totalProducto(${id})" id="descpor${id}" class="form-control text-right pdesc" type="text"  onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0.0" name="descpor_trans[]" >
                <input onchange="totalProducto(${id})" id="maxdesc${id}" class="form-control text-right maxdesc" type="hidden"  onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0.0" name="maxdesc_trans[]" >
            </td>
            <td>
                <input onchange="totalProducto(${id})" id="desc${id}" class="form-control text-right desc" type="text"  onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0.0" name="desc_trans[]" >
            </td>
            <td>
                <input onchange="totalProducto(${id})" id="precioneto${id}" class="form-control px-1 text-right precioneto" type="text" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto_trans[]" >
            </td>
            <td style="width:3%;text-align:center">
                <input onchange="totalProducto(${id}); select_iva(${id});" class="form" type="checkbox"  name="valoriva_trans[]" id="valoriva${id}" value="1">
                <input type="hidden" name="val_iva_trans[]" id="val_iva${id}" class="form-control px-1 text-right val_iva" >
                <input type="hidden" name="total_valor_trans[]" id="total_valor${id}" class="form-control px-1 text-right total_valor" value="0.00">
                <input class="check_iva" id="check_iva${id}" type="hidden" name="check_iva[]" value="0">

            </td>

            <td>
                 <button type="button" class="btn btn-danger btn-gray delete">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </button>
            </td>
        </tr>
    `

    $('#agregar_cuentas').append(fila);
    $('.select2_cuentas').select2({
        tags: false
    });

    id++;
    document.getElementById('contador_items').value = id;
}
function crearGasto() {
    var id = document.getElementById('contador_items').value;
    let fila = `
        <tr >
            <td style="width:3%;text-align:center">
               
            </td>
            <td>
                <input type="hidden" name="gasto" value="GASTO"/>
                <input id="codigo${id}" class="form-control" type="text" name="codigo_gasto[]" value="" readonly>
            </td>
            <td>
                <select id="id_plan${id}" name="id_plan[]" class="form-control js-data_cuenta validar" style="width: 100%;">
                    <option value=""> Seleccione...</option>
                </select>
                <textarea  rows="3" name="observacion_gasto[]" id="observacion${id}" class="form-control px-1" style="margin-top:3px;" placeholder="Observacion"></textarea>
            </td>
            <td>
                <input onchange="totalProducto(${id})" id="cantidad${id}" class="form-control cant validar text-right" type="text" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="1" name="cantidad_gasto[]" >

            </td>
            <td>
                <input onblur="this.value=parseFloat(this.value).toFixed(2);" onchange="totalProducto(${id});" id="precio${id}" value="0.00" type="text" class="pneto form-control text-right" name="precio_gasto[]" placeholder="0.00">
            </td>

            <td>
                <input onchange="totalProducto(${id})" id="descpor${id}" class="form-control text-right pdesc" type="text" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0.00" name="descpor_gasto[]" >
                <input onchange="totalProducto(${id})" id="maxdesc${id}" class="form-control text-right maxdesc" type="hidden" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0.00" name="maxdesc_gasto[]" >
            </td>
            <td>
                <input onchange="totalProducto(${id})" id="desc${id}" class="form-control text-right desc" type="text" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0.00" name="desc_gasto[]" >
            </td>
            <td>
                <input onchange="totalProducto(${id})" id="precioneto${id}" class="form-control px-1 text-right precioneto" type="text"  onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto_gasto[]" >
            </td>
            <td style="width:3%;text-align:center">
                <input onchange="totalProducto(${id}); select_iva(${id});" class="form" type="checkbox" name="valoriva_gasto[]" id="valoriva${id}" value="1">
                <input type="hidden" name="val_iva_gasto[]" id="val_iva${id}" class="form-control px-1 text-right val_iva" >
                <input type="hidden" name="total_valor_gasto[]" id="total_valor${id}" class="form-control px-1 text-right total_valor" value="0.00">
                <input class="check_iva" id="check_iva${id}" type="hidden" name="check_iva[]" value="0">

            </td>
            <td>
                <button type="button" class="btn btn-danger btn-gray delete">
                   <i class="fa fa-trash" aria-hidden="true"></i>
                </button>
            </td>
        </tr>
    `;

    $('#agregar_cuentas').append(fila);
    callSelect2Cuenta();
    id++;
    document.getElementById('contador_items').value = id;
}

function callSelect2Cuenta() {
    $(".js-data_cuenta")
        .select2({
            minimumInputLength: 3,
            cache: true,
            ajax: {
                url: "v3/" + $("#controlador").val() + "/buscarPlandeCuenta",
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term,
                    };
                },
                processResults: function (data) {
                    console.log(data);
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.nombre,
                                id: item.id,
                            };
                        }),
                    };
                },
            },
            language: {
                inputTooShort: function () {
                    return "Por favor ingrese 3 caracteres....";
                },
            },
        });
}

function totalProducto(id) {
    let cantidad = document.getElementById("cantidad" + id).value;
    let precio = document.getElementById("precio" + id).value;
    let descpor = document.getElementById("descpor" + id).value;
    let desc = document.getElementById("desc" + id)
    let precioneto = document.getElementById("precioneto" + id)
    let val_iva = document.getElementById("val_iva" + id)
    let total_valor = document.getElementById("total_valor" + id)
    let iva_global = document.getElementById("iva_global").value
    let preXcantidad = (precio * cantidad);
    let porcDescuento = (descpor / 100) * preXcantidad;
    let precioTotal = (preXcantidad) - porcDescuento;
    let valoriva = 0;
    if ($('#valoriva' + id).prop('checked')) {
        valoriva = Math.round(precioTotal * (1000 * parseFloat(iva_global)));
        valoriva = valoriva / 1000;
    }
    let total = precioTotal + valoriva;
    val_iva.value = valoriva;
    desc.value = porcDescuento.toFixed(2);
    precioneto.value = precioTotal.toFixed(2);
    total_valor.value = total;
    totalGlobal();
}

function descuento(id) {
    let descpor = document.getElementById("descpor" + id);
    let cantidad = document.getElementById("cantidad" + id).value;
    let precio = document.getElementById("precio" + id).value;
    let desc = document.getElementById("desc" + id).value;
    let preXcantidad = (precio * cantidad);
    let preXcantidadV = 0;
    if (preXcantidad == 0) {
        preXcantidadV = 1;
    } else {
        preXcantidadV = preXcantidad;
    }
    let descuento = (desc * 100) / preXcantidadV;
    descpor.value = descuento.toFixed(4);
    totalProducto(id);
}

function select_iva(id) {
    let check = document.getElementById("valoriva" + id);
    let check_af = document.getElementById("check_iva" + id);

    if (check.checked) {
        check_af.value = 1;
    } else {
        check_af.value = 0;
    }
    totalProducto(id);
}

function sumarDias(fecha, dias) {
    fecha.setDate(fecha.getDate() + dias + 1);
    return fecha;
}

function fecha_vencimiento(e) {
    const d = new Date(document.getElementById("fecha_compra").value);
    let f_caducidad = document.getElementById("fecha_caduca");
    let day = e.options[e.selectedIndex].text;
    const dayArray = day.split(" ");
    let fechaNew = "";
    if (parseInt(dayArray[0]) >= 0) {
        fechaNew = sumarDias(d, +dayArray[0])
    } else if (dayArray[0].toUpperCase() == "CONTADO") {
        fechaNew = sumarDias(d, +0)
    } else {
        f_caducidad.readOnly = false;
        return;
    }
    var fechaImp = fechaNew.getFullYear() + '-' + ('0' + (fechaNew.getMonth() + 1)).slice(-2) + '-' + ('0' + fechaNew.getDate()).slice(-2);
    f_caducidad.value = fechaImp;
    f_caducidad.readOnly = true;
    return;
}

$("#sucursal_empresa").on("change", function () {
    let sucursal = $(this).val();
    $.ajax({
        type: "post",
        url: "v3/" + $("#controlador").val() + "/obtenerPuntoEmision",
        headers: {
            "X-CSRF-TOKEN": $("input[name=_token]").val(),
        },
        datatype: "json",
        data: {
            id: sucursal,
        },
        success: function (data) {
            if (sucursal != 0) {
                $("#punto_emision").empty();

                $.each(data, function (key, registro) {
                    var select = "";
                    if (registro.codigo_caja == "100") select = "selected";
                    $("#punto_emision").append(
                        "<option value=" +
                        registro.id +
                        " " +
                        select +
                        ">" +
                        registro.codigo_sucursal +
                        "-" +
                        registro.codigo_caja +
                        "</option>"
                    );
                });
            } else {
                $("#punto_emision").empty();
            }
        },
        error: function (data) { },
    });
});

function validarFechasPeriodo() {
    let periodo_mes = document.getElementById("periodo_mes").value;
    let periodo_anio = document.getElementById("periodo_anio").value;
    let f_autorizacion = document.getElementById("fecha_compra").value;
    const fechArray = f_autorizacion.split("-");
    let fechAuto = `${fechArray[0]}-${fechArray[1]}`;
    let fechPeriodo = `${periodo_anio}-${periodo_mes}`;
    console.log(fechAuto,fechPeriodo,fechArray[0]);
    if (fechAuto == fechPeriodo) {
        return true;
    } else {
        return false;
    }
}

$("#btnGuardarIngresoActivos").on("click", function () {
    let tbody = document.getElementById('agregar_cuentas');
    let filas = tbody.rows.length;
    if (document.getElementById("id_proveedor").value == '') {
        return mensajes("warning", "Ingrese el proveedor");
    }
    if (document.getElementById("divisas").value == '') {
        return mensajes("warning", "Ingrese la divisa");
    }
    if (document.getElementById("termino").value == '') {
        return mensajes("warning", "Ingrese el termino");
    }
    if (document.getElementById("cred_tributario").value == '') {
        return mensajes("warning", "Ingrese el crédito tributario");
    }
    if (document.getElementById("nro_autorizacion").value == '') {
        return mensajes("warning", "Ingrese el número autorización");
    }
    if (document.getElementById("secuencia").value == '') {
        return mensajes("warning", "Ingrese la secuencia");
    }
    if (document.getElementById("tipo_comprobante").value == '') {
        return mensajes("warning", "Ingrese el tipo comprobante");
    }
    if (document.getElementById("concepto").value == '') {
        return mensajes("warning", "Ingrese el concepto");
    }
    if (filas < 0) {
        return mensajes("error", "Ingrese los detalles");
    }
    console.log($('#nro_autorizacion').val().length);
    if ($('#nro_autorizacion').val() != '') {
        if ($('#nro_autorizacion').val().length != 10 && $('#nro_autorizacion').val().length != 49) {
            return mensajes('warning','Por favor, El número de autorización debe contener 10 o 49 dígitos');
        }
    }
    if (!validarFechasPeriodo()) {
        return mensajes("warning", "La fecha del periodo con la fecha de facturacion no coinciden");
    }
    mensajes("question", $("#pregunta").val(), "frm_guardarCompraActivo", "documentoFacturaBuscarOrden");
});