<!DOCTYPE html>
<html lang="en">


<head>
    <style>
        @page {
            margin-top: 60px;
            margin-left: 30px;
            margin-right: 30px;

        }

        #header {
            position: fixed;
            left: 20px;
            top: -10px;
            right: 20px;
            height: 150px;
            text-align: center;
        }

        /* #content { position: fixed; left: 20px; top: 10px; right: 20px; height: 150px; } */
        #footer {
            position: fixed;
            left: 20px;
            bottom: -90px;
            right: 20px;
            height: 150px;
        }


        table {
            width: 100%;
            border: black 2px solid;
        }



        .small {
            font-size: x-small;
            font-weight: bold;
        }

        .texto {
            font-size: 5;

        }

        .alinear {

            text-align: center !important;
        }

        .celda-titulo td {
            background-color: #5B70B6;
            width: 90px;
            font-weight: bold;
        }

        .textarea {
            width: 100%;
        }

        .alineard {

            text-align: right !important;
        }

        .altura {
            height: 20px;
        }

        .espacio {

            padding: 10px;
        }


        .altura2 {
            height: 20px;
        }

        #paginacion {
            border: 1px solid #CCC;
            background-color: #E0E0E0;
            padding: .5em;
            overflow: hidden;
        }

        .derecha {
            float: right;
        }

        .izquierda {
            float: left;
        }
    </style>

    <title>{{trans('transformularios.FORMULARIO008')}}</title>

</head>

