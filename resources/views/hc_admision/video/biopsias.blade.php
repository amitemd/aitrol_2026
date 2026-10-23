
@extends('hc_admision.visita.base')

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
    .nopad {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    /*image gallery*/
    .image-checkbox {
        cursor: pointer;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
        border: 4px solid transparent;
        margin-bottom: 0;
        outline: 0;
    }
    .image-checkbox input[type="checkbox"] {
        display: none;
    }

    .image-checkbox-checked {
        border-color: #4783B0;
    }
    .image-checkbox .fa {
      position: absolute;
      color: #4A79A3;
      background-color: #fff;
      padding: 10px;
      top: 0;
      right: 0;
    }
    .image-checkbox-checked .fa {
      display: block !important;
    }
</style>

<div class="modal fade" id="foto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">

      </div>
    </div>  
</div>

 
<div class="container-fluid" >
    <div class="row ">
        <div class="col-md-9">
            <div class="box box-primary" style="margin-bottom: 5px;">
                <div class="box-header with-border" style="padding: 1px;">
                    <div class="table-responsive col-md-12">
                        <table class="table table-striped" style="margin-bottom: 0px;">
                            <tbody>
                                <tr>
                                    <td><b>Nombres</b></td><td>{{ $paciente->nombre1}} @if($paciente->nombre2 != "(N/A)"){{ $paciente->nombre2}}@endif</td>
                                    <td><b>Apellidos</b></td><td>{{ $paciente->apellido1}} @if($paciente->apellido2 != "(N/A)"){{ $paciente->apellido2}}@endif</td>
                                    <td><b>Identificación</b></td><td>{{$paciente->id}}</td>
                                </tr>
                            </tbody>
                        </table>  
                        

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <!--a type="button" href="{{ route('procedimiento.ruta', ['id' => $agenda->id])}}" class="btn btn-success btn-sm">
                Regresar
            </a-->
            <a type="button" href="{{route('hc_video.regreso',['id' => $protocolo->id, 'agenda_ori' => $agenda_ori, 'ruta' => $ruta])}}" class="btn btn-success btn-sm">
                <span class="glyphicon glyphicon-user"> Regresar</span>
            </a>
        </div>

        <div class="col-md-7">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="col-md-9">
                        <h4>GUARDADO DE BIOPSIAS</h4>
                    </div>                                                     
                </div>                           
                <div class="box-body">
                    <div class="row">
                        <div class="form-group col-xs-12{{ $errors->has('conclusiones') ? ' has-error' : '' }}">
                            <label for="conclusiones" class="col-md-12 control-label">Ingreso de Biopsias del Procedimiento</label>
                            <div class="col-md-12">
                                <form method="POST" action="{{route('hc_video.guardado_foto2_biopsias')}}" enctype="multipart/form-data" class="dropzone" id="addimage"> 
                                    <input type="hidden" name="id_hc_protocolo" value="{{$id}}">   
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <div class="fallback" >
                                        
                                    </div>
                                </form>
                            </div>  
                        </div>
                    </div> 
                </div>      
            </div>
        </div>  
        <div class="col-md-5">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="col-md-12">
                        <h4>Biopsias del Procedimiento </h4>
                    </div>                                                     
                </div>
                <div class="box-body">
                    <div class="col-md-12">
                        <div class="table-responsive col-md-12" id="fotos_agregar">
                            @foreach($imagenes as $imagen)
                            <div class="col-md-6" style='margin: 10px 0;' >
                                @php
                                    $explotar = explode( '.', $imagen->nombre);
                                    $extension = end($explotar);
                                @endphp
                                @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') || ($extension == 'JPG') || ($extension == 'JPEG') || ($extension == 'PNG'))
                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                        <img  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="90%">
                                    </a> 
                                @elseif(($extension == 'pdf'))
                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                        <img  src="{{asset('imagenes/pdf.png')}}" width="70%">
                                        <span>{{$imagen->nombre_anterior}}</span>  
                                    </a> 
                                @else
                                    @php
                                        $variable = explode('/' , asset('/hc_ima/'));
                                        $d1 = $variable[3];
                                        $d2 = $variable[4];
                                        $d3 = $variable[5];
                                        $ruta = "http%3A%2F%2F186.68.76.210%3A86%2F".$d1."%2Fstorage%2Fapp%2Fhc_ima%2F".$imagen->nombre;
                                    @endphp 
                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                        <img  src="{{asset('imagenes/office.png')}}" width="70%">
                                        <span>{{$imagen->nombre_anterior}}</span>  
                                    </a>  
                                @endif      
                            </div>   
                            @endforeach 
                        </div>
                    </div> 
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="col-md-9">
                        <h4> Biopsias sin asignar procedimiento </h4>
                    </div>                                                     
                </div>
                <div class="box-body">
                    <form role="form" method="POST" action="{{ route('hc_video.nuevas_ima_biopsias')}}">
                        {{ csrf_field() }}
                        <input type="hidden" name="id_hc_protocolo" value="{{$id}}">
                        <div class="col-md-12">
                            <div class="table-responsive col-md-12" id="fotos_agregar">

                                @foreach($imagenes2 as $imagen)   
                                <div class="col-md-2  text-center" style='' >
                                    <label class="image-checkbox">
                                        @php
                                            $explotar = explode( '.', $imagen->nombre);
                                            $extension = end($explotar);
                                        @endphp
                                        @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') || ($extension == 'JPG') || ($extension == 'JPEG') || ($extension == 'PNG'))
                                            <img  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="90%">
                                        @elseif(($extension == 'pdf'))
                                            <img  src="{{asset('imagenes/pdf.png')}}" width="90%">  
                                        @else
                                            <img  src="{{asset('imagenes/office.png')}}" width="90%">
                                        @endif
                                        <input type="checkbox" name="image[]" value="{{$imagen->id}}" />
                                        <br>
                                        <span style="text-align: center;">{{substr($imagen->created_at, 0, -9)}}</span>
                                        <i class="fa fa-check hidden"></i>
                                    </label>
                                </div>   
                                @endforeach 
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-3 col-md-offset-9">
                                <input type="submit" value="Subir" class="btn btn-primary">
                            </div>
                        </div>

                    </form> 
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
    };
    $('#foto').on('hidden.bs.modal', function(){
        $(this).removeData('bs.modal');
    });
    function regresar(){
        window.history.go(-1);
    }

    $(document).ready(function() {

        $(".breadcrumb").append('<li class="active">Historia Clinica</li>');

    });
    // image gallery
        // init the state from the input
        $(".image-checkbox").each(function () {
          if ($(this).find('input[type="checkbox"]').first().attr("checked")) {
            $(this).addClass('image-checkbox-checked');
          }
          else {
            $(this).removeClass('image-checkbox-checked');
          }
        });

        // sync the state to the input
        $(".image-checkbox").on("click", function (e) {
          $(this).toggleClass('image-checkbox-checked');
          var $checkbox = $(this).find('input[type="checkbox"]');
          $checkbox.prop("checked",!$checkbox.prop("checked"))

          e.preventDefault();
        });

        var vartiempo = setInterval(function(){ location.reload(); }, 7201000);
</script>

@include('sweet::alert')
@endsection

