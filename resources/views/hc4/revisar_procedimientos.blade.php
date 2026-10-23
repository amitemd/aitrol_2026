<style type="text/css">
  .colorx2{
    background-color: #e6f9ff;
  }
  .table>tbody>tr>td{
    padding-top: 5px;
    padding-bottom: 5px;
  }


</style>

  

<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
  
  
<!-- Ventana modal editar -->
<div class="modal fade" id="mlog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document" style="width: 110% !important">
    <div class="modal-content">
      
    </div>
  </div>
</div>
<div class="row" style="margin: 0;">  
  <div class="col-lg-6 col-6"><h4>@lang('hc4.revisarProcedimientos') @lang('hc4.desde'): {{$desde}} @lang('hc4.hasta'): {{$hasta}}</h4></div>
  <div class="col-lg-6 col-6 color" style="padding-left: 15px;font-size: 15px">@lang('hc4.totalRegistros'): {{$procedimientos->count()}}</div>
</div>
<div class="box box-success">
  <div class="box-header with-border">
    <h3 class="box-title">@lang('hc4.procedimientosEndoscopicos')</h3>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
      </button>
      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
    <table  id="example2" class="table" cellspacing="0" width="100%" style="font-size: 12px;">
      <thead style="">
        <tr style=" ">
          <th scope="col" class="color titulo" width="10%" >@lang('hc4.fecha') / @lang('hc4.hora')</th>
          <th scope="col" class="color titulo" width="8%" >@lang('hc4.cedula')</th>
          <th scope="col" class="color titulo" width="10%" >@lang('hc4.apellidos')</th>
          <th scope="col" class="color titulo" width="10%" >@lang('hc4.nombres')</th>
          <th scope="col" class="color titulo" width="8%" >@lang('hc4.edad')</th>
          <th scope="col" class="color titulo" width="5%" >@lang('hc4.seguro')</th>
          <th scope="col" class="color titulo" width="22%" >@lang('hc4.procedimientos')</th>
          <th scope="col" class="color titulo" width="5%" >@lang('hc4.amb')/@lang('hc4.hosp')</th>
          <th scope="col" class="color titulo" width="7%" >@lang('hc4.estado')</th>
          <th scope="col" class="color titulo" width="10%" >@lang('hc4.revisado')</th>
          <th scope="col" class="color titulo" width="5%" >@lang('hc4.accion')</th>
        </tr>
      </thead>
      <tbody>

        @foreach($procedimientos as $procedimiento)
          @php
            $edad= 0;$mostrar=false;$tiene_wave = 0;$tiene_albumina=1;
            if ($procedimiento->paciente->fecha_nacimiento != null) {
              $edad = Carbon\Carbon::createFromDate(substr($procedimiento->paciente->fecha_nacimiento, 0, 4), substr($procedimiento->paciente->fecha_nacimiento, 5, 2), substr($procedimiento->paciente->fecha_nacimiento, 8, 2))->age;
            }
            $resto_procs = Sis_medico\AgendaProcedimiento::where('id_agenda',$procedimiento->id)->get();
            $pentax = Sis_medico\Pentax::where('id_agenda',$procedimiento->id)->first();
            $pcant = 0;
            if(!is_null($pentax)){
              $pentax_procs = Sis_medico\PentaxProc::where('id_pentax',$pentax->id)->get();
              $pcant = $pentax_procs->count();
              foreach($pentax_procs as $px1){
                if($px1->id_procedimiento=='145'){
                  $tiene_wave = 1;
                }
                if(!is_null($px1->procedimiento->id_grupo_procedimiento)){
                  if($px1->procedimiento->grupo_procedimiento->tipo_procedimiento=='0'){
                    $mostrar=true;
                    
                  }
                }
              }  
            }else{
              if(!is_null($procedimiento->procedimiento->id_grupo_procedimiento)){
                if($procedimiento->procedimiento->grupo_procedimiento->tipo_procedimiento=='0'){
                  $mostrar=true;
                }
              }
              foreach($resto_procs as $px1){
                if($px1->id_procedimiento=='145'){
                  $tiene_wave = 1;
                }
                if(!is_null($px1->procedimiento->id_grupo_procedimiento)){
                  if($px1->procedimiento->grupo_procedimiento->tipo_procedimiento=='0'){
                    $mostrar=true;
                    
                  }
                }      
              }
            }
            $fecha_antes   = Date('Y-m-d', strtotime('- 1 month', strtotime($procedimiento->fechaini)));
            if($tiene_wave){ $tiene_albumina = 0;
              $ordenes = Sis_medico\Examen_Orden::where('id_paciente', $procedimiento->id_paciente)->whereBetween('created_at', [$fecha_antes, $procedimiento->fechaini])->where('estado','1')->get();
              foreach($ordenes as $orden){
                $detalles = $orden->detalles;
                foreach($detalles as $detalle){
                  if($detalle->id_examen=='417'){
                    $tiene_albumina = '1';
                    break;
                  }
                }     
              }

            }

          @endphp
          @if($mostrar)
            <tr>
              <td class="color">{{$procedimiento->fechaini}}</td>
              <td class="color">{{$procedimiento->id_paciente}}</td>
              <td class="color">{{$procedimiento->paciente->apellido1}} {{$procedimiento->paciente->apellido2}}</td>
              <td class="color">{{$procedimiento->paciente->nombre1}} {{$procedimiento->paciente->nombre2}}</td>
              <td class="color">{{$edad}}</td>
              <td class="color">{{$procedimiento->seguro->nombre}}</td>
              <td class="color">@if($pcant<=0 )<span class="label" style="padding-right: 5px;padding-left: 5px;background-color: green;@if($procedimiento->supervisa_robles) background-color: red; @endif @if($procedimiento->solo_robles) background-color: purple; @endif color: white;">{{$procedimiento->procedimiento->observacion}}</span>  @foreach($resto_procs as $val) <span class="label " style="padding-right: 5px;padding-left: 5px;background-color: green;@if($procedimiento->supervisa_robles) background-color: red; @endif @if($procedimiento->solo_robles) background-color: purple; @endif color: white;">{{Sis_medico\Procedimiento::find($val->id_procedimiento)->observacion}}</span> @endforeach @else @foreach($pentax_procs as $val) <span class="label " style="padding-right: 5px;padding-left: 5px;background-color: green;@if($procedimiento->supervisa_robles) background-color: red; @endif @if($procedimiento->solo_robles) background-color: purple; @endif color: white;">{{Sis_medico\Procedimiento::find($val->id_procedimiento)->observacion}}</span> @endforeach @endif</td>
              <td class="color">@if($procedimiento->est_amb_hos == 0) @lang('hc4.amb') @else @lang('hc4.hosp') @endif</td>
              <td class="color">@if($procedimiento->estado_cita=='0') @lang('hc4.porConfirmar') @elseif($procedimiento->estado_cita=='1') @lang('hc4.confirmada') @elseif($procedimiento->estado_cita=='2') @lang('hc4.reagendado') @elseif($procedimiento->estado_cita=='3') @lang('hc4.suspendido') @elseif($procedimiento->estado_cita=='4') @lang('hc4.preadmisionado') @endif</td>
              <td class="color">
                <select id="revisado{{$procedimiento->id}}" name="revisado{{$procedimiento->id}}" onclick="revisado_si_no(this);">
                  <option @if($procedimiento->revisado=='0') selected @endif value="0">NO</option>
                  <option @if($procedimiento->revisado=='1') selected @endif value="1">SI</option>
                </select>
                @if(!$tiene_albumina)<span class="label" style="padding-right: 5px;padding-left: 5px;background-color: red;color: white;">@lang('hc4.albumina')</span>@endif
              </td>
              <td>
                <button class="btn btn-success btn-sm" onclick="mostrar_detalle({{$procedimiento->id}});"><span id="b{{$procedimiento->id}}" class="glyphicon glyphicon-plus"></span></button>
              </td>
            </tr>
            <tr>
              <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;" ><b style="color: green;">@lang('hc4.diagnostico')</b></td>
              <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;" colspan="3"><input style="width: 100%" type="text" name="diagnostico{{$procedimiento->id}}" id="diagnostico{{$procedimiento->id}}" onchange="revisar_formulario({{$procedimiento->id}})" maxlength="200" value="{{$procedimiento->diagnostico_proc}}"></td>
              <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;"><b style="color: green;">@lang('hc4.observacion')</b></td>
              <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;" colspan="3"><input style="width: 100%" type="text" name="observacion{{$procedimiento->id}}" id="observacion{{$procedimiento->id}}" onchange="revisar_formulario({{$procedimiento->id}})" maxlength="200" value="{{$procedimiento->observacion_proc}}"></td> 
              <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;" colspan="1"><button class="btn btn-success btn-sm" onclick="revisar_formulario({{$procedimiento->id}})"><span class="glyphicon glyphicon-floppy-saved"></span></button></td>
              <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;" colspan="1"><span style="color: red"> SUP: </span><input id="sup{{$procedimiento->id}}" name="sup{{$procedimiento->id}}" type="checkbox" class="flat-red" @if($procedimiento->supervisa_robles) checked @endif ><span style="color: purple"> CRM: </span><input id="crm{{$procedimiento->id}}" name="crm{{$procedimiento->id}}" type="checkbox" class="flat-purple" @if($procedimiento->solo_robles) checked @endif ></td> 
              <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;" colspan="1">
                <a class="btn btn-primary btn-sm" href="{{route('nd.buscador', ['id_paciente' => $procedimiento->id_paciente])}}" target="_blank">@lang('hc4.detalle')</a>
              </td>  
            </tr>
            <tr>
              <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;" ><b style="color: green;">('hc4.procedimientos')</b></td>
              <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;" colspan="9">
                <select class="form-control select2 input-sm" multiple="multiple" name="proc{{$procedimiento->id}}[]" id="proc{{$procedimiento->id}}" data-placeholder="@lang('hc4.seleccioneProcedimientos')" required style="width: 100%;">
                @if($pcant<=0 )  
                  <option selected value="{{$procedimiento->procedimiento->id}}">{{$procedimiento->procedimiento->observacion}}</option>
                  @foreach($resto_procs as $val)
                    <option selected value="{{$val->id_procedimiento}}">{{Sis_medico\Procedimiento::find($val->id_procedimiento)->observacion}}</option>
                  @endforeach 
                  @foreach($pxs as $val)
                    <option value="{{$val->id}}">{{$val->observacion}}</option>
                  @endforeach 
                @else
                  @foreach($pentax_procs as $val)
                    <option selected value="{{$val->id_procedimiento}}">{{Sis_medico\Procedimiento::find($val->id_procedimiento)->observacion}}</option>
                  @endforeach
                  @foreach($pxs as $val)
                    <option value="{{$val->id}}">{{$val->observacion}}</option>
                  @endforeach 
                @endif     
                </select>
              </td>
              <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;" >
                <a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#mlog" data-remote="{{route('hc4controller.agenda_log', ['id_agenda' => $procedimiento->id])}}" style="color: white">Log</a>
              </td>
            </tr> 
          @endif     
        @endforeach
      </tbody>

    </table>
  </div>
