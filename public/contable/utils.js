//FUNCTIONS TO USE CONTABLE 
//add zero with secuence
function zero(secuence,parameters=10) {
    var secuencia_factura =secuence.val();
    var digitos = 9;
    var ceros = 0;
    var varos = '0';
    var secuencia = 0;
    if (secuencia_factura > 0) {
        var longitud = parseInt(secuencia_factura.length);
        if (longitud > parameters) {
            swal("Error!", "Valor no permitido", "error");
            secuence.val('');

        } else {
            var concadenate = parseInt(digitos - longitud);
            switch (longitud) {
                case 1:
                    secuencia = '000000000';
                    break;
                case 2:
                    secuencia = '00000000';
                    break;
                case 3:
                    secuencia = '0000000';
                    break;
                case 4:
                    secuencia = '000000';
                    break;
                case 5:
                    secuencia = '00000';
                    break;
                case 6:
                    secuencia = '0000';
                    break;
                case 7:
                    secuencia = '000';
                    break;
                case 8:
                    secuencia = '00';
                    break;
                case 9:
                    secuencia = '0';
            }
            secuence.val(secuencia + secuencia_factura);
        }


    } else {
        swal("Error!", "Valor no permitido", "error");
        secuence.val('');
    }
}
//function round decimals
function round(num,decimales =2){
    var signo = (num >= 0 ? 1 : -1);
    num = num * signo;
    //console.log("eduardo maricon");
    if (decimales === 0) //con 0 decimales
        return signo * Math.round(num);
    // round(x * 10 ^ decimales)
    num = num.toString().split('e');
    num = Math.round(+(num[0] + 'e' + (num[1] ? (+num[1] + decimales) : decimales)));
    // x * 10 ^ (-decimales)
    num = num.toString().split('e');
    return signo * (num[0] + 'e' + (num[1] ? (+num[1] - decimales) : -decimales));
}
//function to save ajax with post or get
function saveajax(type,url,form,msj){
    if(type=="POST"){
        $.ajax({
            url: url,
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            type: 'POST',
            datatype: 'json',
            data: $("#"+form).serialize(),
            success: function(data) {
                if (data != "error") {
                    Swal.fire("¡Correcto!",msj,"success");
                } else {
                    Swal.fire("Error! No guardo correctamente, intente de nuevo");
                }
    
            },
            error: function(data) {
                console.log(data.responseText);
            }
        });
    }else{
        $.ajax({
            url: url,
            type: 'GET',
            datatype: 'json',
            data: $("#"+form).serialize(),
            success: function(data) {
                if (data != "error") {
                    Swal.fire("Correcto",msj,"success");
                } else {
                    Swal.fire("Error! No guardo correctamente, intente de nuevo");
                }
            },
            error: function(data) {
                console.log(data.responseText);
            }
        });
    }
   
}
//function to go back without caché
function goback(url){
    location.href =url;
}
//function to go back cache and restore before window
function gobackcache(){
    window.history.back();
}
//function to printDiv
function printDiv(nombreDiv) {
    Popup($('<div/>').append($("#"+nombreDiv).clone()).html());
}
//function to open new windows to print 
function Popup(data) {
    var mywindow = window.open('', 'my div', 'height=400,width=600');
    mywindow.document.write('<html><head><title>SISTEMA MÉDICO</title>');
    mywindow.document.write('<link rel="stylesheet" href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" type="text/css" />');
    mywindow.document.write('<style>   .hidden-paginator {display: none;} </style>');
    mywindow.document.write('</head><body >');
    mywindow.document.write(data);
    mywindow.document.write('</body></html>');
    mywindow.print();
    return true;
}
//disable button
function disabledButton(nombreDiv){
    $("#"+nombreDiv).attr("disabled", "disabled");
}