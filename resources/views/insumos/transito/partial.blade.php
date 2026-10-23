<script>
    var opcion = 0;

    $( document ).ready(function() {

        sumar_detalles();
    });

    $('body').on('click', '.des', function() {
        console.log($(this));
        //alert('eliminando')
        $(this).parent().parent().parent().parent().hide('slow').remove();
        var sources = sources1();
        console.log(sources);
        if (sources > 0) {

        } else {
            $("#bodega_saliente").attr('disabled', false);
        }
        //totales(0);
    });
    $('body').on('click', '.delete', function() {
        console.log($(this));

        $(this).parent().parent().remove();

        //totales(0);
    });

    function existenciax(e){
        var cantidad= parseFloat($(e).val());
        var existencias= parseFloat($(e).parent().parent().find('.existencia').val());
        //Swal.fire('aqui');
        if(cantidad>existencias) {
            Swal.fire('Mensaje','Esta sobrepasando las existencias de este producto en las bodegas','error');
            $(e).val('1');
        }
        if (cantidad<=0) {
            Swal.fire('Mensaje','La cantidad no puede ser 0 o menor','error');
            $(e).val('1');
        }
    }

    function lote(e){
        var lote= parseFloat($(e).val());
        if(lote!=""){
            Swal.fire('Mensaje','Ingrese el valor del lote','error');
            // $(e).val('1');
        }
    }

    function sumar_detalles() {
        var cont =1;
        $(".titulo").each(function (){
            $(this).parent().find(".numdetalle").html(cont);
            // console.log("gordomenestra "+$(this).parent().find(".numdetalle").html());
            cont++;
        });
    }

    function obtener_plantilla() {
        var plantilla = $('#plantilla').val();
        var bodega = $("#bodega_saliente").val();
        if (bodega != '') {
            $.ajax({
                type: "get",
                url: "{{route('transito.htmlSource')}}",
                data: {
                    'id': $('#plantilla').val(),
                    'bodega': $("#bodega_saliente").val(),
                    'plantilla': '1'
                },
                datatype: "html",
                success: function(datahtml, data) {
                    //console.log(data);
                    //$(e).val('');
                    $('#bodega_saliente').attr('readonly', 'redonly');
                    $("#heading").append(datahtml);
                    //totales(0)
                },
                error: function() {
                    //$(e).val('');
                    Swal.fire("Mensaje: ","No hay existencias en esta bodega","error");
                }
            });
        } else {
            alert('Primero seleccione la bodega');
        }

    }

    function add(e) {
        if (opcion==0) {
            $("#heading_details").val("");
            $('#inputpedido').prop('readonly', true);
        }
        opcion = 1;
        var bodega = $("#bodega_saliente").val();
        if (bodega != '') {
            var askdata= valida_add();
            // console.log(askdata);
            if(!askdata){
                $.ajax({
                    type: "get",
                    url: "{{route('transito.htmlSource')}}",
                    data: {
                        'id': $(e).val(),
                        'bodega': $("#bodega_saliente").val(),
                        'plantilla': '0'
                    },
                    datatype: "html",
                    success: function(datahtml, data) {
                        // console.log(data);
                        $(e).val('');
                        $('#bodega_saliente').attr('readonly', 'readonly');
                        $("#heading").append(datahtml);
                        sumar_detalles();
                        //totales(0)
                    },
                    error: function() {
                        $(e).val('');
                        Swal.fire("Error: ","Error al cargar item.","error");
                    }
                });
            }else{
                Swal.fire('Mensaje','Ya agrego un producto similar, ingrese otro','error');
                $("#inputserie").val("");
            }

        } else {
            $(e).val('');
            Swal.fire('Primero seleccione la bodega');
        }


    }

    function addPedido(e) {
        if (opcion==0) {
            $("#heading_details").val("");
            $('#inputserie').prop('readonly', true);
        }
        opcion = 1;
        var bodega = $("#bodega_saliente").val();
        if (bodega != '') {
            var askdata= valida_add();
            // console.log(askdata);
            if(!askdata){
                $.ajax({
                    type: "get",
                    url: "{{route('transito.htmlSource.pedido')}}",
                    data: {
                        'id': $(e).val(),
                        'bodega': $("#bodega_saliente").val(),
                        'plantilla': '0',
                        'pedido': $("#inputpedido").val(),
                    },
                    datatype: "html",
                    success: function(datahtml, data) {
                        console.log(data);
                        $(e).val('');
                        $('#bodega_saliente').attr('readonly', 'readonly');
                        $("#heading").append(datahtml);
                        sumar_detalles();
                        //totales(0)
                    },
                    error: function() {
                        $(e).val('');
                        Swal.fire("Error: ","Error al cargar item.","error");
                    }
                });
            }else{
                Swal.fire('Mensaje','Ya agrego un producto similar, ingrese otro','error');
                $("#inputserie").val("");
            }

        } else {
            $(e).val('');
            Swal.fire('Primero seleccione la bodega');
        }


    }

    function add_lote(e) {
        if (opcion==0) {
            $("#heading_details").val("");
            $('#inputpedido').prop('readonly', true);
        }
        opcion = 1;
        var bodega = $("#bodega_saliente").val();
        if (bodega != '') {
            var askdata= valida_add();
            // console.log(askdata);
            if(!askdata){
                $.ajax({
                    type: "get",
                    url: "{{route('transito.htmlSource_lote')}}",
                    data: {
                        'id': $(e).val(),
                        'bodega': $("#bodega_saliente").val(),
                        'plantilla': '0'
                    },
                    datatype: "html",
                    success: function(datahtml, data) {
                        // console.log(data);
                        $(e).val('');
                        $('#bodega_saliente').attr('readonly', 'readonly');
                        $("#heading").append(datahtml);
                        sumar_detalles();
                        //totales(0)
                    },
                    error: function() {
                        $(e).val('');
                        Swal.fire("Error: ","Error al cargar item.","error");
                    }
                });
            }else{
                Swal.fire('Mensaje','Ya agrego un producto similar, ingrese otro','error');
                $("#inputserie").val("");
            }
        } else {
            $(e).val('');
            Swal.fire('Primero seleccione la bodega');
        }
    }
    var memory_array=[];
    function valida_add(){
        var validate=false;
        var contador=0;

        $('.product').each(function(){
           var valor = $(this).val();
           var input = $("#inputserie").val();
           if (valor == input) {
                // console.log(input);
                validate =  true;
           }

        });

        // console.log('el contador es'+contador,memory_array);
        return validate;
    }

    function saveData(e) {
        console.log(e);
        if (validar()) {
            $(e).attr('disabled', 'disabled');
            $.ajax({
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                url: "{{route('transito.storenew')}}",
                data: $('#aform').serialize(),
                datatype: "json",
                success: function(data) {
                    console.log(data);

                    console.log("aqioooooasoaosoa")
                    if(data.respuesta=="success"){
                        console.log("aqiiiii");

                        Swal.fire("Mensaje: ","Guardado exitosamente","success");
                        setTimeout(()=>{
                            location.href = "{{route('transito.index_transito')}}";
                        }, 2000)
                    }else{
                        console.log("aquiii 2")
                        Swal.fire("Mensaje: ",data.msj, "error");
                    }

                   //
                },
                error: function() {
                    //$(e).val('');
                    Swal.fire("Error: ","Error al guardar el item.","error");
                }
            });
        }
    }

    function sources1() {
        var contador = 0;
        $('.details').each(function() {
            contador++;
        });

        return contador;
    }

    function update(e) {
        $(e).attr('disabled', 'disabled');
        $.ajax({
            type: "post",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            url: "{{route('transito.newupdate')}}",
            data: $('#aform').serialize(),
            datatype: "json",
            success: function(data) {
                console.log(data);
                if (data.res=='ok') {
                    Swal.fire('Mensaje','Guardado exitosamente','success');
                }
                location.href = "{{route('transito.index_transito')}}";
            },
            error: function() {
                Swal.fire("Error: ","Error al guardar el item.","error");
            }
        });
    }

    function validar() {
        var bodega_origen = $("#bodega_saliente").val();
        var bodega_destino = $("#bodega_entrante").val();
        // alert("O "+bodega_origen+" D "+bodega_destino);
        if(bodega_destino=="") {
            Swal.fire("Error: ","Selecciones la bodega de destino.","error");
            return false;
        }
        if (bodega_origen == bodega_destino) {
            Swal.fire("Error: ","La bodega de origen no puede ser la misma que la de destino.","error");
            return false;
        }
        return true;
    }



    const disabledTr = (i) => {
       let value = document.getElementById("check" + i).checked;
       if (value) {
           $("#del" + i).children().children().prop('disabled', false);
       } else {
           $("#del" + i).children().children().prop('disabled', true);
       }
   }

</script>
