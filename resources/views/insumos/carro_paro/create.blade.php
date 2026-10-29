@extends('insumos.carro_paro.base')
@section('action-content')
@php

use Carbon\Carbon;
$rolUsuario = Auth::user()->id_tipo_usuario;

@endphp
<style>
    .producto-nombre {
        display: block;
        min-width: 280px;
        white-space: normal;
        font-weight: 500;
    }

    .registro-existente-badge {
        display: inline-block;
        margin-left: 8px;
        padding: 3px 8px;
        border-radius: 12px;
        background-color: #d9534f;
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        cursor: help;
    }

    .bg-danger {
        background-color: #f8d7da !important;
        color: #721c24 !important;
    }

    .bg-warning {
        background-color: #fff3cd !important;
        color: #856404 !important;
    }

    .bg-info {
        background-color: #d1ecf1 !important;
        color: #0c5460 !important;
    }

    .badge-danger {
        background-color: #dc3545 !important;
        color: #fff;
    }

    .badge-warning {
        background-color: #ffc107 !important;
        color: #212529;
    }

    .badge-info {
        background-color: #17a2b8 !important;
        color: #fff;
    }
</style>
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('winsumos_reportes.insumos')}}</a></li>
            <li class="breadcrumb-item"><a href="../">Carrao de paro insumos</a></li>
        </ol>
    </nav>
    <div class="box">
        <div class="row head-title align-items-center">
            <div class="col-md-8 d-flex align-items-center">
                <label class="color_texto mb-0 fw-bold" for="opc">
                    CONTROL DE INSUMOS DE COCHE DE PARO
                </label>
                @if(isset($registro))
                <span class="registro-existente-badge" style="height:30px;" title="Ya existe un registro cargado para esta pantalla.">
                    Registro Guardado
                </span>
                @endif
            </div>
        </div>

        <div class="box-body dobra">
            <form id="formulario">

                <input type="hidden" id="fechaAnterior" name="fechaAnterior">
                <input type="hidden" name="editarId" id="editarId" value="{{ isset($registro) ? $registro->id : '' }}">
                <input type="hidden" id="fechaEdit" value="{{ isset($registro) ? $registro->created_at : '' }}">
                <div class="row" style="margin-bottom: 10px;">
                    <div class="col-md-3">
                        <label for="opc">Tipo</label>
                        @php
                        $tipoSeleccionado = isset($registro) ? $registro->tipo : request('tipo', 1);
                        @endphp

                        <select name="opc" id="opc" class="form-control select2" onchange="changeType()">
                            <option value="1" {{ $tipoSeleccionado == 1 ? 'selected' : '' }}>
                                MEDICAMENTOS
                            </option>

                            <option value="2" {{ $tipoSeleccionado == 2 ? 'selected' : '' }}>
                                INSUMOS
                            </option>
                        </select>

                    </div>
                    <div class="col-md-9" style="text-align: right;">
                        @if(in_array($rolUsuario, array(1,7)) == true)
                        <button type="button" class="btn btn-danger" onclick="abrirModalReporte()">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                            Productos proximos a caducar
                        </button>
                        @endif
                        <a class="btn btn-info" href="{{route('insumos_index')}}">
                            <i class="fa fa-book" aria-hidden="true"></i>
                            Registros
                        </a>
                        <button type="button" class="btn btn-success" onclick="guardarForm()">
                            <i class="fa fa-floppy-o" aria-hidden="true"></i>
                            @if(isset($registro))
                            Editar
                            @else
                            Guardar
                            @endif
                        </button>

                    </div>
                </div>
                <div class="row">

                    <div class="col-md-4 form-group">
                        <label for="responsable">Responsable</label>
                        <select id="responsable" class="select2 form-control" name="responsable" autocomplete="off">
                            <option value="">--Seleccione--</option>
                            @foreach($usuarios as $val)
                            <option {{$val->id === '0951419837' ? 'selected' : ''}} value="{{$val->id}}" {{ isset($registro) && $registro->responsable_id == $val->id ? 'selected' : '' }}>
                                {{$val->nombre1}}
                                {{$val->nombre2}}
                                {{$val->apellido1}}
                                {{$val->apellido2}}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="servicio">Servicio</label>
                        <select id="servicio" class="form-control" name="servicio" autocomplete="off">
                            <option value="">--Seleccione--</option>
                            <option value="1" selected {{ isset($registro) && $registro->servicio == 1 ? 'selected' : '' }}>GASTROENTEROLOGIA</option>
                        </select>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="fecha">Fecha</label>
                        <input type="date" name="fecha" id="fecha" class="form-control" onchange="changeDate(this)" value="{{ isset($registro) ? $registro->fecha : date('Y-m-d') }}">
                    </div>
                </div>


                <div class="row" style="margin-top: 20px;" id="tablaC">

                </div>
            </form>
        </div>
    </div>
</section>


