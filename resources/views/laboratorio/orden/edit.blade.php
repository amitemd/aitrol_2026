@extends('laboratorio.orden.base')

@section('action-content')
@php
    $xuser = Auth::user();

@endphp 
<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/fullcalendar.min.css')}}" rel="stylesheet">
<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/fullcalendar.print.min.css')}}" rel="stylesheet" media="print">
<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/scheduler.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">

  <style type="text/css">
  .icheckbox_flat-green.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }
</style>
<section class="content" >
    
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="col-md-4">
                        <h3 class="box-title">Editar Orden Examen Público </h3>
                    </div> 
                    <div class="col-md-8">
                        <h3 class="box-title"><b>Paciente:</b> {{$orden->id_paciente}}-{{$orden->pnombre1}} @if($orden->pnombre2=='N/A'||$orden->pnombre2=='(N/A)') @else{{ $orden->pnombre2 }} @endif {{$orden->papellido1}} @if($orden->papellido2=='N/A'||$orden->papellido2=='(N/A)') @else{{ $orden->papellido2 }} @endif   </h3>
                    </div>    
                </div>
                <div class="box-body">
                    <form class="form-vertical" role="form" method="POST" action="{{ route('orden.update',['id' => $orden->id]) }}">
                        
                        <input type="hidden" name="_method" value="PATCH">
                        {{ csrf_field() }}
                        <!--div class="col-md-12" style="text-align: right;">
                            <b>Valor:</b><span id="xvalor"></span>    
                        </div-->
                        <input type="hidden" name="dir" value="{{$dir}}">
                        <div class="modal fade bs-example-modal-lg" id="modal-agenda" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content" id="div_agenda">
                                </div>
                            </div>
                        </div>
          
<!--div class="col-md-12">
    
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      
        <div class="table-responsive">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
            <tbody>
              <tr role="row">
                <td><b>Paciente:</b></td>
                <td>{{$orden->id_paciente}}</td>
                <td>{{$orden->pnombre1}} @if($orden->pnombre2=='N/A'||$orden->pnombre2=='(N/A)') @else{{ $orden->pnombre2 }} @endif {{$orden->papellido1}} @if($orden->papellido2=='N/A'||$orden->papellido2=='(N/A)') @else{{ $orden->papellido2 }} @endif</td>
                <td><b>Seguro:</b></td>
                <td>{{$seguro->nombre}}</td>
                <td><b>@if($orden->est_amb_hos==0) AMBULATORIO @else HOSPITALIZADO @endif</b></td>         
              </tr>
            </tbody>
          </table>
        </div>
      
    </div>
