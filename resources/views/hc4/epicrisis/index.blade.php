<style type="text/css">
  .parent{
      overflow-y:scroll;
      height: 600px;
  }
  .parent::-webkit-scrollbar {
      width: 8px;
  } /* this targets the default scrollbar (compulsory) */
  .parent::-webkit-scrollbar-thumb {
      background: #004AC1;
      border-radius: 10px;
  }
  .parent::-webkit-scrollbar-track {
    width: 10px;
      background-color: #004AC1;
      box-shadow: inset 0px 0px 0px 3px #56ABE3;
  } /* the new scrollbar will have a flat appearance with the set background color */
  .parent::-webkit-scrollbar-track-piece{
    width: 2px;
      background-color: none;
  }

  .parent::-webkit-scrollbar-button {
        background-color: none;
  } /* optionally, you can style the top and the bottom buttons (left and right for horizontal bars) */

  .parent::-webkit-scrollbar-corner {
        background-color: none;
  } /* if both the vertical and the horizontal bars appear, then perhaps the right bottom corner also needs to be styled */

  .btn-block{
      background-color: #004AC1;
    }
     .table>tbody>tr>td, .table>tbody>tr>th {
        padding: 0.4% ;
    }



    .ui-corner-all
    {
        -moz-border-radius: 4px 4px 4px 4px;
    }

    .ui-widget
    {
        font-family: Verdana,Arial,sans-serif;
        font-size: 15px;
    }
    .ui-menu
    {
        display: block;
        float: left;
        list-style: none outside none;
        margin: 0;
        padding: 2px;
    }
    .ui-autocomplete
    {
        overflow-x: hidden;
        max-height: 200px;
        width:1px;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        float: left;
        display: none;
        min-width: 160px;
        _width: 160px;
        padding: 4px 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #fff;
        border-color: #ccc;
        border-color: rgba(0, 0, 0, 0.2);
        border-style: solid;
        border-width: 1px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
        *border-right-width: 2px;
        *border-bottom-width: 2px;
    }
    .ui-menu .ui-menu-item
    {
        clear: left;
        float: left;
        margin: 0;
        padding: 0;
        width: 100%;
    }
    .ui-menu .ui-menu-item a
    {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        cursor: pointer;
        background-color: #ffffff;
    }
    .ui-menu .ui-menu-item a:hover
    {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        color: White;
        cursor: pointer;
        background-color: #006699;
    }
    .ui-widget-content a
    {
        color: #222222;
    }

    .mce-edit-focus,
    .mce-content-body:hover {
        outline: 2px solid #2276d2 !important;
    }

    .select2-selection--multiple{
        background-color: white !important;
    }

    .btn_agregar_diag{
      color: white;
      background-color: green;
    }
    .alerta_correcto{
    position: absolute;
    z-index: 9999;
    top: 100px;
    right: 10px;
  }
</style>
@php
  $ip_cliente= $_SERVER["REMOTE_ADDR"];
    $idusuario = Auth::user()->id;
@endphp


