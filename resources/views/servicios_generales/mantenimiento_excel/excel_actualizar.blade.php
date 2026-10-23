@extends('servicios_generales.mantenimiento_excel.base')
@section('action-content')
<div class="box">
    <input type="hidden" name="id_examen" id="id_examen" value="{{$examen->id}}">
    <div class="box-header">
        <div class="col-md-10">
            <h5><b>EDITAR EXAMEN</b></h5>
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
            <label for="fecha" class="col-md-4 texto">Otros Nombres</label>
            <div class="col-md-8">
                <input id="nombre_largo" name="nombre_largo" type="text" value="{{$examen->nombre_largo}}" class="form-control">
            </div>
        </div>
        <div class="form-group col-md-6">
            <label for="fecha" class="col-md-4 texto">Sugerencia</label>
            <div class="col-md-8">
                <input id="sugerencia" name="sugerencia" type="text" value="{{$examen->sugerencia}}" class="form-control" >
            </div>
        </div>
        <div class="form-group col-md-12">
            <div class="text-center">
                <button class="btn btn-primary" type="button" onclick="edit()">
                    Guardar
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

        if (document.getElementById("nombre_largo").value == '' || document.getElementById("sugerencia").value == '') {
            Swal.fire({
                position: 'top-center',
                icon: 'error',
                title: 'Los campos no pueden ser vacios',
                showConfirmButton: false,
                timer: 1500
            });
        } else {
            $.ajax({
                url: "{{route('documento.update_excel')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: {
                    'nombre_largo': document.getElementById("nombre_largo").value,
                    'sugerencia': document.getElementById("sugerencia").value,
                    'id_examen': document.getElementById("id_examen").value,
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

                        window.history.back()
                    }

                },
                error: function(xhr, status) {
                    console.log('Existió un problema');
                },
            });
        }





    }
</script>

@endsection
