<html>

<head>

    <title>REPORTE DE CARDIOLOGIA {{ $paciente->apellido1 }} {{ $paciente->nombre1 }}</title>
    <meta http-equiv=Content-Type content="text/html; charset=windows-1252">
    <meta name=Generator content="Microsoft Word 15 (filtered)">
    <style type="text/css">
        .div_tiny p {
            margin: 0;
        }
    </style>
    <style>
        /* Font Definitions */
        @font-face {
            font-family: "Cambria Math";
            panose-1: 2 4 5 3 5 4 6 3 2 4;
        }

        @font-face {
            font-family: Calibri;
            panose-1: 2 15 5 2 2 2 4 3 2 4;
        }

        @font-face {
            font-family: Harrington;
            panose-1: 4 4 5 5 5 10 2 2 7 2;
        }

        /* Style Definitions */
        p.MsoNormal,
        li.MsoNormal,
        div.MsoNormal {
            margin-top: 0cm;
            margin-right: 0cm;
            margin-bottom: 10.0pt;
            margin-left: 0cm;
            line-height: 115%;
            font-size: 11.0pt;
            font-family: "Calibri", sans-serif;
        }

        p.MsoNoSpacing,
        li.MsoNoSpacing,
        div.MsoNoSpacing {
            margin: 0cm;
            margin-bottom: .0001pt;
            font-size: 9.0pt;
            font-family: "Calibri", sans-serif;
        }

        span.apple-converted-space {
            mso-style-name: apple-converted-space;
        }

        .MsoChpDefault {
            font-size: 10.0pt;
            font-family: "Calibri", sans-serif;
        }

        @page WordSection1 {
            size: 595.3pt 841.9pt;
            margin: 42.55pt 49.55pt 7.1pt 70.9pt;
        }

        div.WordSection1 {
            page: WordSection1;
        }

        /* List Definitions */
        ol {
            margin-bottom: 0cm;
        }

        ul {
            margin-bottom: 0cm;
        }

        span {
            font-size: 10.0pt;
        }
    </style>