</div> 

<div class="box box-success">
  <div class="box-header with-border">
    <h3 class="box-title">@lang('hc4.procedimientosFuncionales')</h3>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
      </button>
      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body"> 
    <table  id="example2" class="table" cellspacing="0" width="100%" style="font-size: 12px;">
      <thead style="">
        <tr style=" ">
          <th scope="col" class="color titulo" width="10%" >@lang('hc4.fecha') / @lang('hc4.hora')</th>
          <th scope="col" class="color titulo" width="8%" >@lang('hc4.cedula')</th>
          <th scope="col" class="color titulo" width="10%" >@lang('hc4.apellidos')</th>
          <th scope="col" class="color titulo" width="10%" >@lang('hc4.nombres')</th>
          <th scope="col" class="color titulo" width="8%" >@lang('hc4.edad')</th>
          <th scope="col" class="color titulo" width="5%" >@lang('hc4.seguro')</th>
          <th scope="col" class="color titulo" width="22%" >@lang('hc4.procedimientos')</th>
          <th scope="col" class="color titulo" width="5%" >@lang('hc4.amb')/@lang('hc4.hosp')</th>
          <th scope="col" class="color titulo" width="7%" >@lang('hc4.estado')</th>
          <th scope="col" class="color titulo" width="10%" >@lang('hc4.revisado')</th>
          <th scope="col" class="color titulo" width="5%" >@lang('hc4.accion')</th>
        </tr>
      </thead>
      <tbody>
        @foreach($procedimientos as $procedimiento)
          @if($procedimiento->id_doctor1!='4444444444')
            @php
              $edad= 0;$mostrar=false;
              if ($procedimiento->paciente->fecha_nacimiento != null) {
                $edad = Carbon\Carbon::createFromDate(substr($procedimiento->paciente->fecha_nacimiento, 0, 4), substr($procedimiento->paciente->fecha_nacimiento, 5, 2), substr($procedimiento->paciente->fecha_nacimiento, 8, 2))->age;
              }
              $resto_procs = Sis_medico\AgendaProcedimiento::where('id_agenda',$procedimiento->id)->get();
              $pentax = Sis_medico\Pentax::where('id_agenda',$procedimiento->id)->first();
              $pcant = 0;
              if(!is_null($pentax)){
                $pentax_procs = Sis_medico\PentaxProc::where('id_pentax',$pentax->id)->get();
                $pcant = $pentax_procs->count();
                foreach($pentax_procs as $px1){
                  if(!is_null($px1->procedimiento->id_grupo_procedimiento)){
                    if($px1->procedimiento->grupo_procedimiento->tipo_procedimiento=='1'){
                      $mostrar=true;
                      
                    }
                  }
                }  
              }else{
                if(!is_null($procedimiento->procedimiento->id_grupo_procedimiento)){
                  if($procedimiento->procedimiento->grupo_procedimiento->tipo_procedimiento=='1'){
                    $mostrar=true;
                  }
                }
                foreach($resto_procs as $px1){
                  if(!is_null($px1->procedimiento->id_grupo_procedimiento)){
                    if($px1->procedimiento->grupo_procedimiento->tipo_procedimiento=='1'){
                      $mostrar=true;
                      
                    }
                  }      
                }
              }
            @endphp
            @if($mostrar)
              <tr>
                <td class="color">{{$procedimiento->fechaini}}</td>
                <td class="color">{{$procedimiento->id_paciente}}</td>
                <td class="color">{{$procedimiento->paciente->apellido1}} {{$procedimiento->paciente->apellido2}}</td>
                <td class="color">{{$procedimiento->paciente->nombre1}} {{$procedimiento->paciente->nombre2}}</td>
                <td class="color">{{$edad}}</td>
                <td class="color">{{$procedimiento->seguro->nombre}}</td>
                <td class="color">@if($pcant<=0 )<span class="label " style="padding-right: 5px;padding-left: 5px;background-color: green;@if($procedimiento->supervisa_robles) background-color: red; @endif @if($procedimiento->solo_robles) background-color: purple; @endif color: white;">{{$procedimiento->procedimiento->observacion}}</span>  @foreach($resto_procs as $val) <span class="label " style="padding-right: 5px;padding-left: 5px;background-color: green;@if($procedimiento->supervisa_robles) background-color: red; @endif @if($procedimiento->solo_robles) background-color: purple; @endif color: white;">{{Sis_medico\Procedimiento::find($val->id_procedimiento)->observacion}}</span> @endforeach @else @foreach($pentax_procs as $val) <span class="label " style="padding-right: 5px;padding-left: 5px;background-color: green;@if($procedimiento->supervisa_robles) background-color: red; @endif @if($procedimiento->solo_robles) background-color: purple; @endif color: white;">{{Sis_medico\Procedimiento::find($val->id_procedimiento)->observacion}}</span> @endforeach @endif</td>
                <td class="color">@if($procedimiento->est_amb_hos == 0) @lang('hc4.amb') @else @lang('hc4.hosp') @endif</td>
                <td class="color">@if($procedimiento->estado_cita=='0') @lang('hc4.porConfirmar') @elseif($procedimiento->estado_cita=='1') @lang('hc4.confirmada') @elseif($procedimiento->estado_cita=='2') @lang('hc4.reagendado') @elseif($procedimiento->estado_cita=='3') @lang('hc4.suspendido') @elseif($procedimiento->estado_cita=='4') @lang('hc4.preadmisionado') @endif</td>
                <td class="color">
                  <select id="revisado{{$procedimiento->id}}" name="revisado{{$procedimiento->id}}" onclick="revisado_si_no(this);">
                    <option @if($procedimiento->revisado=='0') selected @endif value="0">@lang('hc4.no')</option>
                    <option @if($procedimiento->revisado=='1') selected @endif value="1">@lang('hc4.si')</option>
                  </select>
                </td>
                <td>
                  <button class="btn btn-success btn-sm" onclick="mostrar_detalle({{$procedimiento->id}});"><span id="b{{$procedimiento->id}}" class="glyphicon glyphicon-plus"></span></button>
                </td>
              </tr>
              <tr>
                <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;" ><b style="color: green;">Diagnóstico</b></td>
                <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;" colspan="3"><input style="width: 100%" type="text" name="diagnostico{{$procedimiento->id}}" id="diagnostico{{$procedimiento->id}}" onchange="revisar_formulario({{$procedimiento->id}})" maxlength="200" value="{{$procedimiento->diagnostico_proc}}"></td>
                <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;"><b style="color: green;">Observacion</b></td>
                <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;" colspan="3"><input style="width: 100%" type="text" name="observacion{{$procedimiento->id}}" id="observacion{{$procedimiento->id}}" onchange="revisar_formulario({{$procedimiento->id}})" maxlength="200" value="{{$procedimiento->observacion_proc}}"></td> 
                <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;" colspan="1"><button class="btn btn-success btn-sm" onclick="revisar_formulario({{$procedimiento->id}})"><span class="glyphicon glyphicon-floppy-saved"></span></button></td>
                <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;" colspan="1"><span style="color: red"> SUP: </span><input id="sup{{$procedimiento->id}}" name="sup{{$procedimiento->id}}" type="checkbox" class="flat-red" @if($procedimiento->supervisa_robles) checked @endif ><span style="color: purple"> CRM: </span><input id="crm{{$procedimiento->id}}" name="crm{{$procedimiento->id}}" type="checkbox" class="flat-purple" @if($procedimiento->solo_robles) checked @endif ></button></td> 
                <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;" colspan="1">
                  <a class="btn btn-primary btn-sm" href="{{route('nd.buscador', ['id_paciente' => $procedimiento->id_paciente])}}" target="_blank">@lang('hc4.detalle')</a>
                </td>  
              </tr>
              <tr>
                <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;" ><b style="color: green;">@lang('hc4.procedimientos')</b></td>
                <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;" colspan="9">
                  <select class="form-control select2 input-sm" multiple="multiple" name="proc{{$procedimiento->id}}[]" id="proc{{$procedimiento->id}}" data-placeholder="@lang('hc4.seleccioneProcedimientos')" required style="width: 100%;">
                  @if($pcant<=0 )  
                    <option selected value="{{$procedimiento->procedimiento->id}}">{{$procedimiento->procedimiento->observacion}}</option>
                    @foreach($resto_procs as $val)
                      <option selected value="{{$val->id_procedimiento}}">{{Sis_medico\Procedimiento::find($val->id_procedimiento)->observacion}}</option>
                    @endforeach 
                    @foreach($pxs as $val)
                      <option value="{{$val->id}}">{{$val->observacion}}</option>
                    @endforeach 
                  @else
                    @foreach($pentax_procs as $val)
                      <option selected value="{{$val->id_procedimiento}}">{{Sis_medico\Procedimiento::find($val->id_procedimiento)->observacion}}</option>
                    @endforeach
                    @foreach($pxs as $val)
                      <option value="{{$val->id}}">{{$val->observacion}}</option>
                    @endforeach 
                  @endif     
                  </select>
                </td>
                <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;" >
                  <a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#mlog" data-remote="{{route('hc4controller.agenda_log', ['id_agenda' => $procedimiento->id])}}" style="color: white">Log</a>
                </td>
              </tr> 
            @endif    
          @endif   
        @endforeach
      </tbody>

    </table>
  </div> 