<div class="modal fade" id="miModal" tabindex="-1" role="dialog" aria-labelledby="miModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h3 class="modal-title" id="miModalLabel" style="font-weight: bold;text-align:center">Productos proximos a caducar</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar" onclick="cerrarModalReporte()">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <table id="tablaProductos" class="table table-bordered table-striped table-sm">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>#</th>
                            <th>Producto</th>
                            <th>Lote</th>
                            <th>Stock</th>
                            <th>Fecha Caducidad</th>
                            <th>Días</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach($productosAlertas as $index => $x)
                        @foreach($x as $v)
                        @php
                        $hoy = Carbon::now();
                        $clase = '';
                        $estado = '';
                        $badge = '';
                        if (!empty($v->fecha_caducidad)) {
                            $fecha = Carbon::parse($v->fecha_caducidad);

                            if ($fecha->lt(Carbon::now())) {
                            $clase = 'bg-danger text-white';
                            $estado = 'Vencido';
                            $badge = 'badge badge-danger';
                            } elseif ($fecha->between(Carbon::now(), Carbon::now()->addWeek())) {
                            $clase = 'bg-warning';
                            $estado = 'Crítico';
                            $badge = 'badge badge-warning';
                            } elseif ($fecha->between(Carbon::now(), Carbon::now()->addWeeks(3))) {
                            $clase = 'bg-info text-white';
                            $estado = 'Próximo';
                            $badge = 'badge badge-info';
                            } else {
                            $estado = 'OK';
                            $badge = 'badge badge-success';
                            }
                        }
                        @endphp

                        <tr class="{{ $clase }}">
                            <td>{{ 1 }}</td>
                            <td>{{ $v["producto"] }}</td>
                            <td>{{ $v["lote"] }}</td>
                            <td>{{ $v["cantidad"]}}</td>
                            <td>
                                {{ !empty($v->fecha_caducidad) 
                    ? Carbon::parse($v->fecha_caducidad)->format('d/m/Y') 
                    : '-' }}
                            </td>
                            <td>
                                {{ Carbon::parse($v->fecha_caducidad)->startOfDay()->diffInDays(Carbon::now()->startOfDay(), false) * -1 }}
                            </td>
                            <td>
                                <span class="{{ $badge }}">
                                    {{ $estado }}
                                </span>
                            </td>
                        </tr>


                        @endforeach
                        @endforeach

                    </tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="cerrarModalReporte()">Cerrar</button>
            </div>

        </div>
    </div>
</div>



