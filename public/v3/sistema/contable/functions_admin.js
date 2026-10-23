$(document).ready(function () {
    $('#elementTour').val('hero-menu,hero-cabecera,hero-footer');
    var valorVentas = 0;
    var privadas = 0;
    var particulares = 0;
    var relacionadas = 0;
    var valorCompras = 0;
    var valorUtilidades = 0;
    var compras = 0;
    var gastos = 0;
    var utilidad = 0;
    dibujarCanvas();
});
function dibujarCanvas(tipo = 'line') {
    $('#chart').val(tipo);
    $('#chartVentas').html('<img src="' + $('#base_url').val() + '/v3/dist/imgYCZH.gif" />');
    $('#chartCompras').html('<img src="' + $('#base_url').val() + '/v3/dist/imgYCZH.gif" />');
    $('#chartUtilidades').html('<img src="' + $('#base_url').val() + '/v3/dist/imgYCZH.gif" />');
    $.ajax({
        url: "v3/" + $("#controlador").val() + "/valorVentas",
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": $("#token").val(),
        },
        async: false,
        dataType: 'json',
        success: function (res) {
            valorVentas = res;
            $('#chartVentas').html('<canvas id="lineVentas" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>');
        }
    });
    $.each(valorVentas, function (i, item) {
        if (item.tipo == 'Ventas') {
            privadas = [parseFloat(item.ene).toFixed(2), parseFloat(item.feb).toFixed(2), parseFloat(item.mar).toFixed(2), parseFloat(item.abr).toFixed(2), parseFloat(item.may).toFixed(2), parseFloat(item.jun).toFixed(2), parseFloat(item.jul).toFixed(2), parseFloat(item.ago).toFixed(2), parseFloat(item.sep).toFixed(2), parseFloat(item.oct).toFixed(2), parseFloat(item.nov).toFixed(2), parseFloat(item.dic).toFixed(2)];
        }
        /*if (item.tipo == 'Particulares') {
            particulares = [parseFloat(item.ene).toFixed(2), parseFloat(item.feb).toFixed(2), parseFloat(item.mar).toFixed(2), parseFloat(item.abr).toFixed(2), parseFloat(item.may).toFixed(2), parseFloat(item.jun).toFixed(2), parseFloat(item.jul).toFixed(2), parseFloat(item.ago).toFixed(2), parseFloat(item.sep).toFixed(2), parseFloat(item.oct).toFixed(2), parseFloat(item.nov).toFixed(2), parseFloat(item.dic).toFixed(2)];
        }
        if (item.tipo == 'Relacionadas') {
            relacionadas = [parseFloat(item.ene).toFixed(2), parseFloat(item.feb).toFixed(2), parseFloat(item.mar).toFixed(2), parseFloat(item.abr).toFixed(2), parseFloat(item.may).toFixed(2), parseFloat(item.jun).toFixed(2), parseFloat(item.jul).toFixed(2), parseFloat(item.ago).toFixed(2), parseFloat(item.sep).toFixed(2), parseFloat(item.oct).toFixed(2), parseFloat(item.nov).toFixed(2), parseFloat(item.dic).toFixed(2)];
        }*/
    });
    var colores = colorearPublicas();
    var colores1 = colorearPublicas();
    var colores2 = colorearPublicas();
    var tipoChart = $('#chart').val() == '' ? 'line' : ($('#chart').val() == 'line' ? 'line' : $('#chart').val());
    var lineDataVenta = {
        labels: [$('#enero').val(), $('#febrero').val(), $('#marzo').val(), $('#abril').val(), $('#mayo').val(), $('#junio').val(), $('#julio').val(), $('#agosto').val(), $('#septiembre').val(), $('#octubre').val(), $('#noviembre').val(), $('#diciembre').val()],
        datasets: [
            {
                label: 'Ventas',
                backgroundColor: 'rgba(197,177,233,0.5)',
                borderColor: "rgba(197,177,233,0.7)",
                pointBackgroundColor: "rgba(197,177,233,1)",
                pointBorderColor: "#fff",
                data: privadas,
            }, /*{
                label: $('#particulares').val(),
                backgroundColor: 'rgba(220, 220, 220, 0.5)',
                pointBorderColor: "#fff",
                data: particulares,
            }
            , {
                label: $('#relacionadas').val(),
                backgroundColor: 'rgba(110,185,229,0.9)',
                borderColor: 'rgba(110,185,229,0.8)',
                pointBorderColor: "#fff",
                data: relacionadas,
            }*/
        ]
    };
    var lineOptionsVentas = {
        maintainAspectRatio: false,
        responsive: true,

    };
    var ctx = document.getElementById("lineVentas").getContext("2d");
    new Chart(ctx, { type: tipoChart, data: lineDataVenta, options: lineOptionsVentas });
    /* $.ajax({
         url: "v3/" + $("#controlador").val() + "/valorCompras",
         type: "POST",
         headers: {
             "X-CSRF-TOKEN": $("#token").val(),
         },
         async: false,
         dataType: 'json',
         success: function (res) {
             valorCompras = res;
             $('#chartCompras').html('<canvas id="lineCompras" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>');
         }
     });
     /*$.each(valorCompras, function (i, item) {
         if (item.tipo == 'Compras') {
             compras = [parseFloat(item.ene).toFixed(2), parseFloat(item.feb).toFixed(2), parseFloat(item.mar).toFixed(2), parseFloat(item.abr).toFixed(2), parseFloat(item.may).toFixed(2), parseFloat(item.jun).toFixed(2), parseFloat(item.jul).toFixed(2), parseFloat(item.ago).toFixed(2), parseFloat(item.sep).toFixed(2), parseFloat(item.oct).toFixed(2), parseFloat(item.nov).toFixed(2), parseFloat(item.dic).toFixed(2)];
         }
         if (item.tipo == 'Gastos') {
             gastos = [parseFloat(item.ene).toFixed(2), parseFloat(item.feb).toFixed(2), parseFloat(item.mar).toFixed(2), parseFloat(item.abr).toFixed(2), parseFloat(item.may).toFixed(2), parseFloat(item.jun).toFixed(2), parseFloat(item.jul).toFixed(2), parseFloat(item.ago).toFixed(2), parseFloat(item.sep).toFixed(2), parseFloat(item.oct).toFixed(2), parseFloat(item.nov).toFixed(2), parseFloat(item.dic).toFixed(2)];
         }
     });
     var lineDataCompas = {
         labels: [$('#enero').val(), $('#febrero').val(), $('#marzo').val(), $('#abril').val(), $('#mayo').val(), $('#junio').val(), $('#julio').val(), $('#agosto').val(), $('#septiembre').val(), $('#octubre').val(), $('#noviembre').val(), $('#diciembre').val()],
         datasets: [
             {
                 label: $('#compras').val(),
                 backgroundColor: 'rgba(148,179,133,0.5)',
                 borderColor: "rgba(148,179,133,0.7)",
                 pointBackgroundColor: "rgba(148,179,133,1)",
                 pointBorderColor: "#fff",
                 data: compras,
             }, {
                 label: $('#gastos').val(),
                 backgroundColor: 'rgba(198,146,146, 0.5)',
                 pointBorderColor: "#fff",
                 data: gastos,
             }
         ]
     };
     var lineOptionsCompras = {
         maintainAspectRatio: false,
         responsive: true,
     };
     var ctx = document.getElementById("lineCompras").getContext("2d");
     new Chart(ctx, { type: tipoChart, data: lineDataCompas, options: lineOptionsCompras });
     */
    /*$.ajax({
        url: "v3/" + $("#controlador").val() + "/valorUtilidad",
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": $("#token").val(),
        },
        async: false,
        dataType: 'json',
        success: function (res) {
            valorUtilidades = res;
            $('#chartUtilidades').html('<canvas id="lineVentasUtilidades" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>');
        }
    });

    $.each(valorUtilidades, function (i, item) {
        utilidad = [parseFloat(item.ene).toFixed(2), parseFloat(item.feb).toFixed(2), parseFloat(item.mar).toFixed(2), parseFloat(item.abr).toFixed(2), parseFloat(item.may).toFixed(2), parseFloat(item.jun).toFixed(2), parseFloat(item.jul).toFixed(2), parseFloat(item.ago).toFixed(2), parseFloat(item.sep).toFixed(2), parseFloat(item.oct).toFixed(2), parseFloat(item.nov).toFixed(2), parseFloat(item.dic).toFixed(2)];
    });

    var lineDataVentaUtilidad = {
        labels: [$('#enero').val(), $('#febrero').val(), $('#marzo').val(), $('#abril').val(), $('#mayo').val(), $('#junio').val(), $('#julio').val(), $('#agosto').val(), $('#septiembre').val(), $('#octubre').val(), $('#noviembre').val(), $('#diciembre').val()],
        datasets: [
            {
                label: $('#utilidad').val(),
                backgroundColor: 'rgba(110,185,229,0.9)',
                borderColor: 'rgba(110,185,229,0.8)',
                pointBackgroundColor: "rgba(110,185,229,1)",
                pointBorderColor: "#fff",
                data: utilidad,
            }
        ]
    };
    var lineOptionsVentasUtilidad = {
        maintainAspectRatio: false,
        responsive: true,

    };
    var ctx = document.getElementById("lineVentasUtilidades").getContext("2d");
    new Chart(ctx, { type: tipoChart, data: lineDataVentaUtilidad, options: lineOptionsVentasUtilidad });*/
}
function getRandomColor(minR, maxR, minG, maxG, minB, maxB) {
    colorR = Math.floor(Math.random() * maxR) + minR;
    colorG = Math.floor(Math.random() * maxG) + minG;
    colorB = Math.floor(Math.random() * maxB) + minB;
    var color = '';
    color = color.concat('rgb(', colorR, ',', colorG, ',', colorB);
    return color;
}

function colorearPublicas() {
    color = getRandomColor(120, 240, 120, 240, 120, 240);
    return color;
}