</div>   


<div class="box box-success">
  <div class="box-header with-border">
    <h3 class="box-title">@lang('hc4.imagenes')</h3>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
      </button>
      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body"> 
    <table  id="example2" class="table" cellspacing="0" width="100%" style="font-size: 12px;">
      <thead style="">
        <tr style=" ">
          <th scope="col" class="color titulo" width="10%" >@lang('hc4.fecha') / @lang('hc4.hora')</th>
          <th scope="col" class="color titulo" width="8%" >@lang('hc4.cedula')</th>
          <th scope="col" class="color titulo" width="10%" >@lang('hc4.apellidos')</th>
          <th scope="col" class="color titulo" width="10%" >@lang('hc4.nombres')</th>
          <th scope="col" class="color titulo" width="8%" >@lang('hc4.edad')</th>
          <th scope="col" class="color titulo" width="5%" >@lang('hc4.seguro')</th>
          <th scope="col" class="color titulo" width="22%" >@lang('hc4.procedimientos')</th>
          <th scope="col" class="color titulo" width="5%" >@lang('hc4.amb')/@lang('hc4.hosp')</th>
          <th scope="col" class="color titulo" width="7%" >@lang('hc4.estado')</th>
          <th scope="col" class="color titulo" width="10%" >@lang('hc4.revisado')</th>
          <th scope="col" class="color titulo" width="5%" >@lang('hc4.accion')</th>
        </tr>
      </thead>
      <tbody>

        @foreach($procedimientos as $procedimiento)
          @php
            $edad= 0;$mostrar=false;
            if ($procedimiento->paciente->fecha_nacimiento != null) {
              $edad = Carbon\Carbon::createFromDate(substr($procedimiento->paciente->fecha_nacimiento, 0, 4), substr($procedimiento->paciente->fecha_nacimiento, 5, 2), substr($procedimiento->paciente->fecha_nacimiento, 8, 2))->age;
            }
            $resto_procs = Sis_medico\AgendaProcedimiento::where('id_agenda',$procedimiento->id)->get();
            $pentax = Sis_medico\Pentax::where('id_agenda',$procedimiento->id)->first();
            $pcant = 0;
            if(!is_null($pentax)){
              $pentax_procs = Sis_medico\PentaxProc::where('id_pentax',$pentax->id)->get();
              $pcant = $pentax_procs->count();
              foreach($pentax_procs as $px1){
                if(!is_null($px1->procedimiento->id_grupo_procedimiento)){
                  if($px1->procedimiento->grupo_procedimiento->tipo_procedimiento=='2'){
                    $mostrar=true;
                    
                  }
                }
              }  
            }else{
              if(!is_null($procedimiento->procedimiento->id_grupo_procedimiento)){
                if($procedimiento->procedimiento->grupo_procedimiento->tipo_procedimiento=='2'){
                  $mostrar=true;
                }
              }
              foreach($resto_procs as $px1){
                if(!is_null($px1->procedimiento->id_grupo_procedimiento)){
                  if($px1->procedimiento->grupo_procedimiento->tipo_procedimiento=='2'){
                    $mostrar=true;
                    
                  }
                }      
              }
            }
          @endphp
          @if($mostrar)
            <tr>
              <td class="color">{{$procedimiento->fechaini}}</td>
              <td class="color">{{$procedimiento->id_paciente}}</td>
              <td class="color">{{$procedimiento->paciente->apellido1}} {{$procedimiento->paciente->apellido2}}</td>
              <td class="color">{{$procedimiento->paciente->nombre1}} {{$procedimiento->paciente->nombre2}}</td>
              <td class="color">{{$edad}}</td>
              <td class="color">{{$procedimiento->seguro->nombre}}</td>
              <td class="color">@if($pcant<=0 )<span class="label" style="padding-right: 5px;padding-left: 5px;background-color: green;@if($procedimiento->supervisa_robles) background-color: red; @endif @if($procedimiento->solo_robles) background-color: purple; @endif color: white;">{{$procedimiento->procedimiento->observacion}}</span>  @foreach($resto_procs as $val) <span class="label" style="padding-right: 5px;padding-left: 5px;background-color: green;@if($procedimiento->supervisa_robles) background-color: red; @endif @if($procedimiento->solo_robles) background-color: purple; @endif color: white;">{{Sis_medico\Procedimiento::find($val->id_procedimiento)->observacion}}</span>@endforeach @else @foreach($pentax_procs as $val) <span class="label " style="padding-right: 5px;padding-left: 5px;background-color: green;@if($procedimiento->supervisa_robles) background-color: red; @endif @if($procedimiento->solo_robles) background-color: purple; @endif color: white;">{{Sis_medico\Procedimiento::find($val->id_procedimiento)->observacion}}</span> @endforeach @endif</td>
              <td class="color">@if($procedimiento->est_amb_hos == 0) @lang('hc4.amb') @else @lang('hc4.hosp') @endif</td>
              <td class="color">@if($procedimiento->estado_cita=='0') @lang('hc4.porConfirmar') @elseif($procedimiento->estado_cita=='1') @lang('hc4.confirmada') @elseif($procedimiento->estado_cita=='2') @lang('contable.reagendado') @elseif($procedimiento->estado_cita=='3') @lang('hc4.suspendida') @elseif($procedimiento->estado_cita=='4') @lang('hc4.preadmisionado') @endif</td>
              <td class="color">
                <select id="revisado{{$procedimiento->id}}" name="revisado{{$procedimiento->id}}" onclick="revisado_si_no(this);">
                  <option @if($procedimiento->revisado=='0') selected @endif value="0">@lang('hc4.no')</option>
                  <option @if($procedimiento->revisado=='1') selected @endif value="1">@lang('hc4.si')</option>
                </select>
              </td>
              <td>
                <button class="btn btn-success btn-sm" onclick="mostrar_detalle({{$procedimiento->id}});"><span id="b{{$procedimiento->id}}" class="glyphicon glyphicon-plus"></span></button>
              </td>
            </tr>
            <tr>
              <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;" ><b style="color: green;">@lang('hc4.diagnostico')</b></td>
              <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;" colspan="3"><input style="width: 100%" type="text" name="diagnostico{{$procedimiento->id}}" id="diagnostico{{$procedimiento->id}}" onchange="revisar_formulario({{$procedimiento->id}})" maxlength="200" value="{{$procedimiento->diagnostico_proc}}"></td>
              <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;"><b style="color: green;">@lang('hc4.observaciones')</b></td>
              <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;" colspan="3"><input style="width: 100%" type="text" name="observacion{{$procedimiento->id}}" id="observacion{{$procedimiento->id}}" onchange="revisar_formulario({{$procedimiento->id}})" maxlength="200" value="{{$procedimiento->observacion_proc}}"></td> 
              <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;" colspan="1"><button class="btn btn-success btn-sm" onclick="revisar_formulario({{$procedimiento->id}})"><span class="glyphicon glyphicon-floppy-saved"></span></button></td>
              <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;" colspan="1"><span style="color: red"> SUP: </span><input id="sup{{$procedimiento->id}}" name="sup{{$procedimiento->id}}" type="checkbox" class="flat-red" @if($procedimiento->supervisa_robles) checked @endif ><span style="color: purple"> CRM: </span><input id="crm{{$procedimiento->id}}" name="crm{{$procedimiento->id}}" type="checkbox" class="flat-purple" @if($procedimiento->solo_robles) checked @endif ></button></td> 
              <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;" colspan="1">
                <a class="btn btn-primary btn-sm" href="{{route('nd.buscador', ['id_paciente' => $procedimiento->id_paciente])}}" target="_blank">Detalle</a>
              </td>  
            </tr>
            <tr>
              <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;" ><b style="color: green;">@lang('hc4.procedimientos')</b></td>
              <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;" colspan="9">
                <select class="form-control select2 input-sm" multiple="multiple" name="proc{{$procedimiento->id}}[]" id="proc{{$procedimiento->id}}" data-placeholder="@lang('hc4.seleccioneProcedimientos')" required style="width: 100%;">
                @if($pcant<=0 )  
                  <option selected value="{{$procedimiento->procedimiento->id}}">{{$procedimiento->procedimiento->observacion}}</option>
                  @foreach($resto_procs as $val)
                    <option selected value="{{$val->id_procedimiento}}">{{Sis_medico\Procedimiento::find($val->id_procedimiento)->observacion}}</option>
                  @endforeach 
                  @foreach($pxs as $val)
                    <option value="{{$val->id}}">{{$val->observacion}}</option>
                  @endforeach 
                @else
                  @foreach($pentax_procs as $val)
                    <option selected value="{{$val->id_procedimiento}}">{{Sis_medico\Procedimiento::find($val->id_procedimiento)->observacion}}</option>
                  @endforeach
                  @foreach($pxs as $val)
                    <option value="{{$val->id}}">{{$val->observacion}}</option>
                  @endforeach 
                @endif     
                </select>
              </td>
              <!--td class="colorx2 celda{{$procedimiento->id}}" style="display: none;" >
                <a class="btn btn-primary btn-sm" href="{{route('nd.buscador', ['id_paciente' => $procedimiento->id_paciente])}}" target="_blank">Detalle</a>
              </td-->
              <td class="colorx2 celda{{$procedimiento->id}}" style="display: none;" >
                <a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#mlog" data-remote="{{route('hc4controller.agenda_log', ['id_agenda' => $procedimiento->id])}}" style="color: white">Log</a>
              </td>
            </tr> 
          @endif     
        @endforeach
      </tbody>

    </table>
  </div>
