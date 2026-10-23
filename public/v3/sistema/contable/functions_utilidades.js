
$(function () {
    $('.my-colorpicker2_fondo').colorpicker()
    $('.my-colorpicker2_fondo').on('colorpickerChange', function (event) {
        $('.my-colorpicker2_fondo .fa-square').css('color', '#FFFFFF');
    });
    $('.my-colorpicker2_texto').colorpicker()
    $('.my-colorpicker2_texto').on('colorpickerChange', function (event) {
        $('.my-colorpicker2_texto .fa-square').css('color', '#000000');
    });
    function ini_events(ele) {
        ele.each(function () {
            var eventObject = {
                title: $.trim($(this).text()) // use the element's text as the event title
            }
            $(this).data('eventObject', eventObject)
            $(this).draggable({
                zIndex: 1070,
                revert: true, // will cause the event to go back to its
                revertDuration: 0  //  original position after the drag
            })

        })
    }
    ini_events($('#external-events div.external-event'))
    var date = new Date()
    var d = date.getDate(),
        m = date.getMonth(),
        y = date.getFullYear()
    var Calendar = FullCalendar.Calendar;
    var Draggable = FullCalendar.Draggable;
    var containerEl = document.getElementById('external-events');
    var checkbox = document.getElementById('drop-remove');
    var calendarEl = document.getElementById('calendar');
    new Draggable(containerEl, {
        itemSelector: '.external-event',
        eventData: function (eventEl) {
            return {
                title: eventEl.innerText,
                backgroundColor: window.getComputedStyle(eventEl, null).getPropertyValue('background-color'),
                borderColor: window.getComputedStyle(eventEl, null).getPropertyValue('background-color'),
                textColor: window.getComputedStyle(eventEl, null).getPropertyValue('color'),
            };
        }
    });
    var calendar = new Calendar(calendarEl, {
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        themeSystem: 'bootstrap',
        events: 'v3/' + $('#controlador').val() + '/listareventos',
        editable: true,
        droppable: true,
        drop: function (info) {
            if (checkbox.checked) {
                info.draggedEl.parentNode.removeChild(info.draggedEl);
            }
        },
        dateClick: function (info) {
            limpiarModal();
            $('#descripcion_evento').html('');
            $('#titulo_modal').html('<i class="fa-regular fa-calendar-days"></i> Agregar nuevo evento');
            $('#myModal').modal('toggle');
        },
        eventClick: function (info) {
            limpiarModal();
            $('#descripcion_evento').html('');
            $('#titulo_modal').html('<i class="fa-regular fa-calendar-days"></i> Modificar evento');
            $('#id_evento').val(info.event.id);
            $('#nombre_evento').val(info.event.title);
            $('#desde_evento').val(moment(info.event.start).format('YYYY-MM-DD'));
            $('#hasta_evento').val(moment(info.event.end).format('YYYY-MM-DD'));
            if (info.event.start !== null)
                $('#hora_desde_evento').val(moment(info.event.start).format('HH:mm'));
            if (info.event.end !== null)
                $('#hora_hasta_evento').val(moment(info.event.end).format('HH:mm'));
            $('#descripcion_evento').html(info.event.extendedProps.descripcion);
            $('#url_evento').val(info.event.url);
            $('#myModal').modal('toggle');
        }, eventResize: function (info) {
            $('#id_evento').val(info.event.id);
            $('#nombre_evento').val(info.event.title);
            $('#desde_evento').val(moment(info.event.start).format("YYYY-MM-DD"));
            $('#hasta_evento').val(moment(info.event.end).format("YYYY-MM-DD"));
            if (info.event.start !== null)
                $('#hora_desde_evento').val(moment(info.event.start).format('HH:mm'));
            if (info.event.end !== null)
                $('#hora_hasta_evento').val(moment(info.event.end).format('HH:mm'));
            $('#color_fondo_evento').val(info.event.backgroundColor);
            $('#color_texto_evento').val(info.event.textColor);
            $('#descripcion_evento').val(info.event.extendedProps.descripcion);
            $('#url_evento').val(info.event.url);
            let registro = recuperarDatosEvento();
            modificarEvento(registro);
        },
        eventDrop: function (info) {
            $('#id_evento').val(info.event.id);
            $('#nombre_evento').val(info.event.title);
            $('#desde_evento').val(moment(info.event.start).format("YYYY-MM-DD"));
            $('#hasta_evento').val(moment(info.event.end).format("YYYY-MM-DD"));
            if (info.event.start !== null)
                $('#hora_desde_evento').val(moment(info.event.start).format('HH:mm'));
            if (info.event.end !== null)
                $('#hora_hasta_evento').val(moment(info.event.end).format('HH:mm'));
            $('#color_fondo_evento').val(info.event.backgroundColor);
            $('#color_texto_evento').val(info.event.textColor);
            $('#descripcion_evento').val(info.event.extendedProps.descripcion);
            $('#url_evento').val(info.event.url);
            let registro = recuperarDatosEvento();
            modificarEvento(registro);
        },
    });

    calendar.render();
    var currColor = '#3c8dbc' //Red by default
    $('#color-chooser > li > a').click(function (e) {
        e.preventDefault()
        currColor = $(this).css('color')
        $('#add-new-event').css({
            'background-color': currColor,
            'border-color': currColor
        })
    })
    $('#add-new-event').click(function (e) {
        e.preventDefault()
        var val = $('#new-event').val()
        if (val.length == 0) {
            return
        }
        var event = $('<div />')
        event.css({
            'background-color': currColor,
            'border-color': currColor,
            'color': '#fff'
        }).addClass('external-event')
        event.text(val)
        $('#external-events').prepend(event)
        ini_events(event)
        $('#new-event').val('')
    })
    $('#btnGuardarEvento').click(function () {
        if ($('#nombre_evento').val() == '') {
            return mensajes('warning', 'Debe ingresar el nombre del evento...');
        } else if ($('#desde_evento').val() == '') {
            return mensajes('warning', 'Debe ingresar la fecha de inicio...');
        } else if ($('#hasta_evento').val() == '') {
            return mensajes('warning', 'Debe ingresar la fecha final...');
        } else if ($('#descripcion_evento').val() == '') {
            return mensajes('warning', 'Debe ingresar la descripcion del evento...');
        }
        else {
            mensajes('question', $('#pregunta').val());
        }
    });
})
function verFactura(id, num) {
    $("#titulo_modal").html('<i class="fas fa-edit"></i> No.: ' + num);
    var data = { id: id };
    var html = ajax("v3/ventas/getVentaPrint", data, "html")
    html = html.responseText;
    $("#cuerpoBusqueda").html(html);
    $("#myModal").modal("toggle");
}
function abrirModulo(modulo, codigo, id = null, num = null) {
    var html = '';
    if (modulo == 'productos') {
        var cod = codigo.split(":");
        $.ajax({
            url: "v3/" + $("#controlador").val() + "/movimientoProducto",
            type: 'post',
            headers: {
                "X-CSRF-TOKEN": $("#token").val(),
            },
            data: {
                codigo: cod[1],
            },
            dataType: 'json',
            success: function (json) {
                let valor_anterior = 0;
                let precio_total_egreso = 0;
                let precio_unitario_egreso = 0;
                let cantidad_egreso = 0;
                let precio_total_ingreso = 0;
                let precio_unitario_ingreso = 0;
                let cantidad_ingreso = 0;
                let precio_unitario = 0;
                let saldo_final = 0;
                let documento = '';
                let dato = 0;
                let htmlDetalle = '';
                $.each(json, function (i, element) {
                    if (element.tipo == 'E') {
                        precio_total_egreso = element.cantidad;
                        precio_unitario_egreso = element.valor_unitario;
                        cantidad_egreso = precio_total_egreso * precio_unitario_egreso;
                        saldo_final -= cantidad_egreso;
                        dato -= precio_total_egreso;
                        valor_anterior = parseFloat(saldo_final / dato);
                    }
                    if (element.tipo == 'I') {
                        precio_total_ingreso = element.cantidad;
                        precio_unitario_ingreso = element.valor_unitario;
                        cantidad_ingreso = precio_total_ingreso * precio_unitario_ingreso;

                        dato += precio_total_ingreso;
                        saldo_final += cantidad_ingreso;
                        valor_anterior = parseFloat(saldo_final / dato);
                    }
                    htmlDetalle += '<tr>';
                    htmlDetalle += '<td style="border-bottom:1px solid #CED3D6;">' + element.descripcion + '</td>';
                    htmlDetalle += '<td style="border-bottom:1px solid #CED3D6;">' + element.documento + '</td>';
                    htmlDetalle += '<td style="text-align:center;border-bottom:1px solid #CED3D6;">' + element.fecha + '</td>';
                    htmlDetalle += '<td style="text-align:center;border-bottom:1px solid #CED3D6;">' + element.lote + '</td>';
                    htmlDetalle += '<td style="text-align:center;border-bottom:1px solid #CED3D6;">' + element.serie + '</td>';
                    if (element.tipo == 'I') {
                        htmlDetalle += '<td style="text-align:right;background:#D7F2C6;border-bottom:1px solid #CED3D6;">' + element.cantidad + '</td>';
                        htmlDetalle += '<td style="text-align:right;background:#D7F2C6;border-bottom:1px solid #CED3D6;">$ ' + parseFloat(element.valor_unitario).toFixed(3) + '</td>';
                        htmlDetalle += '<td style="text-align:right;background:#D7F2C6;border-bottom:1px solid #CED3D6;">$ ' + parseFloat(element.subtotal).toFixed(3) + '</td>';

                        htmlDetalle += '<td style="text-align:right;background:#F2D7C6;border-bottom:1px solid #CED3D6;">0</td>';
                        htmlDetalle += '<td style="text-align:right;background:#F2D7C6;border-bottom:1px solid #CED3D6;">$ 0.000</td>';
                        htmlDetalle += '<td style="text-align:right;background:#F2D7C6;border-bottom:1px solid #CED3D6;">$ 0.000</td>';

                        htmlDetalle += '<td style="text-align:right;background:#C6E0F2;border-bottom:1px solid #CED3D6;">' + dato + '</td>';
                        htmlDetalle += '<td style="text-align:right;background:#C6E0F2;border-bottom:1px solid #CED3D6;">$ ' + valor_anterior.toFixed(3) + '</td>';
                        htmlDetalle += '<td style="text-align:right;background:#C6E0F2;border-bottom:1px solid #CED3D6;">$ ' + saldo_final.toFixed(3) + '</td>';
                    }
                    else {
                        htmlDetalle += '<td style="text-align:right;background:#D7F2C6;border-bottom:1px solid #CED3D6;">0</td>';
                        htmlDetalle += '<td style="text-align:right;background:#D7F2C6;border-bottom:1px solid #CED3D6;">$ 0.000</td>';
                        htmlDetalle += '<td style="text-align:right;background:#D7F2C6;border-bottom:1px solid #CED3D6;">$ 0.000</td>';

                        htmlDetalle += '<td style="text-align:right;background:#F2D7C6;border-bottom:1px solid #CED3D6;">' + element.cantidad + '</td>';
                        htmlDetalle += '<td style="text-align:right;background:#F2D7C6;border-bottom:1px solid #CED3D6;">$ ' + parseFloat(element.valor_unitario).toFixed(3) + '</td>';
                        htmlDetalle += '<td style="text-align:right;background:#F2D7C6;border-bottom:1px solid #CED3D6;">$ ' + parseFloat(element.subtotal).toFixed(3) + '</td>';

                        htmlDetalle += '<td style="text-align:right;background:#C6E0F2;border-bottom:1px solid #CED3D6;">' + dato + '</td>';
                        htmlDetalle += '<td style="text-align:right;background:#C6E0F2;border-bottom:1px solid #CED3D6;">$ ' + valor_anterior.toFixed(3) + '</td>';
                        htmlDetalle += '<td style="text-align:right;background:#C6E0F2;border-bottom:1px solid #CED3D6;">$ ' + saldo_final.toFixed(3) + '</td>';
                    }

                    htmlDetalle += '</tr>';
                });
                html = '<table style="width:100%;" cellspacing="0" cellpadding="0">';
                html += '<tr>';
                html += '<td>Codigo: </td>';
                html += '<td>' + json[0].codigo + '</td>';
                html += '<tr>';
                html += '<td>Nombre: </td>';
                html += '<td>' + json[0].producto + '</td>';
                html += '</tr>';
                html += '<tr>';
                html += '<td>Marca: </td>';
                html += '<td>' + json[0].marca + '</td>';
                html += '</tr>';
                html += '<tr>';
                html += '<td>Stock: </td>';
                html += '<td>' + dato + '</td>';
                html += '</tr>';
                html += '<tr>';
                html += '<td>Ultimo costo: </td>';
                html += '<td>$ ' + valor_anterior.toFixed(4) + '</td>';
                html += '</tr>';
                html += '<tr>';
                html += '<td colspan="2"><hr/></td>';
                html += '</tr>';
                html += '<tr>';
                html += '<td colspan="2"><table style="width:100%;" cellspacing="0" cellpadding="0">';
                html += '<tr>';
                html += '<th style="border:1px solid #CED3D6;text-align:center;">Tipo</th>';
                html += '<th style="border:1px solid #CED3D6;text-align:center;">Documento</th>';
                html += '<th style="border:1px solid #CED3D6;text-align:center;">Fecha</th>';
                html += '<th style="border:1px solid #CED3D6;text-align:center;">Lote</th>';
                html += '<th style="border:1px solid #CED3D6;text-align:center;">Serie</th>';

                html += '<th colspan="3" style="text-align:center;"><table style="width:100%;" cellspacing="0" cellpadding="0"><tr><td style="border-top:1px solid #CED3D6;width:100%;" colspan="3">Ingreso</td></tr><tr><td style="border:1px solid #CED3D6;">Cant</td><td style="border:1px solid #CED3D6;">V. Unit</td><td style="border:1px solid #CED3D6;">Total</td></tr></table></th>';
                html += '<th colspan="3" style="text-align:center;"><table style="width:100%;" cellspacing="0" cellpadding="0"><tr><td style="border:1px solid #CED3D6;width:100%;" colspan="3">Salida</td></tr><tr><td style="border:1px solid #CED3D6;">Cant</td><td style="border:1px solid #CED3D6;">V. Unit</td><td style="border:1px solid #CED3D6;">Total</td></tr></table></th>';
                html += '<th colspan="3" style="text-align:center;"><table style="width:100%;" cellspacing="0" cellpadding="0"><tr><td style="border-top:1px solid #CED3D6;border-right:1px solid #CED3D6;width:100%;" colspan="3">Saldo</td></tr><tr><td style="border:1px solid #CED3D6;">Cant</td><td style="border:1px solid #CED3D6;">V. Unit</td><td style="border:1px solid #CED3D6;">Total</td></tr></table></th>';

                html += '</tr>';
                html += htmlDetalle;
                html += '</table></td>';
                html += '</tr>';
                html += '</table>';
                $('#titulo_modal').html('<i class="fas fa-edit"></i> Información: ' + json[0].producto + '...');
                $("#cuerpoBusqueda").html(html);
                $("#myModal").modal("toggle");
            }
        });
    } else if (modulo == 'ventas') {
        verFactura(id, num);
    }

}
function recuperarDatosEvento() {
    let registro = {
        id_evento: $('#id_evento').val(),
        nombre_evento: $('#nombre_evento').val(),
        descripcion_evento: $('#descripcion_evento').val(),
        desde_evento: $('#desde_evento').val(),
        hora_desde_evento: $('#hora_desde_evento').val(),
        hasta_evento: $('#hasta_evento').val(),
        hora_hasta_evento: $('#hora_hasta_evento').val(),
        color_fondo_evento: $('#color_fondo_evento').val(),
        color_texto_evento: $('#color_texto_evento').val(),
        url_evento: $('#url_evento').val(),
        d: '|',
    };
    return registro;
}
function modificarEvento(registro) {
    $.ajax({
        url: 'v3/' + $('#controlador').val() + '/modificarEvento',
        type: 'post',
        headers: {
            'X-CSRF-TOKEN': $('#token').val(),
        },
        data: registro,
        dataType: 'json',
        success: function (json) {
            return mensajes(json.status, json.message);
        },
        error: function (error) {
            return mensajes('error', error);
        }
    });
}