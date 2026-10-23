@extends('actividad_comercial.base')
@section('action-content')


@php
$rolUsuario = Auth::user()->id_tipo_usuario;
$fecha_desde = isset($params['fecha_desde']) ? $params['fecha_desde'] : null;
$fecha_hasta = isset($params['fecha_hasta']) ? $params['fecha_hasta'] : null;
@endphp

<style>
    .grid-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(316px, 1fr));
        gap: 20px;
        padding: 0px 10px;
    }

    .grid-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(5em, 1fr));
        gap: 5px;
        margin-top: 0px;
    }

    .banda {
        position: relative;
    }

    .banda::before {
        position: absolute;
        top: 11px;
        left: 204px;
        display: block;
        box-sizing: border-box;
        content: "Prospecto";
        color: #4B5563;
        font-size: 13px;
        text-transform: uppercase;
        text-align: center;
        font-weight: bold;
        height: 40px;
        width: 150px;
        border: 25px solid transparent;
        border-bottom: 25px solid rgb(147 197 253);
        line-height: 23px;
        transform: rotate(45deg);
    }

    .pagination>li>a {
        color: #4B5563 !important;
    }
</style>

<div class="p-4 rounded-lg bg-white mt-4">
    <div class="flex justify-end gap-2">
        <a href="{{ url('actividad/estados/comercial') }}">
            <button type="button" class="bg-cyan-600 hover:bg-cyan-800 px-4 py-2 text-white rounded font-medium shadow-md 
                        duration-300 transition-all flex gap-2 items-center">
                <i class="fa fa-plus" aria-hidden="true"></i>
                Crear estados
            </button>
        </a>

        <a href="{{ route('actividad_comercial.create') }}">
            <button type="button" class="bg-green-600 hover:bg-green-800 px-4 py-2 text-white rounded font-semibold shadow-md duration-300 transition-all flex gap-2 items-center">
                <i class="fa fa-plus" aria-hidden="true"></i>
                Crear
            </button>
        </a>

        @if($rolUsuario == 7 || $rolUsuario == 1)
        <form action="{{ route('actividad_comercial.excel_diferencia') }}" method="POST">
            {{ csrf_field() }}
            <input type="hidden" name="fdesde" id="fdesde" value="{{$fecha_desde}}">
            <input type="hidden" name="fhasta" id="fhasta" value="{{$fecha_hasta}}">

            <button type="submit" class="bg-green-600 hover:bg-green-800 px-4 py-2 text-white rounded font-semibold shadow-md duration-300 transition-all flex gap-2 items-center">
                <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                Excel Prospecto
            </button>
        </form>
        @endif
        {{--
        @if($rolUsuario == 17 || $rolUsuario == 1)
        <form action="{{ route('actividad_comercial.excel_diferencia_vendedor') }}" method="POST">
        {{ csrf_field() }}
        <input type="hidden" name="fdesde" id="fdesde" value="{{$fecha_desde}}">
        <input type="hidden" name="fhasta" id="fhasta" value="{{$fecha_hasta}}">

        <button type="submit" class="bg-green-600 hover:bg-green-800 px-4 py-2 text-white rounded font-semibold shadow-md duration-300 transition-all flex gap-2 items-center">
            <i class="fa fa-file-excel-o" aria-hidden="true"></i>
            Excel Prospecto
        </button>
        </form>
        @endif
        --}}


        @if($rolUsuario == 7 || $rolUsuario == 1)
        <form action="{{ route('actividad_comercial.excel_actividad') }}" method="POST">
            {{ csrf_field() }}
            <input type="hidden" name="fdesde" id="fdesde" value="{{$fecha_desde}}">
            <input type="hidden" name="fhasta" id="fhasta" value="{{$fecha_hasta}}">

            <button type="submit" class="bg-green-600 hover:bg-green-800 px-4 py-2 text-white rounded font-semibold shadow-md duration-300 transition-all flex gap-2 items-center">
                <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                excel
            </button>
        </form>
        @endif
        {{--
        @if($rolUsuario == 17 || $rolUsuario == 1)
        <form action="{{ route('actividad_comercial.excel_actividad_vendedor') }}" method="POST">
        {{ csrf_field() }}
        <input type="hidden" name="fdesde" id="fdesde" value="{{$fecha_desde}}">
        <input type="hidden" name="fhasta" id="fhasta" value="{{$fecha_hasta}}">

        <button type="submit" class="bg-green-600 hover:bg-green-800 px-4 py-2 text-white rounded font-semibold shadow-md duration-300 transition-all flex gap-2 items-center">
            <i class="fa fa-file-excel-o" aria-hidden="true"></i>
            excel
        </button>
        </form>
        @endif
        --}}


        @if($rolUsuario != 17)
        <form action="{{ route('actividad_comercial.estadistico') }}" method="POST">
            {{ csrf_field() }}
            <button type="submit" class="bg-blue-600 hover:bg-blue-800 px-4 py-2 text-white rounded font-semibold shadow-md duration-300 transition-all flex gap-2 items-center">
                Estadistico
            </button>
        </form>
        @endif
    </div>

    @include("actividad_comercial.components.Buscador")

    @include("actividad_comercial.components.TableData")

    <div class="row">
        <div class="col-sm-5">
            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('contableM.mostrando')}} {{1 + (($datos->currentPage() - 1) * $datos->perPage())}} / {{count($datos) + (($datos->currentPage() - 1) * $datos->perPage())}} {{trans('laboratorio.de')}} {{$datos->total()}} {{trans('contableM.registros')}}</div>
        </div>
        <div class="col-sm-7">
            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                {{ $datos->appends(Request::only(['fecha_desde', 'fecha_hasta', 'id_cliente']))->links() }}
            </div>
        </div>
    </div>
