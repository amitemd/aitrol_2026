<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usos de equipos</title>
    <style>
        #page_pdf {
            width: 100%;
            margin: 5px auto 5px auto;
        }

        .title {
            padding: 10px;
            font-size: 19px;
            text-align: center;
            font-weight: bold;
        }

        .title_red {
            padding: 10px;
            font-size: 19px;
            text-align: center;
            font-weight: bold;
            color: tomato;
        }

        #cupesr {
            width: 100%;
        }
    </style>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>

<body>
    <div id="page_pdf">
        <div class="title"> <span>USO DE EQUIPOS</span> </div>
        <div id="cupesr">



            @foreach($group_productos as $key=>$values)
            @php
            $mes = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"][date('m',strtotime($fecha_desde)) - 1];
            //dd($key);
            $nombre= \Sis_medico\Ct_productos::where('codigo',$key)->first();
            @endphp


            @php
            $contador=1;
            //dd($group_productos);
            @endphp
            @foreach($values as $value)
            @php
            //dd($value['id_paciente']);
            $verified= \Sis_medico\Ct_Factura_Omni::find($value['id_omni']);
            $paciente= \Sis_medico\Paciente::find($value['id_paciente'])->first();

            @endphp
            @if($verified->tipo_factura==2)
            @if($contador==1)
            <div class="title_red">{{$mes}}</div>
            <div class="title_red">{{$nombre->nombre}}</div>
            <div></div>
            @endif
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{$contador}}</td>
                        <td></td>
                        <td>{{$value['fecha_procedimiento']}}</td>
                        <td>{{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}}</td>
                        <td>{{$paciente->seguro->nombre}}</td>
                        <td>{{$value['nombre_principal']}}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            @endif
            @php
            $contador++;
            @endphp
            @endforeach


            @endforeach


        </div>
    </div>
</body>

</html>