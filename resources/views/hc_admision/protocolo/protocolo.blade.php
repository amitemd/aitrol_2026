
@extends('hc_admision.anestesiologia.base') 

@section('action-content')
 
<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
}  
</style>
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">

  <style type="text/css">
  .icheckbox_flat-green.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }
    #mceu_61{
        display: none;
    }
</style>
 
 <div class="modal fade" id="favoritesModal2" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document" style="width:1350px; " >
    <div class="modal-content"  id="imprimir3">
       <p>Hola Mundo</p>
    </div>
  </div>
</div>
<div class="modal fade" id="favoritesModal" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document" style="width:70%;">
    <div class="modal-content" >
    </div>
  </div>
</div>
 
<div class="container-fluid" >
    <div class="row ">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <a class="form-group col-md-3 col-sm-3 col-xs-3" href="{{ route('agenda.detalle',['id' => $agenda->id]) }}"><button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-level-up"></span> Detalles</button>
                    </a>
                    <a class="form-group col-md-3 col-sm-3 col-xs-3" href="{{route('historia.historia',['id' => $agenda->id])}}"><button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-level-up"></span> Historia Clinica</button>
                    </a>
                    <a class="form-group col-md-3 col-sm-3 col-xs-3" href="{{route('procedimientos_historia.mostrar',['id' => $agenda->id])}}"><button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-triangle-left"></span> Procedimientos</button>
                    </a>  
                    @php $especialidad=Sis_medico\Especialidad::find($agenda->espid); @endphp
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <td><b>Paciente:</b></td>
                                <td colspan="3">{{$agenda->id_paciente}} - {{$agenda->pnombre1}} @if($agenda->pnombre2 != "(N/A)"){{ $agenda->pnombre2}}@endif {{ $agenda->papellido1}} @if($agenda->papellido2 != "(N/A)"){{ $agenda->papellido2}}@endif</td>
                                <td><b>Edad:</b></td>
                                <td><span id="edad"></span></td>
                                <td><b>Seguro:</b></td>
                                <td>{{$seguro->nombre}}</td>
                            </tr>

                            <tr>
                                <td><b>Procedimientos:</b></td>
                                <td colspan="3">{{$hc_procedimientos->procedimiento_completo->nombre_general}}</td>
                            </tr>                          
                        </tbody>
                    </table>
                    <div class="w3-bar w3-blue">
                        <button class="w3-bar-item w3-button tablink"onclick="location.href = '{{route('epicrisis.diagnostico',['id' => $hc_procedimientos->id])}}' ">DIAGNOSTICO</button>
                        <button class="w3-bar-item w3-button tablink"onclick="location.href = '{{route('anestesiologia.mostrar',['id' => $hc_procedimientos->id])}}'">ANESTESIOLOGÍA</button>
                        <button class="w3-bar-item w3-button tablink w3-red" onclick="location.href = '{{route('protocolo.mostrar',['id' => $hc_procedimientos->id])}}'">PROTOCOLO</button>

                        <button class="w3-bar-item w3-button tablink" onclick="location.href = '{{route('evolucion.evolucion',['id' => $hc_procedimientos->id])}}'">EVOLUCIÓN</button>
                        <button class="w3-bar-item w3-button tablink" onclick="location.href = '{{route('epicrisis.mostrar',['id' => $hc_procedimientos->id])}}'">EPICRISIS</button>
                        <button class="w3-bar-item w3-button tablink " onclick="location.href = '{{route('hc_video.mostrar',['id' => $id])}}'">VIDEO</button>
                    </div>

                    <div id="tab1" class="w3-container w3-border city" >
                        <div class="box ">
                            <div class="box-header with-border">
                                <div class="col-md-9">
                                    <h4>PROTOCOLO </h4>
                                </div>
                                @if($anestesiologia != "")
                                    @if($protocolo != "")   
                                    <div class="col-md-3">
                                        <a href="{{ route('hc_protocolo.imprime', ['id' => $protocolo->id]) }}" type="button" class="btn btn-primary">
                                                Descargar
                                        </a>
                                    </div>
                                    @endif
                                @else
                                <div class="col-md-9">
                                    <h6 style="color: #555555;">* Falta llenar Record Anestesico </h6>
                                </div>    
                                @endif                                                      
                            </div>
                            <div class="box-body">
                                
                                <form class="form-vertical" role="form" method="POST" action="{{ route('protocolo.crea_actualiza') }}" >
                                    <input type="hidden" name="id_hc_procedimientos" value="{{ $id }}">   
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                    <!--hora_ini-->  
                                    <div class="form-group col-md-12{{ $errors->has('fecha') ? ' has-error' : '' }}">
                                        <label for="fecha" class="col-md-12 control-label" >Fecha</label>
                                        <input min="2012-01-01" id="fecha" required type="date" class="form-control input-md-4" name="fecha" value=@if(old('fecha')!='')"{{old('fecha')}}"@elseif($protocolo != ''){{ $protocolo->fecha }}@endif >
                                        @if ($errors->has('fecha'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('fecha') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--hora_ini-->  
                                    <div class="form-group col-md-4{{ $errors->has('hora_ini') ? ' has-error' : '' }}">
                                        <label for="hora_ini" class="col-md-12 control-label" >Hora de Inicio</label>
                                        <input id="hora_ini" required type="time" class="form-control input-sm" name="hora_ini" value=@if(old('hora_ini')!='')"{{old('hora_ini')}}"@elseif($protocolo != ''){{ $protocolo->hora_inicio }}@endif >
                                        @if ($errors->has('hora_ini'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('hora_ini') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--hora_fin-->  
                                    <div class="form-group col-md-4{{ $errors->has('hora_fin') ? ' has-error' : '' }}">
                                        <label for="hora_fin" class="col-md-12 control-label" >Hora de Fin</label>
                                        <input id="hora_fin" required type="time" class="form-control input-sm" name="hora_fin" value=@if(old('hora_fin')!='')"{{old('hora_fin')}}"@elseif($protocolo != ''){{ $protocolo->hora_fin }}@endif >
                                        @if ($errors->has('hora_fin'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('hora_fin') }}</strong>
                                        </span> 
                                        @endif
                                    </div>

                                    <div class="form-group col-md-4{{ $errors->has('complicaciones') ? ' has-error' : '' }}">
                                        <label for="complicaciones" class="col-md-12 control-label" >Complicaciones Transoperatorias</label>
                                        <input id="complicaciones" required class="form-control input-sm" name="complicaciones" value="@if(old('complicaciones')!='')"{{old('complicaciones')}}"@elseif($protocolo != ''){{ $protocolo->complicaciones }}@endif" >
                                        @if ($errors->has('complicaciones'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('complicaciones') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group col-md-4{{ $errors->has('estado_final') ? ' has-error' : '' }}">
                                        <label for="estado_final" class="col-md-12 control-label" >Estado del paciente al terminar la Operación</label>
                                        <input id="estado_final" required class="form-control input-sm" name="estado_final" value="@if(old('estado_final')!='')"{{old('estado_final')}}"@elseif($protocolo != ''){{ $protocolo->estado_final }}@endif" >
                                        @if ($errors->has('estado_final'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('estado_final') }}</strong>
                                        </span>
                                        @endif
                                    </div>  
                                    <!--Hallazgos-->
                                    <div class="form-group col-xs-12{{ $errors->has('hallazgos') ? ' has-error' : '' }}">
                                        <label for="hallazgos" class="col-md-12 control-label">Hallazgos</label>
                                        <div class="col-md-12">
                                             <textarea id="hallazgos"  name="hallazgos" style="width: 100%; height: 200px;"><?php echo                  $hallazgos;?></textarea>
                                        
                                            @if ($errors->has('hallazgos'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('hallazgos') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-9">
                                            <button type="submit" class="btn btn-primary">
                                                    Guardar
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                @if($protocolo != "")
                                    <div class="row">
                                        <div class="form-group col-xs-12{{ $errors->has('conclusiones') ? ' has-error' : '' }}">
                                            <label for="conclusiones" class="col-md-12 control-label">Ingreso de Imagenes del Procedimiento</label>
                                            <div class="col-md-10">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading">Listado de Fotos</div>
                                                    <div class="panel-body">
                                                        <div class="row">
                                                        @foreach($fotos as $thumbnail)
                                                            <div class="col-sm-6 col-md-4">
                                                                <div class="thumbnail">
                                                                    <a href="{{ route('procedimiento.imagen', ['id' => $thumbnail->id])}}" data-toggle="modal" data-target="#favoritesModal">
                                                                        <img src="{{asset($thumbnail->ruta.$thumbnail->archivo)}}" style="width: 120px; height: 120px;" alt="{{$thumbnail->tipo_documento}}">
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <form method="POST" action="{{route('hc_protocolo.fotos')}}" enctype="multipart/form-data" class="dropzone" id="addimage"> 
                                                    <input type="hidden" name="_token" value="{{ csrf_token()}}">
                                                    <input type="hidden" name="id_hc_protocolo" value="{{ $protocolo->id_hc_procedimientos }}"> 
                                                </form>
                                            </div>  
                                        </div>
                                    </div>
                                @endif 
                                <img src="file:///Z:/Imagenes/ALVARADO%20BARREZUETA%20ALEXIS_3267/3267_22948.jpeg" alt="" />        
                            </div>
                        </div> 
                    </div>


<script>

</script> 
                </div>
            </div>
        </div>  

        
        

    </div>
</div>
<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script>
  tinymce.init({
    selector: '#hallazgos',
    required: true,
  }); 
  tinymce.init({
    selector: '#conclusiones',
    required: true,
  }); 
</script>  
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script>
    $('input[type="checkbox"].flat-green').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green'
    }) 
</script>

@include('sweet::alert')
@endsection