</div>

<button type="button" id="btn_modal" class="hidden" data-toggle="modal" data-target="#exampleModal" data-whatever="@mdo">Open modal for @mdo</button>
@include('actividad_comercial.ModalStoreEstados')




<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const viewModal = (id, e) => {
        document.getElementById('estado_transaccion').value = e.value
        document.getElementById('id_actividad').value = id;
        if (e.value != 1) {
            document.getElementById('btn_modal').click();
        } else {
            Swal.fire({
                title: "Esta seguro?",
                text: "No podra reversar esta accion",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Aceptar"
            }).then((result) => {
                if (result.isConfirmed) {
                    guardarEstadoTipo();
                }
            });
        }
    }

    const deleteActividad = (id) => {
        Swal.fire({
            title: "Esta seguro?",
            text: "No podra reversar esta accion",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Aceptar"
        }).then((result) => {
            $.ajax({
                type: "POST",
                url: "{{ route('actividad_comercial.delete') }}",
                data: {
                    "id_actividad": id,
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data) {
                    if (data.status == 'success') {
                        return Swal.fire({
                            icon: 'success',
                            title: "Exito...",
                            text: data.message,
                        }).then(() => window.location.href = "{{ route('actividad_comercial.index') }}")
                    } else {
                        return Swal.fire({
                            icon: 'error',
                            title: "Error...",
                            text: data.message,
                        })
                    }
                }
            })
        });
    }

    $(document).ready(function() {
        $(".select2_nombres_clientes").each(function() {
            let placeholderText = this.id === 'identificacion_cliente' ?
                '' :
                '';
            let oldp = this.id === 'identificacion_cliente' ? 1 : 0; // 1 cedula 0 nombre

            $(this).select2({
                placeholder: placeholderText,
                allowClear: true,
                ajax: {
                    url: "{{ route('venta.clientesearch') }}",
                    data: function(params) {
                        var id = '';
                        var query = {
                            search: params.term,
                            id_cliente: id,
                            type: oldp
                        }
                        return query;
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                }
            });

            $(this).on('select2:select', function(e) {
                let selectedElement = e.params.data;

                let newOptionN = new Option(selectedElement.nombre, selectedElement.id, true,
                    true);
                let newOptionC = new Option(selectedElement.id, selectedElement.id, true, true);
                let appendo = oldp == 1 ? newOptionN : newOptionC;
                let nombre = oldp == 1 ? 'cliente_nombre' : 'identificacion_cliente';
                document.getElementById("direccion").value = selectedElement.direccion_representante

                $(`#${nombre}`).append(appendo).trigger('change');
                // $("#direccion").val(selectedElement.direccion_representante);
            });
        });
    });
</script>

@endsection