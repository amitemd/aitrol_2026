@extends('paciente.base')
@section('action-content')
<div class="modal fade" id="foto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        
      </div>
    </div>  
</div>
@foreach($historico2 as $txt)
<div class="modal fade" id="formato{{$txt->ahid}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="col-md-12">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
        </div>
        <?php echo $txt->texto; ?>  
      </div>
    </div>  
</div>
@endforeach
<div class="container-fluid" >
    <div class="row ">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="col-md-9">
                        <h2>Historial Agregado en la Agenda</h2>
                    </div>                                                     
                </div>                           
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            
                            <h5>Cantidad: {{count($historico) + count($historico2)}}</h5>
                            <div class="table-responsive col-md-12">
                                <table class="table table-bordered  dataTable" >
                                    <tbody style="font-size: 12px;">

                                        @php $count=0; @endphp    
                                        @foreach($historico as $imagen)
                                            <div class="col-md-3" style='margin: 10px 0;text-align: center;' >
                                            @php
                                                $explotar = explode( '.', $imagen->archivo);
                                                $extension = end($explotar);
                                            @endphp
                                            @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') ||  ($extension == 'JPG') || ($extension == 'JPEG') || ($extension == 'PNG'))
                                                <a data-toggle="modal" data-target="#foto" href="{{ route('agenda.imagen567', ['id' => $imagen->ahid])}}">
                                                    <img  src="{{asset('hc_agenda/'.$imagen->archivo)}}" width="90%" style="height: 140px;">
                                                </a>                                            
                                                <span>{{substr($imagen->fechaini, 0, 10)}}</span><br> 
                                                <a type="button" href="{{route('paciente.stream_image',['id' => $imagen->ahid])}}" class="btn btn-primary btn-sm" target="_blank">
                                                    <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                                </a> 
                                            @elseif(($extension == 'pdf')|| ($extension == 'PDF'))
                                                <a data-toggle="modal" data-target="#foto" href="{{ route('agenda.imagen567', ['id' => $imagen->ahid])}}">
                                                    <img  src="{{asset('imagenes/pdf.png')}}" width="90%" style="height: 140px;">   
                                                </a>
                                                <span>{{substr($imagen->fechaini, 0, 10)}}</span><br> 
                                                <!--a type="button" href="#" class="btn btn-primary btn-sm" target="_blank">
                                                    <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                                </a-->
                                            @elseif(($extension == 'mp4'))
                                                <a data-toggle="modal" data-target="#foto" href="{{ route('agenda.imagen567', ['id' => $imagen->ahid])}}">
                                                    <img  src="{{asset('imagenes/video.png')}}" width="90%" style="height: 140px;">  
                                                </a>
                                                <span>{{substr($imagen->fechaini, 0, 10)}}</span><br> 
                                                <!--a type="button" href="#" class="btn btn-primary btn-sm" target="_blank">
                                                    <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                                </a-->   
                                            @else
                                                @php
                                                    $variable = explode('/' , asset('/hc_ima/'));
                                                    $d1 = $variable[3];
                                                    $d2 = $variable[4];
                                                    $d3 = $variable[5];
                                                    
                                                @endphp 
                                                <a data-toggle="modal" data-target="#foto" href="{{ route('agenda.imagen567', ['id' => $imagen->ahid])}}">
                                                    <img  src="{{asset('imagenes/office.png')}}" width="90%" style="height: 140px;">
                                                </a>
                                                <span>{{substr($imagen->fechaini, 0, 10)}}</span><br>  
                                                <!--a type="button" href="#" class="btn btn-primary btn-sm" target="_blank">
                                                    <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                                </a--> 
                                            @endif      
                                            </div>          
                                        @endforeach
                                        @foreach($historico2 as $txt)
                                            <div class="col-md-3" style='margin: 10px 0;text-align: center;' >
                                                <a data-toggle="modal" data-target="#formato{{$txt->ahid}}" href="#">
                                                   <i class="glyphicon glyphicon-envelope" style="font-size: 75px;"></i>     
                                                </a><br>
                                                <span >{{substr($txt->fechaini, 0, 10)}}</span> 
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

    $('#foto').on('hidden.bs.modal', function(){
        
        $(this).removeData('bs.modal');
     

    });
    
    
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