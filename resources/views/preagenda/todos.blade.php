
@extends('agenda.base')

@section('action-content')
<style type="text/css">
    @foreach($agenda2 as $value)
        .a{{$value->id}}
        {
            @if($value->estado_cita == 0)
                color: black;
            @else
                color: {{ $value->color}}; 
            @endif

        }
    @endforeach
        .reuniones          
        {
            color: #023f84;
                                     
        }
    @foreach($agenda as $value)
        .a{{$value->id}}
        {
            @if($value->estado_cita == 0)
                color: black;
            @else
                color: {{ $value->color}}; 
            @endif
        }
    @endforeach
</style>


<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
<div class="container">
  <div class="row">
  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title">Tipo de Agendamiento</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="inputtipodeprocedimiento" class="col-sm-3 control-label">Tipo de Procedimiento</label>
            <div class="col-sm-9">
              <!-- oldVals' => [isset($searchingVals) ? $searchingVals['proc_consul'] : '']])-->
              <select id="proc_consul" name="proc_consul" class="form-control" onchange="campos();">
                <option value="99" >Todos</option>
                  <option value="0" >Consulta</option>                
                  <option value="1" >Procedimiento</option>                               
                  <option value="2" >Reunion</option>     
                </select>
              </div>
            </div>
          </div>
        </div>          </br>
      </div>
    

  <div id='calendar'></div>
  </div>
  <!-- /.box-body -->
</div>
</div>

