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
                <input type="hidden" value="{{$registro->id}}" name="id" id="id">
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
                        <input type="text" class="form-control" id="codigo" value="{{$registro->codigo}}" />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="nombre" class="col-sm-1 col-form-label">Nombre</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" id="nombre" value="{{$registro->nombre}}"/>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="estado" class="col-sm-1 col-form-label">Estado</label>
                    <div class="col-sm-5">
                        <select name="estado" id="estado" class="form-control">
                            <option value="">Seleccione</option>
                            <option {{$registro->estado == 1 ? 'selected' : ''}} value="1">Activo</option>
                            <option {{$registro->estado == 0 ? 'selected' : ''}} value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-success bt-xs" onclick="editar()">
                            Editar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function editar() {
            $.ajax({
                url: "{{ route('doctores_save_editar.editar') }}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: {
                   'codigo':$("#codigo").val(),
                   'nombre':$("#nombre").val(),
                   'estado':$("#estado").val(),
                   'id':$("#id").val(),
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
                        location.href="{{route("doctores_convenios.index")}}";
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
