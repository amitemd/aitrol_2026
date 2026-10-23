@extends('auditoria.base')
@section('action-content')
    <section class="content">
        <div class="box">
            <div class="box-header">
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h3>Pacientes con codigo activo</h3>
                        </div>
                    </div>
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <form method="POST" action="{{ route('caducidad_codigo.buscar') }}">
                                {{ csrf_field() }}
                                <div class="col-sm-12">
                                    <div class="col-sm-1">
                                        <label for="cedula">Cédula</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="text" name="cedula" id="cedula" class="form-control">
                                    </div>
                                    <div class="col-sm-1">
                                        <label for="nombres">Nombres</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="text" name="nombres" id="nombres" class="form-control">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="submit" class="btn btn-danger">
                                            Buscar
                                        </button>
                                    </div>

                                </div>
                            </form>
                            <div class="col-sm-12">
                                <table id="example" class="table table-bordered table-hover dataTable" role="grid"
                                    aria-describedby="example2_info">
                                    <thead style="background-color: #337ab7;color:white;">
                                        <tr role="row" id="cabezera">
                                            <th>Nombre del Paciente</th>
                                            <th>Cédula</th>
                                            <th>Fecha de Validación</th>
                                            <th>Código de Validacion</th>
                                            <th>Seguro</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cuerpo">
                                        @foreach ($usuarios as $value)
                                            <tr>

                                                <td>
                                                    {{ $value->nombre1 }} {{ $value->apellido1 }}
                                                </td>

                                                <td>
                                                    {{ $value->id_paciente }}
                                                </td>

                                                <td>
                                                    {{ $value->fecha_val }}

                                                </td>

                                                <td>
                                                    {{ $value->cod_val }}

                                                </td>
                                                <td>
                                                    {{ $value->nombreseguro }}
                                                </td>

                                                <td>
                                                    <a onclick='abrir_modal(`{{ $value->id_importante }}`)'
                                                        class="btn btn-xs btn-primary">Editar</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">
                                    {{ trans('tecnicof.showing') }}
                                    {{ 1 + ($usuarios->currentPage() - 1) * $usuarios->perPage() }} /
                                    {{ count($usuarios) + ($usuarios->currentPage() - 1) * $usuarios->perPage() }}
                                    {{ trans('tecnicof.of') }} {{ $usuarios->total() }} {{ trans('tecnicof.records') }}
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                    {{ $usuarios->appends(['codigo_valido' => $usuarios->currentPage()])->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h3>Pacientes con codigo caducado</h3>
                        </div>
                    </div>
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <form method="POST" action="{{ route('caducidad_codigo.buscar_codigo_no_valido') }}">
                                {{ csrf_field() }}
                                <div class="col-sm-12">
                                    <div class="col-sm-1">
                                        <label for="cedula_sin_codigo">Cédula</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="text" name="cedula_sin_codigo" id="cedula_sin_codigo" class="form-control">
                                    </div>

                                    <div class="col-sm-1">
                                        <label for="nombres_sin_codigo">Nombres</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="text" name="nombres_sin_codigo" id="nombres_sin_codigo" class="form-control">
                                    </div>

                                    <div class="col-sm-2">
                                        <button type="submit" class="btn btn-danger">
                                            Buscar
                                        </button>
                                    </div>

                                </div>
                            </form>
                            <div class="col-sm-12">
                                <table id="example" class="table table-bordered table-hover dataTable" role="grid"
                                    aria-describedby="example2_info">
                                    <thead style="background-color: #337ab7;color:white;">
                                        <tr role="row" id="cabezera">
                                            <th>Nombre del Paciente</th>
                                            <th>Cédula</th>
                                            <th>Fecha de Validación</th>
                                            <th>Código de Validacion</th>
                                            <th>Seguro</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cuerpo">
                                        @foreach ($usuarios_sin_codigo as $val)
                                            <tr>

                                                <td>
                                                    {{ $val->nombre1 }} {{ $val->apellido1 }}
                                                </td>

                                                <td>
                                                    {{ $val->id_paciente }}
                                                </td>

                                                <td>
                                                    {{ $val->fecha_val }}

                                                </td>

                                                <td>
                                                    {{ $val->cod_val }}

                                                </td>
                                                <td>
                                                    {{ $val->nombreseguro }}
                                                </td>

                                                <td>
                                                    <a onclick='abrir_modal(`{{ $val->id_importante }}`)'
                                                        class="btn btn-xs btn-primary">Editar</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">
                                    {{ trans('tecnicof.showing') }} 1 / {{ count($usuarios_sin_codigo) }}
                                    {{ trans('limpieza_equipof.de') }} {{ $usuarios_sin_codigo->total() }}
                                    {{ trans('tecnicof.register') }}</div>
                            </div>
                            <div class="col-sm-7">
                                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">

                                    {{ $usuarios_sin_codigo->appends(['codigo_invalido' => $usuarios_sin_codigo->currentPage()])->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>

    <div class="modal" tabindex="-1" role="dialog" id="modal_abrir">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title">EDITAR</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="idEn" id="idEn">
                    <div class="row">
                        <div class="col-md-2">
                            <label for="codigo">Código</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="codigo" id="codigo">
                        </div>

                        <div class="col-md-2">
                            <label for="fecha_vali">Fecha</label>
                        </div>
                        <div class="col-md-4">
                            <input type="date" class="form-control" name="fecha_vali" id="fecha_vali">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="guardarId()">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        function abrir_modal(id) {

            $.ajax({
                url: "{{ route('caducidad_codigo.buscador') }}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: {
                    'id': id,

                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.status) {
                        
                        $("#idEn").val(data.array.id);
                        $("#codigo").val(data.array.cod_val);
                        $("#fecha_vali").val(data.array.fecha_val);
                        $('#modal_abrir').modal();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Ocurrio un error!',

                        })
                    }
                },
                error: function(xhr, status) {
                    alert("Hubo un error");
                },
            });
        }

        function guardarId() {

            let idN = document.getElementById("idEn").value;


            $.ajax({
                url: "{{ route('caducidad_codigo.actualizar') }}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: {
                    'id': idN,
                    'codigo': document.getElementById("codigo").value,
                    'fecha_vali': document.getElementById("fecha_vali").value,
                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Perfecto...',
                            text: 'El campo se actualizo!',
                        });

                        location.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Ocurrio un error!',

                        })
                    }
                },
                error: function(xhr, status) {
                    alert("Hubo un error");
                },
            });
        }
    </script>
@endsection
