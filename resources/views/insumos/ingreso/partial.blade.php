<script>
    $(document).ready(function () {
        totales(0);
        $('.select2').select2({
            tags: false
        });

        $('#fecha').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
            @if(@$pedido->fecha != "")
            defaultDate: '{{@$pedido->fecha}}'
            @else
            defaultDate: new Date()
            @endif

        });
        $('#findfechadesde').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
            defaultDate: new Date()
            //Important! See issue #1075

        });
        $('#findfechahasta').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
            defaultDate: new Date()
            //Important! See issue #1075

        });

        $('#vencimiento').datetimepicker({
            format: 'YYYY/MM/DD',
            @if(@$pedido->vencimiento != "")
            defaultDate: '{{@$pedido->vencimiento}}'
            @else
            defaultDate: new Date()
            @endif
        });

    });


    $('#tbl_detalles').DataTable({
        'paging': false,
        dom: 'lBrtip',
        'lengthChange': false,
        'searching': true,
        'ordering': false,
        'responsive': true,
        'info': false,
        'autoWidth': true,
        'columnDefs': [{
                "width": "5%",
                "targets": 0
            },
            {
                "width": "5%",
                "targets": 2
            },
            {
                "width": "10%",
                "targets": 6
            },
            {
                "width": "5%",
                "targets": 8
            }
        ],
        language: {
            zeroRecords: " "
        },
        buttons: [{
                extend: 'copyHtml5',
                footer: true
            },

            {
                extend: 'excelHtml5',
                footer: true,
                title: 'FACTURA CONGLOMERADA'
            },
            {
                extend: 'csvHtml5',
                footer: true
            },
            {
                extend: 'pdfHtml5',
                orientation: 'landscape',
                pageSize: 'LEGAL',
                footer: true,
                title: 'FACTURA CONGLOMERADA',
                customize: function (doc) {
                    doc.styles.title = {
                        color: 'black',
                        fontSize: '17',
                        alignment: 'center'
                    }
                }
            }
        ],
    });

    function valida_add(){
        var validate=false;

        $('.serie').each(function(){
            var valor = $(this).val();
            var input = $("#findserie").val();
            if (valor == input) {
                validate =  true;
            }
        });
        return validate;
    }

    function series_entabla() {
        const series = [];

        $('.serie').each(function(){
            series.push($(this).val());
        });
        return series;
    }

    function getLoader(pedido, tipo) {
        var series = series_entabla();
        if (!valida_add()){
            if (tipo == 1) {
                if ($('#findlote').val() == "" && $('#findcodigo').val() == "" && $('#findserie').val() == "") {
                    Swal.fire("Error: ", "No se ha ingresado ningun parametro para la consulta", "error");
                    return;
                }
            }
            if (tipo == 2) {
                if ($('#findpedido').val() == "") {
                    Swal.fire("Error: ", "Ingrese el numero del pedido", "error");
                    return;
                }
            }
            if (tipo == 3) {
                if ($('#findidproveedor').val() == "") {
                    Swal.fire("Error: ", "Ingrese el proveedor", "error");
                    return;
                }
                if ($('#findfechadesde').val() == "" && $('#findfechahasta').val() == "") {
                    Swal.fire("Error: ", "Ingrese la fecha desde o la fecha hasta", "error");
                    return;
                }
            }
            var pedido = $('#findpedido').val();
            $.ajax({
                type: "get",
                url: "{{route('ingreso.details.conglomerada.anterior')}}",
                data: {
                    'series': series,
                    'serie': $('#findserie').val(),
                    'pedido': $('#findpedido').val(),
                    'codigo': $('#findcodigo').val(),
                    'lote': $('#findlote').val(),
                    'tipo': tipo,
                    'proveedor': $('#findidproveedor').val(),
                    'desde': $('#findfechadesde').val(),
                    'hasta': $('#findfechahasta').val(),

                },
                datatype: "html",
                success: function (datahtml, data) {
                    console.log(data);
                    $('#findpedido').val('');
                    // $("#contes").append(datahtml);
                    var px = validPedido(pedido);
                    // if(tipo==2){

                    // }
                    //console.log(px);
                    if (!px) {
                        $("#tbldetalles").append(datahtml);
                        addClases();
                        totales(0);
                    } else {
                        Swal.fire("Ingrese otro pedido");
                    }
                    if (datahtml == "") {
                        Swal.fire("Mensaje: ", "No se encontraron resultados para la busqueda", "");
                    }

                },
                error: function () {
                    Swal.fire("Error: ", "ingrese otro pedido", "error");
                    $('#findpedido').val('');
                    // alert('Error, ingrese otro pedido');
                }
            });

        } else {
            Swal.fire("Mensaje: ", "La serie ya se encuentra ingresada", "");
        }
    }

    function validPedido(a) {
        console.log(a);
        var contador = 0;
        $('.pedido').each(function () {
            var pedido = $(this).val();
            if (pedido == a) {
                contador++;
            }

        });
        if (contador > 0) {
            return true;
        }
        return false;
    }

    function verificar(e) {
        var iva = $('option:selected', e).data("iva");
        var codigo = $(e).val(); //$('option:selected',e).data("codigo");
        var usadescuento = $('option:selected', e).data("descuento");
        var max = $('option:selected', e).data("maxdesc");
        var modPrecio = $('option:selected', e).data("precio");

        $(e).parent().children().closest(".codigo_producto").val($('option:selected', e).data("name"));
        //console.log('codigo', $(e).val());
        //console.log($(e).parent().next().next().children().closest(".cp"));
        /*
        if (modPrecio) {
            //$(e).parent().next().next().closest(".cp");
            console.log("modifica precio");
            $(e).parent().next().next().children().closest(".cp").attr("disabled", "disabled");
        } else {
            console.log("no modifca el precio");
            $(e).parent().next().next().children().closest(".cp").removeAttr("disabled");
        }*/
        if (!usadescuento) {
            $(e).parent().next().next().next().next().next().children().attr("readonly", "readonly");
            $(e).parent().next().next().next().next().children().attr("readonly", "readonly");
            $(e).parent().next().next().next().next().children().val(0);
            $(e).parent().next().next().next().next().next().children().val(0);
        } else {
            $(e).parent().next().next().next().next().next().children().removeAttr("readonly");
            $(e).parent().next().next().next().next().children().removeAttr("readonly");
            $(e).parent().next().next().next().next().next().children().val(0);
            $(e).parent().next().next().next().next().children().val(0);
        }
        $(e).parent().next().next().next().next().children().closest(".maxdesc").val(max);
        if (iva == '1') {
            $(e).parent().next().next().next().next().next().next().next().children().attr("checked",
                "checked");
        } else {
            $(e).parent().next().next().next().next().next().next().next().children().removeAttr("checked");
        }

        //cargarPrecios
        var tipo = $("#tipo_cliente").val();
        var selected = "";
        $.ajax({
            type: 'post',
            url: "{{route('precios')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                id: codigo
            },
            success: function (data) {
                $(e).parent().next().next().children().find('option').remove();
                $.each(data, function (key, value) {
                    if (tipo == value.nivel) {
                        selected = "selected";
                    } else {
                        selected = "";
                    }
                    $(e).parent().next().next().children().closest(".pneto").append(
                        '<option value=' + value.precio + ' ' + selected + '>' + value
                        .precio + '</option>');
                });

            },
            error: function (data) {
                console.log(data);
            }
        });
    }


    //cantidad
    //precio
    //copago
    //%descuento
    //descuento
    //precioneto
    $('body').on('blur', '.pneto', function () {
        // verificar(this);
        var cant = $(this).parent().prev().prev().prev().prev().prev().children().val();
        var copago = 0;
        var descuento = $(this).parent().next().next().next().children().val();
        var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
        total = redondeafinal(total);
        $(this).parent().next().next().next().children().val(total);
        totales(0);
    });
    $('body').on('active', '.pneto', function () {
        // verificar(this);
        var cant = $(this).parent().prev().prev().prev().prev().prev().children().val();
        var copago = 0;
        var descuento = $(this).parent().next().next().children().val();
        var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
        total = redondeafinal(total);
        $(this).parent().next().next().next().children().val(total);
        totales(0);
    });

    $('body').on('change', '.valor_iva', function () {
        var cant = $(this).parent().prev().children().val();
        var copago = $(this).parent().next().children().val();
        var descuento = $(this).parent().next().next().next().children().val();
        var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
        total = redondeafinal(total);
        $(this).parent().next().next().next().next().children().val(total);
        totales(0);
    });

    $('body').on('change', '.pneto', function () {
        // verificar(this);
        var cant = $(this).parent().prev().children().val();
        var copago = $(this).parent().next().children().val();
        var descuento = $(this).parent().next().next().next().children().val();
        var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
        total = redondeafinal(total);
        $(this).parent().next().next().next().next().children().val(total);
        totales(0);
    });
    $('body').on('change', '.cneto', function () {
        // verificar(this);
        var cant = $(this).val();
        var precio = $(this).parent().next().next().next().next().next().next().children().val();
        // console.log("this", $(this).parent().next().children().val());
        var copago = 0;
        //console.log("copago", copago);
        var descuento = $(this).parent().next().next().next().next().next().next().next().next()
            .children().val();
        var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
        total = redondeafinal(total);
        $(this).parent().next().next().next().next().next().next().next().next().next().children().val(
            total);

        totales(0);
    });

    function cnx(e) {
        var cant = $(e).val();
        var precio = $(e).parent().next().children().val();
        // console.log("e", $(e).parent().next().children().val());
        var copago = $(e).parent().next().next().children().val();
        //console.log("copago", copago);
        var descuento = $(e).parent().next().next().next().next().children().val();
        var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
        total = redondeafinal(total);
        $(e).parent().next().next().next().next().next().children().val(total);
    }

    function redondeafinal(num, decimales = 2) {
        var signo = (num >= 0 ? 1 : -1);
        num = num * signo;
        if (decimales === 0) //con 0 decimales
            return signo * Math.round(num); // to fixed num
        // round(x * 10 ^ decimales)
        num = num.toString().split('e');
        num = Math.round(+(num[0] + 'e' + (num[1] ? (+num[1] + decimales) : decimales)));
        // x * 10 ^ (-decimales)
        num = num.toString().split('e');
        return signo * (num[0] + 'e' + (num[1] ? (+num[1] - decimales) : -decimales));
    }

    function comprueba_exist() {
        $('.cneto').each(function (i) {
            var ths = $(this).val();
            if (isNaN(ths)) {
                ths = 0;
            }
        });
    }

    $('body').on('change', '.copago', function () {
        verificar(this);
        var cant = $(this).parent().prev().prev().children().val();
        var precio = $(this).parent().prev().children().val();

        var copago = $(this).val();
        //console.log("copago", copago);
        var descuento = $(this).parent().next().next().children().val();
        var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
        //console.log(total);
        total = redondeafinal(total);
        $(this).parent().next().next().next().children().val(total);

        totales(0);
    });


    $('body').on('change', '.pdesc', function () {

        var m = $(this).next().val();
        var cant = $(this).parent().prev().prev().prev().prev().prev().prev().prev().children().val();
        var precio = $(this).parent().prev().prev().children().val();
        var pdesc = $(this).val();

        var descuento = (parseInt(cant) * parseFloat(precio)) * pdesc / 100; //;
        $(this).parent().next().children().val(descuento.toFixed(2));
        var copago = $(this).parent().prev().children().val();
        var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
        total = redondeafinal(total);
        $(this).parent().next().next().children().val(total);
        totales(0);
    });
    $('body').on('change', '.desc', function () {
        var m = verificar(this);
        var cant = $(this).parent().prev().prev().prev().prev().prev().prev().prev().prev().children()
            .val();
        var precio = $(this).parent().prev().prev().children().val();
        /*if(pdesc> m){
            swal("El descuento no puede ser mayor a "+m+"%");
            $(this).val(m);
        }*/
        var descuento = $(this).val();
        verificar(this);
        console.log(cant, precio);
        var pdesc = 0;
        if (cant == 0 || precio == 0) {
            pdesc = 0;
        } else {
            pdesc = (descuento * 100) / (parseInt(cant) * parseFloat(precio));
        }
        //(parseInt(cant)* parseFloat(precio)) * pdesc /100;//;
        $(this).parent().prev().children().val(pdesc);
        var copago = $(this).parent().prev().prev().children().val();
        var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
        total = redondeafinal(total);
        $(this).parent().next().children().val(total);
        totales(0);
    });
    $('body').on('click', '.delete', function () {
        console.log($(this));

        $(this).parent().parent().remove();
        totales(0);
    });

    $('body').on('click', '.des', function () {
        console.log($(this));
        //alert('eliminando')
        $(this).parent().parent().parent().parent().hide('slow').remove()
        totales(0);
    });

    function totales(e) {
        console.log("calculando...");
        var subt12 = [];
        var subt0 = [];
        var descuento = [];
        var sb12 = 0;
        var sb0 = 0;
        var descuentosub0 = 0;
        var descuentosub12 = 0;
        var d = 0;
        var copagoTotal = 0;

        if (e == 0) {
            //$(".cneto").change();
            //console.log("sda");
            $('.cneto').each(function (i, obj) {
                var cant = $(this).val();
                $(this).parent().prev().find('.productos').attr('name', 'codigo[]');
                $(this).parent().prev().find('.codigo_producto').attr('name', 'nombre[]');
                //console.log(cant);
                //$(this).attr('name', 'cantidad[]');
                //var e = $(this).parent().prev().children().closest(".select2_cuentas");
                var precio1 = 0;
                var precio2 = 0;
                var precio3 = 0;
                var precio4 = 0;
                var precio5 = 0;
                var precioAut = 0;
                var tipo = $("#tipo_cliente").val();
                //console.log("el e es: ", e.val());
                var precio = $(this).parent().next().next().next().next().next().children().val();
                var copago = 0;
                //$(this).parent().next().next().children().attr('name', 'copago[]');
                var descuento = 0;
                var descuento = $(this).parent().next().next().next().next().next().next().children().val();console.log("vh vhvh");console.log(descuento);
                //$(this).parent().next().next().next().next().children().attr('name', 'descuento[]');
                
                d = d + parseFloat(descuento);
                var iva = $(this).parent().next().next().next().next().next().next().next().next()
                    .children().prop('checked');
                //console.log(iva);
                precio = precio != null ? precio : 0;
                //console.log(precio);
                var total = (parseInt(cant) * parseFloat(precio)) - parseFloat(0) - parseFloat(copago);

                //console.log("precio y cantidad" + total);

                if (iva == 1) {
                    subt12.push(total);
                    sb12 = sb12 + total;
                    descuentosub12 += parseFloat(descuento);

                } else {
                    subt0.push(total);
                    sb0 = sb0 + total;
                    descuentosub0 += parseFloat(descuento);
                }
                copagoTotal = parseFloat(copagoTotal) + parseFloat(copago);
                //aqui falta
                //console.log("subtotal12" + sb12);
                $("#subtotal_12").html(sb12.toFixed(2));
                $("#subtotal_0").html(sb0.toFixed(2));
                $("#descuento").html(d.toFixed(2));
                var descuento_total = descuentosub12 + descuentosub0;
                var sum = sb12 + sb0 - descuento_total;
                $("#base").html(sum.toFixed(2));
                var iva = 0.12;
                var ti = iva * sb12;
                if (d > 0) {
                    if (sb12 > 0) {
                        ti = iva * (sb12 - descuentosub12);
                    }

                }
                ti = redondeafinal(ti);
                $("#tarifa_iva").html(ti.toFixed(2, 2));
                var t = sb12 + sb0 + ti - d;
                //console.log(t);
                var totax = sum + ti;
                totax = redondeafinal(totax);
                copagoTotal = redondeafinal(copagoTotal);
                sb12 = redondeafinal(sb12);
                sb0 = redondeafinal(sb0);
                d = redondeafinal(d);

                $("#total").html(totax.toFixed(2, 2));
                $("#copagoTotal").html(copagoTotal.toFixed(2, 2));
                $("#subtotal_121").val(sb12);
                $("#subtotal_01").val(sb0);
                $("#descuento1").val(d);
                $("#tarifa_iva1").val(ti);
                $("#total1").val(totax);
                $("#totalc").val(copagoTotal);

            });

        }
    }

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;

        return true;
    }

    function nuevo(id, data, patient) {
        var nuevafila = $("#mifila").html();
        var rowk = document.getElementById("entrega" + id).insertRow(-1);
        //$('#mifila tr:last').before("<tr class='well'>"+nuevafila+"</tr>")
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = "id_orden[]";
        input.value = data;
        rowk.innerHTML = fila;
        rowk.append(input);
        var input2 = document.createElement('input');
        input2.type = 'hidden';
        input2.name = "paciente[]";
        input2.value = patient;
        rowk.append(input);
        rowk.className = "well";
    }
    /*$(".anadir").click(function(){
        alert("dsadada");
    });*/
    // 001-001-00990099
    $(".btn_add").click(function () {

        if ($("#frm").valid()) {
            //$(".print").css('visibility', 'visible');
            /* $(".btn_add").attr("disabled", true);*/
            $('.btn_add').prop('disabled', 'disabled');
            $.ajax({
                type: "POST",
                @if (@$pedido->id!="")
                    url: "{{route('ingreso.update_conglomerada')}}",
                @else
                    url: "{{route('ingreso.store_new')}}",
                @endif
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $("#frm").serialize(),
                beforeSend: function() {
                    Swal.fire({
                        text: "CARGANDO PORFAVOR ESPERE NO CERRAR LA PANTALLA",
                        imageUrl: `{{asset('/images/espera.gif')}}`,
                        imageWidth: 200,
                        imageHeight: 200,
                        imageAlt: 'Custom image',
                    })
                },
                datatype: "json",
                success: function (data) {
                    //console.log(data);


                    if(data == "Guardado correctamente"){
                        Swal.fire("Mensaje: ", "Guardado correctamente", "success");
                        setTimeout(function () {
                            location.href = "{{ route('codigo.barra')}}";
                        }, 3000);
                    }else{
                        Swal.fire("Mensaje: ", "Ocurrio un error, no se pudo guardar", "error");
                        
                    }

                   
                },
                error: function (data) {
                    alert(data);
                }
            });
        } else {
            alert("Existen campos vacios");
        }

    });

    $(".btn_generar_orden").click(function () {

        if ($("#frm").valid()) {
            //$(".print").css('visibility', 'visible');
            /* $(".btn_add").attr("disabled", true);*/
            $('.btn_add').prop('disabled', 'disabled');
            $.ajax({
                type: "POST",
                url: "{{route('ingreso.crear_orden_conglomerada')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $("#frm").serialize(),
                datatype: "json",
                success: function (data) {
                    //console.log(data);
                    Swal.fire("Mensaje: ", "Guardado correctamente", "success");
                    setTimeout(function () {
                        location.href = "{{ route('codigo.barra')}}";
                    }, 3000);
                },
                error: function (data) {
                    alert(data);
                }
            });
        } else {
            alert("Existen campos vacios");
        }

    });

    $(".btn_anular_envio_orden").click(function () {
        if ($("#frm").valid()) {
            $('.btn_add').prop('disabled', 'disabled');
            $.ajax({
                type: "POST",
                url: "{{route('ingreso.anular_envio_orden')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $("#frm").serialize(),
                datatype: "json",
                success: function (data) {
                    //console.log(data);
                    Swal.fire("Mensaje: ", "Guardado correctamente", "success");
                    setTimeout(function () {
                        location.href = "{{ route('codigo.barra')}}";
                    }, 3000);
                },
                error: function (data) {
                    alert(data);
                }
            });
        } else {
            alert("Existen campos vacios");
        }

    });

    function cantidad_permitida(a, e) {
        var cantidadmax = parseFloat(a);
        var cantidadnow = parseFloat($(e).val());
        if (cantidadnow > cantidadmax) {
            $(e).val(a);
            Swal.fire('Superaste la cantidad maxima');
            totales(0);
        }
        recalcularValores();
    }

    function addClases() {
        let names = document.getElementsByName('valor_iva[]');
        for(let i = 0; i < names.length ; i++){
            names[i].classList.add("valor_iva");
        }
    }

    const recalcularValores = () => {
        
        let cantidad = document.querySelectorAll(".cneto");
        let pneto = document.querySelectorAll(".preciouni");
        let valor_iva = document.querySelectorAll(".valor_iva");
        let totalUnitario = document.querySelectorAll(".totalUnitario")
        let total = 0;
        console.log(pneto, cantidad);
        for(let i = 0 ; i < cantidad.length; i++){
            let totalLinea = 0;
           /* if(valor_iva[i].checked){
                //tiene iva
                //totalLinea = (parseFloat(cantidad[i].value) * parseFloat(pneto[i].value)) * 1.12
                 //totalUnitario[i].value = totalLinea.toFixed(2)
            }else{
                //No tiene iva
                totalLinea = (parseFloat(cantidad[i].value) * parseFloat(pneto[i].value))
                 totalUnitario[i].value = totalLinea.toFixed(2)
            }*/
            totalLinea = (parseFloat(cantidad[i].value) * parseFloat(pneto[i].value))
                 totalUnitario[i].value = totalLinea.toFixed(2)
        }


    }
</script>
