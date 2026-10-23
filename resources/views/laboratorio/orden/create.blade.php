@extends('laboratorio.orden.base')

@section('action-content')


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

    .form-group{
        margin-bottom: 0px;
        padding-left: 5px;
        padding-right: 5px;
    }
</style>
<section class="content" >
    
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border"><div class="col-md-6"><h3 class="box-title">Orden para Examen Público</h3></div><div class="col-md-6"><div class="alert-danger"><span id="cant_ord"></span></div></div></div>
                <div class="box-body">
                    <form id="form_aglabs_publica" class="form-vertical" role="form" method="POST" action="{{ route('orden.store') }}">
                        {{ csrf_field() }}
                        <!--div class="col-md-12" style="text-align: right;">
                            <b>Valor:</b><span id="xvalor"></span>    
                        </div-->
                        <div class="modal fade bs-example-modal-lg" id="modal-agenda" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content" id="div_agenda">
                                </div>
                            </div>
                        </div>
                        
                        <!--cedula-->
                        <div class="form-group col-md-3{{ $errors->has('id') ? ' has-error' : '' }}">
                            <label for="id" class="col-md-12 control-label">Cédula</label>
                            <div class="col-md-12">
                                <input id="id" maxlength="10" type="text" class="form-control input-sm" name="id" value="{{ old('id') }}" required autofocus onkeyup="validarCedula(this.value);" onchange="buscapaciente();">
                                @if ($errors->has('id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <!--primer nombre-->
                        <div class="form-group col-md-3{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                            <label for="nombre1" class="col-md-12 control-label">Primer Nombre</label>
                            <div class="col-md-12">
                                <input id="nombre1" class="form-control input-sm" type="text" name="nombre1" value="{{ old('nombre1') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('nombre1'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre1') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
               
                        <!--//segundo nombre-->
                        <div class="form-group col-md-3 {{ $errors->has('nombre2') ? ' has-error' : '' }}">
                            <label for="nombre2" class="col-md-12 control-label">Segundo Nombre</label>
                            <div class="col-md-12">
                                <div class="input-group dropdown">
                                    <input id="nombre2" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="nombre2" value="{{ old('nombre2') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus required >
                                    <ul class="dropdown-menu usuario1">
                                        <li><a data-value="N/A">N/A</a></li>
                                    </ul>
                                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                </div>
                                    @if ($errors->has('nombre2'))
                                    <span class="help-block">
                                     <strong>{{ $errors->first('nombre2') }}</strong>
                                    </span>
                                    @endif
                            </div>
                        </div>
           
                        <!--primer apellido-->
                        <div class="form-group col-md-3{{ $errors->has('apellido1') ? ' has-error' : '' }}">
                            <label for="apellido1" class="col-md-12 control-label">Primer Apellido</label>
                            <div class="col-md-12">
                                <input id="apellido1" type="text" class="form-control input-sm" name="apellido1" value="{{ old('apellido1') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('apellido1'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('apellido1') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                 
                        <!--Segundo apellido-->
                        <div class="form-group col-md-3 {{ $errors->has('apellido2') ? ' has-error' : '' }}">
                            <label for="apellido2" class="col-md-12 control-label">Segundo Apellido</label>
                            <div class="col-md-12">
                                <div class="input-group dropdown">
                                    <input id="apellido2" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="apellido2" value="{{ old('apellido2') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus required >
                                    <ul class="dropdown-menu usuario2">
                                        <li><a data-value="N/A">N/A</a></li>
                                    </ul>
                                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                </div>
                                    @if ($errors->has('apellido2'))
                                    <span class="help-block">
                                     <strong>{{ $errors->first('apellido2') }}</strong>
                                    </span>
                                    @endif
                            </div>
                        </div>

                        <!--sexo 1=MASCULINO 2=FEMENINO-->
                        <div class="form-group col-md-3{{ $errors->has('sexo') ? ' has-error' : '' }}">
                            <label for="sexo" class="col-md-12 control-label">Sexo</label>
                            <div class="col-md-12">
                                <select id="sexo" name="sexo" class="form-control input-sm" required>
                                    <option value="">Seleccionar ..</option>
                                    <option @if(old('sexo')=='1') selected @endif value="1">MASCULINO</option>
                                    <option @if(old('sexo')=='2') selected @endif value="2">FEMENINO</option>
                                            
                                </select>  
                                @if ($errors->has('sexo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('sexo') }}</strong>
                                    </span>
                                @endif
                            </div>        
                        </div>

                        <!--fecha_nacimiento-->
                        <div class="form-group col-md-3 {{ $errors->has('fecha_nacimiento') ? ' has-error' : '' }} ">
                            <label class="col-md-12 control-label">Fecha Nacimiento</label>
                            <div class="col-md-12">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{old('fecha_nacimiento')}}" name="fecha_nacimiento" class="form-control pull-right input-sm" id="fecha_nacimiento" required >

                                </div>
                                @if ($errors->has('fecha_nacimiento'))
                                <span class="help-block">
                                  <strong>{{ $errors->first('fecha_nacimiento') }}</strong>
                                </span>
                                @endif
                                
                            </div>
                        </div>

                            <div class="form-group col-md-3{{ $errors->has('est_amb_hos') ? ' has-error' : '' }}">
                                <label for="est_amb_hos" class="col-md-12 control-label">Tipo</label>
                                <div class="col-md-12">
                                    <select id="est_amb_hos" name="est_amb_hos" class="form-control input-sm" required>
                                        <option @if(old('est_amb_hos')== '0') selected @endif value="0">Ambulatorio</option>
                                        <option @if(old('est_amb_hos')== '1') selected @endif value="1">Hospitalizado</option>
                                    </select>  
                                
                                    @if ($errors->has('est_amb_hos'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('est_amb_hos') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group col-md-3 {{ $errors->has('id_doctor_ieced') ? ' has-error' : '' }}">
                                <label for="id_doctor_ieced" class="col-md-12 control-label">Médico</label>
                                <div class="col-md-12"> 
                                    <select id="id_doctor_ieced" name="id_doctor_ieced" class="form-control input-sm" required>
                                        <option value="">Seleccione ...</option>
                                    @foreach ($usuarios as $usuario)
                                        <option @if(old('id_doctor_ieced') == $usuario->id) selected @endif value="{{$usuario->id}}" @if($usuario->id=='1307189140') style="color: red;" @endif>{{$usuario->apellido1}} {{$usuario->apellido2}} {{$usuario->nombre1}}</option>
                                    @endforeach
                                    </select>
                                    @if ($errors->has('id_doctor_ieced'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_doctor_ieced') }}</strong>
                                    </span>
                                    @endif 
                                </div>
                            </div>

                            <div class="form-group col-md-3 {{ $errors->has('id_convenio') ? ' has-error' : '' }}">
                                <label for="id_convenio" class="col-md-12 control-label">Convenio</label>
                                <div class="col-md-12"> 
                                    <select id="id_convenio" name="id_convenio" class="form-control input-sm" required>
                                        <option value="">Seleccione ...</option>
                                    @foreach ($convenios as $convenio)
                                        <option @if(old('id_convenio') == $convenio->id) selected @endif value="{{$convenio->id}}">{{$convenio->nombre}}-{{$convenio->nombrecomercial}}</option>
                                    @endforeach
                                    </select>
                                    @if ($errors->has('id_convenio'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_convenio') }}</strong>
                                    </span>
                                    @endif 
                                </div>
                            </div>
                            <!--div class="form-group col-md-3 {{ $errors->has('id_seguro') ? ' has-error' : '' }}">
                                <label for="id_seguro" class="col-md-12 control-label">Seguro</label>
                                <div class="col-md-12"> 
                                    <select id="id_seguro" name="id_seguro" class="form-control input-sm" required>
                                        <option value="">Seleccione ...</option>
                                    @foreach ($seguros as $seguro)
                                        @if($seguro->tipo=='0')
                                        <option @if(old('id_seguro') == $seguro->id) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
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

                            <div class="form-group col-md-3 {{ $errors->has('id_empresa') ? ' has-error' : '' }}">
                                <label for="id_empresa" class="col-md-12 control-label">Empresa</label>
                                <div class="col-md-12"> 
                                    <select id="id_empresa" name="id_empresa" class="form-control input-sm" >
                                        <option value="">Seleccionar ...</option>
                                    @foreach ($empresas as $value)
                                        <option @if(old('id_empresa') == $value->id) selected @endif value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                                    @endforeach
                                    </select>
                                    @if ($errors->has('id_empresa'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_empresa') }}</strong>
                                    </span>
                                    @endif 
                                </div>
                            </div-->        
                            

                            <div class="form-group col-md-3 {{ $errors->has('id_protocolo') ? ' has-error' : '' }}">
                                <label for="id_protocolo" class="col-md-12 control-label">Protocolo</label>
                                <div class="col-md-12"> 
                                    <select id="id_protocolo" name="id_protocolo" class="form-control input-sm" onchange="Protocolo();">
                                        <option value="">Seleccione ...</option>
                                    @foreach ($protocolos as $protocolo)
                                        <option @if(old('id_protocolo') == $protocolo->id) selected @endif value="{{$protocolo->id}}">{{$protocolo->nombre}}</option>
                                    @endforeach
                                    </select>
                                    @if ($errors->has('id_protocolo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_protocolo') }}</strong>
                                    </span>
                                    @endif 
                                </div>
                            </div>

                            

                            <!--fecha_orden-->
                            <div class="form-group col-md-3 {{ $errors->has('fecha_orden') ? ' has-error' : '' }} ">
                                <label class="col-md-12 control-label">Fecha Orden</label>
                                <div class="col-md-12">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" value="{{old('fecha_orden')}}" name="fecha_orden" class="form-control pull-right input-sm" id="fecha_orden" required >

                                    </div>
                                    @if ($errors->has('fecha_orden'))
                                    <span class="help-block">
                                      <strong>{{ $errors->first('fecha_orden') }}</strong>
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
                                <input id="observacion" type="text" class="form-control input-sm" name="observacion" value="{{ old('observacion') }}" style="text-transform:uppercase; width: 95.5%;" onkeyup="javascript:this.value=this.value.toUpperCase();" >

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
                            @if($value->no_orden_pub=='0')
                                @if($value->id_agrupador==$agrupador->id)
                                    @if($value->id!='628')
                                        <td>{{$value->nombre}}</td>
                                        <td><input id="ch{{$value->id}}" name="ch{{$value->id}}" type="checkbox" class="flat-green"></td> 
                                        @php $contador = $contador + 1; @endphp        
                                        @if($contador=='2')
                                            @php $contador=0; @endphp
                                            </tr>
                                        @endif
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
                        @if($value->id!='201')
                        <tr role="row">
                            <td>{{$value->nombre}}</td>
                            <td><input id="ch{{$value->id}}" name="ch{{$value->id}}" type="checkbox" class="flat-green"></td>         
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
                            <td><input id="ch{{$value->id}}" name="ch{{$value->id}}" type="checkbox" class="flat-green"></td>         
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
                            <td><input id="ch{{$value->id}}" name="ch{{$value->id}}" type="checkbox" class="flat-green"></td> 
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
                            <td><input id="ch{{$value->id}}" name="ch{{$value->id}}" type="checkbox" class="flat-green"></td> 
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
                            <td><input id="ch{{$value->id}}" name="ch{{$value->id}}" type="checkbox" class="flat-green"></td> 
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
                        @if($value->no_orden_pub=='0')
                        <tr role="row">
                            <td>{{$value->nombre}}</td>
                            <td><input id="ch{{$value->id}}" name="ch{{$value->id}}" type="checkbox" class="flat-green"></td>         
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
                        @if($value->no_orden_pub=='0')
                        @if($xcont==0)<tr role="row">@endif
                            <td>{{$value->nombre}}</td>
                            <td><input id="ch{{$value->id}}" name="ch{{$value->id}}" type="checkbox" class="flat-green"></td> @php $xcont++; @endphp         
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
                            <div class="col-md-6 col-md-offset-4">
                                 <button id="boton" type="button" class="btn btn-primary" onclick="guardar_examen();">
                                    <span class="ionicons ion-ios-flask"></span> Solicitar
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
<script src="{{asset('/plugins/colorpicker/bootstrap-colorpicker.js')}}"></script>
<script src="{{asset('/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

<script type="text/javascript">
    var contador = 0; 


//Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-green').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green'
    }) 
    

    $(document).ready(function() {

        @foreach($examenes as $examen)
            @if($examen->estado=='0')
                $('#ch{{$examen->id}}').iCheck('disable');
            @endif
        @endforeach

        $("#boton").prop('disabled', true);

        $('input[type="checkbox"].flat-green').on('ifChecked', function(event){

            contador = contador + 1;

            //alert(contador);
            if(contador>0){
                $("#boton").prop('disabled', false);    
            }else{
                $("#boton").prop('disabled', true);
            }
            
            
        });

        Protocolo();

        



        $('input[type="checkbox"].flat-green').on('ifUnchecked', function(event){
            //Protocolo();
            
            

            var protocolo = document.getElementById('id_protocolo').value;
            var textid = this.id; 
            //alert(this.id);

            if(protocolo!=''){

                contador = contador - 1;    
                /* $.ajax({
                    type: 'get',
                    url: "{{ url('protocolo/busca/examen')}}/"+protocolo+"/"+this.id, //protocolo.buscaexamenid
                       
                    success: function(data){
                        //alert("ahora");
                        //console.log(data);
                        //alert(data);
                        if(data=='ok'){
                            alert("No puede quitar examen del protocolo"); 
                            $('#'+textid).prop('checked',true).iCheck('update');   
                        }else{
                            contador = contador - 1;    
                            //alert('resta');
                            //alert(contador);   
                        }
                        
                    }    
                }); */

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

        $('#fecha_orden').datetimepicker({
            format: 'YYYY/MM/DD',
            date: '{{date('Y-m-d')}}'


            });
        
        $(".breadcrumb").append('<li><a href="{{asset('orden/')}}"> Inicio</a></li>');
        $(".breadcrumb").append('<li class="active">Solicitar</li>');


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

        //$('.select2').select2();

        $('#examen').on('select2:select', function (e) {
            var data = e.params.data;
            //console.log(data);
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
                    $('#sexo').val('');
                    $('#fecha_nacimiento').val('1980/01/01');
                 
                }else{
                    //alert('Paciente ya ingresado en el sistema');
                    //console.log(data);
                    $('#nombre1').val(data.nombre1);
                    $('#nombre2').val(data.nombre2);
                    $('#apellido1').val(data.apellido1);
                    $('#apellido2').val(data.apellido2);
                    $('#sexo').val(data.sexo);
                    $('#fecha_nacimiento').val(data.fecha_nacimiento);
                    //$('#procedencia').focus();
                    busca_ordenes_hoy();
                    $("#boton").prop('disabled', false); 

                }
            }     
        });  
    
    }

    function busca_ordenes_hoy(){

        var js_paciente = document.getElementById('id').value;

        $.ajax({
            type: 'get',
            url: "{{ url('laboratorio/orden/buscar')}}/"+js_paciente, //orden_lab.buscar_orden
                       
            success: function(data){
                if(data > '0'){
                    $('#cant_ord').text('Paciente tiene: '+data+' orden(es) para el día de hoy, confirme');    
                }
                
                //alert(data);
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

    function guardar_examen(){
        var texto = '';
        var cedula = $("#id").val();
        var proc_consul = 0;
        var id_doctor_ieced = $('#id_doctor_ieced').val();
        var convenio = $('#id_convenio').val();

        // Fausto  6/10/2023

     

        let nombre1 = $("#nombre1").val();

        if(nombre1==''){
            texto = texto + "Ingrese el nombre 1 \n";
        }

        let nombre2 = $("#nombre2").val();

        if(nombre2==''){
            texto = texto + "Ingrese el nombre 2 \n";
        }

        let apellido1 = $("#apellido1").val();

        if(apellido1==''){
            texto = texto + "Ingrese el apellido 1 \n";
        }

        let sexo = $("#sexo").val();

        if(sexo==''){
            texto = texto + "Ingrese el sexo \n";
        }

        let fecha_nacimiento = $("#fecha_nacimiento").val();

        if(fecha_nacimiento==''){
            texto = texto + "Ingrese la fecha nacimiento \n";
        }

        let id_protocolo = $("#id_protocolo").val();

        // if(id_protocolo==''){
        //     texto = texto + "Ingrese el protocolo \n";
        // }
        
        let fecha_orden = $("#fecha_orden").val();

        if(fecha_orden==''){
            texto = texto + "Ingrese la fecha orden \n";
        }
        if(cedula==''){
            texto = texto + "Ingrese la Cedula \n";
        }
        if(id_doctor_ieced==''){
            texto = texto + "Seleccione al doctor \n";
        }
        if(convenio==''){
            texto = texto + "Ingrese el convenio \n";
        }
    

        if(texto != ''){

            $("#err").text(texto);
            swal.fire({
                title: texto,
                //text: "You won't be able to revert this!",
                icon: "error",
                type: 'error',
                buttons: true,
            })    
        }

        if (texto=='') {
            //div_agenda
            $.ajax({
                type: 'get',
                url: "{{ url('agenda_labs/agendar/ex_publico')}}/"+cedula,
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
    }  

</script>
@endsection
