@php
    $explotar = explode( '.', $imagen->nombre);
    $extension = end($explotar);
@endphp
@if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') || ($extension == 'JPG') || ($extension == 'JPEG') || ($extension == 'PNG') || ($extension == 'bmp') || ($extension == 'BMP') )
   
    <style type="text/css">
        .oculto{
            display: none;
        }
        .btn-verde{
            background-color:#00a65a;
            border-color:#008d4c;
            color: #fff;
        }
    </style>
     <link href="https://vjs.zencdn.net/7.11.4/video-js.css" rel="stylesheet" />
    <div class="row" id="imagen_solita">
        <div class="col-12">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
            <!--img id="imafoto" src="{{asset('')}}/../storage/app/hc_ima/{{$imagen->nombre}}" alt="Imagen Ingresada" style="max-width: 900px;"-->
            <img id="imafoto" src="{{asset('public/'.$imagen->nombre)}}" alt="Imagen Ingresada" style="max-width: 900px;">
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10" style="margin:20px 0;">
                    <div class="row">
                        <input type="hidden" id="verificador" value="0">
                        <div class="col-md-3">
                            <a class="btn btn-secondary" id="ltotal" onclick="verificar()" style="color: white"><i class="fa fa-times" id="xlupa" aria-hidden="true"></i> <i class="fa fa-check oculto" id="checklupa" aria-hidden="true"></i> <i class="fa fa-search" aria-hidden="true"></i> Zoom</a>
                        </div>
                        <div class="col-md-4 oculto"  id="level" >
                            <div class="row">
                                <label class="col-md-6 control-label" >@lang('hc4.nivelZoom')</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="llevel" value="2">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 oculto"  id="espacio">
                            <div class="row">
                                <label class="col-md-6 control-label" >@lang('hc4.tamanoLupa') (px)</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="ltamano"  value="200">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-7"></div>
                <div class="col-4" style="margin:20px 0;">
                    <a href="{{asset('public/'.$imagen->nombre)}}" class="btn btn-primary" target="_blank"  style="color: white"><!-- ruta 0 desde la historia clinica -->
                        <span class="glyphicon glyphicon-download-alt"> @lang('hc4.descargar')</span>
                    </a>
                    <a class="btn btn-primary" onclick="eliminar('{{$imagen->id}}');" style="color: white">@lang('hc4.eliminarFoto') </a>
                </div>

            </div>
        </div>
    </div>

    <script src="{{asset('/js/lupa.js')}}" ></script>
    <script>
        function verificar(){
            elemento = $('#verificador').val();
            if(elemento == 0){
                destruir();

                $('#ltotal').removeClass('btn-secondary');
                $('#ltotal').addClass('btn-verde');
                $('#xlupa').addClass('oculto');
                $('#checklupa').removeClass('oculto');
                $('#level').removeClass('oculto');
                $('#espacio').removeClass('oculto');
                $('#verificador').val(1);
            }else{

                tamano = $('#ltamano').val();
                nivel = $('#llevel').val();

                $('#ltotal').addClass('btn-secondary');
                $('#ltotal').removeClass('btn-verde');
                $('#xlupa').removeClass('oculto');
                $('#checklupa').addClass('oculto');
                $('#level').addClass('oculto');
                $('#espacio').addClass('oculto');
                $('#verificador').val(0);
                $("#imafoto").mlens("update",
                {
                    lensShape: "circle",
                    lensSize: tamano,           // color of the lens border (#hex)
                    zoomLevel: parseInt(nivel),           // size of the lens (in px)
                    borderSize: 1,                  // size of the lens border (in px)
                    borderColor: "#000000",
                    borderRadius: 0,
                    overlayAdapt: true, // true if the overlay image has to adapt to the lens size (true/false)
                    responsive: true
                });
            }

        }
        function destruir(){
            $("#imafoto").mlens("update",
            {
                lensShape: "circle",
                lensSize: '0%',
                borderSize: '0',
                borderColor: "transparent",
                borderRadius: 0,
                overlayAdapt: false,                // color of the lens border (#hex)
                zoomLevel: 0,
            });
        }

        $(document).ready(function() {
            $("#imafoto").mlens(
            {
                imgSrc: $("#imafoto").attr("data-big"),   // path of the hi-res version of the image
                lensShape: "circle",                // shape of the lens (circle/square)
                lensSize: 200,                  // size of the lens (in px)
                borderSize: 1,                  // size of the lens border (in px)
                borderColor: "#000000",                // color of the lens border (#hex)
                zoomLevel: 2,
                borderRadius: 0,                // border radius (optional, only if the shape is square)
                imgOverlay: $("#imafoto").attr("data-overlay"), // path of the overlay image (optional)
                overlayAdapt: true, // true if the overlay image has to adapt to the lens size (true/false)
                responsive: true
            });
        });
    </script>

