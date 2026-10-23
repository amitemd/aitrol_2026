@extends('hc_admision.cardiologia.base_a')

@section('action-content')

<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/fullcalendar.min.css')}}" rel="stylesheet">
<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/fullcalendar.print.min.css')}}" rel="stylesheet" media="print">
<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/scheduler.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('/css/bootstrap-datetimepicker.css')}}">
<link href="{{asset('/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">

<div class="container-fluid" >
    <div class="row">
        <div class="col-md-6" style="padding-right: 0px;">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h4>Agendar Valoración Cardiológica</h4>
                    <p><b>Paciente: </b>{{$agenda->id_paciente}} - {{$agenda->nombre1}} {{$agenda->nombre2}} {{$agenda->apellido1}} {{$agenda->apellido2}}</p>
                </div>
                <div class="box-body">
                    <form role="form" method="POST" action="{{route('cardiologia.agendar')}}" id="frm">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
                        <!--especialidad-->
                        <input type="hidden" name="paciente" value="{{$agenda->nombre1}} {{$agenda->nombre2}} {{$agenda->apellido1}} {{$agenda->apellido2}}">
                        <input type="hidden" name="id_agenda" value="{{$agenda->id}}">
                        <input type="hidden" name="url_doctor" value="{{$url_doctor}}">
                        <div class="form-group col-md-6 {{ $errors->has('id_doctor') ? ' has-error' : '' }}" style="padding: 0px;">
                            <label for="id_doctor" class="col-md-12 control-label">Cardiologo</label>
                            <div class="col-md-12">
                                <select id="id_doctor" name="id_doctor" class="form-control input-sm" required onchange="calendario();">
                                    @foreach ($cardiologos as $cardiologo)
                                        <option  @if($cardiologo->id=='3596988777') {{"selected"}} @endif value="{{$cardiologo->id}}">{{$cardiologo->nombre1}} {{$cardiologo->apellido1}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('id_doctor'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_doctor') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group col-md-6 {{ $errors->has('fecha') ? ' has-error' : '' }}" style="padding: 0px;">
                            <label for="fecha" class="col-md-12 control-label">Fecha </label>
                            <div class="input-group date col-md-12" >
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" name="fecha" class="form-control pull-right input-sm" id="fecha" required autocomplete="off">    
                            </div>
                        </div>    
                        <!--salas-->
                        <div class="form-group col-md-6 {{ $errors->has('id_sala') ? ' has-error' : '' }}" style="padding: 0px;">
                            <label for="id_sala" class="col-md-12 control-label">Ubicación</label>
                            <div class="col-md-12">
                                <select id="id_sala" name="id_sala" class="form-control input-sm" required >
                                    <!--option value="">Seleccionar...</option-->
                                @foreach ($salas as $sala)
                                    <option @if(old('id_sala')==$sala->id){{"selected"}} @elseif($agenda->id_sala==$sala->id){{"selected"}} @endif value="{{$sala->id}}">{{$sala->nombre_sala}} / {{$sala->hospital()->first()->nombre_hospital}}</option>
                                @endforeach
                                </select> 
                                @if ($errors->has('id_sala'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_sala') }}</strong>
                                </span>
                                @endif    
                            </div>
                        </div>

                        <div class="form-group col-md-6 {{ $errors->has('observaciones') ? ' has-error' : '' }}" style="padding: 0px;">
                            <label for="observaciones" class="col-md-12 control-label">Observacion </label>
                            <div class="col-md-12" style="padding: 0px;">
                                <input type="text" name="observaciones" class="form-control input-sm" id="observaciones" autocomplete="off">    
                            </div>
                        </div>

                        <div class="form-group col-md-6 {{ $errors->has('inicio') ? ' has-error' : '' }}" style="padding: 0px;">
                            
                            <div class="col-md-12">
                                @if ($errors->has('inicio'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('inicio') }}</strong>
                                </span>
                                @endif    
                            </div>
                        </div>

                        <div class="form-group col-md-6 {{ $errors->has('fin') ? ' has-error' : '' }}" style="padding: 0px;">
                            
                            <div class="col-md-12">
                                @if ($errors->has('fin'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('fin') }}</strong>
                                </span>
                                @endif    
                            </div>
                        </div>

                        <input type="hidden" name="inicio" id="inicio">
                        <input type="hidden" name="fin" id="fin"> 
                        <div class="col-md-12">&nbsp;</div>
                        <div class="col-md-offset-5">
                            <button type="button" id="bagregar" onclick="agendar();" class="btn btn-primary">
                                <span class="glyphicon glyphicon-floppy-disk"></span> Agendar
                            </button>
                        </div>    
                    </form>  
                </div>
                <div class="box-body" id="consulta_calendario">
                       
                </div>    
            </div>
        </div>
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h4>Asignación de Valoración Cardiológica</h4>
                </div>
                <div class="box-body">
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap ">
                        <div class="table-responsive col-md-12 col-xs-12">
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                            <thead style="text-align: center; font-size: 11px;">
                              <th > Chk</th>
                              <th > Fecha</th>
                              <th > Doctor</th>
                              <th > Estado</th>
                            </thead>
                            <tbody style="font-size: 12px;">
                              @foreach($consulta_cardio as $value)
                                <tr>
                                  @php $asignado = DB::table('agenda_proc_cardiologia')->where('id_ag_procedimiento',$agenda->id)->where('id_ag_cardiologia',$value->id)->first(); @endphp  
                                  <td style="text-align: center;"><input id="ch{{$value->id}}" @if(!is_null($asignado)) checked @endif type="radio" class="flat-green" name="seleccion" value="{{$value->id}}"></td>
                                  <td>{{$value->fechaini}}</td>
                                  <td>{{$value->nombre1}} {{$value->apellido1}}</td>
                                  <td>@if($value->estado=='0')POR CONFIRMAR @elseif($value->estado=='1')CONFIRMAR @elseif($value->estado=='4') ADMISIONADO @endif</td>   
                                </tr>       
                              @endforeach  
                            </tbody>
                          </table>
                        </div>
                    </div> 

                    <a @if($agenda->estado=='-1') href="{{ route('preagenda.edit', ['id' => $agenda->id])}}" @elseif($agenda->estado=='1') href="{{ route('agenda.edit2', ['id' => $agenda->id, 'doctor' => $url_doctor])}}" @endif>
                        <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-chevron-right"></span> Continuar</button>
                    </a>
                </div>    
            </div>
        </div>
    </div>
</div> 

<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/moment.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/jquery.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/fullcalendar.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/scheduler.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/es.js')}}"></script>
<script src="{{asset('/js/bootstrap-datetimepicker.js')}}"></script>
<script src="{{asset('/plugins/colorpicker/bootstrap-colorpicker.js')}}"></script>
<script src="{{asset('/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script type="text/javascript">
    
    $(function () {

         
        
        $('#bagregar').attr('disabled',true);

        $('#fecha').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',

            defaultDate: '{{Date('Y-m-d')}}',

    
        });

        calendario();




        $("#fecha").on("dp.change", function (e) {
            calendario();
        });
    });

    $('input[type="radio"].flat-green').iCheck({
          checkboxClass: 'icheckbox_flat-green',
          radioClass   : 'iradio_flat-green'
    });


    function calendario(){
        
        $.ajax({
          type: 'post',
          url:"{{route('cardiologia.calendario')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#frm").serialize(),
          success: function(data){
            $('#consulta_calendario').empty().html(data);
          },
          error: function(data){
             //console.log(data);
          }
        });
    }

    function agendar(){
        
        $('#bagregar').attr('disabled',true);
        $('#frm').submit();
        
    }

    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    });

    $(document).ready(function () {

    @foreach($consulta_cardio as $value)
        
      $('input[type="radio"]#ch{{$value->id}}').on('ifChecked', function(){

        $.ajax({
            type: 'get',
            url:'{{route('cardiologia.asignacion',['proc' => $agenda->id, 'cardio' => $value->id])}}',
            success: function(data){
          
                alert("VALORACIÓN CARDIOLÓGICA ASIGNADA");

            }

        });

      });

    @endforeach  


});




</script>                   





@endsection