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
    .parpadea {

      animation-name: parpadeo;
      animation-duration: 1s;
      animation-timing-function: linear;
      animation-iteration-count: infinite;

      -webkit-animation-name:parpadeo;
      -webkit-animation-duration: 1s;
      -webkit-animation-timing-function: linear;
      -webkit-animation-iteration-count: infinite;
    }

    @-moz-keyframes parpadeo{  
      0% { opacity: 1.0; }
      50% { opacity: 0.0; }
      100% { opacity: 1.0; }
    }

    @-webkit-keyframes parpadeo {  
      0% { opacity: 1.0; }
      50% { opacity: 0.0; }
       100% { opacity: 1.0; }
    }

    @keyframes parpadeo {  
          0% { opacity: 1.0; }
           50% { opacity: 0.0; }
          100% { opacity: 1.0; }
    }

</style>


<script src="{{asset('hc4/js/jquery-3.3.1.js')}}"></script>
<script src="{{ asset ("/js/dropzone.js") }}"></script>
<script src="{{asset('hc4/js/grabador/RecordRTC.js')}}"></script>
<script src="{{asset('hc4/js/grabador/gif-recorder.js')}}"></script>
<script src="{{asset('hc4/js/grabador/getScreenId.js')}}"></script>

<!-- for Edige/FF/Chrome/Opera/etc. getUserMedia support -->
<script src="{{asset('plugins/video/gumadapter.js')}}"></script>


<div class="modal fade" id="foto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">

      </div>
    </div>  
</div>

<div class="box" style="border: 2px solid #004AC1; background-color: #56ABE3; border-radius: 3px; ">
  <div class="box-header with-border" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004ac1;">
    <h3 class="box-title"><img style="width: 41px; margin-left: 15px" src="{{asset('/')}}hc4/img/iconos/imagenes.png"> <b>IMAGENES</b></h3>
    
    <!-- /.box-tools -->
  </div>
  <!-- /.box-header -->
  <div class="box-body" >
  	<div class="col-12">
	  	<div class="row parent" >
	    	<div class="col-8" style="padding-right: 2px;">
	    		<div class="box" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
				  <div class="box-header with-border" style="background-color: #004AC1; color: white; text-align: center; font-family: 'Helvetica general3';border-bottom: #004ac1;">
				    <span><b>Guardado de Im&aacute;genes y Video</b><span>
				    <!-- /.box-tools -->
				  </div>
				  <div class="box-body" style="background: white;">
				  	<div class="row">
				  		<div class="col-md-12" style="padding-right: 2px;">
				  			<input id="id_hc_procedimientos" type="hidden" name="id_hc_procedimientos" value="{{ $id }}">   
		                    <div class="row">
		                        <div class="col-8" >
		                            <video id="video" style="width: 100%" autoplay="true"/>
		                        </div>
		                        <div class="col-4">
		                        	<div class="col-md-12">
		                        		<span class="btn btn-success texto" id="color_grabacion" style="width: 100%;">En Espera</span>
		                        	</div>
		                        	<div class="col-sm-12"> &nbsp;</div>
			                        <div class="col-md-12">
			                            <button id="tomar" class="btn btn-primary" style="width: 100%;">Tomar Foto</button>
			                        </div>
			                        <div class="col-md-12">
			                            <section class="experiment recordrtc">
			                                <h2 class="header">
			                                    <input type="hidden" class="recording-media" value="record-video" />
			                                    <input type="hidden" class="media-container-format" value="Mp4" />
			                                    <button id="grabacion" class="btn btn-primary">Start Recording</button>
			                                </h2>

			                                <div style="text-align: center; display: none;">
			                                    <button class="btn btn-primary" id="save-to-disk">Save To Disk</button>
			                                    <button class="btn btn-primary" id="open-new-tab">Open New Tab</button>
			                                    <button class="btn btn-primary" id="upload-to-server">Upload To Server</button>
			                                </div>

			                                <br>

			                                <video style="display: none;" controls muted></video>
			                            </section>
			                        </div>
		                        </div>
		                        <!--  inputs para el grabado de imagenes --> 
						        <input type="hidden" class="recording-media" value="record-audio-plus-video">
						        <input type="hidden" class="media-container-format" value="mp4">
						        <input type="hidden" class="media-resolutions" value="default">
						        <input type="hidden" class="media-framerates" value="default">
						        <input type="hidden" class="media-bitrates" value="default">
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
				  		</div>
				  	</div>
				  </div>
				</div>
	    	</div>
	    	<div class="col-4" style="padding-left: 2px;">
	    		<div class="box" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
				  <div class="box-header with-border" style="background-color: #004AC1; color: white; text-align: center; font-family: 'Helvetica general3';border-bottom: #004ac1;">
				    <span><b>Imagenes del Procedimiento</b><span>
				    <!-- /.box-tools -->
				  </div>
				  <div class="box-body" style="background: white;">
				  	<div class="row">
	                    <div class="col-md-12" >
	                    	<span style="font-family: 'Helvetica general3';">Imagenes Capturadas: </span>
	                    	<span class="parpadea" id="cimagenes" style="color:white; background-color: red; padding: 5px; font-weight: 800px;"><b>{{$cimagenes}}</b></span> 
	                    </div> 
	                    <div class="col-md-12" >
	                    	<span style="font-family: 'Helvetica general3';">Videos Capturados: </span>
	                    	<span class="parpadea" id="cvideo" style="color: white; background-color: red; padding: 5px; font-weight: 800px; "><b>{{$cvideo}}</b></span> 
	                    </div>
	                    <div class="col-12">
		                    <div class="row" style="padding: 0px 5px; " id="fotos_agregar">
	                            @foreach($imagenes as $imagen)
	                            
	                            <div class="col-3" style='margin: 10px 0;padding: 2px;' >
	                                @php
	                                    $explotar = explode( '.', $imagen->nombre);
	                                    $extension = end($explotar);
	                                @endphp
	                                @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') || ($extension == 'JPG') || ($extension == 'JPEG') || ($extension == 'PNG'))
	                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
	                                        <img  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" style='width:100%;'>
	                                    </a> 
	                                @elseif(($extension == 'pdf') || ($extension == 'PDF'))
	                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
	                                        <img  src="{{asset('imagenes/pdf.png')}}" style='width:100%;'>  
	                                    </a>
	                                @elseif(($extension == 'mp4'))
	                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
	                                        <img  src="{{asset('imagenes/video.png')}}" style='width:100%;'>  
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
	    	</div>
	    </div>		
  	</div>    
  </div>
  <!-- box-footer -->