@elseif(($extension == 'mp4') )
    <div class="row">
        <div class="col-12">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
        </div>
        <div class="col-12">

            <div class="col-md-12">
                <video loop autoplay="" controls width="100%" height="500"  src="{{asset('/')}}uploads/prodmax.mp4#t=10800" >
                   <!--<source src="{{asset('/')}}uploads/endos.mp4" type="video/mp4" />-->
                </video>
            </div>

            <div class="col-md-3 col-md-offset-8" style="margin:20px 0;">
                <a class="btn btn-danger btn_ordenes" style="height: 100%; font-size: 16px; color: white;" onclick="eliminar('{{$imagen->id}}');">@lang('hc4.eliminarvideo')</a>
            </div>
            <div class="col-md-12">
                <div class="row">
                    <div class="col-2" >
                        <button type="button" id="envio_mp4" class="btn btn-primary" onclick="procesar('mp4', '{{$imagen->id}}')" data-loading-text="">
                            <span id="load_mp4" ><i class='fa fa-spinner fa-spin '></i> Loading Video... </span>
                            <span id="load2_mp4" class="glyphicon glyphicon-download-alt"> Download MP4 </span>
                        </button>
                        <a type="button" id="descarga_mp4" href="{{asset('/')}}uploads/{{$imagen->nombre}}" download="{{$imagen->nombre}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica --></a>
                    </div>


                    <div class="col-2" >
                        <button type="button" id="envio_avi" class="btn btn-primary" onclick="procesar('avi', '{{$imagen->id}}')" data-loading-text="">
                            <span id="load_avi" ><i class='fa fa-spinner fa-spin '></i> Loading Video... </span>
                            <span id="load2_avi" class="glyphicon glyphicon-download-alt"> Download AVI </span>
                        </button>
                        <a type="button" id="descarga_avi" href="{{asset('/')}}uploads/{{substr($imagen->nombre,0,-3)}}avi" download="{{substr($imagen->nombre,0,-3)}}avi" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica --></a>
                    </div>

                    <div class="col-2" >
                        <button type="button" id="envio_wmv" class="btn btn-primary" onclick="procesar('wmv', '{{$imagen->id}}')" data-loading-text="">
                            <span id="load_wmv" ><i class='fa fa-spinner fa-spin '></i> Loading Video... </span>
                            <span id="load2_wmv" class="glyphicon glyphicon-download-alt"> Download WMV </span>
                        </button>
                        <a type="button" id="descarga_wmv" href="{{asset('/')}}uploads/{{substr($imagen->nombre,0,-3)}}wmv" download="{{substr($imagen->nombre,0,-3)}}wmv" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica --></a>
                    </div>

                    <div class="col-2" >
                        <button type="button" id="envio_mov" class="btn btn-primary" onclick="procesar('mov', '{{$imagen->id}}')" data-loading-text="">
                            <span id="load_mov" ><i class='fa fa-spinner fa-spin '></i> Loading Video... </span>
                            <span id="load2_mov" class="glyphicon glyphicon-download-alt"> Download MOV </span>
                        </button>
                        <a type="button" id="descarga_mov" href="{{asset('/')}}uploads/{{substr($imagen->nombre,0,-3)}}mov" download="{{substr($imagen->nombre,0,-3)}}mov" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica --></a>
                    </div>

                    <div class="col-2" >
                        <button type="button" id="envio_mpeg" class="btn btn-primary" onclick="procesar('mpeg', '{{$imagen->id}}')" data-loading-text="">
                            <span id="load_mpeg" ><i class='fa fa-spinner fa-spin '></i> Loading Video... </span>
                            <span id="load2_mpeg" class="glyphicon glyphicon-download-alt"> Download MPEG </span>
                        </button>
                        <a type="button" id="descarga_mpeg" href="{{asset('/')}}uploads/{{substr($imagen->nombre,0,-3)}}mpeg" download="{{substr($imagen->nombre,0,-3)}}mpeg" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica --></a>
                    </div>
                </div>
            </div>
            <br><br>
            <div class="col-md-12">&nbsp;</div>
        </div>
          <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
          <script src="https://vjs.zencdn.net/7.11.4/video.min.js"></script>

        <script type="text/javascript">
            $('#load_mp4').hide();
            $('#descarga_mp4').hide();
            $('#load_avi').hide();
            $('#descarga_avi').hide();
            $('#load_mov').hide();
            $('#descarga_mov').hide();
            $('#load_wmv').hide();
            $('#descarga_wmv').hide();
            $('#load_mpeg').hide();
            $('#descarga_mpeg').hide();
            function procesar(formato, id){
                $('#load_'+formato).show();
                $('#load2_'+formato).hide();
                $.ajax({
                    type: 'GET',
                    url:"{{asset('/uploads/')}}/convertidor.php?id_imagen="+id+"&formato="+formato,
                    success: function(data){
                      if(data == 1){
                        document.getElementById("descarga_"+formato).click();
                      }
                      $('#load_'+formato).hide();
                      $('#load2_'+formato).show();
                    },
                    error: function(data){
                        console.log(data);
                    }
                });
            }
        </script>
    </div>