<div class="box " style="border: 2px solid #004AC1; background-color: white; ">
  <div class="box-header with-border" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004AC1; ">
    <div class="row">
      <div class="col-md-9 col-sm-8 col-12">
        <h1 style="font-size: 15px; margin:0; background-color: #004AC1; color: white;" >
              <img style="width: 35px; margin-left: 5px; margin-bottom: 5px" src="{{asset('/')}}hc4/img/iconos/pendo.png">
              <b>EPICRISIS</b>
      </h1>
    </div>

  </div>
  @if(!is_null($paciente))
    <center>
      <div class="col-12" style="padding-top: 15px">
        <h1 style="font-size: 14px; margin:0; background-color: #004AC1; color: white;padding-left: 20px" >
              <b>PACIENTE : {{$paciente->apellido1}} {{$paciente->apellido2}}
                {{$paciente->nombre1}} {{$paciente->nombre2}}
                  </b>
        </h1>
        </div>
    </center>
  @endif
    <!-- /.box-tools -->
  </div>
  <!-- /.box-header -->
  <div class="box-body" style="background-color: #56ABE3;">
    <div class="col-12">
      <div class="row parent" >

        @foreach($procedimientos2 as $value)

          <div class="col-12">
            @php
              if(!is_null($value->f_operacion)){
                $fecha_r =  Date('Y-m-d',strtotime($value->f_operacion));
              }else{
                $fecha_r = Date('Y-m-d',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini));
              }
            @endphp
            <div class="box @if($fecha_r != date('Y-m-d') ) collapsed-box @endif" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 0px; ">
            <div class="box-header with-border" style="background-color: white; color: black; font-family: 'Helvetica general3';border-bottom: #004AC1;">

              @php
                  $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->id_procedimiento)->get();
                  $mas = true;
                  $texto = "";

                            foreach($adicionales as $value2)
                            {
                      if($mas == true){
                       $texto = $texto.$value2->procedimiento->nombre  ;
                       $mas = false;
                       }
                      else{
                         $texto = $texto.' + '.  $value2->procedimiento->nombre  ;
                         }
                    }
                @endphp
              <div class="row">
                <div class="col-4">
                  @if(!is_null($value->f_operacion))
                                @php
                                $dia =  Date('N',strtotime($value->f_operacion));
                                $mes =  Date('n',strtotime($value->f_operacion)); @endphp
                              <b>
                                @if($dia == '1') Lunes
                                     @elseif($dia == '2') Martes
                                     @elseif($dia == '3') Miércoles
                                     @elseif($dia == '4') Jueves
                                     @elseif($dia == '5') Viernes
                                     @elseif($dia == '6') Sábado
                                     @elseif($dia == '7') Domingo
                                @endif
                                  {{substr($value->f_operacion,8,2)}} de
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
                                  del {{substr($value->f_operacion,0,4)}}</b>
                            @else
                              @php
                                $dia =  Date('N',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini));
                                $mes =  Date('n',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini)); @endphp
                              <b>
                                @if($dia == '1') Lunes
                                     @elseif($dia == '2') Martes
                                     @elseif($dia == '3') Miércoles
                                     @elseif($dia == '4') Jueves
                                     @elseif($dia == '5') Viernes
                                     @elseif($dia == '6') Sábado
                                     @elseif($dia == '7') Domingo
                                @endif
                                  {{substr(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini,8,2)}} de
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
                                  del {{substr(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini,0,4)}}</b>
                            @endif
                          </div>
                <div class="col-4">
                  <div>
                    <span style="font-family: 'Helvetica general'; font-size: 12px">Procedimiento:</span>
                    <span style="font-size: 12px">@if(!is_null($texto)) {{$texto}} @endif </span>
                  </div>
                </div>
                <div class="col-3">
                  <div>
                    <span style="font-family: 'Helvetica general'; font-size: 12px">Dr (a) </span>
                    <span style="font-size: 12px">
                    @if(!is_null($value->nombre1))
                      {{$value->nombre1}} {{$value->apellido1}}
                    @else
                      @php
                        $doc_id_hc = \Sis_medico\Historiaclinica::where('hcid', $value->hcid)->first();
                      @endphp

                      @if(!is_null($doc_id_hc))
                        @if (!is_null($doc_id_hc->id_doctor1))
                          @php
                            $nombre_doc_hc = \Sis_medico\User::where('id', $doc_id_hc->id_doctor1)->first();
                          @endphp

                          Dr. {{$nombre_doc_hc->nombre1}} {{$nombre_doc_hc->apellido1}}

                        @else
                          @php
                            $doc_id_agenda = \Sis_medico\Agenda::where('id', $doc_id_hc->id_agenda)->first();

                            $nombre_doc_agenda = \Sis_medico\User::where('id', $doc_id_agenda->id_doctor1)->first();
                          @endphp
                          Dr. {{$nombre_doc_agenda->nombre1}} {{$nombre_doc_agenda->apellido1}}
                        @endif
                      @endif
                    @endif

                    </span>
                  </div>
                </div>
                      </div>
                      <div style="color: white">
                           {{$value->id_protocolo}}
                        </div>
                      <div class="pull-right box-tools ">
                            <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
                                <i class="fa fa-plus"></i>
                            </button>
                      </div>
                <!-- /.box-tools -->
            </div>
            <div class="box-body" style="background: white;">
                @php
                  //dd($value->id_procedimiento);
                  $reporte_epic = \Sis_medico\Hc_Epicrisis::where('hc_id_procedimiento', $value->id_procedimiento)->first();
                  //dd($reporte_epic->id);
                @endphp
              <div class="row">
                <div class="col-12" style="padding-top: 10px">

                  <div class="box box-primary " style=" border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
                    <div class="col-md-12">
                      <label style="color: white">RESUMEN DEL CUADRO CLINICO</label>
                    </div>
                  </div>
                  <div class="col-md-12" style="padding-left: 15px">
                    <span style="font-family: 'Helvetica general';">Evolucion Pre</span>
                  </div>
                    @php
                      $evoluciones_pre1 = \Sis_medico\Hc_Evolucion::where('hc_id_procedimiento', $value->id_procedimiento)->where('secuencia', '0')->first();
                    @endphp
                    <div class="form-group col-md-12" >
                        <div id="pre" > @if(!is_null($evoluciones_pre1)) <?php echo $evoluciones_pre1->cuadro_clinico ?> @endif </div>
                    </div>

                    <div class="col-md-12" style="padding-left: 15px">
                      <span style="font-family: 'Helvetica general';">Evolucion Post</span>
                    </div>
                      @php
                        $evoluciones_post1 = \Sis_medico\Hc_Evolucion::where('hc_id_procedimiento', $value->id_procedimiento)->where('secuencia', '1')->first();
                      @endphp
                      <div class="form-group col-md-12" >
                          <div id="post" >@if(!is_null($evoluciones_post1)) <?php echo $evoluciones_post1->cuadro_clinico ?> @endif</div>
                      </div>
                      <div class="col-md-12" style="padding-left: 15px">
                        <span style="font-family: 'Helvetica general';">Conclusion</span>
                      </div>
                      <div class="form-group col-md-12" >
                          <div id="post" >@if(!is_null($value->conclusion)) <?php echo $value->conclusion ?> @endif</div>
                      </div>
                </div>

                <div class="col-12" style="padding-top: 10px">
                  <div class="box box-primary " style=" border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
                    @php
                      $epic_condiciones1 = \Sis_medico\Hc_Epicrisis::where('hc_id_procedimiento', $value->id_procedimiento)->first();
                      //dd($epic_condiciones1->alta);
                    @endphp
                    <div class="col-md-12">
                      <label style="color: white">CONDICIONES DE EGRESO Y PRONOSTICO</label>
                    </div>
                  </div>
                  <div class="col-md-12">
                  <div class="row">
                    <div class="col-md-3" style="padding-left: 15px">
                        <span style="font-family: 'Helvetica general';">ALTA</span>
                        <div class="form-group col-md-12" >@if(!is_null($epic_condiciones1)){{$epic_condiciones1->alta}}@endif</div>
                    </div>

                    <div class="col-md-3" style="padding-left: 15px">
                        <span style="font-family: 'Helvetica general';">DISCAPACIDAD</span>
                        <div class="form-group col-md-12" >@if(!is_null($epic_condiciones1)){{$epic_condiciones1->discapacidad}}@endif</div>
                    </div>

                    <div class="col-md-3" style="padding-left: 15px">
                        <span style="font-family: 'Helvetica general';">RETIRO</span>
                        <div class="form-group col-md-12" >@if(!is_null($epic_condiciones1)){{$epic_condiciones1->retiro}}@endif</div>
                    </div>

                    <div class="col-md-3" style="padding-left: 15px">
                        <span style="font-family: 'Helvetica general';">DEFUNCIÓN</span>
                        <div class="form-group col-md-12" >@if(!is_null($epic_condiciones1)){{$epic_condiciones1->defuncion}}@endif</div>
                    </div>

                    <div class="col-md-3" style="padding-left: 15px">
                        <span style="font-family: 'Helvetica general';">DIAS DE ESTADIA</span>
                        <div class="form-group col-md-12" >@if(!is_null($epic_condiciones1)){{$epic_condiciones1->dias_estadia}}@endif</div>
                    </div>

                    <div class="col-md-3" style="padding-left: 15px">
                      <span style="font-family: 'Helvetica general';">DIAS INCAPACIDAD</span>
                      <div class="form-group col-md-12" >@if(!is_null($epic_condiciones1)){{$epic_condiciones1->dias_incapacidad}}@endif</div>
                    </div>
                  </div>
                  </div>
                </div>

                <div class="col-12" style="padding-top: 10px">
                  <div class="box box-primary " style=" border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
                    <div class="col-md-12">
                      <label style="color: white">DIAGNÓSTICO PRESUNTIVO/DEFINITIVO</label>
                    </div>
                  </div>
                  <div class="col-md-12">
                    @php $hc_cie101 = null ;  @endphp
                    @if(!is_null($value))
                    @php
                      $hc_cie101 = DB::table('hc_cie10')->where('hc_id_procedimiento',$value->id_procedimiento)->get();
                    @endphp
                    @endif

                    @if(!is_null($hc_cie101))
                      @foreach($hc_cie101 as $cie10)
                        @php $c10_1 = DB::table('cie_10_3')->where('id',$cie10->cie10)->first(); @endphp
                          @if(!is_null($c10_1))
                            <div class="row">
                              <div class="col-md-1">{{$cie10->cie10}}</div>
                              <div class="col-md-7">{{$c10_1->descripcion}}</div>
                              <div class="col-md-2">{{$cie10->ingreso_egreso}}</div>
                              <div class="col-md-2">{{$cie10->presuntivo_definitivo}}</div>
                            </div>
                          @endif
                        @php $c10_1 = DB::table('cie_10_4')->where('id',$cie10->cie10)->first(); @endphp
                          @if(!is_null($c10_1))
                            <div class="row">
                              <div class="col-md-1">{{$cie10->cie10}}</div>
                              <div class="col-md-7">{{$c10_1->descripcion}}</div>
                              <div class="col-md-2">{{$cie10->ingreso_egreso}}</div>
                              <div class="col-md-2">{{$cie10->presuntivo_definitivo}}</div>
                            </div>
                          @endif
                      @endforeach
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
          </div>

        @endforeach

        @foreach($procedimientos1 as $value)
          <div class="col-12">
            <div class="box collapsed-box" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
            <div class="box-header with-border" style="background-color: white; color: black; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
                <div class="row">
                  <div class="col-4">
                    @if(!is_null($value->f_operacion))
                                @php
                                $dia =  Date('N',strtotime($value->f_operacion));
                                $mes =  Date('n',strtotime($value->f_operacion)); @endphp
                                    <b>
                                      @if($dia == '1') Lunes
                                           @elseif($dia == '2') Martes
                                           @elseif($dia == '3') Miércoles
                                           @elseif($dia == '4') Jueves
                                           @elseif($dia == '5') Viernes
                                           @elseif($dia == '6') Sábado
                                           @elseif($dia == '7') Domingo
                                      @endif
                                        {{substr($value->f_operacion,8,2)}} de
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
                                        del {{substr($value->f_operacion,0,4)}}</b>
                                  @else
                                    @php
                                $dia =  Date('N',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini));
                                $mes =  Date('n',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini)); @endphp
                                    <b>
                                      @if($dia == '1') Lunes
                                           @elseif($dia == '2') Martes
                                           @elseif($dia == '3') Miércoles
                                           @elseif($dia == '4') Jueves
                                           @elseif($dia == '5') Viernes
                                           @elseif($dia == '6') Sábado
                                           @elseif($dia == '7') Domingo
                                      @endif
                                        {{substr(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini,8,2)}} de
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
                                        del {{substr(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini,0,4)}}</b>
                              @endif
                          </div>
                          <div class="col-4">
                  <div>
                    <span style="font-family: 'Helvetica general'; font-size: 12px">Procedimiento:</span>
                    <span style="font-size: 12px"> {{$value->nombre}} </span>
                  </div>
                </div>
                <div class="col-3">
                  <div>
                    <span style="font-family: 'Helvetica general'; font-size: 12px">Dr (a) </span>
                    <span style="font-size: 12px">
                    @if(!is_null($value->nombre1))
                      {{$value->nombre1}} {{$value->apellido1}}
                    @else
                      @php
                        $doc_id_hc = \Sis_medico\Historiaclinica::where('hcid', $value->hcid)->first();
                      @endphp

                      @if(!is_null($doc_id_hc))
                        @if (!is_null($doc_id_hc->id_doctor1))
                          @php
                            $nombre_doc_hc = \Sis_medico\User::where('id', $doc_id_hc->id_doctor1)->first();
                          @endphp

                          Dr. {{$nombre_doc_hc->nombre1}} {{$nombre_doc_hc->apellido1}}

                        @else
                          @php
                            $doc_id_agenda = \Sis_medico\Agenda::where('id', $doc_id_hc->id_agenda)->first();

                            $nombre_doc_agenda = \Sis_medico\User::where('id', $doc_id_agenda->id_doctor1)->first();
                          @endphp
                          Dr. {{$nombre_doc_agenda->nombre1}} {{$nombre_doc_agenda->apellido1}}
                        @endif
                      @endif
                    @endif
                    </span>
                  </div>
                </div>
                </div>
                <div style="color: white">
                   {{$value->id_protocolo}}
                </div>
                <div class="pull-right box-tools">
                    <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
                        <i class="fa fa-plus"></i>
                      </button>
                </div>
                <!-- /.box-tools -->
            </div>
            <div class="box-body" style="background: white;">
              <div class="row">

                <div class="col-12" style="padding-top: 10px">
                  <div class="box box-primary " style=" border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
                    <div class="col-md-12">
                      <label style="color: white">RESUMEN DEL CUADRO CLINICO</label>
                    </div>
                  </div>
                  <div class="col-md-12" style="padding-left: 15px">
                    <span style="font-family: 'Helvetica general';">Evolucion Pre</span>
                  </div>
                    @php
                      $evoluciones_pre2 = \Sis_medico\Hc_Evolucion::where('hc_id_procedimiento', $value->id_procedimiento)->where('secuencia', '0')->first();
                    @endphp
                    <div class="form-group col-md-12" >
                        <div id="pre" >@if(!is_null($evoluciones_pre2)) <?php echo $evoluciones_pre2->cuadro_clinico ?> @endif</div>
                    </div>

                    <div class="col-md-12" style="padding-left: 15px">
                      <span style="font-family: 'Helvetica general';">Evolucion Post</span>
                    </div>
                      @php
                        $evoluciones_post2 = \Sis_medico\Hc_Evolucion::where('hc_id_procedimiento', $value->id_procedimiento)->where('secuencia', '1')->first();
                      @endphp
                      <div class="form-group col-md-12" >
                          <div id="post" >@if(!is_null($evoluciones_post2)) <?php echo $evoluciones_post2->cuadro_clinico ?> @endif </div>
                      </div>
                      <div class="col-md-12" style="padding-left: 15px">
                        <span style="font-family: 'Helvetica general';">Conclusion</span>
                      </div>
                      <div class="form-group col-md-12" >
                          <div id="post" >@if(!is_null($value)) <?php echo $value->conclusion ?> @endif </div>
                      </div>
                </div>

                <div class="col-12" style="padding-top: 10px">
                  <div class="box box-primary " style=" border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
                    @php
                      $epic_condiciones2 = \Sis_medico\Hc_Epicrisis::where('hc_id_procedimiento', $value->id_procedimiento)->first();

                    @endphp
                    <div class="col-md-12">
                      <label style="color: white">CONDICIONES DE EGRESO Y PRONOSTICO</label>
                    </div>
                  </div>
                  <div class="col-md-12">
                  <div class="row">
                    <div class="col-md-3" style="padding-left: 15px">
                        <span style="font-family: 'Helvetica general';">ALTA</span>
                        <div class="form-group col-md-12" >@if(!is_null($epic_condiciones2)){{$epic_condiciones2->alta}}@endif</div>
                    </div>

                    <div class="col-md-3" style="padding-left: 15px">
                        <span style="font-family: 'Helvetica general';">DISCAPACIDAD</span>
                        <div class="form-group col-md-12" >@if(!is_null($epic_condiciones2)){{$epic_condiciones2->discapacidad}}@endif</div>
                    </div>

                    <div class="col-md-3" style="padding-left: 15px">
                        <span style="font-family: 'Helvetica general';">RETIRO</span>
                        <div class="form-group col-md-12" >@if(!is_null($epic_condiciones2)){{$epic_condiciones2->retiro}}@endif</div>
                    </div>

                    <div class="col-md-3" style="padding-left: 15px">
                        <span style="font-family: 'Helvetica general';">DEFUNCIÓN</span>
                        <div class="form-group col-md-12" >@if(!is_null($epic_condiciones2)){{$epic_condiciones2->defuncion}}@endif</div>
                    </div>

                    <div class="col-md-3" style="padding-left: 15px">
                        <span style="font-family: 'Helvetica general';">DIAS DE ESTADIA</span>
                        <div class="form-group col-md-12" >@if(!is_null($epic_condiciones2)){{$epic_condiciones2->dias_estadia}}@endif</div>
                    </div>

                    <div class="col-md-3" style="padding-left: 15px">
                      <span style="font-family: 'Helvetica general';">DIAS INCAPACIDAD</span>
                      <div class="form-group col-md-12" >@if(!is_null($epic_condiciones2)){{$epic_condiciones2->dias_incapacidad}}@endif</div>
                    </div>
                  </div>
                  </div>
                </div>

                <div class="col-12" style="padding-top: 10px">
                  <div class="box box-primary " style=" border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
                    <div class="col-md-12">
                      <label style="color: white">DIAGNÓSTICO PRESUNTIVO/DEFINITIVO</label>
                    </div>
                  </div>
                  <div class="col-md-12">
                    @php $hc_cie102 = null ;  @endphp
                    @if(!is_null($value))
                    @php
                      $hc_cie102 = DB::table('hc_cie10')->where('hc_id_procedimiento',$value->id_procedimiento)->get();
                    @endphp
                    @endif

                    @if(!is_null($hc_cie102))
                      @foreach($hc_cie102 as $cie10)
                        @php $c10_2 = DB::table('cie_10_3')->where('id',$cie10->cie10)->first(); @endphp
                          @if(!is_null($c10_2))
                            <div class="row">
                              <div class="col-md-1">{{$cie10->cie10}}</div>
                              <div class="col-md-7">{{$c10_2->descripcion}}</div>
                              <div class="col-md-2">{{$cie10->ingreso_egreso}}</div>
                              <div class="col-md-2">{{$cie10->presuntivo_definitivo}}</div>
                            </div>
                          @endif
                        @php $c10_2 = DB::table('cie_10_4')->where('id',$cie10->cie10)->first(); @endphp
                          @if(!is_null($c10_2))
                            <div class="row">
                              <div class="col-md-1">{{$cie10->cie10}}</div>
                              <div class="col-md-7">{{$c10_2->descripcion}}</div>
                              <div class="col-md-2">{{$cie10->ingreso_egreso}}</div>
                              <div class="col-md-2">{{$cie10->presuntivo_definitivo}}</div>
                            </div>
                          @endif
                      @endforeach
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
  <!-- box-footer -->
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>



