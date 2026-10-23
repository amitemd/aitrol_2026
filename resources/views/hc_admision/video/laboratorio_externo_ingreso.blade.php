@extends('hc_admision.visita.base')
@section('action-content')
<div class="modal fade" id="foto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        
      </div>
    </div>  
</div>
                        
<div class="container-fluid" >
    <div class="row ">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="col-md-9">
                        <h4>@lang('admision.ingresoexamlabexter')</h4>
                    </div>                                                     
                </div>                           
                <div class="box-body">
                    <div class="row">
                        <div class="form-group col-xs-12{{ $errors->has('conclusiones') ? ' has-error' : '' }}">
                            <label for="conclusiones" class="col-md-12 control-label">@lang('admision.examentregado')</label>
                            <h2 for="conclusiones" class="col-md-12 control-label">@lang('laboratorio.paciente'): <b>{{$paciente->nombre1}}</b> <b>{{$paciente->apellido1}}</b></h2>
                            <label for="conclusiones" class="col-md-12 control-label">@lang('admision.selecexamarrastrerecuadro')</label>
                            <div class="col-md-12">
                                <form method="POST" action="{{route('laboratorio_externo.guardar')}}" enctype="multipart/form-data" class="dropzone" id="addimage"> 
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="id_paciente" value="{{$paciente->id }}">
                                    <div class="fallback" >
                                        
                                    </div>
                                </form>
                            </div>  
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h2>@lang('admision.histoexamlab')</h2>
                            <div class="table-responsive col-md-12">
                                <table class="table table-bordered  dataTable" >
                                    <tbody style="font-size: 12px;">
                                        @php $count=0; @endphp    
                                        @foreach($historico as $imagen)
                                        <div class="col-md-3" style='margin: 10px 0; text-align: center;' >
                                        @php
                                            $explotar = explode( '.', $imagen->nombre);
                                            $extension = end($explotar);
                                        @endphp
                                        @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png'))
                                            <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_lab_externo', ['id' => $imagen->id]) }}">
                                                <img  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="90%" style="max-height: 140px;">
                                                <span>{{$imagen->nombre_anterior}}</span>  
                                            </a>
                                            <span>{{substr($imagen->created_at, 0, 10)}}</span><br>
                                            @php $usuario = Sis_medico\User::find($imagen->id_usuariomod);  @endphp
                                            <span>@if(!is_null($usuario)) {{$usuario->nombre1}} {{$usuario->apellido1}} @endif</span><br>  
                                            <a type="button" href="{{asset('laboratorio_externo_descarga')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                                <span class="glyphicon glyphicon-download-alt"> </span>
                                            </a>
                                            <a type="button" onclick="eliminar2('{{$imagen->id}}');" class="btn btn-danger btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                                <span class="glyphicon glyphicon-trash"></span>
                                            </a>
                                        @elseif(($extension == 'pdf'))
                                            <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_lab_externo', ['id' => $imagen->id]) }}">
                                                <img  src="{{asset('imagenes/pdf.png')}}" width="90%" style="max-height: 140px;">
                                                <span>{{$imagen->nombre_anterior}}</span>    
                                            </a>
                                            <span>{{substr($imagen->created_at, 0, 10)}}</span><br> 
                                            @php $usuario = Sis_medico\User::find($imagen->id_usuariomod);  @endphp
                                            <span>@if(!is_null($usuario)) {{$usuario->nombre1}} {{$usuario->apellido1}} @endif</span><br> 
                                            <a type="button" href="{{asset('laboratorio_externo_descarga')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                                <span class="glyphicon glyphicon-download-alt"></span>
                                            </a>
                                            <a type="button" onclick="eliminar2('{{$imagen->id}}');" class="btn btn-danger btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                                <span class="glyphicon glyphicon-trash"></span>
                                            </a>

                                        @else
                                            
                                            <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_lab_externo', ['id' => $imagen->id]) }}">
                                                <img  src="{{asset('imagenes/office.png')}}" width="90%" style="height: 140px;">
                                                <span>{{$imagen->nombre_anterior}}</span>  
                                            </a>
                                            <span>{{substr($imagen->created_at, 0, 10)}}</span><br>
                                            @php $usuario = Sis_medico\User::find($imagen->id_usuariomod);  @endphp
                                            <span>@if(!is_null($usuario)) {{$usuario->nombre1}} {{$usuario->apellido1}} @endif</span><br> 
                                            <a type="button" href="{{asset('laboratorio_externo_descarga')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                                <span class="glyphicon glyphicon-download-alt"></span>
                                            </a>
                                            <a type="button" onclick="eliminar2('{{$imagen->id}}');" class="btn btn-danger btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                                <span class="glyphicon glyphicon-trash"></span>
                                            </a>  
                                        @endif      
                                        </div>          
                                        @endforeach  
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div> 
                </div>      
            </div>
        </div> 

    </div>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    Dropzone.options.addimage = {
      acceptedFiles: ".pdf, .doc, .docx, .txt, .xls, .xlsx, .jpg, .jpeg, .png, .gif", 
      init: function() {
        this.on("error", function(file, response) { 
            alert(`{{trans('admision.archivonoformatcorrectoopac')}}`);
            console.log(response);
        });
        this.on("success", function(file, response) { 
            console.log(response);
            location.reload();
        });
      }
    };
    
    
</script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    Dropzone.options.addimage = {
      acceptedFiles: ".pdf, .doc, .docx, .txt, .xls, .xlsx, .jpg, .jpeg, .png, .gif", 
      init: function() {
        this.on("error", function(file, response) { 
            alert(`{{trans('admision.archivonoformatcorrectoopac')}}`);
            console.log(response);
        });
        this.on("success", function(file, response) { 
            console.log(response);
            location.reload();
        });
      }
    };
    function eliminar2(id){
        var opcion = confirm("¿Estas Seguro que deseas eliminar la foto?");
        if (opcion == true) {
            $.ajax({
                type: 'get',
                url:'{{ url("paciente/eliminar/biopsia")}}/'+id,
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                success: function(data){
                    //console.log(data);
                    alert(data);
                    location.reload();
                }
            })
            
        }
    }

    
    
    
</script>
@endsection