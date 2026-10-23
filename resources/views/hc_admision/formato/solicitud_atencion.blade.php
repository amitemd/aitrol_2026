<html>

<head>
    <title>solicitud atencion </title>

</head>

<style>
    @page {
        margin: 10 0
    }

    /*body{
        background-image:  url("{{base_path().'/storage/app/logo/logo_consentimiento.jpg'}}");
        background-repeat: no-repeat;
        background-size:   100%; 
    }*/


    /*.content {

        /*margin-top:  50px;
        margin-left: 100px;
        margin-right: 100px;

    }*/
    body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            position: relative;
        }
        .footer {
            position: fixed;
            bottom: 450px;
            left: 0;
            width: 100%;
            text-align: center;
            
        }
        .content {
            padding-bottom: 50px; /* Espacio suficiente para que el contenido no se solape con el footer */
            margin-left: 100px;
            margin-right: 100px;
        }
</style>

<body>
    <div class="content">
        @php
        $tipo_usuario = Auth::user()->id;
        @endphp
        @if($tipo_usuario == '0922290697')

        @endif



        <img src="{{base_path().'/storage/app/logo/logo_cabecera.png'}}" alt="">

        <div>
            <div style="background: white; position: absolute; top: 10px;"></div>
            <div style="text-align: center">
                <div>SOLICITUD DE ATENCIÓN POR SEGURO PÚBLICO </div>
            </div>
            <br>
            <br>
            <br>
            <br>
            <br>
            <div>
                @php $ames = ['0', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'] @endphp
                <br>
                <div>{{ $empresa->ciudad }}, {{ date('d',strtotime($agenda->fechaini)) }} de {{ $ames[(int)date('m',strtotime($agenda->fechaini))] }} del
                    {{ date('Y',strtotime($agenda->fechaini)) }}
                </div>
                <br>
                <div>
                    <div style="">Señores, </div>
                </div>
                <br>
                <div>
                    <div>{{ $empresa->nombrecomercial }}</div>
                </div>
                <br>
                <div style="text-align: justify;">
                    @if($paciente->id_usuario != $paciente->id )
                    @php $usuario = $paciente->usuario @endphp
                    <div>Por medio de la presente, Yo {{ $paciente->apellido1familiar }} {{ $paciente->apellido2familiar }} {{ $paciente->nombre1familiar }} {{ $paciente->nombre2familiar }}, con cédula de ciudadanía N°
                        {{ $paciente->cedulafamiliar }}, en calidad de representante legal de {{ $paciente->apellido1 }} {{ $paciente->apellido2 }}
                        {{ $paciente->nombre1 }} {{ $paciente->nombre2 }}, con cédula de ciudadanía N° {{ $paciente->id }} solicito que la atención médica sea por medio del seguro
                        {{ $paciente->seguro->nombre }}
                    </div>
                    @else
                    <div>Por medio de la presente, Yo {{ $paciente->apellido1 }} {{ $paciente->apellido2 }} {{ $paciente->nombre1 }} {{ $paciente->nombre2 }}, con cédula de ciudadanía N°
                        {{ $paciente->id }}, en calidad de paciente; solicito que la atención médica sea por medio del seguro
                        {{ $paciente->seguro->nombre }}
                    </div>
                    @endif
                </div>
                <br>
                <div style="">
                    <div>_________________________</div>
                    <div>Firma del solicitante</div>
                    @if($paciente->id_usuario != $paciente->id )
                    <div>C.I.: {{$paciente->cedulafamiliar}}</div>
                    @else
                    <div>C.I.: {{$paciente->id}}</div>
                    @endif
                </div>
            </div>
        </div>

    </div>
    <div class="footer">
        <img src="{{base_path().'/storage/app/logo/logo_footer.png'}}" style="width:100%" alt="" >
    </div>
</body>

</html>