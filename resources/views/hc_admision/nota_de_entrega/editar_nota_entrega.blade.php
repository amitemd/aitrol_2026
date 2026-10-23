@extends('hc_admision.nota_de_entrega.base')
@section('action-content')
    <div class="modal fade" id="tarifario_nivel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content ">
            </div>
        </div>
    </div>
    <div class="modal fade" id="log_rc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content ">
                <div class="modal-content">
                    <div style="text-align: left" class="modal-header">
                        <h3 class="box-title">LOG</h3>
                    </div>
                    <div class="modal-body">
                        @php 
                        $logs = $nota_entrega->logs_nota; @endphp
                        @if(!is_null($logs))
                            @foreach ($logs as $log)
                                <p><b>{{ $log->usercrea->apellido1 }} {{ $log->usercrea->apellido2 }} {{ $log->usercrea->nombre1 }}: </b>{{ $log->descripcion }}
                                </p>
                            @endforeach
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger"
                            data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="box box s">
            <div class="box-body">
                <a href="#" target="_blank" id="ride"></a>
                <form class="form-vertical" id="form_actualizar_cabecera" method="POST" autocomplete="off">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id_orden" id="id_orden" value="{{ $nota_entrega->id }}">
                    <div class="col-md-12">
                        <div class="row dobra">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading " style="background-color: #D3EEFF;">
                                        <div class="row">
                                            <label class="col-md-4" style="font-size: 20px">{{ trans('new_recibo.DatosPrincipales') }}</label>
                                            @if ($nota_entrega->estado == 1)
                                                <div class="col-md-2 alert-success" style="text-align: center; padding: 10px;">!! Nota de Entrega emitida !!</div>
                                                <div class="col-md-2" style="text-align: center;">
                                                    <a target="_blank" style="font-size: 18px" class="btn btn-primary" formtarget="_blank" id="examenes_externos" href="{{route('nota_entrega.imprimir_pdf_nota_entrega', ['id_nota' => $nota_entrega->id]) }}"> <span class="glyphicon glyphicon-download-alt" aria-hidden="true"> Impimir</span> </a>
                                                </div>
                                                <div class="col-md-2" style="text-align: center;">
                                                    <a target="_blank" style="font-size: 18px" class="btn btn-primary" formtarget="_blank" id="examenes_externos" onclick="devolver()"> <span aria-hidden="true"> Devolver</span> </a>
                                                </div>
                                            @else
                                                <div class="col-md-2" style="text-align: center;">
                                                <button class="btn btn-success" type="button" onclick="validar_detalle()" style="font-size: 18px">EMITIR</button>
                                            </div>
                                            @endif
                                            <div class="col-md-2" style="text-align: center;">
                                                <a class="btn btn-primary" data-toggle="modal" data-target="#log_rc" style="font-size: 18px">Logs</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <img src="{{ asset('/logo') . '/' . $empresa->logo }}" style="width:300px;height:120px">
                                            </div>
                                            <div class="col-md-9">
                                                <label>{{ $empresa->nombrecomercial }}</label><br>
                                                <label>{{ $empresa->direccion }}</label>
                                            </div>
                                        </div>
                                        <center><b style="font-size: 20px;">NOTA DE ENTREGA No. </b><label style="color: red; font-size: 22px">{{ $nota_entrega->numero }}</label></center>

                                            <b style="text-transform: uppercase; font-size:17px;">Seleccione la bodega:</b>
                                            <select @if($nota_entrega->estado == '1') disabled @endif style="min-width:25%;width:25%;" name="bodega" id="bodega" class="form-control" required >
                                               <option value="">Seleccione</option>
                                               <option selected value="{{$bodegas->id}}">{{$bodegas->nombre}}</option>
                                            </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="panel panel-default" >
                                    <div class="panel-heading" style="background-color: #D3EEFF;">
                                        <label style="font-size: 20px">Datos del Paciente</label>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-6 col-xs-12">
                                            <label for="cedula"
                                                class="col-md-5 control-label">Nombre:</label>
                                            <div class=" col-md-7">
                                                <label style="font-style: 15px;">{{ $paciente->apellido1 }} @if ($paciente->apellido2 != '(N/A)') {{ $paciente->apellido2 }}@endif {{ $paciente->nombre1 }} @if ($paciente->nombre2 != '(N/A)') {{ $paciente->nombre2 }}@endif</label>
                                            </div>
                                        </div>
                                        <!--Ruc/Cedula-->
                                        <div class="form-group col-md-6 col-xs-12">
                                            <label for="cedula"
                                                class="col-md-5 control-label">Identificacion:</label>
                                            <div class="input-group col-md-7">
                                                <label style="font-style: 15px;">{{ $paciente->id }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding: 0;">
                            <div id="detalles_orden">
                            </div>
                        </div>
                    </div>
                </form>
            </div> 
        </div>
    </section>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
    <script src="{{ asset('/js/jquery-ui.js') }}"></script>
    <script type="text/javascript">
        $(".js-seguro").select2();
        lista_productos();

        function lista_productos() {
            $.ajax({
                url: "{{ route('nota_entrega.detalles', ['id' => $id_receta]) }}",
                type: 'get',
                datatype: 'html',
                success: function(data) {
                    $('#detalles_orden').empty().html(data);
                },
                error: function(data) {
                    console.log(data);
                }
            })
        }

        function seleccionar_producto() {
            var producto_nuevo = $('#producto_nuevo').val();
            $.ajax({
                type: 'post',
                url: "{{ route('nota_entrega.guardar_medicina') }}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'producto_nuevo': producto_nuevo,
                    'id': '{{ $id_receta }}'
                },
                success: function(data) {
                    lista_productos();
                    console.log(data);
                },
                error: function(data) {
                    console.log(data);
                    alert("No se pudo agregar la medicina");
                }
            });

        }


        function actualizar_valor(id) {
            var id_nota = $('#id_nota').val();
            var cantidad = $('#cantidad' + id).val();
            var precio = $('#precio' + id).val();
            var id_producto = $('#id_producto' + id).val();
            var msj = "";
            if (cantidad == "") {
                msj += "Ingresar la Cantidad<br/>";
            }
            if (precio == "") {
                msj += "Ingresar el Precio<br/>";
            }
            if (msj == '') {
                cantidad = parseFloat(cantidad);
                precio = parseFloat(precio);
                $.ajax({
                    type: 'post',
                    url: "{{ route('nota_entrega.actualizar_medicina') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: {
                        'id_nota': id_nota,
                        'id': id,
                        'id_producto': id_producto,
                        'cantidad': cantidad,
                        'precio': precio,
                    },
                    success: function(data) {
                        lista_productos();
                        console.log(data);
                    },
                    error: function(data) {
                        console.log(data);
                        alert("No se pudo agregar la medicina");
                    }
                });

            } else {
                Swal.fire({
                    title: "Advertencia!",
                    icon: 'info',
                    html: msj
                });
            }
        }

        function eliminar_detalle(id) {
            var id_nota = $('#id_nota').val();
            $.ajax({
                type: 'post',
                url: "{{ route('nota_entrega.eliminar_medicina') }}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'id': id,
                    'id_nota': id_nota
                },
                success: function(data) {
                    lista_productos();
                    console.log(data);
                },
                error: function(data) {
                    console.log(data);
                    alert("No se pudo eliminar la medicina");
                }
            });
        }

        function soloNumeros(e) {
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

        function validar_detalle() {
            $.ajax({
                url: "{{ route('nota_entrega.validar_detalle', ['id' => $nota_entrega->id]) }}",
                type: 'get',
                datatype: 'html',
                success: function(data) {
                    if (data == 'ok') {
                        emitir();
                        //Swal.fire('Correcto', 'validado Correctamente', 'success');
                    } else {
                        Swal.fire({
                            title: "!Advertencia!",
                            icon: 'info',
                            html: data
                        });
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            })
        }


        function emitir() {
            let bodega = document.getElementById("bodega").value;
            let id = document.getElementById("id_orden").value;
            var msj = "";
            if (bodega == "") {
                msj += "Por favor, Seleccione la Bodega<br/>";
            }
            if( msj == '' ){
                Swal.fire({
                    title: `Al emitir no se podra editar`,
                    text: "¿Esta seguro de emitir la nota de entrega?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: `Si, emitir`
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                        url: "{{route('nota_entrega.validar_stock')}}",
                        type: 'get',
                        datatype: 'json',
                        data : {
                          "id"    : id,
                          "id_bodega" : bodega
                        },
                        success: function(data) {
                            if (data == 'ok') {
                                emitir_recibo_cobro();
                                setTimeout(function() {
                                    window.location.reload();
                                }, 1500)
                            } else {
                                Swal.fire({
                                    title: "Error!",
                                    icon: 'error',
                                    html: data
                                });
                            }
                        },
                        error: function(data) {
                            console.log(data);
                        }
                        })
                    } else {
                    }
                })
            }else{
                Swal.fire({
                    title: "!Advertencia!",
                    icon: 'info',
                    html: msj
                });
            }    
        }

        function emitir_recibo_cobro() {
            $.ajax({
                url: "{{ route('nota_entrega.emitir_recibo', ['id' => $nota_entrega->id]) }}",
                type: 'get',
                datatype: 'html',
                success: function(data) {
                    if (data == 'ok') {
                        Swal.fire('Correcto', 'Emitida Correctamente', 'success');
                    } else {
                        Swal.fire({
                            title: "Error!",
                            type: "error",
                            html: data
                        });
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            })
        }

        function devolver() {
            Swal.fire({
                title: `Devolver a Cotizacion`,
                text: "¿Esta seguro de devolver la nota de entrega?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: `Si, devolver`
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('nota_entrega.devolver_nota_entrega', ['id' => $nota_entrega->id]) }}",
                        type: 'get',
                        data: {
                            'id': {{ $nota_entrega->id }}
                        },
                        datatype: 'html',
                        success: function(data) {
                            if (data == 'ok') {
                                Swal.fire('Correcto', 'Devuelta Correctamente', 'success');
                                setTimeout(function() {
                                    window.location.reload();
                                }, 1500)
                            } else {
                                Swal.fire('Error', '!No se puede devolver, la planilla ya se encuentra aprobada.!', 'error');
                            }
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    })
                } else {
                }
            })
        }

    </script>
@endsection