<script type="text/javascript">
    function abrirModalReporte() {
        $('#miModal').modal('show');
    }

    function limpiarBloqueoModalReporte() {
        const $modal = $('#miModal');

        if ($modal.hasClass('in') || $modal.hasClass('show') || $modal.is(':visible')) {
            return;
        }

        $('body').removeClass('modal-open').css('padding-right', '');
        $('.modal-backdrop').remove();
    }

    function cerrarModalReporte() {
        const $modal = $('#miModal');

        if (typeof $modal.modal === 'function') {
            $modal.modal('hide');
        } else {
            $modal.removeClass('in show').hide().attr('aria-hidden', 'true');
        }

        window.setTimeout(limpiarBloqueoModalReporte, 350);
    }


    $(document).ready(function() {
        $(".select2").select2();

        $('#miModal').on('hidden.bs.modal', limpiarBloqueoModalReporte);

        const params = new URLSearchParams(window.location.search);
        const fecha = params.get('fecha');
        if (fecha) {
            $("#fechaAnterior").val(fecha);
            $("#fecha").val(fecha);
        }

        let tipoInicial = "{{ $tipoSeleccionado }}";
        $("#opc").val(tipoInicial).trigger('change.select2');
        changeType();
    });

    const fechaR = () => {
        const fechaAnterior = $("#fechaAnterior").val();
        if (fechaAnterior) {
            return fechaAnterior.replace(/-/g, ''); // "2026-05-09" → "20260509"
        }

        let fechaEdit = $("#fechaEdit").val();
        const hoy = fechaEdit ? new Date(fechaEdit) : new Date();
        const y = hoy.getFullYear();
        const m = String(hoy.getMonth() + 1).padStart(2, '0');
        const d = String(hoy.getDate()).padStart(2, '0');
        return `${y}${m}${d}`;
    }

    function changeType() {
        let editarId = $("#editarId").val();
        let opc = $("#opc").val();
        if (opc == 1) {
            $("#title").text("CONTROL DE MEDICAMENTOS DE COCHE DE PARO");
            $("#obs_insumos").empty();

        } else {
            $("#title").text("CONTROL DE INSUMOS DE COCHE DE PARO");
            $("#obs_insumos").empty();
        }

        // buscar si hay algo ese dia
        $.ajax({
            type: 'post',
            url: "{{route('insumos_cargarTipo')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                tipo: opc,
                editarId: $("#editarId").val(),
                fecha: $("#fecha").val(),
                fechaAnterior: $("#fechaAnterior").val(),
            },
            success: function(data) {
                if (data.status) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                        return;
                    }


                    $("#tablaC").empty();
                    $("#tablaC").append(data.html);
                    $(".select2").select2({
                        'width': '100%'
                    });
                    let fecha = fechaR();
                    const $input = $(`.${fecha}`);
                    // $input[0].scrollIntoView({
                    //     behavior: 'smooth',
                    //     block: 'center',
                    //     inline: 'center'
                    // });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: `Error:   ${data.error} Line: ${data.line}`,
                    });
                }

            },
            error: function(xhr, status, error) {
                let message = "Ocurrió un error inesperado";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    message = xhr.responseText;
                } else if (error) {
                    message = error;
                }
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: message,
                });
            }
        });

    }



    const validarForm = () => {

        const errores = [];

        let fecha = fechaR();

        let opc = $("#opc").val();
        if ($("#responsable").val() === "") {
            errores.push("El responsable se encuentra vacio.")
        }
        if ($("#fecha").val() === "") {
            errores.push("La fecha se encuentra vacia.")
        }



        // if (opc == 1) {
        //     $('select[name="presentacion[]"]').each(function(index) {
        //         if ($(this).val().trim() == '') {
        //             errores.push("La presentación en la fila " + (index + 1) + "esta vacia.");
        //         }
        //     });
        // }

        // $('input[name="cantidad[]"]').each(function(index) {
        //     if ($(this).val().trim() == '') {
        //         errores.push("La cantidad en la fila " + (index + 1) + " esta vacia.");
        //     }
        // });

        // $(`.${fecha}`).each(function(index) {
        //     if ($(this).val().trim() == '') {
        //         errores.push("La validación diaria en la fila " + (index + 1) + " esta vacia.");
        //     }
        // });



        return errores;
    }

    function guardarForm() {


        const validar = validarForm();

        if (validar.length > 0) {
            Swal.fire({
                icon: "error",
                title: "Validaciones...",
                html: validar.join("<br>"),
            });
        } else {

            Swal.fire({
                title: "¿Estás seguro?",
                text: "Se guardará la información ingresada",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, guardar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'post',
                        url: "{{route('insumos_guardarForm')}}",
                        headers: {
                            'X-CSRF-TOKEN': $('input[name=_token]').val()
                        },
                        datatype: 'json',
                        data: {
                            form: $("#formulario").serialize(),
                        },
                        success: function(data) {
                            let editarId = $("#editarId").val();

                            if (!data.status) {
                                return Swal.fire({
                                    icon: "error",
                                    title: "Error en el proceso de guardado...",
                                    text: `Error: ${data.error}${data.line !== '' ? ` Line: ${data.line}` : ''}`,
                                });
                            }

                            Swal.fire({
                                icon: "success",
                                title: `${editarId ? 'Editado' : 'Guardado'} correctamente...`,
                            }).then(() => {
                                console.log(data.id);
                                window.location.href = "{{ url('carro/paro?id=') }}" + encodeURIComponent(data.id);
                            });
                        },

                        error: function(xhr, status, error) {
                            let message = "Ocurrió un error inesperado";

                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            } else if (xhr.responseText) {
                                message = xhr.responseText;
                            } else if (error) {
                                message = error;
                            }

                            Swal.fire({
                                icon: "error",
                                title: "Oops...",
                                text: message,
                            });
                        }
                    });

                }

            });
        }
    }

    function validarMinimo(dia, id_producto, idLote, e) {

        $.ajax({
            type: 'post',
            url: "{{route('insumos.validar_stock')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                id_producto: id_producto,
                cantidad: $(e).val(),
                lote: idLote

            },
            success: function(data) {
                if (!data.status) {
                    Swal.fire({
                        icon: "error",
                        title: "Validación fallida...",
                        text: `Error: ${data.error}${data.line ? ' Line: ' + data.line : ''}`,
                    });
                    $(e).val($(e).closest('tr').find('input[name="cantidad[]"]').val());
                }


            },
            error: function(xhr, status, error) {
                let message = "Ocurrió un error inesperado";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    message = xhr.responseText;
                } else if (error) {
                    message = error;
                }
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: message,
                });
            }
        });
    }

    function changeDate(e) {
        $.ajax({
            type: 'post',
            url: "{{route('carro_paro.registro_anterior')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                fecha: e.value,
                tipo: $("#opc").val(),

            },
            success: function(data) {
                if (!data.status) {
                    Swal.fire({
                        icon: "error",
                        title: "Validación fallida...",
                        text: `Error: ${data.error}${data.line ? ' Line: ' + data.line : ''}`,
                    });

                }

                if (data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }


            },
            error: function(xhr, status, error) {
                let message = "Ocurrió un error inesperado";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    message = xhr.responseText;
                } else if (error) {
                    message = error;
                }
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: message,
                });
            }
        });
    }
</script>
@endsection
