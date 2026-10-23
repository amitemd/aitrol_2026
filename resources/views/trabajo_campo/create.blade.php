@extends('trabajo_campo.base')
@section('action-content')


<div class="box">
    <div class="box-header">
        <div class="col-md-10">
            <h5><b>Creación del trabajo de campo</b></h5>
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
            <label for="fecha" class="col-md-4 texto">Fecha Inicio</label>
            <div class="col-md-8">
                <input id="fechaInicio" name="fechaInicio" type="datetime-local" class="form-control" required placeholder="fecha inicio" autofocus>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label for="fecha" class="col-md-4 texto">Fecha Fin</label>
            <div class="col-md-8">
                <input id="fechaFin" name="fechaFin" type="datetime-local" class="form-control" placeholder="fecha fin" >
            </div>
        </div>
        <div class="form-group col-md-12">
            <label for="fecha" class="col-md-2 texto">Lugar</label>
            <div class="col-md-10">
                <input id="lugar" name="lugar" type="text" class="form-control"  required placeholder="Lugar" >
            </div>
        </div>
        <div class="form-group col-md-12">
            <label for="fecha" class="col-md-2 texto">Actividades</label>
            <div class="col-md-10">
                <textarea id="obs" name="obs" type="text" class="form-control" placeholder="observaciones" ></textarea>
            </div>
        </div>
        <div class="form-group col-md-12">
            <div class="text-center">
                <button class="btn btn-primary" type="button" onclick="save()">
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

    function save() {
        let obs = document.getElementById("obs").value;
        flag = "";
        if (!(obs.trim().length > 0)) {
            flag = "El campo observaciones no puede estar vacio \n";
        }
        var value=$.trim($("#lugar").val());

        if(value.length<= 0)
        {
            flag = flag+"El campo lugar no puede estar vacio \n";
        }

        var value2=$.trim($("#fechaInicio").val());

        if(value2.length<= 0)
        {
            flag = flag+"El campo Fecha Inicio no puede estar vacio \n";
        }


        if (flag == "") {

            $.ajax({
                url: "{{route('trabajo_campo_save')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: {
                    'fecha_desde': document.getElementById("fechaInicio").value,
                    'fecha_hasta': document.getElementById("fechaFin").value,
                    'obs': document.getElementById("obs").value,
                    'lugar': document.getElementById("lugar").value,
                },
                type: 'POST',
                dataType: 'json',
                success: function(data) {

                    if (data == 'ok') {

                        Swal.fire({
                            position: 'top-center',
                            icon: 'success',
                            title: 'Guardado con exito',
                            showConfirmButton: false,
                            timer: 1500
                        }).then((result) => {
                              if (result.isConfirmed) {
                                location.href = "{{route('trabajo_campo_index')}}";

                              }else{
                                location.href = "{{route('trabajo_campo_index')}}";
                              }
                            });


                    }

                },
                error: function(xhr, status) {
                    alert('Existió un problema');
                },
            });
        } else {

            Swal.fire({
                position: 'top-center',
                icon: 'error',
                title: flag,
                showConfirmButton: false,
                timer: 1000
            });

        }


    }
</script>

@endsection
