<style>
    .recuadro {
        height: 200px;
        width: 100px;
    }

    .sradio {
        border: 1px solid white;
        border-radius: 20px;
        font-size: 20px;
        width: 40px;
        text-align: center;
        color: white;
    }

    .colorbasic {
        color: white !important;
    }

    b {
        padding-top: 11px !important;
    }

    .burbuja {
        padding-left: 4px;
        height: 200px;
    }

    .card-title {
        font-size: 14px !important;
    }

    .card-header {

        max-height: 100px;
    }

    #cambio8 {
        transition: height 1s, width 1s, padding 1s, visibility 1s, opacity 0.5s ease-out;
    }

    #cambio8.hide {
        -webkit-animation: out 700ms ease both;
        animation: out 700ms ease both;
    }
    #cambio14 {
        transition: height 1s, width 1s, padding 1s, visibility 1s, opacity 0.5s ease-out;
    }

    #cambio14.hide {
        -webkit-animation: out 700ms ease both;
        animation: out 700ms ease both;
    }

    .selection__choice{
        background-color: red !important;
        border-color: red !important;
    }

</style>

@php
  $rolUsuario = Auth::user()->id_tipo_usuario;
@endphp
<div class="modal fade" id="examen_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content "id="examen_div"  >
        </div>
    </div>
</div>
<div class="modal fade" id="consulta_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content "id="consulta_div"  >
        </div>
    </div>
</div>
<div class="modal fade" id="estudio_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content "id="estudio_div"  >
        </div>
    </div>
