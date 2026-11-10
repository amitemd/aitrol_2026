<script src="{{asset('assets/vendors/sweetalert2/sweetalert2.min.js')}}"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>

<script type="text/javascript">
    var receiptEditable = {{ (int) $orden->estado === -1 ? 'true' : 'false' }};
    function requireEditableReceipt(showMessage) {
        if (receiptEditable) return true;
        if (showMessage !== false) {
            receiptAlert({title: 'Recibo emitido', icon: 'info', text: 'Debe devolver el recibo antes de modificarlo.'});
        }
        return false;
    }

    function receiptAlert(options) { options.icon = options.icon || options.type; delete options.type; return Swal.fire(options); }
    $(document).off('show.bs.modal.receipt', '#tarifario_nivel').on('show.bs.modal.receipt', '#tarifario_nivel', function(event) {
        var remote = $(event.relatedTarget).attr('data-remote');
        if (remote) $(this).find('.modal-content').load(remote);
    });
    //////////////// NUEVAS FUNCIONES DE INGRESO DE PRODUCTOS
    lista_productos();
    lista_formas_pago();

    function actualizar_cliente() {
        if (!requireEditableReceipt()) return;
        //alert(id_cliente);
        $.ajax({
            type: 'get',
            url:"{{ route('nuevorecibocobro.modal_actualiza_cliente', ['id_cliente' => $orden->identificacion, 'id_orden' => $orden->id])}}",
            datatype: 'json',
            success: function(data) {
              $('#datos_factura').empty().html(data);
              $('#modal_datosfacturas').modal();
            },
            error: function(data) {
              //console.log(data);
            }
        });
    }

    $("#buscadorasesor").autocomplete({
        source: function( request, response ) {

            $.ajax({
                url:"{{route('ticketpermisos.vh_buscar_usuario')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                data: {
                    term: request.term,
                    tipo: 7,
                },
                dataType: "json",
                type: 'post',
                success: function(data){
                    response(data);
                }
            })
        },
        minLength: 5,
        select: function(data, ui){
            
            //console.log("++"+ui.item.codigo);
            $('#idasesor').val(ui.item.id);
            //enfermeria_nombre_2(ui.item.codigo);
        }
    } );

    function lista_formas_pago() {
        $('#detalles_forma_pago').html('<p role="status">Cargando formas de pago…</p>');
        $.ajax({
            url: "{{route('nuevorecibocobro.formas_pago',['id' => $orden->id])}}",
            type: 'get',
            datatype: 'html',
            success: function(data) {
                //console.log(data);
                $('#detalles_forma_pago').empty().html(data);
            },
            error: function(data) {
                $('#detalles_forma_pago').html('<div class="alert alert-danger" role="alert">No se pudieron cargar las formas de pago. <button type="button" class="btn btn-sm btn-light" onclick="lista_formas_pago()">Reintentar</button></div>');
            }
        })
    }

    function lista_productos(silent) {
        if (!silent) $('#detalles_orden').html('<p class="re-loading" role="status"><i class="fas fa-circle-notch fa-spin"></i> Cargando productos…</p>');
        $.ajax({
            url: "{{route('nuevorecibocobro.detalles',array('id' => $orden->id,'_v3' => isset($receiptIsV3) && $receiptIsV3 ? 1 : 0))}}",
            type: 'get',
            datatype: 'html',
            success: function(data) {
                //console.log(data);
                $('#detalles_orden').empty().html(data);
            },
            error: function(data) {
                if (data.status === 409 && data.responseJSON && data.responseJSON.redirect) {
                    window.location.assign(data.responseJSON.redirect);
                    return;
                }
                $('#detalles_orden').html('<div class="alert alert-danger" role="alert">No se pudieron cargar los productos. <button type="button" class="btn btn-sm btn-light" onclick="lista_productos()">Reintentar</button></div>');
            }
        })
    }

    function seleccionar_producto() {
        if (!requireEditableReceipt()) return;
        var producto_nuevo = $('#producto_nuevo').val();

        $.ajax({
            type: 'post',
            url: "{{route('nuevorecibocobro.guardar_producto')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'producto_nuevo': producto_nuevo,
                'id': '{{ $orden->id }}',
                '_v3': {{isset($receiptIsV3) && $receiptIsV3 ? 1 : 0}}
            },
            success: function(data) {
                lista_productos(true);
                console.log(data);
            },
            error: function(xhr) {
                var message = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'No se pudo agregar el producto.';
                receiptAlert({title: 'Producto no agregado', icon: 'error', text: message});
            }
        });

    }

    function seleccionar_metodo() {
        if (!requireEditableReceipt()) return;
        var fp_metodo_nuevo = $('#fp_metodo_nuevo').val();
        var fp_fecha_nueva = $('#fp_fecha_nueva').val();
        var fp_tarjetanueva = $('#fp_tarjetanueva').val();
        var fp_numero_nuevo = $('#fp_numero_nuevo').val();
        var fp_banco = $('#fp_banco').val();
        var fp_cuenta_nueva = $('#fp_cuenta_nueva').val();
        var fp_girado_nuevo = $('#fp_girado_nuevo').val();
        var fp_valor_nuevo = $('#fp_valor_nuevo').val();
        var fp_ingresar = $('#fp_ingresar').val();
        var selectedPayment = $('#fp_metodo_nuevo option:selected');
        var requiereBanco = selectedPayment.data('requiere-banco') == 1;
        var requiereReferencia = selectedPayment.data('requiere-referencia') == 1;
        var requiereTarjeta = selectedPayment.data('tarjeta') == 1;

        var msj = "";

        if (requiereTarjeta && fp_tarjetanueva == "") msj += "Por favor, selecciona la tarjeta.<br/>";
        if (requiereReferencia && fp_numero_nuevo == "") msj += "Por favor, ingresa el número de referencia.<br/>";
        if (requiereBanco && fp_banco == "") msj += "Por favor, selecciona el banco.<br/>";

        if (fp_fecha_nueva == "") {
            msj += "Por favor,Ingrese la Fecha<br/>";
        }
        if (fp_valor_nuevo == "") {
            msj += "Por favor,Ingrese el Valor<br/>";
        }
        if (fp_metodo_nuevo == "") {
            msj += "Por favor,Seleccione el método<br/>";
        }


        if(msj == ''){

            $.ajax({
                type: 'post',
                url: "{{route('nuevorecibocobro.guardar_formapago')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'id': '{{ $orden->id }}',
                    'fp_metodo_nuevo': fp_metodo_nuevo,
                    'fp_fecha_nueva': fp_fecha_nueva,
                    'fp_tarjetanueva': fp_tarjetanueva,
                    'fp_numero_nuevo': fp_numero_nuevo,
                    'fp_banco': fp_banco,
                    'fp_cuenta_nueva': fp_cuenta_nueva,
                    'fp_girado_nuevo': fp_girado_nuevo,
                    'fp_valor_nuevo': fp_valor_nuevo,
                    'fp_ingresar': fp_ingresar, 
                },
                success: function(data) {
                    lista_formas_pago();
                    console.log(data);
                },
                error: function(data) {
                    console.log(data);
                    var message = data.responseJSON && data.responseJSON.message ? data.responseJSON.message : 'Revisa los datos obligatorios de la forma de pago.';
                    receiptAlert({title: 'No se pudo agregar el pago', icon: 'error', text: message});
                }
            });

        }else{

            receiptAlert({
                title: "Error!",
                type: "error",
                html: msj
            });
            
        }

            

    }

    function actualizar_valor(id) {
        if (!requireEditableReceipt()) return;
        function receiptNumber(selector, fallback) {
            var value = String($(selector).val() == null ? '' : $(selector).val()).replace(',', '.').trim();
            var parsed = parseFloat(value);
            return Number.isFinite(parsed) ? parsed : fallback;
        }

        var values = {
            id: id,
            cantidad: receiptNumber('#cantidad' + id, NaN),
            precio: receiptNumber('#precio' + id, NaN),
            p_cpac: receiptNumber('#p_cpac' + id, 100),
            p_dcto: receiptNumber('#p_dcto' + id, 0),
            deducible: receiptNumber('#valor_deducible' + id, 0),
            _v3: {{isset($receiptIsV3) && $receiptIsV3 ? 1 : 0}}
        };

        if (!Number.isFinite(values.cantidad) || values.cantidad <= 0 ||
            !Number.isFinite(values.precio) || values.precio < 0) {
            receiptAlert({title: 'Revise el producto', icon: 'warning', text: 'Ingrese una cantidad y un precio válidos.'});
            return;
        }

        var $row = $('#precio' + id).closest('tr');
        $row.addClass('is-saving');
        $.ajax({
            type: 'post',
            url: "{{route('nuevorecibocobro.actualizar_producto')}}",
            headers: {'X-CSRF-TOKEN': $('input[name=_token]').val()},
            dataType: 'json',
            data: values,
            success: function() {
                lista_productos(true);
            },
            error: function(xhr) {
                $row.removeClass('is-saving');
                var message = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'No se pudo actualizar el producto.';
                receiptAlert({title: 'No se guardó el cambio', icon: 'error', text: message});
            }
        });
    }

    function actualizar_p_cobro(id) {
        var cantidad = $('#cantidad' + id).val();
        var precio = $('#precio' + id).val();
        var cobrar_paciente = $('#cobrar_paciente' + id).val();
        cantidad = parseFloat(cantidad);
        precio = parseFloat(precio);
        cobrar_paciente = parseFloat(cobrar_paciente);
        var neto = cantidad * precio;
        var pct_cobra = cobrar_paciente / neto;
        pct_cobra = pct_cobra * 100;
        pct_cobra = Math.round(pct_cobra * 100) / 100;

        $('#p_cpac' + id).val(pct_cobra);
        actualizar_valor(id);
    }

    function actualizar_p_dcto(id) {
        var cantidad = $('#cantidad' + id).val();
        var precio = $('#precio' + id).val();
        var descuento = $('#descuento' + id).val();
        cantidad = parseFloat(cantidad);
        precio = parseFloat(precio);
        descuento = parseFloat(descuento);
        var neto = cantidad * precio;
        var pct_dcto = descuento / neto;
        pct_dcto = pct_dcto * 100;
        pct_dcto = Math.round(pct_dcto * 100) / 100;

        $('#p_dcto' + id).val(pct_dcto);
        actualizar_valor(id);
    }

    function actualizar_descripcion(id) {
        if (!requireEditableReceipt()) return;
        var descripcion = $('#descripcion' + id).val();
        var id_producto = $('#id_producto' + id).val();
        id_producto = parseFloat(id_producto);
        $.ajax({
            type: 'post',
            url: "{{route('nuevorecibocobro.actualizar_descripcion')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id': id,
                'id_producto': id_producto,
                'descripcion': descripcion,
                'id_producto': id_producto
            },
            success: function(data) {
                lista_productos();
                console.log(data);
            },
            error: function(data) {
                console.log(data);
                alert("No se pudo agregar el producto");
            }
        });
    }

    var deletingReceiptProduct = false;
    function eliminar_detalle(id, button) {
        if (!requireEditableReceipt()) return false;
        if (deletingReceiptProduct) return false;
        var row = $(button).closest('tr');
        deletingReceiptProduct = true;
        $(button).prop('disabled', true).html('<i class="fas fa-circle-notch fa-spin"></i>');
        $.ajax({
            type: 'post',
            url: "{{route('nuevorecibocobro.eliminar_producto')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id': id,
                '_v3': {{isset($receiptIsV3) && $receiptIsV3 ? 1 : 0}}
            },
            success: function(data) {
                row.fadeOut(160, function() { row.remove(); });
                lista_productos(true);
            },
            error: function(data) {
                $(button).prop('disabled', false).html('<i class="fa fa-trash"></i>');
                receiptAlert({title: 'No se pudo eliminar', icon: 'error', text: 'El producto no pudo eliminarse. Intenta nuevamente.'});
            },
            complete: function() {
                deletingReceiptProduct = false;
            }
        });
        return false;
    }

    function eliminar_forma_pago(id) {
        if (!requireEditableReceipt()) return;
        $.ajax({
            type: 'post',
            url: "{{route('nuevorecibocobro.eliminar_pago')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id': id
            },
            success: function(data) {
                lista_formas_pago();
                console.log(data);
            },
            error: function(data) {
                console.log(data);
                alert("No se pudo agregar el producto");
            }
        });
    }


    function guardar_cabecera() {
        if (!requireEditableReceipt()) return;

        var msn = validar_formulario();

        if( msn == '' ){

            return $.ajax({
                type: 'post',
                url: "{{route('nuevorecibocobro.actualizar_cabecera')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#form_actualizar_cabecera").serialize(),
                success: function(data) {
                    if (data !== 'ok') { receiptAlert({title: 'No se guardó el recibo', icon: 'error', text: 'Revisa los datos e intenta nuevamente.'}); return; }
                    lista_productos(true);
                    receiptAlert({
                        title: "Guardado",
                        type: "success",
                        html: 'Guardado Con Exito'
                    });
                },
                error: function(xhr) {
                    var message = xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : 'No se pudo guardar el recibo ni sincronizar el valor del seguro.';
                    receiptAlert({title: 'No se guardó el recibo', icon: 'error', text: message});
                }
            });

        }else{
            receiptAlert({
                title: "Error!",
                type: "error",
                html: msn
            });
        }

            

    }

    function guardar_auto() {
        if (!requireEditableReceipt(false)) return;
        if (!$('#id_seguro').val() || $('#id_seguro').prop('disabled') || $('#id_nivel').prop('disabled')) return;
        $.ajax({
            type: 'post',
            url: "{{route('nuevorecibocobro.actualizar_cabecera')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $("#form_actualizar_cabecera").serialize(),
            success: function(data) {
                if (data === 'ok') lista_productos(true);
            },
            error: function(data) {
                console.log(data);
            }
        });
    }
    var receiptInsuranceRequest = null, receiptLevelRequest = null, receiptInsuranceVersion = 0, receiptLevelVersion = 0;
    function receiptCatalogError(message) {
        receiptAlert({title: 'No se pudo cargar el catálogo', icon: 'error', text: message});
    }
    function cargar_nivel(saveAfter) {
        var version = ++receiptLevelVersion;
        if (receiptLevelRequest) receiptLevelRequest.abort();
        var insurance = $('#id_seguro').val(), previous = $('#id_nivel').val();
        if (!insurance) { $('#id_nivel').empty().append(new Option('Seleccione un seguro', '')); return; }
        $('#id_nivel').prop('disabled', true);
        receiptLevelRequest = $.ajax({
            type: 'post', url: "{{route('lista_nivel.seguro')}}", dataType: 'json',
            headers: {'X-CSRF-TOKEN': $('input[name=_token]').val()},
            data: {id_seguro: insurance, id_empresa: $('#id_empresa').val()}
        }).done(function(data) {
            if (version !== receiptLevelVersion) return;
            if (!Array.isArray(data)) { receiptCatalogError('La respuesta de niveles no es válida.'); return; }
            var select = $('#id_nivel').empty().append(new Option(data.length ? 'Seleccione...' : 'Sin nivel configurado', ''));
            data.forEach(function(item) { select.append(new Option(item.nombre, item.id_nivel)); });
            if (!saveAfter && data.some(function(item) { return String(item.id_nivel) === String(previous); })) select.val(previous);
            else if (data.length === 1) select.val(data[0].id_nivel);
            select.prop('disabled', false);
            if (saveAfter) guardar_auto();
        }).fail(function(xhr, status) {
            if (status !== 'abort') receiptCatalogError('No se pudieron consultar los niveles. Vuelve a seleccionar el seguro.');
        }).always(function() {
            if (version === receiptLevelVersion) $('#id_nivel').prop('disabled', false);
        });
    }
    function cargar_seguro() {
        var version = ++receiptInsuranceVersion;
        ++receiptLevelVersion;
        if (receiptInsuranceRequest) receiptInsuranceRequest.abort();
        if (receiptLevelRequest) receiptLevelRequest.abort();
        var type = $('#id_tipo').val();
        if (!type) { $('#id_seguro').val(''); $('#id_nivel').val(''); return; }
        $('#id_seguro').prop('disabled', true);
        receiptInsuranceRequest = $.ajax({
            type: 'post', url: "{{route('facturaagenda.tipifica_seguros')}}", dataType: 'json',
            headers: {'X-CSRF-TOKEN': $('input[name=_token]').val()}, data: {id_tipo: type}
        }).done(function(data) {
            if (version !== receiptInsuranceVersion) return;
            if (!Array.isArray(data)) { receiptCatalogError('La respuesta de seguros no es válida.'); return; }
            var select = $('#id_seguro').empty().append(new Option(data.length ? 'Seleccione...' : 'Sin seguros disponibles', ''));
            data.forEach(function(item) { select.append(new Option(item.nombre, item.id)); });
            $('#id_nivel').empty().append(new Option('Seleccione un seguro', ''));
            if (data.length === 1) { select.val(data[0].id); cargar_nivel(true); }
        }).fail(function(xhr, status) {
            if (status !== 'abort') receiptCatalogError('No se pudieron consultar los seguros. Intenta nuevamente.');
        }).always(function() { if (version === receiptInsuranceVersion) $('#id_seguro').prop('disabled', false); });
    }
    function buscar() {
        var cedula_1 = $('#cedula').val();
        var pasaporte = parseInt($('#tipo_identificacion').val());
        var alerta = 0;
        if (cedula_1.length < 10) {
            alert('La Cantidad de Digitos no es Correcta');
        }
        var cedula = validarCedula($('#cedula').val());
        if (cedula == false && pasaporte != 6 && pasaporte != 8) {
            console.log("no");
        } else {
            console.log("si");
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
                        //$('#caja').val(data.caja);
                    }
                    console.log(data);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }
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

    function validar_formulario() {
        
        var formulario = document.forms["form_actualizar_cabecera"];
        //Datos Generales
        
        var ced_cliente = formulario.cedula.value;
        var raz_social = formulario.razon_social.value;
        var ciud_cliente = formulario.ciudad.value;
        var dire_cliente = formulario.direccion.value;
        var telf_cliente = formulario.telefono.value;
        var email_cliente = formulario.email.value;
        var caja_pago = formulario.caja.value;

        var msj = "";
        var msj2 = "";
        if ($('#id_seguro').prop('disabled') || $('#id_nivel').prop('disabled')) msj += 'Espera a que termine la carga del seguro y nivel.<br/>';
        if (!$('#id_seguro').val()) msj += 'Selecciona un seguro.<br/>';
        
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
        
        if (caja_pago == "") {
            msj += "Por favor,Ingrese la caja a la cual se va a pagar<br/>";
        }

        return msj;
        
    }

    function emitir(){
        if (!requireEditableReceipt()) return;

        var msn = validar_formulario();

        if(msn == ''){

            Swal.fire({
                title: 'Al emitir se enviará al Cierre de Caja',
                text: "Esta seguro que desa Emitir?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, Emitir!'
            }).then((result) => {
                if (result.isConfirmed){
                    validar_cuadre();
                    
                }else{
                    
                }
            }) 

        }else{

            receiptAlert({
                title: "Error!",
                type: "error",
                html: msn
            });

        }

    }

    function devolver(){
        Swal.fire({
            title: 'Al devolver ya no estará en el Cierre de Caja',
            text: "Esta seguro que desa devolver?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, Devolver!'
        }).then((result) => {
            if (result.isConfirmed){
                $.ajax({
                    url: "{{route('nuevorecibocobro.devolver',['id_orden' => $orden->id, '_v3' => isset($receiptIsV3) && $receiptIsV3 ? 1 : 0])}}",
                    type: 'get',
                    datatype: 'html',
                    success: function(data) {
                        if(data === 'ok'){
                            receiptAlert({
                                title: "Devuelto!",
                                type: "success",
                                html: 'Devuelto a Recepción'
                            });
                            location.reload();
                        }else{
                        }    
                    },
                    error: function(data) {
                        console.log(data);
                    }
                })    
            }else{
            }
        }) 
    }
    function validar_cuadre(){

        $.ajax({
            url: "{{route('nuevorecibocobro.validar_valores',['id_orden' => $orden->id])}}",
            type: 'get',
            datatype: 'html',
            success: function(data) {
                if(data.estado != 'ok'){
                    receiptAlert({
                        title: "Error!",
                        type: "error",
                        html: data.mensaje
                    });
                }else{
                    var saving = guardar_cabecera();
                    if (saving) saving.done(function(result) {
                        if (result === 'ok') emitir_recibo_cobro();
                    });

                }    
            
            },
            error: function(data) {
                console.log(data);
            }
        })

    }

    function emitir_recibo_cobro(){

        $.ajax({
            url: "{{route('nuevorecibocobro.emitir_recibo',['id_orden' => $orden->id, '_v3' => isset($receiptIsV3) && $receiptIsV3 ? 1 : 0])}}",
            type: 'get',
            datatype: 'html',
            success: function(data) {
                if(data == 'ok'){
                receiptAlert({
                    title: "Emision Correcta!",
                    type: "success",
                    html: 'Su Recibo de Cobro ya se encuentra en el cierre de caja'
                });
                    window.open("{{route('facturacion.imprimir_ride',['id_orden' => $orden->id])}}", '_blank ');
                location.reload();
                }else{
                    receiptAlert({
                        title: "Error!",
                        type: "error",
                        html: data
                    }); 
                } 
               
            },
            error: function(xhr) {
                var message = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'No se pudo emitir el recibo ni registrar la orden del seguro.';
                receiptAlert({title: 'No se emitió el recibo', icon: 'error', text: message});
            }
        })

    }

    function agregar_deducible( id ){
        if (!requireEditableReceipt()) return;

        $.ajax({
            url: "{{url('nuevo_rc/deducible/crear/item/xseguro')}}/" + id + "?_v3={{isset($receiptIsV3) && $receiptIsV3 ? 1 : 0}}",
            type: 'get',
            datatype: 'html',
            success: function(data) {
                lista_productos();
                
            },
            error: function(data) {
                console.log(data);
            }
        })            

    }







    //////////////


</script>
