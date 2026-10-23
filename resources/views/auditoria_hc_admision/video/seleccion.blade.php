
@extends('hc_admision.visita.base')

@section('action-content')
  
<style type="text/css">
    .table>tbody>tr>td, .table>tbody>tr>th { 
        padding: 0.4% ;
    }  
</style>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

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
    
    .image-checkbox2 input[type="checkbox"] {
        display: none;
    }

    .image-checkbox-checked2 {
        border-color: #4783B0;
    }
    .image-checkbox2 .fa {
      position: absolute;
      color: #4A79A3;
      background-color: #fff;
      padding: 10px;
      top: 0;
      right: 0;
    }
    .image-checkbox-checked2 .fa {
      display: block !important;
    }
</style>

<div class="modal fade" id="foto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 80%;">
      <div class="modal-content" >

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
                                    <td><b>Paciente: </b></td><td style="color: red; font-weight: 700;">{{ $paciente->apellido1}} @if($paciente->apellido2 != "(N/A)"){{ $paciente->apellido2}}@endif {{ $paciente->nombre1}} @if($paciente->nombre2 != "(N/A)"){{ $paciente->nombre2}}@endif</td>
                                    <td><b>Identificación</b></td><td>{{$paciente->id}}</td>
                                </tr>
                            </tbody>
                        </table>  
                        

                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="contador" value="0">
        <div class="col-md-1">
            <a type="button" href="{{url('auditoria_agenda/horario/doctores')}}/{{$agenda->id}}" class="btn btn-success btn-sm">
                <span > Historia Clinica</span>
            </a>
        </div>
        <div class="col-md-1">
            <a type="button" href="{{url('auditoria_estudio/auditoria_editar')}}/{{$protocolo->id_protocolo_org}}/{{$agenda->id}}" class="btn btn-success btn-sm">
                <span > Procedimiento</span>
            </a>
        </div>

        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="col-md-9">
                        <h4>Imagenes del Procedimiento </h4>
                        <h5 style="font-size: 14px; color:#6E6E6E;">Seleccione imagenes que desee que salgan en el reporte</h5>
                        <h4>Imagenes Seleccionas: <span id="contador_muestra">0</span></h4>
                        @if($procedimiento_completo == "")
                        <h5 style="font-size: 14px; color:#6E6E6E;">Para descargar el reporte primero seleccione el procedimiento que se realizo </h5>
                        @endif

                        <div class="row" @if(Auth::user()->id_tipo_usuario != 11){{"style='display:none;'"}}@endif >
                          <form id="modificar_fecha" >
                            <div class="col-md-3">
                              <input type="hidden" name="id" value="{{$protocolo->id}}">
                              <input type="hidden" name="id_procedimiento" value="{{$protocolo->procedimiento->id}}">
                              <span><b>Fecha imprimible:</b></span><input type="text" name="fecha" id="fecha" value="@if($protocolo->fecha != null){{$protocolo->fecha}}@else{{substr($protocolo->created_at, 0, -9)}}@endif" class="form-control pull-right input-sm" required onchange="cambio_fecha()">
                              <br><br>
                            </div>
                            <div class="col-md-3" style="padding: 1px;">
                                <span><b>Medico que firma</b></span>
                                <select onchange="cambio_fecha();"  class="form-control input-sm" style="width: 100%;" name="id_doctor_examinador2" id="id_doctor_examinador2">
                                    @foreach($doctores as $value)

                                        <option @if($protocolo->auditoria_procedimiento ->id_doctor_examinador2 == $value->id) selected @endif value="{{$value->id}}" >{{$value->apellido1}} @if($value->apellido2 != "(N/A)"){{ $value->apellido2}}@endif {{ $value->nombre1}} @if($value->nombre2 != "(N/A)"){{ $value->nombre2}}@endif</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3" style="padding: 1px;">
                                <span><b>Medico Responsable</b></span>
                                <select onchange="cambio_fecha();"  class="form-control input-sm" style="width: 100%;" name="id_doctor_responsable" id="id_doctor_responsable">
                                    <option value="">No</option>
                                    @foreach($doctores as $value)
                                        <option @if($protocolo->auditoria_procedimiento->id_doctor_responsable == $value->id) selected @endif value="{{$value->id}}" >{{$value->apellido1}} @if($value->apellido2 != "(N/A)"){{ $value->apellido2}}@endif {{ $value->nombre1}} @if($value->nombre2 != "(N/A)"){{ $value->nombre2}}@endif</option>
                                    @endforeach
                                </select>
                            </div>

                             @php
                                $referido_agenda = \Sis_medico\Paciente::where('id', $paciente->id)->first();
                            @endphp

                             @php
                                $referido_doctor = \Sis_medico\Aud_Hc_Protocolo::where('id', $protocolo->id)->first();

                            @endphp


                            <div class="col-md-3">
                              <input type="hidden" name="id" value="{{$protocolo->id}}">
                              <input type="hidden" name="id_procedimiento" value="{{$protocolo->auditoria_procedimiento->id}}">
                              <span><b>Referido por:</b></span>
                                @php
                                    /*$referido = null;
                                    $referido_estudio = "";
                                    if($referido_doctor->referido_por != null){
                                        $referido = $referido_doctor->referido_por;
                                    }
                                    elseif(!is_null($referido_agenda->referido)){

                                         $input =  [
                                            'referido_por' => $referido_agenda->referido 
                                        ];

                                        \Sis_medico\Aud_Hc_Protocolo::where('id_protocolo_org', $protocolo->id)->update($input);
                                          
                                        $referido = $referido_agenda->referido;
                                    }*/
                                @endphp
                              <input class="form-control" onchange="cambio_fecha();" type="text" name="referido" id="referido" value="{{$referido_doctor->referido_por}}" >
                              <br><br>
                            </div>
                          </form>
                        </div>

                    </div>
                    <div class="col-md-1" style="color: #f2f2f2;">
                        <a type="button" class="btn btn-primary btn-sm" href="{{route('auditoria_hc_reporte.seleccion_descargar', ['id_protocolo' => $protocolo->id_protocolo_org])}}" data-toggle="modal" data-target="#foto">
                            <span class="glyphicon glyphicon-download-alt">  Descargar</span>
                        </a>
                    </div>                                                   
                </div>
                <!--div class="col-md-1" style="color: #f2f2f2;">
                    <a type="button" class="btn btn-primary btn-sm" id="recortar_todas">
                        <span >  Recortar Todas</span>
                    </a>
                </div-->
                <div class="box-body">
                    <div class="col-md-12">
                        <div class="table-responsive col-md-12" id="fotos_agregar">
                            @foreach($imagenes as $imagen)
                                @if(substr($imagen->nombre, -3) != "mp4")
                                <div class="col-md-4" style='margin: 10px 0; text-align: center;    ' >
                                    <label class="image-checkbox" onclick="seleccion_imagen({{$imagen->id}})">
                                        <img  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="90%" style="max-height: 180px;">
                                        <input type="checkbox" name="image[]" value="{{$imagen->id}}" @if($imagen->seleccionado == 1){{"checked"}} @endif/>
                                        <i class="fa hidden" style="font-size: 15px; font-weight: 700;"></i>
                                    </label>
                                    <!--input class="imagen_recortada" type="checkbox" value="{{$imagen->id}}" @if($imagen->seleccionado_recortada == 1){{"checked"}} @endif  ><span>Recortar Imagen</span-->
                                </div>
                                @endif   
                            @endforeach 
                        </div>
                    </div> 
                </div>
            </div>
        </div>
        <!--div class="col-md-12">
            <div class="row">
                <div class="form-group col-xs-12{{ $errors->has('conclusiones') ? ' has-error' : '' }}">
                    <label for="conclusiones" class="col-md-12 control-label">Ingreso de Imagenes del Procedimiento</label>
                    <div class="col-md-12">
                        <form method="POST" action="{{route('hc_video.guardado_foto2')}}" enctype="multipart/form-data" class="dropzone" id="addimage"> 
                                <input type="hidden" name="id_hc_protocolo" value="{{$id}}">   
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="fallback">
                                </div>
                        </form>
                    </div>  
                </div>
            </div> 
        </div-->
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="col-md-9">
                        <h4> Historial de Imagenes del Paciente </h4>
                    </div>                                                     
                </div>
                <div class="box-body">
                    <form role="form" method="POST" action="{{ route('hc_video.nuevas_ima')}}">
                        {{ csrf_field() }}
                        <!--div class="form-group">
                            <div class="col-md-3 col-md-offset-9">
                                <input type="submit" value="Subir" class="btn btn-primary">
                            </div>
                        </div-->
                        <input type="hidden" name="id_hc_protocolo" value="{{$id}}">
                        <div class="col-md-12">
                            <div class="table-responsive col-md-12" id="fotos_agregar">
                                @foreach($imagenes2 as $imagen)
                                <div class="col-md-2  text-center" style='' >
                                    <label class="image-checkbox2">
                                        @php
                                            $explotar = explode( '.', $imagen->nombre);
                                            $extension = end($explotar);
                                        @endphp
                                        @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') || ($extension == 'JPEG') || ($extension == 'PNG'))
                                            <img  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="90%" style="max-height: 84px;">
                                        @elseif(($extension == 'pdf'))
                                            <img  src="{{asset('imagenes/pdf.png')}}" width="90%" style="max-height: 84px;">  
                                        @elseif(($extension == 'mp4'))
                                            <img  src="{{asset('imagenes/video.png')}}" width="90%" style="max-height: 84px;">
                                        @else
                                            <img  src="{{asset('imagenes/office.png')}}" width="90%" style="max-height: 84px;">
                                        @endif
                                        <input type="checkbox" name="image[]" value="{{$imagen->id}}" />
                                        <br>
                                        <span style="text-align: center;">{{substr($imagen->created_at, 0, -9)}}</span>
                                        <i class="fa fa-check hidden"></i>
                                    </label>
                                    <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                    </a> 
                                    <br><br>
                                </div>   
                                @endforeach 
                            </div>
                        </div>
                        <!--div class="form-group">
                            <div class="col-md-3 col-md-offset-9">
                                <input type="submit" value="Subir" class="btn btn-primary">
                            </div>
                        </div-->

                    </form> 
                </div>
            </div>
        </div>                   
    </div>   