<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js'></script>
<script src="{{ asset ("/plugins/fullcalendar/jquery.min.js") }}"></script>
<script src="{{ asset ("/plugins/fullcalendar/fullcalendar.js") }}"></script>
<script src="{{ asset ("/plugins/fullcalendar/es.js") }}"></script>
<script src="{{ asset ("/plugins/colorpicker/bootstrap-colorpicker.js") }}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script>
    $(document).ready(function() {
        // page is now ready, initialize the calendar...
        $('#calendar').fullCalendar({
            // put your options and callbacks here
            lang: 'es',
            locate: 'es',

            events : [
                @foreach($agenda as $value)
                {
                  id    : '{{$value->id}}',
                  className: 'a{{$value->id}}',
                  title : '{{ $value->papellido1}} {{ $value->papellido2}} {{ $value->pnombre1}}  {{ $value->pnombre2}} | Consulta |@if($value->est_amb_hos == 0) Ambulatorio @else Hospitalizado @endif | Dr. {{ $value->d1nombre1}} {{ $value->d1apellido1}} | {{ $value->nombre_seguro}} | {{ $value->nombre_sala}} /  {{ $value->nombre_hospital}}| @if($value->tipo_cita == 0) Primera vez @endif @if($value->tipo_cita == 1) Consecutivo @endif | Agendado por: {{$value->unombre1}} {{$value->uapellido1}} | Seguro: {{ $value->nombre_seguro}}',	
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                  @if($value->estado_cita == 0)
                    color: 'black',
                  @else
                    color: '{{ $value->color}}', 

                  @endif

                },
                @endforeach
                @foreach($agenda2 as $value)
                {
                  id    : '{{$value->id}}',
                  className: 'a{{$value->id}}',
                  title : '{{ $value->papellido1}} {{ $value->papellido2}} {{ $value->pnombre1}}  {{ $value->pnombre2}} | Procedimiento: {{ $value->prnombre}} |@if($value->est_amb_hos == 0) Ambulatorio @else Hospitalizado @endif | Dr. {{ $value->d1nombre1}} {{ $value->d1apellido1}} | {{ $value->nombre_seguro}} | {{ $value->nombre_sala}} /  {{ $value->nombre_hospital}}| @if($value->tipo_cita == 0) Primera vez @endif @if($value->tipo_cita == 1) Consecutivo @endif | Agendado por: {{$value->unombre1}} {{$value->uapellido1}} | Seguro: {{ $value->nombre_seguro}}' ,  
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                  @if($value->estado_cita == 0)
                    color: 'black',
                  @else
                    color: '{{ $value->color}}', 

                  @endif

                },
                @endforeach

                @foreach($agenda3 as $value)
                {
                  className: 'reuniones',
                  id    : '{{$value->id}}',
                  title : 'Reunion: {{ $value->observaciones}} | Dr. {{ $value->d1nombre1}} {{ $value->d1apellido1}} | {{ $value->nombre_sala}} /  {{ $value->nombre_hospital}} | Agendado por: {{$value->unombre1}} {{$value->uapellido1}} ',  
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                  color: '#023f84', 
                                     
                },
                @endforeach


            ],
            defaultView: 'listDay',
			      editable: false,
            selectable: true,
            header: {
				      left: 'prev,next today',
				      center: 'title',
				      right: 'listMonth,listDay',
			      },
                 
            /*eventClick: function(event, jsEvent, view){
              alert(event.id);
            }*/
        })
    });

    function campos()
{
   <?php $i = 0;?>
    var valor = document.getElementById("proc_consul").value;
    if(valor == 99){
      @foreach($agenda as $value)

        var elemento{{$i}} = document.getElementsByClassName("a{{$value->id}}");
        $(elemento{{$i}}).removeClass('oculto');
        <?php $i = $i+1;?>
      @endforeach
      @foreach($agenda2 as $value)

        var elemento{{$i}} = document.getElementsByClassName("a{{$value->id}}");
        $(elemento{{$i}}).removeClass('oculto');
        <?php $i = $i+1;?>
      @endforeach
      @foreach($agenda3 as $value)

        var elemento{{$i}} = document.getElementsByClassName("a{{$value->id}}");
        $(elemento{{$i}}).removeClass('oculto');
        <?php $i = $i+1;?>
      @endforeach
    }
    if(valor == 0){
      @foreach($agenda as $value)

        var elemento{{$i}} = document.getElementsByClassName("a{{$value->id}}");
        $(elemento{{$i}}).removeClass('oculto');
        <?php $i = $i+1;?>
      @endforeach
      @foreach($agenda2 as $value)

        var elemento{{$i}} = document.getElementsByClassName("a{{$value->id}}");
        $(elemento{{$i}}).addClass('oculto');
        <?php $i = $i+1;?>
      @endforeach
      @foreach($agenda3 as $value)

        var elemento{{$i}} = document.getElementsByClassName("a{{$value->id}}");
        $(elemento{{$i}}).addClass('oculto');
        <?php $i = $i+1;?>
      @endforeach
    }
    if(valor == 1){
      @foreach($agenda as $value)

        var elemento{{$i}} = document.getElementsByClassName("a{{$value->id}}");
        $(elemento{{$i}}).addClass('oculto');
        <?php $i = $i+1;?>
      @endforeach
      @foreach($agenda2 as $value)

        var elemento{{$i}} = document.getElementsByClassName("a{{$value->id}}");
        $(elemento{{$i}}).removeClass('oculto');
        <?php $i = $i+1;?>
      @endforeach
      @foreach($agenda3 as $value)

        var elemento{{$i}} = document.getElementsByClassName("a{{$value->id}}");
        $(elemento{{$i}}).addClass('oculto');
        <?php $i = $i+1;?>
      @endforeach
    }
    if(valor == 2){
      @foreach($agenda as $value)

        var elemento{{$i}} = document.getElementsByClassName("a{{$value->id}}");
        $(elemento{{$i}}).addClass('oculto');
        <?php $i = $i+1;?>
      @endforeach
      @foreach($agenda2 as $value)

        var elemento{{$i}} = document.getElementsByClassName("a{{$value->id}}");
        $(elemento{{$i}}).addClass('oculto');
        <?php $i = $i+1;?>
      @endforeach
      @foreach($agenda3 as $value)

        var elemento{{$i}} = document.getElementsByClassName("a{{$value->id}}");
        $(elemento{{$i}}).removeClass('oculto');
        <?php $i = $i+1;?>
      @endforeach
    }
}
</script>
@endsection
