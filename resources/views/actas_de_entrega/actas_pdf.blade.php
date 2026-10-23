<!--Logo mdconsgroup-->
<html>
<head>
<title>
@php
    $tipo_pag = DB::table('tipo_acta_de_entrega')->find($datos->id_tipo);
@endphp  
    acta_de_entrega_{{$datos->fecha}}_{{ strtolower($tipo_pag->nombre) }}
</title>
    <style>
        
        .fecha {
            padding-left: 60px;
            margin-top: 15px;   
        }
        .titulo {
            text-align: center; 
            font-weight: bold; 
            text-transform:uppercase;
        }
        .parrafo {
            padding-left: 60px; 
            padding-right: 40px; 
            text-align: justify; 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 13;
        }
        .columna1 {
            width: 33%; 
            float: left; 
            margin-top: 3em; 
            margin-left: 60px;
        }
        .columna2 {
            width: 33%; 
            float: right; 
            margin-right: 60px; 
            margin-top: 3em;
        }
        header {
            position: fixed;
            top: 0px;
            left: 0px;
            right: -40px;
            height: 50px;
            font-size: 20px;
            text-align: left;
            line-height: 35px;
            z-index: -1;
        }
        footer {
            position: fixed;
            bottom: -65px;
            left: -45px;
            right: -45px;
            height: 380px;
            font-size: 20px;
            z-index: -1;
            color:black;-->
            text-align: center;
            line-height: 35px;
        }
        .footer-text {
            position: absolute; 
            align-items: right;
            margin-right: 50px;
            top: 230px; 
            left: 0%; 
            width: 100%; 
            height: 100%;
        }

        .columna1 p {
            border-top: 1px solid black;
            padding-top: 5px;   
        }

        .columna2 p {
            border-top: 1px solid black;
            padding-top: 5px;   
        }

    </style>
</head>
<body>
        @php
            $empresa= Sis_medico\Empresa::find($id_empresa);
        @endphp

    <header>
        @if ($logo_empresa != null)
        <img src="{{base_path().'/storage/app/logo/' .$logo_empresa}}" style="width: 200px; height: auto;">
        @endif
    </header>  

    <!-- <footer>
        @if ($logo_receta != null)
        <img src="{{base_path().'/storage/app/logo/' .$logo_receta}}" style="width: 100%; height: auto;">
        @endif
    </footer> -->

<br>
<br>
<br>

<main>

<div class="fecha">
    <p>@if(!is_null($empresa)) {{$empresa->ciudad}}, @endif {{ substr($datos->fecha, 8, 2)}} de {{ $meses[substr($datos->fecha, 5, 2)-1] }} del {{ substr($datos->fecha, 0, 4)}}</p>
</div>

<div style="margin-top: 4em">
    <p class="titulo">acta de entrega</p>
</div>