</head>
@php $empresa_nombre = Sis_medico\Empresa::where('prioridad', '1')->where('estado', 1)->first();  @endphp
<body lang=ES-EC style="margin-top: -20px;">
    <div id="header">
        <table>
            <tr>
                <td style="width: 150px;"><img style="margin: 0;width: 180px;"
                        src="{{base_path().'/storage/app/logo/'.$empresa_nombre->logo}}"></td>
                <td colspan="2">
                    <p style="font-size: 19px; text-align: center; width: 350px; margin-left:50px;"><b
                            style="text-align: center;">@if(!is_null($empresa_nombre->id)) {{$empresa_nombre->razonsocial}} @endif</b></p>
                </td>
            </tr>
            <tr>
                <td></td>
                <td colspan="2"><span lang=ES
                        style='font-size:12px; margin-left:50px;'>{{ trans('pacientes.fechaimp') }} :
                        @if (date('N') == 1)
                            @lang('pacientes.lunes')
                        @elseif(date('N') == 2)
                            @lang('pacientes.martes')
                        @elseif(date('N') == 3)
                            @lang('pacientes.miercoles')
                        @elseif(date('N') == 4)
                            @lang('pacientes.jueves')
                        @elseif(date('N') == 5)
                            @lang('pacientes.viernes')
                        @elseif(date('N') == 6)
                            @lang('pacientes.sabado')
                        @elseif(date('N') == 7)
                            @lang('pacientes.domingo')
                        @endif
                        {{ date('j') }} @lang('pacientes.de')
                        @if (date('n') == 1)
                            @lang('pacientes.enero')
                        @elseif(date('n') == 2)
                            @lang('pacientes.febrero')
                        @elseif(date('n') == 3)
                            @lang('pacientes.marzo')
                        @elseif(date('n') == 4)
                            @lang('pacientes.abril')
                        @elseif(date('n') == 5)
                            @lang('pacientes.mayo')
                        @elseif(date('n') == 6)
                            @lang('pacientes.junio')
                        @elseif(date('n') == 7)
                            @lang('pacientes.julio')
                        @elseif(date('n') == 8)
                            @lang('pacientes.agosto')
                        @elseif(date('n') == 9)
                            @lang('pacientes.septiembre')
                        @elseif(date('n') == 10)
                            @lang('pacientes.octubre')
                        @elseif(date('n') == 11)
                            @lang('pacientes.noviembre')
                        @elseif(date('n') == 12)
                            @lang('pacientes.diciembre')
                        @endif
                        @lang('pacientes.del') {{ date('Y') }}
                    </span></td>
            </tr>
        </table>
    </div>
    <input type="hidden" name="fecha_nacimiento" id="fecha_nacimiento" value="{{ $paciente->fecha_nacimiento }}">

    <div class=WordSection1>
        <p class=MsoNoSpacing><b><span lang=ES style='font-size:6.0pt'>&nbsp;</span></b></p>
        <table style="border: none; font-size: 17px !important; line-height: 25px;">
            <tbody>
                <tr>
                    <td><span><b>{{ trans('pacientes.ncodigo') }} </b></span></td>
                    <td></td>
                </tr>
                <tr>
                    <td><span><b>{{ trans('pacientes.cedula') }}</b></span></td>
                    <td>{{ $paciente->id }}</td>
                </tr>
                <tr>
                    <td><span><b>{{ trans('pacientes.apellidos') }} </b></span></td>
                    <td>{{ $paciente->apellido1 }} @if ($paciente->apellido2 != '(N/A)')
                            {{ $paciente->apellido2 }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td><span><b>{{ trans('pacientes.nombres') }}: </b></span></td>
                    <td>{{ $paciente->nombre1 }} @if ($paciente->nombre2 != '(N/A)')
                            {{ $paciente->nombre2 }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td><span><b>{{ trans('pacientes.fechaimp') }}: </b></span></td>
                    <td>{{ date('d/m/Y', strtotime($paciente->created_at)) }}</td>
                </tr>
                <tr>
                    <td><span><b>{{ trans('pacientes.fechadenacimiento') }}: &nbsp;&nbsp;&nbsp;</b></span></td>
                    <td>{{ date('d/m/Y', strtotime($paciente->fecha_nacimiento)) }}</td>
                </tr>
                <tr>
                    <td><span><b>{{ trans('pacientes.edad') }}: </b></span></td>
                    <td>{{ $age }} {{ trans('pacientes.anos') }}</td>
                </tr>
                <tr>
                    <td><span><b>{{ trans('pacientes.sexo') }}: </b></span></td>
                    <td>
                        @if ($paciente->sexo == 1)
                            {{ trans('pacientes.masculino') }}
                        @elseif($paciente->sexo == 2)
                            {{ trans('pacientes.femenino') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td><span><b>{{ trans('pacientes.estadocivil') }}: </b></span></td>
                    <td>
                        @if ($paciente->estadocivil == 1)
                            {{ trans('pacientes.soltero') }}
                        @elseif($paciente->estadocivil == 2)
                            {{ trans('pacientes.casado') }}
                        @elseif($paciente->estadocivil == 3)
                            {{ trans('pacientes.viudo') }}
                        @elseif($paciente->estadocivil == 4)
                            {{ trans('pacientes.divorciado') }}
                        @elseif($paciente->estadocivil == 5)
                            {{ trans('pacientes.unionlibre') }}
                        @elseif($paciente->estadocivil == 6)
                            {{ trans('pacientes.unionhecho') }}
                        @endif
                    </td>
                </tr>
                @if ($paciente->id != '1303411605')
                    <tr>
                        <td><span><b>{{ trans('pacientes.seguro') }}: </b></span></td>
                        <td>{{ $paciente->seguro->nombre }}</td>
                    </tr>
                @endif
                <tr>
                    <td><span><b>{{ trans('pacientes.tiposangre') }}: </b></span></td>
                    <td>{{ $paciente->gruposanguineo }}</td>
                </tr>
                <tr>
                    <td><span><b>{{ trans('pacientes.lugarnacimiento') }}: </b></span></td>
                    <td>{{ $paciente->lugar_nacimiento }}</td>
                </tr>
                <tr>
                    <td><span><b>{{ trans('pacientes.procedencia') }}: </b></span></td>
                    <td>{{ $paciente->ciudad }}</td>
                </tr>
                <tr>
                    <td><span><b>{{ trans('pacientes.ocupacion') }}: </b></span></td>
                    <td>{{ $paciente->ocupacion }}</td>
                </tr>
                <tr>
                    <td><span><b>{{ trans('pacientes.direccion') }}: </b></span></td>
                    <td>{{ $paciente->direccion }}</td>
                </tr>
                <tr>
                    <td><span><b>{{ trans('pacientes.telefono') }}: </b></span></td>
                    <td>{{ $paciente->telefono1 }}</td>
                </tr>
                <tr>
                    <td><span><b>{{ trans('pacientes.celular') }}: </b></span></td>
                    <td>{{ $paciente->telefono2 }}</td>
                </tr>
                <tr>
                    <td><span><b>{{ trans('pacientes.email') }}: </b></span></td>
                    <td>{{ $paciente->usuario->email }}</td>
                </tr>
            </tbody>
        </table>





    </div>
    <div style="page-break-after:always;"></div>
    <span style="font-size: 25px; font-color: blue;width: 120px;"><b>{{ trans('pacientes.visitas') }}
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></span>
    <span style="width: 30px;"> &nbsp;</span>

    <span>{{ trans('pacientes.fechaimp') }} :
        @if (date('N') == 1)
            @lang('pacientes.lunes')
        @elseif(date('N') == 2)
            @lang('pacientes.martes')
        @elseif(date('N') == 3)
            @lang('pacientes.miercoles')
        @elseif(date('N') == 4)
            @lang('pacientes.jueves')
        @elseif(date('N') == 5)
            @lang('pacientes.viernes')
        @elseif(date('N') == 6)
            @lang('pacientes.sabado')
        @elseif(date('N') == 7)
            @lang('pacientes.domingo')
        @endif
        {{ date('j') }} @lang('pacientes.de')
        @if (date('n') == 1)
            @lang('pacientes.enero')
        @elseif(date('n') == 2)
            @lang('pacientes.febrero')
        @elseif(date('n') == 3)
            @lang('pacientes.marzo')
        @elseif(date('n') == 4)
            @lang('pacientes.abril')
        @elseif(date('n') == 5)
            @lang('pacientes.mayo')
        @elseif(date('n') == 6)
            @lang('pacientes.junio')
        @elseif(date('n') == 7)
            @lang('pacientes.julio')
        @elseif(date('n') == 8)
            @lang('pacientes.agosto')
        @elseif(date('n') == 9)
            @lang('pacientes.septiembre')
        @elseif(date('n') == 10)
            @lang('pacientes.octubre')
        @elseif(date('n') == 11)
            @lang('pacientes.noviembre')
        @elseif(date('n') == 12)
            @lang('pacientes.diciembre')
        @endif
        @lang('pacientes.del') {{ date('Y') }}
    </span>
    <hr>
    <table style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
        <tbody>
            <tr>
                <td style="border-right: 1px solid;width: 103px;"><b>{{ trans('pacientes.cedula') }}:</b></td>
                <td style="border-right: 1px solid;width: 110px;">&nbsp;&nbsp; {{ $paciente->id }}</td>
                <td style="border-right: 1px solid;width: 65px;"><b>{{ trans('pacientes.paciente') }}:</b></td>
                <td style="border-right: 1px solid;" colspan="4">&nbsp;&nbsp; {{ $paciente->apellido1 }}
                    @if ($paciente->apellido2 != '(N/A)')
                        {{ $paciente->apellido2 }}
                        @endif {{ $paciente->nombre1 }} @if ($paciente->nombre2 != '(N/A)')
                            {{ $paciente->nombre2 }}
                        @endif
                </td>
            </tr>
        </tbody>
    </table>
    <hr>
    @php
        $contador_1 = 1;
        $modulo = 0;
    @endphp
 
    
        @foreach ($general->procedimiento as $procedimiento)
            @if ($procedimiento->id_procedimiento_completo == 40)
                @php
                    $firma     = Sis_medico\Firma_Usuario::where('id_usuario', $procedimiento->id_doctor_examinador)->first();
                    $evolucion = Sis_medico\Hc_Evolucion::where('hc_id_procedimiento', $procedimiento->id)->first();
                    $cie10 = Sis_medico\Hc_Cie10::where('hc_id_procedimiento', $procedimiento->id)->get();
                    $receta = Sis_medico\hc_receta::where('id_hc', $general->hcid)->first();
                    $hc_cardio = Sis_medico\Cardiologia::where('hcid', $general->hcid)->first();
      
                @endphp
                <table style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
                    <tbody>
                        <tr>
                            <td style="border-right: 1px solid;"><b>{{ trans('pacientes.fecha') }}: </b></td>
                            <td style="border-right: 1px solid;">&nbsp;&nbsp;
                                {{ date('d/m/Y', strtotime($general->created_at)) }}</td>
                            <td style="border-right: 1px solid;"><b>{{ trans('pacientes.medicoexaminador') }}:</b></td>
                            <td colspan="3" style="border-right: 1px solid;">&nbsp;Dr.
                                {{ $general->doctor_1->apellido1 }} @if ($general->doctor_1->apellido2 != '(N/A)')
                                    {{ $general->doctor_1->apellido2 }}
                                    @endif {{ $general->doctor_1->nombre1 }} @if ($general->doctor_1->nombre2 != '(N/A)')
                                        {{ $general->doctor_1->nombre2 }}
                                    @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 1px solid; border-top: 1px solid;">
                                <b>{{ trans('pacientes.presion') }}: </b></td>
                            <td style="border-right: 1px solid;border-top: 1px solid;">
                                <b>{{ trans('pacientes.pulso') }}: </b></td>
                            <td style="border-right: 1px solid;border-top: 1px solid;">
                                <b>{{ trans('pacientes.peso') }}: </b></td>
                            <td style="border-right: 1px solid;border-top: 1px solid;">
                                <b>{{ trans('pacientes.altura') }}: </b></td>
                            <td style="border-right: 1px solid;border-top: 1px solid;">
                                <b>{{ trans('pacientes.perimetroab') }}: </b></td>
                            <td style="border-right: 1px solid;border-top: 1px solid;">
                                <b>{{ trans('pacientes.pesoideal') }}: </b></td>
                        </tr>
                        <tr style="text-align: center;">
                            <td style="border-right: 1px solid; border-top: 1px solid;">&nbsp;@if ($general->presion != null)
                                    @if ($general->presion != 0)
                                        {{ $general->presion }}
                                    @endif
                                @endif
                            </td>
                            <td style="border-right: 1px solid; border-top: 1px solid;">&nbsp;@if ($general->pulso != 0)
                                    {{ $general->pulso }}
                                @endif
                            </td>
                            <td style="border-right: 1px solid; border-top: 1px solid;">&nbsp;@if ($general->peso != 0)
                                    {{ $general->peso }}
                                @endif
                            </td>
                            <td style="border-right: 1px solid; border-top: 1px solid;">&nbsp;@if ($general->altura != 0)
                                    {{ $general->altura }}
                                @endif
                            </td>
                            <td style="border-right: 1px solid; border-top: 1px solid;">&nbsp;@if ($general->perimetro != 0)
                                    {{ $general->perimetro }}
                                @endif
                            </td>
                            <td style="border-right: 1px solid; border-top: 1px solid;">&nbsp;@if ($general->altura != 0)
                                    @php
                                        $altura = pow($general->altura / 100, 2);
                                        $peso_ideal = 21.45 * $altura;
                                        $imc = $general->peso / $altura;
                                        $sexo = null;
                                        if ($paciente->sexo == 1) {
                                            $sexo = 1;
                                        } else {
                                            $sexo = 0;
                                        }
                                        $texto = '';
                                        if ($imc < 16) {
                                            $texto = trans('pacientes.desnutricion');
                                        } elseif ($imc < 18) {
                                            $texto = trans('pacientes.bajo_peso');
                                        } elseif ($imc < 25) {
                                            $texto = trans('pacientes.normal');
                                        } elseif ($imc < 27) {
                                            $texto = trans('pacientes.sobrepeso');
                                        } elseif ($imc < 30) {
                                            $texto = trans('pacientes.obesidad_tipo');
                                        } elseif ($imc < 40) {
                                            $texto = trans('pacientes.obesidad_clinica');
                                        } else {
                                            $texto = trans('pacientes.obesidad_morbida');
                                        }
                                        $gct = 1.2 * $imc + 0.23 * $age - 10.8 * $sexo - 5.4;
                                    @endphp
                                    {{ number_format($peso_ideal, 2, '.', '') }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="border-right: 1px solid; border-top: 1px solid;">
                                <b>{{ trans('pacientes.gct') }}: </b></td>
                            <td colspan="2" style="border-right: 1px solid; border-top: 1px solid;">
                                <b>{{ trans('pacientes.imc') }}: </b></td>
                            <td colspan="2" style="border-right: 1px solid; border-top: 1px solid;">
                                <b>{{ trans('pacientes.imccategoria') }}:</b></td>
                        </tr>
                        <tr style="text-align: center;">
                            <td colspan="2" style="border-right: 1px solid; border-top: 1px solid;">&nbsp;
                                @if ($general->altura != 0)
                                    {{ number_format($gct, 2, '.', '') }}
                                @endif
                            </td>
                            <td colspan="2" style="border-right: 1px solid; border-top: 1px solid;">&nbsp;
                                @if ($general->altura != 0)
                                    {{ number_format($imc, 2, '.', '') }}
                                @endif
                            </td>
                            <td colspan="2" style="border-right: 1px solid; border-top: 1px solid;">&nbsp;
                                @if ($general->altura != 0)
                                    {{ $texto }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" style="border-right: 1px solid; border-top: 1px solid;">
                                <b>{{ trans('pacientes.motivo') }}</b></td>
                        </tr>
                        <tr>
                            <td colspan="6" style="border-right: 1px solid; border-top: 1px solid;">&nbsp;
                                @if ($evolucion != null)
                                    {{ $evolucion->motivo }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" style="border-right: 1px solid; border-top: 1px solid;">
                                <b>{{ trans('pacientes.antecedentes') }}</b></td>
                        </tr>
                        <tr>
                            <td colspan="6" style="border-right: 1px solid; border-top: 1px solid;">
                                <div style="padding: 0;"><b>Antecedentes
                                        Patológicos:</b>{{ $paciente->antecedentes_pat }}</div>
                                <div style="padding: 0;"><b>Antecedentes
                                        Famililares:</b>{{ $paciente->antecedentes_fam }}</div>
                                <div style="padding: 0;"><b>Antecedentes
                                        Quirúrgicos:</b>{{ $paciente->antecedentes_quir }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" style="border-right: 1px solid; border-top: 1px solid;">
                                <b>{{ trans('pacientes.evolucion') }}</b></td>
                        </tr>
                        <tr>
                            <td colspan="6" style="border-right: 1px solid; border-top: 1px solid;">
                                <div class="div_tiny" style="width: 100%;line-height: 1.2;font-size: 12px !important;">
                                    @if ($hc_cardio != null)
                                        @if ($hc_cardio->cuadro_clinico != null)
                                            <?php echo $hc_cardio->cuadro_clinico; ?> @else{{ '&nbsp;' }}
                                        @endif @else{{ '&nbsp;' }}
                                    @endif &nbsp;
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" style="border-right: 1px solid; border-top: 1px solid;">
                                <b>{{ trans('pacientes.examenfisico') }}</b></td>
                        </tr>
                        <tr>
                            <td colspan="6" style="border-right: 1px solid; border-top: 1px solid;">
                                <div style="font-family:'Calibri',sans-serif;margin: 0;padding: 0;font-size: 12px;">
                                    @if ($evolucion != null)
                                        @if ($evolucion->child_pug != null)
                                            {{ $evolucion->child_pug->examen_fisico }} @else{{ '&nbsp;' }}
                                        @endif @else{{ '&nbsp;' }}
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" style="border-right: 1px solid; border-top: 1px solid;">
                                <b>{{ trans('pacientes.diagnostico') }}</b></td>
                        </tr>
                        <tr>
                            <td colspan="6" style="border-right: 1px solid; border-top: 1px solid;">
                                @if ($cie10 != '[]')
                                    @php
                                        $contador = 0;
                                    @endphp
                                    @foreach ($cie10 as $value_cie10)
                                        <?php
                                        $cie10_nombre = Sis_medico\Cie_10_3::find($value_cie10);
                                        if ($cie10_nombre == '[]') {
                                            $cie10_nombre = Sis_medico\Cie_10_4::find($value_cie10);
                                        }
                                        if ($contador != 0) {
                                            echo ', ';
                                        }
                                        echo $cie10_nombre[0]->descripcion . ' (' . $value_cie10->presuntivo_definitivo . ')';
                                        
                                        $contador++;
                                        ?>
                                    @endforeach @else{{ '&nbsp;' }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" style="border-right: 1px solid; border-top: 1px solid;">
                                <b>{{ trans('pacientes.medicacion') }}</b></td>
                        </tr>
                        <tr>
                            <td colspan="6" style="border-right: 1px solid; border-top: 1px solid;">
                                <div class="div_tiny" style="width: 100%;line-height: 1.2;">
                                    @if ($receta != null)
                                        @if ($receta->rp != null)
                                            <?php echo $receta->rp; ?>@else{{ '&nbsp;' }}
                                        @endif @else{{ '&nbsp;' }}
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" style="border-right: 1px solid; border-top: 1px solid;">
                                <b>{{ trans('pacientes.estudiossol') }}</b></td>
                        </tr>
                        <tr>
                            <td colspan="6" style="border-right: 1px solid; border-top: 1px solid;">
                                <div style="font-family:'Calibri',sans-serif;margin: 0;padding: 0;font-size: 12px;">
                                    @if ($general->examenes_realizar != null)
                                        <?php echo $general->examenes_realizar; ?>
                                        
                                        @else{{ '&nbsp;' }}
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p style="text-align: center;">
                    @if(!is_null($firma))
                        <img width=170 height=80 src="{{base_path().'/storage/app/avatars/'.$firma->nombre}}" style="" align=center hspace=12><br>
                        <span style="font-size: 12px;">Dr. {{$procedimiento->doctor->nombre1 }} {{$procedimiento->doctor->nombre2 }} {{$procedimiento->doctor->apellido1 }} {{$procedimiento->doctor->apellido2 }}<br>
                        <span style="font-size: 12px;"><strong>Cédula: </strong>{{$procedimiento->doctor->id}}</span>
                    @else
                        <b>Dr. {{$procedimiento->doctor->nombre1 }} {{$procedimiento->doctor->nombre2 }} {{$procedimiento->doctor->apellido1 }} {{$procedimiento->doctor->apellido2 }} <br>
                        <span style="font-size: 12px;"><strong>Cédula: </strong>{{$procedimiento->doctor->id}}</span>
                    @endif
                </p>
                @php
                    $modulo = $contador_1 % 2;
                @endphp
                @if ($modulo == 0)
                @endif              
                @php
                    $contador_1++;
                @endphp
            @endif
        @endforeach
    </body>
</html>
