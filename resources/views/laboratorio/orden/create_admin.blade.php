@extends('laboratorio.orden.base')

@section('action-content')
<!-- iCheck for checkboxes and radio inputs -->

<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/fullcalendar.min.css')}}" rel="stylesheet">
<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/fullcalendar.print.min.css')}}" rel="stylesheet" media="print">
<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/scheduler.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('/css/bootstrap-datetimepicker.css')}}">
<link href="{{asset('/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">

<style type="text/css">
    .icheckbox_flat-green.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }
    .sin-pad{
        padding: 0;
    }
    .table{
        font-size: 11px !important;
    }
    .cabeza{
        background-color: #00bfff;
    }
    .table td{
        padding: 3px !important;
    }

</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.css"/>
<section class="content" >
    
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="col-md-6"><h3 class="box-title">Solicitar Examen de Laboratorio Público </h3></div>
                    <div class="col-md-6"><h3 class="box-title">Paciente: {{$agenda->id_paciente}}-{{$agenda->nombre1}} {{$agenda->nombre2}} {{$agenda->apellido1}} {{$agenda->apellido2}}</h3></div>
                    
                </div>
                <div class="box-body">
                    <!--form class="form-vertical" role="form" method="POST" action="{{ route('orden.store_admin') }}"-->
                    <form class="form-vertical" role="form" id="form_aglabs">   
                        {{ csrf_field() }}
                        <input type="hidden" name="id_agenda" value="{{$agenda->id}}">
                        <div class="modal fade bs-example-modal-lg" id="modal-agenda" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content" id="div_agenda">
                                
                                </div>
                            </div>
                        </div>
                        
                        <!--sexo 1=MASCULINO 2=FEMENINO-->
                        <div class="form-group col-md-3{{ $errors->has('sexo') ? ' has-error' : '' }}">
                            <label for="sexo" class="col-md-12 control-label sin-pad">Sexo</label>
                            <div class="col-md-12 sin-pad">
                                <select id="sexo" name="sexo" class="form-control input-sm" required>
                                    <!--option value="">Seleccionar ..</option-->
                                    <option @if($agenda->sexo=='1') selected @endif value="1">MASCULINO</option>
                                    <option @if($agenda->sexo=='2') selected @endif value="2">FEMENINO</option>
                                            
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
                            <label class="col-md-12 control-label sin-pad">F.Nacimiento</label>
                            <div class="col-md-12 sin-pad">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{$agenda->fecha_nacimiento}}" name="fecha_nacimiento" class="form-control pull-right input-sm" id="fecha_nacimiento" required >

                                </div>
                                @if ($errors->has('fecha_nacimiento'))
                                <span class="help-block">
                                  <strong>{{ $errors->first('fecha_nacimiento') }}</strong>
                                </span>
                                @endif
                                
                            </div>
                        </div>

                        <div class="form-group col-md-3 {{ $errors->has('id_doctor_ieced') ? ' has-error' : '' }}">
                            <label for="id_doctor_ieced" class="col-md-12 control-label sin-pad">Médico</label>
                            <div class="col-md-12 sin-pad"> 
                                <select id="id_doctor_ieced" name="id_doctor_ieced" class="form-control input-sm" required>
                                    <option value="">Seleccione ...</option>
                                @foreach ($usuarios as $usuario)
                                    <!--option @if(old('id_doctor_ieced')!=null) @if(old('id_doctor_ieced') == $usuario->id) selected @endif @else @if($agenda->id_doctor1==$usuario->id) selected @endif @endif value="{{$usuario->id}}" @if($usuario->id=='1307189140') style="color: red;" @endif>{{$usuario->apellido1}} {{$usuario->apellido2}} {{$usuario->nombre1}}</option-->
                                    <option value="{{$usuario->id}}" @if($usuario->id=='1307189140') style="color: red;" @endif>{{$usuario->apellido1}} {{$usuario->apellido2}} {{$usuario->nombre1}}</option>
                                @endforeach
                                </select>
                                @if ($errors->has('id_doctor_ieced'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_doctor_ieced') }}</strong>
                                </span>
                                @endif 
                            </div>
                        </div>

                        <div class="form-group col-md-3{{ $errors->has('est_amb_hos') ? ' has-error' : '' }}">
                            <label for="est_amb_hos" class="col-md-12 control-label sin-pad">Tipo</label>
                            <div class="col-md-12 sin-pad">
                                <select id="est_amb_hos" name="est_amb_hos" class="form-control input-sm" required>
                                    <option @if(old('est_amb_hos')!='') @if(old('est_amb_hos')== '0') selected @endif @else @if($agenda->est_amb_hos=='0') selected @endif @endif value="0">Ambulatorio</option>
                                    <option @if(old('est_amb_hos')!='') @if(old('est_amb_hos')== '1') selected @endif @if($agenda->est_amb_hos=='1') selected @endif @endif value="1">Hospitalizado</option>
                                </select>  
                            
                                @if ($errors->has('est_amb_hos'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('est_amb_hos') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        @php 
                            $xseguro = $agenda->id_seguro;  
                            if(!is_null($historia)){
                                $xseguro = $historia->id_seguro;
                            }
                        @endphp
                        <div class="form-group col-md-3 {{ $errors->has('id_convenio') ? ' has-error' : '' }}">
                            <label for="id_convenio" class="col-md-12 control-label sin-pad">Convenio</label>
                            <div class="col-md-12 sin-pad"> 
                                <select id="id_convenio" name="id_convenio" class="form-control input-sm" required>
                                    <!--option value="">Seleccione ...</option-->
                                @foreach ($convenios as $convenio)
                                    <option @if(old('id_convenio')!=null) @if(old('id_convenio') == $convenio->id) selected @endif @else @if($convenio->id_empresa == $agenda->id_empresa && $convenio->id_seguro== $xseguro ) selected @endif @endif value="{{$convenio->id}}">{{$convenio->nombre}}-{{$convenio->nombrecomercial}}</option>
                                @endforeach
                                </select>
                                @if ($errors->has('id_convenio'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_convenio') }}</strong>
                                </span>
                                @endif 
                            </div>
                        </div>


                        <div class="form-group col-md-6 {{ $errors->has('id_protocolo') ? ' has-error' : '' }}">
                            <label for="id_protocolo" class="col-md-12 control-label sin-pad">Protocolo</label>
                            <div class="col-md-12 sin-pad"> 
                                <select id="id_protocolo" name="id_protocolo" class="form-control input-sm" onchange="Protocolo();">
                                    <option value="">Seleccione ...</option>
                                @foreach ($protocolos as $protocolo)
                                    <option @if($protocolo->pre_post=='PRE') style="color: red;" @else style="color: blue;" @endif @if(old('id_protocolo') == $protocolo->id) selected @endif value="{{$protocolo->id}}">{{$protocolo->nombre}}</option>
                                @endforeach
                                </select>
                                @if ($errors->has('id_protocolo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_protocolo') }}</strong>
                                </span>
                                @endif 
                            </div>
                        </div>

                        @php $cont=count(old('examen')) @endphp 

                        <div class="col-md-12">    
                            @php $agrupador1 = $agrupadores->find('1'); //HEMATOLOGIA @endphp                   
                            <div class="col-md-4" style="padding: 5px;">    
                                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                                    <div class="table-responsive">
                                        <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                            <thead>
                                                <tr role="row" class="cabeza">
                                                    <th colspan="4" width="90%"><b>{{$agrupador1->nombre}}</b></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr role="row">
                                                @php $contador=0; @endphp
                                                @foreach ($examenes->where('id_agrupador',1)->where('no_orden_pub',0) as $value)
                                                    <td>{{$value->nombre}}</td>
                                                    <td><input id="ch{{$value->id}}" name="ch[]" type="checkbox" class="flat-green" value="ch{{$value->id}}"></td> 
                                                    @php $contador = $contador + 1; @endphp        
                                                    @if($contador=='2')
                                                        @php $contador=0; @endphp
                                                        </tr>
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
                            
                            @php $agrupador3 = $agrupadores->find('3'); //UROANALISIS @endphp
                            <div class="col-md-4" style="padding: 5px;">    
                                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                                    <div class="table-responsive">
                                        <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                                            <thead>
                                                <tr role="row" class="cabeza">
                                                    <th width="90%"><b>{{$agrupador3->nombre}}</b></th>
                                                    <th >Sel.</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($examenes->where('id_agrupador',3)->where('no_orden_pub',0) as $value)
                                                <tr role="row">
                                                    <td>{{$value->nombre}}</td>
                                                    <td><input id="ch{{$value->id}}" name="ch[]" type="checkbox" class="flat-green" value="ch{{$value->id}}"></td>         
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                @php $agrupador4 = $agrupadores->find('4'); //COPROLOGICO @endphp 
                                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                                    <div class="table-responsive">
                                        <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                                            <thead>
                                                <tr role="row" class="cabeza">
                                                    <th width="90%"><b>{{$agrupador4->nombre}}</b></th>
                                                    <th >Sel.</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($examenes->where('id_agrupador',4)->where('no_orden_pub',0) as $value)
                                                <tr role="row">
                                                    <td>{{$value->nombre}}</td>
                                                    <td><input id="ch{{$value->id}}" name="ch[]" type="checkbox" class="flat-green" value="ch{{$value->id}}"></td>
                                                </tr> 
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                              
                                </div>
                            </div>
                            
                            @php $agrupador2 = $agrupadores->find('2'); //QUIMICA @endphp 
                            <div class="col-md-4" style="padding: 5px;">    
                                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                                    <div class="table-responsive">
                                        <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                                            <thead>
                                                <tr role="row" class="cabeza">
                                                    <th colspan="4" width="90%"><b>{{$agrupador2->nombre}}</b></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr role="row">
                                                @php $contador=0; @endphp
                                                @foreach ($examenes->where('id_agrupador',2)->where('no_orden_pub',0) as $value)
                                                    <td>{{$value->nombre}}</td>
                                                    <td><input id="ch{{$value->id}}" name="ch[]" type="checkbox" class="flat-green" value="ch{{$value->id}}"></td> 
                                                    @php $contador = $contador + 1; @endphp        
                                                    @if($contador=='2')
                                                        @php $contador=0; @endphp
                                                        </tr>
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
                        </div>

                        <div class="col-md-12" > 
                            @php $agrupador5 = $agrupadores->find('5'); //SEROLOGIA @endphp 
                            <div class="col-md-4" style="padding: 5px;">    
                                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                                    <div class="table-responsive">
                                        <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                                            <thead>
                                                <tr role="row" class="cabeza">
                                                    <th colspan="4" width="90%"><b>{{$agrupador5->nombre}}</b></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr role="row">
                                                @php $contador=0; @endphp
                                                @foreach ($examenes->where('id_agrupador',5)->where('no_orden_pub',0) as $value)
                                                    <td>{{$value->nombre}}</td>
                                                    <td><input id="ch{{$value->id}}" name="ch[]" type="checkbox" class="flat-green" value="ch{{$value->id}}"></td> 
                                                    @php $contador = $contador + 1; @endphp        
                                                    @if($contador=='2')
                                                        @php $contador=0; @endphp
                                                        </tr>
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

                            @php $agrupador6 = $agrupadores->find('6'); //BACTEROLOGIA @endphp 
                            <div class="col-md-4" style="padding: 5px;">    
                                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                                    <div class="table-responsive">
                                        <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                                            <thead>
                                                <tr role="row" class="cabeza">
                                                    <th colspan="4" width="90%"><b>{{$agrupador6->nombre}}</b></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr role="row">
                                                @php $contador=0; @endphp
                                                @foreach ($examenes->where('id_agrupador',6)->where('no_orden_pub',0) as $value)
                                                    <td>{{$value->nombre}}</td>
                                                    <td><input id="ch{{$value->id}}" name="ch[]" type="checkbox" class="flat-green" value="ch{{$value->id}}"></td> 
                                                    @php $contador = $contador + 1; @endphp        
                                                    @if($contador=='2')
                                                        @php $contador=0; @endphp
                                                        </tr>
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

                            @php $agrupador7 = $agrupadores->find('7'); //OTROS @endphp
                            <div class="col-md-4" style="padding: 5px;">    
                                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                                    <div class="table-responsive">
                                        <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                                            <thead>
                                                <tr role="row" style="background-color: #00bfff;">
                                                    <th width="90%"><b>{{$agrupador7->nombre}}</b></th>
                                                    <th >Sel.</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($examenes->where('id_agrupador',7)->where('no_orden_pub',0)->where('especial_publico',0) as $value)
                                                    <tr role="row">
                                                        <td>{{$value->nombre}}</td>
                                                        <td><input id="ch{{$value->id}}" name="ch[]" type="checkbox" class="flat-green" value="ch{{$value->id}}"></td>         
                                                    </tr>  
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                              
                                </div>
                            </div> 
                        </div>
                        
                        @php $agrupador7 = $agrupadores->find('7'); //OTROS @endphp
                        <div class="col-md-12">    
                            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                                <div class="table-responsive">
                                    <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                                        <thead>
                                            <tr role="row" style="background-color: #00bfff;">
                                                <th width="20%" ><b>{{$agrupador7->nombre}} ESPECIALES</b></th>
                                                <th width="5%" >Sel.</th>
                                                <th width="20%" ><b>{{$agrupador7->nombre}} ESPECIALES</b></th>
                                                <th width="5%" >Sel.</th>
                                                <th width="20%" ><b>{{$agrupador7->nombre}} ESPECIALES</b></th>
                                                <th width="5%" >Sel.</th>
                                                <th width="20%" ><b>{{$agrupador7->nombre}} ESPECIALES</b></th>
                                                <th width="5%" >Sel.</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $xcont=0; @endphp
                                            @foreach ($examenes->where('id_agrupador',7)->where('no_orden_pub',0)->where('especial_publico',1) as $value)
                                                @if($xcont==0)
                                                    <tr role="row">
                                                @endif        
                                                <td>{{$value->nombre}}</td>
                                                <td><input id="ch{{$value->id}}" name="ch[]" type="checkbox" class="flat-green" value="ch{{$value->id}}"></td> 
                                                @php $xcont++; @endphp         
                                                @if($xcont=='4')
                                                    </tr> 
                                                    @php $xcont=0; @endphp 
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                          
                            </div>
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
<script src="{{asset('/js/bootstrap-datetimepicker.js')}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
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

        //empresas();

        $('input[type="checkbox"].flat-green').on('ifUnchecked', function(event){
            //Protocolo();
            
            var protocolo = document.getElementById('id_protocolo').value;
            var textid = this.id; 
            //alert(this.id);
            contador = contador - 1; 
            /*if(protocolo!=''){

                $.ajax({
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
                });

            }else{

                   
                //alert('resta');
                //alert(contador); 

            }*/

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
        
        @if($url_doctor=='0')
        $(".breadcrumb").append('<li><a href="{{asset('/agenda')}}"></i> Agenda</a></li>');
        
        $(".breadcrumb").append('<li><a href="{{asset('/agenda_procedimiento/pentax_procedimiento')}}">Pentax</li>');
        @if($agenda->id_doctor1!='')
        $(".breadcrumb").append('<li><a href="{{ route('agenda.edit2', ['id' => $agenda->id, 'doctor' => $url_doctor])}}">Editar</li>');
        @else
        $(".breadcrumb").append('<li><a href="{{ route('preagenda.edit', ['id' => $agenda->id])}}">Editar</li>');
        @endif
        $(".breadcrumb").append('<li class="active">Orden</li>');
        @else
        $(".breadcrumb").append('<li><a href="{{asset('/agenda')}}"></i> Agenda</a></li>');
        $(".breadcrumb").append('<li><a href="{{ route('agenda.agenda', ['id' => $agenda->id_doctor1, 'i' => $url_doctor]) }}"></i> Doctor</a></li>');
        $(".breadcrumb").append('<li><a href="{{ route('agenda.edit2', ['id' => $agenda->id, 'doctor' => $url_doctor])}}">Editar</li>');
        $(".breadcrumb").append('<li class="active">Orden</li>');
        @endif


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
                    console.log(data);
                    if(data!='no'){
                        data.forEach(function(element){
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

    function empresas()
    {
        vseguro = document.getElementById("id_seguro").value;

        vseguro =  vseguro.trim();

        if (vseguro != ""){
                
                $.ajax({
                    type: 'get',
                    url: "{{url('convenios/admision')}}/"+vseguro+"/{{$agenda->id}}/@if(old('id_subseguro')!=''){{old('id_empresa')}}@else{{'0'}}@endif",
                    success: function(data){
                        if(data!="null"){
                            $('#div_empresa').empty().html(data);
                            $('#div_empresa').removeClass("oculto");        
                        }
                    }
                })  
            }    
    }

    function guardar_examen(){
        
        var texto = '';
        var fecha_nacimiento = $("#fecha_nacimiento").val();
        var proc_consul = {{$agenda->proc_consul}};
        var protocolo = $("#id_protocolo").val();
        var id_doctor_ieced = $('#id_doctor_ieced').val();

        if(fecha_nacimiento==''){
            texto = texto + " fecha de nacimiento";
        }
        if(id_doctor_ieced==''){
            texto = texto + " Doctor";
        }
        if(proc_consul == '1'){
            if(protocolo==''){
                texto = texto + " Protocolo de Exámenes";
            }    
        }
            

        if(texto != ''){

            $("#err").text(texto);
            swal.fire({
                title: 'complete los siguientes campos:'+texto,
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
                url: "{{route('orden_labs.aglaboratorio_nuevo',['id-agenda' => $agenda->id])}}",
                success: function(data){
                    $('#div_agenda').html(data);
                    $("#modal-agenda").modal();
                },
                error: function(data){
                    console.log(data);
                    alert('Error, no se pudo cuardar la informacion');
                }    
            }); 
            
        }    
        
    }    


    



    

</script>
@endsection