</div>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script>
    $('#recortar_todas').on('click', function(event, value){

        $.ajax({
            url:"{{route('seleccionar_todas.recortar', ['id_protocolo' => $protocolo->id])}}",
            dataType: "json",
            type: 'get',
            success: function(data){
                location.reload();
            },
            error: function(data){
                console.log(data);
            }
        }); 
    });
    $('#foto').on('hidden.bs.modal', function(){
        $(this).removeData('bs.modal');
        //$(this).find('#imagen_solita').empty().html('');
    });
    function seleccion_imagen(id){
        $.ajax({
            url:"{{route('hc_reporte.cambio_seleccion')}}/"+id,
            dataType: "json",
            type: 'get',
            success: function(data){
                alert(data);

            },
            error: function(data){
                console.log(data);
            }
        })  
    }
    
    // image gallery
    // init the state from the input
    $(".image-checkbox").each(function () {
      if ($(this).find('input[type="checkbox"]').first().attr("checked")) {
        $(this).addClass('image-checkbox-checked');
        contador = parseInt($('#contador').val());
        contador = contador+1;
        $('#contador').val(contador);
        $(this).find("i").text(contador);
        $('#contador_muestra').text(contador);
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
        if ($(this).hasClass('image-checkbox-checked')){      
            contador = parseInt($('#contador').val());
            contador = contador+1;
            $('#contador_muestra').text(contador);
            $('#contador').val(contador);
            //$(this).find("i").text(contador);
            var cuenta = 1;
            $(".image-checkbox").each(function () {
              if ($(this).hasClass('image-checkbox-checked')) {
                $(this).find("i").text(cuenta);
                cuenta++;
              }
            });
           //alert(contador);
        }else{
            
            
           contador = parseInt($('#contador').val());
            contador = contador-1;
            $('#contador_muestra').text(contador);
            $('#contador').val(contador);
            var cuenta2 = 1;
            $(".image-checkbox").each(function () {
              if ($(this).hasClass('image-checkbox-checked')) {
                $(this).find("i").text(cuenta2);
                cuenta2++;
              }
            });
            //alert(contador);
        }
      

      
    });


    $(document).ready(function() {

        $(".breadcrumb").append('<li class="active">Historia Clinica</li>');

        $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',
            });
        $("#fecha").on("dp.change", function (e) {
            cambio_fecha();
        });
        $('.imagen_recortada').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });

        $('.imagen_recortada').on('ifChecked', function(event, value){
            var val = this.checked ? this.value : ''; 
            $.ajax({
                url:"{{route('hc_reporte.cambio_seleccion2')}}/"+val,
                dataType: "json",
                type: 'get',
                success: function(data){
                    alert(data);
                },
                error: function(data){
                    console.log(data);
                }
            }); 
        });
        $('.imagen_recortada').on('ifClicked', function(event, value){
            var val = this.checked ? this.value : '';     
            $.ajax({
                url:"{{route('hc_reporte.cambio_seleccion2')}}/"+val,
                dataType: "json",
                type: 'get',
                success: function(data){
                    alert(data);
                },
                error: function(data){
                    console.log(data);
                }
            }); 
        });
    });   
    function cambio_fecha(){
      $.ajax({
          type: 'post',
          url:'{{route("auditoria_hc_foto.fecha_convenios")}}',
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#modificar_fecha").serialize(),
          success: function(data){
              //alert('valio');
              //console.log('{{route("hc_foto.fecha_convenios")}}');
              console.log(data);
          },
          error: function(data){
            console.log(data);
          }
      })
    }   
</script>
<script>
    Dropzone.options.addimage = {
      acceptedFiles: ".jpg, .jpeg, .png, .bmp, .jpe, .jfif, .tiff, .tif", 
      maxFiles: 50,
    };

    var myDropzone = new Dropzone("#addimage");
      myDropzone.on("queuecomplete", function(file) {
        location.reload();
      });


    
    // image gallery
    // init the state from the input
    $(".image-checkbox2").each(function () {
      if ($(this).find('input[type="checkbox"]').first().attr("checked")) {
        $(this).addClass('image-checkbox-checked2');
      }
      else {
        $(this).removeClass('image-checkbox-checked2');
      }
    });

    // sync the state to the input
    $(".image-checkbox2").on("click", function (e) {
      $(this).toggleClass('image-checkbox-checked2');
      var $checkbox = $(this).find('input[type="checkbox"]');
      $checkbox.prop("checked",!$checkbox.prop("checked"))

      e.preventDefault();
    });
    var vartiempo = setInterval(function(){ location.reload(); }, 7201000);
</script>

@include('sweet::alert')
@endsection
 
