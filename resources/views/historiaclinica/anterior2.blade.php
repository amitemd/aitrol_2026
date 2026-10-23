@if($hcagenda[0]->tipo_documento == 'txt')
    <div class="container-fluid" >
        <div class="row">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
            <br>
            <hr>
            <div class="col-md-12"></div>
            <div class="col-md-1"></div>
            <div class="col-md-10">
                <?php echo $hcagenda[0]->texto; ?>
            </div>
        </div>
    </div>
@endif
@if($hcagenda[0]->tipo_documento == 'HCAGENDA')
    @php
        $explotar = explode( '.', $hcagenda[0]->archivo);
        $extension = end($explotar);
    @endphp
    @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png'))
    @php
        $variable = explode('/' , asset('/hc_agenda/'));
        $d1 = $variable[3];
        $d2 = $variable[4];
        $d3 = $variable[5];
        $variable =  env('APP_URL').'/'.$d1.'/'.$d2.'/'.$d3.'/'.$hcagenda[0]->archivo;
    @endphp
    <div class="row">
        <div class="col-md-12">

            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>

            <img id="imafoto" src="{{asset($hcagenda[0]->ruta.$hcagenda[0]->archivo)}}" alt="Imagen Ingresada" style="max-width: 900px;">
        </div>
    </div>
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
    @else
        @php
            $variable = explode('/' , asset('/hc_agenda/'));
            $d1 = $variable[3];
            $d2 = $variable[4];
            $d3 = $variable[5];
            $ruta = "http%3A%2F%2F186.68.76.210%3A86%2F".$d1."%2Fstorage%2Fapp%2Fhc_agenda%2F".$hcagenda[0]->archivo;
        @endphp
        <div class="container-fluid" >

            <div class="row">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
                <br>
                <hr>

                <iframe id='miIFrame'  src="https://docs.google.com/viewer?hl=en&embedded=true&url={{$ruta}}" width="100%" style="border: none;"></iframe>
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
@endif 
<script>
 
        $(document).ready(function() {

                $('#favoritesModal2').on('hidden.bs.modal', function(){
                    $(this).removeData('bs.modal');
                });


            })
 
        </script>    
