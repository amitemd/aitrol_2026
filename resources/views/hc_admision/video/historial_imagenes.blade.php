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
                    <div class="col-md-4">
                        <h4>Historial de Imagenes</h4>
                    </div>         
                    <div class="col-md-7">
                        <h3>Paciente: {{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}} {{$paciente->apellido2}}</h3>
                    </div>                                            
                </div>                            
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h2>Historial de Imagenes</h2>
                            <h5>Cantidad de Imagenes Ingresada: {{count($historico)}}</h5>
                            <div class="table-responsive col-md-12">
                                <table class="table table-bordered  dataTable" >
                                    <tbody style="font-size: 12px;">

                                        @php $count=0; @endphp    
                                        @foreach($historico as $imagen)
                                        <div class="col-md-3" style='margin: 10px 0;text-align: center;' >
                                        @php
                                            $explotar = explode( '.', $imagen->nombre);
                                            $extension = end($explotar);
                                        @endphp
                                        @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') ||  ($extension == 'JPG') || ($extension == 'JPEG') || ($extension == 'PNG'))
                                            <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                <img  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="90%" style="height: 140px;">
                                            </a>                                            
                                            <span>{{substr($imagen->created_at, 0, 10)}}</span><br> 
                                            <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                                <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                            </a> 
                                        @elseif(($extension == 'pdf'))
                                            <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                <img  src="{{asset('imagenes/pdf.png')}}" width="90%" style="height: 140px;">   
                                            </a>
                                            <span>{{substr($imagen->created_at, 0, 10)}}</span><br> 
                                            <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                                <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                            </a>
                                        @elseif(($extension == 'mp4'))
                                            <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                <img  src="{{asset('imagenes/video.png')}}" width="90%" style="height: 140px;">  
                                            </a>
                                            <span>{{substr($imagen->created_at, 0, 10)}}</span><br> 
                                            <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                                <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                            </a>   
                                        @else
                                            @php
                                                $variable = explode('/' , asset('/hc_ima/'));
                                                $d1 = $variable[3];
                                                $d2 = $variable[4];
                                                $d3 = $variable[5];
                                                
                                            @endphp 
                                            <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                <img  src="{{asset('imagenes/office.png')}}" width="90%" style="height: 140px;">
                                            </a>
                                            <span>{{substr($imagen->created_at, 0, 10)}}</span><br>  
                                            <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                                <span class="glyphicon glyphicon-download-alt"> Descargar</span>
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
            alert('archivo no consta en el formato correcto o el paciente no existe, revise el archivo');
            console.log(response);
        });
        this.on("success", function(file, response) { 
            console.log(response);
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
            alert('archivo no consta en el formato correcto o el paciente no existe, revise el archivo');
            console.log(response);
        });
        this.on("success", function(file, response) { 
            console.log(response);
            location.reload();
        });
      }
    };
    
    
</script>
@endsection