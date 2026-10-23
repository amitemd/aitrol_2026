var idPais = 0;
var idProvincia = 0;
var idCiudad = 0;
var hoy = new Date();
var latitud = '';
var longitud = '';
var tituloExcel = '';
tituloExcel = $('#submodulo_').val() == '' ? $('#submodulo').val() + ' | ' + hoy : $('#submodulo').val() + ' | ' + $('#submodulo_').val() + ' | ' + hoy;
function audit() {
    navigator.geolocation.getCurrentPosition(position => {
        latitud = position.coords.latitude;
        longitud = position.coords.longitude;
        $.ajax({
            url: 'v3/utilidades/saveAuditoria',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('#token').val(),
            },
            data: {
                c: $('#controlador').val(),
                m: $('#modulo').val(),
                s: $('#submodulo').val(),
                v: $('#vista').val(),
                o: $('#obseraudi').val(),
                d: latitud + '|' + longitud
            },
        });
    });
}
$(document).ready(function () {
    audit();
    $('#cheestado').change(function () {
        if ($('#cheestado').is(':checked')) {
            $('#estado').val(1);
        } else {
            $('#estado').val(0);
        }
    });
    try {
        $('.select2').select2()
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        });
    } catch (error) { }
    $('.pais').change(function () {
        if ($('.pais').val() != '') {
            $('.provincia').html('<option value="">--Seleccione--</option>');
            $('.ciudad').html('<option value="">--Seleccione--</option>');
            $.ajax({
                url: 'v3/utilidades/provinciaCombo',
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                },
                data: {
                    id_pais: $('.pais').val(),
                    id_provincia: idProvincia
                },
                dataType: 'html',
                success: function (html) {
                    $('#provincia').html(html).trigger('change');
                },
                error: function (error) {
                    alert(error);
                }
            });
        }
    });
    $('.provincia').change(function () {
        if ($('.provincia').val() != '') {
            $.ajax({
                url: 'v3/utilidades/ciudadCombo',
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                },
                data: {
                    id_provincia: $('.provincia').val(),
                    id_ciudad: idCiudad
                },
                dataType: 'html',
                success: function (html) {
                    $('.ciudad').html(html).trigger('change');
                },
                error: function (error) {
                    alert(error);
                }
            });
        }
    });
    $('.pais_representante').change(function () {
        if ($('.pais_representante').val() != '') {
            $('.provincia_representante').html('<option value="">--Seleccione--</option>');
            $('.ciudad_representante').html('<option value="">--Seleccione--</option>');
            $.ajax({
                url: 'v3/utilidades/provinciaCombo',
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                },
                data: {
                    id_pais: $('.pais_representante').val(),
                    id_provincia: idProvincia
                },
                dataType: 'html',
                success: function (html) {
                    $('#provincia_representante').html(html).trigger('change');
                },
                error: function (error) {
                    alert(error);
                }
            });
        }
    });
    $('.provincia_representante').change(function () {
        if ($('.provincia_representante').val() != '') {
            $.ajax({
                url: 'v3/utilidades/ciudadCombo',
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                },
                data: {
                    id_provincia: $('.provincia_representante').val(),
                    id_ciudad: idCiudad
                },
                dataType: 'html',
                success: function (html) {
                    $('.ciudad_representante').html(html).trigger('change');
                },
                error: function (error) {
                    alert(error);
                }
            });
        }
    });
    $(document).on('select2:open', function (e) {
        document.querySelector(`[aria-controls="select2-${e.target.id}-results"]`).focus();
    });
    $('#modo').click(function () {
        if ($('#modo').html() == '<i class="fa-regular fa-moon" aria-hidden="true"></i>') {
            $("body").addClass("dark-mode");
            $('#modo').html('<i class="fa-regular fa-sun"></i>');
        } else {
            $("body").removeClass("dark-mode");
            $('#modo').html('<i class="fa-regular fa-moon" aria-hidden="true"></i>');
        }
    });
});
function validarDocumento(numero) {
    var suma = 0;
    var residuo = 0;
    var pri = false;
    var pub = false;
    var nat = false;
    var numeroProvincias = 24;
    var modulo = 11;
    /* Verifico que el campo no contenga letras */
    var ok = 1;
    for (var i = 0; i < numero.length && ok == 1; i++) {
        var n = parseInt(numero.charAt(i));
        if (isNaN(n))
            ok = 0;

    }
    if (ok == 0) { // alert("No puede ingresar caracteres en el número");
        mensajes('warning', 'No puede ingresar caracteres en el número');
        return false;
    }
    if (numero.length < 10) { // alert('El número ingresado no es válido');
        mensajes('warning', 'El número ingresado no es válido');
        return false;
    }
    /* Los primeros dos digitos corresponden al codigo de la provincia */
    var provincia = numero.substr(0, 2);
    if (provincia < 1 || provincia > numeroProvincias) { // alert('El código de la provincia (dos primeros dígitos) es inválido');
        mensajes('warning', 'El código de la provincia (dos primeros dígitos) es inválido');
        return false;
    }
    /* Aqui almacenamos los digitos de la cedula en variables. */
    var d1 = numero.substr(0, 1);
    var d2 = numero.substr(1, 1);
    var d3 = numero.substr(2, 1);
    var d4 = numero.substr(3, 1);
    var d5 = numero.substr(4, 1);
    var d6 = numero.substr(5, 1);
    var d7 = numero.substr(6, 1);
    var d8 = numero.substr(7, 1);
    var d9 = numero.substr(8, 1);
    var d10 = numero.substr(9, 1);
    /* El tercer digito es: */
    /* 9 para sociedades privadas y extranjeros   */
    /* 6 para sociedades publicas */
    /* menor que 6 (0, 1, 2, 3, 4, 5) para personas naturales */
    if (d3 == 7 || d3 == 8) { // alert('El tercer dígito ingresado es inválido');
        mensajes('warning', 'El tercer dígito ingresado es inválido');
        return false;
    }
    /* Solo para personas naturales (modulo 10) */
    if (d3 < 6) {
        nat = true;
        var p1 = d1 * 2;
        if (p1 >= 10)
            p1 -= 9;

        var p2 = d2 * 1;
        if (p2 >= 10)
            p2 -= 9;

        var p3 = d3 * 2;
        if (p3 >= 10)
            p3 -= 9;

        var p4 = d4 * 1;
        if (p4 >= 10)
            p4 -= 9;

        var p5 = d5 * 2;
        if (p5 >= 10)
            p5 -= 9;

        var p6 = d6 * 1;
        if (p6 >= 10)
            p6 -= 9;

        var p7 = d7 * 2;
        if (p7 >= 10)
            p7 -= 9;

        var p8 = d8 * 1;
        if (p8 >= 10)
            p8 -= 9;

        var p9 = d9 * 2;
        if (p9 >= 10)
            p9 -= 9;

        modulo = 10;
    }
    /* Solo para sociedades publicas (modulo 11) */
    /* Aqui el digito verficador esta en la posicion 9, en las otras 2 en la pos. 10 */ else if (d3 == 6) {
        pub = true;
        p1 = d1 * 3;
        p2 = d2 * 2;
        p3 = d3 * 7;
        p4 = d4 * 6;
        p5 = d5 * 5;
        p6 = d6 * 4;
        p7 = d7 * 3;
        p8 = d8 * 2;
        p9 = 0;
    }
    /* Solo para entidades privadas (modulo 11) */ else if (d3 == 9) {
        pri = true;
        p1 = d1 * 4;
        p2 = d2 * 3;
        p3 = d3 * 2;
        p4 = d4 * 7;
        p5 = d5 * 6;
        p6 = d6 * 5;
        p7 = d7 * 4;
        p8 = d8 * 3;
        p9 = d9 * 2;
    }
    suma = p1 + p2 + p3 + p4 + p5 + p6 + p7 + p8 + p9;
    residuo = suma % modulo;
    /* Si residuo=0, dig.ver.=0, caso contrario 10 - residuo*/
    var digitoVerificador = residuo == 0 ? 0 : modulo - residuo;
    /* ahora comparamos el elemento de la posicion 10 con el dig. ver.*/
    if (pub == true) {
        if (digitoVerificador != d9) { // alert('El ruc de la empresa del sector público es incorrecto.');
            mensajes('warning', 'El ruc de la empresa del sector público es incorrecto.');
            return false;
        }
        /* El ruc de las empresas del sector publico terminan con 0001*/
        if (numero.substr(9, 4) != '0001') { // alert('El ruc de la empresa del sector público debe terminar con 0001');
            mensajes('warning', 'El ruc de la empresa del sector público es incorrecto.');
            return false;
        }
    } else if (pri == true) {
        if (digitoVerificador != d10) { // alert('El ruc de la empresa del sector privado es incorrecto.');
            mensajes('warning', 'El ruc de la empresa del sector privado es incorrecto.');
            return false;
        }
        if (numero.substr(10, 3) != '001') { // alert('El ruc de la empresa del sector privado debe terminar con 001');
            mensajes('warning', 'El ruc de la empresa del sector privado debe terminar con 001.');
            return false;
        }
    } else if (nat == true) {
        if (digitoVerificador != d10) { // alert('El número de cédula de la persona natural es incorrecto.');
            mensajes('warning', 'El número de cédula de la persona natural es incorrecto.');
            return false;
        }
        if (numero.length > 10 && numero.substr(10, 3) != '001') { // alert('El ruc de la persona natural debe terminar con 001');
            mensajes('warning', 'El ruc de la persona natural debe terminar con 001.');
            return false;
        }
    }
    return true;
}
function limpiarModal() {
    $('#frm_')[0].reset();
    $('#frm_').trigger('reset');
    $('.select2').val('').trigger('change');
}
function mensajes(icon, title, frm = 'frm_', action = 'action', url = '', tabla = '', footer = '') {
    if (icon == 'question') {
        Swal.fire({
            title: title,
            text: $('#informa').val(),
            icon: icon,
            showCancelButton: true,
            cancelButtonText: 'No',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: $('#' + action).val(),
                    type: 'post',
                    data: $('#' + frm).serialize(),
                    dataType: 'json',
                    success: function (json) {
                        if (json.status == 'success') {
                            try {
                                if (url === '') {
                                    mensajes('success', json.message, frm, action, url, tabla, footer);
                                    $('.modalTotal').modal('hide');
                                    $('.tablaDatos').DataTable().ajax.reload();
                                } else if (json.tipo == 'crucecuentas') {
                                    $("#id_asiento").val(json.id_asiento);
                                    $("#id").val(json.id);
                                    $("#numero").val(json.numero);
                                    document.getElementById("btnguardarCruce").style.display = "none";
                                    mensajes('success', json.message, frm, action);
                                } else if (json.tipo == 'compIngreso') {
                                    $("#id_asiento").val(json.id_asiento);
                                    $("#id").val(json.id);
                                    $("#numero").val(json.numero);
                                    document.getElementById("btnguardarCompIngreso").style.display = "none";
                                    mensajes('success', json.message, frm, action);
                                } else if (json.tipo == 'crucevalores') {
                                    $("#id_asiento").val(json.id_asiento);
                                    $("#id").val(json.id);
                                    $("#numero").val(json.numero);
                                    document.getElementById("btnguardarCruceValores").style.display = "none";
                                    mensajes('success', json.message, frm, action);
                                } else {
                                    //location.reload = $("#base_url").val() + '/' + url;
                                    mensajes('success', json.message, frm, action, url, tabla, footer);
                                }
                            } catch (error) {
                            }
                        } else {
                            mensajes('error', json.message, frm, action, url, tabla, json.footer);
                        }
                    }
                });
            }
        });
    } else if (icon == 'warning' || icon == 'success' || icon == 'error' || icon == 'info') {
        Swal.fire({
            title: title,
            text: $('#informa').val(),
            icon: icon,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Ok',
            footer: footer
        }).then((result) => {
            if (result.isConfirmed) {
                if (url != '') {
                    window.open(url, '_self');
                }
                if (tabla != '') {
                    $('.modalTotal').modal('hide');
                    $('.tablaDatos').DataTable().ajax.reload();
                }
            }
        });
    }
}
function soloNumeros(event) {
    if (event.charCode >= 48 && event.charCode <= 57) {
        return true;
    }
    return false;
}
function cambiarEstado(id, estado, dato) {
    $.ajax({
        url: 'v3/' + $('#controlador').val() + "/estado" + dato,
        type: "post",
        headers: {
            'X-CSRF-TOKEN': $('#token').val(),
        },
        data: {
            estado: estado,
            id: id
        },
        dataType: "json",
        success: function (json) {
            if (json.status == 'success') {
                mensajes('info', json.message);
                $('.tablaDatos').DataTable().ajax.reload();
            } else {
                mensajes('error', json.message + dato);
            }
        }
    });
}
var recognition;
var recognizing = false;
if (!('webkitSpeechRecognition' in window)) {
    $('#botonhablar').css({
        'display': 'none'
    });
} else {
    recognition = new webkitSpeechRecognition();
    recognition.lang = "es-EC";
    recognition.continuous = true;
    recognition.interimResults = true;
    recognition.onstart = function () {
        recognizing = true;
    };
    recognition.onresult = function (event) {
        for (var i = event.resultIndex; i < event.results.length; i++) {
            if (event.results[i].isFinal) {
                if (/salir/.test(event.results[i][0].transcript) || /Salir/.test(event.results[i][0].transcript)) {
                    recognition.stop();
                    recognizing = false;
                    // window.open($('#base_url').val() + '/logout', '_self');
                    $.ajax({
                        type: "post",
                        url: $('#base_url').val() + '/logout',
                        headers: {
                            'X-CSRF-TOKEN': $('#token').val()
                        }, success: function (response) {
                            return location.reload();
                        }, error: function (response) {
                            return location.reload();

                        }
                    })
                } else {
                    $('#textBuscador').val(event.results[i][0].transcript);
                    $('#frmbuscador').submit();
                }
            }
        }
    };
    recognition.onerror = function (event) { };
    recognition.onend = function () {
        recognizing = false;
        $('#procesar').html('<i class="fa fa-microphone"></i>');
    }
}
function procesar() {
    if (recognizing == false) {
        recognition.start();
        recognizing = true;
        $('#procesar').html('<i class="fa fa-microphone-slash"></i>');
    } else {
        recognition.stop();
        recognizing = false;
        $('#procesar').html('<i class="fa fa-microphone"></i>');
    }
}
function PadLeft(value, length) {
    try {
        return (value.toString().length < length) ? PadLeft("0" + value, length) : value;
    } catch (error) {
        mensajes(error, 'error', '', '');
    }
}
function seleccionarPeriodo(anio) {
    window.location.href = $('#base_url').val() + '/v3/utilidades/periodo?anio=' + anio;
}
function seleccionarEmpresa(id) {
    window.location.href = $('#base_url').val() + '/v3/utilidades/empresa?id=' + id;
}
function goBack(variable) {
    if (variable !== undefined) {
        window.location.href = "v3/" + $('#controlador').val() + "/" + variable;
    }
    else {
        window.history.back();
    }
}
function iconModal() {
    $.ajax({
        url: "v3/" + $('#controlador').val() + "/traerModaIcon",
        dataType: 'html',
        success: function (datahtml) {
            $('.modal-content > .rt').html(datahtml);
            $('#modal_icono').modal("show");
        },
        error: function (xhr, status) {
            console.log(xhr);
        },
    });
}
function verMapa() {
    var direccion = $('.direccion').val() != '' ? $('.direccion').val() : '';
    var pais = $(".pais option:selected").text() != '' ? $(".pais option:selected").text() : '';
    var provincia = $(".provincia option:selected").text() != '' ? $(".provincia option:selected").text() : '';
    var ciudad = $(".ciudad option:selected").text() != '' ? $(".ciudad option:selected").text() : '';
    var manzana = $(".manzana").val() != '' ? $('.manzana').val() : '';
    var urbanizacion = $(".urbanizacion").val() != '' ? $('.urbanizacion').val() : '';
    var etapa = $(".etapa").val() != '' ? $('.etapa').val() : '';
    var villa = $(".villa").val() != '' ? $('.villa').val() : '';
    var url = 'https://maps.google.com/maps?width=770&height=450&hl=es&authuser=0&q=' + direccion + ',' + ciudad + '+' + provincia + '+' + pais + '+' + manzana + '+' + urbanizacion + '+' + etapa + '+' + villa + '&oq=' + direccion + ',' + ciudad + '+' + provincia + '+' + pais + '+' + manzana + '+' + urbanizacion + '+' + etapa + '+' + villa + '&t=&z=12&ie=UTF8&iwloc=B&tbm=map&output=embed';
    window.open(url, '_blank');
}
function getfecha(fecha, horas = null, separadores = '.') {
    if (fecha != '') {
        var f = new Date(fecha);
        var dia = f.getDate();
        if (dia < 10)
            dia = '0' + dia;
        var mes = parseFloat(f.getMonth()) + 1;
        if (mes < 10)
            mes = '0' + mes;
        var anio = f.getFullYear();
        var hora = '';
        var hora_ = f.getHours();
        if (hora_ < 10)
            hora_ = '0' + hora_;
        var minuto = f.getMinutes();
        if (minuto < 10)
            minuto = '0' + minuto;
        var segundos = f.getSeconds();
        if (segundos < 10)
            segundos = '0' + segundos;
        hora = hora_ + ':' + minuto + ':' + segundos + ' ' + hora;
        fecha = anio + separadores + mes + separadores + dia;
        if (horas != null)
            fecha = anio + separadores + mes + separadores + dia + ' ' + hora;
        return fecha;
    } else {
        return '';
    }
}
function verUbicacion() {
    var la = parseFloat($('#latitud').val());
    var lo = parseFloat($('#longitud').val());
    //window.open('https://www.google.com/maps/@' + la + ',' + lo + ',14z?entry=ttu', 'blank');
    initMap(la, lo);
}
function initMap(la, lo) {
    document.getElementById('map').style.width = '100%';
    document.getElementById('map').style.height = '350px';
    var map;

    map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: la, lng: lo },
        zoom: 13,
    });

    var marker = new google.maps.Marker({
        position: { lat: la, lng: lo },
        map: map,
        title: 'Ubicación cercana'
    });
}
function capitalizar(string) {
    return string.toLowerCase().trim().split(' ').map(v => v[0].toUpperCase() + v.substr(1)).join(' ');
}
function visualizarAsiento(id) {
    $.ajax({
        url: $('#base_url').val() + '/v3/utilidades/verAsiento',
        type: "post",
        headers: {
            'X-CSRF-TOKEN': $('#token').val(),
        },
        data: {
            id: id
        },
        dataType: 'html',
        success: function (html) {
            $('#titulo_modal').html('<i class="fas fa-eye"></i> Visualizador de asientos...');
            $('#contenidoAsiento').html(html);
            $('#myModalAsientos').modal("toggle");
        },
        error: function (xhr, status) {
            console.log(xhr);
        },
    });
}
function obtener_caja() {
    $.ajax({
        url: "v3/utilidades/obtenerPuntoEmision",
        type: "post",
        headers: {
            "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
            id: $("#establecimiento").val(),
        },
        datatype: "json",
        success: function (json) {
            if ($("#establecimiento").val() != "") {
                $("#emision").empty();
                $("#emision").append('<option value="">--Seleccione--</option>');
                $.each(json, function (key, registro) {
                    $("#emision").append("<option value=" + registro.id + ">" + registro.codigo_sucursal + "-" + registro.codigo_caja + "</option>");
                });
            } else {
                $("#emision").empty();
                $("#emision").append('<option value="">--Seleccione--</option>');
            }
        },
        error: function (json) {
            console.log(json);
        },
    });
}
function ajax(url, data, dataType) {
    return $.ajax({
        url: url,
        type: "post",
        headers: {
            "X-CSRF-TOKEN": $("#token").val(),
        },
        data: data,
        async: false,
        dataType: dataType,
        success: function (data) {
            return data;
        }, error(error) {
            return error.message;
        }
    });
}