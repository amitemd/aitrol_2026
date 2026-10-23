/**
 * SISTEMA DE PRÉSTAMOS Y AMORTIZACIONES - FUNCIONES JAVASCRIPT
 * Archivo: functions_prestamos.js
 * Descripción: Manejo de AJAX y lógica del lado del cliente para módulo de préstamos
 * Fecha: 2026-03-03
 */

/**
 * Calcular amortización mediante AJAX
 * Genera la proyección de cuotas en tiempo real
 */
function calcularAmortizacion() {
    // Validar campos
    const monto = parseFloat($('#monto').val());
    const tasa = parseFloat($('#tasa').val());
    const plazo = parseInt($('#plazo').val());
    const tipo_amortizacion = $('#tipo_amortizacion').val();
    const fecha_desembolso = $('#fecha_desembolso').val();

    if (!monto || !tasa || !plazo || !tipo_amortizacion || !fecha_desembolso) {
        alert('Por favor, complete todos los campos requeridos.');
        return;
    }

    // Llamar al controlador mediante AJAX
    $.ajax({
        url: '?controlador=prestamos&submodulo=calcularAmortizacion',
        type: 'GET',
        data: {
            monto: monto,
            tasa: tasa,
            plazo: plazo,
            tipo_amortizacion: tipo_amortizacion,
            fecha_desembolso: fecha_desembolso
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                renderizarTablaAmortizacion(response.cuotas, response.total_interes);
                $('#divTablaAmortizacion').show();
            } else {
                alert('Error: ' + response.error);
            }
        },
        error: function() {
            alert('Error en la comunicación con el servidor');
        }
    });
}

/**
 * Renderizar tabla de amortización en el DOM
 */
function renderizarTablaAmortizacion(cuotas, totalInteres) {
    let html = '';
    
    cuotas.forEach(function(cuota) {
        html += '<tr>';
        html += '<td><strong>#' + cuota.numero_cuota + '</strong></td>';
        html += '<td style="text-align:right">' + cuota.fecha_vencimiento + '</td>';
        html += '<td style="text-align:right">$' + formatearNumero(cuota.saldo_inicial) + '</td>';
        html += '<td style="text-align:right">$' + formatearNumero(cuota.capital_amortizado) + '</td>';
        html += '<td style="text-align:right">$' + formatearNumero(cuota.interes) + '</td>';
        html += '<td style="text-align:right"><strong>$' + formatearNumero(cuota.cuota_total) + '</strong></td>';
        html += '<td style="text-align:right">$' + formatearNumero(cuota.saldo_final) + '</td>';
        html += '</tr>';
    });

    $('#bodyTablaAmortizacion').html(html);
    $('#totalInteres').text(formatearNumero(parseFloat(totalInteres.replace(/,/g, '.'))));
}

/**
 * Guardar nuevo préstamo en la base de datos
 */
function guardarPrestamo() {
    // Validar que la tabla de amortización haya sido calculada
    if ($('#bodyTablaAmortizacion').html() === '') {
        alert('Por favor, primero calcule la amortización');
        return;
    }

    // Validar campos obligatorios
    const tipo_prestamo = $('#tipo_prestamo').val();
    const monto = parseFloat($('#monto').val());
    const tasa = parseFloat($('#tasa').val());
    const plazo = parseInt($('#plazo').val());

    if (!tipo_prestamo) {
        alert('Seleccione el tipo de préstamo');
        return;
    }

    if (tipo_prestamo === 'bancario' && !$('#id_banco').val()) {
        alert('Seleccione un banco');
        return;
    }

    if (tipo_prestamo === 'mutuo' && !$('#id_proveedor').val()) {
        alert('Seleccione un acreedor');
        return;
    }

    // Preparar datos para envío
    const datos = {
        tipo_prestamo: tipo_prestamo,
        id_banco: $('#id_banco').val() || null,
        id_proveedor: $('#id_proveedor').val() || null,
        id_tercero: null,
        monto: monto,
        tasa: tasa,
        plazo: plazo,
        tipo_amortizacion: $('#tipo_amortizacion').val(),
        fecha_desembolso: $('#fecha_desembolso').val(),
        id_cuenta_banco: $('#id_cuenta_banco').val(),
        id_cuenta_pasivo: $('#id_cuenta_pasivo').val(),
        observaciones: $('#observaciones').val(),
        _token: $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
    };

    // Deshabilitar botón para evitar duplicados
    const btnGuardar = $('button:contains("Guardar Préstamo")').first();
    const textOriginal = btnGuardar.html();
    btnGuardar.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Guardando...');

    // Realizar POST del formulario
    $.ajax({
        url: '?controlador=prestamos&submodulo=guardar',
        type: 'POST',
        data: datos,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('¡Préstamo creado exitosamente!');
                // Redirigir a la lista
                setTimeout(function() {
                    window.location.href = '?controlador=prestamos&submodulo=ver&id=' + response.id_prestamo;
                }, 1000);
            } else {
                alert('Error: ' + response.error);
                btnGuardar.prop('disabled', false).html(textOriginal);
            }
        },
        error: function(xhr) {
            console.log(xhr.responseText);
            alert('Error en la comunicación con el servidor');
            btnGuardar.prop('disabled', false).html(textOriginal);
        }
    });
}