</div>
<div class="container-fluid" >
    <div class="row ">

        <div class="col-md-7">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="col-md-9">
                        <h4>GUARDADO DE IMAGENES Y VIDEO </h4>
                    </div>                                                     
                </div>                           
                <div class="box-body">
                    
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
        

        
    </div>                    
</div> 
    </div>
</div>
<script>
    Dropzone.options.addimage = {
      acceptedFiles: ".jpg, .jpeg, .png, .bmp, .jpe, .jfif, .tiff, .tif", 
      maxFiles: 50,
    };
    //constants
    var MAX_WIDHT = 1280,
        MAX_HEIGHT = 720;
 
    var URL = window.URL;
 
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
                  var 
                      video = document.querySelector('video');
                      video.srcObject  = stream;      
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
        	async: false,
            type: 'post',
            url: '{{route('hc_video.guardado_foto', ['id_protocolo' => $id])}}',
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            data: { 
                imgBase64: dataURL
            },
            success: function (result) {
                anterior = $("#fotos_agregar").html();
                canterior = parseInt($('#cimagenes').text());
                canterior = canterior+1;
                $("#cimagenes").html(canterior);    
                 $("#fotos_agregar").html("<div class='col-3' style='margin: 10px 0;padding: 2px;'><a data-toggle='modal' data-target='#foto' href='{{route('hc_video.mostrar_foto2')}}/"+result.id+"'><img src='{{asset('hc_ima')}}/"+result.archivo+"' style='width:100%;'></div>"+anterior);
                console.log(result);
            }
        });
    });
    $('#foto').on('hidden.bs.modal', function(){
        $(this).removeData('bs.modal');
    });


    $(document).keypress(function(e) {          
        tecla = (document.all) ? e.keyCode : e.which;
        if (tecla==13){
            $("#tomar").click();
        }
        if (tecla == 112){
            $("#grabacion").click();
        }        
    });  
