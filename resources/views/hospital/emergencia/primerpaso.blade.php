<style>
    .tabla_ad {
        font-family: Arial, Helvetica, sans-serif;
        width: 100%;
    }

    .td_ad {
        width: 25%;
    }
</style>

<div class="card">
    <div class="card-header bg bg-primary">
        <div class="row">
            <div class="d-flex align-items-center col-md-12">


                <h4 class="card-title ml-25 colorbasic">
                    {{trans('pasos.RegistrodeAdmision')}}
                </h4>

            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <b> {{trans('pasos.apellidosynombres')}} </b>
            </div>
            <div class="col-md-12">
                <span>{{ $solicitud->paciente->apellido1}} {{ $solicitud->paciente->apellido2}} {{ $solicitud->paciente->nombre1}} {{ $solicitud->paciente->nombre2}} </span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table style="width: 100%;" class="tabla_ad">

                    <tr>
                        <td class="td_ad"><b> {{trans('pasos.cedula')}} </b></td>
                        <td class="td_ad"><b> {{trans('pasos.tipodocumentodeidentificacion')}} </b></td>
                        <td class="td_ad"><b> {{trans('pasos.ciudad')}} </b></td>
                        <td class="td_ad"><b> {{trans('pasos.telefono')}} </b></td>
                    </tr>

                    <tr>
                        <td>{{ $solicitud->id_paciente }}</td>
                        <td>{{ $solicitud->paciente->tipo_documento }}</td>
                        <td>{{ $solicitud->paciente->ciudad }}</td>
                        <td>{{ $solicitud->paciente->telefono1 }}</td>
                    </tr>

                    <tr>
                        <td class="td_ad"><b>{{trans('pasos.Seguro')}}</b></td>
                        <td class="td_ad"><b>{{trans('pasos.SubSeguro')}}</b></td>
                        <td class="td_ad"></td>
                        <td class="td_ad"></td>
                    </tr>

                    <tr>
                        <td>@if($solicitud->id_seguro != null) {{ $solicitud->seguro->nombre}} @endif</td>
                        <td>@if($solicitud->id_subseguro != null) {{ $solicitud->id_subseguro}} @endif</td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="td_ad"><b> {{trans('pasos.celular')}} </b></td>
                        <td class="td_ad"><b> {{trans('pasos.barrio')}}</b></td>
                        <td class="td_ad"><b> {{trans('pasos.parroquia')}}</b></td>
                        <td class="td_ad"><b> {{trans('pasos.direccion')}}</b></td>
                    </tr>

                    <tr>
                        <td>{{ $solicitud->paciente->telefono2 }}</td>
                        <td>{{ $solicitud->paciente->ho_datos_paciente->barrio }}</td>
                        <td>{{ $solicitud->paciente->ho_datos_paciente->parroquia }}</td>
                        <td>{{ $solicitud->paciente->direccion }}</td>
                    </tr>

                    <tr>
                        <td class="td_ad"><b>{{trans('pasos.canton')}} </b></td>
                        <td class="td_ad"><b>{{trans('pasos.provincia')}} </b></td>
                        <td class="td_ad"><b>{{trans('pasos.fechaNacimiento')}}</b></td>
                        <td class="td_ad"><b>{{trans('pasos.nacionalidad')}}</b></td>
                    </tr>

                    <tr>
                        <td>{{ $solicitud->paciente->ho_datos_paciente->canton }}</td>
                        <td>{{ $solicitud->paciente->ho_datos_paciente->provincia }}</td>
                        <td>{{ $solicitud->paciente->fecha_nacimiento }}</td>
                        <td>{{ $solicitud->paciente->ho_datos_paciente->nacionalidad }}</td>
                    </tr>

                    <tr>
                        <td class="td_ad"><b>{{trans('pasos.CallePrincipal')}}</b></td>
                        <td class="td_ad"><b>{{trans('pasos.CalleSecundaria')}}</b></td>
                        <td class="td_ad"><b>{{trans('pasos.Referencia')}}</td>
                        <td class="td_ad"></td>
                    </tr>

                    <tr>
                        <td>{{ $solicitud->paciente->ho_datos_paciente->calle_principal }}</td>
                        <td>{{ $solicitud->paciente->ho_datos_paciente->calle_secundaria }}</td>
                        <td>{{ $solicitud->paciente->ho_datos_paciente->referencia }}</td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="td_ad"><b> {{trans('pasos.edad')}}</b></td>
                        <td class="td_ad"><b>{{trans('pasos.sexo')}}</b></td>
                        <td class="td_ad"><b>{{trans('pasos.estadoCivil')}}</b></td>
                        <td class="td_ad"><b>{{trans('pasos.ocupacion')}}</b></td>
                    </tr>

                    <tr>
                        <td><span id="edad"> </span></td>
                        <td>
                            @if($solicitud->paciente->sexo =='1') HOMBRE @else MUJER @endif
                        </td>
                        <td>
                            @if($solicitud->paciente->estadocivil == 1)<span> SOLTERO(A)</span>
                            @elseif($solicitud->paciente->estadocivil == 2)<span> CASADO(A)</span>
                            @elseif($solicitud->paciente->estadocivil == 3)<span> VIUDO(A)</span>
                            @elseif($solicitud->paciente->estadocivil == 4)<span> DIVORCIADO(A)</span>
                            @elseif($solicitud->paciente->estadocivil == 5)<span> UNION LIBRE</span>
                            @endif
                        </td>
                        <td>{{ $solicitud->paciente->ocupacion }}</td>
                    </tr>

                    <tr>
                        <td class="td_ad"><b>{{trans('pasos.LugarNacimiento')}}</b></td>
                        <td class="td_ad"><b>{{trans('pasos.GrupoPrioritario')}}</b></td>
                        <td class="td_ad"><b>{{trans('pasos.EspecifiqueGrupoPrioritario')}}</b></td>
                        <td class="td_ad"><b>{{trans('pasos.AutoidentificacionEtnica')}}</b></td>
                    </tr>

                    <tr>
                        <td>{{ $solicitud->paciente->lugar_nacimiento }}</td>
                        <td>{{ $solicitud->paciente->ho_datos_paciente->grupo_prioritario}}</td>
                        <td>{{ $solicitud->paciente->ho_datos_paciente->especifique_grupo}}</td>
                        <td>{{ $solicitud->paciente->ho_datos_paciente->autoidentificacion_etnica}}</td>
                    </tr>
                    

                    <tr>
                        <td class="td_ad"><b>{{trans('pasos.NacionalidadEtnica')}}</b></td>
                        <td class="td_ad"><b>{{trans('pasos.Pueblos')}}</b></td>
                        <td class="td_ad"><b>{{trans('pasos.NivelEducacion')}}</b></td>
                        <td class="td_ad"><b>{{trans('pasos.EstadoNivelEducacion')}}</b></td>
                    </tr>

                    <tr>
                        <td>{{ $solicitud->paciente->ho_datos_paciente->nacionalidad_etnica}}</td>
                        <td>{{ $solicitud->paciente->ho_datos_paciente->pueblos}}</td>
                        <td>{{ $solicitud->paciente->ho_datos_paciente->nivel_educacion}}</td>
                        <td>{{ $solicitud->paciente->ho_datos_paciente->estado_nivel_educacion}}</td>
                    </tr>

                    <tr>
                        <td class="td_ad"><b>{{trans("pasos.TipoDeEmpresaDeTrabajo")}}</b></td>
                        <td class="td_ad"><b>{{trans('pasos.referido')}}</b></td>
                        <td class="td_ad"><b>{{trans('pasos.llamara')}}</b></td>
                        <td class="td_ad"></td>
                    </tr>

                    <tr>
                        <td>{{ $solicitud->paciente->ho_datos_paciente->empresa_trabajo }} </td>
                        <td>{{ $solicitud->paciente->referido }}</td>
                        <td>{{ $solicitud->paciente->ho_datos_paciente->llamar_a }}</td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="td_ad"><b> {{trans('pasos.parentesco')}}</b></td>
                        <td class="td_ad"><b> {{trans('pasos.telefono')}}</b></td>
                        <td class="td_ad"><b> {{trans('pasos.familiar')}}</b></td>
                        <td class="td_ad"><b> {{trans('pasos.llegada')}}</b></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>{{ $solicitud->paciente->ho_datos_paciente->telefono_inst_per_paci }}</td>
                        <td>{{ $solicitud->paciente->ho_datos_paciente->direccion_familiar }}</td>
                        <td>{{ $solicitud->paciente->ho_datos_paciente->forma_llegada }}</td>
                    </tr>

                    <tr>
                        <td class="td_ad"><b>{{trans('pasos.informacion')}}</b></td>
                        <td class="td_ad"><b> {{trans('pasos.telefono')}}</b></td>
                        <td class="td_ad"><b>{{trans('pasos.InstitucionOPersonaQueEntregaAlPaciente')}}</b></td>
                        <td class="td_ad"></td>
                    </tr>

                    <tr>
                        <td>{{ $solicitud->paciente->ho_datos_paciente->direccion_familiar }}</td>
                        <td>{{ $solicitud->paciente->telefono3 }}</td>
                        <td>{{ $solicitud->paciente->ho_datos_paciente->inst_per_paci_entrega }}</td>
                        <td></td>
                    </tr>

                </table>
            </div>

        </div>
    </div>

</div>
<script type="text/javascript">
    Edad('{{$solicitud->paciente->fecha_nacimiento}}', `{{date('Y-m-d',strtotime($solicitud->created_at))}}`);

    function Edad(FechaNacimiento, Fechasolicitud) {

        var fechaNace = new Date(FechaNacimiento);
        var fechaActual = new Date(Fechasolicitud)

        var mes = fechaActual.getMonth();
        var dia = fechaActual.getDate();
        var año = fechaActual.getFullYear();

        fechaActual.setDate(dia);
        fechaActual.setMonth(mes);
        fechaActual.setFullYear(año);

        edad = Math.floor(((fechaActual - fechaNace) / (1000 * 60 * 60 * 24) / 365));
        $('#edad').text(edad);
        //return edad;
        //alert(edad);
    }
</script>