</div-->
                        


                            <div class="form-group col-md-4 {{ $errors->has('id_doctor_ieced') ? ' has-error' : '' }}">
                                <label for="id_doctor_ieced" class="col-md-12 control-label">Médico</label>
                                <div class="col-md-12"> 
                                    <select id="id_doctor_ieced" name="id_doctor_ieced" class="form-control input-sm" required>
                                        <option value="">Seleccione ...</option>
                                    @foreach ($usuarios as $usuario)
                                        <option @if(old('id_doctor_ieced')!='') @if(old('id_doctor_ieced') == $usuario->id) selected @endif @else @if($orden->id_doctor_ieced==$usuario->id) selected @endif @endif value="{{$usuario->id}}" @if($usuario->id=='1307189140') style="color: red;" @endif>{{$usuario->apellido1}} {{$usuario->apellido2}} {{$usuario->nombre1}}</option>
                                    @endforeach
                                    </select>
                                    @if ($errors->has('id_doctor_ieced'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_doctor_ieced') }}</strong>
                                    </span>
                                    @endif 
                                </div>
                            </div>
                            <div class="form-group col-md-4 {{ $errors->has('id_convenio') ? ' has-error' : '' }}">
                                <label for="id_convenio" class="col-md-12 control-label">Convenio</label>
                                <div class="col-md-12"> 
                                    <select id="id_convenio" name="id_convenio" class="form-control input-sm" required>
                                        <option value="">Seleccione ...</option>
                                    @foreach ($convenios as $con)
                                        
                                        <option @if(old('id_con')!='') @if(old('id_con') == $con->id) selected @endif @else @if($convenio->id==$con->id) selected @endif @endif value="{{$con->id}}">{{$con->nombre}}-{{$con->nombrecomercial}}</option>
                                        
                                    @endforeach
                                    </select>
                                    @if ($errors->has('id_convenio'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_convenio') }}</strong>
                                    </span>
                                    @endif 
                                </div>
                            </div>

                            <div class="form-group col-md-4 ">
                                <label for="id_doctor_ieced" class="col-md-12 control-label">Valor</label>
                                <div class="col-md-12">
                                    <input class="form-control input-sm" type="text" name="valor" value="{{$orden->valor}}" readonly style="text-align: right;"> 
                                    
                                </div>
                            </div>
                            <div class="form-group col-md-4{{ $errors->has('est_amb_hos') ? ' has-error' : '' }}">
                                <label for="est_amb_hos" class="col-md-12 control-label">Tipo</label>
                                <div class="col-md-12">
                                    <select id="est_amb_hos" name="est_amb_hos" class="form-control input-sm" required>
                                        <option @if(old('est_amb_hos')!=null) @if(old('est_amb_hos')== '0') selected @endif @else @if($orden->est_amb_hos=='0') selected @endif @endif value="0">Ambulatorio</option>
                                        <option @if(old('est_amb_hos')!=null) @if(old('est_amb_hos')== '1') selected @endif @else @if($orden->est_amb_hos=='1') selected @endif @endif value="1">Hospitalizado</option>
                                    </select>  
                                
                                    @if ($errors->has('est_amb_hos'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('est_amb_hos') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            

                            

                            <!--fecha_orden-->
                            <div class="form-group col-md-4 {{ $errors->has('fecha_orden') ? ' has-error' : '' }} ">
                                <label class="col-md-12 control-label">@if($xuser->id_tipo_usuario=='5') Fecha Orden @else Fecha Convenios @endif</label>
                                <div class="col-md-12">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" value="{{old('fecha_orden')}}" name="fecha_orden" class="form-control pull-right input-sm" id="fecha_orden" @if($orden->estado=='1')  readonly @endif required >

                                    </div>
                                    @if ($errors->has('fecha_orden'))
                                    <span class="help-block">
                                      <strong>{{ $errors->first('fecha_orden') }}</strong>
                                    </span>
                                    @endif
                                    
                                </div>
                            </div>

                            

                            <!--div class="form-group col-md-6 {{ $errors->has('id_seguro') ? ' has-error' : '' }}">
                                <label for="id_seguro" class="col-md-4 control-label">Seguro</label>
                                <div class="col-md-7"> 
                                    <select id="id_seguro" name="id_seguro" class="form-control input-sm" required>
                                        <option value="">Seleccione ...</option>
                                    @foreach ($seguros as $seguro)
                                        @if($seguro->tipo=='0')
                                        <option @if(old('id_seguro')!='') @if(old('id_seguro') == $seguro->id) selected @endif @else @if($orden->id_seguro==$seguro->id) selected @endif @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                                        @endif
                                    @endforeach
                                    </select>
                                    @if ($errors->has('id_seguro'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_seguro') }}</strong>
                                    </span>
                                    @endif 
                                </div>
                            </div>

                            <div class="form-group col-md-6 {{ $errors->has('id_empresa') ? ' has-error' : '' }}">
                                <label for="id_empresa" class="col-md-4 control-label">Empresa</label>
                                <div class="col-md-7"> 
                                    <select id="id_empresa" name="id_empresa" class="form-control input-sm" >
                                        <option value="">Seleccione ...</option>
                                    @foreach ($empresa as $value)
                                        <option @if(old('id_empresa') == $value->id) selected @endif @if($orden->id_empresa==$value->id) selected @endif value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                                    @endforeach
                                    </select>
                                    @if ($errors->has('id_empresa'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_empresa') }}</strong>
                                    </span>
                                    @endif 
                                </div>
                            </div-->

                            <div class="form-group col-md-4 {{ $errors->has('id_protocolo') ? ' has-error' : '' }}">
                                <label for="id_protocolo" class="col-md-12 control-label">Protocolo</label>
                                <div class="col-md-12"> 
                                    <select id="id_protocolo" name="id_protocolo" class="form-control input-sm" onchange="Protocolo();">
                                        <option value="">Seleccione ...</option>
                                    @foreach ($protocolos as $protocolo)
                                        <option @if(old('id_protocolo') == $protocolo->id) selected @endif @if($orden->id_protocolo==$protocolo->id) selected @endif value="{{$protocolo->id}}">{{$protocolo->nombre}}</option>
                                    @endforeach
                                    </select>
                                    @if ($errors->has('id_protocolo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_protocolo') }}</strong>
                                    </span>
                                    @endif 
                                </div>
                            </div>

                        <!--div class="form-group col-md-12{{ $errors->has('doctor_txt') ? ' has-error' : '' }}">
                            <label for="doctor_txt" class="col-md-2 control-label">Doctor Externo</label>

                            <div class="col-md-10" style="margin-left: -5px;">
                                <input id="doctor_txt" type="text" class="form-control input-sm" name="doctor_txt" value="{{ old('doctor_txt') }}" style="text-transform:uppercase; width: 95.5%;" onkeyup="javascript:this.value=this.value.toUpperCase();" >

                                @if ($errors->has('doctor_txt'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('doctor_txt') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div-->    


                        <!--div class="form-group col-md-12{{ $errors->has('observacion') ? ' has-error' : '' }}">
                            <label for="observacion" class="col-md-2 control-label">Observación</label>

                            <div class="col-md-10" style="margin-left: -5px;">
                                <input id="observacion" type="text" class="form-control input-sm" name="observacion" value=@if(old('observacion')!='')"{{ old('observacion') }}"@else "{{$orden->observacion}}" @endif style="text-transform:uppercase; width: 95.5%;" onkeyup="javascript:this.value=this.value.toUpperCase();" >

                                @if ($errors->has('observacion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('observacion') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div-->

                        @php $cont=count(old('examen')) @endphp 
                        <!--div class="form-group col-md-12 {{ $errors->has('examen') ? ' has-error' : '' }}">
                            <label for="examen" class="col-md-2 control-label">Exámenes </label>
                            <div class="col-md-10" style="margin-left: -5px;">
                                    <select id="examen" class="form-control input-sm select2" name="examen[]" multiple="multiple" data-placeholder="Seleccione"
                                    style="width: 95.5%;" >
                                        @foreach($examenes as $examen)
                                            <option  @for($x=0; $x<$cont; $x++) @if(old('examen.'.$x)==$examen->id) selected @endif @endfor value="{{$examen->id}}">{{$examen->nombre}}:   $ {{$examen->valor}}  </option>
                                        @endforeach
                                    </select>

                                @if ($errors->has('examen'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('examen') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div-->
<!--div class="col-md-12">
    @foreach($agrupadores as $agrupador)
    <div class="col-md-4">    
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      
            <div class="table-responsive">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                    <thead>
                        <tr role="row" style="background-color: #00bfff;">
                            <th width="90%" ><b>{{$agrupador->nombre}}</b></th>
                            <th >Sel.</th>
                        </tr>
                    </thead>
                    <tbody >
                        @foreach ($examenes as $value)
                            @php $ch_det=false; @endphp
                            @foreach($detalles as $detalle)
                        @if($detalle->id_examen==$value->id)
                        @php $ch_det=true; @endphp
                @endif
            @endforeach
            @if($value->id_agrupador==$agrupador->id)
              <tr role="row">
                <td>{{$value->nombre}}</td>
                <td><input id="ch{{$value->id}}" @if($ch_det) checked @endif name="ch{{$value->id}}" type="checkbox" class="flat-green"</td>         
              </tr>
            @endif  
            @endforeach
            </tbody>
          </table>
        </div>
      
    </div>
</div>    
@endforeach    
</div-->


<div class="col-md-12">                        
    @foreach($agrupadores as $agrupador)
    @if($agrupador->id=='1')
    <div class="col-md-4">    
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      
            <div class="table-responsive">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                    <thead>
                        <tr role="row" style="background-color: #00bfff;">
                            <th colspan="4" width="90%"><b>{{$agrupador->nombre}}</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr role="row">
                        @php $contador=0; @endphp
                        @foreach ($examenes as $value)
                            @if($value->id_agrupador==$agrupador->id)
                                @if($value->no_orden_pub=='1' && is_null($detalles->where('id_examen',$value->id)->first()))
                                @else 
                                    @if((!is_null($detalles->where('id_examen',$value->id)->first()) && $value->id=='628')||$value->id!='628')
                                        <td>{{$value->nombre}}</td>
                                        <td>
                                            <input id="ch{{$value->id}}" @if(!is_null($detalles->where('id_examen',$value->id)->first())) checked @endif @if(!is_null($detalles->where('id_examen',$value->id)->first())) checked @endif name="ch{{$value->id}}" type="checkbox" class="flat-green">
                                        </td> 
                                        @php $contador = $contador + 1; @endphp 
                                    @endif           
                                    @if($contador=='2')
                                        @php $contador=0; @endphp
                                        </tr>
                                    @endif
                                @endif    
                            @endif  
                        @endforeach
                        @if($contador=='1')
                            <td></td>
                            <td></td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
      
        </div>
    </div>
    @endif    
    @endforeach 
    @foreach($agrupadores as $agrupador)
    @if($agrupador->id=='3')
    <div class="col-md-4">    
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      
            <div class="table-responsive">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                    <thead>
                        <tr role="row" style="background-color: #00bfff;">
                            <th width="90%"><b>{{$agrupador->nombre}}</b></th>
                            <th >Sel.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($examenes as $value)
                        @if($value->id_agrupador==$agrupador->id)
                        @if((!is_null($detalles->where('id_examen',$value->id)->first()) && $value->id=='201')||$value->id!='201')
                        <tr role="row">
                            <td>{{$value->nombre}}</td>
                            <td><input id="ch{{$value->id}}" @if(!is_null($detalles->where('id_examen',$value->id)->first())) checked @endif name="ch{{$value->id}}" type="checkbox" class="flat-green"></td>         
                        </tr>
                        @endif
                        @endif  
                        @endforeach
                    </tbody>
                </table>
            </div>
      
        </div>

        @endif    
        @endforeach   
        @foreach($agrupadores as $agrupador)
        @if($agrupador->id=='4')
    
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      
            <div class="table-responsive">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                    <thead>
                        <tr role="row" style="background-color: #00bfff;">
                            <th width="90%"><b>{{$agrupador->nombre}}</b></th>
                            <th >Sel.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($examenes as $value)
                        @if($value->id_agrupador==$agrupador->id)
                        <tr role="row">
                            <td>{{$value->nombre}}</td>
                            <td><input id="ch{{$value->id}}" @if(!is_null($detalles->where('id_examen',$value->id)->first())) checked @endif name="ch{{$value->id}}" type="checkbox" class="flat-green"></td>         
                        </tr>
                        @endif  
                        @endforeach
                    </tbody>
                </table>
            </div>
      
        </div>
    </div>
    @endif    
    @endforeach
    @foreach($agrupadores as $agrupador)
    @if($agrupador->id=='2')
    <div class="col-md-4">    
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      
            <div class="table-responsive">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                    <thead>
                        <tr role="row" style="background-color: #00bfff;">
                            <th colspan="4" width="90%"><b>{{$agrupador->nombre}}</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr role="row">
                        @php $contador=0; @endphp
                        @foreach ($examenes as $value)
                        @if($value->id_agrupador==$agrupador->id)
                            <td>{{$value->nombre}}</td>
                            <td><input id="ch{{$value->id}}" @if(!is_null($detalles->where('id_examen',$value->id)->first())) checked @endif name="ch{{$value->id}}" type="checkbox" class="flat-green"></td> 
                            @php $contador = $contador + 1; @endphp        
                        @if($contador=='2')
                        @php $contador=0; @endphp
                        </tr>
                        @endif
                        
                        @endif  
                        @endforeach
                        @if($contador=='1')
                        <td></td>
                        <td></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
      
        </div>
    </div>
    @endif    
    @endforeach      
</div>

<div class="col-md-12">                        
    @foreach($agrupadores as $agrupador)
    @if($agrupador->id=='5')
    <div class="col-md-4">    
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      
            <div class="table-responsive">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                    <thead>
                        <tr role="row" style="background-color: #00bfff;">
                            <th colspan="4" width="90%"><b>{{$agrupador->nombre}}</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr role="row">
                        @php $contador=0; @endphp
                        @foreach ($examenes as $value)
                        @if($value->id_agrupador==$agrupador->id)
                            <td>{{$value->nombre}}</td>
                            <td><input id="ch{{$value->id}}" @if(!is_null($detalles->where('id_examen',$value->id)->first())) checked @endif name="ch{{$value->id}}" type="checkbox" class="flat-green"></td> 
                            @php $contador = $contador + 1; @endphp        
                        @if($contador=='2')
                        @php $contador=0; @endphp
                        </tr>
                        @endif
                        
                        @endif  
                        @endforeach
                        @if($contador=='1')
                        <td></td>
                        <td></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
      
        </div>
    </div>
    @endif    
    @endforeach

    @foreach($agrupadores as $agrupador)
    @if($agrupador->id=='6')
    <div class="col-md-4">    
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      
            <div class="table-responsive">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                    <thead>
                        <tr role="row" style="background-color: #00bfff;">
                            <th colspan="4" width="90%"><b>{{$agrupador->nombre}}</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr role="row">
                        @php $contador=0; @endphp
                        @foreach ($examenes as $value)
                        @if($value->id_agrupador==$agrupador->id)
                            <td>{{$value->nombre}}</td>
                            <td><input id="ch{{$value->id}}" @if(!is_null($detalles->where('id_examen',$value->id)->first())) checked @endif name="ch{{$value->id}}" type="checkbox" class="flat-green"></td> 
                            @php $contador = $contador + 1; @endphp        
                        @if($contador=='2')
                        @php $contador=0; @endphp
                        </tr>
                        @endif
                        
                        @endif  
                        @endforeach
                        @if($contador=='1')
                        <td></td>
                        <td></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
      
        </div>
    </div>
    @endif    
    @endforeach 

    @foreach($agrupadores as $agrupador)
    @if($agrupador->id=='7')
    <div class="col-md-4">    
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      
            <div class="table-responsive">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                    <thead>
                        <tr role="row" style="background-color: #00bfff;">
                            <th width="90%"><b>{{$agrupador->nombre}}</b></th>
                            <th >Sel.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($examenes as $value)
                        @if($value->id_agrupador==$agrupador->id)
                        @if($value->especial_publico=='0')
                        @if($value->no_orden_pub=='1' && is_null($detalles->where('id_examen',$value->id)->first()))
                        @else
                        <tr role="row">
                            <td>{{$value->nombre}}</td>
                            <td><input id="ch{{$value->id}}" @if(!is_null($detalles->where('id_examen',$value->id)->first())) checked @endif name="ch{{$value->id}}" type="checkbox" class="flat-green"></td>         
                        </tr>
                        @endif
                        @endif
                        @endif  
                        @endforeach
                    </tbody>
                </table>
            </div>
      
        </div>
    </div>    

    @endif    
    @endforeach 

    <div class="col-md-12">  </div>
    @foreach($agrupadores as $agrupador)
    @if($agrupador->id=='7')
    <div class="col-md-12">    
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      
            <div class="table-responsive">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                    <thead>
                        <tr role="row" style="background-color: #00bfff;">
                            <th width="20%" ><b>{{$agrupador->nombre}} ESPECIALES</b></th>
                            <th width="5%" >Sel.</th>
                            <th width="20%" ><b>{{$agrupador->nombre}} ESPECIALES</b></th>
                            <th width="5%" >Sel.</th>
                            <th width="20%" ><b>{{$agrupador->nombre}} ESPECIALES</b></th>
                            <th width="5%" >Sel.</th>
                            <th width="20%" ><b>{{$agrupador->nombre}} ESPECIALES</b></th>
                            <th width="5%" >Sel.</th>
                        </tr>
                    </thead>
                    <tbody>@php $xcont=0; @endphp
                        @foreach ($examenes as $value) 
                        @if($value->id_agrupador==$agrupador->id)
                        @if($value->especial_publico=='1')
                        @if($value->no_orden_pub=='1' && is_null($detalles->where('id_examen',$value->id)->first()))
                        @else 
                        @if($xcont==0)<tr role="row">@endif
                            <td>{{$value->nombre}}</td>
                            <td><input id="ch{{$value->id}}" @if(!is_null($detalles->where('id_examen',$value->id)->first())) checked @endif name="ch{{$value->id}}" type="checkbox" class="flat-green"></td> @php $xcont++; @endphp         
                        @if($xcont=='4')</tr> @php $xcont=0; @endphp @endif
                        @endif
                        @endif
                        @endif  
                        @endforeach
                    </tbody>
                </table>
            </div>
      
        </div>
    </div>    

    @endif    
    @endforeach  
        
         
</div>
                        

                    

                        <div class="form-group">
                            <div class="col-md-12">&nbsp;</div>
                            <!--div class="col-md-4"></div-->
                            <div class="col-md-4">
                                <button id="boton" type="submit" class="btn btn-primary">
                                    Actualizar
                                </button>
                            </div>

                            <div class="col-md-4">
                                <button id="boton" type="button" class="btn btn-danger" onclick="confirmar();" @if($orden->estado=='1') disabled @endif>
                                    Generar
                                </button>
                            </div>

                            <div class="col-md-4">
                                <button id="boton" type="button" class="btn btn-primary" onclick="guardar_agenda_labs();">
                                    <span class="ionicons ion-ios-flask"></span> Agendar
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            
            </div>
        </div>
    </div>
    
</section>
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/moment.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/jquery.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/fullcalendar.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/scheduler.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/es.js')}}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{ asset ('plugins/sweetalert2_6_11/sweetalert2.js') }}"></script>
<script src="{{ asset ('plugins/sweetalert2_6_11/sweetalert2.all.min.js') }}"></script>

<script type="text/javascript">



//Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-green').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green'
    });
    $('#fecha_orden').datetimepicker({
        format: 'YYYY/MM/DD',
        date: '{{$orden->fecha_orden}}'


    }); 
    var contador = {{$orden->cantidad}};

    function confirmar(){
        swal.fire({
            title: 'Al emitir la orden, se enviará al laboratorio con la fecha de emisión {{date('Y-m-d')}}',
            //text: "You won't be able to revert this!",
            icon: "warning",
            type: 'warning',
            buttons: true,
          
          }).then((result) => {
            if (result.value) {
             //aqui pon algo
             cierre_caja()
             location.href = "{{url('orden_iess/confirmar')}}"+"/{{$orden->id}}"
        }
        })

        
    }

    function cierre_caja(){
        $.ajax({
        type: 'post',
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        url:"{{route('c_caja.storeLabs')}}",
        data:{'id_orden': '{{$orden->id}}','id_paciente':'{{$orden->id_paciente}}','total':'{{$orden->total_valor}}'},
        datatype: 'json',
        success: function(data){
            console.log(data);
            //alert(data)
        },
        error: function(data){
            //console.log(data);
            //alert(data)
        }
        });  
    }
    $(document).ready(function() {

        @foreach($examenes as $examen)
            @if($examen->estado=='0')
                $('#ch{{$examen->id}}').iCheck('disable');
            @endif
        @endforeach

        if(contador>0){
                $("#boton").prop('disabled', false);    
            }else{
                $("#boton").prop('disabled', true);
            }

        $('input[type="checkbox"].flat-green').on('ifChecked', function(event){
            contador = contador + 1;
            if(contador>0){
                $("#boton").prop('disabled', false);    
            }else{
                $("#boton").prop('disabled', true);
            }
            
        });

        $('input[type="checkbox"].flat-green').on('ifUnchecked', function(event){
            //Protocolo();
            
            

            var protocolo = document.getElementById('id_protocolo').value;
            var textid = this.id; 
            //alert(this.id);

            if(protocolo!=''){

                $.ajax({
                    type: 'get',
                    url: "{{ url('protocolo/busca/examen')}}/"+protocolo+"/"+this.id, //protocolo.buscaexamenid
                       
                    success: function(data){
                        //alert("ahora");
                        //console.log(data);
                        //alert(data);
                        //if(data=='ok'){
                            //alert("No puede quitar examen del protocolo"); 
                           // $('#'+textid).prop('checked',true).iCheck('update');   
                        //}else{
                            contador = contador - 1;     
                            //alert('resta');
                            //alert(contador);   
                        //}
                        
                    }    
                });

            }else{

                contador = contador - 1;    
                //alert('resta');
                //alert(contador); 

            }

            if(contador>0){
                $("#boton").prop('disabled', false);    
            }else{
                $("#boton").prop('disabled', true);
            }



        });

        $("#id_tipo_usuario").change(function () {
            
            //var valor = 0;
            var estado = document.getElementById("id_tipo_usuario").value;
            
             
        });



        $('#fecha_nacimiento').datetimepicker({
            format: 'YYYY/MM/DD'


            });
         $(".breadcrumb").append('<li><a href="{{asset('orden/')}}"> Inicio</a></li>');
        $(".breadcrumb").append('<li class="active">Editar</li>');

        $('.usuario1 a').click(function() {
            $(this).closest('.dropdown').find('input.nombrecode')
            .val('(' + $(this).attr('data-value') + ')');    
        });

        $('.usuario2 a').click(function() {
            $(this).closest('.dropdown').find('input.nombrecode')
            .val('(' + $(this).attr('data-value') + ')');

        });

        $('#fechaini').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
             //Important! See issue #1075
            
        });

        $('.select2').select2();

        $('#examen').on('select2:select', function (e) {
            var data = e.params.data;
            console.log(data);
        });
           

    });

    var buscapaciente = function ()
    {
    

        var js_paciente = document.getElementById('id').value;
        
        $.ajax({
            type: 'get',
            url: "{{ url('hospitalizados/buscapaciente')}}/"+js_paciente, //hospitalizados.buscapaciente
                       
            success: function(data){
                if(data=='no'){
                    $('#nombre1').val('');
                    $('#nombre2').val('');
                    $('#apellido1').val('');
                    $('#apellido2').val('');
                 
                }else{
                    //alert('Paciente ya ingresado en el sistema');
                    //console.log(data);
                    $('#nombre1').val(data.nombre1);
                    $('#nombre2').val(data.nombre2);
                    $('#apellido1').val(data.apellido1);
                    $('#apellido2').val(data.apellido2);
                    $('#procedencia').focus();
                }
            }    
        });  
    
    }
    function Protocolo(){

        contador = 0;
        $('input[type="checkbox"].flat-green').prop('checked',false).iCheck('update');
        

        var protocolo = document.getElementById('id_protocolo').value;
        //alert(protocolo);
 
        if(protocolo!=''){

            $.ajax({
                type: 'get',
                url: "{{ url('protocolo/busca/examen')}}/"+protocolo, //protocolo.buscaexamen
                       
                success: function(data){
                    if(data!='no'){
                    
                        data.forEach(function(element) {
                            //alert('ch'+element.id_examen);
                            $('#ch'+element.id_examen).prop('checked',true).iCheck('update');
                        
                            contador = contador + 1;
                            //alert(contador);
                            
                            if(contador>0){
                                $("#boton").prop('disabled', false);    
                            }else{
                                $("#boton").prop('disabled', true);
                            }
                        });
                 
                    }
                }    
            });

        }else{
            $("#boton").prop('disabled', true);    
        }

    } 
    function guardar_agenda_labs(){
            
            //div_agenda
            $.ajax({
                type: 'get',
                url: "{{route('orden_labs.new_aglaboratorio_nuevo_publico',['id_orden'=>$orden->id])}}",
                success: function(data){
                    $('#div_agenda').html(data);
                    $("#modal-agenda").modal();
                },
                error: function(data){
                    console.log(data);
                    alert('Error, no se pudo guardar la informacion');
                }    
            }); 
    
          
    }  

    

</script>
@endsection
