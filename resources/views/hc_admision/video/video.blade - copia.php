
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
<script src="https://cdn.webrtc-experiment.com/RecordRTC.js"></script>

<!-- for Edge/FF/Chrome/Opera/etc. getUserMedia support -->
<script src="https://webrtc.github.io/adapter/adapter-latest.js"></script>
<script src="https://cdn.webrtc-experiment.com/DetectRTC.js"> </script>

<!-- video element -->
<link href="https://cdn.webrtc-experiment.com/getHTMLMediaElement.css" rel="stylesheet">
<script src="https://cdn.webrtc-experiment.com/getHTMLMediaElement.js"></script>

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
                        <h4>GUARDADO DE IMAGENES Y VIDEO </h4>
                    </div>                                                     
                </div>                           
                <div class="box-body">
                    <input id="id_hc_procedimientos" type="hidden" name="id_hc_procedimientos" value="{{ $id }}">   
                    <div class="row">
                        <div class="col-md-12" >
                            <video id="video" style="width: 100%" autoplay="true"/>
                        </div>
                        <div class="col-md-6">
                            <div></div>
                            <form  id="frm2"  method="POST"   enctype="multipart/form-data">
                                <input type="hidden" name="id_hc_protocolo" value="{{$id}}">   
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input style="display: none;" type="file" id="pic" name="pic" />
                                <input type="hidden" id="imageName" name="imageName" />
                                <input type="hidden" id="contentType" name="contentType" />
                                <input type="hidden" id="imageData" name="imageData" />
                                <canvas id="canvas" width="1280" height="720" style="display: none;"></canvas>
                            </form>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-md-2">
                            <button id="tomar" class="btn btn-primary">Tomar Foto</button>
                        </div>
                        <div class="col-md-5">
                            <button id="btn-start-recording" class="btn btn-primary">Start Recording</button>
                            <button id="btn-pause-recording" class="btn btn-danger" style="display: none; font-size: 15px;">Pause</button>
                        </div>
                        <div class="col-md-12">
                            <div style=" display: none;">
                                <button id="save-to-disk" class="btn btn-primary">Descargar</button>
                                <button id="upload-to-php" class="btn btn-primary">Subir</button>
                                <button id="open-new-tab" class="btn btn-primary">Abrir en nueva Pestaña</button>
                            </div>
                        </div>
                        

                    </div>
                    
                    <div style="display: none;" style="margin-top: 10px;" id="recording-player"></div>
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
                </div>      
            </div>
        </div>
        <!--  inputs para el grabado de imagenes --> 
        <input type="hidden" class="recording-media" value="record-audio-plus-video">
        <input type="hidden" class="media-container-format" value="h264">
        <input type="hidden" class="media-resolutions" value="default">
        <input type="hidden" class="media-framerates" value="default">
        <input type="hidden" class="media-bitrates" value="default">
        <div class="col-md-5">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="col-md-9">
                        <h4>Imagenes del Procedimiento </h4>
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
                        <h4> Historial de Imagenes del Paciente </h4>
                    </div>                                                     
                </div>
                <div class="box-body">
                    <form role="form" method="POST" action="{{ route('hc_video.nuevas_ima')}}">
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
                                        @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png'))
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
      acceptedFiles: ".jpg, .jpeg, .png, .bmp, .jpe, .jfif, .tiff, .tif", 
    };
    //constants
    var MAX_WIDHT = 1280,
        MAX_HEIGHT = 720;
 
    var URL = window.webkitURL || window.URL;
 
    var inputFile = document.getElementById('pic');

    var video_foto = document.getElementById('video');
    var localStream = null;
    var canvas = document.getElementById('canvas');
    var context = canvas.getContext('2d');
    var errBack = function(e) {
        console.log('Opps.. no se puede utilizar la camara', e);
    };
    navigator.getUserMedia = navigator.getUserMedia ||  
                         navigator.webkitGetUserMedia || 
                         navigator.mozGetUserMedia || 
                         navigator.msGetUserMedia;
                          
    window.URL = window.URL || 
                 window.webkitURL || 
                 window.mozURL || 
                 window.msURL;
     
    window.addEventListener('load', function() {
       
      navigator.getUserMedia({
                  video: true,
                  audio:true
                }, 
                function(stream) {
                  var src = window.URL.createObjectURL(stream),
                      video = document.querySelector('video');
                  video.src = src;      
                }, 
                function(e) {
                  console.log(e);
                });
               
        }, false);
    //captura de video
    document.getElementById('tomar').addEventListener('click', function(event) {        
        //elements

        var canvas = document.getElementById('canvas'),
            ctx = canvas.getContext('2d');

        context.drawImage(video_foto, 0, 0, 1280, 720   );
        //envio de datos por ajax
         
        var canvas = document.getElementById('canvas');
        var dataURL = canvas.toDataURL();

        //console.log("empieza guardado");
         
        
        $.ajax({ 
            type: 'post',
            url: '{{route('hc_video.guardado_foto', ['id_protocolo' => $id])}}',
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            data: { 
                imgBase64: dataURL
            },
            success: function (result) {
                anterior = $("#fotos_agregar").html();
                $("#fotos_agregar").html("<div class='col-md-6' style='margin: 10px 0;'><a data-toggle='modal' data-target='#foto' href='{{route('hc_video.mostrar_foto2')}}/"+result.id+"'><img src='{{asset('hc_ima')}}/"+result.archivo+"' width='90%'></div>"+anterior);
                console.log(result);
            }
        });
    });
    $('#foto').on('hidden.bs.modal', function(){
        $(this).removeData('bs.modal');
    });
    function regresar(){
        window.history.go(-1);
    }

    
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


        $(document).ready(function() {

            $(".breadcrumb").append('<li class="active">Historia Clinica</li>');
            $(document).keypress(function(e) {          
                tecla = (document.all) ? e.keyCode : e.which;
                if (tecla==13){
                    $("#tomar").click();
                }
                if (tecla == 112){
                    $("#btn-start-recording").click();
                }        
            });  
        });    
