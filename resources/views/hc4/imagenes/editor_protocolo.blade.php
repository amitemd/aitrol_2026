<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th { 
    padding: 0.4% ;
}  
    .hc4-inline-image-editor {
        width: 100%; padding: 18px; border: 1px solid #18a7b1; border-radius: 18px;
        background: #f8fcfd; box-shadow: 0 10px 24px rgba(14, 93, 126, .08);
    }
    .hc4-inline-image-editor .hc4-editor-toolbar { display: flex; justify-content: flex-end; margin-bottom: 12px; }
    .hc4-inline-image-editor .hc4-editor-toolbar .btn {
        border: 1px solid #169eaa; border-radius: 8px; color: #0b6584; background: #fff;
    }
    .hc4-inline-image-editor .box {
        overflow: hidden; margin-bottom: 18px; border: 1px solid #18a7b1 !important;
        border-radius: 15px !important; background: #fff !important;
        box-shadow: 0 8px 20px rgba(14, 93, 126, .08);
    }
    .hc4-inline-image-editor .box-header {
        min-height: 56px; padding: 12px 18px !important; border-bottom: 0 !important;
        color: #fff !important;
        background: linear-gradient(100deg, #075f87 0%, #0789b4 55%, #19b5ac 100%) !important;
    }
    .hc4-inline-image-editor .box-header .box-title {
        display: flex; align-items: center; margin: 0; font-size: 20px;
    }
    .hc4-inline-image-editor .box-body { padding: 18px !important; background: #fff !important; }
    .hc4-inline-image-editor .hc4-capture-grid,
    .hc4-inline-image-editor .hc4-editor-main-row { display: flex; flex-wrap: wrap; }
    .hc4-inline-image-editor #video {
        display: block; width: 100% !important; min-height: 340px; max-height: 560px;
        border-radius: 12px; background: #0c2532; object-fit: contain;
    }
    .hc4-inline-image-editor .hc4-capture-controls {
        display: flex; flex-direction: column; justify-content: flex-start; gap: 12px;
        height: auto !important;
    }
    .hc4-inline-image-editor .hc4-capture-controls > * { flex: 0 0 auto; width: 100%; }
    .hc4-inline-image-editor .hc4-capture-help { margin: 2px 0 0; color: #60717c; line-height: 1.35; }
    .hc4-inline-image-editor .hc4-camera-error {
        display: none; margin: 0; padding: 10px; border: 1px solid #efb4ad;
        border-radius: 8px; color: #9f2d22; background: #fff3f1; line-height: 1.35;
    }
    .hc4-inline-image-editor #color_grabacion,
    .hc4-inline-image-editor #tomar,
    .hc4-inline-image-editor #grabacion {
        display: flex; width: 100% !important; min-height: 46px; align-items: center;
        justify-content: center; margin: 0; border-radius: 9px; white-space: normal;
    }
    .hc4-inline-image-editor .recordrtc .header { margin: 0; }
    .hc4-inline-image-editor .hc4-image-counts {
        display: flex; flex-wrap: wrap; gap: 12px; margin: 0 0 14px;
    }
    .hc4-inline-image-editor .hc4-image-counts > div:nth-child(-n+2) {
        flex: 1; padding: 12px; border-radius: 9px; background: #eef8fa; text-align: center;
    }
    .hc4-inline-image-editor .hc4-image-counts > div:nth-child(3) {
        flex: 0 0 100%; width: 100%; max-width: 100%;
    }
    .hc4-inline-image-editor #fotos_agregar { max-height: 475px; overflow-y: auto; }
    .hc4-inline-image-editor .dropzone {
        min-height: 160px; border: 2px dashed #18a7b1; border-radius: 12px; background: #f8fcfd;
    }
    .hc4-images-inline-editor { padding: 15px 10px; }
    @media (max-width: 991px) {
        .hc4-inline-image-editor #video { min-height: 260px; }
    }
    @media (max-width: 767px) {
        .hc4-inline-image-editor { padding: 10px; }
        .hc4-inline-image-editor #video { min-height: 220px; }
        .hc4-inline-image-editor .hc4-capture-controls { margin-top: 12px; }
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


<script src="{{asset('hc4/js/grabador/RecordRTC.js')}}"></script>
<script src="{{asset('hc4/js/grabador/gif-recorder.js')}}"></script>
<script src="{{asset('hc4/js/grabador/getScreenId.js')}}"></script>

<!-- for Edige/FF/Chrome/Opera/etc. getUserMedia support -->
<script src="{{asset('plugins/video/gumadapter.js')}}"></script>


<div class="hc4-inline-image-editor" id="hc4_editor_imagenes_{{$id}}">
<div class="hc4-editor-toolbar">
    <button type="button" class="btn btn-default" onclick="cerrarEditorImagenes({{$id}})">
        <i class="fa fa-times"></i> Cerrar editor
    </button>
</div>

<div class="box hc4-editor-shell" style="border: 2px solid #004AC1; background-color: #56ABE3; border-radius: 3px; ">
  <div class="box-header with-border" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004ac1;">
    <h3 class="box-title"><img style="width: 34px; margin-right: 12px" src="{{asset('/')}}hc4/img/iconos/imagenes.png"> <b>Editor de imágenes del procedimiento</b></h3>
    
    <!-- /.box-tools -->
  </div>
  <!-- /.box-header -->
  <div class="box-body" >
  	<div class="col-12">
        <div class="row hc4-editor-main-row">
            <div class="col-lg-8 col-md-7 col-12 hc4-image-capture-column">
	    		<div class="box" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
				  <div class="box-header with-border" style="background-color: #004AC1; color: white; text-align: center; font-family: 'Helvetica general3';border-bottom: #004ac1;">
				    <span><b>Guardado de Im&aacute;genes y Video</b></span>
				    <!-- /.box-tools -->
				  </div>
				  <div class="box-body" style="background: white;">
				  	<div class="row">
				  		<div class="col-md-12" style="padding-right: 2px;">
				  			<input id="id_hc_procedimientos" type="hidden" name="id_hc_procedimientos" value="{{ $id }}">   
			                    <div class="row hc4-capture-grid">
			                        <div class="col-lg-9 col-md-8 col-12" >
		                            <video id="video" style="width: 100%" autoplay muted playsinline></video>
		                        </div>
			                        <div class="col-lg-3 col-md-4 col-12 hc4-capture-controls">
                                            <span class="btn btn-warning texto" id="color_grabacion">Solicitando cámara…</span>
			                            <button type="button" id="tomar" class="btn btn-primary" disabled>
                                                <i class="fa fa-camera"></i> Capturar foto
                                            </button>
			                            <section class="experiment recordrtc">
			                                <input type="hidden" class="recording-media" value="record-video" />
			                                <input type="hidden" class="media-container-format" value="Mp4" />
			                                <button type="button" id="grabacion" class="btn btn-primary" data-recording="0" disabled>
                                                <i class="fa fa-video-camera"></i> Iniciar grabación
                                            </button>

			                                <div style="text-align: center; display: none;">
			                                    <button class="btn btn-primary" id="save-to-disk">Save To Disk</button>
			                                    <button class="btn btn-primary" id="open-new-tab">Open New Tab</button>
			                                    <button class="btn btn-primary" id="upload-to-server">Upload To Server</button>
			                                </div>

			                                <br>

			                                <video style="display: none;" controls muted></video>
			                            </section>
			                            <button type="button" id="reintentar_camara" class="btn btn-default" style="display: none;">
                                                <i class="fa fa-refresh"></i> Reintentar cámara
                                            </button>
			                            <p id="detalle_error_camara" class="hc4-camera-error"></p>
			                            <p class="hc4-capture-help">Autoriza la cámara en el navegador. La foto se guarda al capturar; el video se guarda al detener la grabación.</p>
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
            <div class="col-lg-4 col-md-5 col-12 hc4-image-gallery-column">
	    		<div class="box" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
				  <div class="box-header with-border" style="background-color: #004AC1; color: white; text-align: center; font-family: 'Helvetica general3';border-bottom: #004ac1;">
				    <span><b>Imágenes del Procedimiento</b></span>
				    <!-- /.box-tools -->
				  </div>
				  <div class="box-body" style="background: white;">
                        <div class="row hc4-image-counts">
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
	                            
	                            <div class="col-3 hc4-procedure-image-item" data-hc4-image-id="{{$imagen->id}}" style='margin: 10px 0;padding: 2px;' >
	                                @php
	                                    $explotar = explode( '.', $imagen->nombre);
	                                    $extension = end($explotar);
	                                @endphp
	                                @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') || ($extension == 'JPG') || ($extension == 'JPEG') || ($extension == 'PNG'))
                                    <a href="#" class="hc4-editor-file-link" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
	                                        <img  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" style='width:100%;'>
	                                    </a> 
	                                @elseif(($extension == 'pdf') || ($extension == 'PDF'))
                                    <a href="#" class="hc4-editor-file-link" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
	                                        <img  src="{{asset('imagenes/pdf.png')}}" style='width:100%;'>  
	                                    </a>
	                                @elseif(($extension == 'mp4'))
                                    <a href="#" class="hc4-editor-file-link" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
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
                                    <a href="#" class="hc4-editor-file-link" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
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
<div class="container-fluid hc4-upload-container" >
    <div class="row ">

        <div class="col-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="col-md-9">
                        <h4>SUBIR IMÁGENES DESDE ARCHIVO</h4>
                    </div>                                                     
                </div>                           
                <div class="box-body">
                    
                    <div class="row">
                        <div class="form-group col-xs-12">
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
<div class="modal fade hc4-editor-file-modal" id="hc4_editor_archivo_modal_{{$id}}" tabindex="-1" role="dialog" aria-label="Vista previa del archivo">
    <div class="modal-dialog modal-lg" role="document" style="width: 80%; max-width: 1100px;">
        <div class="modal-content"></div>
    </div>
</div>
<script>
    var hc4EditorImagenes = document.getElementById('hc4_editor_imagenes_{{$id}}');
    var $hc4EditorImagenes = $(hc4EditorImagenes);
    var $hc4EditorArchivoModal = $('#hc4_editor_archivo_modal_{{$id}}');

    // La vista principal ya contiene modales antiguas con ids repetidos. Esta
    // modal exclusiva se mueve al body para que no quede detrás del backdrop.
    $hc4EditorArchivoModal.appendTo(document.body);
    $hc4EditorImagenes.off('click.hc4EditorArchivo', '.hc4-editor-file-link')
        .on('click.hc4EditorArchivo', '.hc4-editor-file-link', function(event){
            event.preventDefault();
            var urlArchivo = $(this).data('remote');
            var $contenidoModal = $hc4EditorArchivoModal.find('.modal-content');

            $contenidoModal.html('<div class="text-center" style="padding:40px"><i class="fa fa-spinner fa-spin"></i> Cargando archivo...</div>');
            $hc4EditorArchivoModal.modal('show');
            $contenidoModal.load(urlArchivo, function(response, estado){
                if(estado === 'error'){
                    $contenidoModal.html('<div class="alert alert-danger" style="margin:20px">No se pudo abrir el archivo.</div>');
                }
            });
        });

    $hc4EditorArchivoModal.off('hidden.bs.modal.hc4EditorArchivo')
        .on('hidden.bs.modal.hc4EditorArchivo', function(){
            $(this).find('.modal-content').empty();
        });

    if(typeof Dropzone !== 'undefined'){
      Dropzone.autoDiscover = false;
      var formularioImagenes = hc4EditorImagenes.querySelector('#addimage');
      if(formularioImagenes && !formularioImagenes.dropzone){
        var cargadorImagenes = new Dropzone(formularioImagenes, {
          url: "{{route('hc_video.guardado_foto2')}}",
          acceptedFiles: ".jpg,.jpeg,.png,.bmp,.jpe,.jfif,.tiff,.tif,.dcm",
          maxFiles: 50,
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });

        cargadorImagenes.on('queuecomplete', function(){
          editar_imagenes({{$id}});
        });
      }
    }
    //constants
    var MAX_WIDHT = 1280,
        MAX_HEIGHT = 720;
 
    var URL = window.URL;
 
    var inputFile = hc4EditorImagenes.querySelector('#pic');

    var video_foto = hc4EditorImagenes.querySelector('#video');
    var hc4EditorCameraStream = null;
    var hc4EditorOwnsCameraStream = false;
    var canvas = hc4EditorImagenes.querySelector('#canvas');
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
     
    function camaraImagenesLista(stream, ownsStream) {
      hc4EditorCameraStream = stream;
      hc4EditorOwnsCameraStream = ownsStream !== false;
      video_foto.srcObject = stream;
      video_foto.play();
      $hc4EditorImagenes.find('#tomar, #grabacion').prop('disabled', false);
      $hc4EditorImagenes.find('#reintentar_camara, #detalle_error_camara').hide();
      $hc4EditorImagenes.find('#color_grabacion').removeClass('btn-warning btn-danger').addClass('btn-success').html('Cámara lista');
      stream.getVideoTracks().forEach(function(track){
        track.addEventListener('ended', function(){
          errorCamaraImagenes({name: 'NotReadableError', message: 'La cámara dejó de enviar video.'});
        });
      });
    }

    function errorCamaraImagenes(e) {
      console.log('No se puede utilizar la cámara', e);
      var nombre = e && e.name ? e.name : 'CameraError';
      var mensajes = {
        NotAllowedError: 'Chrome tiene permiso para este sitio, pero macOS puede estar bloqueándolo. Activa Google Chrome en Configuración del Sistema > Privacidad y seguridad > Cámara, cierra Chrome por completo y vuelve a abrirlo.',
        SecurityError: 'El navegador o el sistema bloqueó el uso de la cámara.',
        NotFoundError: 'No se detectó ninguna cámara. Conecta o enciende el dispositivo de captura y vuelve a intentarlo.',
        DevicesNotFoundError: 'No se detectó ninguna cámara. Conecta o enciende el dispositivo de captura y vuelve a intentarlo.',
        NotReadableError: 'La cámara está ocupada por otra aplicación o el dispositivo dejó de responder. Cierra Zoom, Meet, FaceTime u otra pestaña que la esté usando.',
        TrackStartError: 'La cámara está ocupada por otra aplicación o el dispositivo dejó de responder.',
        OverconstrainedError: 'La cámara detectada no admite la configuración de video solicitada.',
        AbortError: 'La cámara no pudo iniciarse. Desconéctala, vuelve a conectarla y reintenta.'
      };
      var detalle = mensajes[nombre] || 'No fue posible iniciar la cámara. Revisa el dispositivo y vuelve a intentarlo.';
      $hc4EditorImagenes.find('#tomar, #grabacion').prop('disabled', true);
      $hc4EditorImagenes.find('#color_grabacion').removeClass('btn-warning btn-success').addClass('btn-danger').html('Sin acceso a cámara');
      $hc4EditorImagenes.find('#detalle_error_camara').text(detalle + ' (' + nombre + ')').show();
      $hc4EditorImagenes.find('#reintentar_camara').show();
    }

    function iniciarCamaraImagenesProcedimiento() {
      if(!video_foto){ return; }

      $hc4EditorImagenes.find('#tomar, #grabacion').prop('disabled', true);
      $hc4EditorImagenes.find('#reintentar_camara, #detalle_error_camara').hide();
      $hc4EditorImagenes.find('#color_grabacion').removeClass('btn-success btn-danger').addClass('btn-warning').html('Solicitando cámara…');

      if(!window.isSecureContext){
        errorCamaraImagenes({name: 'SecurityError', message: 'La página no está en un contexto HTTPS seguro.'});
        return;
      }

      if(hc4EditorCameraStream && hc4EditorOwnsCameraStream){
        hc4EditorCameraStream.getTracks().forEach(function(track){ track.stop(); });
      }
      if(hc4EditorCameraStream){
        hc4EditorCameraStream = null;
        hc4EditorOwnsCameraStream = false;
        video_foto.srcObject = null;
      }

      var streamActivo = null;
      document.querySelectorAll('video').forEach(function(videoExistente){
        if(streamActivo || videoExistente === video_foto || !videoExistente.srcObject){ return; }
        var pistasActivas = videoExistente.srcObject.getVideoTracks().filter(function(track){
          return track.readyState === 'live';
        });
        if(pistasActivas.length){ streamActivo = videoExistente.srcObject; }
      });

      if(streamActivo){
        camaraImagenesLista(streamActivo, false);
        return;
      }

      if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia){
        navigator.mediaDevices.getUserMedia({video: true, audio: false})
          .then(function(stream){ camaraImagenesLista(stream, true); })
          .catch(errorCamaraImagenes);
        return;
      }

      if(navigator.getUserMedia){
        navigator.getUserMedia(
          {video: true, audio: false},
          function(stream){ camaraImagenesLista(stream, true); },
          errorCamaraImagenes
        );
      }else{
        errorCamaraImagenes(new Error('El navegador no admite captura de cámara'));
      }
    }

    $hc4EditorImagenes.find('#reintentar_camara').off('click.hc4Camara').on('click.hc4Camara', function(){
      iniciarCamaraImagenesProcedimiento();
    });

    iniciarCamaraImagenesProcedimiento();
    //captura de video
    hc4EditorImagenes.querySelector('#tomar').addEventListener('click', function(event) {
        event.preventDefault();
        if(!video_foto || video_foto.readyState < 2){
            alert('La cámara todavía no está lista. Autoriza el acceso y vuelve a intentarlo.');
            return;
        }
        //elements
        var canvas = hc4EditorImagenes.querySelector('#canvas'),
            ctx = canvas.getContext('2d');

        context.drawImage(video_foto, 0, 0, 1280, 720   );
        //envio de datos por ajax
         
        var canvas = hc4EditorImagenes.querySelector('#canvas');
        var dataURL = canvas.toDataURL();

        //console.log("empieza guardado");
         
        
        $.ajax({ 
            async: true,
            type: 'post',
            url: '{{route('hc_video.guardado_foto', ['id_protocolo' => $id])}}',
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            data: { 
                imgBase64: dataURL
            },
            success: function (result) {
                anterior = $hc4EditorImagenes.find('#fotos_agregar').html();
                canterior = parseInt($hc4EditorImagenes.find('#cimagenes').text());
                canterior = canterior+1;
                $hc4EditorImagenes.find('#cimagenes').html(canterior);
                $hc4EditorImagenes.find('#fotos_agregar').html("<div class='col-3' style='margin: 10px 0;padding: 2px;'><a href='#' class='hc4-editor-file-link' data-remote='{{route('hc_video.mostrar_foto2')}}/"+result.id+"'><img src='{{asset('hc_ima')}}/"+result.archivo+"' style='width:100%;'></a></div>"+anterior);
                $hc4EditorImagenes.find('#color_grabacion').html('Foto guardada');
                setTimeout(function(){
                    if($hc4EditorImagenes.find('#grabacion').attr('data-recording') !== '1'){
                        $hc4EditorImagenes.find('#color_grabacion').html('Cámara lista');
                    }
                }, 1500);
            },
            error: function () {
                alert('No se pudo guardar la fotografía. Inténtalo nuevamente.');
            }
        });
    });


    $(document).off('keypress.hc4Imagenes').on('keypress.hc4Imagenes', function(e) {
        tecla = (document.all) ? e.keyCode : e.which;
        if (tecla==13){
            $hc4EditorImagenes.find('#tomar').click();
        }
        if (tecla == 112){
            $hc4EditorImagenes.find('#grabacion').click();
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
    var recordingDIV = hc4EditorImagenes.querySelector('.recordrtc');
    var recordingMedia = recordingDIV.querySelector('.recording-media');
    var recordingPlayer = recordingDIV.querySelector('video');
    var mediaContainerFormat = recordingDIV.querySelector('.media-container-format');

    recordingDIV.querySelector('button').onclick = function() {
        var button = this;

        if(button.getAttribute('data-recording') === '1') {
            button.disabled = true;
            button.disableStateWaiting = true;
            setTimeout(function() {
                button.disabled = false;
                button.disableStateWaiting = false;
            }, 2 * 1000);

            button.setAttribute('data-recording', '0');
            button.innerHTML = '<i class="fa fa-video-camera"></i> Iniciar grabación';
            $hc4EditorImagenes.find('#color_grabacion').html('En espera');
            $hc4EditorImagenes.find('#color_grabacion').addClass('btn-success');
            $hc4EditorImagenes.find('#color_grabacion').removeClass('btn-danger');
            $hc4EditorImagenes.find('#color_grabacion').removeClass('parpadea');

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
                            return;
                        }

                        button.recordRTC[1].stopRecording(function(url) {
                            button.recordingEndedCallback(url);
                            stopStream();
                            // En Chrome, audio y video pueden grabarse por separado.
                            // El segundo grabador es el que contiene el video.
                            saveToDiskOrOpenNewTab(button.recordRTC[1]);
                        });
                    });
                }
                else {
                    button.recordRTC.stopRecording(function(url) {
                        button.recordingEndedCallback(url);
                        stopStream();

                        saveToDiskOrOpenNewTab(button.recordRTC);
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

                button.setAttribute('data-recording', '1');
                button.innerHTML = '<i class="fa fa-stop-circle"></i> Detener y guardar';

                $hc4EditorImagenes.find('#color_grabacion').html('Grabando');
                $hc4EditorImagenes.find('#color_grabacion').removeClass('btn-success');
                $hc4EditorImagenes.find('#color_grabacion').addClass('btn-danger');
                $hc4EditorImagenes.find('#color_grabacion').addClass('parpadea');
                button.disabled = false;
            },
            onMediaStopped: function() {
                button.setAttribute('data-recording', '0');
                button.innerHTML = '<i class="fa fa-video-camera"></i> Iniciar grabación';
                $hc4EditorImagenes.find('#color_grabacion').html('En espera');
                $hc4EditorImagenes.find('#color_grabacion').addClass('btn-success');
                $hc4EditorImagenes.find('#color_grabacion').removeClass('btn-danger');
                $hc4EditorImagenes.find('#color_grabacion').removeClass('parpadea');
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
        if(hc4EditorCameraStream && hc4EditorCameraStream.getVideoTracks().some(function(track){ return track.readyState === 'live'; })) {
            recordingPlayer.srcObject = hc4EditorCameraStream;
            recordingPlayer.play();
            config.onMediaCaptured(hc4EditorCameraStream);
            return;
        }

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
        // RecordRTC necesita estos controles para el flujo interno, pero no deben
        // formar parte de la interfaz clínica.
        recordingDIV.querySelector('#save-to-disk').parentNode.style.display = 'none';
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
            uploadToServer(recordRTC, function(progress, result) {
                if(progress === 'ended') {
                    button.innerHTML = 'Video guardado';
                    $hc4EditorImagenes.find('#color_grabacion')
                        .removeClass('btn-warning btn-danger parpadea')
                        .addClass('btn-success')
                        .html('Video guardado');

                    var cantidadVideos = parseInt($hc4EditorImagenes.find('#cvideo').text(), 10) || 0;
                    $hc4EditorImagenes.find('#cvideo').html(cantidadVideos + 1);
                    $hc4EditorImagenes.find('#fotos_agregar').prepend(
                        "<div class='col-3' style='margin: 10px 0;padding: 2px;'>" +
                            "<a href='#' class='hc4-editor-file-link' data-remote='{{route('hc_video.mostrar_foto2')}}/" + result.id + "'>" +
                                "<img src='{{asset('imagenes/video.png')}}' style='width:100%;'>" +
                            "</a>" +
                        "</div>"
                    );

                    setTimeout(function() {
                        $hc4EditorImagenes.find('#color_grabacion').html('Cámara lista');
                    }, 1800);
                    return;
                }

                if(progress === 'error') {
                    button.disabled = false;
                    button.innerHTML = 'Reintentar guardado';
                    $hc4EditorImagenes.find('#color_grabacion')
                        .removeClass('btn-warning btn-success parpadea')
                        .addClass('btn-danger')
                        .html('Error al guardar video');
                    alert(result || 'No se pudo guardar el video.');
                    return;
                }

                if(progress === 'progress') {
                    button.innerHTML = 'Guardando ' + result + '%';
                    $hc4EditorImagenes.find('#color_grabacion')
                        .removeClass('btn-success btn-danger')
                        .addClass('btn-warning')
                        .html('Guardando video ' + result + '%');
                    return;
                }

                button.innerHTML = 'Guardando video';
                $hc4EditorImagenes.find('#color_grabacion')
                    .removeClass('btn-success btn-danger')
                    .addClass('btn-warning')
                    .html('Guardando video…');
            });

        };

        // El guardado clínico es automático al detener la grabación. No depende
        // de que el navegador permita hacer clic en un control oculto.
        recordingDIV.querySelector('#upload-to-server').onclick();
    }

    function uploadToServer(recordRTC, callback) {
        var blob = recordRTC instanceof Blob ? recordRTC : recordRTC.blob;
        var fileName = 'procedimiento_{{$id}}_' + Date.now() + '.mp4';

        var formData = new FormData();
        formData.append('video', blob, fileName);

        callback('starting');
        makeXMLHttpRequest("{{ route('hc_video.guardado_video_protocolo', ['id_protocolo' => $id]) }}", formData, callback);
    }

    function makeXMLHttpRequest(url, data, callback) {
        var request = new XMLHttpRequest();
        request.onload = function() {
            var result = {};
            try {
                result = JSON.parse(request.responseText || '{}');
            } catch (error) {
                result = {};
            }

            if (request.status >= 200 && request.status < 300 && result.id) {
                callback('ended', result);
                return;
            }

            callback('error', result.message || 'El servidor no pudo guardar el video.');
        };

        request.upload.onloadstart = function() {
            callback('starting');
        };

        request.upload.onprogress = function(event) {
            if (event.lengthComputable) {
                callback('progress', Math.round(event.loaded / event.total * 100));
            }
        };

        request.onerror = function() {
            callback('error', 'No se pudo conectar con el servidor para guardar el video.');
        };

        request.onabort = function() {
            callback('error', 'El guardado del video fue cancelado.');
        };

        request.open('POST', url);
        request.setRequestHeader('Accept', 'application/json');
        var metas = document.getElementsByTagName('meta');
        for (var i=0; i<metas.length; i++) {
            if (metas[i].getAttribute("name") == "csrf-token") {
                request.setRequestHeader("X-CSRF-Token", metas[i].getAttribute("content"));
            }
        }
        request.send(data);
    }

    window.detenerEditorImagenesProcedimiento = function() {
        $(document).off('keypress.hc4Imagenes');
        $hc4EditorImagenes.off('click.hc4EditorArchivo', '.hc4-editor-file-link');
        if($hc4EditorArchivoModal.length){
            $hc4EditorArchivoModal.modal('hide');
            $hc4EditorArchivoModal.remove();
        }
        recordingDIV.querySelector('button').disabled = false;
        recordingMedia.disabled = false;
        mediaContainerFormat.disabled = false;

        if(hc4EditorCameraStream && hc4EditorOwnsCameraStream){
            hc4EditorCameraStream.getTracks().forEach(function(track){ track.stop(); });
        }
        hc4EditorCameraStream = null;
        hc4EditorOwnsCameraStream = false;

        return true;
    };
</script>
