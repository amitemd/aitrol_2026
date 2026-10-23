@extends('activosfijos.documentos.factura.base')
@section('action-content')
<div class="modal fade" id="foto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        
      </div>
    </div>  
</div>

<div class="modal fade" id="ver_anteprima" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
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
                        <h4>Ingreso Archivos de Activo Fijo</h4>
                    </div>                                                     
                </div>                           
                <div class="box-body">
                    <div class="row">
                        <div class="form-group col-xs-12{{ $errors->has('conclusiones') ? ' has-error' : '' }}">
                            
                        
                            <label for="conclusiones" class="col-md-12 control-label">Seleccione los archivos o arrastrelos al recuadro</label>
                            <div class="col-md-12">
                                <form method="POST" action="{{route('documentofactura.guardar_archivo')}}" enctype="multipart/form-data" class="dropzone" id="addimage"> 
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="id_fac_af" id="id_fac_af" value="{{$id}}">
                                    <div class="fallback" >
                                        
                                    </div>
                                </form>
                            </div>  
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h2>Historial Archivos de Activo Fijo</h2>
                            <div class="table-responsive col-md-12">
                                <table class="table table-bordered  dataTable" >
                                    <tbody style="font-size: 12px;">
                                        
                                        @php $count=0; @endphp    
                                        @foreach($imp_archivos as $imagen)
                                        <div class="col-md-3" style='margin: 10px 0; text-align: center;' >
                                        @php
                                            $explotar = explode( '.', $imagen->nombre);
                                            $extension = end($explotar);
                                        @endphp
                                        @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png'))
                                           
                                                <img  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="90%" style="max-height: 140px;">
                                                <span>{{$imagen->nombre_archivo}}</span>  
                                            
                                            <span>{{substr($imagen->created_at, 0, 10)}}</span><br> 
                                            <a type="button" href="{{asset('contable/activofijo/archivo_descarga')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                                <span class="glyphicon glyphicon-download-alt"> </span>
                                            </a>
                                            <a type="button" onclick="eliminar2('{{$imagen->id}}');" class="btn btn-danger btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                                <span class="glyphicon glyphicon-trash"></span>
                                            </a>
                                        @elseif(($extension == 'pdf'))
                                           
                                                <img  src="{{asset('imagenes/pdf.png')}}" width="90%" style="max-height: 140px;">
                                                <span>{{$imagen->nombre_archivo}}</span>    
                                          
                                            <span>{{substr($imagen->created_at, 0, 10)}}</span><br> 
                                            <a type="button" href="{{asset('contable/activofijo/archivo_descarga')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                                <span class="glyphicon glyphicon-download-alt"></span>
                                            </a>
                                            <a type="button" onclick="eliminar2('{{$imagen->id}}');" class="btn btn-danger btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                                <span class="glyphicon glyphicon-trash"></span>
                                            </a>

                                        @else
                                            @php
                                                $variable = explode('/' , asset('/hc_ima/'));
                                                $d1 = $variable[3];
                                                $d2 = $variable[4];
                                                $d3 = $variable[5];
                                                
                                            @endphp 
                                          
                                                <img  src="{{asset('imagenes/office.png')}}" width="90%" style="height: 140px;">
                                                <span>{{$imagen->nombre_archivo}}</span>  <br>
                                       
                                            <span>{{substr($imagen->created_at, 0, 10)}}</span><br> 
                                            <a type="button" href="{{asset('contable/activofijo/archivo_descarga')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                                <span class="glyphicon glyphicon-download-alt"></span>
                                            </a>
                                            <a type="button" onclick="eliminar2('{{$imagen->id}}');" class="btn btn-danger btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                                <span class="glyphicon glyphicon-trash"></span>
                                            </a>  
                                            <a data-remote="{{ route('documentofactura.ver_anteprima',['id_imagen'=>$imagen->id])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#ver_anteprima">
                                                <span class="glyphicon glyphicon-eye-open"></span>
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
            alert('archivo no consta en el formato correcto o el paciente no existe, revise el archivo');
            console.log(response);
        });
        this.on("success", function(file, response) { 
            console.log(response);
            location.reload();
        });
      }
    };
    function eliminar2(id){
        var opcion = confirm("¿Estas Seguro que deseas eliminar?");
        if (opcion == true) {
            $.ajax({
                type: 'get',
                url:'{{ url("contable/activofijo/eliminar_archivo")}}/'+id,
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                success: function(data){
                    //console.log(data);
                   // alert(data);
                    location.reload();
                }
            })
            
        }
    }

    
    
    
</script>
@endsection