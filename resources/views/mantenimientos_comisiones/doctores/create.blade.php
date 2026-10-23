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
                        <button onclick="history.back()" class="btn btn-danger" data-toggle="modal" data-target="#modalcrear">
                            Regresar
                        </button>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="codigo" class="col-sm-1 col-form-label">Código</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" id="codigo" />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="nombre" class="col-sm-1 col-form-label">Nombre</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" id="nombre" />
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-success bt-xs" onclick="guardar()">
                            guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function guardar() {
            $.ajax({
                url: "{{ route('doctores_convenios_guardar.save') }}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: {
                   'codigo':$("#codigo").val(),
                   'nombre':$("#nombre").val(),
                },
                type: 'get',
                dataType: 'json',
                success: function(data) {
                    if (data.status) {
                        Swal.fire(
                            'Guardado Correctamente',
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
