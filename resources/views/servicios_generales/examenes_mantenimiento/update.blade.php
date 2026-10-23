@extends('servicios_generales.examenes_mantenimiento.base')
@section('action-content')
<div class="box">
    <input type="hidden" id="id" name="id" value="{{$examenes->id}}">
    <div class="box-header">
        <div class="col-md-10">
            <h5><b>Editar los tubos</b></h5>
        </div>
        <div class="col-md-2 text-right">
            <button type="button" onclick="goBack()" class="btn btn-danger">
                <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
            </button>
        </div>
    </div>
    <div class="separator"></div>
    <div class="box-body">
        <div class="form-group col-md-6">
            <label for="indice" class="col-md-4 texto">Indice</label>
            <div class="col-md-8">
                <select name="indice" id="indice" class="form-control">
                    <option value="">Seleccione</option>
                    <option {{$examenes->indice_tubos == 'G' ? 'selected' : ''}} value="G">G</option>
                    <option {{$examenes->indice_tubos == 'U' ? 'selected' : ''}} value="U">U</option>
                </select>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label for="fecha" class="col-md-4 texto" >Cantidad</label>
            <div class="col-md-8">
                <input id="cantidad" name="cantidad" type="text" value="{{$examenes->cantidad_tubos}}" class="form-control">
            </div>
        </div>

        <div class="form-group col-md-6">
            <label for="fecha" class="col-md-4 texto" >Tipo Tubo</label>
            <div class="col-md-8">
                <select class="form-control" name="id_labs" id="id_labs">
                    <option value="">Seleccione</option>
                    @foreach($tipo as $val)
                       <option {{$examenes->id_labs_tubo == $val->id ? 'selected' : ''}} value="{{$val->id}}">{{$val->nombre}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group col-md-12">
            <div class="text-center">
                <button class="btn btn-primary" type="button" onclick="edit()">
                    Editar
                </button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
    function goBack() {
        window.history.back()
    }

    function edit() {

        if (document.getElementById("indice").value == '' || document.getElementById("cantidad").value == '') {
            Swal.fire({
                position: 'top-center',
                icon: 'error',
                title: 'Los campos no pueden ser vacios',
                showConfirmButton: false,
                timer: 1500
            });



        } else {

            $.ajax({
                url: "{{route('mantenimientoexcel.update')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: {
                    'indice': document.getElementById("indice").value,
                    'id_labs': document.getElementById("id_labs").value,
                    'cantidad': document.getElementById("cantidad").value,
                    'id': document.getElementById("id").value,
                },
                type: 'POST',
                dataType: 'json',
                success: function(data) {

                    if (data == 'ok') {

                        Swal.fire({
                            position: 'top-center',
                            icon: 'success',
                            title: 'Editado con exito',
                            showConfirmButton: false,
                            timer: 1500
                        });

                        let url = "{{route('mantenimientoexcel.index')}}"
                        window.location = url;
                    } else {
                        Swal.fire({
                            position: 'top-center',
                            icon: 'error',
                            title: 'Hubo un error',
                            showConfirmButton: false,
                            timer: 1500
                        });

                    }

                },
                error: function(xhr, status) {
                    alert('Existió un problema');
                },
            });
        }
    }
</script>

@endsection