</script>

<script>
    (function() {
        var params = {},
            r = /([^&=]+)=?([^&]*)/g;

        function d(s) {
            return decodeURIComponent(s.replace(/\+/g, ' '));
        }

        var match, search = window.location.search;
        while (match = r.exec(search.substring(1))) {
            params[d(match[1])] = d(match[2]);

            if(d(match[2]) === 'true' || d(match[2]) === 'false') {
                params[d(match[1])] = d(match[2]) === 'true' ? true : false;
            }
        }

        window.params = params;
    })();

    function addStreamStopListener(stream, callback) {
        var streamEndedEvent = 'ended';

        if ('oninactive' in stream) {
            streamEndedEvent = 'inactive';
        }

        stream.addEventListener(streamEndedEvent, function() {
            callback();
            callback = function() {};
        }, false);

        stream.getAudioTracks().forEach(function(track) {
            track.addEventListener(streamEndedEvent, function() {
                callback();
                callback = function() {};
            }, false);
        });

        stream.getVideoTracks().forEach(function(track) {
            track.addEventListener(streamEndedEvent, function() {
                callback();
                callback = function() {};
            }, false);
        });
    }
</script>

<script>
    var video = document.createElement('video');
    video.controls = false;
    var mediaElement = getHTMLMediaElement(video, {
        title: 'Recording status: inactive',
        buttons: ['full-screen'/*, 'take-snapshot'*/],
        showOnMouseEnter: false,
        width: 720,
        onTakeSnapshot: function() {
            var canvas = document.createElement('canvas');
            canvas.width = mediaElement.clientWidth;
            canvas.height = mediaElement.clientHeight;

            var context = canvas.getContext('2d');
            context.drawImage(recordingPlayer, 0, 0, canvas.width, canvas.height);

            window.open(canvas.toDataURL('image/png'));
        }
    });
    document.getElementById('recording-player').appendChild(mediaElement);

    var div = document.createElement('section');
    mediaElement.media.parentNode.appendChild(div);
    div.appendChild(mediaElement.media);
    
    var recordingPlayer = mediaElement.media;
    var mimeType = 'video/mp4';
    var fileExtension = 'mp4';
    var type = 'video';
    var recorderType;
    var defaultWidth;
    var defaultHeight;

    var btnStartRecording = document.querySelector('#btn-start-recording');

    window.onbeforeunload = function() {
        btnStartRecording.disabled = false;
    };

    btnStartRecording.onclick = function(event) {
        var button = btnStartRecording;

        if(button.innerHTML === 'Stop Recording') {
            btnPauseRecording.style.display = 'none';
            button.disabled = true;
            button.disableStateWaiting = true;
            setTimeout(function() {
                button.disabled = false;
                button.disableStateWaiting = false;
            }, 2000);

            button.innerHTML = 'Start Recording';

            function stopStream() {
                if(button.stream && button.stream.stop) {
                    button.stream.stop();
                    button.stream = null;
                }

                if(button.stream instanceof Array) {
                    button.stream.forEach(function(stream) {
                        stream.stop();
                    });
                    button.stream = null;
                }

                videoBitsPerSecond = null;
                var html = 'Recording status: stopped';
                html += '<br>Size: ' + bytesToSize(button.recordRTC.getBlob().size);
                recordingPlayer.parentNode.parentNode.querySelector('h2').innerHTML = html;
            }

            if(button.recordRTC) {
                if(button.recordRTC.length) {
                    button.recordRTC[0].stopRecording(function(url) {
                        if(!button.recordRTC[1]) {
                            button.recordingEndedCallback(url);
                            stopStream();

                            saveToDiskOrOpenNewTab(button.recordRTC[0]);
                            return;
                        }

                        button.recordRTC[1].stopRecording(function(url) {
                            button.recordingEndedCallback(url);
                            stopStream();
                        });
                    });
                }
                else {
                    button.recordRTC.stopRecording(function(url) {
                        if(button.blobs && button.blobs.length) {
                            var blob = new File(button.blobs, getFileName(fileExtension), {
                                type: mimeType
                            });
                            
                            button.recordRTC.getBlob = function() {
                                return blob;
                            };

                            url = URL.createObjectURL(blob);
                        }

                        button.recordingEndedCallback(url);
                        saveToDiskOrOpenNewTab(button.recordRTC);
                        stopStream();
                    });
                }
            }

            return;
        }

        if(!event) return;

        button.disabled = true;

        var commonConfig = {
            onMediaCaptured: function(stream) {
                button.stream = stream;
                if(button.mediaCapturedCallback) {
                    button.mediaCapturedCallback();
                }

                button.innerHTML = 'Stop Recording';
                button.disabled = false;
            },
            onMediaStopped: function() {
                button.innerHTML = 'Start Recording';

                if(!button.disableStateWaiting) {
                    button.disabled = false;
                }
            },
            onMediaCapturingFailed: function(error) {
                console.error('onMediaCapturingFailed:', error);

                if(error.toString().indexOf('no audio or video tracks available') !== -1) {
                    alert('RecordRTC failed to start because there are no audio or video tracks available.');
                }

                if(DetectRTC.browser.name === 'Safari') return;
                
                if(error.name === 'PermissionDeniedError' && DetectRTC.browser.name === 'Firefox') {
                    alert('Firefox requires version >= 52. Firefox also requires HTTPs.');
                }

                commonConfig.onMediaStopped();
            }
        };

        mimeType = 'video/mp4\;codecs=h264';
        fileExtension = 'mp4';

        // video/mp4;codecs=avc1    
        if(isMimeTypeSupported('video/mpeg')) {
            mimeType = 'video/mpeg';
        }


        //MEDIA ERCORDER

        captureAudioPlusVideo(commonConfig);

        button.mediaCapturedCallback = function() {
            if(typeof MediaRecorder === 'undefined') { // opera or chrome etc.
                button.recordRTC = [];

                if(!params.bufferSize) {
                    // it fixes audio issues whilst recording 720p
                    params.bufferSize = 16384;
                }

                var options = {
                    type: 'audio', // hard-code to set "audio"
                    leftChannel: params.leftChannel || false,
                    disableLogs: params.disableLogs || false,
                    video: recordingPlayer
                };

                if(params.sampleRate) {
                    options.sampleRate = parseInt(params.sampleRate);
                }

                if(params.bufferSize) {
                    options.bufferSize = parseInt(params.bufferSize);
                }

                if(params.frameInterval) {
                    options.frameInterval = parseInt(params.frameInterval);
                }

                if(recorderType) {
                    options.recorderType = recorderType;
                }

                if(videoBitsPerSecond) {
                    options.videoBitsPerSecond = videoBitsPerSecond;
                }

                options.ignoreMutedMedia = false;
                var audioRecorder = RecordRTC(button.stream, options);

                options.type = type;
                var videoRecorder = RecordRTC(button.stream, options);

                // to sync audio/video playbacks in browser!
                videoRecorder.initRecorder(function() {
                    audioRecorder.initRecorder(function() {
                        audioRecorder.startRecording();
                        videoRecorder.startRecording();
                        btnPauseRecording.style.display = '';
                    });
                });

                button.recordRTC.push(audioRecorder, videoRecorder);

                button.recordingEndedCallback = function() {
                    var audio = new Audio();
                    audio.src = audioRecorder.toURL();
                    audio.controls = true;
                    audio.autoplay = true;

                    recordingPlayer.parentNode.appendChild(document.createElement('hr'));
                    recordingPlayer.parentNode.appendChild(audio);

                    if(audio.paused) audio.play();
                };
                return;
            }

            var options = {
                type: type,
                mimeType: mimeType,
                disableLogs: params.disableLogs || false,
                getNativeBlob: false, // enable it for longer recordings
                video: recordingPlayer
            };

            if(recorderType) {
                options.recorderType = recorderType;

                if(recorderType == WhammyRecorder || recorderType == GifRecorder) {
                    options.canvas = options.video = {
                        width: defaultWidth || 320,
                        height: defaultHeight || 240
                    };
                }
            }

            if(videoBitsPerSecond) {
                options.videoBitsPerSecond = videoBitsPerSecond;
            }

            if(timeSlice && typeof MediaRecorder !== 'undefined') {
                options.timeSlice = timeSlice;
                button.blobs = [];
                options.ondataavailable = function(blob) {
                    button.blobs.push(blob);
                };
            }

            options.ignoreMutedMedia = false;
            button.recordRTC = RecordRTC(button.stream, options);

            button.recordingEndedCallback = function(url) {
                setVideoURL(url);
            };

            button.recordRTC.startRecording();
            btnPauseRecording.style.display = '';
            recordingPlayer.parentNode.parentNode.querySelector('h2').innerHTML = '<img src="https://cdn.webrtc-experiment.com/images/progress.gif">';
        };
    };

    function captureVideo(config) {
        captureUserMedia({video: true}, function(videoStream) {
            config.onMediaCaptured(videoStream);

            addStreamStopListener(videoStream, function() {
                config.onMediaStopped();
            });
        }, function(error) {
            config.onMediaCapturingFailed(error);
        });
    }

    function captureAudio(config) {
        captureUserMedia({audio: true}, function(audioStream) {
            config.onMediaCaptured(audioStream);

            addStreamStopListener(audioStream, function() {
                config.onMediaStopped();
            });
        }, function(error) {
            config.onMediaCapturingFailed(error);
        });
    }

    function captureAudioPlusVideo(config) {
        captureUserMedia({video: true, audio: true}, function(audioVideoStream) {
            config.onMediaCaptured(audioVideoStream);

            if(audioVideoStream instanceof Array) {
                audioVideoStream.forEach(function(stream) {
                    addStreamStopListener(stream, function() {
                        config.onMediaStopped();
                    });
                });
                return;
            }

            addStreamStopListener(audioVideoStream, function() {
                config.onMediaStopped();
            });
        }, function(error) {
            config.onMediaCapturingFailed(error);
        });
    }

    var MY_DOMAIN = 'webrtc-experiment.com';

    function isMyOwnDomain() {
        // replace "webrtc-experiment.com" with your own domain name
        return document.domain.indexOf(MY_DOMAIN) !== -1;
    }

    function isLocalHost() {
        // "chrome.exe" --enable-usermedia-screen-capturing
        // or firefox => about:config => "media.getusermedia.screensharing.allowed_domains" => add "localhost"
        return document.domain === 'localhost' || document.domain === '127.0.0.1';
    }

    function captureScreen(config) {
        // Firefox screen capturing addon is open-sourced here: https://github.com/muaz-khan/Firefox-Extensions
        // Google Chrome screen capturing extension is open-sourced here: https://github.com/muaz-khan/Chrome-Extensions/tree/master/desktopCapture

        window.getScreenId = function(chromeMediaSource, chromeMediaSourceId) {
            var screenConstraints = {
                audio: false,
                video: {
                    mandatory: {
                        chromeMediaSourceId: chromeMediaSourceId,
                        chromeMediaSource: isLocalHost() ? 'screen' : chromeMediaSource
                    }
                }
            };

            if(DetectRTC.browser.name === 'Firefox') {
                screenConstraints = {
                    video: {
                        mediaSource: 'window'
                    }
                }
            }

            captureUserMedia(screenConstraints, function(screenStream) {
                config.onMediaCaptured(screenStream);

                addStreamStopListener(screenStream, function() {
                    // config.onMediaStopped();

                    btnStartRecording.onclick();
                });
            }, function(error) {
                config.onMediaCapturingFailed(error);

                if(isMyOwnDomain() === false && DetectRTC.browser.name === 'Chrome') {
                    // otherwise deploy chrome extension yourselves
                    // https://github.com/muaz-khan/Chrome-Extensions/tree/master/desktopCapture
                    alert('Please enable this command line flag: "--enable-usermedia-screen-capturing"');
                }

                if(isMyOwnDomain() === false && DetectRTC.browser.name === 'Firefox') {
                    // otherwise deploy firefox addon yourself
                    // https://github.com/muaz-khan/Firefox-Extensions
                    alert('Please enable screen capturing for your domain. Open "about:config" and search for "media.getusermedia.screensharing.allowed_domains"');
                }
            });
        };

        if(DetectRTC.browser.name === 'Firefox' || isLocalHost()) {
            window.getScreenId();
        }

        window.postMessage('get-sourceId', '*');
    }

    function captureAudioPlusScreen(config) {
        // Firefox screen capturing addon is open-sourced here: https://github.com/muaz-khan/Firefox-Extensions
        // Google Chrome screen capturing extension is open-sourced here: https://github.com/muaz-khan/Chrome-Extensions/tree/master/desktopCapture

        window.getScreenId = function(chromeMediaSource, chromeMediaSourceId) {
            var screenConstraints = {
                audio: false,
                video: {
                    mandatory: {
                        chromeMediaSourceId: chromeMediaSourceId,
                        chromeMediaSource: isLocalHost() ? 'screen' : chromeMediaSource
                    }
                }
            };

            if(DetectRTC.browser.name === 'Firefox') {
                screenConstraints = {
                    video: {
                        mediaSource: 'window'
                    },
                    audio: false
                }
            }

            captureUserMedia(screenConstraints, function(screenStream) {
                captureUserMedia({audio: true}, function(audioStream) {
                    var newStream = new MediaStream();

                    // merge audio and video tracks in a single stream
                    audioStream.getAudioTracks().forEach(function(track) {
                        newStream.addTrack(track);
                    });

                    screenStream.getVideoTracks().forEach(function(track) {
                        newStream.addTrack(track);
                    });

                    config.onMediaCaptured(newStream);

                    addStreamStopListener(newStream, function() {
                        config.onMediaStopped();
                    });
                }, function(error) {
                    config.onMediaCapturingFailed(error);
                });
            }, function(error) {
                config.onMediaCapturingFailed(error);

                if(isMyOwnDomain() === false && DetectRTC.browser.name === 'Chrome') {
                    // otherwise deploy chrome extension yourselves
                    // https://github.com/muaz-khan/Chrome-Extensions/tree/master/desktopCapture
                    alert('Please enable this command line flag: "--enable-usermedia-screen-capturing"');
                }

                if(isMyOwnDomain() === false && DetectRTC.browser.name === 'Firefox') {
                    // otherwise deploy firefox addon yourself
                    // https://github.com/muaz-khan/Firefox-Extensions
                    alert('Please enable screen capturing for your domain. Open "about:config" and search for "media.getusermedia.screensharing.allowed_domains"');
                }
            });
        };

        if(DetectRTC.browser.name === 'Firefox' || isLocalHost()) {
            window.getScreenId();
        }

        window.postMessage('get-sourceId', '*');
    }

    var videoBitsPerSecond;

    function setVideoBitrates() {
        var select = document.querySelector('.media-bitrates');
        var value = select.value;

        if(value == 'default') {
            videoBitsPerSecond = null;
            return;
        }

        videoBitsPerSecond = parseInt(value);
    }

    function getFrameRates(mediaConstraints) {
        if(!mediaConstraints.video) {
            return mediaConstraints;
        }
        var value = '30';

        if(value == 'default') {
            return mediaConstraints;
        }

        value = parseInt(value);

        if(DetectRTC.browser.name === 'Firefox') {
            mediaConstraints.video.frameRate = value;
            return mediaConstraints;
        }

        if(!mediaConstraints.video.mandatory) {
            mediaConstraints.video.mandatory = {};
            mediaConstraints.video.optional = [];
        }
        
        mediaConstraints.video.mandatory.minFrameRate = value;

        return mediaConstraints;
    }

    function setGetFromLocalStorage(selectors) {
        selectors.forEach(function(selector) {
            var storageItem = selector.replace(/\.|#/g, '');
            if(localStorage.getItem(storageItem)) {
                document.querySelector(selector).value = localStorage.getItem(storageItem);
            }

            addEventListenerToUploadLocalStorageItem(selector, ['change', 'blur'], function() {
                localStorage.setItem(storageItem, document.querySelector(selector).value);
            });
        });
    }

    function addEventListenerToUploadLocalStorageItem(selector, arr, callback) {
        arr.forEach(function(event) {
            document.querySelector(selector).addEventListener(event, callback, false);
        });
    }

    setGetFromLocalStorage(['.media-resolutions', '.media-framerates', '.media-bitrates', '.recording-media', '.media-container-format']);

    function getVideoResolutions(mediaConstraints) {
        if(!mediaConstraints.video) {
            return mediaConstraints;
        }

        var select = document.querySelector('.media-resolutions');
        var value = select.value;

        if(value == 'default') {
            return mediaConstraints;
        }

        value = value.split('x');

        if(value.length != 2) {
            return mediaConstraints;
        }

        defaultWidth = parseInt(value[0]);
        defaultHeight = parseInt(value[1]);

        if(DetectRTC.browser.name === 'Firefox') {
            mediaConstraints.video.width = defaultWidth;
            mediaConstraints.video.height = defaultHeight;
            return mediaConstraints;
        }

        if(!mediaConstraints.video.mandatory) {
            mediaConstraints.video.mandatory = {};
            mediaConstraints.video.optional = [];
        }

        mediaConstraints.video.mandatory.minWidth = defaultWidth;
        mediaConstraints.video.mandatory.minHeight = defaultHeight;

        return mediaConstraints;
    }

    function captureUserMedia(mediaConstraints, successCallback, errorCallback) {
        if(mediaConstraints.video == true) {
            mediaConstraints.video = {};
        }

        setVideoBitrates();

        mediaConstraints = getVideoResolutions(mediaConstraints);
        mediaConstraints = getFrameRates(mediaConstraints);

        var isBlackBerry = !!(/BB10|BlackBerry/i.test(navigator.userAgent || ''));
        if(isBlackBerry && !!(navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia)) {
            navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia;
            navigator.getUserMedia(mediaConstraints, successCallback, errorCallback);
            return;
        }

        navigator.mediaDevices.getUserMedia(mediaConstraints).then(function(stream) {
            successCallback(stream);

            setVideoURL(stream, true);
        }).catch(function(error) {
            if(error && (error.name === 'ConstraintNotSatisfiedError' || error.name === 'OverconstrainedError')) {
                alert('Your camera or browser does NOT supports selected resolutions or frame-rates. \n\nPlease select "default" resolutions.');
            }
            else if(error && error.message) {
                alert(error.message);
            }
            else {
                alert('Unable to make getUserMedia request. Please check browser console logs.');
            }

            errorCallback(error);
        });
    }



    function isMimeTypeSupported(mimeType) {
        if(DetectRTC.browser.name === 'Edge' || DetectRTC.browser.name === 'Safari' || typeof MediaRecorder === 'undefined') {
            return false;
        }

        if(typeof MediaRecorder.isTypeSupported !== 'function') {
            return true;
        }

        return MediaRecorder.isTypeSupported(mimeType);
    }


    if(DetectRTC.browser.name === 'Edge' || DetectRTC.browser.name === 'Safari') {
        // webp isn't supported in Microsoft Edge
        // neither MediaRecorder API
        // so lets disable both video/screen recording options

        console.warn('Neither MediaRecorder API nor webp is supported in ' + DetectRTC.browser.name + '. You cam merely record audio.');
    }

    function stringify(obj) {
        var result = '';
        Object.keys(obj).forEach(function(key) {
            if(typeof obj[key] === 'function') {
                return;
            }

            if(result.length) {
                result += ',';
            }

            result += key + ': ' + obj[key];
        });

        return result;
    }

    function mediaRecorderToStringify(mediaRecorder) {
        var result = '';
        result += 'mimeType: ' + mediaRecorder.mimeType;
        result += ', state: ' + mediaRecorder.state;
        result += ', audioBitsPerSecond: ' + mediaRecorder.audioBitsPerSecond;
        result += ', videoBitsPerSecond: ' + mediaRecorder.videoBitsPerSecond;
        if(mediaRecorder.stream) {
            result += ', streamid: ' + mediaRecorder.stream.id;
            result += ', stream-active: ' + mediaRecorder.stream.active;
        }
        return result;
    }

    function getFailureReport() {
        var info = 'RecordRTC seems failed. \n\n' + stringify(DetectRTC.browser) + '\n\n' + DetectRTC.osName + ' ' + DetectRTC.osVersion + '\n';

        if (typeof recorderType !== 'undefined' && recorderType) {
            info += '\nrecorderType: ' + recorderType.name;
        }

        if (typeof mimeType !== 'undefined') {
            info += '\nmimeType: ' + mimeType;
        }

        Array.prototype.slice.call(document.querySelectorAll('select')).forEach(function(select) {
            info += '\n' + (select.id || select.className) + ': ' + select.value;
        });

        if (btnStartRecording.recordRTC) {
            info += '\n\ninternal-recorder: ' + btnStartRecording.recordRTC.getInternalRecorder().name;
            
            if(btnStartRecording.recordRTC.getInternalRecorder().getAllStates) {
                info += '\n\nrecorder-states: ' + btnStartRecording.recordRTC.getInternalRecorder().getAllStates();
            }
        }

        if(btnStartRecording.stream) {
            info += '\n\naudio-tracks: ' + btnStartRecording.stream.getAudioTracks().length;
            info += '\nvideo-tracks: ' + btnStartRecording.stream.getVideoTracks().length;
            info += '\nstream-active? ' + !!btnStartRecording.stream.active;

            btnStartRecording.stream.getAudioTracks().concat(btnStartRecording.stream.getVideoTracks()).forEach(function(track) {
                info += '\n' + track.kind + '-track-' + (track.label || track.id) + ': (enabled: ' + !!track.enabled + ', readyState: ' + track.readyState + ', muted: ' + !!track.muted + ')';

                if(track.getConstraints && Object.keys(track.getConstraints()).length) {
                    info += '\n' + track.kind + '-track-getConstraints: ' + stringify(track.getConstraints());
                }

                if(track.getSettings && Object.keys(track.getSettings()).length) {
                    info += '\n' + track.kind + '-track-getSettings: ' + stringify(track.getSettings());
                }
            });
        }

        if(timeSlice && btnStartRecording.recordRTC) {
            info += '\ntimeSlice: ' + timeSlice;

            if(btnStartRecording.recordRTC.getInternalRecorder().getArrayOfBlobs) {
                var blobSizes = [];
                btnStartRecording.recordRTC.getInternalRecorder().getArrayOfBlobs().forEach(function(blob) {
                    blobSizes.push(blob.size);
                });
                info += '\nblobSizes: ' + blobSizes;
            }
        }

        else if(btnStartRecording.recordRTC && btnStartRecording.recordRTC.getBlob()) {
            info += '\n\nblobSize: ' + bytesToSize(btnStartRecording.recordRTC.getBlob().size);
        }

        if(btnStartRecording.recordRTC && btnStartRecording.recordRTC.getInternalRecorder() && btnStartRecording.recordRTC.getInternalRecorder().getInternalRecorder && btnStartRecording.recordRTC.getInternalRecorder().getInternalRecorder()) {
            info += '\n\ngetInternalRecorder: ' + mediaRecorderToStringify(btnStartRecording.recordRTC.getInternalRecorder().getInternalRecorder());
        }

        return info;
    }

    function saveToDiskOrOpenNewTab(recordRTC) {
        if(!recordRTC.getBlob().size) {
            var info = getFailureReport();
            console.log('blob', recordRTC.getBlob());
            console.log('recordrtc instance', recordRTC);
            console.log('report', info);
        }

        var fileName = getFileName(fileExtension);

        document.querySelector('#save-to-disk').parentNode.style.display = 'block';
        document.querySelector('#save-to-disk').onclick = function() {
            if(!recordRTC) return alert('No recording found.');

            var file = new File([recordRTC.getBlob()], fileName, {
                type: mimeType
            });

            invokeSaveAsDialog(file, file.name);
        };

        document.querySelector('#open-new-tab').onclick = function() {
            if(!recordRTC) return alert('No recording found.');

            var file = new File([recordRTC.getBlob()], fileName, {
                type: mimeType
            });

            window.open(URL.createObjectURL(file));
        };

        // upload to PHP server
        document.querySelector('#upload-to-php').disabled = false;
        document.querySelector('#upload-to-php').onclick = function() {
            if(!recordRTC) return alert('No recording found.');
            this.disabled = true;

            var button = this;
            uploadToPHPServer(fileName, recordRTC, function(progress, fileURL) {
                if(progress === 'ended') {
                    button.disabled = false;
                    button.innerHTML = 'Descargar del Servidor';
                    button.onclick = function() {
                        SaveFileURLToDisk(fileURL, fileName);
                    };

                    setVideoURL(fileURL);

                    var html = 'Uploaded to PHP.<br>Download using below link:<br>';
                    html += '<a href="'+fileURL+'" download="'+fileName+'" style="color: yellow; display: block; margin-top: 15px;">'+fileName+'</a>';
                    recordingPlayer.parentNode.parentNode.querySelector('h2').innerHTML = html;
                    return;
                }
                button.innerHTML = progress;
                recordingPlayer.parentNode.parentNode.querySelector('h2').innerHTML = progress;
            });
        };

        // upload to YouTube!
        document.querySelector('#upload-to-youtube').disabled = false;
        document.querySelector('#upload-to-youtube').onclick = function() {
            if(!recordRTC) return alert('No recording found.');
            this.disabled = true;

            if(isLocalHost()) {
                alert('This feature is NOT available on localhost.');
                return;
            }

            if(isMyOwnDomain() === false) {
                var url = 'https://github.com/muaz-khan/RecordRTC/wiki/Upload-to-YouTube';
                alert('YouTube API key is configured to work only on webrtc-experiment.com. Please create your own YouTube key + oAuth client-id and use it instead.\n\nWiki page: ' + url);

                // check instructions on the wiki page
                location.href = url;
                return;
            }

            var button = this;
            uploadToYouTube(fileName, recordRTC, function(percentageComplete, fileURL) {
                if(percentageComplete == 'uploaded') {
                    button.disabled = false;
                    button.innerHTML = 'Uploaded. However YouTube is still processing.';
                    button.onclick = function() {
                        window.open(fileURL);
                    };
                    return;
                }
                if(percentageComplete == 'processed') {
                    button.disabled = false;
                    button.innerHTML = 'Uploaded & Processed. Click to open YouTube video.';
                    button.onclick = function() {
                        window.open(fileURL);
                    };

                    document.querySelector('h1').innerHTML = 'Your video has been uploaded.';
                    window.scrollTo(0, 0);

                    alert('Your video has been uploaded.');
                    return;
                }
                if(percentageComplete == 'failed') {
                    button.disabled = false;
                    button.innerHTML = 'YouTube failed transcoding the video.';
                    button.onclick = function() {
                        window.open(fileURL);
                    };
                    return;
                }
                button.innerHTML = percentageComplete + '% uploaded to YouTube.';
            });
        };
    }

    function uploadToPHPServer(fileName, recordRTC, callback) {
        var blob = recordRTC instanceof Blob ? recordRTC : recordRTC.getBlob();
        
        blob = new File([blob], getFileName(fileExtension), {
            type: mimeType
        });
        //guardado anterior
        // create FormData
        /*var formData = new FormData();
        formData.append('video-filename', fileName);
        formData.append('video-blob', blob);

        callback('Uploading recorded-file to server.');

        makeXMLHttpRequest('https://webrtcweb.com/RecordRTC/', formData, function(progress) {
            if (progress !== 'upload-ended') {
                callback(progress);
                return;
            }

            var initialURL = 'https://webrtcweb.com/RecordRTC/uploads/';

            callback('ended', initialURL + fileName);
        });*/

        var formData = new FormData();
        formData.append('video-filename', fileName);
        formData.append('video-blob' + '-blob', blob);

        callback('Uploading recorded-file to server.');

        makeXMLHttpRequest('{{asset("/")}}historiaclinica/video10/grabacion_captura10', formData, function(progress) {
            if (progress !== 'upload-ended') {
                callback(progress);
                return;
            }

            var initialURL = location.href.replace(location.href.split('/').pop(), '') + 'uploads/';

            callback('ended', initialURL + fileName);
        });
    }

    function makeXMLHttpRequest(url, data, callback) {
        var request = new XMLHttpRequest();

        request.onreadystatechange = function() {
            if (request.readyState == 4 && request.status == 200) {
                if(request.responseText === 'success') {
                    callback('upload-ended');
                    return;
                }
                alert(request.responseText);
                console.log(request.responseText);
            }
        };

        request.upload.onloadstart = function() {
            callback('Upload started...');
        };

        request.upload.onprogress = function(event) {
            callback('Upload Progress ' + Math.round(event.loaded / event.total * 100) + "%");
        };

        request.upload.onload = function() {
            callback('progress-about-to-end');
        };

        request.upload.onload = function() {
            callback('Getting File URL..');
        };

        request.upload.onerror = function(error) {
            callback('Failed to upload to server');
        };

        request.upload.onabort = function(error) {
            callback('Upload aborted.');
        };

        request.open('POST', url);
        var metas = document.getElementsByTagName('meta'); 
        for (i=0; i<metas.length; i++) { 
            if (metas[i].getAttribute("name") == "csrf-token") {  
                request.setRequestHeader("X-CSRF-Token", metas[i].getAttribute("content"));
            } 
        }
        request.send(data);
    }

    function getRandomString() {
        if (window.crypto && window.crypto.getRandomValues && navigator.userAgent.indexOf('Safari') === -1) {
            var a = window.crypto.getRandomValues(new Uint32Array(3)),
                token = '';
            for (var i = 0, l = a.length; i < l; i++) {
                token += a[i].toString(36);
            }
            return token;
        } else {
            return (Math.random() * new Date().getTime()).toString(36).replace(/\./g, '');
        }
    }

    function getFileName(fileExtension) {
        var d = new Date();
        var year = d.getUTCFullYear();
        var month = d.getUTCMonth();
        var date = d.getUTCDate();
        return 'RecordRTC-' + year + month + date + '-' + getRandomString() + '.' + fileExtension;
    }

    function SaveFileURLToDisk(fileUrl, fileName) {
        var hyperlink = document.createElement('a');
        hyperlink.href = fileUrl;
        hyperlink.target = '_blank';
        hyperlink.download = fileName || fileUrl;

        (document.body || document.documentElement).appendChild(hyperlink);
        hyperlink.onclick = function() {
           (document.body || document.documentElement).removeChild(hyperlink);

           // required for Firefox
           window.URL.revokeObjectURL(hyperlink.href);
        };

        var mouseEvent = new MouseEvent('click', {
            view: window,
            bubbles: true,
            cancelable: true
        });

        hyperlink.dispatchEvent(mouseEvent);
    }

    function getURL(arg) {
        var url = arg;

        if(arg instanceof Blob || arg instanceof File) {
            url = URL.createObjectURL(arg);
        }

        if(arg instanceof RecordRTC || arg.getBlob) {
            url = URL.createObjectURL(arg.getBlob());
        }

        if(arg instanceof MediaStream || arg.getTracks || arg.getVideoTracks || arg.getAudioTracks) {
            // url = URL.createObjectURL(arg);
        }

        return url;
    }

    function setVideoURL(arg, forceNonImage) {
        var url = getURL(arg);

        var parentNode = recordingPlayer.parentNode;
        parentNode.removeChild(recordingPlayer);
        parentNode.innerHTML = '';

        var elem = 'video';
        if(type == 'gif' && !forceNonImage) {
            elem = 'img';
        }
        if(type == 'audio') {
            elem = 'audio';
        }

        recordingPlayer = document.createElement(elem);
        
        if(arg instanceof MediaStream) {
            recordingPlayer.muted = true;
        }

        recordingPlayer.addEventListener('loadedmetadata', function() {
            if(navigator.userAgent.toLowerCase().indexOf('android') == -1) return;

            // android
            setTimeout(function() {
                if(typeof recordingPlayer.play === 'function') {
                    recordingPlayer.play();
                }
            }, 2000);
        }, false);

        recordingPlayer.poster = '';

        if(arg instanceof MediaStream) {
            recordingPlayer.srcObject = arg;
        }
        else {
            recordingPlayer.src = url;
        }

        if(typeof recordingPlayer.play === 'function') {
            recordingPlayer.play();
        }

        recordingPlayer.addEventListener('ended', function() {
            url = getURL(arg);
            
            if(arg instanceof MediaStream) {
                recordingPlayer.srcObject = arg;
            }
            else {
                recordingPlayer.src = url;
            }
        });

        parentNode.appendChild(recordingPlayer);
    }
</script>


<script>
    /* cors_upload.js Copyright 2015 Google Inc. All Rights Reserved. */

    var DRIVE_UPLOAD_URL = 'https://www.googleapis.com/upload/drive/v2/files/';

    var RetryHandler = function() {
      this.interval = 1000; // Start at one second
      this.maxInterval = 60 * 1000; // Don't wait longer than a minute 
    };

    RetryHandler.prototype.retry = function(fn) {
      setTimeout(fn, this.interval);
      this.interval = this.nextInterval_();
    };

    RetryHandler.prototype.reset = function() {
      this.interval = 1000;
    };

    RetryHandler.prototype.nextInterval_ = function() {
      var interval = this.interval * 2 + this.getRandomInt_(0, 1000);
      return Math.min(interval, this.maxInterval);
    };

    RetryHandler.prototype.getRandomInt_ = function(min, max) {
      return Math.floor(Math.random() * (max - min + 1) + min);
    };

    var MediaUploader = function(options) {
      var noop = function() {};
      this.file = options.file;
      this.contentType = options.contentType || this.file.type || 'application/octet-stream';
      this.metadata = options.metadata || {
        'title': this.file.name,
        'mimeType': this.contentType
      };
      this.token = options.token;
      this.onComplete = options.onComplete || noop;
      this.onProgress = options.onProgress || noop;
      this.onError = options.onError || noop;
      this.offset = options.offset || 0;
      this.chunkSize = options.chunkSize || 0;
      this.retryHandler = new RetryHandler();

      this.url = options.url;
      if (!this.url) {
        var params = options.params || {};
        params.uploadType = 'resumable';
        this.url = this.buildUrl_(options.fileId, params, options.baseUrl);
      }
      this.httpMethod = options.fileId ? 'PUT' : 'POST';
    };

    MediaUploader.prototype.upload = function() {
      var self = this;
      var xhr = new XMLHttpRequest();

      xhr.open(this.httpMethod, this.url, true);
      xhr.setRequestHeader('Authorization', 'Bearer ' + this.token);
      xhr.setRequestHeader('Content-Type', 'application/json');
      xhr.setRequestHeader('X-Upload-Content-Length', this.file.size);
      xhr.setRequestHeader('X-Upload-Content-Type', this.contentType);

      xhr.onload = function(e) {
        if (e.target.status < 400) {
          var location = e.target.getResponseHeader('Location');
          this.url = location;
          this.sendFile_();
        } else {
          this.onUploadError_(e);
        }
      }.bind(this);
      xhr.onerror = this.onUploadError_.bind(this);
      xhr.send(JSON.stringify(this.metadata));
    };

    MediaUploader.prototype.sendFile_ = function() {
      var content = this.file;
      var end = this.file.size;

      if (this.offset || this.chunkSize) {
        // Only bother to slice the file if we're either resuming or uploading in chunks
        if (this.chunkSize) {
          end = Math.min(this.offset + this.chunkSize, this.file.size);
        }
        content = content.slice(this.offset, end);
      }

      var xhr = new XMLHttpRequest();
      xhr.open('PUT', this.url, true);
      xhr.setRequestHeader('Content-Type', this.contentType);
      xhr.setRequestHeader('Content-Range', 'bytes ' + this.offset + '-' + (end - 1) + '/' + this.file.size);
      xhr.setRequestHeader('X-Upload-Content-Type', this.file.type);
      if (xhr.upload) {
        xhr.upload.addEventListener('progress', this.onProgress);
      }
      xhr.onload = this.onContentUploadSuccess_.bind(this);
      xhr.onerror = this.onContentUploadError_.bind(this);
      xhr.send(content);
    };

    MediaUploader.prototype.resume_ = function() {
      var xhr = new XMLHttpRequest();
      xhr.open('PUT', this.url, true);
      xhr.setRequestHeader('Content-Range', 'bytes */' + this.file.size);
      xhr.setRequestHeader('X-Upload-Content-Type', this.file.type);
      if (xhr.upload) {
        xhr.upload.addEventListener('progress', this.onProgress);
      }
      xhr.onload = this.onContentUploadSuccess_.bind(this);
      xhr.onerror = this.onContentUploadError_.bind(this);
      xhr.send();
    };

    MediaUploader.prototype.extractRange_ = function(xhr) {
      var range = xhr.getResponseHeader('Range');
      if (range) {
        this.offset = parseInt(range.match(/\d+/g).pop(), 10) + 1;
      }
    };

    MediaUploader.prototype.onContentUploadSuccess_ = function(e) {
      if (e.target.status == 200 || e.target.status == 201) {
        this.onComplete(e.target.response);
      } else if (e.target.status == 308) {
        this.extractRange_(e.target);
        this.retryHandler.reset();
        this.sendFile_();
      }
    };

    MediaUploader.prototype.onContentUploadError_ = function(e) {
      if (e.target.status && e.target.status < 500) {
        this.onError(e.target.response);
      } else {
        this.retryHandler.retry(this.resume_.bind(this));
      }
    };

    MediaUploader.prototype.onUploadError_ = function(e) {
      this.onError(e.target.response); // TODO - Retries for initial upload
    };

    MediaUploader.prototype.buildQuery_ = function(params) {
      params = params || {};
      return Object.keys(params).map(function(key) {
        return encodeURIComponent(key) + '=' + encodeURIComponent(params[key]);
      }).join('&');
    };

    MediaUploader.prototype.buildUrl_ = function(id, params, baseUrl) {
      var url = baseUrl || DRIVE_UPLOAD_URL;
      if (id) {
        url += id;
      }
      var query = this.buildQuery_(params);
      if (query) {
        url += '?' + query;
      }
      return url;
    };
</script>

<script>
    var timeSlice = false;

    if(typeof MediaRecorder === 'undefined') {
       
    }
</script>

<script>
    var btnPauseRecording = document.querySelector('#btn-pause-recording');
    btnPauseRecording.onclick = function() {
        if(!btnStartRecording.recordRTC) {
            btnPauseRecording.style.display = 'none';
            return;
        }

        btnPauseRecording.disabled = true;
        if(btnPauseRecording.innerHTML === 'Pause') {
            btnStartRecording.disabled = true;
            btnStartRecording.style.fontSize = '15px';
            btnStartRecording.recordRTC.pauseRecording();
            recordingPlayer.parentNode.parentNode.querySelector('h2').innerHTML = 'Recording status: paused';
            recordingPlayer.pause();

            btnPauseRecording.style.fontSize = 'inherit';
            setTimeout(function() {
                btnPauseRecording.innerHTML = 'Resume Recording';
                btnPauseRecording.disabled = false;
            }, 2000);
        }

        if(btnPauseRecording.innerHTML === 'Resume Recording') {
            btnStartRecording.disabled = false;
            btnStartRecording.style.fontSize = 'inherit';
            btnStartRecording.recordRTC.resumeRecording();
            recordingPlayer.parentNode.parentNode.querySelector('h2').innerHTML = '<img src="https://cdn.webrtc-experiment.com/images/progress.gif">';
            recordingPlayer.play();

            btnPauseRecording.style.fontSize = '15px';
            btnPauseRecording.innerHTML = 'Pause';
            setTimeout(function() {
                btnPauseRecording.disabled = false;
            }, 2000);
        }
    };
</script>

@include('sweet::alert')
@endsection

