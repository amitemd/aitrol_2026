<!DOCTYPE html>
<html>
    <?php
        $paciente = $orden->paciente; 
        $fecha=date('Y-m-d');
        $hora = date('H:i:s');
        $fechaNacimiento = new DateTime($paciente->fecha_nacimiento);
        $hoy = new DateTime();
        $edad = $hoy->diff($fechaNacimiento)->y;
        $fechaNacimiento = new DateTime($paciente->fecha_nacimiento);
        $diaNacimiento = $fechaNacimiento->format('d');
        $fechaNacimiento = new DateTime($paciente->fecha_nacimiento);
        $mesNacimiento = $fechaNacimiento->format('m');
    ?>

    <head>
        <title>HCU_010</title>
        <style>
            @page {
                margin: 0 0;

            }

            /** Define now the real margins of every page in the PDF **/
            body {
                margin-top: 5px;
                margin-left: 20px;
                margin-right: 20px;
                margin-bottom: 1px;
                font-size: 12px;
            }

            /** Define the header rules **/
            header {
                position: fixed;
                top: 0cm;
                left: 0cm;
                right: 0cm;
                height: 1cm;
            }

            /** Define the footer rules **/
            footer {
                position: fixed;
                bottom: 0cm;
                left: 0cm;
                right: 0cm;
                height: 2cm;
            }

            table {
                width: 100px;
                border-collapse: collapse;
            }

            p {
                padding-top: 0;
                padding-bottom: 0;
                margin: 0;
            }

            div {
                padding-top: 0;
                padding-bottom: 0;
            }

            .td-all-border {
                border-top: 1px solid;
                border-left: 1px solid;
                border-right: 1px solid;
                border-bottom: 1px solid;
            }

            .td-border {
                border-top: 1px solid;
                border-bottom: 1px solid;
                font-size: 9px !important;
            }

            td.contenedor-texto {
                padding: 20px;
                text-align: left;
            }

            ol {
                list-style-type: decimal;
                margin-left: 20px;
            }

            .div_clear {
                clear: both;
            }

            .outer-box {
                width: 33.3%;
                display: inline-block;
                vertical-align: top;
                /*background-color: red;*/

            }
            
            .outer-box2 {
                width: 33%;
                display: inline-block;
                vertical-align: top;
                /*background-color: green;*/

            }

            .outer-box3 {
                width: 33%;
                display: inline-block;
                vertical-align: top;
                /*background-color: yellow;*/
            }

            .outer-box3 {
                width: 33%;
                display: inline-block;
                vertical-align: top;
                /*background-color: yellow;*/
            }

            .inner-box {
                display: block;
                width: 44%;
                background-color: #DDEBF7;
                font-size: 10px;
                float: left;
                border-top: 1px solid black;
                /*border-right: 1px solid black;*/
            }

            .inner-box2 {
                display: block;
                width: 6%;
                float: left;
                border-top: 1px solid black;
                /*border-right: 1px solid black;*/
                text-align: center;
                font-size: 10px;
            }

            .inner-box3 {
                display: block;
                width: 44%;
                background-color: #DDEBF7;
                font-size: 10px;
                float: left;
                border-top: 1px solid black;
                /*border-right: 1px solid black;*/
                font-size: 10px;
            }

            .inner-box4 {
                display: block;
                width: 6%;
                float: right;
                border-top: 1px solid black;
                /*border-right: 1px solid black;*/
                text-align: center;
                font-size: 10px;
            }

            .contenido {
                width: 100%;
            }

            .inner-box21 {
                width: 94%;
                background-color: #DDEBF7;
                font-size: 10px;
                float: left;
                border-top: 1px solid black;
                /*border-right: 1px solid black;*/
            }

            .inner-box22 {
                width: 5%;
                float: right;
                border-top: 1px solid black;
                /*border-right: 1px solid black;*/
                font-size: 10px;
            }

            .molecular {
                position: relative;
                width: 66%;
                top: -50px;

            }

            .table-footer {
                position: relative;
                top: -50px;
            }

            p {
                padding-top: 0;
                padding-bottom: 0;
                margin: 0;
            }

            div {
                padding-top: 0;
                padding-bottom: 0;
            }

            .page-break-before {
                page-break-before: always;
            }

            .page-break-after {
                page-break-after: always;
            }

            .subtitulo0 {
                background-color: #DDEBF7;
                border-top: 1px solid;
                border-left: 1px solid;
                border-right: 1px solid;
                border-bottom: 1px solid;
                color: #000;
                font-size: 14px !important;
            }

            .subtitulo1 {
                text-align: center;
                background-color: #BADAC8;
                font-size: 12px !important;
                border-top: 1px solid;
                border-left: 1px solid;
                border-right: 1px solid;
                border-bottom: 1px solid;
                color: #000;
            }

            .subtitulo2 {
                text-align: center;
                background-color: #BADAC8;
                font-size: 12px !important;
                border-top: 1px solid;
                border-bottom: 1px solid;
                color: #000;
            }
            .contenido1 {
                border-top: 1px solid;
                border-left: 1px solid;
                border-right: 1px solid;
                border-bottom: 1px solid;
                font-size: 14px !important;
                color: #000;
                text-align: center;
            }

            .contenido2 {
                font-size: 14px !important;
                color: #000;
                text-align: center;
            }

            .firma-container {
                width: 250px;
                height: 60px;
                overflow: hidden;
                display: inline-block;
                margin: 0 auto;

            }

            .firma-imagen {
                width: 120%;
                height: 140%;
                object-fit: cover; /* Recorta la imagen para llenar el contenedor */
                object-position: center; /* Centra la imagen dentro del contenedor */
            }
        </style>
    </head>

    <body>
        <main>
            <table border="1" style="width: 100%">
                <tr>
                    <td colspan="12" style="text-align: center;">
                        @if(!is_null($empresa))
                        <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" style="width: 140px;height: 45px">
                        @endif
                    </td>
                </tr>
            </table>

            <table cellpadding="0" cellspacing="0" style="width: 100%">
                <thead>
                    <tr>
                        <td colspan="12" class="subtitulo0"><b>A. DATOS DEL ESTABLECIMIENTO Y USUARIO</b></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="subtitulo1"><b>INSTITUCION DEL SISTEMA</b></td>
                        <td class="subtitulo1"><b>UNICODIGO</b></td>
                        <td class="subtitulo1"><b>ESTABLECIMIENTO DE SALUD</b></td>
                        <td colspan="5" class="subtitulo1"><b>NÚMERO DE HISTORIA CLÍNICA ÚNICA</b></td>
                        <td colspan="3" class="subtitulo1"><b>NÚMERO DE ARCHIVO</b></td>
                        <td class="subtitulo1"><b>No.HOJA</b></td>
                    </tr>
                    <tr>
                        <td class="contenido1" style="height: 18px;">{{$orden->seguro->nombre}}</td>
                        <td class="contenido1">{{$empresa->unicodigo}}</td>
                        <td class="contenido1">{{$empresa->nombrecomercial}}</td>
                        <td class="contenido1" colspan="5">{{$orden->id_paciente}}</td>
                        <td class="contenido1" colspan="3"></td>
                        <td class="contenido1"></td>
                    </tr>
                    <tr>
                        <td rowspan="2" colspan="2" class="subtitulo1"><b>PRIMER APELLIDO</b></td>
                        <td rowspan="2" colspan="1" class="subtitulo1"><b>SEGUNDO APELLIDO</b></td>
                        <td rowspan="2" colspan="2" class="subtitulo1"><b>PRIMER NOMBRE</b></td>
                        <td rowspan="2" colspan="1" class="subtitulo1"><b>SEGUNDO NOMBRE</b></td>
                        <td rowspan="2" colspan="1" class="subtitulo1"><b>SEXO</b></td>
                        <td rowspan="2" colspan="1" class="subtitulo1"><b>EDAD</b></td>
                        <td colspan="4" class="subtitulo1"><b>CONDICIÓN EDAD (MARCAR)</b></td>
                    </tr>
                    <tr>
                        <td class="subtitulo1">D</td>
                        <td class="subtitulo1">H</td>
                        <td class="subtitulo1">M</td>
                        <td class="subtitulo1">A</td>
                    </tr>
                    <tr>
                        <td class="contenido1" colspan="2" style="height: 18px;">{{$paciente->apellido1}}</td>
                        <td class="contenido1">{{$paciente->apellido2}}</td>
                        <td class="contenido1" colspan="2">{{$paciente->nombre1}}</td>
                        <td class="contenido1">{{$paciente->nombre2}}</td>
                        <td class="contenido1">@if($paciente->sexo == 1) Masculino @endif @if($paciente->sexo == 2) Femenino @endif</td>
                        <td class="contenido1">{{$edad}}</td>
                        <td class="contenido1">{{$diaNacimiento}}</td>
                        <td class="contenido1"></td>
                        <td class="contenido1">{{$mesNacimiento}}</td>
                        <td class="contenido1">{{ date('Y', strtotime($paciente->fecha_nacimiento))}}</td>
                    </tr>
                </tbody>
            </table>

            <table cellpadding="0" cellspacing="0" style="width: 100%">
                <thead>
                    <tr>
                        <td colspan="12" class="subtitulo0"><b>B. DATOS DEL SERVICIO</b></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="2" class="subtitulo1">DIAGNOSTICO</td>
                        <td class="subtitulo1">CIE</td>
                        <td rowspan="3" class="subtitulo1">SERVICIO</td>
                        <td class="subtitulo1">EMERGENCIA</td>
                        <td class="contenido1">&nbsp;</td>
                        <td class="subtitulo1">ESPECIALIDAD</td>
                        <td class="contenido1">&nbsp;</td>
                        <td colspan="2" class="subtitulo1">PRIORIDAD</td>
                        <td colspan="2" class="contenido1">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="contenido1">1</td>
                        <td class="contenido1">@if(isset($diagnosticos[0])) {{$diagnosticos[0]['descripcion']}} @endif</td>
                        <td class="contenido1">@if(isset($diagnosticos[0])) {{$diagnosticos[0]['cie10']}} @endif</td>
                        <td class="contenido1">CONSULTA EXTERNA</td>
                        <td class="contenido1"> X </td>
                        <td class="contenido1">SALA</td>
                        <td class="contenido1">&nbsp;</td>
                        <td colspan="2" class="contenido1">URGENTE</td>
                        <td colspan="2" class="contenido1">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="contenido1">2</td>
                        <td class="contenido1">@if(isset($diagnosticos[1])) {{$diagnosticos[1]['descripcion']}} @endif</td>
                        <td class="contenido1">@if(isset($diagnosticos[1])) {{$diagnosticos[1]['cie10']}} @endif</td>
                        <td class="contenido1">HOSPITALIZACION</td>
                        <td class="contenido1">&nbsp; </td>
                        <td class="contenido1">CAMA</td>
                        <td class="contenido1">&nbsp;</td>
                        <td colspan="2" class="contenido1">RUTINA</td>
                        <td colspan="2" class="contenido1"> X </td>
                    </tr>
                    <tr>
                        <td class="contenido1">3</td>
                        <td class="contenido1">@if(isset($diagnosticos[2])) {{$diagnosticos[2]['descripcion']}} @endif</td>
                        <td class="contenido1">@if(isset($diagnosticos[2])) {{$diagnosticos[2]['cie10']}} @endif</td>
                        <td class="contenido1">&nbsp;</td>
                        <td class="contenido1"></td>
                        <td class="contenido1">&nbsp; </td>
                        <td class="contenido1">&nbsp;</td>
                        <td class="contenido1">&nbsp;</td>
                        <td colspan="2" class="contenido1">&nbsp;</td>
                        <td colspan="2" class="contenido1"> &nbsp;</td>
                    </tr>
                </tbody>
            </table>
            <br>
            <table cellpadding="0" cellspacing="0" style="width: 100%;">
                <thead>
                    <tr>
                        <td colspan="14" class="subtitulo0"><b>C. RESULTADO DE EXÁMENES</b></td>
                    </tr>
                </thead>
            </table>


            <div class="contenido1">
                @php
                    $cant = count($hematologia);
                    if($cant < 22){ $cant=22; } $cant_coag=count($coagulacion); $x_coag=12; if($cant_coag < 12 ){ $x_coag=$x_coag - $cant_coag; } $cant2=count($quimica); if($cant2 < 34){ $cant2=30; } $cant_orina=count($orina); $x_orina=18; if($cant_orina < 18 ){ $x_orina=$x_orina - $cant_orina; } $cant3=count($inmunologia); $cant4=count($heces); $cant5=count($tumorales); $cant6=count($citologia); $cant7=count($cardiacos); $cant8=count($hormonas); $cant9=count($farmacos); $cant10=count($serologia); $cant11=count($biologia);
                @endphp 
                <div class="outer-box">
                <div class="bg-purple"><b>HEMATOLÓGIA</b></div>
                @for($i=0;$i< $cant ;$i=$i+2) <div class="inner-box">@if(isset($hematologia[$i])){{$hematologia[$i]->nombre}}@endif</div>
                    <div class="inner-box2">@if(isset($hematologia[$i])) @if($detalles->where('id_examen',$hematologia[$i]->id)->first() != null) X @endif @endif</div>
                    <div class="inner-box3">@if(isset($hematologia[$i+1])){{$hematologia[$i + 1]->nombre}} @else &nbsp;@endif</div>
                    <div class="inner-box4">@if(isset($hematologia[$i+1])) @if($detalles->where('id_examen',$hematologia[$i+1]->id)->first() != null) X @endif @endif</div>
                    <div class="div_clear"></div>
                @endfor
                <div class="inner-box" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="inner-box2" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="inner-box3" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="inner-box4" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="div_clear"></div>
                <div class="bg-purple"><b>INMUNOLOGÍA / INFECCIOSAS</b></div>
                @for($i=0;$i< $cant3 ;$i=$i+2) <div class="inner-box">@if(isset($inmunologia[$i])){{$inmunologia[$i]->nombre}}@endif</div>
                    <div class="inner-box2">@if(isset($inmunologia[$i])) @if($detalles->where('id_examen',$inmunologia[$i]->id)->first() != null) X @endif @endif</div>
                    <div class="inner-box3">@if(isset($inmunologia[$i+1])){{$inmunologia[$i + 1]->nombre}} @else &nbsp;@endif</div>
                    <div class="inner-box4">@if(isset($inmunologia[$i+1])) @if($detalles->where('id_examen',$inmunologia[$i+1]->id)->first() != null) X @endif @endif</div>
                    <div class="div_clear"></div>
                @endfor
                <div class="inner-box" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="inner-box2" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="inner-box3" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="inner-box4" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="div_clear"></div>
                <div class="bg-purple"><b>MARCADORES CARDIACOS/VASCULARES</b></div>
                @for($i=0;$i< $cant7 ;$i=$i+2) <div class="inner-box">@if(isset($cardiacos[$i])){{$cardiacos[$i]->nombre}}@endif</div>
                    <div class="inner-box2">@if(isset($cardiacos[$i])) @if($detalles->where('id_examen',$cardiacos[$i]->id)->first() != null) X @endif @endif</div>
                    <div class="inner-box3">@if(isset($cardiacos[$i+1])){{$cardiacos[$i + 1]->nombre}} @else &nbsp;@endif</div>
                    <div class="inner-box4">@if(isset($cardiacos[$i+1])) @if($detalles->where('id_examen',$cardiacos[$i+1]->id)->first() != null) X @endif @endif</div>
                    <div class="div_clear"></div>
                @endfor
                <div class="inner-box" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="inner-box2" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="inner-box3" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="inner-box4" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="div_clear"></div>
                <div class="bg-purple"><b>HORMONAS</b></div>
                @for($i=0;$i< $cant8 ;$i=$i+2) <div class="inner-box">@if(isset($hormonas[$i])){{$hormonas[$i]->nombre}}@endif</div>
                    <div class="inner-box2">@if(isset($hormonas[$i])) @if($detalles->where('id_examen',$hormonas[$i]->id)->first() != null) X @endif @endif</div>
                    <div class="inner-box3">@if(isset($hormonas[$i+1])){{$hormonas[$i + 1]->nombre}} @else &nbsp;@endif</div>
                    <div class="inner-box4">@if(isset($hormonas[$i+1])) @if($detalles->where('id_examen',$hormonas[$i+1]->id)->first() != null) X @endif @endif</div>
                    <div class="div_clear"></div>
                @endfor
                <div class="inner-box" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="inner-box2" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="inner-box3" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="inner-box4" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="div_clear"></div>
                </div>
                <div class="outer-box2">
                <div class="bg-purple"><b>COAGULACIÓN Y HEMOSTASIA</b></div>
                @foreach($coagulacion as $value)
                    <div class="inner-box21">{{$value->nombre}}</div>
                    <div class="inner-box22">@if($detalles->where('id_examen',$value->id)->first() != null) X @endif</div>
                    <div class="div_clear"></div>
                @endforeach
                @for($xc = 0;$xc < $x_coag;$xc++) 
                    <div class="inner-box21">&nbsp;
                    </div>
                    <div class="inner-box22">&nbsp;</div>
                    <div class="div_clear"></div>
                @endfor
                <div class="inner-box21" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="inner-box22" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="div_clear"></div>
                <div class="bg-purple"><b>ORINA</b></div>
                @foreach($orina as $value)
                    <div class="inner-box21">{{$value->nombre}}</div>
                    <div class="inner-box22">@if($detalles->where('id_examen',$value->id)->first() != null) X @endif</div>
                    <div class="div_clear"></div>
                @endforeach
                @for($xo = 0;$xo < $x_orina;$xo++) <div class="inner-box21">&nbsp;</div>
                    <div class="inner-box22">&nbsp;</div>
                    <div class="div_clear"></div>
                @endfor

                <div class="inner-box21" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="inner-box22" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="div_clear"></div>
                <div class="bg-purple"><b>INMUNOSUPRESORES</b></div>
                @foreach($inmunosupresor as $value)
                    <div class="inner-box21">{{$value->nombre}}</div>
                    <div class="inner-box22">@if($detalles->where('id_examen',$value->id)->first() != null) X @endif</div>
                    <div class="div_clear"></div>
                @endforeach
                <div class="inner-box21" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="inner-box22" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="div_clear"></div>
                <div class="bg-purple"><b>GASES Y ELECTROLITOS</b></div>
                @foreach($gases as $value)
                    <div class="inner-box21">{{$value->nombre}}</div>
                    <div class="inner-box22">@if($detalles->where('id_examen',$value->id)->first() != null) X @endif</div>
                    <div class="div_clear"></div>
                @endforeach
                <div class="inner-box21" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="inner-box22" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="div_clear"></div>
                <div class="bg-purple"><b>MEDICINA TRANSFUSIONAL</b></div>
                @foreach($trasfusional as $value)
                    <div class="inner-box21">{{$value->nombre}}</div>
                    <div class="inner-box22">@if($detalles->where('id_examen',$value->id)->first() != null) X @endif</div>
                    <div class="div_clear"></div>
                @endforeach
                <div class="inner-box21" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="inner-box22" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="div_clear"></div>
                </div>

                <div class="outer-box3">
                <div class="bg-purple"><b>QUÍMICA SANGUÍNEA</b></div>
                @for($j=0;$j< $cant2 ;$j=$j+2) <div class="inner-box">@if(isset($quimica[$j])){{$quimica[$j]->nombre}} @else &nbsp; @endif
                    </div>
                    <div class="inner-box2">@if(isset($quimica[$j])) @if($detalles->where('id_examen',$quimica[$j]->id)->first() != null) X @endif @endif</div>
                    <div class="inner-box3">@if(isset($quimica[$j+1])){{$quimica[$j + 1]->nombre}} @else &nbsp; @endif</div>
                    <div class="inner-box4">@if(isset($quimica[$j+1])) @if($detalles->where('id_examen',$quimica[$j+1]->id)->first() != null) X @endif @endif</div>
                    <div class="div_clear"></div>
                @endfor
                <div class="div_clear"></div>
                <div class="bg-purple"><b>HECES</b></div>
                @for($j=0;$j< $cant4 ;$j=$j+2) <div class="inner-box">@if(isset($heces[$j])){{$heces[$j]->nombre}} @else &nbsp; @endif</div>
                    <div class="inner-box2">@if(isset($heces[$j])) @if($detalles->where('id_examen',$heces[$j]->id)->first() != null) X @endif @endif</div>
                    <div class="inner-box3">@if(isset($heces[$j+1])){{$heces[$j + 1]->nombre}} @else &nbsp; @endif</div>
                    <div class="inner-box4">@if(isset($heces[$j+1])) @if($detalles->where('id_examen',$heces[$j+1]->id)->first() != null) X @endif @endif</div>
                    <div class="div_clear"></div>
                @endfor
                <div class="div_clear"></div>
                <div class="bg-purple"><b>MARCADORES TUMORALES</b></div>
                @for($j=0;$j< $cant5 ;$j=$j+2) <div class="inner-box">@if(isset($tumorales[$j])){{$tumorales[$j]->nombre}} @else &nbsp; @endif</div>
                    <div class="inner-box2">@if(isset($tumorales[$j])) @if($detalles->where('id_examen',$tumorales[$j]->id)->first() != null) X @endif @endif</div>
                    <div class="inner-box3">@if(isset($tumorales[$j+1])){{$tumorales[$j + 1]->nombre}} @else &nbsp; @endif</div>
                    <div class="inner-box4">@if(isset($tumorales[$j+1])) @if($detalles->where('id_examen',$tumorales[$j+1]->id)->first() != null) X @endif @endif</div>
                    <div class="div_clear"></div>
                @endfor
                <div class="div_clear"></div>
                <div class="bg-purple"><b>CITOQUÍMICO Y BACTERIOLÓGICO DE LÍQUIDOS</b></div>
                @for($j=0;$j< $cant6 ;$j=$j+2) <div class="inner-box">@if(isset($citologia[$j])){{$citologia[$j]->nombre}} @else &nbsp; @endif</div>
                    <div class="inner-box2">@if(isset($citologia[$j])) @if($detalles->where('id_examen',$citologia[$j]->id)->first() != null) X @endif @endif</div>
                    <div class="inner-box3">@if(isset($citologia[$j+1])){{$citologia[$j + 1]->nombre}} @else &nbsp; @endif</div>
                    <div class="inner-box4">@if(isset($citologia[$j+1])) @if($detalles->where('id_examen',$citologia[$j+1]->id)->first() != null) X @endif @endif</div>
                    <div class="div_clear"></div>
                @endfor
                <div class="div_clear"></div>
                <div class="div_clear"></div>
                <div class="bg-purple"><b>NIVELES DE FÁRMACOS TERAPÉUTICAS</b></div>
                @for($j=0;$j< $cant9 ;$j=$j+2) <div class="inner-box">@if(isset($farmacos[$j])){{$farmacos[$j]->nombre}} @else &nbsp; @endif</div>
                    <div class="inner-box2">@if(isset($farmacos[$j])) @if($detalles->where('id_examen',$farmacos[$j]->id)->first() != null) X @endif @endif</div>
                    <div class="inner-box3">@if(isset($farmacos[$j+1])){{$farmacos[$j + 1]->nombre}} @else &nbsp; @endif</div>
                    <div class="inner-box4">@if(isset($farmacos[$j+1])) @if($detalles->where('id_examen',$farmacos[$j+1]->id)->first() != null) X @endif @endif</div>
                    <div class="div_clear"></div>
                @endfor
                <div class="inner-box" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="inner-box2" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="inner-box3" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="inner-box4" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="div_clear"></div>
                <div class="div_clear"></div>
                <div class="bg-purple"><b>SEROLOGÍA</b></div>
                @for($j=0;$j< $cant10 ;$j=$j+2) <div class="inner-box">@if(isset($serologia[$j])){{$serologia[$j]->nombre}} @else &nbsp; @endif</div>
                    <div class="inner-box2">@if(isset($serologia[$j])) @if($detalles->where('id_examen',$serologia[$j]->id)->first() != null) X @endif @endif</div>
                    <div class="inner-box3">@if(isset($serologia[$j+1])){{$serologia[$j + 1]->nombre}} @else &nbsp; @endif</div>
                    <div class="inner-box4">@if(isset($serologia[$j+1])) @if($detalles->where('id_examen',$serologia[$j+1]->id)->first() != null) X @endif @endif</div>
                    <div class="div_clear"></div>
                @endfor
                <div class="inner-box" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="inner-box2" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="inner-box3" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="inner-box4" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="div_clear"></div>
                <div class="div_clear"></div>
                <div class="bg-purple"><b>MICROBIOLOGÍA</b></div>
                @for($j=0;$j< $cant11;$j=$j+2) <div class="inner-box">@if(isset($biologia[$j])){{$biologia[$j]->nombre}} @else &nbsp; @endif</div>
                    <div class="inner-box2">@if(isset($biologia[$j])) @if($detalles->where('id_examen',$biologia[$j]->id)->first() != null) X @endif @endif</div>
                    <div class="inner-box3">@if(isset($biologia[$j+1])){{$biologia[$j + 1]->nombre}} @else &nbsp; @endif</div>
                    <div class="inner-box4">@if(isset($biologia[$j+1])) @if($detalles->where('id_examen',$biologia[$j+1]->id)->first() != null) X @endif @endif</div>
                    <div class="div_clear"></div>
                @endfor
                <div class="inner-box" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="inner-box2" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="inner-box3" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="inner-box4" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="div_clear"></div>
                </div>
                </div>
                <div class="div_clear"></div>

                <div class="subtitulo0"><b>BIOLOGÍA MOLECULAR Y GENÉTICA</b></div>
                <div class="molecular" style="border-bottom: 1px solid;height: 35px !important">&nbsp;</div>

                <table class="table" cellpadding="0" cellspacing="0" style="width: 100%;margin-top: -90px;">
                    <thead>
                        <tr>
                            <td colspan="5" class="subtitulo0"><b>D. DATOS DEL PROFESIONAL RESPONSABLE</b></td>
                        </tr>
                    </thead>

                    <tr>
                        <td style="width:20%;" class="subtitulo1"><b>FECHA GENERACIÓN PEDIDO </b></td>
                        <td style="width:20%;" class="subtitulo1"><b>HORA GENERACIÓN PEDIDO </b></td>
                        <td style="width:20%;" class="subtitulo1"><b>PRIMER NOMBRE</b></td>
                        <td style="width:20%;" class="subtitulo1"><b>PRIMER APELLIDO</b></td>
                        <td style="width:20%;" class="subtitulo1"><b>SEGUNDO APELLIDO</b></td>
                    </tr>
                    <tr>
                        <td class="contenido1" style="width:5%; ">@if($orden->fecha_convenios == null){{ substr($orden->fecha_orden,0,10) }} @else{{ substr($orden->fecha_convenios,0,10) }} @endif</td>
                        <td class="contenido1" style="width:5%; ">@if($orden->fecha_convenios == null){{ substr($orden->fecha_orden,11,8) }}@else {{ substr($orden->fecha_convenios,11,8) }}@endif</td>
                        <!--td class="contenido1" style="width:5%; ">{{$fecha}}</td>
                        <td class="contenido1" style="width:5%; ">{{$hora}}</td-->
                        <td class="contenido1" style="width:14%; ">{{$orden->doctor->nombre1}}</td>
                        <td class="contenido1" style="width:14%; ">{{$orden->doctor->apellido1}}</td>
                        <td class="contenido1" style="width:14%; ">{{$orden->doctor->apellido2}}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="subtitulo1"><b>NÚMERO DE DOCUMENTO DE IDENTIFICACIÓN</b></td>
                        <td colspan="2" class="subtitulo1"><b>FIRMA</b></td>
                        <td class="subtitulo1"><b>SELLO</b></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="contenido1" style="width:12%; ">{{$orden->id_doctor_ieced}}</td>
                        @php
                        $ruta='';
                        $firma = Sis_medico\Firma_Usuario::where('id_usuario',$orden->id_doctor_ieced)->first();


                        if(!is_null($firma)){
                        $ruta = $firma->firma;
                        }

                        if($ruta==''){
                        $firma = Sis_medico\Firma_Usuario::where('id_usuario','1307189140')->first();
                        if(!is_null($firma)){
                        $ruta = $firma->nombre;
                        }
                        }
                        @endphp
                        <td colspan="2" class="contenido1" style="width:25%; ">
                            @if(!is_null($firma) && !is_null($firma->firma))
                            <div class="firma-container">
                                <img
                                    src="{{ base_path() . '/storage/app/avatars/' . $firma->firma }}"
                                    alt="Firma"
                                    class="firma-imagen"
                                >
                            </div>
                            @else
                            <br><br><br>
                            <b class="contenido1">@if(!is_null($firma)) @if($firma->sexo == '2') Dra. @else Dr. @endif @endif {{$orden->id_doctor_ieced->apellido1}} @if($orden->id_doctor_ieced->apellido2!='N/A'){{$orden->id_doctor_ieced->apellido2}}@endif {{$orden->id_doctor_ieced->nombre1}} {{$orden->id_doctor_ieced->nombre2}}</b><br>
                            @endif

                        </td>
                        <td class="contenido1" style="width:25%;">
                        @if(!is_null($firma)) @if($firma->sexo == '2') Dra. @else Dr. @endif @endif {{$orden->doctor->apellido1}} @if($orden->doctor->apellido2!='N/A'){{$orden->doctor->apellido2}}@endif {{$orden->doctor->nombre1}} {{$orden->doctor->nombre2}} <br>
                            Cedula No. {{$orden->id_doctor_ieced}}

                        </td>

                    </tr>
                    <tr>
                        <td style="width:14%; " class="subtitulo1"><b>FECHA DE TOMA DE MUESTRA </b></td>
                        <td style="width:15%;" class="subtitulo1"><b>HORA DE TOMA DE MUESTRA </b></td>
                        <td colspan="2" style="width:20%" class="subtitulo1"><b>NOMBRE Y APELLIDO DE LA PERSONA QUE TOMA LA MUESTRA</b></td>
                        <td style="width:20%" class="subtitulo1"><b>FIRMA</b></td>
                    </tr>
                    <tr>
                        <td class="contenido1" style="width:5%;">@if($orden->fecha_convenios == null){{ substr($orden->fecha_orden,0,10) }} @else{{ substr($orden->fecha_convenios,0,10) }} @endif</td>
                        <td class="contenido1" style="height:100px;">@if($orden->fecha_convenios == null){{ substr($orden->fecha_orden,11,8) }}@else {{ substr($orden->fecha_convenios,11,8) }}@endif</td>
                        @php
                        $firma = Sis_medico\Firma_Usuario::where('nombre', 'labs_responsable.jpg')->first();
                        @endphp
                        @if(!is_null($firma))
                        @php
                        $id_laboratorista = $firma->id_usuario;
                        $laboratorista = Sis_medico\User::where('id',$id_laboratorista)->first();
                        @endphp
                        <td colspan="2" class="contenido1">{{$laboratorista->nombre1}}  {{$laboratorista->nombre2}} {{$laboratorista->apellido1}} {{$laboratorista->apellido2}} </td>
                        @else
                        <td colspan="2" class="contenido1"></td>
                        @endif

                        <td class="contenido1" style="width: 60%; height:100px;">
                            <!--br-->
                            @if( $orden->fecha_orden < '2021-06-01  0:00:00' ) 
                                <img style="position: absolute;margin-left: 150px" width=auto height=80 src="{{base_path().'/storage/app/logo/labs_karla.png'}}" align=center hspace=12>
                            @else
                                <img style='position: absolute;margin-left: 150px' width=auto height=80 src="{{base_path().'/storage/app/logo/labs_responsable.jpg'}}" align=center hspace=12>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td colspan="3">SNS-MSP/HCU-form.010A/2021</td>
                        <td colspan="2" style="text-align: right;">LABORATORIO CLÍNICO - SOLICITUD (1)</td>
                    </tr>
                    </tbody>
                </table>
                <div class="page-break-after"></div>


                <table cellpadding="0" cellspacing="0" style="width: 100%">
                    <thead>
                        <tr>
                            <td colspan="8" class="subtitulo0"><b>A. VIH / ITS</b></td>
                        </tr>
                        <tr>
                            @for($i = 0; $i < 4; $i++) <td class="subtitulo2">@if(isset($vih[$i])) {{$vih[$i]->nombre}} @else &nbsp; @endif</td>
                                <td style="width: 4%" class="contenido1">@if(isset($vih[$i])) @if($detalles->where('id_examen',$vih[$i]->id)->first() != null) X @endif @endif</td>
                            @endfor
                        </tr>
                        <tr>
                            @for($i = 4; $i < 8; $i++) <td class="subtitulo2">@if(isset($vih[$i])) {{$vih[$i]->nombre}} @else &nbsp; @endif</td>
                                <td style="width: 4%" class="contenido1">@if(isset($vih[$i])) @if($detalles->where('id_examen',$vih[$i]->id)->first() != null) X @endif @endif</td>
                            @endfor
                        </tr>
                        </tbody>
                </table>
                <br>
                <table cellpadding="0" cellspacing="0" style="width: 100%">
                    <thead>
                        <tr>
                            <td colspan="31" class="subtitulo0"><b>B. TUBERCULOSIS</b></td>
                        </tr>
                        <tr>
                            <td colspan="31" class="subtitulo1"><b>Tipo de afectado</b></td>
                        </tr>
                        <td>&nbsp;</td>
                    </thead>
                </table>
                <table style="width: 100%">
                    <tbody>
                        <tr>
                            <td style="width: 5%; height: 5px;" class="contenido2">Nuevo </td>
                            <td>&nbsp;</td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td>&nbsp;</td>
                            <td style="width: 5%; height: 5px;" class="contenido2">Recaída</td>
                            <td>&nbsp;</td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td style="width: 5%; height: 5px;" class="contenido2">Fracaso</td>
                            <td>&nbsp;</td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td style="width: 25%; height: 5px;" class="contenido2">Pérdida en el seguimiento
                            </td>
                            <td>&nbsp;</td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td>&nbsp;</td>
                            <td style="width: 5%; height: 5px; " class="contenido2">PVV</td>
                            <td>&nbsp;</td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td>&nbsp;</td>
                            <td style="width: 5%; height: 5px; " class="contenido2">PPL</td>
                            <td>&nbsp;</td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td>&nbsp;</td>
                            <td style="width: 9%; height: 5px  font-size: 10px;" class="contenido2">Niño 5 años</td>
                            <td>&nbsp;</td>
                            <td class="contenido1" style="width: 10%"></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="31">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="width: 14%; height: 5px;" colspan="6" class="contenido2">Sospecha de
                                Meningitis TB </td>
                            <td></td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td></td>
                            <td style="height: 5px;" colspan="7" class="contenido2">Alta sospecha clínica
                                y/o radiológica BK (-) </td>
                            <td></td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td></td>
                            <td></td>
                            <td style=" height: 5px;" colspan="3" class="contenido2">Comorbilidad</td>
                            <td></td>
                            <td class="contenido1" style="width: 1%"></td>
                            <td></td>
                            <td class="contenido1" colspan="2" style="width: 10%"></td>
                            <td></td>
                        </tr>

                        <tr>
                            <td colspan="31">&nbsp;</td>
                        </tr>



                        <tr>
                            <td style="width: 12%; height: 5px; font-size: 10px " colspan="3" class="contenido2">Contacto TBR
                            </td>
                            <td></td>
                            <td></td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td></td>
                            <td colspan="31"></td>
                        </tr>

                        <tr>
                            <td colspan="31">&nbsp;</td>
                        </tr>

                        <tr>
                            <td style="width: 20%;" class="contenido2">Sospecha de TB EP</td>
                            <td></td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td></td>
                            <td style="width: 20%; height: 5px;" class="contenido2" colspan="6">Talento humano
                                en salud </td>
                            <td></td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td></td>
                            <td style="width: 20%; height: 5px;" class="contenido2" colspan="4">Irregularida en
                                la toma del Ttos</td>
                            <td></td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td></td>
                            <td style="width: 10%; height: 5px;" class="contenido2" colspan="6">Reversión</td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="31">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="31">&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="contenido2">Embarazo</td>
                            <td></td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td></td>
                            <td style="width: 20%; height: 5px;" class="contenido2" colspan="6">BK (+) al
                                2do. mes </td>
                            <td></td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td></td>
                            <td  class="contenido2" colspan="3">Condiciones especiales </td>
                            <td></td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td></td>
                            <td class="contenido1" colspan="2" style="width: 10%"></td>
                            <td></td>
                            <td class="contenido2">otros</td>
                            <td></td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td></td>
                            <td class="contenido1" style="width: 10%"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="62">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="31"></td>
                        </tr>
                    </tbody>
                </table>
                <table cellpadding="0" cellspacing="0" style="width: 100%">
                    <thead>
                        <tr>
                            <td class="subtitulo0" colspan="31"><b>Antecedentes de tuberculosis</b></td>
                            <td style="width: 45%;" class="subtitulo0" colspan="31"><b>Tipo de muestra</b>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="62">&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="contenido2" colspan="10"><b>TIPO DE RESISTENCIA</b></td>
                            <td colspan="21"></td>
                            <td colspan="31">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="62">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="width: 18%; height: 5px; " class="contenido2" colspan="2">TB sensible </td>
                            <td>&nbsp;</td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td>&nbsp;</td>
                            <td style="width: 18%; height: 10px;" class="contenido2">TB resistente</td>
                            <td>&nbsp;</td>
                            <td class="contenido1" style="width: 4%"></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td class="contenido1" style="width: 15%"></td>
                            <td colspan="17"></td>
                            <td colspan="5" style="width: 5%; height: 10px;" class="contenido2">Esputo</td>
                            <td colspan="3">&nbsp;</td>
                            <td class="contenido1" style="width: 4%"></td>
                            <td style="width: 2%; height: 10px;" class="contenido2" colspan="2">Otro</td>
                            <td colspan="1">&nbsp;</td>
                            <td class="contenido1" style="width: 3%"></td>
                            <td></td>
                            <td class="contenido1" style="width: 15%"></td>
                            <td colspan="12"></td>
                        </tr>
                        <tr>
                            <td colspan="40">&nbsp;</td>
                            <td colspan="12">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="62" style="font-size: 14px;" class="bg-blue contenido1"><b>Solicitud
                                    para diagnóstico</b></td>
                        </tr>
                        <tr>
                            <td colspan="62">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="width: 9%; height: 5px; " class="contenido2" colspan="2"><b>Ada</b></td>
                            <td>&nbsp;</td>
                            <td class="contenido1" style="width: 3%"></td>
                            <td colspan="2">&nbsp;</td>
                            <td style="width: 9%; height: 5px; " class="contenido2" colspan="5"><b>Baciloscopia</b></td>
                            <td>&nbsp;</td>
                            <td style="width: 9%; height: 5px; " class="contenido2" colspan="5">Diagnóstico</td>
                            <td colspan="2">&nbsp;</td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td>&nbsp;</td>
                            <td style="width: 9%; height: 5px; " class="contenido2" colspan="2">No.</td>
                            <td>&nbsp;</td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td style="width: 30%; height: 5px; " class="contenido2" colspan="10"><b>Cultivo medio
                                    sólido(OK)<b></td>
                            <td>&nbsp;</td>
                            <td style="width: 9%; height: 5px; " class="contenido2">Diagnóstico</td>
                            <td colspan="2">&nbsp;</td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td style="width: 9%; height: 5px; " class="contenido2">No.</td>
                            <td class="contenido1" style="width: 9%"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="40">&nbsp;</td>
                            <td colspan="12">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="6">&nbsp;</td>
                            <td style="width: 9%; height: 5px; " class="contenido2">Control</td>
                            <td>&nbsp;</td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td></td>
                            <td></td>
                            <td style="width: 12%; height: 5px; " class="contenido2" colspan="2">No. Mes</td>
                            <td></td>
                            <td>&nbsp;</td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td>&nbsp;</td>
                            <td colspan="15"></td>
                            <td style="width: 9%; height: 5px; " class="contenido2">Control</td>
                            <td>&nbsp;</td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td></td>
                            <td></td>
                            <td style="width: 9%; height: 5px; " class="contenido2" colspan="2">No. Mes</td>
                            <td></td>
                            <td>&nbsp;</td>
                            <td class="contenido1" style="width: 4%"></td>
                            <td>&nbsp;</td>
                            <td colspan="15"></td>
                        </tr>
                        <tr>
                            <td colspan="62">&nbsp;</td>
                        </tr>

                        <tr>
                            <td style="width: 24%; height: 5px; " class="contenido2" colspan="2"><b>PCR tiempo real</b></td>
                            <td>&nbsp;</td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td colspan="2">&nbsp;</td>
                            <td style="width: 9%; height: 5px; " class="contenido2" colspan="5"><b>Nitrato reductasa</b></td>
                            <td colspan="1">&nbsp;</td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td style="width: 30%; height: 5px; " class="contenido2" colspan="10"><b>Cultivo medioliquido<b></td>
                            <td colspan="4">&nbsp;</td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td>&nbsp;</td>
                            <td style="width: 11%; height: 5px; " class="contenido2"><b>Genotipificación</b></td>
                            <td colspan="2">&nbsp;</td>
                            <td class="contenido1" style="width: 3%"></td>
                            <td colspan="3">&nbsp;</td>
                            <td style="width: 9%; height: 5px; " class="contenido2"><b>Tipificación</b></td>
                            <td colspan="2">&nbsp;</td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td></td>
                        </tr>

                        <tr>
                            <td style="width: 9%; height: 5px; " class="contenido2" colspan="2">(Xpert/MTB/RIF)</td>
                            <td colspan="4">&nbsp;</td>
                            <td style="width: 9%; height: 5px; " class="contenido2" colspan="5">(GRIESS)*</td>
                            <td colspan="6">&nbsp;</td>
                            <td style="width: 9%; height: 5px; " class="contenido2" colspan="5">Diagnóstico</td>
                            <td colspan="20">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="62">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="width: 24%; height: 5px; " class="contenido2" colspan="2"><b>PSD 1ra. Línea</b>
                            </td>
                            <td>&nbsp;</td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td colspan="2">&nbsp;</td>
                            <td style="width: 9%; height: 5px; " class="contenido2" colspan="5"><b>PSD 1ra. Línea</b>
                            </td>
                            <td colspan="1">&nbsp;</td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td style="width: 30%; height: 5px; " class="contenido2" colspan="10"><b>PSD 2da. Línea<b></td>
                            <td colspan="4">&nbsp;</td>
                            <td class="contenido1" style="width: 2%"></td>
                            <td>&nbsp;</td>
                            <td style="width: 11%; height: 5px; " class="contenido2"><b>PSD 2da. Línea</b></td>
                            <td colspan="2">&nbsp;</td>
                            <td class="contenido1" style="width: 3%"></td>
                            <td colspan="3">&nbsp;</td>
                            <td></td>
                        </tr>

                        <tr>
                            <td style="width: 9%; height: 5px; " class="contenido2" colspan="7">(Proporciones-Medio sólido)</td>
                            <td style="width: 9%; height: 5px; " class="contenido2" colspan="5">(MGIT-Medio líquido)</td>
                            <td colspan="5">&nbsp;</td>
                            <td style="width: 9%; height: 5px; " class="contenido2" colspan="6">(Proporciones-Medio sólido)</td>
                            <td colspan="8">&nbsp;</td>
                            <td style="width: 9%; height: 5px; " class="contenido2" colspan="6">(Proporciones-Medio sólido)</td>
                        </tr>
                        <tr>
                            <td colspan="62">&nbsp;</td>
                        </tr>
                    </thead>
                </table>

                <table class="table-footer2" cellpadding="0" cellspacing="0" style="width: 100%;">
                    <thead>
                        <tr>
                            <td colspan="5" class="subtitulo0"><b>D. DATOS DEL PROFESIONAL RESPONSABLE</b></td>
                        </tr>
                    </thead>

                    <tr>
                        <td style="width:14%; height: 10px;" class="subtitulo1"><b>FECHA GENERACIÓN PEDIDO </b></td>
                        <td style="width:15%; height: 10px;" class="subtitulo1"><b>HORA GENERACIÓN PEDIDO </b></td>
                        <td style="width:20%" class="subtitulo1"><b>PRIMER NOMBRE</b></td>
                        <td style="width:20%" class="subtitulo1"><b>PRIMER APELLIDO</b></td>
                        <td style="width:15%" class="subtitulo1"><b>SEGUNDO APELLIDO</b></td>
                    </tr>
                    <tr>
                        <td class="contenido1" style="width:5%; ">@if($orden->fecha_convenios == null){{ substr($orden->fecha_orden,0,10) }} @else{{ substr($orden->fecha_convenios,0,10) }} @endif</td>
                        <td class="contenido1" style="width:5%; ">@if($orden->fecha_convenios == null){{ substr($orden->fecha_orden,11,8) }}@else {{ substr($orden->fecha_convenios,11,8) }}@endif</td>
                        <!--td class="contenido1" style="width:5%; ">{{$fecha}}</td>
                        <td class="contenido1" style="width:5%; ">{{$hora}}</td-->
                        <td class="contenido1" style="width:14%; ">{{$orden->doctor->nombre1}}</td>
                        <td class="contenido1" style="width:14%; ">{{$orden->doctor->apellido1}}</td>
                        <td class="contenido1" style="width:14%; ">{{$orden->doctor->apellido2}}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="subtitulo1"><b>NÚMERO DE DOCUMENTO DE IDENTIFICACIÓN</b></td>
                        <td colspan="2" class="subtitulo1"><b>FIRMA</b></td>
                        <td class="subtitulo1"><b>SELLO</b></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="contenido1" style="width:12%; ">{{$orden->id_doctor_ieced}}</td>
                        @php
                        $ruta='';
                        $firma = Sis_medico\Firma_Usuario::where('id_usuario',$orden->id_doctor_ieced)->first();


                        if(!is_null($firma)){
                        $ruta = $firma->firma;
                        }

                        if($ruta==''){
                        $firma = Sis_medico\Firma_Usuario::where('id_usuario','1307189140')->first();
                        if(!is_null($firma)){
                        $ruta = $firma->nombre;
                        }
                        }
                        @endphp
                        <td colspan="2" class="contenido1" style="width:25%; ">
                            @if(!is_null($firma) && !is_null($firma->firma))
                            <div class="firma-container">
                                <img
                                    src="{{ base_path() . '/storage/app/avatars/' . $firma->firma }}"
                                    alt="Firma"
                                    class="firma-imagen"
                                >
                            </div>
                            @else
                            <br><br><br>
                            <b class="contenido1">@if(!is_null($firma)) @if($firma->sexo == '2') Dra. @else Dr. @endif @endif {{$orden->id_doctor_ieced->apellido1}} @if($orden->id_doctor_ieced->apellido2!='N/A'){{$orden->id_doctor_ieced->apellido2}}@endif {{$orden->id_doctor_ieced->nombre1}} {{$orden->id_doctor_ieced->nombre2}}</b><br>
                            @endif

                        </td>
                        <td class="contenido1" style="width:25%;">
                        @if(!is_null($firma)) @if($firma->sexo == '2') Dra. @else Dr. @endif @endif {{$orden->doctor->apellido1}} @if($orden->doctor->apellido2!='N/A'){{$orden->doctor->apellido2}}@endif {{$orden->doctor->nombre1}} {{$orden->doctor->nombre2}} <br>
                            Cedula No. {{$orden->id_doctor_ieced}}
                        </td>

                    </tr>
                    <tr>
                        <td style="width:14%; " class="subtitulo1"><b>FECHA DE TOMA DE MUESTRA </b></td>
                        <td style="width:15%;" class="subtitulo1"><b>HORA DE TOMA DE MUESTRA </b></td>
                        <td colspan="2" style="width:20%" class="subtitulo1"><b>NOMBRE Y APELLIDO DE LA PERSONA QUE TOMA LA MUESTRA</b></td>
                        <td style="width:20%" class="subtitulo1"><b>FIRMA</b></td>
                    </tr>
                    <tr>
                        <td class="contenido1" style="width:5%; ">@if($orden->fecha_convenios == null){{ substr($orden->fecha_orden,0,10) }} @else{{ substr($orden->fecha_convenios,0,10) }} @endif</td>
                        <td class="contenido1">@if($orden->fecha_convenios == null){{ substr($orden->fecha_orden,11,8) }}@else {{ substr($orden->fecha_convenios,11,8) }}@endif</td>
                        @php
                        $firma = Sis_medico\Firma_Usuario::where('nombre', 'labs_responsable.jpg')->first();
                        @endphp
                        @if(!is_null($firma))
                        @php
                        $id_laboratorista = $firma->id_usuario;
                        $laboratorista = Sis_medico\User::where('id',$id_laboratorista)->first();
                        @endphp
                        <td colspan="2" class="contenido1" >{{$laboratorista->nombre1}} {{$laboratorista->nombre2}} {{$laboratorista->apellido1}} {{$laboratorista->apellido2}} </td>
                        @else
                        <td colspan="2" class="contenido1"></td>
                        @endif

                        <td class="contenido1" style="width: 60%; height:100px;">
                            <br>
                            @if( $orden->fecha_orden < '2021-06-01  0:00:00' ) 
                                <img style="position: absolute;margin-left: 150px" width=auto height=80 src="{{base_path().'/storage/app/logo/labs_karla.png'}}" align=center hspace=12>
                            @else
                                <img style="position: absolute;margin-left: 150px" width=auto height=80 src="{{base_path().'/storage/app/logo/labs_responsable.jpg'}}" align=center hspace=12>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td colspan="3">SNS-MSP/HCU-form.010A/2021</td>
                        <td colspan="2" style="text-align: right;">LABORATORIO CLÍNICO - SOLICITUD (1)</td>
                    </tr>
                    </tbody>
                </table>
                                                    
                <!-- HEADER -->
                <div class="row page-break-before" style="border-bottom: 1px solid #009a98;padding: 0px;width: 100%;">
                <div class="row box-body card" style="text-align: center;">
                    @if(!is_null($empresa))
                    <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" style="width: 140px;height: 45px">
                    @endif
                </div>
                <div class="row" style="border-bottom: 1px solid #009a98;padding: 0px;width: 100%;">
                    <table style="font-size: 16px;color: #009a98; width: 100%;">
                    <thead>
                        <tr role="row" class="align: center;">
                            <td colspan="6" class="subtitulo0"><b>A. DATOS DEL ESTABLECIMIENTO Y USUARIO</b></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr role="row" >
                        <td class="subtitulo1" width="25%"><b>INSTITUCION DEL SISTEMA</b></td>
                        <td class="subtitulo1" width="10%"><b>UNICODIGO</b></td>
                        <td class="subtitulo1" width="25%"><b>ESTABLECIMIENTO DE SALUD</b></td>
                        <td class="subtitulo1" width="15%"><b>NÚMERO DE HISTORIA CLÍNICA ÚNICA</b></td>
                        <td class="subtitulo1" width="15%"><b>NÚMERO DE ARCHIVO</b></td>
                        <td class="subtitulo1" width="10%"><b>No.HOJA</b></td>
                        </tr>
                        <tr role="row" >
                        <td class="contenido1" width="25%">{{$empresa->nombrecomercial}}<</td>
                        <td class="contenido1" width="10%">{{$empresa->unicodigo}}</td>
                        <td class="contenido1" width="25%">{{$empresa->nombrecomercial}}</td>
                        <td class="contenido1" width="15%">{{$orden->id_paciente}}</td>
                        <td class="contenido1" width="15%"></td>
                        <td class="contenido1" width="10%">1</td>
                        </tr>
                    </tbody>
                    </table>
                </div>
                <div class="row" style="border-bottom: 1px solid #009a98;padding: 0px;width: 100%;">
                    <table style="font-size: 16px;color: #009a98; width: 100%;">
                    <thead>
                        <tr>
                            <td rowspan="2" class="subtitulo1"><b>PRIMER APELLIDO</b></td>
                            <td rowspan="2" class="subtitulo1"><b>SEGUNDO APELLIDO</b></td>
                            <td rowspan="2" class="subtitulo1"><b>PRIMER NOMBRE</b></td>
                            <td rowspan="2" class="subtitulo1"><b>SEGUNDO NOMBRE</b></td>
                            <td rowspan="2" class="subtitulo1"><b>SEXO</b></td>
                            <td rowspan="2" class="subtitulo1"><b>EDAD</b></td>
                            <td colspan="4" class="subtitulo1"><b>CONDICIÓN EDAD (MARCAR)</b></td>
                        </tr>
                        <tr>
                            <td class="subtitulo1">D</td>
                            <td class="subtitulo1">H</td>
                            <td class="subtitulo1">M</td>
                            <td class="subtitulo1">A</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <td class="contenido1" style="height: 18px;">{{$orden->paciente->apellido1}}</td>
                        <td class="contenido1">{{$orden->paciente->apellido2}}</td>
                        <td class="contenido1">{{$orden->paciente->nombre1}}</td>
                        <td class="contenido1">{{$orden->paciente->nombre2}}</td>
                        <td class="contenido1">@if($orden->paciente->sexo == 1) Masculino @endif @if($orden->paciente->sexo == 2) Femenino @endif</td>
                        <td class="contenido1">{{$edad}}</td>
                        <td class="contenido1">{{$diaNacimiento}}</td>
                        <td class="contenido1"></td>
                        <td class="contenido1">{{$mesNacimiento}}</td>
                        <td class="contenido1">{{ date('Y', strtotime($orden->paciente->fecha_nacimiento))}}</td>
                        </tr>
                    </tbody>
                    </table>
                </div>
                <div class="row" style="border-bottom: 1px solid #009a98;padding: 0px;width: 100%;">
                    <table style="font-size: 16px;color: #009a98; width: 100%;">
                    <thead>
                        <tr role="row" class="align: center;">
                            <td colspan="11" class="subtitulo0"><b>B. SERVICIO Y PRIORIDAD DE ATENCIÓN</b></td>
                        </tr>
                    </thead>
                    <tbody>
                            <tr>
                                <td colspan="2" class="subtitulo1">DIAGNOSTICO</td>
                                <td class="subtitulo1" >CIE</td>
                                <td rowspan="3" class="subtitulo1" >SERVICIO</td>
                                <td class="subtitulo1" >EMERGENCIA</td>
                                <td class="contenido1" >&nbsp;</td>
                                <td class="subtitulo1" >ESPECIALIDAD</td>
                                <td class="contenido1" >&nbsp;</td>
                                <td colspan="2" class="subtitulo1">PRIORIDAD</td>
                                <td colspan="2" class="contenido1" >&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="contenido1">1</td>
                                <td class="contenido1">@if(isset($diagnosticos[0])) {{$diagnosticos[0]['descripcion']}} @endif</td>
                                <td class="contenido1">@if(isset($diagnosticos[0])) {{$diagnosticos[0]['cie10']}} @endif</td>
                                <td class="contenido1" >CONSULTA EXTERNA</td>
                                <td class="contenido1"> X </td>
                                <td class="contenido1" >SALA</td>
                                <td class="contenido1">&nbsp;</td>
                                <td colspan="2" class="contenido1">URGENTE</td>
                                <td colspan="2" class="contenido1">&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="contenido1">2</td>
                                <td class="contenido1" >@if(isset($diagnosticos[1])) {{$diagnosticos[1]['descripcion']}} @endif</td>
                                <td class="contenido1" >@if(isset($diagnosticos[1])) {{$diagnosticos[1]['cie10']}} @endif</td>
                                <td class="contenido1" >HOSPITALIZACION</td>
                                <td class="contenido1">&nbsp; </td>
                                <td class="contenido1" >CAMA</td>
                                <td class="contenido1">&nbsp;</td>
                                <td colspan="2" class="contenido1">RUTINA</td>
                                <td colspan="2" class="contenido1"> X </td>
                            </tr>
                            <tr>
                                <td class="contenido1">3</td>
                                <td class="contenido1">@if(isset($diagnosticos[2])) {{$diagnosticos[2]['descripcion']}} @endif</td>
                                <td class="contenido1">@if(isset($diagnosticos[2])) {{$diagnosticos[2]['cie10']}} @endif</td>
                                <td class="contenido1">&nbsp;</td>
                                <td class="contenido1" ></td>
                                <td class="contenido1">&nbsp; </td>
                                <td class="contenido1">&nbsp;</td>
                                <td class="contenido1">&nbsp;</td>
                                <td colspan="2" class="contenido1">&nbsp;</td>
                                <td colspan="2" class="contenido1"> &nbsp;</td>
                            </tr>
                            <tr>
                                <td class="contenido1"></td>
                                <td class="contenido1">&nbsp;</td>
                                <td class="contenido1">&nbsp;</td>
                                <td class="contenido1">&nbsp;</td>
                                <td class="contenido1"></td>
                                <td class="contenido1">&nbsp; </td>
                                <td class="contenido1">&nbsp;</td>
                                <td class="contenido1">&nbsp;</td>
                                <td colspan="2" class="contenido1">&nbsp;</td>
                                <td colspan="2" class="contenido1"> &nbsp; </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                </div>
                <div class="row" style="border-bottom: 1px solid #009a98;padding: 0px;width: 100%;">
                    @php
                    if($orden->completo == '0'){
                        if($pct<'100'){
                        echo "{{trans('laboratorio.informe_parcial')}}";
                        }
                    }
                    $cantidad_detalle=0;
                    @endphp
                </div>
                <!-- CONTENT -->
                <div class="row" style="border-bottom: 1px solid #009a98;padding: 0px;width: 100%;">
                @foreach($agrupador as $value)
                    @php $i_agrupador=0; $cantidad_detalle= $detalle->count();@endphp
                    @foreach($detalle as $value_detalle)
                        @php
                        $i=0;

                        if($orden->seguro->tipo == '0' ){
                            $id_agrupador = $value_detalle->examen->id_agrupador;
                        }else{
                            $agrupador_part = DB::table('examen_agrupador_sabana')->where('id_examen',$value_detalle->examen->id)->first();

                            $id_agrupador = 0;
                            if(!is_null($agrupador_part)){
                            $id_agrupador = $agrupador_part->id_examen_agrupador_labs;
                            }
                        }
                        @endphp
                        @if($value_detalle->examen->no_resultado=='0')
                        @if($id_agrupador == $value->id)
                            @if($value_detalle->examen->resultado_especial == null)
                            @php

                                if($value_detalle->examen->sexo_n_s=='0'){
                                $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo','3');
                                }else{
                                $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo',$orden->paciente->sexo);
                                }
                                $xfmostrar = false;
                                if($parametro_nuevo->count()>0){
                                foreach($parametro_nuevo as $xpar){
                                    $xresultado = $orden->resultados
                                    ->where('id_parametro', $xpar->id)
                                    ->where('certificado','1')
                                    ->first();
                                    if(!is_null($xresultado)){
                                    $xfmostrar = true; break;
                                    }
                                }
                                }
                            @endphp
                            @if($xfmostrar)
                            <div class="col-md-12">
                                @if($i_agrupador == 0)
                                <div style="text-align: center;border-bottom: 1px solid #009a98;padding: 0px;background-color: #e5f5f5;width: 1122px;padding-top: 5px;padding-bottom: 5px;font-size: 18px;">
                                    {{$value->nombre}}</b>
                                    @php $i_agrupador=1; @endphp
                                </div>
                                <div style="width: 40%;float:left;font-size: 19px;padding-bottom: 0;padding-top: 0;" >NOMBRE</b></div>
                                <div style="width: 20%;float:left;font-size: 19px;padding-bottom: 0;padding-top: 0;" >RESULTADO</b></div>
                                <div style="width: 15%;float:left;font-size: 19px;padding-bottom: 0;padding-top: 0;" >UNIDADES</b></div>
                                <div style="width: 25%;float:left;font-size: 19px;padding-bottom: 0;padding-top: 0;" >REFERENCIA</b></div>
                                @endif
                                @if($i == 0)
                                @php $xvhfecha = null; @endphp
                                @if($parametro_nuevo->count() > 1 && $xfmostrar )
                                @if(substr($value_detalle->examen->nombre,0,12) == "trans('laboratorio.testalimento')" || substr($value_detalle->examen->nombre,0,15) == "trans('laboratorio.alergiasaalim')")
                                    @foreach($parametro_nuevo as $xvhpar)
                                        @php
                                        $resultado = $resultados->where('id_parametro', $xvhpar->id)->first();
                                        @endphp
                                        @if(!is_null($resultado))
                                            @php
                                            $xvhfecha = $resultado->created_at;
                                            
                                            break;

                                            @endphp  
                                        @endif
                                    @endforeach
                                    @php  @endphp
                                    @endif
                                    

                                    @if($value_detalle->id_examen!='639')
                                    <div style="border-bottom: 1px solid #009a98;clear:both;padding-top: 10px;padding-bottom: 10px;">
                                    <b style="font-size: 20px;">{{$value_detalle->examen->descripcion}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  @if($xvhfecha!=null){{trans('laboratorio.fecha_muestra')}}: {{$xvhfecha}} @endif</b>
                                    </div>
                                    @if($value_detalle->examen->texto!=null)
                                    <div style="border-bottom: 1px solid #009a98;padding: 0px;font-size: 20px !important;">
                                        <?php echo $value_detalle->examen->texto; ?>
                                    </div>
                                    @endif
                                    @endif
                                @endif
                                @php $i = 1; @endphp
                                @endif

                                @if($value_detalle->id_examen=='639' && $xfmostrar)
                                @if($cantidad_detalle>1)
                                <div></div>
                                @endif
                                <section style="float:left;width: 68%;">
                                    <!--br-->
                                    <div style="text-align: center;border-bottom: 1px solid #009a98;padding: 0px; width: 1122px;">
                                            <b>{{trans('laboratorio.fecha_muestra')}}</b>@if($xvhfecha!=null) {{trans('laboratorio.fecha_muestra')}}:  {{$xvhfecha}} @endif
                                </div>
                                <!--br-->

                                    @php $cuenta=0;

                                    $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo','3');
                                    @endphp

                                    <table style="width: 98%;">
                                        @foreach($parametro_nuevo as $value_agrupador)
                                        @php
                                            $rvalor=0;

                                            $resultado = $resultados->where('id_parametro',$value_agrupador->id)->first();
                                            if(!is_null($resultado)){
                                            $rvalor=$resultado->valor;
                                            }
                                        @endphp
                                        @if($value_agrupador->orden=='46' || $value_agrupador->orden=='48')
                                            @if($cuenta == 0) <tr> @endif
                                            <td  style=" font-size: 15px; padding: 12px;">{{$value_agrupador->sec}}</td>
                                            <td style=" font-size: 15px;">{{$value_agrupador->nombre}}</td>
                                            <td style="border: 1px solid #FFF;text-align: center;" ></td>
                                            <td style="border: 1px solid #FFF;text-align: center;" ></td>
                                            <td style="background-color:#A8A8A8 ; border: 1px solid #FFF;text-align: center;font-size: 18px;"> @if($value_agrupador->orden=='48')  <span style="color: white;">X</span> @else &nbsp; @endif</td>
                                            @php $cuenta++; @endphp
                                            @if($cuenta == 2)
                                            </tr>

                                            @endif
                                        @else
                                            @if($cuenta == 0) <tr> @endif
                                            <td style="font-size: 15px; padding: 12px;width: 5px;">{{$value_agrupador->sec}}</td>
                                            <td style=" font-size: 15px; width: 60px;">{{$value_agrupador->nombre}}</td>
                                            <td style="background-color:#eaf2fb ; border: 1px solid #FFF;text-align: center;font-size: 18px; width: 25px;" > @if($rvalor==null || $rvalor==0) X @endif</td>
                                            <td style="background-color:#ACCBEE ; border: 1px solid #FFF;text-align: center;font-size: 18px; width: 25px;" > @if($rvalor==1) @if($resultado->certificado=='1') X @endif @else &nbsp; @endif</td>
                                            <td style="background-color:#8AB0DB ; border: 1px solid #FFF;text-align: center;font-size: 18px;width: 25px;" > @if($rvalor==2) @if($resultado->certificado=='1') X @endif @else &nbsp; @endif</td>
                                            <td style="background-color:#376EAC ; border: 1px solid #FFF;text-align: center;font-size: 18px;width: 25px;">  @if($rvalor==3) <span style="color: white">@if($resultado->certificado=='1') X @endif</span> @else &nbsp; @endif</td>
                                            @php $cuenta++; @endphp
                                            @if($cuenta == 2)
                                            </tr>
                                            @php
                                            $cuenta=0;
                                            @endphp
                                            @endif
                                        @endif
                                        @endforeach
                                    </table>
                                </section>
                                <section style="float:right;width: 30%;">
                                    <table>
                                    <tr>
                                        <td>
                                        <br>
                                        <br>
                                        <p style=" font-size: 20px;">{{trans('laboratorio.a19')}}</p>
                                        <p style=" font-size: 20px;">{{trans('laboratorio.a20')}}</p>
                                        <p style=" font-size: 20px;">{{trans('laboratorio.a22')}}</p>
                                        <p style=" font-size: 20px;">{{trans('laboratorio.a30')}}</p>
                                        <p style=" font-size: 20px;">{{trans('laboratorio.a31')}}</p>
                                        <p style=" font-size: 20px;">{{trans('laboratorio.a33')}}</p>
                                        <p align="justify" style=" font-size: 20px; "> {{trans('laboratorio.afinal')}}</p>
                                        <p align="justify" style=" font-size: 20px;"> {{trans('laboratorio.afinal2')}} </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <table align="right"  cellpadding="0" cellspacing="0" width="100%" >
                                        <tr>
                                        <td BGCOLOR="eaf2fb" width="15%" border="1" > &nbsp;</td>
                                        <td width="30%" style=" font-size: 20px;">{{trans('laboratorio.negativo')}}</td>
                                        </tr>
                                        <tr>
                                        <td BGCOLOR="ACCBEE" width="15%" border="1" > &nbsp;</td>
                                        <td width="30%" style=" font-size: 20px;">{{trans('laboratorio.reaccion_leve')}}</td>
                                        </tr>
                                        <tr>
                                        <td BGCOLOR="8AB0DB"  width="15%" border="1"> &nbsp;</td>
                                        <td width="50%" style=" font-size: 20px;">{{trans('laboratorio.reaccion_moderada')}}</td>
                                        </tr>
                                        <tr>
                                        <td BGCOLOR="376EAC"  width="15%" border="1"> &nbsp;</td>
                                        <td width="30%" style=" font-size: 20px;">{{trans('laboratorio.reaccion_fuerte')}}</td>
                                        </tr>
                                        </table>
                                    </tr>
                                    </table>
                                </section>
                                @endif
                                <div style="clear:both;"></div>
                                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 18px;table-layout: fixed;width: 100%;">
                                    <tbody>
                                    @if($value_detalle->id_examen=='661')
                                        <tr style="font-size: 19px;">
                                        <td style="width: 30%;"><b>{{trans('laboratorio.grado-')}}</b></td>
                                        <td style="width: 20%;"><b>% {{trans('laboratorio.celulas-')}}</b></td>
                                        <td style="width: 15%;"><b>L.A.P. {{trans('laboratorio.puntos-')}}</b></td>
                                        <td style="width: 35%;"><b>{{trans('laboratorio.referencia-')}}</b></td>
                                        </tr>
                                    @endif
                                    @if($value_detalle->id_examen=='661')
                                        @foreach($parametro_nuevo as $value_agrupador)
                                        @if($value_agrupador->unidad1=='L.A.P. PUNTOS')
                                            @php
                                            $resultado = $resultados->where('id_parametro', $value_agrupador->id)->first();
                                            @endphp
                                            <tr role="row" >
                                            <td style="width: 30%;">{{$value_agrupador->nombre}}</td>
                                            @php
                                                $hermano = DB::table('examen_parametro')->where('id_examen',$value_detalle->id_examen)->where('orden',$value_agrupador->orden)->where('unidad1','% CELULAS')->first();
                                                $resultado_2 = null;
                                                if(!is_null($hermano)){
                                                $resultado_2 = $resultados->where('id_parametro', $hermano->id)->first();
                                                }

                                            @endphp
                                            <td style="width: 20%;">
                                                <div style="word-wrap: break-word;">
                                                @if(!is_null($hermano))
                                                    @if(!is_null($resultado_2)) @if($resultado_2->certificado=='1') {{$resultado_2->valor}}@else{{"0"}} @endif @endif
                                                @endif
                                                </div>
                                            </td>
                                            <td style="width: 15%;">@if(!is_null($resultado))  {{$resultado->valor}}@else{{"0"}} @endif</td>
                                            <td style="width: 35%;">@if($value_agrupador->texto_referencia == ""){{$value_agrupador->valor1}} - {{$value_agrupador->valor1g}}@else <?php echo $value_agrupador->texto_referencia; ?> @endif</td>

                                            </tr>

                                        @endif
                                        @endforeach

                                    @elseif($value_detalle->id_examen=='639')

                                    @else
                                    @if(!(($value_detalle->id_examen == '414' || $value_detalle->id_examen == '412' || $value_detalle->id_examen == '680') && $orden->created_at<'2018-12-17' && $orden->id!='1543' && $orden->id!='1291'))
                                        @foreach($parametro_nuevo as $value_agrupador)
                                            @php
                                            $resultado = $resultados->where('id_parametro', $value_agrupador->id)->first();
                                            @endphp

                                            @if(!is_null($resultado))
                                            @if($resultado->certificado=='1')
                                                <tr role="row">
                                                <td style="width: 30%;padding-left: 3px;padding-top: 5px;@if($parametro_nuevo->count() == 1) font-size: 20px;font-weight: 800;padding-left: -3px;border-top: solid 1px #009a98;padding-top: 5px;padding-bottom: 5px;@else padding-left: 10px; @endif">{{$value_agrupador->nombre}}</td>
                                                <td style="width: 20%;padding-top: 5px;@if($parametro_nuevo->count() == 1) border-top: solid 1px #009a98;padding-top: 5px;padding-bottom: 5px; @endif">
                                                    <!--div style="word-wrap: break-word;"-->
                                                    @if(!is_null($resultado)){{$resultado->valor}}@else{{"0"}}@endif
                                                    <!--/div-->
                                                </td>
                                                @php
                                                    $fl_ref = 0;
                                                    $ref_ant = Sis_medico\Examen_Parametro_Referencia_Anterior::where('id_parametro',$value_agrupador->id)->where('fecha_valida','>',$orden->fecha_orden)->orderBy('fecha_valida','asc')->first();
                                                    if(!is_null($ref_ant)){
                                                    $fl_ref = 1;
                                                    }
                                                @endphp
                                                <td style="width: 15%;padding-top: 5px;@if($parametro_nuevo->count() == 1) border-top: solid 1px #009a98;padding-top: 5px;padding-bottom: 5px; @endif">@if($fl_ref) {{$ref_ant->unidad1}} @else {{$value_agrupador->unidad1}} @endif</td>
                                                <td style="width: 35%;padding-top: 5px;@if($parametro_nuevo->count() == 1) border-top: solid 1px #009a98;padding-top: 5px;padding-bottom: 5px; @endif">
                                                @if($fl_ref)
                                                    @if($ref_ant->texto_referencia == "")
                                                    {{$ref_ant->valor1}} - {{$ref_ant->valor1g}}
                                                    @else
                                                    <?php echo $ref_ant->texto_referencia; ?>
                                                    @endif
                                                @else
                                                    @if($value_agrupador->texto_referencia == "")
                                                    {{$value_agrupador->valor1}} - {{$value_agrupador->valor1g}}
                                                    @else
                                                    <?php echo $value_agrupador->texto_referencia; ?>
                                                    @endif
                                                @endif
                                                </td>

                                                </tr>
                                            @endif
                                            @endif

                                        @endforeach
                                        @if($value_detalle->examen->tiene_detalle=='1')
                                            @php
                                            $sub_resultados = DB::table('examen_sub_resultado')->where('id_orden',$orden->id)->where('estado','1')->where('id_examen',$value_detalle->id_examen)->get();
                                            @endphp
                                            <tr >
                                            <td colspan="4" id="sub_tabla">
                                                <table class="table table-bordered table-hover dataTable">
                                                <tbody>
                                                    @foreach($sub_resultados as $sub)
                                                    <tr>
                                                    <td style="width: 300px !important;">{{$sub->campo1}}</td>
                                                    <td style="width: 300px !important;">{{$sub->campo2}}</td>
                                                    <td style="width: 300px !important;">{{$sub->campo3}}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                                </table>
                                            </td>
                                            </tr>
                                        @endif
                                        @endif
                                    @endif
                                    </tbody>
                                </table>
                                </div>
                            </div>
                            @endif
                            @else
                            <div class="col-md-12" style="text-align: center;">{{$value_detalle->examen->nombre}}</div>
                            @php 
                                $parametros_nuevos = $parametros->where('id_examen', $value_detalle->id_examen)->where('id_parametro_ref','<>',null); 
                                $xp_cant = 0;  $limite2 = 0;
                                $ele_cant = count($parametros_nuevos);
                            @endphp
                            
                                
                            <table class="table table-striped" style="width: 100%">
                                <tr>                    
                                <td style="font-size: 18px;width:1%;"></td>
                                <td style="font-size: 18px;width:19%;"></td>
                                <td style="font-size: 20px;border: 1px solid #FFF;text-align: center;font-size: 18px; width: 15%;">NEGATIVO</td>
                                <td style="font-size: 20px;border: 1px solid #FFF;text-align: center;font-size: 18px; width: 15%;">POSITIVO</td>
                                <td style="font-size: 18px;width:1%;"></td>
                                <td style="font-size: 18px;width:19%;"></td>
                                <td style="font-size: 20px;border: 1px solid #FFF;text-align: center;font-size: 18px; width: 15%;">NEGATIVO</td>
                                <td style="font-size: 20px;border: 1px solid #FFF;text-align: center;font-size: 18px; width: 15%;">POSITIVO</td>
                                </tr>
                                <tr>                    
                                @foreach($parametros_nuevos as $px_n)
                                @php  
                                    $xresultado = $orden->resultados
                                    ->where('id_parametro', $px_n->id)
                                    ->where('certificado','1')
                                    ->first();
                                    if(!is_null($xresultado)){
                                    $xp_cant ++; $limite2 ++;
                                    }    
                                    
                                    $color1 = '#ACCBEE'; $color2 = '#8AB0DB';  
                                    if($px_n->id_parametro_ref == '1103'){
                                    $color2 = '#76D7C4'; $color1 = '#A3E4D7';
                                    }
                                    if($px_n->id_parametro_ref == '1146'){
                                    $color1 = '#FADBD8'; $color2 = '#FDEDEC';
                                    }
                                    
                                @endphp
                                @if(!is_null($xresultado))
                                <td style="font-size: 18px;width:1%;"> {{$xp_cant}}</td>
                                <td style="font-size: 18px;width:19%;"> {{$xresultado->parametro->nombre}}</td>
                                <td style="font-size: 20px;background-color:{{$color1}} ; border: 1px solid #FFF;text-align: center;font-size: 18px; width: 15%;"> @if($xresultado->valor == 'NEGATIVO') X @endif</td>
                                <td style="font-size: 20px;background-color:{{$color2}} ; border: 1px solid #FFF;text-align: center;font-size: 18px; width: 15%;"> @if($xresultado->valor == 'POSITIVO') X @endif</td>
                                @if($limite2 == 2) @php $limite2 = 0;  @endphp </tr><tr> @endif
                                @endif 
                                @endforeach
                            </table> 

                            @php 
                                $parametros_nuevos2 = $parametros->where('id_examen', $value_detalle->id_examen)->where('id_parametro_ref',null); 
                                
                            @endphp
                            <table class="table table-striped" style="width: 50%">
                                <tr>                    
                                <td style="font-size: 18px;width:1%;"></td>
                                <td style="font-size: 18px;width:19%;"></td>
                                <td style="font-size: 20px;border: 1px solid #FFF;text-align: center;font-size: 18px; width: 15%;">NEGATIVO</td>
                                <td style="font-size: 20px;border: 1px solid #FFF;text-align: center;font-size: 18px; width: 15%;">POSITIVO</td>
                                </tr>
                                                
                                @foreach($parametros_nuevos2 as $px_n)
                                @php  
                                    $xresultado = $orden->resultados
                                    ->where('id_parametro', $px_n->id)
                                    ->where('certificado','1')
                                    ->first();
                                    
                                    if(!is_null($xresultado)){
                                    $xp_cant ++; 
                                    }  
                                    
                                    
                                    $color1 = '#ACCBEE'; $color2 = '#8AB0DB';  
                                    if($px_n->id == '1103'){
                                    $color2 = '#76D7C4'; $color1 = '#A3E4D7';
                                    }
                                    if($px_n->id == '1146'){
                                    $color1 = '#FADBD8'; $color2 = '#FDEDEC';
                                    }
                                @endphp
                                @if(!is_null($xresultado))
                                <tr> 
                                <td style="font-size: 18px;width:1%;"> {{$xp_cant}} </td>
                                <td style="font-size: 18px;width:19%;"> {{$xresultado->parametro->nombre}} </td>
                                <td style="font-size: 20px;background-color:{{$color1}} ; border: 1px solid #FFF;text-align: center;font-size: 18px; width: 15%;"> @if($xresultado->valor == 'NEGATIVO') X @endif </td>
                                <td style="font-size: 20px;background-color:{{$color2}} ; border: 1px solid #FFF;text-align: center;font-size: 18px; width: 15%;"> @if($xresultado->valor == 'POSITIVO') X @endif </td>
                                </tr>
                                @endif
                                @endforeach
                            </table>
                            @endif                         

                        @endif

                        @endif



                    @endforeach

                @endforeach

                </div>
                <!-- FOOTER -->
                <div class="row" style="border-bottom: 1px solid #009a98;padding: 0px;width: 100%;height: 300px;">
                <table style="font-size: 16px;color: #009a98; width: 100%;">
                    <thead>
                        <tr role="row" class="align: center;">
                            <td colspan="5" class="subtitulo0"><b>D. DATOS DEL PROFESIONAL RESPONSABLE</b></td>
                        </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="subtitulo1"><b>FECHA<br>(aaaa-mm-dd)</b></td>
                        <td class="subtitulo1"><b>HORA<br>(hh:mm)</b></td>
                        <td class="subtitulo1"><b>PRIMER NOMBRE</b></td>
                        <td class="subtitulo1"><b>PRIMER APELLIDO</b></td>
                        <td class="subtitulo1"><b>SEGUNDO APELLIDO</b></td>
                    </tr>
                    <tr>
                        @php
                        $firma = Sis_medico\Firma_Usuario::where('nombre', 'labs_responsable.jpg')->first();
                        @endphp
                        <td style="width: 5%;" class="contenido1">@if($orden->fecha_convenios == null) {{ substr($orden->fecha_orden,0,10) }} @else{{ substr($orden->fecha_convenios,0,10) }} @endif</td>
                        <td style="width: 5%;" class="contenido1">@if($orden->fecha_convenios == null) {{ substr($orden->fecha_orden,11,8) }}@else {{ substr($orden->fecha_convenios,11,8) }}@endif</td>
                        @if(!is_null($firma))
                        @php
                        $id_laboratorista = $firma->id_usuario;
                        $laboratorista = Sis_medico\User::where('id',$id_laboratorista)->first();
                        @endphp
                        <td style="width: 30%;" class="contenido1">{{$laboratorista->nombre1}}  </td>
                        <td style="width: 30%;" class="contenido1">{{$laboratorista->apellido1}} </td>
                        <td style="width: 30%;" class="contenido1">{{$laboratorista->apellido2}} </td>
                        @else
                        <td style="width: 30%;" class="contenido1"> {{$ucreador->nombre1}} </td>
                        <td style="width: 30%;" class="contenido1"> {{$ucreador->apellido1}} </td>
                        <td style="width: 30%;" class="contenido1">{{$ucreador->apellido2}} </td>
                        @endif
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 25%;" class="subtitulo1"><b>NÚMERO DE DOCUMENTO DE IDENTIFICACIÓN</b></td>
                        <td colspan="2" style="width: 40%;" class="subtitulo1"><b>FIRMA</b></td>
                        <td style="width: 35%;" class="subtitulo1"><b>SELLO</b></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 25%;" class="contenido1">{{$laboratorista->id}}</td>
                        <td colspan="2" style="width: 40%;height: 100px;" class="contenido1">
                        @if( $orden->fecha_orden < '2021-06-01  0:00:00') 
                            <img style="position: absolute;margin-left: 150px" width=auto height=80 src="{{base_path().'/storage/app/logo/labs_karla.png'}}" align=center hspace=12>
                        @else 
                            <img style='position: absolute;margin-left: 150px' width=auto height=80 src="{{base_path().'/storage/app/logo/labs_responsable.jpg'}}" align=center hspace=12> 
                        @endif
                        </td>
                        <td style="width: 35%;" class="contenido1">LABORATORISTA RESPONSABLE:<br>{{$laboratorista->apellido1}} {{$laboratorista->apellido2}} {{$laboratorista->nombre1}}  {{$laboratorista->nombre2}}<br> Ci: {{$laboratorista->id}}  </td>
                    </tr>
                    </tbody>
                </table>
                </div>
                
                <div class="row" style="border-bottom: 1px solid #009a98;padding: 0px;width: 100%;">
                <table style="font-size: 14px;color: #009a98; width: 100%;">
                    <tr>
                    <td style="color: black;width: 50%;text-align: left;font-size: 16px;"><b>SNS-MSP/HCU-form.010B/2021</b></td>
                    <td style="color: black;width: 50%;text-align: right;font-size: 16px;"><b>LABORATORIO CLÍNICO - INFORME (1)</b></td>
                    </tr>
                </table>
                </div>
                <script type="text/php">
                    if (isset($pdf)) {
                    $font = $fontMetrics->getFont("Arial", "bold");
                    $pdf->page_text(550, 780, "{PAGE_NUM}/{PAGE_COUNT}", $font, 9, array(0, 0, 0));
                    }
                </script>

            </div>
        </main>
    </body>
</html>