</div>       

    
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script type="text/javascript">
  
  $('input[type="checkbox"].flat-red').iCheck({
    checkboxClass: 'icheckbox_flat-red',
    radioClass   : 'iradio_flat-red'
  });

  $('input[type="checkbox"].flat-purple').iCheck({
    checkboxClass: 'icheckbox_flat-purple',
    radioClass   : 'iradio_flat-purple'
  });

  $('input[type="checkbox"].flat-red').on('ifChecked', function(event){

    //console.log(this.name,this.name.substring(3));
    cambiar_supervision(this.name.substring(3),'1');
    //cotizador_crear_id(this.name.substring(2));
  }); 

  $('input[type="checkbox"].flat-purple').on('ifChecked', function(event){

    //console.log(this.name,this.name.substring(3));
    cambiar_crm(this.name.substring(3),'1');
    //cotizador_crear_id(this.name.substring(2));
  }); 

  $('input[type="checkbox"].flat-red').on('ifUnchecked', function(event){
   
    //cotizador_crear();
    cambiar_supervision(this.name.substring(3),'0');

  });

  $('input[type="checkbox"].flat-purple').on('ifUnchecked', function(event){
   
    //cotizador_crear();
    cambiar_crm(this.name.substring(3),'0');

  });

</script>    

<script type="text/javascript">

  $(document).ready(function(){
    $('.select2').select2({
      tags: false
    });

    $("select").on("select2:select", function (evt) {
      var element = evt.params.data.element;
      //console.log(element);
      var $element = $(element);
      //console.log($element);
      $element.detach();
      //console.log($element);
      $(this).append($element);
      $(this).trigger("change");
    });

      $('#ex_excel').hide();
      $('#ex_revision').show();

  
  }); 

  $("select").on("select2:select", function (evt) {
    //console.log(evt.target.id);
    //proc21009
    var procs = evt.target.id.substring(4);
    //console.log(procs);

    $.ajax({
      type: 'post',
      url:"{{route('hc4_revisar.formulario_procs')}}",
      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
      datatype: 'html',
      data: { id: procs, procedimientos:  $('#proc'+procs).val()},
      success: function(datahtml){
        console.log(datahtml);
        //alert("ok");
      },
      error:  function(){
        alert('error al cargar');
      }
    });
    
  }); 

  $("select").on("select2:unselect", function (evt) {
    //console.log(evt.target.id);
    //proc21009
    var procs = evt.target.id.substring(4);
    //console.log(procs);

    $.ajax({
      type: 'post',
      url:"{{route('hc4_revisar.formulario_procs')}}",
      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
      datatype: 'html',
      data: { id: procs, procedimientos:  $('#proc'+procs).val()},
      success: function(datahtml){
        console.log(datahtml);
        //alert("ok");
      },
      error:  function(){
        alert('error al cargar');
      }
    });
    
  }); 

  function revisado_si_no(val){
    $.ajax({
      type: 'get',
      url:"{{url('revision/cargar')}}/"+val.id+"/"+val.value,
      datatype: 'html',

      success: function(datahtml){
        //console.log(datahtml);
        
        
      },
      error:  function(){
        alert('error al cargar');
      }
    });
  }
  function revisar_formulario(val){
    //alert($('#diagnostico'+val).val());
    $.ajax({
      type: 'post',
      url:"{{route('hc4_revisar.formulario')}}",
      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
      datatype: 'html',
      data: { id: val, diagnostico: $('#diagnostico'+val).val(), observacion: $('#observacion'+val).val()},
      success: function(datahtml){
        //console.log(datahtml);
      },
      error:  function(){
        alert('error al cargar');
      }
    }); 
  }
  function cambiar_supervision(id, val){
    
    $.ajax({
      type: 'get',
      url:"{{url('revision/cambiar_supervision')}}/"+val+"/"+id,
      datatype: 'html',
      success: function(datahtml){
        //console.log(datahtml);
      },
      error:  function(){
        alert('error al cargar');
      }
    }); 
  }
  function cambiar_crm(id, val){
    //alert($('#diagnostico'+val).val());
    $.ajax({
      type: 'get',
      url:"{{url('revision/cambiar_crm')}}/"+val+"/"+id,
      datatype: 'html',
      success: function(datahtml){
        //console.log(datahtml);
      },
      error:  function(){
        alert('error al cargar');
      }
    }); 
  }

  function mostrar_detalle(id){
    //alert(id);
    //alert($('#b'+id).attr("class"));
    var clase = $('#b'+id).attr("class");
    if(clase == 'glyphicon glyphicon-plus'){
      $('.celda'+id).show();
      $('#b'+id).removeClass("glyphicon-plus").addClass("glyphicon-minus");  
    }else{
      $('.celda'+id).hide();
      $('#b'+id).removeClass("glyphicon-minus").addClass("glyphicon-plus");  
    }
    

  }

  var remoto_href = '';
  jQuery('body').on('click', '[data-toggle="modal"]', function() {
      if(remoto_href != jQuery(this).data('remote')) {
          remoto_href = jQuery(this).data('remote');
          jQuery(jQuery(this).data('target')).removeData('bs.modal');

          jQuery(jQuery(this).data('target')).find('.modal-body').empty();
          jQuery(jQuery(this).data('target') + ' .modal-content').load(jQuery(this).data('remote'));
      }
  });

</script>


