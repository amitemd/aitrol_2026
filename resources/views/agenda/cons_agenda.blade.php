                
    


                <div class="box-header with-border">
                   
                        <label class="col-md-4 control-label" for="id_consultadr" style="padding-right: 0px; ">@lang('agenda.doctor'):</label>
                        <div class="col-md-6" style="padding-left: 0px; padding-right: 0px">
                            <select id="id_consultadr" name="id_consultadr" class="form-control input-sm" onchange="buscar();">
                            @foreach ($usuarios as $usuario)
                                <option @if(old('id_consultadr') != '') @if(old('id_consultadr') == $usuario->id) {{"selected"}} @endif @elseif($usuario->id == $id_doc) {{"selected"}} @endif value="{{$usuario->id}}">{{$usuario->apellido1}} @if($usuario->apellido2!='N/A'){{$usuario->apellido2}}@endif {{$usuario->nombre1}} </option>
                            @endforeach
                        </select>
                        </div>
                        <label class="col-md-4 control-label">@lang('agenda.fecha'): </label>
                        <div class="input-group date col-md-6">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" onchange="buscar();" value="@if(old('fecha_cons')!=''){{old('fecha_cons')}}@else{{$fecha_cons}}@endif" name="fecha_cons" class="form-control pull-right input-sm" id="fecha_cons" required >    
                        </div>
                        <div class="col-md-12 " >
                            <div class="col-md-12" style="text-align: center;">
                                <h4>@lang('agenda.coloresreferencia')</h4>
                            </div>
                            <div class="col-md-12" >
                                
                                  <label for="" class="col-md-6 control-label">@lang('agenda.consulta')</label>
                                
                                <div class="form-group col-md-offset-1 col-md-3" style=" height: 20px; background-color: #6666FF; ">
                                </div> 
                            
                                
                                  <label for="" class="col-md-6 control-label">@lang('agenda.procedimiento')</label>
                           
                                <div class="form-group col-md-offset-1 col-md-3" style=" height: 20px; background-color: #FF00BF; ">
                                </div> 
                            
                              
                                  <label for="" class="col-md-6 control-label">@lang('agenda.todoTipo')</label>
                               
                                <div class="form-group col-md-offset-1 col-md-3" style=" height: 20px; background-color: #61c9ff; ">
                                </div> 
                            </div>
                        </div>
                                           
                </div> 
                
                <div class="box-body">
                    <div id='calendar'>
        
        
                    </div>

    

                </div>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ('/js/calendario/moment.min.js') }}"></script>
<script src="{{ asset ("/plugins/fullcalendar/fullcalendar.js") }}"></script>
<script src="{{ asset ("/plugins/fullcalendar/es.js") }}"></script>

    <script src="{{ asset ("/plugins/colorpicker/bootstrap-colorpicker.js") }}"></script>

<script src="{{ asset ("/js/paciente.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>



        <script type="text/javascript">


            $(function () {
        
                $('#fecha_cons').datetimepicker({
                    useCurrent: false,
                    format: 'YYYY/MM/DD',

                    defaultDate: '{{$fecha_cons}}',

            
                });

                

        
                $("#fecha_cons").on("dp.change", function (e) {
                    buscar();
                });
            });


            $('#calendar').fullCalendar({
            // put your options and callbacks here
            lang: 'es',
            locate: 'es',
            defaultDate: '{{$fecha_cons}}',
            views:{
                agenda:{
                    slotDuration: "00:15:00",
                    slotLabelFormat: 'HH:mm',
                    scrollTime: "08:00:00" 
                }
            },
            events : [

                @foreach($horario as $key=>$value)
                    {
                    
                        start: '{{$value->hora_ini}}', 
                        end: '{{$value->hora_fin}}',
                        color: '@if($value->tipo == 0) #61c9ff @endif @if($value->tipo == 1) #6666FF @endif @if($value->tipo == 2) #FF00BF @endif',
                        rendering: 'background',
                        @if($value->ndia != 7)
                        dow: [ {{$value->ndia}}],
                        @endif
                        @if($value->ndia == 7)
                            dow: [0],
                        @endif
                    },
                @endforeach
                @foreach($extra as $key=>$value)
                    {
                    
                        start: '{{$value->inicio}}', 
                        end: '{{$value->fin}}',
                        color: '@if($value->tipo == 0) #61c9ff @endif @if($value->tipo == 1) #6666FF @endif @if($value->tipo == 2) #FF00BF @endif',
                        rendering: 'background',
                    },
                @endforeach
                @foreach($cagenda as $value)
                {
                  @php $agendaprocedimientos=Sis_medico\AgendaProcedimiento::where('id_agenda',$value->id)->get(); @endphp  
                  id    : '{{$value->id}}',
                  className: 'a{{$value->id}}',
                  title : '{{ $value->pnombre1}} {{ $value->papellido1}} | Proc: {{$value->nombre_procedimiento}} @if(!$agendaprocedimientos->isEmpty()) @foreach($agendaprocedimientos as $agendaproc) - {{Sis_medico\Procedimiento::find($agendaproc->id_procedimiento)->nombre}} @endforeach @endif  | {{ $value->nombre_seguro}}',    
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                   
                  @if($value->estado_cita == 0)
                    color: 'black',
                  @else
                    color: '{{ $value->color}}',
                    textColor: 'black', 

                  @endif

                },
                @endforeach
                @foreach($cagenda3 as $value)
                {
                  id    : '{{$value->id}}',
                  className: 'a{{$value->id}}',
                  title : '{{ $value->pnombre1}} {{ $value->papellido1}} | Consulta | {{ $value->nombre_seguro}}',  
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                   
                  @if($value->estado_cita == 0)
                    color: 'black',
                  @else
                    color: '{{ $value->color}}', 
                    textColor: 'black',

                  @endif

                },
                @endforeach

                @foreach($cagenda2 as $value)
                {
                  @php $sala=Sis_medico\Sala::find($value->id_sala) @endphp  
                  
                  id    : '{{$value->id}} idreunion',
                  className: 'a{{$value->id}} classreunion',
                  title : 'Reunión Programada - {{$sala->nombre_sala}}/{{Sis_medico\Hospital::find($sala->id_hospital)->nombre_hospital}} {{ $value->observaciones}} | Agendado por: {{$value->unombre1}} {{$value->uapellido1}}',  
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                  color: '#023f84',
                   


                                     
                },


                @endforeach


            ],
            defaultView: 'agendaDay',
                  editable: false,
            selectable: true,
            header: {
                      left: 'prev,next today',
                      center: 'title',
                      right: 'month,agendaWeek,agendaDay,listMonth,listDay',
                  },
                 
            
        });


            var buscar = function ()
{
    
    var id_consultadr = document.getElementById('id_consultadr').value;
    var fecha = document.getElementById('fecha_cons').value;    
    var unix =  Math.round(new Date(fecha).getTime()/1000);
    $.ajax({
        type: 'get',
        url:'{{ url('agenda/consulta_agenda')}}/'+id_consultadr+'/'+unix,   //agenda.consulta_ag
        success: function(data){
            console.log(data);
            $('#consulta_calendario').empty().html(data);
        }
    })
    
}

 
    </script>        