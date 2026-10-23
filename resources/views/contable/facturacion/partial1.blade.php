
<script type="text/javascript">

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;

        return true;
    }

    function soloNumeros(e) {
        // capturamos la tecla pulsada
        var teclaPulsada = window.event ? window.event.keyCode : e.which;

        // capturamos el contenido del input
        var valor = e.value;
        if (teclaPulsada == 45 && valor.indexOf("-") == -1) {
            document.getElementById("inputNumero").value = "-" + valor;
        }

        // 13 = tecla enter
        // 46 = tecla punto (.)
        // Si el usuario pulsa la tecla enter o el punto y no hay ningun otro
        // punto
        if (teclaPulsada == 13 || (teclaPulsada == 46 && valor.indexOf(".") == -1)) {
            return true;
        }

        // devolvemos true o false dependiendo de si es numerico o no
        return /\d/.test(String.fromCharCode(teclaPulsada));
    }

    function seguro() {
        var seguro = $('#id_seguro').val();
        $.ajax({
            type: 'get',
            url: "{{asset('contable/facturacion/verificar/seguro/')}}" + '/' + seguro,
            datatype: 'html',
            success: function(data) {
                if (data == 1) {

                } else {

                }
                pagar();

            },
            error: function() {
                //alert('error al cargar');
            }
        });
    }

    function cargar_nivel() {

        var id_emp = $('#id_empresa').val();
        var xseguro = $('#id_seguro').val();

        $.ajax({
            type: 'post',
            url: "{{route('lista_nivel.seguro')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_seguro': xseguro,
                'id_empresa': id_emp
            },
            success: function(data) {
                console.log(data);

                if (data.value != 'no') {
                    //if(data.count()>0){

                    //alert("Prueba Ingreso");

                    if (xseguro != 0) {

                        //Muestra el Nivel del Seguro
                        //document.getElementById("ident_nivel").style.visibility = "visible";  
                        $("#id_nivel").empty();
                        $.each(data, function(key, registro) {
                            $("#id_nivel").append('<option value=' + registro.id_nivel + '>' + registro.nombre + '</option>');
                        });

                    } else {

                        $("#id_nivel").empty();

                    }

                } else {

                    //alert("Prueba Ingreso 2");
                    //Oculta el Nivel del Seguro
                    document.getElementById("ident_nivel").style.visibility = "hidden";

                }

            },
            error: function(data) {

            }
        });

    }

    function pagar() {
        var seguro = $('#id_seguro').val();
        var doctor = $('#id_doctor').val();
        $.ajax({
            type: 'get',
            url: "{{asset('contable/facturacion/verificar/pago/')}}" + '/' + seguro + '/' + doctor,
            datatype: 'html',
            data: $("#obs_id").serialize(),
            success: function(data) {
                $('#pago').val(data.trim());
            },
            error: function() {
                //alert('error al cargar');
            }
        });
    }

    function verificar(e) {
        var iva = $('option:selected', e).data("iva");
        var codigo = $('option:selected', e).data("codigo");
        var usadescuento = $('option:selected', e).data("descuento");
        var max = $('option:selected', e).data("maxdesc");
        var modPrecio = $('option:selected', e).data("precio");

        $(e).parent().children().closest(".codigo_producto").val(codigo);
        $(e).parent().children().closest(".iva").val(iva);
        //console.log($(e).parent().next().next().children().closest(".cp"));

        if (modPrecio) {
            //$(e).parent().next().next().closest(".cp");
            //console.log("modifica precio");
            $(e).parent().next().next().children().find(".cp").removeAttr("disabled");
        } else {
            //console.log("no modifca el precio");
            $(e).parent().next().next().children().find(".cp").attr("disabled", "disabled");
        }
         /* if (!usadescuento) {
            $(e).parent().next().next().next().next().next().children().attr("readonly", "readonly");
            $(e).parent().next().next().next().next().children().attr("readonly", "readonly");
            $(e).parent().next().next().next().next().children().val(0);
            $(e).parent().next().next().next().next().next().children().val(0);
        } else {
            $(e).parent().next().next().next().next().next().children().removeAttr("readonly");
            $(e).parent().next().next().next().next().children().removeAttr("readonly");
            $(e).parent().next().next().next().next().next().children().val(0);
            $(e).parent().next().next().next().next().children().val(0);
        } */
        //$(e).parent().next().next().next().next().children().closest(".maxdesc").val(max);

        if (iva == '1') {
            $(e).parent().next().next().next().next().next().next().next().children().attr("checked", "checked");
        } else {
            $(e).parent().next().next().next().next().next().next().next().children().removeAttr("checked");
        }
        //cargarPrecios
        var tipo = $("#tipo_cliente").val();
        var selected = "selected=''";
        $.ajax({
            type: 'post',
            url: "{{route('contable_precio_prod.tarifario_codigo')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_emp': '{{$empresa->id}}',
                'id_prod': codigo,
                'id_seg': $("#id_seguro").val(),
                'id_niv': $("#id_nivel").val()
            },
            success: function(data) {

                //alert(data.nivel);
                // console.log(data);
                // console.log('aqui va los precios de los productos')
                $(e).parent().next().next().children().find('option').remove();
             /*    $(e).parent().next().next().children().closest(".pneto").append('<option value=' + data.producto.id_producto + ' ' + selected + '>' + data.producto.precio_producto + '</option>'); */
                $(e).parent().next().next().children().val(data.producto.precio_producto);
              /*   $(e).parent().next().next().children().closest(".pneto").trigger('change'); */
                $(e).parent().find('.precioOriginal').val(data.producto.precio_producto);
               
               /*  $.each(data, function(key, value) {
                    console.log(value)
                    $(e).parent().next().next().children().closest(".pneto").append('<option value=' + value.precio_prod + ' ' + selected + '>' + value.precio_prod + '</option>');

                }); */
                // totales(0);
                // var valida = setTimeout(calcular_total, 2000); 
                calcular_total();
 
            },
            error: function(data) {
                console.log(data.responseText);
            }
        });
        // totales(0)
        // calcular_total();

    }
    
    var fila = $("#mifila").html();
    var fila2 = $("#mifilaf").html(); 

    function nuevoa() {
        var nuevafila = $("#mifila").html();
        
        var rowk = document.getElementById("agregar_cuentas").insertRow(-1);

        //$('#mifila tr:last').before("<tr class='well'>"+nuevafila+"</tr>")
        rowk.innerHTML = fila;
        $('.select2').select2({
            tags: false
        });
    }
    //chx
    $('body').on('change', '.chx', function() {  
        var chk = $(this).val(); 
        if (chk==0) {
            $(this).val(1); 
        } else{
            $(this).val(0);
        }
        calcular_total();
    });
    $('body').on('change', '.cneto', function() { 
        if ($(this).val()=="" || $(this).val() <0 ) { $(this).val("0.00") } 
        /* var descuento = 0;
        var cant = $(this).val();
        var precio = $(this).parent().next().children().val(); 
        var desc_porc = $(this).parent().next().next().children().val();
        var desc = $(this).parent().next().next().next().children().val();
        var total = (parseInt(cant) * (precio));
        if (desc_porc!=0 && total!=0) {
            descuento = (total * desc_porc) / 100;
        } else {
            descuento = desc;
        }
        if (total!=0) {
            total = (parseFloat(total) - parseFloat(descuento));
        } 
        $(this).parent().next().next().next().next().children().val(total.toFixed(2, 2)); */ 
        calcular_total();
    }); 
    $('body').on('change', '.pneto', function() { 
        if ($(this).val()=="" || $(this).val() <0 ) { $(this).val("0.00") } 
        /* var descuento = 0;
        var precio = $(this).val();
        var cant = $(this).parent().prev().children().val();  
        var desc_porc = $(this).parent().next().children().val();
        var desc = $(this).parent().next().next().children().val();
        var total = (parseInt(cant) * (precio)); 
        if (desc_porc!=0 && total!=0) {
            descuento = (total * desc_porc) / 100;
        } else {
            descuento = desc;
        }
        var total = ((parseInt(cant) * (precio)) - descuento); 
        $(this).parent().next().next().next().children().val(total.toFixed(2, 2));  */ 
        calcular_total();
    });

    $('body').on('change', '.pdesc', function() { 
        if ($(this).val()=="" || $(this).val() <0 ) { $(this).val("0.00") } 
        /* var descuento = 0;
        var desc = 0;
        var desc_porc = $(this).val();
        $(this).parent().next().children().val("0.00");  
        var precio = $(this).parent().prev().children().val(); 
        var cant = $(this).parent().prev().prev().children().val();   

        var total = (parseInt(cant) * (precio)); 
        if (desc_porc!=0 && total!=0) {
            descuento = (total * desc_porc) / 100;
        } else {
            descuento = desc;
        }
        if (total!=0) {
            var total = ((parseInt(cant) * (precio)) - descuento);
        } 
        $(this).parent().next().next().children().val(total.toFixed(2, 2));  */
        calcular_total();
    });

    $('body').on('change', '.desc', function() { 
        if ($(this).val()=="" || $(this).val() <0 ) { $(this).val("0.00") } 
        /* var descuento = 0;
        var desc_porc = 0;
        var desc = $(this).val();
        $(this).parent().prev().children().val("0.00");  
        var precio = $(this).parent().prev().prev().children().val(); 
        var cant = $(this).parent().prev().prev().prev().children().val();   

        var total = (parseInt(cant) * (precio)); 
        if (desc_porc!=0 && total!=0) {
            descuento = (total * desc_porc) / 100;
        } else {
            descuento = desc;
        }
        if (total!=0) {
            var total = ((parseInt(cant) * (precio)) - descuento);
        }
        $(this).parent().next().children().val(total.toFixed(2, 2));  */
        calcular_total();
    });

    $('#btn_pago').click(function(event) {
        id = document.getElementById('contador_pago').value;
        var midiv_pago = document.createElement("tr")
        midiv_pago.setAttribute("id", "dato_pago" + id);
        midiv_pago.setAttribute("class", "fpago");
        midiv_pago.innerHTML = '<td><input type="hidden" id="id_forma' + id +'" name="id_forma' + id +'"><select class="dogde" name="id_tip_pago' + id 
        + '" id="id_tip_pago' + id + '" style="width: 100px;height:20px" onchange="revisar_componentes(this,' + id + ');" required><option value="">Seleccione</option>@foreach($tipo_pago as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select><input type="hidden" id="visibilidad_pago' + id + '" name="visibilidad_pago' + id + '" value="1"></td><td><input type="date" class="dogde input-number" value="{{date('Y-m-d')}}" name="fecha_pago' + id + '" id="fecha_pago' + id + '" style="width: 120px;"></td><td><select  id="tipo_tarjeta' + id + '"  class="dogde" name="tipo_tarjeta' + id + '" style="width: 175px;height:20px"><option value="">Seleccione...</option> @foreach($tipo_tarjeta as $tipo_t) <option value="{{$tipo_t->id}}">{{$tipo_t->nombre}}@endforeach</select></td><td><input  type="text" name="numero_pago' + id + '" id="numero_pago' + id + '" style="width: 100px;" ></td><td><select class="dogde" name="id_banco_pago' + id + '" id="id_banco_pago' + id + '" style="width: 90px;height:20px"><option value="">Seleccione...</option>@foreach($lista_banco as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select></td><td><input  style="text-align:center;" type="checkbox" name="fi'+id+'" id="fi'+id+'" onchange="revision_total('+id+')" value="0" ></td><td><input  autocomplete="off" class="dogde" name="id_cuenta_pago' + id + '" id="id_cuenta_pago' + id + '" ></td><td><input class="dogde"  type="text" id="giradoa' + id + '" name="giradoa' + id + '"></td><td><input class="dogde text-right input-number fpago" type="text" id="valor' + id + '" name="valor' + id + '" style="width: 100px;" onblur="this.value=parseFloat(this.value).toFixed(2);"  value="0" onchange="revision_total(' + id + ')" onkeypress="return soloNumeros(this);" required></td><td><input class="dogde input-number fbase" type="text" readonly id="valor_base' + id + '" name="valor_base' + id + '" required onkeypress="return soloNumeros(event);" ></td><td><button style="text-align:center;" type="button" onclick="eliminar_form_pag(' + id + ')" class="btn btn-danger btn-gray delete btn-xs"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';

        document.getElementById('agregar_pago').appendChild(midiv_pago);
        id = parseInt(id);
        id = id + 1;
        document.getElementById('contador_pago').value = id;

    }); 

    $('body').on('change', '.psegu', function() {  
        if ($(this).val()=="" || $(this).val() <0 ) { $(this).val("0.00") } 
        var ppaci = 0;
        var psegu = $(this).val();  
        if (psegu==0 || psegu <0 || psegu > 101 ) {
            $(this).val("80.00"); 
            $(this).parent().next().children().val("20.00");  
        } else { 
            ppaci = 100 - parseFloat(psegu);
            psegu = parseFloat(psegu);
            $(this).val(psegu.toFixed(2, 2)); 
            $(this).parent().next().children().val(ppaci.toFixed(2, 2)); 
        }
        calcular_total();
    });

    $('body').on('change', '.ppaci', function() {  
        if ($(this).val()=="" || $(this).val() <0 ) { $(this).val("0.00") } 
        var psegu = 0;
        var ppaci = $(this).val();  
        if (ppaci==0 || ppaci <0 || ppaci > 101 ) {
            $(this).val("20.00"); 
            $(this).parent().prev().children().val("80.00");  
        } else { 
            psegu = 100 - parseFloat(ppaci);
            ppaci = parseFloat(ppaci);
            $(this).val(ppaci.toFixed(2, 2)); 
            $(this).parent().prev().children().val(psegu.toFixed(2, 2)); 
        }
        calcular_total();
    });

    $('body').on('change', '.pdeducible', function() {  
        if ($(this).val()=="" || $(this).val() <0 ) { $(this).val("0.00") } 
        calcular_total();
    });

    $('body').on('change', '.pfee', function() {  
        if ($(this).val()=="" || $(this).val() <0 ) { $(this).val("0.00") } 
        calcular_total();
    });

    $('body').on('change', '.pdeducible', function() {  
        if ($(this).val()=="" || $(this).val() <0 ) { $(this).val("0.00") } 
        calcular_total();
    });

    $('body').on('click', '.delete', function() {
        $(this).parent().parent().remove();
        calcular_total();
    });

    $('body').on('click', '#interesadoPositivo', function() { 
        calcular_total();
    });
    $('body').on('click', '#interesadoNegativo', function() { 
        $('#oda').val("");
        calcular_total();
    });
    $('body').on('change', '#id_seguro', function() {  
        calcular_total();
    });

    function calcular_total() {  
        var id_seguro = $("#id_seguro").val(); 
        var acum_subtotal = 0;
        var acum_subtotal0 = 0;
        var acum_descuento = 0;
        var acum_sin_impuesto = 0;
        var acum_iva = 0;
        var acum_total = 0;
        var acum_tot_pag_seg = 0;
        var acum_cobr_segu = 0;
        var impuesto = 0.12;
        var por_impuesto = 1.12;
        var oda = $("#interesadoPositivo").val();
        var num_oda = $("#oda").val();
        $('#agregar_cuentas tr').each(function () {  
            var cant = $(this).find("td").eq(1).children().val();
            var precio = $(this).find("td").eq(2).children().val();
            // var total = $(this).find("td").eq(3).children().val(); 
            var total = (parseInt(cant) * (precio)); 
            $(this).find("td").eq(3).children().val(total.toFixed(2,2));  // total
            var por_seg = $(this).find("td").eq(4).children().val();
            var por_pac = $(this).find("td").eq(5).children().val(); 
            var deducible = $(this).find("td").eq(6).children().val();  
            var fee = $(this).find("td").eq(7).children().val();  
            var desc_porc = $(this).find("td").eq(8).children().val(); 
            var desc = $(this).find("td").eq(9).children().val();  
            var chk_iva = $(this).find("td").eq(12).children();  
            var val_por_seg = 0;
            var val_por_pac = 0;
            var val_pag_pac = 0;
            var total_pag_pac = 0; 
            var total_pag_seg = 0; 
            var descuento = 0;
            var obj_cobrar_paciente = $(this).find("td").eq(10).children();
            var obj_cobrar_seguro = $(this).find("td").eq(11).children();
            var obj_deducible = $(this).find("td").eq(6).children();
            var obj_fee = $(this).find("td").eq(7).children();

            if (por_seg!=0&&por_pac!=0&&oda=='si'&&num_oda!="") {
                if (id_seguro==4) {
                    console.log("Humana"); 
                    if (total==0) {
                        obj_cobrar_seguro.val('0.00');  // cobrar seguro
                        obj_cobrar_paciente.val('0.00');  // cobrar paciente
                    } else {
                        val_por_seg = ((parseFloat(total) - parseFloat(deducible)) * por_seg)/100;
                        val_por_pac = ((parseFloat(total) - parseFloat(deducible)) * por_pac)/100; 
                        total_pag_pac = parseFloat(deducible) + parseFloat(val_por_pac) + parseFloat(fee);  
                        total_pag_seg = val_por_seg;
                        obj_cobrar_seguro.val(total_pag_seg.toFixed(2,2)); // cobrar paciente total_pag_pac
                        obj_cobrar_paciente.val(total_pag_pac.toFixed(2,2)); // cobrar seguro 
                    }
                } else if (id_seguro==7) { // salud
                    if (total==0) {
                        obj_cobrar_seguro.val('0.00');  // cobrar seguro
                        obj_cobrar_paciente.val('0.00');  // cobrar paciente
                    } else {
                        val_por_seg = (parseFloat(total) * parseFloat(por_seg))/100;
                        val_por_pac = (parseFloat(total) * parseFloat(por_pac))/100;
                        total_pag_pac = parseFloat(deducible) + parseFloat(val_por_pac) + parseFloat(fee); 
                        total_pag_seg = parseFloat(val_por_seg) - parseFloat(deducible) - parseFloat(fee);  
                        obj_cobrar_seguro.val(total_pag_seg.toFixed(2,2));  // cobrar paciente
                        obj_cobrar_paciente.val(total_pag_pac.toFixed(2,2));  // cobrar seguro
                    }
                } else {
                    if (total==0) {
                        obj_cobrar_seguro.val('0.00');  // cobrar seguro
                        obj_cobrar_paciente.val('0.00');  // cobrar paciente
                    } else {
                        val_por_seg = (parseFloat(total) * parseFloat(por_seg))/100;
                        val_por_pac = (parseFloat(total) * parseFloat(por_pac))/100;
                        total_pag_pac = parseFloat(deducible) + parseFloat(val_por_pac) + parseFloat(fee); 
                        total_pag_seg = parseFloat(val_por_seg) - parseFloat(deducible); 
                        obj_cobrar_seguro.val(total_pag_seg.toFixed(2,2));  // cobrar paciente
                        obj_cobrar_paciente.val(total_pag_pac.toFixed(2,2));  // cobrar seguro
                    } 
                }
            }else{
                val_por_seg = 0;    val_por_pac = 0;    total_pag_pac = total;
                obj_deducible.val("0.00"); // deducible
                obj_fee.val("0.00"); // fee
                obj_cobrar_seguro.val("0.00"); // cobrar paciente
                obj_cobrar_paciente.val("0.00");  // cobrar seguro
            } 

            if (desc_porc!=0 && total!=0) {
                descuento = (total_pag_pac * desc_porc) / 100;
            } else {
                descuento = desc;
            }

            total_pag_pac = parseFloat(total_pag_pac) - parseFloat(descuento);
            obj_cobrar_paciente.val(total_pag_pac.toFixed(2,2));

            if (parseInt(chk_iva.val())==1) { 
                acum_subtotal += total_pag_pac;
            } else { 
                acum_subtotal0 += total_pag_pac;
                acum_sin_impuesto += total_pag_pac;
            }

            acum_descuento += parseFloat(descuento);
            var cobr_segu = obj_cobrar_seguro.val();
            acum_cobr_segu += parseFloat(cobr_segu); 
            acum_tot_pag_seg += 0;
        });

        $("#subtotal_12").html(acum_subtotal.toFixed(2));
        $("#subtotal_0").html(acum_subtotal0.toFixed(2));
        $("#descuento").html(acum_descuento.toFixed(2));
        $("#base").html(acum_sin_impuesto.toFixed(2)); 
        acum_iva = parseFloat(impuesto);
        acum_total = parseFloat(acum_subtotal) + parseFloat(acum_subtotal0) - parseFloat(acum_descuento) + parseFloat(acum_iva);
        acum_iva =  parseFloat(acum_subtotal) * parseFloat(impuesto);
        $("#tarifa_iva").html(acum_iva.toFixed(2)); 
        $("#total").html(acum_total.toFixed(2)); 
        $("#copagoTotal").html(acum_cobr_segu.toFixed(2)); 
        // $('#valorTotals').val(acum_total.toFixed(2,2));
        // hidden
        $("#subtotal_121").val(acum_subtotal.toFixed(2));
        $("#subtotal_01").val(acum_subtotal0.toFixed(2));
        $("#descuento1").val(acum_descuento.toFixed(2));
        $("#base1").val(acum_sin_impuesto.toFixed(2)); 
        $("#tarifa_iva1").val(acum_iva.toFixed(2)); 
        $("#total1").val(acum_total.toFixed(2)); 
        $("#totalc").val(acum_cobr_segu.toFixed(2)); 
        return true;
    }

    function revision_total(id) {
        var fi = document.getElementById("fi" + id);
        var valor = $('#valor' + id).val();
        if(valor>0){
            if (fi.checked == true) {
            tipo = $('#id_tip_pago' + id).val();
            if (tipo == '4') {
                ntotal = valor * 1.07;
            } else if (tipo == '6') {
                ntotal = valor * 1.02;
            } else {
                ntotal = valor * 1;
            }
            $('#valor_base' + id).val(ntotal.toFixed(2));
            var tos= parseFloat($('#total1').val());
            var permiso= ntotal- valor;
            if(permiso<0){
                permiso= permiso * -1;
            }
            permiso= permiso.toFixed(2,2);
            if(valor>0){
                snew(permiso);
            }

            } else {
                ntotal = valor * 1;
                $('#valor_base' + id).val(ntotal.toFixed(2));
            }
            suma_total();
        }

    }

    function revisar_componentes(e, id) {
        metodo = $('#id_tip_pago' + id).val();
        if (metodo == 1) {
            $("#tipo_tarjeta" + id).prop('disabled', true);
            $("#numero" + id).prop('disabled', true);
            $("#tipo_tarjeta" + id).prop('disabled', true);
            $("#id_banco" + id).prop('disabled', true);
            $("#fi" + id).prop('disabled', true);
            revision_total(id);
        } else if (metodo == 2) {
            $("#tipo_tarjeta" + id).prop('disabled', true);
            $("#numero" + id).prop('disabled', false);
            $("#tipo_tarjeta" + id).prop('disabled', true);
            $("#id_banco" + id).prop('disabled', false);
            $("#fi" + id).prop('disabled', true);
            revision_total(id);
        } else if (metodo == 3) {
            $("#tipo_tarjeta" + id).prop('disabled', true);
            $("#numero" + id).prop('disabled', false);
            $("#tipo_tarjeta" + id).prop('disabled', true);
            $("#id_banco" + id).prop('disabled', false);
            $("#fi" + id).prop('disabled', true);
            revision_total(id);
        } else if (metodo == 4) {
            $("#tipo_tarjeta" + id).prop('disabled', false);
            $("#numero" + id).prop('disabled', false);
            $("#tipo_tarjeta" + id).prop('disabled', false);
            $("#id_banco" + id).prop('disabled', false);
            $("#fi" + id).prop('disabled', false);
            revision_total(id);
        } else if (metodo == 5) {
            $("#tipo_tarjeta" + id).prop('disabled', false);
            $("#numero" + id).prop('disabled', false);
            $("#tipo_tarjeta" + id).prop('disabled', false);
            $("#id_banco" + id).prop('disabled', false);
            $("#fi" + id).prop('disabled', false);
            revision_total(id);
        } else if (metodo == 6) {
            $("#tipo_tarjeta" + id).prop('disabled', false);
            $("#numero" + id).prop('disabled', false);
            $("#tipo_tarjeta" + id).prop('disabled', false);
            $("#id_banco" + id).prop('disabled', false);
            $("#fi" + id).prop('disabled', false);
            revision_total(id);
        }
    }

    function suma_total() {
        var contador = $('#contador_pago').val();
        var sumador = 0;
        var sumador_sin = 0;

        for (var i = 0; i < contador; i++) {
            if ($('#visibilidad_pago' + i).val() == 1) {
                sumador_sin = sumador_sin + parseFloat($('#valor_base' + i).val());
                sumador = sumador + parseFloat($('#total' + i).val());
            }
        }
        console.log("fpt: "+sumador)
        $('#total_sin_tarjeta').val(sumador_sin.toFixed(2));
        $('#total_final1').val(sumador.toFixed(2));
        $('#valorTotals').val(sumador_sin.toFixed(2));
        
    }

    function guardar(e){
       if($('#crear_form').valid()){
        recalcular_fpago();
        var valor_totalp= parseFloat($('#total1').val());//valorTotals
        var total_pago= valor_totalp.toFixed(2,2);
        if(total_pago==parseFloat($("#valorTotals").val())){
            $.ajax({
                type: 'post',
                url: "{{route('facturacion.store_new')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#crear_form").serialize(),
                success: function(data) {
                    console.log(data);
                    swal('Mensaje','Guardado correctamente','success');
                    $(e).attr('disabled','disabled');
                    $('#printer').removeClass('visible');
                    $('#id_store').val(data.id);
                    window.open('{{url("comprobante/orden/venta/")}}/'+data.id,'_blank');
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }else{
            swal('Mensaje','No coinciden los valores de pago con el total, Total pago: '+total_pago+' Total Recibo: '+$('#valorTotals').val(),'error');
        }
        
       }
        
                
    }

    function actualizar(e){
       if($('#crear_form').valid()){
        recalcular_fpago();
        var valor_totalp= parseFloat($('#total1').val());//valorTotals
        var total_pago= valor_totalp.toFixed(2,2);
        if(total_pago==parseFloat($("#valorTotals").val())){
            $.ajax({
                type: 'post',
                url: "{{route('facturacion.update_new')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#crear_form").serialize(),
                success: function(data) {
                    console.log(data);
                    swal('Mensaje','Guardado correctamente','success');
                    $(e).attr('disabled','disabled');
                    $('#printer').removeClass('visible');
                    $('#id_store').val(data.id);
                    window.open('{{url("comprobante/orden/venta/")}}/'+data.id,'_blank');
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }else{
            swal('Mensaje','No coinciden los valores de pago con el total, Total pago: '+total_pago+' Total Recibo: '+$('#valorTotals').val(),'error');
        }
        
       }
        
                
    }

    function recalcular_fpago(){
        var total_pagos = 0;
        $('.fpago').each(function(i, obj) {
            total_pagos = parseFloat(total_pagos) + parseFloat($(this).val());
        });
        console.log('lo ultimo en la forma de pago es : '+total_pagos);
        $("#valor_totalPagos").val(total_pagos);
    }

    function changeSobrante(e){
        var total= parseFloat($("#valorTotals").val());
        var nuevo= parseFloat($(e).val());
        tot=0;
        if(total>0){
            if(nuevo<=total){
                var tot= (total-nuevo);
            }else{
                var tot= (total-nuevo) * (-1);
            }
            
            $('#diferencia').val(tot.toFixed(2,2));
        }else{
            $(e).val('0.00');
            swal('Ingrese valores en los productos');
        }
        
    }

    function limpiar() {
        $("#datos_tarjeta_credito").hide();
        $("#datos_tarjeta_debito").hide();
        $("#datos_cheque").hide();
        $("#valor_tarjetadebito").val('');
        $("#valor_cheque").val('');
        $("#valor_efectivo").val('');
        $("#valor_tarjetacredito").val('');
        $("#numero_oda").val('0');

    }

    function eliminar_form_pag(valor) {
        var dato_pago1 = "dato_pago" + valor;
        var nombre_pago2 = 'visibilidad_pago' + valor;
        document.getElementById(dato_pago1).style.display = 'none';
        document.getElementById(nombre_pago2).value = 0;
        suma_total();

        // recalcular_fpago();

    }

    function obtener_fecha() {

        //obtenemos la fecha actual
        var now = new Date();
        var day = ("0" + now.getDate()).slice(-2);
        var month = ("0" + (now.getMonth() + 1)).slice(-2);
        var today = now.getFullYear() + "-" + (month) + "-" + (day);
        $("#fecha").val(today);

    }

    function crear_factura() {
        $('#crear_recibo').button('loading');

        var formulario = document.forms["crear_form"];

        //Datos Generales
        var id_emp = formulario.id_empresa.value;
        /*var sucurs = formulario.sucursal.value;
        var punt_emision = formulario.punto_emision.value;*/


        //Datos Paciente
        var cedula = formulario.idpaciente.value;
        var nombre_paciente = formulario.nombres.value;
        var seguro_paciente = formulario.id_seguro.value;


        //Datos Clientes
        var tipo_identicacion = formulario.tipo_identificacion.value;
        var ced_cliente = formulario.cedula.value;
        var raz_social = formulario.razon_social.value;
        var ciud_cliente = formulario.ciudad.value;
        var dire_cliente = formulario.direccion.value;
        var telf_cliente = formulario.telefono.value;
        var email_cliente = formulario.email.value;
        var numero_oda = formulario.numero_oda.value;
        var copago = formulario.copago;




        //Concepto y Nota

        //var concepto = formulario.concepto.value;
        //var nota = formulario.nota.value;

        //Pago
        var pago = formulario.pago.value;
        var caja_pago = formulario.caja.value;

        var msj = "";
        var msj2 = "";

        //Datos Generales

        if (id_emp == "") {

            msj = msj + "Por favor, Seleccione la Empresa<br/>";
        }
        //alert(copago.checked);
        if (copago.checked == true) {

            if (numero_oda == '0' || numero_oda == '') {
                msj = msj + "Por favor, Ingrese el número de Oda<br/>";
            }
        }

        /*if(sucurs == ""){

           msj = msj + "Por favor, Seleccione la Sucursal<br/>";
        }

        if(punt_emision == ""){
           msj = msj + "Por favor, Seleccione el Punto de Emision<br/>";
        }*/

        //Paciente
        if (cedula == "") {
            msj += "Por favor,Ingrese la cedula del paciente<br/>";
        }
        if (nombre_paciente == "") {
            msj += "Por favor,Ingrese el nombre del paciente<br/>";
        }
        if (seguro_paciente == "") {
            msj += "Por favor,Seleccione el seguro paciente<br/>";
        }

        //Cliente
        if (tipo_identicacion == "") {
            msj += "Por favor,Selecione el Tipo de Identificaciòn<br/>";
        }
        if (ced_cliente == "") {
            msj += "Por favor,Ingrese la cedula del cliente<br/>";
        }
        if (raz_social == "") {
            msj += "Por favor,Ingrese la razon social<br/>";
        }
        if (ciud_cliente == "") {
            msj += "Por favor,Ingrese la ciudad del cliente<br/>";
        }

        if (dire_cliente == "") {
            msj += "Por favor,Ingrese la direccion cliente<br/>";
        }
        if (telf_cliente == "") {
            msj += "Por favor,Ingrese el telefono del cliente<br/>";
        }
        if (email_cliente == "") {
            msj += "Por favor,Ingrese el email del cliente<br/>";
        }


        //Pago
        if (pago == "") {
            msj += "Por favor,Ingrese el pago\n";
        }
        if (caja_pago == "") {
            msj += "Por favor,Ingrese la caja a la cual se va a pagar<br/>";
        }

        var i;
        var max = document.getElementById('agregar_pago').rows.length;
        for (i = 0; i < max; i++) {
            var tipo_pago = document.getElementById('id_tip_pago' + i).value;
            var numero = document.getElementById('numero' + i).value;
            var id_banco = document.getElementById('id_banco' + i).value;
            var tipo_tarjeta = document.getElementById('tipo_tarjeta' + i).value;
            if (tipo_pago == '2') {

                if (numero == "") {
                    msj += "Por favor,Ingrese el número del cheque<br/>";
                }
                if (id_banco == "") {
                    msj += "Por favor,Seleccione el Banco<br/>";
                }
            }
            if (tipo_pago == '4') {
                if (tipo_tarjeta == "") {
                    msj += "Por favor,Ingrese el Tipo de tarjeta<br/>";
                }

                if (id_banco == "") {
                    msj += "Por favor,Seleccione el Banco<br/>";
                }
            }
        }

        if (msj != "") {
            $('#crear_recibo').button('reset');
            swal({
                title: "Error!",
                type: "error",
                html: msj
            });
            return false;
        }

        if (msj2 != "") {
            $('#crear_recibo').button('reset');
            swal({
                title: "Error!",
                type: "error",
                html: msj2
            });
            return false;
        }

        var fecha = document.getElementById('fecha').value;

        var unix = Math.round(new Date(fecha).getTime() / 1000);

        $.ajax({
            type: 'post',
            url: "{{route('facturacion.guardar_orden')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $("#crear_form").serialize(),
            success: function(data) {

                //console.log(data);
                $("#ride").attr("href", );
                window.open("{{asset('/comprobante/orden/venta')}}/" + data.id_orden, '_blank ');
                window.location.href = "{{asset('/agenda/calendario/')}}/{{$agenda->id_doctor1}}/" + unix;
                $('#crear_recibo').button('reset');

            },
            error: function(data) {
                console.log(data);

            }
        });
    }

    $("#cedula").autocomplete({
        source: function(request, response) {
            $.ajax({
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                url: "{{route('facturacion.buscar_cliente')}}",
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        minLength: 3,
    });

    function buscar() {
        var cedula_1 = $('#cedula').val();
        var pasaporte = parseInt($('#tipo_identificacion').val());
        var alerta = 0;
        if (cedula_1.length < 10) {
            alert('La Cantidad de Digitos no es Correcta');
        }
        var cedula = validarCedula($('#cedula').val());
        if (cedula == false && pasaporte != 6 && pasaporte != 8) {
            /* $('#cedula').val('');
            $('#razon_social').val('');
            $('#ciudad').val('');
            $('#direccion').val('');
            $('#telefono').val('');
            $('#email').val('');
            $('#caja').val(''); */

        } else {

            $.ajax({
                type: 'post',
                url: "{{route('facturacion.cliente')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                type: 'POST',
                datatype: 'json',
                data: $("#cedula"),
                success: function(data) {
                    if (data.value != 'no') {
                        $('#razon_social').val(data.nombre);
                        $('#ciudad').val(data.ciudad);
                        $('#direccion').val(data.direccion);
                        $('#telefono').val(data.telefono);
                        $('#email').val(data.email);
                        $('#caja').val(data.caja);
                    }
                    console.log(data);
                },
                error: function(data) {
                    console.log(data);
                }
            });

        }

    }

    function fun(){
        var id= $("#id_store").val();
        if(id!=0){
            window.open('{{url("comprobante/orden/venta/")}}/'+id,'_blank');
        }else{
            swal("Compruebe si el guardado fue correcto");
        }
        
    }

  

</script>