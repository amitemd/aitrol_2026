@extends('mantenimientos_comisiones.doctores.base')
@section('action-content')
    <style>
        th {
            text-align: center;
        }
    </style>
    <section class="content">
        <div class="box">
            <div class="box-body">
                <div class="form-group col-md-12  text-right">
                    <div class="col-md-12">
                        <a href="{{ route('doctores_convenios.crear') }}" class="btn btn-warning">
                            crear
                        </a>
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
                                        <th>Codigo</th>
                                        <th>Estado</th>
                                        <th>{{ trans('prod_tar.accion') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($registros as $value)
                                        <tr>
                                            <td>
                                                {{ $value->nombre }}
                                            </td>

                                            <td>
                                                {{ $value->codigo }}
                                            </td>

                                            <td>
                                                @if ($value->estado == 1)
                                                    <button class="btn btn-xs btn-success">
                                                        activo
                                                    </button>
                                                @else
                                                    <button class="btn btn-xs btn-danger">
                                                        inactivo
                                                    </button>
                                                @endif

                                            </td>
                                            <td>
                                                <a class="btn btn-xs btn-danger" href="{{ route('doctores_convenios_editar.editar', ['id' => $value->id]) }}">
                                                    Editar
                                                </a>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function guardar() {


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
