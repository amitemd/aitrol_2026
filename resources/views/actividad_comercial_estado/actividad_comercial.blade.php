@extends('actividad_comercial_estado.base_actividad')
@section('action-content')

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actividad comercial</title>

    <style>
        .buttonEnviar {
            background-color: #1E90FF !important;
            border: none;
            color: white;
            padding: 10px 30px;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            margin: 20px 0px;
            font-weight: 600;
        }
    </style>

</head>

<body>
    <div class="box-body" style="background-color: white;margin-left: 50px;margin-right: 50px;">
        <br>
        <br>
        <div class="text-center"><b>Ingrese tipo de Actividad</b></div>
        <br>
        <div class="text-center">
            <input style="width: 30%; height: 40px;  border-radius: 10px;" class="rounded-md" type="text" name="actividad" id="actividad">
        </div>
        <br>
        <div class="text-center">
            <button class="buttonEnviar" style="border-radius: 10px;" onclick="guardar_actividad()">Guardar</button>
        </div>
    </div>

</body>

</html>

<script>
    function guardar_actividad() {



        $.ajax({
            url: "{{ url('/actividad/estados/comercial/guardar') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: {
                actividad: $('#actividad').val()
            },
            dataType: 'json',
            type: 'post',


            success: function(data) {

                if (data.status === "error") {
                    return Swal.fire({
                        title: "error",
                        text: data.msj,
                        icon: data.status
                    })
                }

                Swal.fire({
                    title: "Exito",
                    text: data.msj,
                    icon: data.status
                })

                if (data.status === "correcto") {

                    location.href = "{{ url('actividad/estados/comercial/') }}";

                }


            },
            error: function(xhr, status, error) {
                console.log('Error: ' + xhr.responseText);
            },

        });
    }
</script>

@endsection