<div style="margin-top: 3em">    
    @if ($datos->id_tipo == 1)
    <p class="parrafo">
        Por medio de la presente, se deja constancia de la entrega del celular {{ strtoupper($datos->descripcion) }} con {{ strtolower($datos->info_adicional) }}, 
        consta con @if (strlen($datos->imei) > 15 ) {{"los"}} @else {{"el"}} @endif imei {{ substr($datos->imei, 0, 15) }}, {{ substr($datos->imei, 15) }}  con el número celular {{ $datos->numero_cel }} con plan
        corporativo a <b>{{ ucfirst(strtolower($usuario_rec->apellido1)) }} @if ($usuario_rec->apellido2 != "N/A" && $usuario_rec->apellido2 != "(N/A)") {{ ucfirst(strtolower($usuario_rec->apellido2)) }} @endif
        {{ ucfirst(strtolower($usuario_rec->nombre1)) }} @if ($usuario_rec->nombre2 != "N/A" && $usuario_rec->nombre2 != "(N/A)") {{ ucfirst(strtolower($usuario_rec->nombre2)) }} @endif</b>con
        cédula {{ $datos->id_usuario_recibe }}, en caso de daño del equipo o alguna de sus piezas la responsabilidad es del empleado la reposición del mismo.
    </p>
    @elseif ($datos->id_tipo == 2)
    <p class="parrafo">
        Por medio de la presente, se deja constancia de la entrega de la {{ strtoupper($datos->descripcion) }} con código <b>{{ strtolower($datos->codigo_tarjeta) }}</b>, 
        y {{ strtolower($datos->info_adicional) }} a <b>{{ strtoupper($usuario_rec->nombre1) }} @if ($usuario_rec->nombre2 != "N/A" && $usuario_rec->nombre2 != "(N/A)") {{ strtoupper($usuario_rec->nombre2) }} @endif 
        {{ strtoupper($usuario_rec->apellido1) }} @if ($usuario_rec->apellido2 != "N/A" && $usuario_rec->apellido2 != "(N/A)") {{ strtoupper($usuario_rec->apellido2) }} @endif
        </b>, para el acceso a las instalaciones del Edificio 100 Business Plaza Off 201-204, Off 804-805 y Off 504 y al parqueo del mismo Horario habilitado de 
        lunes a sábado de 08h00 a 19h00, en caso de pérdida se hará un recargo por reposición de 25.00 USD por cada una de ellas.
    </p>
     @else
    <p class="parrafo">
        Por medio de la presente, se deja constancia de la entrega de la {{ strtoupper($datos->descripcion) }} con {{ strtolower($datos->info_adicional) }}, 
        modelo {{ $datos->modelo_computador }} a <b>{{ ucfirst(strtolower($usuario_rec->nombre1)) }} @if ($usuario_rec->nombre2 != "N/A" && $usuario_rec->nombre2 != "(N/A)") {{ ucfirst(strtolower($usuario_rec->nombre2)) }} @endif 
        {{ ucfirst(strtolower($usuario_rec->apellido1)) }} @if ($usuario_rec->apellido2 != "N/A" && $usuario_rec->apellido2 != "(N/A)") {{ ucfirst(strtolower($usuario_rec->apellido2)) }} @endif </b> con cédula {{ $datos->id_usuario_recibe }}, 
        en caso de daño del equipo o alguna de sus piezas la responsabilidad es del empleado la reposición del mismo.
    </p>
    @endif
</div>
<br>
<br>
<br>
<br>
<br>
<br>

    <div class="columna1"> 
        Entregado Por:
        <div>
            <br><br><br><br>
        </div>
        <p><b>{{ ucfirst(strtolower( $usuario_ent->nombre1 )) }} {{ ucfirst(strtolower( $usuario_ent->apellido1 )) }} @if ($usuario_ent->apellido2 != "N/A" && $usuario_ent->apellido2 != "(N/A)") {{ ucfirst(strtolower( $usuario_ent->apellido2 )) }} @endif
            <!-- Cargo -->
            <br> @if ($usuario_ent->cargo == null) {{$nombre_empresa}} @else {{ ucfirst(strtolower( $usuario_ent->cargo )) }} @endif
            <br> C.I.:</b> {{ $datos->id_usuario_entrega }}   
        </p>
    </div>
    <div class="columna2"> 
        Recibido Por:
        <div>
            <br><br><br><br>
        </div>

        <p><b>{{ ucfirst(strtolower( $usuario_rec->nombre1 )) }} {{ ucfirst(strtolower( $usuario_rec->apellido1 )) }} @if ($usuario_rec->apellido2 != "N/A" && $usuario_rec->apellido2 != "(N/A)") {{ ucfirst(strtolower( $usuario_rec->apellido2 )) }} @endif
            <!-- Cargo -->
            <br> @if ($usuario_rec->cargo == null) {{$nombre_empresa}} @else {{ ucfirst(strtolower( $usuario_rec->cargo )) }} @endif
            <br> C.I.:</b> {{ $datos->id_usuario_recibe }}
        </p>
    </div>
</main> 

<div style="position: fixed; left: 5px; bottom: -80px; right: 5px; height: 200px;">
    <div style="float: left;width: 100%;text-align: center;">
    @if($logo_receta != null)
    <img src="{{base_path().'/storage/app/logo/' .$logo_receta}}" style="width: 100%; height: auto;">
    @endif  
    </div>
    <!-- <div style="float: left;width: 50%;text-align: center;">
    @if($logo_receta != null)
    <img src="{{base_path().'/storage/app/logo/' .$logo_receta}}" style="width: 100%; height: auto;">
    @endif  
    </div> -->
</div> 

</body>
</html>


