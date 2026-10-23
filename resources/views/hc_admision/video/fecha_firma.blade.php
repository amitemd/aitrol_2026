<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<div class="container-fluid" >
    <div class="row">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
        <br>
        <hr>
    </div>
    <div class="row">
        <form class="form-vertical" role="form" method="POST" action="{{route('hc_reporte.descargar3')}}" >
            {{ csrf_field() }}
            <div class="col-md-4">
              <input type="hidden" name="id" value="{{$protocolo->id}}">
              <input type="hidden" name="tipo" value="{{$tipo}}">
              <input type="hidden" name="id_procedimiento" value="{{$protocolo->procedimiento->id}}">
              <span><b>Fecha imprimible:</b></span><input type="text" name="fecha" id="fecha" value="@if($protocolo->fecha != null){{$protocolo->fecha}}@else{{substr($protocolo->created_at, 0, -9)}}@endif" class="form-control pull-right input-sm" required>
              <br><br>
            </div>
            <div class="col-md-4" style="padding: 1px;">
                <span><b>Medico que firma</b></span>
                <select class="form-control input-sm" style="width: 100%;" name="id_doctor_examinador2" id="id_doctor_examinador2">
                    @foreach($doctores as $value)
                        <option @if($protocolo->procedimiento->id_doctor_examinador2 == $value->id) selected @endif value="{{$value->id}}" >{{$value->apellido1}} @if($value->apellido2 != "(N/A)"){{ $value->apellido2}}@endif {{ $value->nombre1}} @if($value->nombre2 != "(N/A)"){{ $value->nombre2}}@endif</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-12"></div>
            <div class="form-group col-md-12">
                <div class="col-md-6 col-md-offset-4">
                    <button type="submit" class="btn btn-primary" formtarget="_blank">
                     Guardar y Descargar
                    </button>
                </div>
            </div>
          </form>
    </div>
    <div> 
        <hr>
        <br>
    </div>
</div>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
    $(function () {
        $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD'
        });
    });    
</script>