/**
 * Ver detalles completos de un préstamo
 */
function verPrestamo(idPrestamo) {
    window.location.href = '?controlador=prestamos&submodulo=ver&id=' + idPrestamo;
}

/**
 * Pagar una cuota (integración con módulo de egresos)
 */
function pagarCuota(idCuota) {
    // Esta función abrirá un modal para crear un comprobante de egreso
    // vinculado a la cuota del préstamo
    $.ajax({
        url: '?controlador=egresos&submodulo=nuevoDesdeCouta',
        type: 'GET',
        data: {
            id_cuota_prestamo: idCuota
        },
        success: function(html) {
            // Mostrar modal con el formulario de egreso
            showModal(html);
        },
        error: function() {
            alert('Error al preparar el pago de cuota');
        }
    });
}

/**
 * Cargar cuotas pendientes de pago
 */
function cargarCuotasPendientes(idPrestamo) {
    $.ajax({
        url: '?controlador=prestamos&submodulo=cuotasPendientes&id=' + idPrestamo,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                renderizarCuotasPendientes(response.cuotas);
            } else {
                alert('Error: ' + response.error);
            }
        },
        error: function() {
            alert('Error al cargar cuotas pendientes');
        }
    });
}

/**
 * Renderizar lista de cuotas pendientes
 */
function renderizarCuotasPendientes(cuotas) {
    let html = '';
    
    cuotas.forEach(function(cuota) {
        html += '<tr>';
        html += '<td>#' + cuota.numero + '</td>';
        html += '<td>' + cuota.fecha_vencimiento + '</td>';
        html += '<td style="text-align:right">$' + cuota.capital + '</td>';
        html += '<td style="text-align:right">$' + cuota.interes + '</td>';
        html += '<td style="text-align:right">$' + cuota.cuota_total + '</td>';
        html += '<td><span class="badge badge-' + (cuota.estado === 'pendiente' ? 'warning' : 'success') + '">' + cuota.estado + '</span></td>';
        html += '<td>';
        html += '<button class="btn btn-sm btn-success" onclick="pagarCuota(\'' + cuota.id + '\')"><i class="fa fa-check"></i> Pagar</button>';
        html += '</td>';
        html += '</tr>';
    });

    $('#tablaCuotasPendientes tbody').html(html);
}

/**
 * Función auxiliar: Formatear números con separadores
 */
function formatearNumero(num) {
    if (isNaN(num)) return '0.00';
    return num.toLocaleString('es-ES', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

/**
 * Función auxiliar: Validar fechas
 */
function validarFecha(fecha) {
    const patronFecha = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
    return patronFecha.test(fecha);
}

/**
 * Función auxiliar: Convertir fecha de formato DD/MM/YYYY a YYYY-MM-DD
 */
function convertirFecha(fecha) {
    if (!validarFecha(fecha)) return null;
    const partes = fecha.split('/');
    return partes[2] + '-' + partes[1] + '-' + partes[0];
}

/**
 * Mostrar modal personalizado
 */
function showModal(contenido) {
    const modal = $('<div class="modal fade" tabindex="-1" role="dialog">' +
        '<div class="modal-dialog" role="document">' +
        '<div class="modal-content">' +
        '<div class="modal-body">' + contenido + '</div>' +
        '</div></div></div>');
    
    modal.modal('show');
    modal.on('hidden.bs.modal', function() {
        $(this).remove();
    });
}

/**
 * Generar archivo PDF de amortización
 */
function exportarAmortizacionPDF(idPrestamo) {
    window.location.href = '?controlador=prestamos&submodulo=exportarPDF&id=' + idPrestamo;
}

/**
 * Generar archivo Excel de amortización
 */
function exportarAmortizacionExcel(idPrestamo) {
    window.location.href = '?controlador=prestamos&submodulo=exportarExcel&id=' + idPrestamo;
}

// Inicializar cuando el documento esté listo
$(document).ready(function() {
    // Inicializaciones si es necesario
});
