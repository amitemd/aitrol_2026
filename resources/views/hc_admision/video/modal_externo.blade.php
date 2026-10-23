@php
    $explotar = explode( '.', $imagen->nombre);
    $extension = end($explotar);
@endphp
@if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') || ($extension == 'JPG') || ($extension == 'JPEG') || ($extension == 'PNG'))
    <div class="row" id="imagen_solita" style="text-align: center;">
        <div class="col-md-12">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
            <img id="imafoto" src="{{asset('hc_ima/'.$imagen->nombre)}}" alt="Imagen Ingresada" style="max-width: 900px;">
        </div> 
        <div class="col-md-3 col-md-offset-8" style="margin:20px 0;">
            <a class="btn btn-primary" onclick="eliminar('{{$imagen->id}}');">Eliminar Archivo </a>
        </div>
    </div>
    
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
                <a class="btn btn-primary" onclick="eliminar('{{$imagen->id}}');">Eliminar Archivo </a>
            </div> 
        </div>
    </div>
@elseif(($extension == 'pdf') || ($extension == 'PDF'))
   
    <div class="row" >
        <div class="col-md-12">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
        </div>
        <div class="col-md-12">
            {{-- <embed id="miIFrame" src="{{URL::to('/')}}/../storage/app/hc_ima/{{$imagen->nombre}}" width="100%" type='application/pdf'> --}}
            <embed id="miIFrame" src="{{route('video.ver_archivo_biopsia',['id_archivo' => $imagen->nombre])}}" width="100%" type='application/pdf'>
            <div class="col-md-3 col-md-offset-8" style="margin:20px 0;">
                <a class="btn btn-primary" onclick="eliminar('{{$imagen->id}}');">Eliminar Archivo </a>
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
        $ruta = "http%3A%2F%2F186.70.157.2%3A86%2F".$d1."%2Fstorage%2Fapp%2Fhc_ima%2F".$imagen->nombre;
    @endphp
    <div class="container-fluid" >
        <div class="row">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
            <br>
            <hr>
            <iframe id='miIFrame'  src="https://docs.google.com/viewer?hl=en&embedded=true&url={{$ruta}}" width="100%" style="border: none;"></iframe>
            <div class="col-md-3 col-md-offset-8" style="margin:20px 0;">
                <a class="btn btn-primary" onclick="eliminar('{{$imagen->id}}');">Eliminar Archivo </a>
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
                url:'{{ url("paciente/eliminar/biopsia")}}/'+id,
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