</script>
<!-- grabacion del video-->
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
</script>
<script>
    var recordingDIV = document.querySelector('.recordrtc');
    var recordingMedia = recordingDIV.querySelector('.recording-media');
    var recordingPlayer = recordingDIV.querySelector('video');
    var mediaContainerFormat = recordingDIV.querySelector('.media-container-format');

    recordingDIV.querySelector('button').onclick = function() {
        var button = this;

        if(button.innerHTML === 'Stop Recording') {
            button.disabled = true;
            button.disableStateWaiting = true;
            setTimeout(function() {
                button.disabled = false;
                button.disableStateWaiting = false;
            }, 2 * 1000);

            button.innerHTML = 'Star Recording';
            $('#color_grabacion').html('En Espera');
            $('#color_grabacion').addClass('btn-success');
            $('#color_grabacion').removeClass('btn-danger');            
            $('#color_grabacion').removeClass('parpadea');

            function stopStream() {
                if(button.stream && button.stream.stop) {
                    button.stream.stop();
                    button.stream = null;
                    
                }
            }

            if(button.recordRTC) {
                if(button.recordRTC.length) {
                    button.recordRTC[0].stopRecording(function(url) {
                        if(!button.recordRTC[1]) {
                            button.recordingEndedCallback(url);
                            stopStream();

                            saveToDiskOrOpenNewTab(button.recordRTC[0]);
                            $('#upload-to-server').click();
                            return;
                        }

                        button.recordRTC[1].stopRecording(function(url) {
                            button.recordingEndedCallback(url);
                            stopStream();
                            $('#upload-to-server').click();
                        });
                    });
                }
                else {
                    button.recordRTC.stopRecording(function(url) {
                        button.recordingEndedCallback(url);
                        stopStream();

                        saveToDiskOrOpenNewTab(button.recordRTC);
                        $('#upload-to-server').click();
                    });
                }
            }

            return;
        }

        button.disabled = true;

        var commonConfig = {
            onMediaCaptured: function(stream) {
                button.stream = stream;
                if(button.mediaCapturedCallback) {
                    button.mediaCapturedCallback();
                }

                button.innerHTML = 'Stop Recording';

                $('#color_grabacion').html('Grabando');
                $('#color_grabacion').removeClass('btn-success');
                $('#color_grabacion').addClass('btn-danger');
                $('#color_grabacion').addClass('parpadea');
                button.disabled = false;
            },
            onMediaStopped: function() {
                button.innerHTML = 'Start Recording';
                $('#color_grabacion').html('En Espera');
                $('#color_grabacion').addClass('btn-success');
                $('#color_grabacion').removeClass('btn-danger');
                $('#color_grabacion').removeClass('parpadea');
                if(!button.disableStateWaiting) {
                    button.disabled = false;
                }
            },
            onMediaCapturingFailed: function(error) {
                if(error.name === 'PermissionDeniedError' && !!navigator.mozGetUserMedia) {
                    InstallTrigger.install({
                        'Foo': {
                            // https://addons.mozilla.org/firefox/downloads/latest/655146/addon-655146-latest.xpi?src=dp-btn-primary
                            URL: 'https://addons.mozilla.org/en-US/firefox/addon/enable-screen-capturing/',
                            toString: function () {
                                return this.URL;
                            }
                        }
                    });
                }

                commonConfig.onMediaStopped();
            }
        };

        if(recordingMedia.value === 'record-video') {
            captureVideo(commonConfig);

            button.mediaCapturedCallback = function() {
                button.recordRTC = RecordRTC(button.stream, {
                    type: mediaContainerFormat.value === 'Gif' ? 'gif' : 'video',
                    disableLogs: params.disableLogs || false,
                    canvas: {
                        width: params.canvas_width || 1280,
                        height: params.canvas_height || 720
                    },
                    frameInterval: typeof params.frameInterval !== 'undefined' ? parseInt(params.frameInterval) : 20 // minimum time between pushing frames to Whammy (in milliseconds)
                });

                button.recordingEndedCallback = function(url) {
                    recordingPlayer.src = null;
                    recordingPlayer.srcObject = null;

                    if(mediaContainerFormat.value === 'Gif') {
                        recordingPlayer.pause();
                        recordingPlayer.poster = url;

                        recordingPlayer.onended = function() {
                            recordingPlayer.pause();
                            recordingPlayer.poster = URL.createObjectURL(button.recordRTC.blob);
                        };
                        return;
                    }

                    recordingPlayer.src = url;
                    recordingPlayer.play();

                    recordingPlayer.onended = function() {
                        recordingPlayer.pause();
                        recordingPlayer.src = URL.createObjectURL(button.recordRTC.blob);
                    };
                };

                button.recordRTC.startRecording();
            };
        }

        if(recordingMedia.value === 'record-audio') {
            captureAudio(commonConfig);

            button.mediaCapturedCallback = function() {
                button.recordRTC = RecordRTC(button.stream, {
                    type: 'audio',
                    bufferSize: typeof params.bufferSize == 'undefined' ? 0 : parseInt(params.bufferSize),
                    sampleRate: typeof params.sampleRate == 'undefined' ? 44100 : parseInt(params.sampleRate),
                    leftChannel: params.leftChannel || false,
                    disableLogs: params.disableLogs || false,
                    recorderType: webrtcDetectedBrowser === 'edge' ? StereoAudioRecorder : null
                });

                button.recordingEndedCallback = function(url) {
                    var audio = new Audio();
                    audio.src = url;
                    audio.controls = true;
                    recordingPlayer.parentNode.appendChild(document.createElement('hr'));
                    recordingPlayer.parentNode.appendChild(audio);

                    if(audio.paused) audio.play();

                    audio.onended = function() {
                        audio.pause();
                        audio.src = URL.createObjectURL(button.recordRTC.blob);
                    };
                };

                button.recordRTC.startRecording();
            };
        }

        if(recordingMedia.value === 'record-audio-plus-video') {
            captureAudioPlusVideo(commonConfig);

            button.mediaCapturedCallback = function() {

                if(webrtcDetectedBrowser !== 'firefox') { // opera or chrome etc.
                    button.recordRTC = [];

                    if(!params.bufferSize) {
                        // it fixes audio issues whilst recording 720p
                        params.bufferSize = 16384;
                    }

                    var audioRecorder = RecordRTC(button.stream, {
                        type: 'audio',
                        bufferSize: typeof params.bufferSize == 'undefined' ? 0 : parseInt(params.bufferSize),
                        sampleRate: typeof params.sampleRate == 'undefined' ? 44100 : parseInt(params.sampleRate),
                        leftChannel: params.leftChannel || false,
                        disableLogs: params.disableLogs || false,
                        recorderType: webrtcDetectedBrowser === 'edge' ? StereoAudioRecorder : null
                    });

                    var videoRecorder = RecordRTC(button.stream, {
                        type: 'video',
                        disableLogs: params.disableLogs || false,
                        canvas: {
                            width: params.canvas_width || 1280,
                            height: params.canvas_height || 720
                        },
                        frameInterval: typeof params.frameInterval !== 'undefined' ? parseInt(params.frameInterval) : 20 // minimum time between pushing frames to Whammy (in milliseconds)
                    });

                    // to sync audio/video playbacks in browser!
                    videoRecorder.initRecorder(function() {
                        audioRecorder.initRecorder(function() {
                            audioRecorder.startRecording();
                            videoRecorder.startRecording();
                        });
                    });

                    button.recordRTC.push(audioRecorder, videoRecorder);

                    button.recordingEndedCallback = function() {
                        var audio = new Audio();
                        audio.src = audioRecorder.toURL();
                        audio.controls = true;
                        audio.autoplay = true;

                        audio.onloadedmetadata = function() {
                            recordingPlayer.src = videoRecorder.toURL();
                            recordingPlayer.play();
                        };

                        recordingPlayer.parentNode.appendChild(document.createElement('hr'));
                        recordingPlayer.parentNode.appendChild(audio);

                        if(audio.paused) audio.play();
                    };
                    return;
                }

                button.recordRTC = RecordRTC(button.stream, {
                    type: 'video',
                    disableLogs: params.disableLogs || false,
                    // we can't pass bitrates or framerates here
                    // Firefox MediaRecorder API lakes these features
                });

                button.recordingEndedCallback = function(url) {
                    recordingPlayer.srcObject = null;
                    recordingPlayer.muted = false;
                    recordingPlayer.src = url;
                    recordingPlayer.play();

                    recordingPlayer.onended = function() {
                        recordingPlayer.pause();
                        recordingPlayer.src = URL.createObjectURL(button.recordRTC.blob);
                    };
                };

                button.recordRTC.startRecording();
            };
        }

        if(recordingMedia.value === 'record-screen') {
            captureScreen(commonConfig);

            button.mediaCapturedCallback = function() {
                button.recordRTC = RecordRTC(button.stream, {
                    type: mediaContainerFormat.value === 'Gif' ? 'gif' : 'video',
                    disableLogs: params.disableLogs || false,
                    canvas: {
                        width: params.canvas_width || 1280,
                        height: params.canvas_height || 720
                    }
                });

                button.recordingEndedCallback = function(url) {
                    recordingPlayer.src = null;
                    recordingPlayer.srcObject = null;

                    if(mediaContainerFormat.value === 'Gif') {
                        recordingPlayer.pause();
                        recordingPlayer.poster = url;
                        recordingPlayer.onended = function() {
                            recordingPlayer.pause();
                            recordingPlayer.poster = URL.createObjectURL(button.recordRTC.blob);
                        };
                        return;
                    }

                    recordingPlayer.src = url;
                    recordingPlayer.play();
                };

                button.recordRTC.startRecording();
            };
        }

        if(recordingMedia.value === 'record-audio-plus-screen') {
            captureAudioPlusScreen(commonConfig);

            button.mediaCapturedCallback = function() {
                button.recordRTC = RecordRTC(button.stream, {
                    type: 'video',
                    disableLogs: params.disableLogs || false,
                    // we can't pass bitrates or framerates here
                    // Firefox MediaRecorder API lakes these features
                });

                button.recordingEndedCallback = function(url) {
                    recordingPlayer.srcObject = null;
                    recordingPlayer.muted = false;
                    recordingPlayer.src = url;
                    recordingPlayer.play();

                    recordingPlayer.onended = function() {
                        recordingPlayer.pause();
                        recordingPlayer.src = URL.createObjectURL(button.recordRTC.blob);
                    };
                };

                button.recordRTC.startRecording();
            };
        }
    };

    function captureVideo(config) {
        captureUserMedia({video: true}, function(videoStream) {
            recordingPlayer.srcObject = videoStream;
            recordingPlayer.play();

            config.onMediaCaptured(videoStream);

            videoStream.onended = function() {
                config.onMediaStopped();
            };
        }, function(error) {
            config.onMediaCapturingFailed(error);
        });
    }

    function captureAudio(config) {
        captureUserMedia({audio: true}, function(audioStream) {
            recordingPlayer.srcObject = audioStream;
            recordingPlayer.play();

            config.onMediaCaptured(audioStream);

            audioStream.onended = function() {
                config.onMediaStopped();
            };
        }, function(error) {
            config.onMediaCapturingFailed(error);
        });
    }

    function captureAudioPlusVideo(config) {
        captureUserMedia({video: true, audio: true}, function(audioVideoStream) {
            recordingPlayer.srcObject = audioVideoStream;
            recordingPlayer.play();

            config.onMediaCaptured(audioVideoStream);

            audioVideoStream.onended = function() {
                config.onMediaStopped();
            };
        }, function(error) {
            config.onMediaCapturingFailed(error);
        });
    }

    function captureScreen(config) {
        getScreenId(function(error, sourceId, screenConstraints) {
            if (error === 'not-installed') {
                document.write('<h1><a target="_blank" href="https://chrome.google.com/webstore/detail/screen-capturing/ajhifddimkapgcifgcodmmfdlknahffk">Please install this chrome extension then reload the page.</a></h1>');
            }

            if (error === 'permission-denied') {
                alert('Screen capturing permission is denied.');
            }

            if (error === 'installed-disabled') {
                alert('Please enable chrome screen capturing extension.');
            }

            if(error) {
                config.onMediaCapturingFailed(error);
                return;
            }

            captureUserMedia(screenConstraints, function(screenStream) {
                recordingPlayer.srcObject = screenStream;
                recordingPlayer.play();

                config.onMediaCaptured(screenStream);

                screenStream.onended = function() {
                    config.onMediaStopped();
                };
            }, function(error) {
                config.onMediaCapturingFailed(error);
            });
        });
    }

    function captureAudioPlusScreen(config) {
        getScreenId(function(error, sourceId, screenConstraints) {
            if (error === 'not-installed') {
                document.write('<h1><a target="_blank" href="https://chrome.google.com/webstore/detail/screen-capturing/ajhifddimkapgcifgcodmmfdlknahffk">Please install this chrome extension then reload the page.</a></h1>');
            }

            if (error === 'permission-denied') {
                alert('Screen capturing permission is denied.');
            }

            if (error === 'installed-disabled') {
                alert('Please enable chrome screen capturing extension.');
            }

            if(error) {
                config.onMediaCapturingFailed(error);
                return;
            }

            screenConstraints.audio = true;

            captureUserMedia(screenConstraints, function(screenStream) {
                recordingPlayer.srcObject = screenStream;
                recordingPlayer.play();

                config.onMediaCaptured(screenStream);

                screenStream.onended = function() {
                    config.onMediaStopped();
                };
            }, function(error) {
                config.onMediaCapturingFailed(error);
            });
        });
    }

    function captureUserMedia(mediaConstraints, successCallback, errorCallback) {
        navigator.mediaDevices.getUserMedia(mediaConstraints).then(successCallback).catch(errorCallback);
    }

    function setMediaContainerFormat(arrayOfOptionsSupported) {
        var options = Array.prototype.slice.call(
            mediaContainerFormat.querySelectorAll('option')
        );

        var selectedItem;
        options.forEach(function(option) {
            option.disabled = true;

            if(arrayOfOptionsSupported.indexOf(option.value) !== -1) {
                option.disabled = false;

                if(!selectedItem) {
                    option.selected = true;
                    selectedItem = option;
                }
            }
        });
    }

    recordingMedia.onchange = function() {
        if(this.value === 'record-audio') {
            setMediaContainerFormat(['WAV', 'Ogg']);
            return;
        }
        setMediaContainerFormat(['WebM', 'Mp4','Gif']);
    };

    if(webrtcDetectedBrowser === 'edge') {
        // webp isn't supported in Microsoft Edge
        // neither MediaRecorder API
        // so lets disable both video/screen recording options

        console.warn('Neither MediaRecorder API nor webp is supported in Microsoft Edge. You cam merely record audio.');

        recordingMedia.innerHTML = '<option value="record-audio">Audio</option>';
        setMediaContainerFormat(['WAV']);
    }

    if(webrtcDetectedBrowser === 'firefox') {
        // Firefox implemented both MediaRecorder API as well as WebAudio API
        // Their MediaRecorder implementation supports both audio/video recording in single container format
        // Remember, we can't currently pass bit-rates or frame-rates values over MediaRecorder API (their implementation lakes these features)

        recordingMedia.innerHTML = '<option value="record-audio-plus-video">Audio+Video</option>'
                                    + '<option value="record-audio-plus-screen">Audio+Screen</option>'
                                    + recordingMedia.innerHTML;
    }

    // disabling this option because currently this demo
    // doesn't supports publishing two blobs.
    // todo: add support of uploading both WAV/WebM to server.
    if(false && webrtcDetectedBrowser === 'chrome') {
        recordingMedia.innerHTML = '<option value="record-audio-plus-video">Audio+Video</option>'
                                    + recordingMedia.innerHTML;
        console.info('This RecordRTC demo merely tries to playback recorded audio/video sync inside the browser. It still generates two separate files (WAV/WebM).');
    }

    function saveToDiskOrOpenNewTab(recordRTC) {
        recordingDIV.querySelector('#save-to-disk').parentNode.style.display = 'block';
        recordingDIV.querySelector('#save-to-disk').onclick = function() {
            if(!recordRTC) return alert('No recording found.');

            recordRTC.save();
        };

        recordingDIV.querySelector('#open-new-tab').onclick = function() {
            if(!recordRTC) return alert('No recording found.');

            window.open(recordRTC.toURL());
        };

        recordingDIV.querySelector('#upload-to-server').disabled = false;
        recordingDIV.querySelector('#upload-to-server').onclick = function() {
            if(!recordRTC) return alert('No recording found.');
            this.disabled = true;

            var button = this;
            uploadToServer(recordRTC, function(progress, fileURL) {
                if(progress === 'ended') {
                    button.disabled = false;
                    button.innerHTML = 'Descargar del servidor';
                    button.onclick = function() {
                        window.open(fileURL);
                    };
                    return;
                }
                button.innerHTML = progress;
            });
      
        };
    }

    var listOfFilesUploaded = [];

    function uploadToServer(recordRTC, callback) {
        var blob = recordRTC instanceof Blob ? recordRTC : recordRTC.blob;
        var fileType = blob.type.split('/')[0] || 'audio';
        var fileName = (Math.random() * 1000).toString().replace('.', '');

        if (fileType === 'audio') {
            fileName += '.' + (!!navigator.mozGetUserMedia ? 'ogg' : 'wav');
        } else {
            fileName += '.mp4';
        }

        // create FormData
        var formData = new FormData();
        formData.append('id_usuario', '{{Auth::user()->id}}');
        formData.append('id_hc_protocolo', '{{$id}}');
        formData.append('video-filename', fileName);
        formData.append('video-blob', blob);

        callback('Uploading ' + fileType + ' recording to server.');

        makeXMLHttpRequest('{{asset("/")}}save.php', formData, function(progress) {
            if (progress !== 'upload-ended') {
                callback(progress);
                return;
            }

            var initialURL = '{{asset("/")}}uploads/';

            callback('ended', initialURL + fileName);
            // to make sure we can delete as soon as visitor leaves
            listOfFilesUploaded.push(initialURL + fileName);
        });
    }

    function makeXMLHttpRequest(url, data, callback) {
        var request = new XMLHttpRequest();
        request.onreadystatechange = function() {
            if (request.readyState == 4 && request.status == 200) {
                callback('upload-ended');
                anterior = $("#fotos_agregar").html();
                canterior = parseInt($('#cvideo').text());
                canterior = canterior+1;
                $("#cvideo").html(canterior); 
                $("#fotos_agregar").html("<div class='col-md-6' style='margin: 10px 0;'><a data-toggle='modal' data-target='#foto' href='{{route('hc_video.mostrar_foto2')}}/"+request.responseText+"'><img src='{{asset("/")}}imagenes/video.png' width='90%'></div>"+anterior);
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
            callback('progress-ended');
        };

        request.upload.onerror = function(error) {
            callback('Failed to upload to server');
            console.error('XMLHttpRequest failed', error);
        };

        request.upload.onabort = function(error) {
            callback('Upload aborted.');
            console.error('XMLHttpRequest aborted', error);
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

    window.onbeforeunload = function() {
        recordingDIV.querySelector('button').disabled = false;
        recordingMedia.disabled = false;
        mediaContainerFormat.disabled = false;

        if(!listOfFilesUploaded.length) return;

        listOfFilesUploaded.forEach(function(fileURL) {
            var request = new XMLHttpRequest();
            request.onreadystatechange = function() {
                if (request.readyState == 4 && request.status == 200) {
                    if(this.responseText === ' problem deleting files.') {
                        alert('Failed to delete ' + fileURL + ' from the server.');
                        return;
                    }

                    listOfFilesUploaded = [];
                    alert('You can leave now. Your files are removed from the server.');
                }
            };
            request.open('POST', 'delete.php');

            var formData = new FormData();
            formData.append('delete-file', fileURL.split('/').pop());
            request.send(formData);
        });

        return 'Please wait few seconds before your recordings are deleted from the server.';
    };
    var vartiempo = setInterval(function(){ location.reload(); }, 7201000);
</script>