@extends('mantenimientos_comisiones.comisiones.base')
@section('action-content')
    <style>
        th {
            text-align: center;
        }
    </style>
    <section class="content">
        @if (session('message'))
            <div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {{ session('message') }}</em>
            </div>
        @endif
        <div class="box">
            <div class="box-header">
            </div>
            <div class="box-body">
                <div class="form-group col-md-10">
                    <div class="row">
                        <div class="form-group col-md-10 ">
                            <div class="col-md-12">
                                <h4><b>{{ trans('prod_tar.producto') }}:</b> {{ $producto->nombre }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group col-md-2 text-right">
                    <div class="col-md-7">
                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalcrear">
                            crear
                        </button>
                    </div>
                </div>

                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                    <div class="row" id="listado">
                        <div class="table-responsive col-md-12">
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid"
                                aria-describedby="example2_info">
                                <thead>
                                    <tr>
                                        <th>Doctor</th>
                                        <th>Porcentaje Particular</th>
                                        <th>Valor Particular</th>
                                        <th>Porcentaje Privado</th>
                                        <th>Valor Privado</th>
                                        <th>Estado</th>
                                        <th>{{ trans('prod_tar.accion') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($all_producto as $val)
                                    @php
                                      
                                    @endphp

                                        <tr>
                                            <td>
                                                {{ $val->doctor_nombre->nombre }}
                                            </td>
                                            <td>
                                                {{ $val->porcentaje }}
                                            </td>
                                            <td>
                                                {{ $val->valor }}
                                            </td>
                                            <td>
                                                {{$val->porcentaje_privado}}
                                            </td>
                                            <td>
                                                {{$val->valor_privado}}
                                            </td>
                                            <td>
                                                @if ($val->estado == 0)
                                                    <button class="btn btn-danger btn-xs">
                                                        INACTIVO
                                                    </button>
                                                @else
                                                    <button class="btn btn-success btn-xs">
                                                        ACTIVO
                                                    </button>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-xs"
                                                    onclick="modalEditar({{ $val->id }})">
                                                    Editar
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalcrear" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Agregar valor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('comisiones.store') }}">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="doctor" class="col-form-label">Doctor:</label>
                            <select name="doctor" id="doctor" class="form-control" required>
                                <option value="">Seleccione</option>
                                @foreach ($doctores as $value)
                                    <option value="{{ $value->id }}">
                                        {{ $value->codigo }} || {{ $value->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="producto" class="col-form-label">Producto:</label>
                            <input type="hidden" name="producto" value="{{ $producto->id }}">
                            <input type="text" class="form-control" id="producto" value="{{ $producto->nombre }}"
                                readonly>
                        </div>

                        <div class="form-group">
                            <label for="porcentaje" class="col-form-label">Porcentaje Particular:</label>
                            <input type="number" class="form-control" id="porcentaje" name="porcentaje" min="0"
                                step="any" required />
                        </div>

                        <div class="form-group">
                            <label for="precio" class="col-form-label">Precio Particular:</label>
                            <input type="number" class="form-control" id="precio" name="precio" min="0"
                                step="any" required />
                        </div>


                        <div class="form-group">
                            <label for="porcentaje_privado" class="col-form-label">Porcentaje Privados:</label>
                            <input type="number" class="form-control" id="porcentaje_privado" name="porcentaje_privado" min="0"
                                step="any" required />
                        </div>

                        <div class="form-group">
                            <label for="precio_privado" class="col-form-label">Precio Privados:</label>
                            <input type="number" class="form-control" id="precio_privado" name="precio_privado" min="0"
                                step="any" required />
                        </div>


                        <div class="form-group">
                            <label for="estado" class="col-form-label">Estado:</label>
                            <select name="estado" id="estado" class="form-control">
                                <option value="">Seleccione</option>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Guardar</button>
                    <button type="button" class="btn btn-danger">cerrar</button>
                </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modaleditar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">

    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function modalEditar(id) {


            $.ajax({
                url: "{{ route('modal.store') }}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: {
                    id: id
                },
                type: 'POST',
                dataType: 'json',
                success: function(data) {

                    $("#modaleditar").html(data.html);
                    $("#modaleditar").modal('show');

                },
                error: function(xhr, status) {
                    alert(`{{ trans('limpieza_equipof.existioproblema') }}`);
                },
            });


        }

        function formulario_editar() {


            $.ajax({
                url: "{{ route('modal.update') }}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: {
                    formulario: $("#formulario_editar").serialize(),
                },
                type: 'POST',
                dataType: 'json',
                success: function(data) {

                    if (data.status) {
                        Swal.fire(
                            'Editado Correctamente',
                            '',
                            'success'
                        );
                        location.reload();
                    } else {

                        Swal.fire(
                            'Ocurrio un error',
                            data.msj,
                            'error'
                        )
                    }


                },
                error: function(xhr, status) {
                    alert(`{{ trans('limpieza_equipof.existioproblema') }}`);
                },
            });

        }
    </script>
@endsection
