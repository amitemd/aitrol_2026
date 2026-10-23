<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Etiquetas de Frascos</title>

</head>

<body>
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
            font-family: 'monospace' !important;
            font-weight: 600 ;
        }



    </style>

    <main>
        @if($hc4_biopsias != [])
        @php $i = 1; @endphp
            @foreach($hc4_biopsias as $biopsia)
                @if($i != 1)
                <div style="page-break-after:always;"></div>
                @endif
                <div style="text-align: center; font-size: 19.5px" class="no">
                    <br>
                    <b>Fecha:</b> @if(isset($biopsia->historia_clinica)) {{substr($biopsia->historia_clinica->fecha_atencion,0,10)}} @endif
                    <br><b>N° Frasco:</b> {{$biopsia->numero_frasco}} 
                    <br> {{$biopsia->descripcion_frasco}}
                    <br><b>C.I:</b> @if(isset($biopsia->pacientes)) {{$biopsia->pacientes->id}} @endif
                    <br><b>Pcte:</b>@if(isset($biopsia->pacientes)) {{$biopsia->pacientes->nombre1}} {{$biopsia->pacientes->nombre2}} <br> {{$biopsia->pacientes->apellido1}} {{$biopsia->pacientes->apellido2}} @endif 
                </div>

                @php $i = 2; @endphp
                
            @endforeach
        @endif
</body>

</html>