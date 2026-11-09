@extends('hc_admision.orden_proc.base_biopsia')
@section('action-content')
@php

$date  = date('Y-m-d');

@endphp
<style type="text/css">
    .parent {
        overflow-y: scroll;
        height: 600px;
    }

    .parent::-webkit-scrollbar {
        width: 8px;
    }

    /* this targets the default scrollbar (compulsory) */
    .parent::-webkit-scrollbar-thumb {
        background: #3c8dbc;
        border-radius: 10px;
    }

    .parent::-webkit-scrollbar-track {
        width: 10px;
        background-color: #3c8dbc;
        box-shadow: inset 0px 0px 0px 3px #3c8dbc;
    }

    .contenedor2 {
        padding-left: 15px;
        padding-right: 60px;
        padding-top: 20px;
        padding-bottom: 0px;
        background-color: #FFFFFF;
        margin-left: 0px;
    }
</style>


<div class="container-fluid">
    <div class="row ">
        <div class="col-md-12" style="padding-left: 0.;padding-left: 0px;padding-right: 9px;margin-left: 5px;margin-right: 0px;border-radius: 10px;">
            <div class="col-md-12" style="border: 0px solid #000000;margin-left: 8px;margin-right: 0px;margin-left: 4px;padding-right: 0px;padding-left: 0px;background-color:#4682B4;">
                <div class="row">
                    <div class="col-md-9" style="padding-left:460px">
                        <h1 style="font-size: 15px; margin:0; color: white;padding-top: 12px;">
                            <b>HISTORIAL ORDENES DE BIOPSIAS</b>
                        </h1>
                    </div>
                    <br>
                    @if(!is_null($paciente))
                    @php
                    $xedad = Carbon\Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
                    @endphp
                    <div class="row">
                        <div class="col-md-9" style="padding-top: 15px">
                            <h1 style="font-size: 14px; margin:0;color: white;padding-left:20px">
                                <b>PACIENTE : {{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif
                                    {{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif
                                </b>
                            </h1>
                        </div>
                        <div class="col-md-3" style="padding-top: 15px">
                            <h1 style="font-size: 14px; margin:0;color: white;padding-left: 10px">
                                <b>
                                    EDAD: {{$xedad}} AÑOS
                                </b>
                            </h1>
                        </div>
                    </div>
                    @endif
                    <br>
                </div>
                <div>
                    <div style="border-left-width: 20px; padding-left: 15px; padding-right: 10px; padding-top: 20px;padding-bottom: 0px; background-color: #ffffff; margin-left: 0px">
                        <div class="parent" style="background-color: #ffffff">
                            <div style=" margin-right: 30px;">

                                @foreach($biopsias as $val)
                                @foreach($val as $orden)
                                <div class="box box-primary collapsed-box" style="border: 1px solid #004AC1;background-color: #004AC1; border-radius: 3px;padding: 0; ">
                                    <div class="box-header with-border" style="background-color: white; color: black; text-align: center;">
                                        <div class="row">
                                            <div class="col-md-3">

                                                  @php
                                                    $dia =  Date('N',strtotime($orden->created_at));
                                                    $mes =  Date('n',strtotime($orden->created_at));
                                                  @endphp

                                                  <span style="font-family: 'Helvetica'; font-size: 14px" class="box-title" >
                                                  @if($dia == '1') Lunes
                                                    @elseif($dia == '2') Martes
                                                    @elseif($dia == '3') Miércoles
                                                    @elseif($dia == '4') Jueves
                                                    @elseif($dia == '5') Viernes
                                                    @elseif($dia == '6') Sábado
                                                    @elseif($dia == '7') Domingo
                                                  @endif
                                                  {{substr($orden->created_at,8,2)}} de
                                                  @if($mes == '1') Enero
                                                     @elseif($mes == '2') Febrero
                                                     @elseif($mes == '3') Marzo
                                                     @elseif($mes == '4') Abril
                                                     @elseif($mes == '5') Mayo
                                                     @elseif($mes == '6') Junio
                                                     @elseif($mes == '7') Julio
                                                     @elseif($mes == '8') Agosto
                                                     @elseif($mes == '9') Septiembre
                                                     @elseif($mes == '10') Octubre
                                                     @elseif($mes == '11') Noviembre
                                                     @elseif($mes == '12') Diciembre
                                                  @endif
                                                  del {{substr($orden->created_at,0,4)}}
                                                </span>

                                            </div>
                                            <div class="col-md-3">

                                              <span style="font-family: 'Helvetica'; font-size: 14px" >
                                                  {{$orden->tipo->descripcion}}
                                              </span>

                                            </div>
                                            <div class="col-md-2">
                                                <a href="{{route('ordenbiopsiasptv.imprimir',['id'=>$orden->id])}}" target="_blank" class="btn btn-success btn-xs">Imprimir</a>
                                            </div>
                                            <div class="col-md-1">
                                              <label style="font-weight:normal" for="">Fecha de imprensión</label>
                                            </div>
                                            <div class="col-md-2">
                                               <input type="date" name="fecha" id="fecha_imprension{{$orden->id}}" value="{{$orden->fecha_imprimible}}" class="form-control" onchange="cambiarFecha({{$orden->id}})">
                                            </div>
                                        </div>
                                        <div class="pull-right box-tools">
                                            <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="open_biopsias_ptv_2{{$orden->id}}">
                                                <i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <!--div class="card-body" id="labs_detalle{{$orden->id}}" style="padding: 0px;"-->
                                    <div class="box-body" id="detalle_biopsia_ptv{{$orden->id}}" style="background: white;">
                                        <div class="row">
                                            <div class="col-md-6"><b>Otras Localizaciones</b></div>
                                            <div class="col-md-6">{{$orden->otras_localizaciones}}</div>
                                            <div class="col-md-6"><b>Otros Organos</b></div>
                                            <div class="col-md-6">{{$orden->otros_organos}}</div>
                                            <div class="col-md-12"><b>Datos Clínicos</b></div>
                                            <div class="col-md-12"><?php echo $orden->datos_clinicos; ?></div>
                                            <div class="col-md-12"><b>Diagnóstico</b></div>
                                            <div class="col-md-12"><?php echo $orden->diagnostico; ?></div>
                                            @foreach($orden->detalles as $detalle)
                                            <div class="col-md-6">
                                                <b>{{$detalle->descripcion}}:</b> {{$detalle->detalle}}
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                @endforeach
                                @endforeach

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

<script type="text/javascript">
   const cambiarFecha = (orden)=>{

     let fecha = document.getElementById("fecha_imprension"+orden).value;

     $.ajax({
              url: "{{route('paciente.historial_impresion')}}",
              headers: {
                  'X-CSRF-TOKEN': $('input[name=_token]').val()
              },
              data: {
                'id_procedimiento':orden,
                'fecha':fecha,
              },
              type: 'POST',
              dataType: 'json',
              success: function(data) {
                if(data){
                  Swal.fire({
                      position: 'center',
                      icon: 'success',
                      title: 'Cambiada la fecha de emisión',
                      showConfirmButton: false,
                      timer: 1500
                  })

                }else{
                  console.log(data);
                }

              },
              error: function(xhr, status) {
                  alert('Existió un problema');
                  //console.log(xhr);
              },
          });
   }
</script>