@elseif(($extension == 'pdf') || ($extension == 'PDF'))
    @php
        $variable = explode('/' , asset('/hc_ima/'));
        $d1 = $variable[3];
        $d2 = $variable[4];
        $d3 = $variable[5];
        $variable =  env('APP_URL').'/'.$d1.'/'.$d2.'/'.$d3.'/'.$imagen->nombre;
    @endphp 
    <div class="row" >
        <div class="col-12">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
        </div>
        <div class="col-12">
            {{-- <embed id="miIFrame" src="{{URL::to('/')}}/../storage/app/hc_ima/{{$imagen->nombre}}" width="100%" type='application/pdf'> --}}
            <embed id="miIFrame" src="{{route('video.ver_archivo_biopsia',['id_archivo' => $imagen->nombre])}}" width="100%" type='application/pdf'>
            <div class="col-3 col-md-offset-8" style="margin:20px 0;">
                <a class="btn btn-primary" onclick="eliminar('{{$imagen->id}}');">@lang('hc4.eliminarArchivo')</a>
            </div>
        </div>
    </div>
            <script type="text/javascript">

            $(document).ready(function () {
              var myWidth = 0, myHeight = 0;
              if( typeof( window.innerWidth ) == 'number' ) {
                //No-IE
                myWidth = window.innerWidth;
                myHeight = window.innerHeight;
              } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
                //IE 6+
                myWidth = document.documentElement.clientWidth;
                myHeight = document.documentElement.clientHeight;
              } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
                //IE 4 compatible
                myWidth = document.body.clientWidth;
                myHeight = document.body.clientHeight;
              }
              var nuevo_alto = myHeight*0.80;
              document.getElementById("miIFrame").height = nuevo_alto;
            });
        </script>
@else
    @php
        $variable = explode('/' , asset('/hc_ima/'));
        $d1 = $variable[3];
        $d2 = $variable[4];
        $d3 = $variable[5];
        $ruta = "http://ieced.siaam.ec/".$d1."/storage/app/hc_ima/".$imagen->nombre;
    @endphp
    <div class="container-fluid" >
        <div class="row">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
            <br>
            <hr>
            <iframe id='miIFrame'  src="http://view.officeapps.live.com/op/view.aspx?src={{$ruta}}" width="100%" style="border: none;"></iframe>
            <div class="col-3 col-md-offset-8" style="margin:20px 0;">
                <a class="btn btn-primary" onclick="eliminar('{{$imagen->id}}');">@lang('hc4.eliminarArchivo')</a>
            </div>
        </div>
    </div>
    <script type="text/javascript">

        $(document).ready(function () {
          var myWidth = 0, myHeight = 0;
          if( typeof( window.innerWidth ) == 'number' ) {
            //No-IE
            myWidth = window.innerWidth;
            myHeight = window.innerHeight;
          } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
            //IE 6+
            myWidth = document.documentElement.clientWidth;
            myHeight = document.documentElement.clientHeight;
          } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
            //IE 4 compatible
            myWidth = document.body.clientWidth;
            myHeight = document.body.clientHeight;
          }
          var nuevo_alto = myHeight*0.80;
          document.getElementById("miIFrame").height = nuevo_alto;
        });
    </script>
@endif
<script>
    $('#foto').on('hidden.bs.modal', function(){
        $(this).removeData('bs.modal');
    });
    $('#video').on('hidden.bs.modal', function(){
        $(this).removeData('bs.modal');
    });
    function eliminar(id){
        var opcion = confirm("¿Estas Seguro que deseas eliminar la foto?");
        if (opcion == true) {
            $.ajax({
                type: 'get',
                url:'{{ url("historiaclinica/video/eliminar_foto/eliminar")}}/'+id,
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