<body>

    @php $fecha = date('Y-m-d'); @endphp

    <div>

        <table border="1" cellspacing="0" cellpadding="0" style="margin-right:25px;">
            <thead>
                <tr class="alinear">
                    <th width="10%" class="texto" scope="col">{{trans('transformularios.INSTITUCIÓNDELSISTEMA')}}</th>
                    <th width="10%" class="texto" scope="col">{{trans('transformularios.UNIDADOPERATIVA')}}</th>
                    <th width="5%" class="texto" scope="col">{{trans('transformularios.CODUO')}}</th>
                    <th width="10%" class="texto" colspan="3" scope="col">{{trans('transformularios.CODLOCALIZACIÓN')}}</th>
                    <th width="10%" class="texto" rowspan="2" scope="col">
                        <p>{{trans('transformularios.NUMERODE')}}</p>
                        <p>{{trans('transformularios.HISTORIACLINICA')}}</p>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="alinear">
                    <td rowspan="2" class="texto">@if(!is_null($empresa)) {{$empresa->nombrecomercial}} @endif</td>
                    <td rowspan="2" width="5%" class="texto"> @if(!is_null($empresa)) {{$empresa->nombrecomercial}} @endif</td>
                    <td rowspan="2" width="5%" class="texto">Mark</td>
                    <td width="2%" class="texto espacio">{{trans('transformularios.PARROQUIA')}}</td>
                    <td width="5%" class="texto">{{trans('transformularios.CANTÓN')}}</td>
                    <td width="5%" class="texto">{{trans('transformularios.PROVINCIA')}}</td>
                </tr>
                <tr class="alinear">
                    <td class="texto espacio"></td>
                    <td class="texto"></td>
                    <td class="texto">Thornton</td>
                    <td class="texto">{{$solicitudemer->id_paciente}}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 10px">

        <table border="1" cellspacing="0" cellpadding="0" style="margin-right:25px">
            <thead>
                <tr class="celda-titulo">
                    <th colspan="5" class="small"> 1 {{trans('transformularios.REGISTRODEADMISIÓN')}}</th>
                </tr>

            </thead>
            <tbody>
                <tr class="alinear">
                    <td width="10%" class="texto altura" scope="col">{{trans('transformularios.APELLIDOPATERNO')}}</td>
                    <td width="10%" class="texto altura" scope="col">{{trans('transformularios.APELLIDOMATERNO')}}</td>
                    <td width="10%" class="texto altura" scope="col">{{trans('transformularios.PRIMERNOMBRE')}}</td>
                    <td width="10%" class="texto altura" scope="col">{{trans('transformularios.SEGUNDONOMBRE')}}</td>
                    <td width="10%" class="texto altura" scope="col">{{trans('transformularios.NUMERODECÉDULA')}} </td>
                </tr>
                <tr class="alinear">
                    <td width="10%" class="texto altura2">{{$solicitudemer->paciente->apellido1}}</td>
                    <td width="10%" class="texto altura2">{{$solicitudemer->paciente->apellido2}}</td>
                    <td width="10%" class="texto altura2">{{$solicitudemer->paciente->nombre1}}</td>
                    <td width="10%" class="texto altura2">{{$solicitudemer->paciente->nombre2}}</td>
                    <td width="10%" class="texto altura2">{{$solicitudemer->id_paciente}}</td>


                </tr>

            </tbody>
        </table>

        <table border="1" cellspacing="0" cellpadding="0" style="margin-right:25px;">
            <thead>
                <tr class="alinear">
                    <th width="30%" class="texto altura2"> {{trans('transformularios.direccion')}}</th>
                    <th width="5%" class="texto altura2"> {{trans('transformularios.BARRIO')}}</th>
                    <th width="2%" class="texto altura2">{{trans('transformularios.PARROQUIA')}}</th>
                    <th width="2%" class="texto altura2"> {{trans('transformularios.CANTÓN')}}</th>
                    <th width="2%" class="texto altura2">{{trans('transformularios.PROVINCIA')}} </th>
                    <th width="2%" class="texto altura2"> {{trans('transformularios.Zona')}}</th>
                    <th width="10%" class="texto altura2">{{trans('transformularios.TELEFONO')}} </th>

                </tr>



            </thead>
            <tbody>
                <tr class="alinear">
                    <td width="10%" class="texto altura2" scope="col">{{ $solicitudemer->paciente->direccion}}</td>
                    <td width="5%" class="texto altura2" scope="col">{{ $solicitudemer->paciente->ho_datos_paciente->barrio }}</td>
                    <td width="5%" class="texto altura2" scope="col">{{ $solicitudemer->paciente->ho_datos_paciente->parroquia }} </td>
                    <td width="5%" class="texto altura2" scope="col">{{ $solicitudemer->paciente->ho_datos_paciente->canton }}</td>
                    <td width="5%" class="texto altura2" scope="col">{{ $solicitudemer->paciente->ho_datos_paciente->provincia }}</td>
                    <td width="10%" class="texto altura2" scope="col">{{ $solicitudemer->paciente->ho_datos_paciente->zona_ur }}</td>
                    <td width="10%" class="texto altura2" scope="col">{{ $solicitudemer->paciente->telefono1 }} </td>
                </tr>

            </tbody>
        </table>

    </div>
    <div>
        <table border="1" cellspacing="0" cellpadding="0" style="margin-right:25px;">
            <thead>
                <tr class="alinear">
                    <th width="10%" class="texto altura2" scope="col">{{trans('transformularios.FECHADENACIMIENTO')}}</th>
                    <th width="10%" class="texto altura2" scope="col">{{trans('transformularios.LUGARDENACIMIENTO')}}</th>
                    <th width="10%" class="texto altura2" scope="col">{{trans('transformularios.NACIONALIDAD')}}</th>
                    <th width="8%" class="texto altura2" scope="col">{{trans('transformularios.GRUPOCULTURAL')}}</th>
                    <th width="10%" class="texto altura2" scope="col">{{trans('transformularios.EDADENAÑOSCUMPLIDOS')}}</th>
                    <th width="10%" colspan="2" class="texto altura2"> {{trans('transformularios.SEXO')}}</th>
                    <th width="10%" colspan="5" class="texto altura2">{{trans('transformularios.ESTADOCIVIL')}} </th>
                    <th width="10%" rowspan="2" class="texto altura2"> {{trans('transformularios.INSTRUCCIONULTIMOAÑOAPROBADO')}}</th>
                </tr>
            </thead>
            <tbody>
                <tr class="alinear">
                    <td rowspan="2" class="texto altura2" scope="row">{{ $solicitudemer->paciente->fecha_nacimiento }}</td>
                    <td rowspan="2" width="5%" class="texto altura2">{{ $solicitudemer->paciente->ho_datos_paciente->nacionalidad }}</td>
                    <td rowspan="2" width="5%" class="texto altura2">{{trans('transformularios.NAC')}}</td>
                    <td rowspan="2" width="5%" class="texto altura2">{{ $solicitudemer->paciente->ho_datos_paciente->grupo_cultural }}</td>
                    <td rowspan="2" width="2%" class="texto altura2"><span id="edad">{{ $solicitudemer->paciente->ho_datos_paciente->edad }} </span></td>
                    <td width="5%" class="texto altura2">M</td>
                    <td width="5%" class="texto altura2">F</td>
                    <td width="5%" class="texto altura2">SOL</td>
                    <td width="5%" class="texto altura2">CAS</td>
                    <td width="5%" class="texto altura2">DIV</td>
                    <td width="5%" class="texto altura2">VIU</td>
                    <td width="5%" class="texto altura2">U-L</td>


                </tr>
                <tr class="alinear">
                    <td class="texto altura2">@if($solicitudemer->paciente->sexo =='1') X @else @endif</td>
                    <td class="texto altura2">@if($solicitudemer->paciente->sexo =='2') X @else @endif</td>
                    <td class="texto altura2"> @if($solicitudemer->paciente->estadocivil == 1)<span> X</span>
                        @elseif($solicitudemer->paciente->estadocivil == 2)<span> </span>
                        @elseif($solicitudemer->paciente->estadocivil == 3)<span> </span>
                        @elseif($solicitudemer->paciente->estadocivil == 4)<span> </span>
                        @elseif($solicitudemer->paciente->estadocivil == 5)<span> </span>
                        @endif</td>
                    <td class="texto altura2">@if($solicitudemer->paciente->estadocivil == 1)<span> </span>
                        @elseif($solicitudemer->paciente->estadocivil == 2)<span> X</span>
                        @elseif($solicitudemer->paciente->estadocivil == 3)<span> </span>
                        @elseif($solicitudemer->paciente->estadocivil == 4)<span> </span>
                        @elseif($solicitudemer->paciente->estadocivil == 5)<span> </span>
                        @endif</td>
                    <td class="texto altura2">@if($solicitudemer->paciente->estadocivil == 1)<span> </span>
                        @elseif($solicitudemer->paciente->estadocivil == 2)<span> </span>
                        @elseif($solicitudemer->paciente->estadocivil == 3)<span> X</span>
                        @elseif($solicitudemer->paciente->estadocivil == 4)<span> </span>
                        @elseif($solicitudemer->paciente->estadocivil == 5)<span> </span>
                        @endif</td>
                    <td class="texto altura2">@if($solicitudemer->paciente->estadocivil == 1)<span> </span>
                        @elseif($solicitudemer->paciente->estadocivil == 2)<span> </span>
                        @elseif($solicitudemer->paciente->estadocivil == 3)<span> </span>
                        @elseif($solicitudemer->paciente->estadocivil == 4)<span>X </span>
                        @elseif($solicitudemer->paciente->estadocivil == 5)<span> </span>
                        @endif</td>
                    <td class="texto altura2">@if($solicitudemer->paciente->estadocivil == 1)<span> </span>
                        @elseif($solicitudemer->paciente->estadocivil == 2)<span> </span>
                        @elseif($solicitudemer->paciente->estadocivil == 3)<span> </span>
                        @elseif($solicitudemer->paciente->estadocivil == 4)<span> </span>
                        @elseif($solicitudemer->paciente->estadocivil == 5)<span>X </span>
                        @endif</td>
                    <td class="texto altura2">{{ $solicitudemer->paciente->ho_datos_paciente->instruccion }} </td>


                </tr>
            </tbody>
        </table>

    </div>
    <div>
        <table border="1" cellspacing="0" cellpadding="0" style="margin-right:25px;">
            <thead>
                <tr class="alinear">
                    <th width="10%" class="texto altura2"> {{trans('transformularios.FECHADEADMISION')}}</th>
                    <th width="10%" class="texto altura2"> {{trans('transformularios.OCUPCION')}}</th>
                    <th width="10%" class="texto altura2">{{trans('transformularios.EMPRESADONDETRABAJA')}} </th>
                    <th width="10%" class="texto altura2">{{trans('transformularios.TIPODESEGURODESALUD')}} </th>
                    <th width="10%" class="texto altura2">{{trans('transformularios.REFERIDODE')}} </th>

                </tr>

            </thead>
            <tbody>
                <tr class="alinear">
                    <td width="10%" class="texto altura2" scope="col">@if($solicitudemer->fecha_ingreso==null){{$fecha}}@else{{$solicitudemer->fecha_ingreso}}@endif</td>
                    <td widd="5%" class="texto altura2" scope="col">{{ $solicitudemer->paciente->ocupacion }}</td>
                    <td width="5%" class="texto altura2" scope="col">{{ $solicitudemer->paciente->ho_datos_paciente->empresa_trabajo }} </td>
                    <td width="5%" class="texto altura2" scope="col">@if($solicitudemer->id_seguro != null) {{ $solicitudemer->seguro->nombre}} @endif</td>
                    <td width="10%" class="texto altura2" scope="col">{{ $solicitudemer->paciente->referido }} </td>

                </tr>

            </tbody>
        </table>

    </div>
    <div>
        <table border="1" cellspacing="0" cellpadding="0" style="margin-right:25px;">
            <thead>
                <tr class="alinear">
                    <th width="10%" class="texto altura2"> {{trans('transformularios.ENCASONECESARIOAVISARA')}}</th>
                    <th width="10%" class="texto altura2">{{trans('transformularios.PARENTESCOAFINIDAD')}} </th>
                    <th width="10%" class="texto altura2">{{trans('transformularios.direccion')}} </th>
                    <th width="10%" class="texto altura2">{{trans('transformularios.TELEFONO')}}</th>


                </tr>

            </thead>
            <tbody>
                <tr class="alinear">
                    <td width="10%" class="texto altura2" scope="col">{{ $solicitudemer->paciente->ho_datos_paciente->llamar_a }} </td>
                    <td widd="5%" class="texto altura2" scope="col">{{trans('transformularios.APELLIDOMATERNO')}}</td>
                    <td width="5%" class="texto altura2" scope="col">{{ $solicitudemer->paciente->ho_datos_paciente->direccion_familiar }}</td>
                    <td width="5%" class="texto altura2" scope="col">{{ $solicitudemer->paciente->ho_datos_paciente->telefono_inst_per_paci }}</td>


                </tr>

            </tbody>
        </table>
        <table border="1" cellspacing="0" cellpadding="0" style="margin-right:25px;">
            <thead>
                <tr class="alinear">
                    <th colspan="6" width="10%" class="texto altura2"> {{trans('transformularios.FORMADELLEGADA')}}</th>
                    <th width="10%" class="texto altura2">{{trans('transformularios.FUENTEDEINFORMACION')}} </th>
                    <th width="20%" class="texto altura2">{{trans('transformularios.INSTITUCIÓNOPERSONAQUEENTREGAALPACIENTE')}} </th>
                    <th width="10%" class="texto altura2">{{trans('transformularios.TELEFONO')}}</th>


                </tr>

            </thead>
            <tbody>

                <tr class="alinear">
                    <td width="10%" class="texto altura2" scope="col">{{trans('transformularios.AMBULATORIO')}}</td>
                    <td widd="10%" class="texto altura2" scope="col">@if($solicitudemer->paciente->ho_datos_paciente->forma_llegada == "Ambulatorio")<span>X </span>
                        @elseif($solicitudemer->paciente->ho_datos_paciente->forma_llegada == "Ambulancia")<span> </span>
                        @elseif($solicitudemer->paciente->ho_datos_paciente->forma_llegada == "Otro")<span> </span>
                        @endif</td>
                    <td width="10%" class="texto altura2" scope="col">AMBULANCIA</td>
                    <td width="5%" class="texto altura2" scope="col">@if($solicitudemer->paciente->ho_datos_paciente->forma_llegada == "Ambulatorio")<span> </span>
                        @elseif($solicitudemer->paciente->ho_datos_paciente->forma_llegada == "Ambulancia")<span>X </span>
                        @elseif($solicitudemer->paciente->ho_datos_paciente->forma_llegada == "Otro")<span> </span>
                        @endif</td>
                    <td width="10%" class="texto altura2" scope="col">OTRO TRANSPORTE</td>
                    <td width="5%" class="texto altura2" scope="col">@if($solicitudemer->paciente->ho_datos_paciente->forma_llegada == "Ambulatorio")<span> </span>
                        @elseif($solicitudemer->paciente->ho_datos_paciente->forma_llegada == "Ambulancia")<span> </span>
                        @elseif($solicitudemer->paciente->ho_datos_paciente->forma_llegada == "Otro")<span> X </span>
                        @endif</td>
                    <td width="5%" class="texto altura2" scope="col"></td>
                    <td width="5%" class="texto altura2" scope="col"></td>
                    <td width="5%" class="texto altura2" scope="col">{{ $solicitudemer->paciente->telefono3 }}</td>



                </tr>

            </tbody>
        </table>
    </div>
    <div>
        <div style="margin-top: 10px">

            <table border="1" cellspacing="0" cellpadding="0" style="margin-right:25px;">
                <thead>
                    <tr>
                        <th colspan="12" class="small"> 2 {{trans('transformularios.INICIODEATENCIONYMOTIVO')}}</th>
                    </tr>

                </thead>

                <tbody>
                    <tr class="alinear">
                        @php $paciente = $solicitudemer->paciente; @endphp
                        @php $form008 = $solicitudemer->form008->first(); @endphp
                        <td width="5%" class="texto altura2" scope="col">HORA</td>
                        <td width="20%" class="texto altura2" scope="col">@if($solicitudemer->fecha_ingreso==null){{$fecha}}@else{{$solicitudemer->fecha_ingreso}}@endif </td>
                        <td width="5%" class="texto altura2" scope="col">TRAUMA</td>
                        <td width="5%" class="texto altura2" scope="col">@if($form008->trauma=='1') X @else @endif</td>
                        <td width="10%" class="texto altura2" scope="col">CAUSA CLÍNICA </td>
                        <td width="5%" class="texto altura2" scope="col">@if($form008->c_clinica=='1') X @else @endif</td>
                        <td width="15%" class="texto altura2" scope="col">CAUSA G. OBSTETRICA</td>
                        <td width="5%" class="texto altura2" scope="col">@if($form008->c_obstetrica=='1') X @else @endif</td>
                        <td width="15%" class="texto altura2" scope="col">CAUSA QUIRURGICA</td>
                        <td width="5%" class="texto altura2" scope="col">@if($form008->c_quirurgica=='1') X @else @endif</td>
                        <td rowspan="2" width="10%" class="texto altura2" scope="col">GRUPO SANGUINEO Y FACTOR Rh</td>
                        <td rowspan="2" width="10%" class="texto altura2" scope="col"> @if($paciente->gruposanguineo=='O+' )O+ @endif
                            @if($paciente->gruposanguineo=='O-') O- @else @endif
                            @if($paciente->gruposanguineo=='A+') A+ @else @endif
                            @if($paciente->gruposanguineo=='A-') A- @else @endif
                            @if($paciente->gruposanguineo=='AB+') AB+ @else @endif
                            @if($paciente->gruposanguineo=='AB-') AB- @else @endif
                        </td>
                    </tr>
                    <tr class="alinear">
                        <td width="15%" class="texto altura2">{{trans('transformularios.NOTIFICACIONALAPOLICIA')}}</td>
                        <td width="5%" class="texto altura2">@if($form008->n_policia=='1') X @else @endif</td>
                        <td width="10%" class="texto altura2">{{trans('transformularios.OTROMOTIVO')}}</td>
                        <td width="5%" class="texto altura2">@if($form008->o_motivo) X @else @endif</td>
                        <td colspan="6" width="10%" class="texto altura2">{{$form008->motivo}}</td>


                    </tr>

                </tbody>
            </table>
        </div>

        <div style="margin-top: 10px">

            <table border="1" cellspacing="0" cellpadding="0" style="margin-right:25px;">
                <thead>
                    <tr>
                        <th colspan="12" class="small"> 3 {{trans('transformularios.ACCIDENTE')}}</th>
                    </tr>

                </thead>
                <tbody>
                    <tr class="alinear">
                        <td width="15%" class="texto altura2" scope="col">{{trans('transformularios.FECHAYHORA')}}</td>
                        <td width="10%" class="texto altura2" scope="col">@if($form008->fecha_evento == null) {{$fecha}} @else {{$form008->fecha_evento}} @endif</td>
                        <td width="10%" class="texto altura2" scope="col">{{trans('transformularios.LUGARDELEVENTO')}}</td>
                        <td width="10%" class="texto altura2" scope="col">{{$form008->lugar_evento}}</td>
                        <td width="10%" class="texto altura2" scope="col">{{trans('transformularios.DIRECCIONDELEVENTO')}} </td>
                        <td colspan="5" width="50%" class="texto altura2" scope="col">{{$form008->direccion_evento}}</td>
                        <td width="10%" class="texto altura2" scope="col">{{trans('transformularios.CUSTODIAPOLICIAL')}}</td>
                        <td width="5%" class="texto altura2" scope="col">@if($form008->custodia_policial=='1') X @endif</td>

                    </tr>

                </tbody>
            </table>
            <table border="1" cellspacing="0" cellpadding="0" style="margin-right:25px; margin-top: -1.6
            px">
                <thead>
                    <tr class="alinear">
                        <td width="8%" class="texto altura2">{{trans('transformularios.ACCIDENTEDETRANSITO')}}</td>
                        <td width="2%" class="texto altura2">@if($form008->accidente_transito=='1') X @endif</td>
                        <td width="8%" class="texto altura2">{{trans('transformularios.CAIDA')}}</td>
                        <td width="2%" class="texto altura2">@if($form008->caida=='1') X @endif</td>
                        <td width="8%" class="texto altura2">{{trans('transformularios.QUEMADURA')}}</td>
                        <td width="2%" class="texto altura2">@if($form008->quemadura=='1') X @endif</td>
                        <td width="8%" class="texto altura2">{{trans('transformularios.MORDEDURA')}}</td>
                        <td width="2%" class="texto altura2">@if($form008->mordedura=='1') X @endif</td>
                        <td width="8%" class="texto altura2">{{trans('transformularios.AHOGAMIENTO')}}</td>
                        <td width="2%" class="texto altura2"> @if($form008->ahogamiento=='1') X @endif</td>
                        <td width="8%" class="texto altura2">{{trans('transformularios.CUERPOEXTRAÑO')}}</td>
                        <td width="2%" class="texto altura2"> @if($form008->violencia_armcp=='1') X @endif</td>
                        <td width="8%" class="texto altura2">{{trans('transformularios.APLASTAMIENTO')}}</td>
                        <td width="2%" class="texto altura2">5656</td>
                        <td width="8%" class="texto altura2">{{trans('transformularios.OTROACCIDENTE')}}</td>
                        <td width="2%" class="texto altura2">5656</td>
                    </tr>

                </thead>
                <tbody>
                    <tr class="alinear">
                        <td width="8%" class="texto altura2">{{trans('transformularios.ARMADEFUEGO')}}</td>
                        <td width="2%" class="texto altura2">@if($form008->violencia_armf=='1') X @endif</td>
                        <td width="10%" class="texto altura2">{{trans('transformularios.PUNZANTE')}}</td>
                        <td width="2%" class="texto altura2">@if($form008->violencia_armcp=='1') X @endif</td>
                        <td width="8%" class="texto altura2">{{trans('transformularios.RIÑA')}}</td>
                        <td width="2%" class="texto altura2">@if($form008->violencia_rina=='1') X @endif</td>
                        <td width="8%" class="texto altura2">{{trans('transformularios.VIOLENCIAFAMILIAR')}}</td>
                        <td width="2%" class="texto altura2">@if($form008->violencia_familiar=='1') X @endif</td>
                        <td width="8%" class="texto altura2">{{trans('transformularios.ABUSOFISICO')}}</td>
                        <td width="2%" class="texto altura2">@if($form008->abuso_fisico=='1') X @endif</td>
                        <td width="8%" class="texto altura2">{{trans('transformularios.ABUSOPSICOLOGICO')}}</td>
                        <td width="2%" class="texto altura2">@if($form008->abuso_psicologico=='1') X @endif</td>
                        <td width="8%" class="texto altura2">{{trans('transformularios.ABUSOSEXUAL')}}</td>
                        <td width="2%" class="texto altura2">@if($form008->abuso_sexual=='1') X @endif</td>
                        <td width="8%" class="texto altura2">{{trans('transformularios.OTRAVIOLENCIA')}}</td>
                        <td width="2%" class="texto altura2">@if($form008->intoxicacion_alcoholica=='1') X @endif</td>
                    </tr>
                    <tr class="alinear">
                        <td width="8%" class="texto altura2">{{trans('transformularios.INTOXICACIONALCOHÓLICA')}}</td>
                        <td width="2%" class="texto altura2">@if($form008->intoxicacion_alcoholica=='1') X @endif</td>
                        <td width="8%" class="texto altura2">{{trans('transformularios.INTOXICACIONALIMENTARIA')}}</td>
                        <td width="2%" class="texto altura2">@if($form008->intoxicacion_alimentaria=='1') X @endif</td>
                        <td width="8%" class="texto altura2">{{trans('transformularios.INTOXICACIONXDROGAS')}}</td>
                        <td width="2%" class="texto altura2">@if($form008->intoxicacion_drogas=='1') X @endif</td>
                        <td width="8%" class="texto altura2">{{trans('transformularios.INHALACIONDEGASES')}}</td>
                        <td width="2%" class="texto altura2">@if($form008->intoxicacion_gases=='1') X @endif</td>
                        <td width="8%" class="texto altura2">{{trans('transformularios.OTRAINTOXICACION')}}</td>
                        <td width="2%" class="texto altura2">@if($form008->violencia_armf=='1') X @endif</td>
                        <td width="8%" class="texto altura2">{{trans('transformularios.ENVENAMIENTO')}}</td>
                        <td width="2%" class="texto altura2">@if($form008->envenenamiento=='1') X @endif</td>
                        <td width="8%" class="texto altura2">{{trans('transformularios.PICADURA')}}</td>
                        <td width="2%" class="texto altura2">@if($form008->picadura=='1') X @endif</td>
                        <td width="8%" class="texto altura2">{{trans('transformularios.ANAFILAXIA')}}</td>
                        <td width="2%" class="texto altura2">@if($form008->anafilaxia=='1') X @endif</td>
                    </tr>
                </tbody>
            </table>
            <table border="1" cellspacing="0" cellpadding="0" style="margin-right:25px;">
                <tr class="alinear">
                    <td width="10%" class="texto altura2">{{trans('transformularios.OBSERVACIONES')}}</td>
                    <td width="60%" class="texto altura2"><textarea float="right" name="observacion_p3" id="observacion_p3" cols="30" rows="10">
                        {{$form008->observacion_p3}}
                        </textarea>
                    </td>
                    <td width="10%" class="texto altura2">{{trans('transformularios.ALIENTOETILICO')}}</td>
                    <td width="5%" class="texto altura2">@if($form008->aliento_etilico=='1') X @endif</td>
                    <td width="10%" class="texto altura2">{{trans('transformularios.VALORALCOCHECK')}}</td>
                    <td width="5%" class="texto altura2">{{$form008->valor_alcocheck}}</td>
                </tr>
            </table>
        </div>


        <div style="margin-top: 10px">
            @php
            $datos_pac = $solicitudemer->paciente->ho_datos_paciente;
            $paciente = $solicitudemer->paciente;
            @endphp
            <table border="1" cellspacing="0" cellpadding="0" style="margin-right:25px;">
                <thead>
                    <tr>
                        <th colspan="10" class="small"> 4 {{trans('transformularios.ANTECEDENTES')}}</th>
                        <th style='text-align:right' width="5%" colspan="6" class="texto altura2">{{trans('transformularios.DESCRIBIR')}}</th>

                    </tr>

                </thead>
                <tbody>
                    <tr class="alinear">
                        <td width="15%" class="texto altura2" scope="col">1. {{trans('transformularios.ALERGICO')}}</td>
                        <td width="5%" class="texto altura2" scope="col">@if (!is_null($paciente->alergias)) X @endif</td>
                        <td width="10%" class="texto altura2" scope="col">2. {{trans('transformularios.CLINICO')}}</td>
                        <td width="5%" class="texto altura2" scope="col">@if (!is_null($datos_pac->clinico)) X @endif</td>
                        <td width="15%" class="texto altura2" scope="col">3. {{trans('transformularios.GINECOLOGICO')}}</td>
                        <td width="5%" class="texto altura2" scope="col">@if(!is_null($datos_pac->ginecologico)) X @endif</td>
                        <td width="15%" class="texto altura2" scope="col">4. {{trans('transformularios.TRAUMATOG')}}</td>
                        <td width="5%" class="texto altura2" scope="col"> @if(!is_null($datos_pac->traumatologico)) X @endif</td>
                        <td width="15%" class="texto altura2" scope="col">5. {{trans('transformularios.QUIRURGICO')}}</td>
                        <td width="5%" class="texto altura2" scope="col">@if (!is_null($paciente->antecedentes_quir)) X @endif</td>
                        <td width="15%" class="texto altura2" scope="col">6. {{trans('transformularios.FARMACOLOG')}}</td>
                        <td width="5%" class="texto altura2" scope="col">@if(!is_null($datos_pac->farmacologico)) X @endif</td>
                        <td width="15%" class="texto altura2" scope="col">7. {{trans('transformularios.PSIQUIATRICO')}}</td>
                        <td width="5%" class="texto altura2" scope="col">@if( !is_null($datos_pac->psiquiatrico)) X @endif</td>
                        <td width="10%" class="texto altura2" scope="col">8. {{trans('transformularios.OTRO')}}</td>
                        <td width="5%" class="texto altura2" scope="col">89865</td>

                    </tr>

                    <tr class="alinear">
                        <td colspan="16" width="10%" class="texto altura2">
                            <textarea float="right" name="" id="" cols="30" rows="10">
                            {{$form008->observacion_p3}}
                            </textarea>

                        </td>
                    </tr>


                </tbody>
            </table>
        </div>

        <div style="margin-top: 10px">

            <table border="1" cellspacing="0" cellpadding="0" style="margin-right:25px;">
                <thead>
                    <tr>
                        <th colspan="8" class="small"> 5 {{trans('transformularios.ENFERMEDADACTUAL')}}</th>
                        <th style='text-align:right' width="5%" colspan="8" class="texto altura2">{{trans('transformularios.DESCRIBIRC')}}</th>

                    </tr>

                </thead>
                <tbody>
                    <tr class="alinear">
                        <td width="5%" class="texto altura2" scope="col">{{trans('transformularios.VIAAEREALIBRE')}}</td>
                        <td widd="10%" class="texto altura2" scope="col">{{$form008->aerea_libre}}</td>
                        <td width="5%" class="texto altura2" scope="col">{{trans('transformularios.VIAAEREAOBSTRUIDA')}}</td>
                        <td width="10%" class="texto altura2" scope="col">{{$form008->aerea_obstruida}}</td>
                        <td width="10%" class="texto altura2" scope="col">{{trans('transformularios.CONDICIONESTABLE')}}</td>
                        <td colspan="5" width="10%" class="texto altura2" scope="col">{{$form008->condicion_estable}}</td>
                        <td width="10%" class="texto altura2" scope="col">{{trans('transformularios.CONDICIONINESTABLE')}}</td>
                        <td colspan="5" width="10%" class="texto altura2" scope="col">{{$form008->condicion_inestable}}</td>



                    </tr>

                    <tr class="alinear">
                        <td colspan="16" width="10%" class="texto altura2">
                            <textarea float="right" name="" id="" cols="30" rows="10">
                            {{$form008->observacion_quintop}}
                            </textarea>

                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div style="page-break-after:always;"></div>
    <div style="margin-top: 10px">
        @php
        $form008 = $solicitudemer->form008->first();
        if($form008 != null) {
        $hc = $form008->agenda->historia_clinica;
        } else {
        $hc = null;
        }
        $triaje = $solicitudemer->manchester->first();
        @endphp
        <table border="1" cellspacing="0" cellpadding="0" style="margin-right:25px;">
            <thead>
                <tr>
                    <th colspan="18" class="small"> 6 {{trans('transformularios.SIGNOSVITALES')}}</th>


                </tr>

            </thead>
            <tbody>

                <tr class="alinear">
                    <td width="10%" class="texto altura2">{{trans('transformularios.PRESIONARTERIAL')}}</td>
                    <td colspan="5" width="10%" class="texto altura2"> @if($triaje != null ) {{$triaje->presion_sistolica}} / {{$triaje->presion_diastolica}} @endif</td>
                    <td width="12%" class="texto altura2" scope="col">{{trans('transformularios.FCARDIACA')}}</td>
                    <td width="10%" class="texto altura2" scope="col">@if($triaje != null) {{$triaje->frec_cardiaca}} @endif</td>
                    <td width="12%" class="texto altura2" scope="col">{{trans('transformularios.FRESPIRAT')}}</td>
                    <td width="10%" class="texto altura2" scope="col">@if($form008->frec_respiratoria != null) {{$form008->frec_respiratoria}} @elseif($triaje != null) {{$triaje->frec_resp}} @endif</td>
                    <td width="12%" class="texto altura2" scope="col">{{trans('transformularios.TEMPB')}}</td>
                    <td width="10%" class="texto altura2" scope="col">@if($form008->temp_bucal != null) {{$form008->temp_bucal}} @endif</td>
                    <td width="13%" class="texto altura2" scope="col">{{trans('transformularios.TEMPA')}}</td>
                    <td width="10%" class="texto altura2" scope="col">@if($form008->temp_axilar != null) {{$form008->temp_axilar}} @endif</td>
                    <td width="5%" class="texto altura2" scope="col">{{trans('transformularios.PESOKg')}}</td>
                    <td width="10%" class="texto altura2" scope="col">@if($hc->peso == null)  @else {{$hc->peso}} @endif</td>
                    <td width="15%" class="texto altura2" scope="col">{{trans('transformularios.TALLAm')}}</td>
                    <td width="10%" class="texto altura2" scope="col">@if($triaje != null) {{$triaje->talla}} @elseif($hc != null) {{ $hc->altura}} @endif</td>

                </tr>

            </tbody>
        </table>
    </div>
    <div style="margin-top: 0px">
        <table border="1" cellspacing="0" cellpadding="0" style="margin-right:25px;">
            <tbody>
                <tr class="alinear">
                    <td colspan="2" width="10%" class="texto altura2" scope="col">{{trans('transformularios.GLASGOW')}}</td>
                    <td width="5%" class="texto altura2" scope="col">{{trans('transformularios.OCULAR4')}}</td>
                    <td width="5%" class="texto altura2" scope="col">{{$form008->ocular}}</td>
                    <td width="5%" class="texto altura2" scope="col">{{trans('transformularios.VERBAL5')}}</td>
                    <td width="5%" class="texto altura2" scope="col">{{$form008->verbal}}</td>
                    <td width="5%" class="texto altura2" scope="col">{{trans('transformularios.MOTORA6')}}</td>
                    <td width="5%" class="texto altura2" scope="col">{{$form008->motora}}</td>
                    <td width="5%" class="texto altura2" scope="col">{{trans('transformularios.TOTAL15')}}</td>
                    <td width="5%" class="texto altura2" scope="col">{{$form008->total_glas}}</td>
                    <td width="10%" class="texto altura2" scope="col">{{trans('transformularios.REACCIONPUPILADER')}}</td>
                    <td width="5%" class="texto altura2" scope="col">{{$form008->reac_pupila_der}}</td>
                    <td width="10%" class="texto altura2" scope="col">{{trans('transformularios.REACCIONPUPILAIZQ')}}</td>
                    <td width="5%" class="texto altura2" scope="col">{{$form008->reac_pupila_izq}}</td>
                    <td width="10%" class="texto altura2" scope="col">{{trans('transformularios.TLLENADOCAPILAR')}}</td>
                    <td width="5%" class="texto altura2" scope="col">@if($triaje != null) {{$triaje->llenado_capilar}} @elseif($form008 != null) {{$form008->t_llenado_capilar}} @endif</td>
                    <td width="10%" class="texto altura2" scope="col">{{trans('transformularios.SATURAOXIGENO')}}</td>
                    <td width="5%" class="texto altura2" scope="col">@if($triaje != null) {{$triaje->sat_oxigeno}} @elseif($form008 != null) {{$form008->satura_oxigeno}} @endif</td>

                </tr>
            </tbody>
        </table>
    </div>
    <div style="margin-top: 10px">
        <table border="1" cellspacing="0" cellpadding="0" style="margin-right:25px;">
            <thead>
                <tr>
                    <th colspan="10" class="small"> 7 {{trans('transformularios.EXAMENFISICOYDIAGNOSTICO')}}</th>
                    <th style='text-align:right' width="5%" colspan="8" class="texto altura2 ">{{trans('transformularios.MARCARSP')}}</th>
                </tr>
            </thead>
            <tbody>
                <tr class="alinear">
                    <td width="15%" class="texto altura2" scope="col">1. {{trans('transformularios.VIAAEREAOBSTRUIDA')}}</td>
                    <td widd="10%" class="texto altura2" scope="col">{{$form008->via_aerea_obs}}</td>
                    <td colspan="2" width="10%" class="texto altura2" scope="col">2. {{trans('transformularios.CABEZA')}}</td>
                    <td width="10%" class="texto altura2" scope="col">{{$form008->cabeza}}</td>
                    <td colspan="2" width="10%" class="texto altura2" scope="col">3. {{trans('transformularios.CUELLO')}}</td>
                    <td width="10%" class="texto altura2" scope="col">{{$form008->cuello}}</td>
                    <td width="10%" class="texto altura2" scope="col">4. {{trans('transformularios.TORAX')}} </td>
                    <td width="10%" class="texto altura2" scope="col">{{$form008->torax}}</td>
                    <td width="12%" class="texto altura2" scope="col">5. {{trans('transformularios.ABDOMEN')}}</td>
                    <td width="10%" class="texto altura2" scope="col">{{$form008->abdomen}}</td>
                    <td width="12%" class="texto altura2" scope="col">6. {{trans('transformularios.COLUMNA')}}</td>
                    <td width="10%" class="texto altura2" scope="col">{{$form008->columna}}</td>
                    <td width="10%" class="texto altura2" scope="col">7. {{trans('transformularios.PELVIS')}}</td>
                    <td width="10%" class="texto altura2" scope="col">{{$form008->pelvis}}</td>
                    <td width="15%" class="texto altura2" scope="col">8. {{trans('transformularios.EXTREMIDADES')}}</td>
                    <td width="10%" class="texto altura2" scope="col">{{$form008->extremidades}}</td>

                </tr>

                <tr class="alinear">
                    <td width="60%" colspan="18" class="texto altura2"><textarea float="right" name="observacion_p3" id="observacion_p3" cols="30" rows="10">
                        {{$form008->observacion_p3}}
                        </textarea>
                    </td>

                </tr>

            </tbody>
        </table>
    </div>
    <div style="width: 100%; display:block;padding-top: 60px; margin-bottom: -45px;">
        <style type="text/css">
            .izq {
                text-align: right;
                border: none;
            }

            .border_none {
                border: none;
            }

            .border_none tr td {
                border-bottom: solid 1px;
                border-right: none;
                border-top: none;
            }

            .tp2 td {
                padding: 3.5px 0px 3px 0px;
            }

            .doble_td {
                padding: 0px 0px 0px 0px;
                font-size: 4.5px;
            }

            .doble {
                font-size: 6px;
            }
        </style>
        <div style="width:425px; display: inline-block; ">
            <table border="1">
                <thead style="border-bottom: solid 1px;">
                    <tr>
                        <th colspan="1" class="small border_none"> 8 {{trans('transformularios.LOCALIZACIONDELESIONES')}}</th>
                        <th style='text-align:right' colspan="4" class="texto altura2 border_none">{{trans('transformularios.ESCRIBIR')}}</th>
                    </tr>
                </thead>
                <tbody class="texto border_none">
                    <tr>
                        <td rowspan="15" class="border_none">
                            @php
                            $imagen= \Sis_medico\Ho_Lesiones008::where('id_008',$solicitudemer->id)->first();
                            @endphp
                            @if(is_null($imagen))
                            <img src="{{asset('body.png')}}" width="200" height="200" srcset="">
                            @else
                            <img src='{{base_path().'/storage/app/hc_ima/'.$imagen->url_imagen}}' width="200" height="200" srcset="">

                            @endif
                        </td>
                        <td style="background-color: red;"></td>
                        <td class="izq">1</td>
                        <td>{{trans('transformularios.HERIDAPENETRANTE')}}</td>
                        <td>hiuil</td>
                    </tr>
                    <tr>
                        <td style="background-color:blue;"></td>
                        <td class="izq">2</td>
                        <td>{{trans('transformularios.HERIDACORTANE')}}</td>
                        <td>GEG</td>
                    </tr>
                    <tr>
                        <td style="background-color:yellow;"></td>
                        <td class="izq">3</td>
                        <td>{{trans('transformularios.FRACTURAEXPUESTA')}}</td>
                        <td>GEG</td>
                    </tr>
                    <tr>
                        <td style="background-color:black;"></td>
                        <td class="izq">4</td>
                        <td>{{trans('transformularios.FRACTURACERRADA')}}</td>
                        <td>GEG</td>
                    </tr>
                    <tr>
                        <td style="background-color:#C5C526;"></td>
                        <td class="izq">5</td>
                        <td>{{trans('transformularios.CUERPOEXTRAÑO')}}</td>
                        <td>GEG</td>
                    </tr>
                    <tr>
                        <td style="background-color:#008080;"></td>
                        <td class="izq">6</td>
                        <td>{{trans('transformularios.HEMORRAGIA')}}</td>
                        <td>GEG</td>
                    </tr>
                    <tr>
                        <td style="background-color:#FF00FF;"></td>
                        <td class="izq">7</td>
                        <td>{{trans('transformularios.MORDEDURA')}}</td>
                        <td>GEG</td>
                    </tr>
                    <tr>
                        <td style="background-color:#FBBBA3"></td>
                        <td class="izq">8</td>
                        <td>{{trans('transformularios.PICADURA')}}</td>
                        <td>GEG</td>
                    </tr>
                    <tr>
                        <td style="background-color:#B8FFB8"></td>
                        <td class="izq">9</td>
                        <td>{{trans('transformularios.EXCORIACION')}}</td>
                        <td>GEG</td>
                    </tr>
                    <tr>
                        <td style="background-color:#FFFFE0"></td>
                        <td class="izq">10</td>
                        <td>{{trans('transformularios.DEFORMIDADOMASA')}}</td>
                        <td>GEG</td>
                    </tr>
                    <tr>
                        <td style="background-color:#00FF00"></td>
                        <td class="izq">11</td>
                        <td>{{trans('transformularios.HEMATOMA')}}</td>
                        <td>GEG</td>
                    </tr>
                    <tr>
                        <td style="background-color:#E5CCFB"></td>
                        <td class="izq">12</td>
                        <td>{{trans('transformularios.ERITEMA/INFLAMACON')}}</td>
                        <td>GEG</td>
                    </tr>
                    <tr>
                        <td style="background-color:#FFDEAD"></td>
                        <td class="izq">13</td>
                        <td>{{trans('transformularios.LUXACION/ESGUINCE')}}</td>
                        <td>GEG</td>
                    </tr>
                    <tr>
                        <td style="background-color:#EC8352"></td>
                        <td class="izq">14</td>
                        <td>{{trans('transformularios.QUEMADURA')}}</td>
                        <td>GEG</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="width:5px; display: inline-block;"></div>

        <div style="width:293px; display: inline-block;">
            <table border="1">
                <thead style="border-bottom: solid 1px;">
                    <tr>
                        <th colspan="11" class="small border_none">9 {{trans('transformularios.EMERGENCIAOBSTETRICA')}}</th>
                        <th class="texto altura2 border_none" style="color: white;">a a</th>
                    </tr>
                </thead>
                <tbody class="texto border_none tp2 doble">
                    <tr>
                        <td>{{trans('transformularios.GESTAS')}}</td>
                        <td colspan="2">{{$form008->gestas}} </td>
                        <td colspan="2">{{trans('transformularios.PARTOS')}}</td>
                        <td>{{$form008->partos}} </td>
                        <td colspan="2">{{trans('transformularios.ABORTOS')}}</td>
                        <td>{{$form008->abortos}} </td>
                        <td colspan="2">{{trans('transformularios.CESAREAS')}}</td>
                        <td><span style="color:white;">X</span>{{$form008->cesareas}}<span style="color:white;">X</span></td>
                    </tr>
                    <tr>
                        <td class="doble_td" colspan="3">{{trans('transformularios.FECHAULTIMAMENSTRUACION')}}</td>
                        <td colspan="3">{{$form008->ultima_menstruacion}} </td>
                        <td class="doble_td" colspan="2">{{trans('transformularios.SEMANAS')}} <br>{{trans('transformularios.GESTACION')}}</td>
                        <td>{{$form008->semanas_gestacion}} </td>
                        <td class="doble_td" colspan="2">{{trans('transformularios.MOVIMIENTOFETAL')}}</td>
                        <td>{{$form008->frecuencia_fetal}} </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="doble_td">{{trans('transformularios.FRECUENCIACFETAL')}}</td>
                        <td colspan="3"> @if($form008->movimiento_fetal=='1') X @endif</td>
                        <td colspan="2" class="doble_td">{{trans('transformularios.MEMBRANAS')}} <br>{{trans('transformularios.ROTAS')}}</td>
                        <td>@if($form008->membranas_rotas=='1') X @endif</td>
                        <td colspan="2">{{trans('transformularios.TIEMPO')}}</td>
                        <td colspan="2">{{$form008->tiempo_ruptura}}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="doble_td">{{trans('transformularios.ALTURA')}} <br>{{trans('transformularios.UTERINA')}}</td>
                        <td colspan="3">{{$form008->altura_uterina}}</td>
                        <td colspan="2" class="doble_td">{{trans('transformularios.PRESENTA')}}<br>{{trans('transformularios.CION')}}</td>
                        <td colspan="5">{{$form008->presentacion}}</td>
                    </tr>
                    <tr>
                        <td colspan="2">{{trans('transformularios.DILATACION')}}</td>
                        <td colspan="3">{{$form008->dilatacion}}</td>
                        <td colspan="2">{{trans('transformularios.BORRAMIENTO')}}</td>
                        <td colspan="2">{{$form008->borramiento}}</td>
                        <td>PLANO</td>
                        <td colspan="2">{{$form008->plano}}</td>

                    </tr>
                    <tr>
                        <td colspan="2">{{trans('transformularios.PELVISUTIL')}}</td>
                        <td colspan="2">@if($form008->pelvis_util=='1') X @endif</td>
                        <td colspan="2" class="doble_td">{{trans('transformularios.SANGRADO')}} <br>{{trans('transformularios.VAGINAL')}}</td>
                        <td>@if($form008->sangrado_vaginal=='1') X @endif</td>
                        <td colspan="3">{{trans('transformularios.CONTRACCIONES')}}</td>
                        <td colspan="3">{{$form008->contracciones}}</td>

                    </tr>
                    <tr>
                        <td colspan="12">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="12">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="12">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="12">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="12">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="12">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="12">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="12">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="12">&nbsp;</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
    <div style="width: 94%; ">

        <table border="1" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th colspan="16" class="small">10 {{trans('transformularios.SOLICITUDDEEXAMENES')}}</th>

                </tr>

            </thead>
            <tbody>
                <tr class="alinear">
                    <td width="5%" class="texto">1. {{trans('transformularios.BIOMERTRIA')}}</td>
                    <td width="5%" class="texto">{{$form008->biometria}}</td>
                    <td width="10%" class="texto">3. {{trans('transformularios.QUIMICASANGUINEA')}}</td>
                    <td width="2%" class="texto">{{$form008->quimica_sanguinea}}</td>
                    <td width="10%" class="texto">5. {{trans('transformularios.GASOMETRIA')}}</td>
                    <td width="2%" class="texto">{{$form008->gasometria}}</td>
                    <td width="10%" class="texto">7. {{trans('transformularios.ENDOSCOPIA')}}</td>
                    <td width="2%" class="texto">{{$form008->endoscopia}}</td>
                    <td width="10%" class="texto">9. {{trans('transformularios.RXABDOMEN')}}</td>
                    <td width="2%" class="texto">{{$form008->rx_abdomen}}</td>
                    <td width="10%" class="texto">11. {{trans('transformularios.TOMOGRAFIA')}}</td>
                    <td width="2%" class="texto">{{$form008->tomografia}}</td>
                    <td width="10%" class="texto">13. {{trans('transformularios.ECOGRAFIAPELVICA')}}</td>
                    <td width="2%" class="texto">{{$form008->ecografia_pelvica}}</td>
                    <td width="10%" class="texto">15. {{trans('transformularios.INTERCONSULTA')}}</td>
                    <td width="2%" class="texto">{{$form008->interconsulta}}</td>

                </tr>
                <tr class="alinear">
                    <td width="10%" class="texto">2. {{trans('transformularios.UROANALISIS')}}</td>
                    <td width="1%" class="texto">{{$form008->interconsulta}}</td>
                    <td width="10%" class="texto">4. {{trans('transformularios.ELECTROLITOS')}}</td>
                    <td width="2%" class="texto">{{$form008->electrolitos}}</td>
                    <td width="2%" class="texto">6. {{trans('transformularios.ELECTROCARDIOGRAMA')}}</td>
                    <td width="2%" class="texto">{{$form008->electro_cardiograma}}</td>
                    <td width="10%" class="texto">8. {{trans('transformularios.RXTORAX')}}</td>
                    <td width="2%" class="texto">{{$form008->rx_torax}}</td>
                    <td width="10%" class="texto">10. {{trans('transformularios.RXOSEA')}}</td>
                    <td width="2%" class="texto">{{$form008->rx_osea}}</td>
                    <td width="10%" class="texto">12. {{trans('transformularios.RESONANCIA')}}</td>
                    <td width="2%" class="texto">{{$form008->resonancia}}</td>
                    <td width="10%" class="texto">14. {{trans('transformularios.ECOGRAFIAABDOMEN')}}</td>
                    <td width="2%" class="texto">{{$form008->ecografia_abdomen}}</td>
                    <td width="10%" class="texto">16. {{trans('transformularios.OTROS')}}</td>
                    <td width="2%" class="texto">{{$form008->intercotrosonsulta}}</td>
            </tbody>
        </table>

    </div>
    <div style="width: 96%; margin-top: 15px;margin-bottom: 25px;">
        <div style="width: 50%;  display: inline-block;">
            <table border="1" class="texto">
                <thead style="border-bottom: solid 1px;" class="border_none">
                    <tr>
                        <th colspan="1" style="border:none;">11 </th>
                        <th colspan="4" style="border:none;">11 {{trans('transformularios.DIAGNOSTICODEINGRESO')}} </th>
                        <th width="10%" style="border:none; font-size: 5px;">{{trans('transformularios.PRESUNTIVO')}} <br>{{trans('transformularios.DEFINITIVO')}} </th>
                        <th style="border:none;">CIE</th>
                        <th style="border:none;">PRE</th>
                        <th style="border:none;">DEF</th>
                    </tr>
                </thead>
                <tbody class="border_none">
                    @php $cont = 1; @endphp
                    @foreach($pasos as $val)
                    @if($val->ingreso_egreso == "INGRESO")
                    <tr class="alinear">
                        <td colspan="1">
                            {{$cont}}
                        </td>
                        <td colspan="4">
                            {{$val->ingreso_egreso}}
                        </td>
                        <td width="10%">
                            @if($val->presuntivo_definitivo == "PRESUNTIVO") PRE @else DEF @endif
                        </td>
                        <td>
                            {{$val->cie10}}
                        </td>
                        <td>
                            @if($val->presuntivo_definitivo == "PRESUNTIVO") X @endif
                        </td>
                        <td>
                            @if($val->presuntivo_definitivo == "DEFINITIVO") X @endif
                        </td>
                    </tr>
                    @php $cont ++; @endphp
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="width: 50%;  display: inline-block;">

            <table border="1" class="texto">
                <thead style="border-bottom: solid 1px;" class="border_none">
                    <tr>
                        <th style="border:none;">12 </th>
                        <th colspan="3" style="border:none;">{{trans('transformularios.DIAGNOSTICODEALTA')}} </th>
                        <th width="10%" style="border:none; font-size: 5px;">{{trans('transformularios.PRESUNTIVO')}} <br>{{trans('transformularios.DEFINITIVO')}} </th>
                        <th style="border:none;">CIE</th>
                        <th style="border:none;">PRE</th>
                        <th style="border:none;">DEF</th>

                    </tr>

                </thead>
                <tbody class="border_none">
                    @php $cont = 1; @endphp
                    @foreach($pasos as $val)
                    @if($val->ingreso_egreso == "EGRESO")
                    <tr class="alinear">
                        <td>
                            {{$cont}}
                        </td>
                        <td colspan="3">
                            {{$val->ingreso_egreso}}
                        </td>
                        <td width="10%">
                            @if($val->presuntivo_definitivo == "PRESUNTIVO") PRE @else DEF @endif
                        </td>
                        <td>
                            {{$val->cie10}}
                        </td>
                        <td>
                            @if($val->presuntivo_definitivo == "PRESUNTIVO") X @endif
                        </td>
                        <td>
                            @if($val->presuntivo_definitivo == "DEFINITIVO") X @endif
                        </td>
                    </tr>
                    @php $cont ++; @endphp
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div style="width: 100%; margin-top: -45px">
        <div style="margin-top: 10px;">

            <table border="2" cellspacing="0" cellpadding="0" style="margin-right:25px;">
                <thead>
                    <tr>
                        <th colspan="5" class="small">13 {{trans('transformularios.PLANDETRATAMIENTO')}}</th>

                    </tr>

                </thead>
                <tbody>

                    <tr class="alinear">
                        <td width="50%" class="texto">{{trans('transformularios.INDICACIONES')}}</td>
                        <td width="2%" class="texto"></td>
                        <td width="40%" class="texto">{{trans('transformularios.MEDICAMENTOP')}}</td>
                        <td colspan="2" width="40%" class="texto">{{trans('transformularios.POSOLOGIA')}}</td>

                    </tr>
                    @php $j=0; @endphp
                    @foreach($detalles as $detalle)
                    <tr class="alinear">
                        <td width="50%" class="texto"></td>
                        <td width="2%" class="texto">1</td>
                        <td width="40%" class="texto">{{$detalle->nombre}}</td>
                        <td colspan="2" width="40%" class="texto">{{$detalle->dosis}}</td>
                    </tr>
                    @php $j++; @endphp
                    @endforeach

                </tbody>
            </table>
        </div>
        @php
        $form008 = $solicitudemer->form008->first();

        @endphp
        <div style="width: 100%; margin-top: 10px">

            <table border="1" cellspacing="0" cellpadding="0" style="margin-right:25px;">
                <thead>
                    <tr>
                        <th colspan="19" class="small">14 {{trans('transformularios.ALTA')}}</th>

                    </tr>

                </thead>
                <tbody>
                    <tr class="alinear">
                        <td width="5%" class="texto">{{trans('transformularios.DOMICILIO')}}</td>
                        <td width="2%" class="texto">@if(empty($verificacion)) @else @if(($verificacion->seccion)==1 ) X @endif @endif</td>
                        <td width="5%" class="texto">{{trans('transformularios.CONSULTAEXTERNA')}}</td>
                        <td width="2%" class="texto">@if(empty($verificacion)) @else @if(($verificacion->seccion)==2 ) X @endif @endif</td>
                        <td width="5%" class="texto">{{trans('transformularios.OBSERVACION')}}</td>
                        <td width="2%" class="texto">@if(empty($verificacion)) @else {{$verificacion->observaciones}} @endif</td>
                        <td width="5%" class="texto">{{trans('transformularios.INTERNACION')}}</td>
                        <td width="2%" class="texto">@if(empty($verificacion)) @else @if(($verificacion->seccion)==4 ) X @endif @endif</td>
                        <td width="5%" class="texto">{{trans('transformularios.REFERENCIA')}}</td>
                        <td width="2%" class="texto">@if(empty($verificacion)) @else {{$verificacion->servicio_reposo}}} @endif</td>
                        <td width="0.5%" class="texto"></td>
                        <td width="5%" class="texto">{{trans('transformularios.EGRESAVIVO')}}</td>
                        <td width="2%" class="texto">@if(empty($verificacion->id_condicion)) @else ($verificacion->id_condicion == 1) X @endif</td>
                        <td width="5%" class="texto">{{trans('transformularios.ENCONDICONESTABLE')}}</td>
                        <td width="2%" class="texto">@if(empty($verificacion->id_condicion)) @else($verificacion->id_condicion == 2) X @endif</td>
                        <td width="5%" class="texto">{{trans('transformularios.ENCONDICONINESTABLE')}}</td>
                        <td width="2%" class="texto">@if(empty($verificacion->id_condicion)) @else ($verificacion->id_condicion == 3) X @endif</td>
                        <td width="5%" class="texto">{{trans('transformularios.DIASDEINCAPACIDAD')}}</td>
                        <td width="2%" class="texto">@if(empty($verificacion->id_condicion)) @else ($verificacion->id_condicion == 4) X @endif</td>

                    </tr>

                    <tr class="alinear">
                        <td width="5%" class="texto">{{trans('transformularios.SERVICIODEREFERENCIA')}} </td>
                        <td colspan="3" width="5%" class="texto">@if(!empty($verificacion->servicio_reposo)) X @endif</td>
                        <td width="5%" class="texto">{{trans('transformularios.ESTABLECIMIENTO')}}</td>
                        <td colspan="5" width="5%" class="texto">@if(!empty($verificacion->id_establecimiento)) X @endif</td>
                        <td width="0.5%" class="texto"></td>
                        <td width="5%" class="texto">{{trans('transformularios.MUERTOENEMERGENCIA')}}</td>
                        <td width="2%" class="texto">@if(empty($verificacion->id_condicion)) @else ($verificacion->id_condicion == 5) X @endif</td>
                        <td width="5%" class="texto">{{trans('transformularios.CAUSA')}}</td>
                        <td colspan="5" width="5%" class="texto">@if(empty($verificacion->causa)) @else {{$verificacion->causa}} @endif</td>
                    </tr>

                </tbody>
            </table>
        </div>

        <div style="margin-top: -8px">

            <p class="texto alinear"> {{trans('transformularios.CODIGO')}}</p>
        </div>
        <div style="margin-top: -8px">

            <table border="1" cellspacing="0" cellpadding="0" style="margin-right:25px;">
                @php
                $usuario= \Sis_medico\User::where('id',$form008->id_usuariocrea)->first();

                @endphp
                <thead>
                    <tr class="alinear">
                        <td width="5%" class="texto">{{trans('transformularios.FECHA')}}</td>
                        <td width="5%" class="texto"> {{substr($form008->created_at,0,10)}}</td>
                        <td colspan="2" width="5%" class="texto">{{trans('transformularios.HORA')}}</td>
                        <td width="5%" class="texto">{{substr($form008->created_at,10,15)}}</td>
                        <td width="5%" class="texto">{{trans('transformularios.NOMBREDELPROFESIONAL')}}</td>
                        <td colspan="2" width="5%" class="texto">{{$form008->id_usuariocrea}}</td>
                        <td colspan="2" width="5%" class="texto">{{trans('transformularios.Código')}}</td>
                        <td colspan="2" width="5%" class="texto">{{$form008->codigo}}</td>
                        <td width="5%" class="texto">{{trans('transformularios.FIRMA')}}</td>
                        <td width="5%" class="texto">{{$usuario->nombre1}} {{$usuario->nombre2}} {{$usuario->apellido1}} {{$usuario->apellido2}} </td>
                        <td width="5%" class="texto">{{trans('transformularios.NUMERODEHOJA')}}</td>
                        <td width="5%" class="texto">1</td>

                    </tr>

                </thead>

            </table>
        </div>
        <footer class="texto">
            <div style="width: 50%;  display: inline-block;">
                SNS-MSP / HCU-form.008 /2008 EMERGENCIAS (1)
        </footer>
</body>


</html>