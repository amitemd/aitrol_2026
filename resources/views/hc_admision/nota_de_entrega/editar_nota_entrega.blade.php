@extends('hc_admision.nota_de_entrega.base')
@section('action-content')
    <div class="modal fade" id="Subir" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
        <div class="modal-dialog " role="document">
            <div class="modal-content" id="imprimir3">
            </div>
        </div>
    </div>
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
                                <p><b>@if($log->usercrea) {{ $log->usercrea->apellido1 }} {{ $log->usercrea->apellido2 }} {{ $log->usercrea->nombre1 }} @else Usuario histórico @endif: </b>{{ $log->descripcion }}
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
                                            <label class="col-md-3" style="font-size: 20px">{{ trans('new_recibo.DatosPrincipales') }}</label>
                                            @if ($nota_entrega->estado == 1)
                                        <div class="col-md-1 btn-sm" style="text-align: center; padding: 10px;"><span class="btn btn-success btn-sm">!! Emitida !!</span></div>
                                        <div class="col-md-1 btn-sm" style="text-align: center; padding: 10px; ">
                                            <a target="_blank" style="font-size: 12px; width:100%" class="btn btn-primary btn-sm" formtarget="_blank" id="examenes_externos" href="{{route('nota_entrega.imprimir_pdf_nota_entrega', ['id_nota' => $nota_entrega->id]) }}"> <span class="glyphicon glyphicon-download-alt" aria-hidden="true"> Impimir</span> </a>
                                                </div>
                                                @if(Auth::user()->id_tipo_usuario == 1)
                                        <div class="col-md-1" style="text-align: center; padding: 10px;">
                                            <a target="_blank" style="font-size: 12px; width:100%" class="btn btn-primary btn-sm" formtarget="_blank" id="examenes_externos" onclick="devolver()"> <span aria-hidden="true"> Devolver</span> </a>
                                                    </div>
                                                @endif
                                            @else
                                                @if($nota_entrega->estado == -1)
                                                    <div class="col-md-1" style="text-align: center; padding: 10px;">
                                            <a href="{{url('receta/nota_entrega/reprocesar')}}/{{$nota_entrega->id}}" class="btn btn-primary btn-sm" style="font-size: 12px; width:100%"> Reprocesar </a>
                                                    </div>
                                                @endif
                                                <div class="col-md-1" style="text-align: center; padding: 10px;">
                                            <button class="btn btn-success btn-sm" type="button" onclick="emitir()" style="font-size: 12px; width:100%">EMITIR</button>
                                                </div>
                                            @endif
                                            
                                        <div class="col-md-1" style="text-align: center; padding: 10px;">
                                            <a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#log_rc" style="font-size: 12px; width:100%">Logs</a>
                                            </div>
                                            @php
                                                $doc_scan = \Sis_medico\Nota_Entrega_Escaneados::where('id_nota_entrega', $nota_entrega->id)->where('estado', '1')->first();
                                                $detalles = \Sis_medico\Nota_entrega_detalle::where('id_nota_entrega', $nota_entrega->id)->where('estado', '1')->get();

                                                $i=0;
                                                if(!is_null($detalles)){
                                                foreach($detalles as $det){
                                                if($det->cantidad_receta != $det->cantidad){
                                                $i++;
                                                }
                                                }
                                                }
                                            @endphp
                                        <div class="col-md-1" style="text-align: center; padding: 10px;">
                                                <a href="{{url('nota_entrega/sube/archivo')}}/{{$nota_entrega->id_hc_receta}}/{{$nota_entrega->id}}" data-toggle="modal" data-target="#Subir">
                                                <button class="btn btn-success btn-sm" style="font-size: 12px; width:100%"> Subir Archivo</button>
                                                </a>
                                            </div>

                                        <div class="col-md-1" style="text-align: center; padding: 10px;">
                                                @if(!is_null($doc_scan))
                                            <a target="_blank" href="{{route('receta.visualizar_nota_entrega_pdf_nuevo',['nombre'=>$doc_scan->archivo])}}" class="btn btn-primary btn-sm" style="font-size: 12px; width:100%">Ver Archivo</a>
                                                @endif
                                            </div>
                                        <div class="col-md-2" style="text-align: center; padding: 10px;">
                                                @if($i >= 1)
                                                    <span style="color: orange; font-weight: bold;">! PENDIENTE DE ENTREGAR MEDICINA !</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <img src="{{ asset('/logo') . '/' . $empresa->logo }}" style="width:150px;height:70px">
                                            </div>
                                            <div class="col-md-9">
                                                <label>{{ $empresa->nombrecomercial }}</label><br>
                                                <label>{{ $empresa->direccion }}</label>
                                            </div>
                                        </div>
                                        <center><b style="font-size: 20px;">NOTA DE ENTREGA No. </b><label style="color: red; font-size: 22px">{{ $nota_entrega->numero }}</label></center>
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
            var cantidad_receta = $('#cantidad_receta' + id).val();
            var cantidad = $('#cantidad' + id).val();
            var precio = $('#precio' + id).val();
            var id_producto = $('#id_producto' + id).val();
            var msj = "";
            if (cantidad == "") {
                msj += "Ingresar la Cantidad a entregar<br/>";
                $('#cantidad'+id).val(0); 
                actualizar_valor(id);      
            }

            if(cantidad_receta < cantidad){
                msj += "No se puede entregar mas medicinas de la cantidad recetada.<br/>";
                $('#cantidad'+id).val(cantidad_receta); 
                actualizar_valor(id);
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
                    },
                    error: function(data) {
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

        function emitir() {
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
                        url: "{{ route('nota_entrega.emitir_recibo', ['id' => $nota_entrega->id]) }}",
                        type: 'get',
                        datatype: 'html',
                        success: function(data) {
                            if (data == 'ok') {
                                Swal.fire('Correcto', 'Emitida Correctamente', 'success');
                                setTimeout(function() {
                                    window.location.reload();
                                }, 1500)
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
                                Swal.fire('Error', '!No se puede devolver.!', 'error');
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