</div>
<div class="col-md-12">
    <input type="hidden" id="solicitudGeneral" value="{{$solicitud->id}}">
    <div class="row">
        <div class="col-md-6" >
            <div class="card h-80" id="ingreso">
                <!---->
                <!---->
                <div class="card-header bg bg-primary colorbasic">
                    <div class="row">
                        <div class="d-flex align-items-center col-md-9">

                            <span class="sradio">1</span>
                            <h4 class="card-title ml-25 colorbasic">
                               {{trans('boxesh.RegistrodeAdmision')}}
                            </h4>

                        </div>
                        <div class="col-md-3 align-items-right">
                            <button class="btn btn-primary btn-xs" type="button" onclick="primer_paso()"> <i class="fa fa-plus"></i> </button>
                        </div>

                    </div>

                </div>
                <div class="card-body" style="min-height: 200px;">
                    <br>
                    <div class="col-md-12">
                        <b> {{trans('boxesh.ApellidosyNombres')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span>{{ $solicitud->paciente->apellido1}} {{ $solicitud->paciente->apellido2}} {{ $solicitud->paciente->nombre1}} {{ $solicitud->paciente->nombre2}} </span>
                    </div>
                    <div class="col-md-12">
                        <b> {{trans('boxesh.Cedula')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span>{{ $solicitud->id_paciente }} </span>
                    </div>
                    <div class="col-md-12">
                        <b> {{trans('boxesh.Ciudad')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span>{{ $solicitud->paciente->ciudad }} </span>
                    </div>
                    <div class="col-md-12">
                        <b> {{trans('boxesh.Telefono')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span>{{ $solicitud->paciente->telefono1 }} </span>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-80">
                <!---->
                <!---->
                <div class="card-header bg bg-primary colorbasic">
                    <div class="row">
                        <div class="d-flex align-items-center col-md-9"> <span class="sradio">2</span>
                            &nbsp;
                            <h4 class="card-title ml-25 colorbasic">
                                {{trans('boxesh.IniciodeAtencionyMotivo')}}
                            </h4>
                        </div>
                        <div class="col-md-3 align-items-right">
                            <button class="btn btn-primary btn-xs" type="button" onclick="segundo_paso()"> <i class="fa fa-plus"></i> </button>
                        </div>

                    </div>

                </div>
                <div class="card-body" style="min-height: 200px;">
                    <br>
                    <div class="col-md-12">
                        <b> {{trans('boxesh.Fecha')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span>{{ $solicitud->fecha_ingreso }}</span>
                    </div>
                    <div class="col-md-12">
                        <b> {{trans('boxesh.Motivo')}}</b>
                    </div>
                    @php $form008 = $solicitud->form008->first(); @endphp
                    <div class="col-md-12">
                        <span>@if($form008 != null) @if($form008->trauma) {{ trans('Trauma') }} @endif @if($form008->c_clinica) {{ trans('C. Clinica') }} @endif @if($form008->c_obstetrica) {{ trans('G. Obstetrica') }} @endif @if($form008->c_quirurgica) {{ trans('Quirurgica') }} @endif @if($form008->n_policia) {{ trans('N. Policia') }} @endif @if($form008->o_motivo) {{ trans('Otros') }}: @endif {{ substr($form008->motivo,0,20) }}... @endif</span>
                    </div>
                    <div class="col-md-12">
                        <b> {{trans('boxesh.GrupoSanguineo')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span>{{ $solicitud->paciente->gruposanguineo }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-80">
                <!---->
                <!---->
                <div class="card-header bg bg-primary colorbasic" >
                    <div class="row">
                        <div class="d-flex align-items-center col-md-9"> 
                            <span class="sradio">3</span>
                            &nbsp;
                            <h4 class="card-title ml-10 colorbasic">
                            {{trans('boxesh.AccidenteViolenciaIntoxicaciónEnvenenamiento')}}
                            </h4>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary btn-xs" type="button" onclick="tercer_paso();"> <i class="fa fa-plus"></i> </button>
                        </div>

                    </div>

                </div>
                <div class="card-body" style="min-height: 200px;">
                    <br>
                    <div class="col-md-12">
                        <b> {{trans('boxesh.FechayHora')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span> @if($form008 != null) @if($form008->fecha_evento != null){{ $form008->fecha_evento }} @else {{$solicitud->fecha_ingreso}} @endif @endif</span>
                    </div>
                    <div class="col-md-12">
                        <b> {{trans('boxesh.LugardelEvento')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span>@if($form008 != null) {{ $form008->lugar_evento }} @endif </span>
                    </div>
                    <div class="col-md-12">
                        <b> {{trans('boxesh.DirecciondelEvento')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span>@if($form008 != null) {{ $form008->direccion_evento }} @endif </span>
                    </div>

                </div>
                <!---->
                <!---->
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-80">
                <!---->
                <!---->
                <div class="card-header bg bg-primary colorbasic" style="min-height: 100px;">
                    <div class="row">
                        <div class="d-flex align-items-center col-md-9"> <span class="sradio">4</span>
                            &nbsp;
                            <h4 class="card-title ml-10 colorbasic">
                                {{ trans('boxesh.AntecedentesPersonalesyFamiliares')}}
                            </h4>
                        </div> &nbsp; &nbsp;
                        <div class="col-md-2 align-items-right">
                            <button class="btn btn-primary btn-xs" type="button" onclick="cuarto_paso()"> <i class="fa fa-plus"></i> </button>
                        </div>

                    </div>

                </div>
                <div class="card-body" style="min-height: 200px;">
                    <br>
                    <div class="row">    
                        <div class="col-md-12">
                            <b> {{trans('boxesh.AntecedentesPersonalesyFamiliares')}}</b>
                        </div>
                        <div class="col-md-4">
                            <b style="font-size: 12px">1. {{trans('boxesh.Alergias')}}</b>
                        </div>
                        @php 
                            $alergias = $solicitud->paciente->a_alergias; $txt_al = '';$cont = 0;
                            foreach($alergias as $alergia){ 
                                if($cont < 2)  {  
                                if($cont==0){ $txt_al = $alergia->principio_activo->nombre; }
                                else{ $txt_al = $txt_al.' + '.$alergia->principio_activo->nombre; }
                                $cont++;
                                }
                            }  
                            $txt_al = $txt_al.' ...'  
                        @endphp
                        <div class="col-md-8">
                            <span class="badge badge-danger" style="font-size: 11px"> {{$txt_al}} </span>
                        </div>
                        
                        <div class="col-md-6">
                            <b style="font-size: 12px">2. {{trans('boxesh.Clinicos')}}</b>
                        </div>
                        @php $datos_paciente = $solicitud->paciente->ho_datos_paciente; @endphp
                        <div class="col-md-6">
                            <span style="font-size: 12px"> {{ substr($datos_paciente->clinico,0,30) }} ... </span>
                        </div>
                         <div class="col-md-6">
                            <b style="font-size: 12px">3. {{trans('boxesh.Ginecologico') }}</b>
                        </div>

                        <div class="col-md-6">
                            <span style="font-size: 12px"> {{ substr($datos_paciente->ginecologico,0,30) }} ... </span>
                        </div>
                        <div class="col-md-6">
                            <b style="font-size: 12px">4. {{trans('boxesh.Traumatologicos')}} </b>
                        </div>
                        <div class="col-md-6">
                            <span style="font-size: 12px"> {{ substr($datos_paciente->traumatologico,0,30) }} ... </span>
                        </div>
                        <!--div class="col-md-12">
                            <span> </span>
                        </div>
                        <div class="col-md-12">
                            <b>5. {{ trans('Quirurgicos') }}</b>
                        </div>
                        <div class="col-md-12">
                            <span>{{ $solicitud->paciente->antecedentes_quir }}</span>
                        </div>
                        <div class="col-md-12">
                            <b>6. {{ trans('Farmacologicos') }} </b>
                        </div>
                        <div class="col-md-12">
                            <span> </span>
                        </div-->
                    </div>    
                </div>
                <!---->
                <!---->
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-80">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center col-md-9"> <span class="sradio">5</span>
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('boxesh.EnfermedadActualyRevisiondeSistemas')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary" type="button" onclick="quinto_paso()"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="min-height: 200px;">
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <span>@if($form008 != null){{ $form008->enfermedad_actual }}@endif</span>
                        </div>
                        <div class="col-md-6">
                            <b>{{trans('boxesh.ViaAereaLibre')}}</b>
                        </div>

                        <div class="col-md-6">
                            <span> @if($form008 != null){{  substr($form008->aerea_libre,0,30) }} ... @endif</span>
                        </div>
                        <div class="col-md-6">
                            <b>{{trans('boxesh.ViaAereaObstruida')}}</b>
                        </div>

                        <div class="col-md-6">
                            <span> @if($form008 != null){{  substr($form008->aerea_obstruida,0,30) }} ... @endif</span>
                        </div>

                        <div class="col-md-6">
                            <b>{{trans('boxesh.CondicionEstable')}}</b>
                        </div>

                        <div class="col-md-6">
                            <span> @if($form008 != null){{ substr($form008->condicion_estable,0,30) }} ... @endif</span>
                        </div>

                        <div class="col-md-6">
                            <b>{{trans('boxesh.CondicionInestable')}}</b>
                        </div>

                        <div class="col-md-6">
                            <span>  @if($form008 != null){{  substr($form008->condicion_inestable,0,30) }} ... @endif</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-80">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center col-md-9"> <span class="sradio">6</span>
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('boxesh.SignosVitalesMedicionesyValores')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary" type="button" onclick="sexto_paso()"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                @php 
                if($form008 != null) { 
                    $hc = $form008->agenda->historia_clinica; 
                } else { 
                    $hc = null;
                } 
                @endphp

                <div class="card-body" style="min-height: 200px;">
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            <b> {{trans('boxesh.Presion')}}</b>
                        </div>
                        <div class="col-md-6">
                            <span>@if($hc != null) {{ $hc->presion }} @endif</span>
                        </div>

                        <div class="col-md-6">
                            <b> {{trans('boxesh.Pulso')}}</b>
                        </div>
                        <div class="col-md-6">
                            <span>@if($hc != null) {{ $hc->pulso }} @endif </span>
                        </div>

                        <!--div class="col-md-6">
                            <b> {{trans('boxesh.Temperatura')}}</b>
                        </div>
                        <div class="col-md-6">
                            <span>@if($hc != null) {{ $hc->temperatura }} @endif </span>
                        </div-->

                        <div class="col-md-6">
                            <b> Sa O2</b>
                        </div>
                        <div class="col-md-6">
                            <span>@if($form008 != null){{ $form008->satura_oxigeno }} @endif </span>
                        </div>

                        <div class="col-md-6">
                            <b> {{trans('boxesh.Estatura')}}</b>
                        </div>
                        <div class="col-md-6">
                            <span>@if($hc != null) {{ $hc->altura }} @endif </span>
                        </div>

                        <div class="col-md-6">
                            <b> {{trans('boxesh.Peso')}}</b>
                        </div>
                        <div class="col-md-6">
                            <span>@if($hc != null) {{ $hc->peso }} @endif </span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-80">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center col-md-9"> <span class="sradio">7</span>
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('boxesh.ExamenFisicoyDiagnostico')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary" type="button" onclick="septimo_paso()"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>

                <div class="card-body" style="min-height: 240px;">
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                          
                        </div>

                        <div class="col-md-6">
                            <b>1. {{trans('boxesh.ViaAereaObstru')}}</b>
                        </div>

                        <div class="col-md-6">
                            <span> @if($form008 != null){{ substr($form008->via_aerea_obs,0,30) }} ... @endif </span>
                        </div>
                        <div class="col-md-6">
                            <b>2. {{trans('boxesh.Cabeza')}}</b>
                        </div>

                        <div class="col-md-6">
                            <span> @if($form008 != null){{ substr($form008->cabeza,0,30) }} ...@endif </span>
                        </div>

                        <div class="col-md-6">
                            <b>3. {{trans('boxesh.Cuello')}}</b>
                        </div>

                        <div class="col-md-6">
                            <span>@if($form008 != null){{ substr($form008->cuello,0,30) }} ...@endif </span>
                        </div>
                        <div class="col-md-6">
                            <b>4. {{trans('boxesh.Torax')}}</b>
                        </div>

                        <div class="col-md-6">
                            <span> @if($form008 != null){{ substr($form008->torax,0,30) }} ... @endif </span>
                        </div>
                        <div class="col-md-6">
                            <b>5. {{trans('boxesh.Abdomen')}}</b>
                        </div>

                        <div class="col-md-6">
                            <span> @if($form008 != null) {{ substr($form008->abdomen,0,30) }} ...@endif </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6" id="cambio8">
            <div class="card h-80">
                <!---->
                <!---->
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center col-md-9"> <span class="sradio">8</span>
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('boxesh.LocalizaciondeLesiones')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" onclick="octavo_paso()"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="min-height: 240px;">
                    <br>
                    <div class="col-md-12">
                        @php
                        $imagen= \Sis_medico\Ho_Lesiones008::where('id_008',$solicitud->id)->first();
                        @endphp
                        @if(is_null($imagen))
                         <img src="{{asset('body.png')}}" width="200" height="200" srcset="">
                        @else
                         <img src='{{asset("hc_ima")}}/{{$imagen->url_imagen}}' width="200" height="200" srcset="">

                        @endif
                    </div>
                </div>
                <!---->
                <!---->
            </div>
        </div>
        <div class="col-md-6" >
            <div class="card h-80">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center col-md-9"> <span class="sradio">9</span>
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('boxesh.EmergenciaObstetrica')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" onclick="noveno_paso()"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="height: 215px;">
                    <br>
                    <div class="col-md-12">
                        <b> {{trans('boxesh.Gestas')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span> @if($form008!=null) {{$form008->gestas}} @endif </span>
                    </div>
                    <div class="col-md-12">
                        <b> {{trans('boxesh.Partos')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span> @if($form008!=null) {{$form008->partos}} @endif </span>
                    </div>
                    <div class="col-md-12">
                        <b> {{trans('boxesh.Abortos')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span> @if($form008!=null) {{$form008->abortos}} @endif</span>
                    </div>
                    <div class="col-md-12">
                        <b> {{trans('boxesh.Cesareas')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span> @if($form008!=null) {{$form008->cesareas}} @endif </span>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-80">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center"> <span class="sradio">10</span>
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('boxesh.SolicituddeExamenes')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" onclick=""> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body">
                    
                    <div class="row">
                        <div class="col-md-12">&nbsp;</div>
                        <div class="col-md-6" style="padding: 5px;"><center>
                            <button class="btn btn-primary btn-xs" style="color: white; width: 100%;" onClick="carga_ordenes_laboratorio()">
                                <span style="font-size: 10px;">{{trans('boxesh.LABORATORIO')}}</span>
                            </button></center>
                        </div>
                        <div class="col-md-6"><center>
                            <button class="btn btn-primary btn-xs" style="color: white; width: 100%;" onClick="carga_ordenes_procedimientos()">
                                <span style="font-size: 10px;">{{trans('boxesh.QUIRURGICOS')}}</span>
                            </button></center>
                        </div>
                        
                        <div class="col-md-12">&nbsp;</div>
                        <div class="col-md-6" style="padding: 5px;"><center>
                            <button class="btn btn-primary btn-xs" style="color: white; width: 100%;" onClick="carga_ordenes_imagenes()">
                                <span style="font-size: 10px;">{{trans('boxesh.IMAGENES')}}</span>
                            </button></center>
                        </div>
                        <div class="col-md-6"><center>
                            <button class="btn btn-primary btn-xs" style="color: white; width: 100%;" onClick="carga_interconsultas()">
                                <span style="font-size: 10px;">{{trans('boxesh.INTERCONSULTAS')}}</span>
                            </button></center>
                        </div> 
                        
                        <div class="col-md-12">&nbsp;</div>
                        <div class="col-md-6" style="padding: 5px;"><center>
                            <button class="btn btn-primary btn-xs" style="color: white; width: 100%;" onClick="carga_ordenes_endoscopicos()">
                                <span style="font-size: 10px;">{{trans('boxesh.ENDOSCOPICOS')}}</span>
                            </button></center>
                        </div>
                        <div class="col-md-6" style="padding: 5px;"><center>
                            <button class="btn btn-primary btn-xs" style="color: white; width: 100%;" onClick="carga_ordenes_funcionales()">
                                <span style="font-size: 10px;">{{trans('boxesh.FUNCIONALES')}}</span>
                            </button></center>
                        </div>
                                   
                    </div>    
                </div>
                <!---->
                <!---->
            </div>
        </div>
        @php
          // dd($solicitud);
            $hc_proc = Sis_medico\Ho_Solicitud::where('ho_solicitud.id',$solicitud->id)->join('agenda as ag','ag.id','ho_solicitud.id_agenda')
            ->join('historiaclinica as h','h.id_agenda','ag.id')
            ->join('hc_procedimientos as hc_proc','hc_proc.id_hc','h.hcid')
            ->select('ag.id as id_agenda','h.hcid','hc_proc.id as id_hcproc','ho_solicitud.id_paciente')
            ->first();
            //dd($hc_proc);
            $hc_cie10_ingreso=[];
            $hc_cie10_alta=[];
            if($hc_proc!=null){
                $hc_cie10_ingreso = Sis_medico\Hc_Cie10::where('hc_cie10.hc_id_procedimiento', $hc_proc->id_hcproc)->where('ingreso_egreso','INGRESO')->get();
                 $hc_cie10_alta = Sis_medico\Hc_Cie10::where('hc_cie10.hc_id_procedimiento', $hc_proc->id_hcproc)->where('ingreso_egreso','EGRESO')->get();
            }
           
        @endphp
        <div class="col-md-6">
            <div class="card h-80">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center"> <span class="sradio">11</span>
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                           {{trans('boxesh.DiagnosticodeIngreso')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" id="boton_p11" type="button" onclick="onceavo_paso();"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="height: 200px;">
                    <br>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{trans('boxesh.Cie')}}</th>
                                <th>{{trans('boxesh.Pref/Def')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hc_cie10_ingreso as $cie)
                            <tr>
                                <td>{{$cie->cie10}}</td>
                                <td>{{$cie->presuntivo_definitivo}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        
                    </table>
                </div>
                <!---->
                <!---->
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-80">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center"> <span class="sradio">12</span>
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('boxesh.DiagnosticodeAlta')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" id="boton_p12" type="button" onclick="doceavo_paso();"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="height: 200px;">
                    <br>
                    <table class="table">
                        <thead>
                            <tr>
                                
                                <th>{{trans('boxesh.Cie')}}</th>
                                <th>{{trans('boxesh.Pref/Def')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hc_cie10_alta as $cie)
                            <tr>
                                <td>{{$cie->cie10}}</td>
                                <td>{{$cie->presuntivo_definitivo}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        
                    </table>

                </div>
                <!---->
                <!---->
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-80">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center"> <span class="sradio">13</span>
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('boxesh.PlandeTratamiento')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary" type="button" onclick="treceavo_paso();"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="min-height: 200px;">
                    @php 
                        $agenda = $solicitud->agenda; 
                        $historia = $agenda->historia_clinica;
                        $receta = null;
                        if(count($historia->recetas)>0){
                            $receta = $historia->recetas->last();
                        }
                        

                    @endphp
                    <br>
                    <div class="row">
                        
                        <div class="col-md-12">
                            <b> Fecha</b>
                        </div>
                        <div class="col-md-12">
                            <span>{{$historia->created_at}} </span>
                        </div>
                        <div class="col-md-12">
                            <b> Medicamentos</b>
                        </div>
                        <div class="col-md-12">
                        @if($receta != null)
                            @foreach($receta->detalles as $detalle)
                                <span>* {{$detalle->nombre}}: cantidad {{$detalle->cantidad}} - {{substr($detalle->dosis, 0, 10)}}...</span><br>   
                            @endforeach 
                        @endif
                        </div>
                    </div>

                </div>
                <!---->
                <!---->
            </div>
        </div>

        <div class="col-md-6" id="cambio14">
            <div class="card h-80">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center"> <span class="sradio">14</span>
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('boxesh.Alta')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">

                        <button class="btn btn-primary" type="button" onclick="catorceavo_paso()"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="height: 200px;">
                    <br>
                         @php
                        $traspaso= \Sis_medico\Ho_Traspaso_Sala008::where('id_solicitud',$solicitud->id)->first();
                        @endphp
                    <div class="col-md-12">
                        <b> {{trans('boxesh.Fecha')}}</b>

                    </div>
                    <div class="col-md-12">
                    
                        <span>@if(!is_null($traspaso)) {{$traspaso->fecha}} @endif</span>
                    </div>
                    <div class="col-md-12">
                        <b> {{trans('boxesh.Condicion')}}</b>

                    </div>
                    <div class="col-md-12">
                    
                        <span>@if(!is_null($traspaso)) @if(isset($traspaso->condiciones)) {{$traspaso->condiciones->nombre}} @endif @endif</span>
                    </div>
                    <div class="col-md-12">
                        <b> {{trans('boxesh.Establecimiento')}}</b>

                    </div>
                    <div class="col-md-12">
                    
                        <span>@if(!is_null($traspaso)) @if(isset($traspaso->hospital)) {{$traspaso->hospital->nombre}} @endif @endif</span>
                    </div>

                </div>
                <!---->
                <!---->
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-80">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center"> <span class="sradio">15</span>
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('Resultado de Examenes')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" onclick="mostrarExamenes()"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="row">
                    <br> 
                    @php
                        $examenes= \Sis_medico\Ho_Solicitud::where('id',$solicitud->id)->first();
                        $examen= \Sis_medico\Examen_Orden::where('id_paciente',$examenes->id_paciente)->orderBy('created_at', 'DESC')->limit(1)->get();
                    @endphp
                    <!--div class="row">
                        
                        <div class="col-md-12" >
                            <table id="example2" class="table " >
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">Fecha</th>
                                        <th style="text-align: center;">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($examen as $value)
                                    <tr>
                                        <td>@if($value == null)  @else {{$value->created_at}} @endif</td>
                                        <td> <a  class="btn btn-primary btn-xs"  href="{{route('hospitalizacion.imprimir',['id' => $value->id])}}" target="_blank">{{trans('hospitalizacion.DescargarExamen')}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div> 
                    </div-->    
                    <div class="col-md-4">
                            <b>  {{trans('hospitalizacion.fecha')}} </b>
                        </div>
                        <div class="col-md-4">
                            <b>  {{trans('hospitalizacion.tipo')}} </b>
                        </div>
                        <div class="col-md-4">
                            <b>  {{trans('hospitalizacion.ver')}} </b>
                        </div>
                        <div class="col-md-12" style="text-align: center;"> <br> </div>
                        <div class="col-md-4" style="text-align: center;"> 
                            @if(empty($arr_listado[0])) @else {{$arr_listado[0][0]}} @endif
                        </div>
                        <div class="col-md-4" style="text-align: center;"> 
                            @if(empty($arr_listado[0])) @else {{$arr_listado[0][1]}} @endif
                        </div>
                        <div class="col-md-4" style="text-align: center;">
                            @if(empty($arr_listado[0]))   @else  <button class="btn btn-primary btn-xs" onclick="ver_laboratorio('{{$arr_listado[0][2]}}')">{{trans('hospitalizacion.ver')}}</button> 
                            <!--a  class="btn btn-primary btn-xs"  href="{{route('hospitalizacion.imprimir',['id' =>$arr_listado[0][2]])}}" target="_blank">{{trans('hospitalizacion.ver')}} </a-->@endif
                        </div>
                        <div class="col-md-12" style="text-align: center;"> <br> </div>
                        <div class="col-md-4" style="text-align: center;"> 
                            @if(empty($arr_listado[1])) @else {{$arr_listado[1][0]}} @endif
                        </div>
                        <div class="col-md-4" style="text-align: center;"> 
                            @if(empty($arr_listado[1])) @else {{$arr_listado[1][1]}} @endif
                        </div>
                        <div class="col-md-4" style="text-align: center;">
                            @if(empty($arr_listado[1]))   @else  <button class="btn btn-primary btn-xs" onclick="ver_estudio('{{$arr_listado[1][2]}}')">{{trans('hospitalizacion.ver')}}</button>@endif
                        </div>
                        <div class="col-md-12" style="text-align: center;"> <br> </div>
                        <div class="col-md-4" style="text-align: center;"> 
                            @if(empty($arr_listado[2])) @else {{$arr_listado[2][0]}} @endif
                        </div>
                        <div class="col-md-4" style="text-align: center;"> 
                            @if(empty($arr_listado[2])) @else {{trans('hospitalizacion.Interconsulta')}} <br> {{$arr_listado[2][1]}} @endif
                        </div>
                        <div class="col-md-4" style="text-align: center;">
                            @if(empty($arr_listado[2]))   @else  <button class="btn btn-primary btn-xs" onclick="ver_consulta('{{$arr_listado[2][2]}}')">{{trans('hospitalizacion.ver')}}  </button> @endif
                    </div>    
                </div>
                <!---->
                <!---->
            </div>
        </div>

    </div>
</div>
<script>
    $(document).ready(function() {
        primer_paso()
    });

     function mostrarExamenes(){
        $.ajax({
            type: "get",
            url: "{{route('hospitalizacion.cargar_examenes',['id' => $solicitud->id])}}",
            datatype: "html",
            success: function(datahtml, data) {
                $("#content").html(datahtml);
                $('html, body').animate({
                    scrollTop: $("#content").offset().top
                }, 1000) 
            },
            error: function() {
                alert('error al cargar');
            }
        });
    }

    function primer_paso() {
        //var id_orden = "1";
        //console.log("aqui");
        $.ajax({
            type: "get",
            url: "{{route('hospital.primerpaso',['id' => $solicitud->id ])}}",
            data: {
                //'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {

                $("#content").html(datahtml);
                
            },
            error: function() {
                alert('error al cargar');
            }
        });
    }

    function segundo_paso() {
        //var id_orden = "1";
        //console.log("aqui");
        @if($rolUsuario==3 || $rolUsuario == 1 )
        $.ajax({
            type: "get",
            url: "{{route('hospital.segundopaso',['id' => $solicitud->id ])}}",
            data: {
                //'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {

                 $("#content").html(datahtml);
                // $('html, body').animate({
                //     scrollTop: $("#content").offset().top
                // }, 1000)
            },
            error: function() {
                alert('error al cargar');
            }
        });
        @else
        alert("No tiene permiso de usuario para modificar");
        @endif
    }

    function tercer_paso() {
        @if($rolUsuario==3 || $rolUsuario == 1)
        var id_orden = "1";
        $.ajax({
            type: "get",
            url: "{{route('hospital.tercerpaso',['id' => $solicitud->id ])}}",
            data: {
                'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {

               $("#content").html(datahtml);
            //    $('html, body').animate({
            //         scrollTop: $("#content").offset().top
            //     }, 1000)
            },
            error: function() {
                alert('error al cargar');
            }
        });
        @else
        alert("No tiene permiso de usuario para modificar");
        @endif
    }

    function cuarto_paso() {
        //var id_orden = "1";
        //console.log("aqui");
        @if($rolUsuario==3 || $rolUsuario == 1)
        $.ajax({
            type: "get",
            url: "{{route('hospital.cuartopaso',['id' => $solicitud->id])}}",
            data: {
                //'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {

                $("#content").html(datahtml);
                // $('html, body').animate({
                //     scrollTop: $("#content").offset().top
                // }, 1000)
            },
            error: function() {
                alert('error al cargar');
            }
        });
        @else
        alert("No tiene permiso de usuario para modificar");
        @endif
    }

    function quinto_paso() {
        //var id_orden = "1";
        console.log("aqui");
        @if($rolUsuario==3 || $rolUsuario == 1)
        $.ajax({
            type: "get",
            url: "{{route('hospital.quintopaso',['id' => $solicitud->id])}}",
            data: {
                //'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {

                $("#content").html(datahtml);
                $('html, body').animate({
                    scrollTop: $("#content").offset().top
                }, 2000)
            },
            error: function() {
                alert('error al cargar');
            }
        });
        @else
        alert("No tiene permiso de usuario para modificar");
        @endif
    }

    function sexto_paso() {
       // var id_orden = "1";
        console.log("aqui");
        @if($rolUsuario==3 || $rolUsuario == 1)
        $.ajax({
            type: "get",
            url: "{{route('hospital.sextopaso',['id' => $solicitud->id])}}",
            data: {
               // 'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {

                $("#content").html(datahtml);
                $('html, body').animate({
                    scrollTop: $("#content").offset().top
                }, 1000) 
            },
            error: function() {
                alert('error al cargar');
            }
        });
        @else
        alert("No tiene permiso de usuario para modificar");
        @endif
    }

    function septimo_paso() {
       // var id_orden = "1";
        console.log("aqui");
        @if($rolUsuario==3 || $rolUsuario == 1)
        $.ajax({
            type: "get",
            url: "{{route('hospital.septimopaso',['id' => $solicitud->id])}}",
            data: {
               // 'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {
                $("#content").html(datahtml);
                $('html, body').animate({
                    scrollTop: $("#content").offset().top
                }, 1000)
            },
            error: function() {
                alert('error al cargar');
            }
        });
        @else
        alert("No tiene permiso de usuario para modificar");
        @endif
    }

    function octavo_paso() {
        var id_orden = $("#solicitudGeneral").val();
        console.log("aqui");
        @if($rolUsuario==3 || $rolUsuario == 1)
        $.ajax({
            type: "get",
            url: "{{route('hospital.octavopaso')}}",
            data: {
                'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {
                //$("#cambio8").hide();
                $("#content").html(datahtml);
                $('html, body').animate({
                    scrollTop: $("#content").offset().top 
                }, 1000)

            },
            error: function() {
                alert('error al cargar');
            }
        });
        @else
        alert("No tiene permiso de usuario para modificar");
        @endif
    }

    function noveno_paso() {
       var id_orden = "1";
        //console.log("aqui");
        @if($rolUsuario==3 || $rolUsuario == 1)
        $.ajax({
            type: "get",
            url: "{{route('hospital.novenopaso',['id' => $solicitud->id ])}}",
            data: {
                'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {

                $("#content").html(datahtml);
                $('html, body').animate({
                    scrollTop: $("#content").offset().top
                }, 1000)
            },
            error: function() {
                alert('error al cargar');
            }
        });
        @else
        alert("No tiene permiso de usuario para modificar");
        @endif
    }

    function onceavo_paso() {
        //var id_orden = "1";
        console.log("aqui");
        @if($rolUsuario==3 || $rolUsuario == 1)
        $.ajax({
            type: "get",
            url: "{{route('hospital.onceavopaso',['id' => $solicitud->id])}}",
            data: {
                //'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {

                $("#content").html(datahtml);
                $('html, body').animate({
                    scrollTop: $("#content").offset().top
                }, 1000)
            },
            error: function() {
                alert('error al cargar');
            }
        });
        @else
        alert("No tiene permiso de usuario para modificar");
        @endif
    }

    function doceavo_paso() {
        var id_orden = "1";
        console.log("aqui");
        @if($rolUsuario==3 || $rolUsuario == 1)
        $.ajax({
            type: "get",
            url: "{{route('hospital.doceavopaso',['id' => $solicitud->id])}}",
            data: {
                'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {

                $("#content").html(datahtml);
                $('html, body').animate({
                    scrollTop: $("#content").offset().top
                }, 1000)
            },
            error: function() {
                alert('error al cargar');
            }
        });
        @else
        alert("No tiene permiso de usuario para modificar");
        @endif
    }

    function treceavo_paso() {
        //var id_orden = "1";
        console.log("aqui");
        @if($rolUsuario==3 || $rolUsuario == 1)
        $.ajax({
            type: "get",
            url: "{{route('hospital.treceavopaso',['id' => $solicitud->id])}}",
            data: {
                //'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {

                $("#content").html(datahtml);
                $('html, body').animate({
                    scrollTop: $("#content").offset().top
                }, 1000)
            },
            error: function() {
                alert('error al cargar');
            }
        });
        @else
        alert("No tiene permiso de usuario para modificar");
        @endif

    }

    function catorceavo_paso() {
        var id_orden = $("#solicitudGeneral").val();
        console.log("aqui");
        @if($rolUsuario==3 || $rolUsuario == 1)
        $.ajax({
            type: "get",
            url: "{{route('hospital.catorceavo')}}",
            data: {
                'ep': id_orden,
            },
            datatype: "html",
            cache: false,
            success: function(datahtml, data) {

                $("#content").html(datahtml);
                //$("#cambio14").hide();
                $('html, body').animate({
                    scrollTop: $("#content").offset().top
                }, 1000)
            },
            error: function() {
                alert('error al cargar');
            }
        });
        @else
        alert("No tiene permiso de usuario para modificar");
        @endif
    }
    function carga_ordenes_laboratorio(){
        @if($rolUsuario==3 || $rolUsuario == 1)
        $.ajax({
            type: "GET",
            url: "{{route('hospital.decimo_laboratorio',['id' => $solicitud->id ])}}",
            data: "",
            datatype: "html",
            success: function(datahtml){

                $("#content").html(datahtml);
                $('html, body').animate({
                    scrollTop: $("#content").offset().top
                }, 1000)
            },
            error:  function(){
                alert('error al cargar');
            }
        });
        @else
        alert("No tiene permiso de usuario para modificar");
        @endif
    }
    function carga_ordenes_funcionales(){
        @if($rolUsuario==3 || $rolUsuario == 1)
        $.ajax({
            type: "GET",
            url: "{{route('hospital.decimo_procedimiento',['id' => $solicitud->id, 'tipo' => 1 ])}}",
            data: "",
            datatype: "html",
            success: function(datahtml){

                $("#content").html(datahtml);
                $('html, body').animate({
                    scrollTop: $("#content").offset().top
                }, 1000)
            },
            error:  function(){
                alert('error al cargar');
            }
        });
        @else
        alert("No tiene permiso de usuario para modificar");
        @endif
    }
    function carga_ordenes_imagenes(){
        @if($rolUsuario==3 || $rolUsuario == 1)
        $.ajax({
            type: "GET",
            url: "{{route('hospital.decimo_procedimiento',['id' => $solicitud->id, 'tipo' => 2 ])}}",
            data: "",
            datatype: "html",
            success: function(datahtml){

                $("#content").html(datahtml);
                $('html, body').animate({
                    scrollTop: $("#content").offset().top
                }, 1000)
            },
            error:  function(){
                alert('error al cargar');
            }
        });
        @else
        alert("No tiene permiso de usuario para modificar");
        @endif
    }
    function carga_ordenes_endoscopicos(){
        @if($rolUsuario==3 || $rolUsuario == 1)
        $.ajax({
            type: "GET",
            url: "{{route('hospital.decimo_procedimiento',['id' => $solicitud->id, 'tipo' => 0 ])}}",
            data: "",
            datatype: "html",
            success: function(datahtml){

                $("#content").html(datahtml);
                $('html, body').animate({
                    scrollTop: $("#content").offset().top
                }, 1000)
            },
            error:  function(){
                alert('error al cargar');
            }
        });
        @else
        alert("No tiene permiso de usuario para modificar");
        @endif
    }
    
    function carga_interconsultas(){
        @if($rolUsuario==3 || $rolUsuario == 1)
        $.ajax({
            type: "GET",
            url: "{{route('decimo.interconsulta',['id' => $solicitud->id ])}}",
            data: "",
            datatype: "html",
            success: function(datahtml){

                $("#content").html(datahtml);
                $('html, body').animate({
                    scrollTop: $("#content").offset().top
                }, 1000)
            },
            error:  function(){
                alert('error al cargar');
            }
        });
        @else
        alert("No tiene permiso de usuario para modificar");
        @endif
    }
    function carga_ordenes_procedimientos(){
        @if($rolUsuario==3 || $rolUsuario == 1)
        $.ajax({
            type: "GET",
            url: "{{route('hospital.decimo_procedimiento',['id' => $solicitud->id, 'tipo' => 3 ])}}",
            data: "",
            datatype: "html",
            success: function(datahtml){

                $("#content").html(datahtml);
                $('html, body').animate({
                    scrollTop: $("#content").offset().top
                }, 1000)
            },
            error:  function(){
                alert('error al cargar');
            }
        });
        @else
        alert("No tiene permiso de usuario para modificar");
        @endif
    }
    function ver_laboratorio(id){
   
    $.ajax({
        type: "get",
        url: "{{url('hospital/vtlaboratorio/modal')}}/"+id,
        data: {
        // 'ep': id_orden,
        },
        datatype: "html",
        success: function(datahtml, data) {
            $("#examen_div").html(datahtml);
        },
        error: function() {
            alert('error al cargar');
        }
    });
    $('#examen_modal').modal('show');
    }
    function ver_consulta(id){
    $.ajax({
        type: "get",
        url: "{{url('hospital/consultas_listado/modal/')}}/"+id,
        data: {
        // 'ep': id_orden,
        },
        datatype: "html",
        success: function(datahtml, data) {
            $("#consulta_div").html(datahtml);
        },
        error: function() {
            alert('error al cargar');
        }
    });
    $('#consulta_modal').modal('show');
    }
    function ver_estudio(id){
    $.ajax({
        type: "get",
        url: "{{url('hospital/vtimagen/modal')}}/"+id,
        data: {
        // 'ep': id_orden,
        },
        datatype: "html",
        success: function(datahtml, data) {
            $("#examen_div").html(datahtml);
        },
        error: function() {
            alert('error al cargar');
        }
    });
    $('#examen_modal').modal('show');
    }
    
</script>