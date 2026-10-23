@php
    $explotar = explode( '.', $imagen->nombre);
    $extension = end($explotar);
@endphp
@if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') || ($extension == 'JPG') || ($extension == 'JPEG') || ($extension == 'PNG'))
    @php
        $variable = explode('/' , asset('/hc_ima/'));
        $d1 = $variable[3];
        $d2 = $variable[4];
        $d3 = $variable[5];
        $variable =  env('APP_URL').'/'.$d1.'/'.$d2.'/'.$d3.'/'.$imagen->nombre;
    @endphp
    <div class="row" id="imagen_solita">
        <div class="col-md-12">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
            <!--img id="imafoto" src="http://186.68.76.210:86/sis_medico_prb/storage/app/hc_ima/{{$imagen->nombre}}" alt="Imagen Ingresada" style="max-width: 900px;"-->
            <img id="imafoto" src="{{asset('hc_ima/'.$imagen->nombre)}}" alt="Imagen Ingresada" style="max-width: 900px;">
            <div class="col-md-3 col-md-offset-8" style="margin:20px 0;">
                <a class="btn btn-primary" onclick="eliminar('{{$imagen->id}}');">Eliminar foto </a>
            </div>
        </div>
    </div>

    <script src="{{asset('/js/lupa.js')}}" ></script>
    <script>

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

@elseif(($extension == 'mp4'))

    <div class="row">
        <div class="col-md-12">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
        </div>
        <div class="col-md-12">
            <div class="col-md-12">
                <video controls style="width: 100%;">
                  <source src="{{asset('/')}}uploads/{{$imagen->nombre}}" type="video/mp4" >
                  Tu navegador no implementa el elemento <code>video</code>.
                </video>
            </div>
            <div class="col-md-3 col-md-offset-8" style="margin:20px 0;">
                <a class="btn btn-primary" onclick="eliminar('{{$imagen->id}}');">Eliminar Archivo</a>
            </div>
        </div>
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
        <div class="col-md-12">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
        </div>
        <div class="col-md-12">
            <embed id="miIFrame" src="{{URL::to('/')}}/../storage/app/hc_ima/{{$imagen->nombre}}" width="100%" type='application/pdf'>
            <div class="col-md-3 col-md-offset-8" style="margin:20px 0;">
                <a class="btn btn-primary" onclick="eliminar('{{$imagen->id}}');">Eliminar Archivo</a>
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
            <iframe id='miIFrame'  src="https://view.officeapps.live.com/op/view.aspx?src={{$ruta}}" width="100%" style="border: none;"></iframe>
            <div class="col-md-3 col-md-offset-8" style="margin:20px 0;">
                <a class="btn btn-primary" onclick="eliminar('{{$imagen->id}}');">Eliminar Archivo</a>
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
                url:'{{ url("historiaclinica/video/eliminar_foto")}}/'+id,
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
