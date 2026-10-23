<style type="text/css"> 
.parent{
      overflow-y:scroll;
      height: 600px;
  }
  .parent::-webkit-scrollbar {
      width: 8px;
  } /* this targets the default scrollbar (compulsory) */
  .parent::-webkit-scrollbar-thumb {
      background: #004AC1;
      border-radius: 10px;
  }
  .parent::-webkit-scrollbar-track {
    width: 10px;
      background-color: #004AC1;
      box-shadow: inset 0px 0px 0px 3px #56ABE3;
  }
</style>
<!--Div principal-->
<div id="area_index" class="container-fluid" style="padding-left: 8px">
  <!--Fila-->
  <div class="row">
    <div class="col-12" style="padding-left: 0.;padding-left: 0px;padding-right: 9px;margin-left: 5px;margin-right: 0px; ">
      <div class="col-12" style="border: 2px solid #004AC1; margin-left: 0px;margin-right: 0px;margin-left: 4px;padding-right: 0px;padding-left: 0px; background-color: #56ABE3; ">
            <h1 style="font-size: 15px; margin:0; background-color: #004AC1; color: white;" >
              <img style="width: 35px; margin-left: 15px; margin-right: 5px; margin-bottom: 5px" src="{{asset('/')}}hc4/img/iconos/biopsias.png"> 
              <b>HISTORIAL DE BIOPSIAS</b>
              @if(!is_null($paciente)) 
                <center> 
                  <div class="col-12" style="padding-bottom: 20px;">
                    <h1 style="font-size: 14px; margin:0; background-color: #004AC1; color: white;padding-left: 20px" >
                        <b>PACIENTE : {{$paciente->apellido1}} {{$paciente->apellido2}}
                              {{$paciente->nombre1}} {{$paciente->nombre2}}
                        </b>
                    </h1>
                  </div> 
                </center>
              @endif  
            </h1>   
          <div style="border-left-width: 20px; padding-left: 15px; padding-right: 10px; padding-top: 20px;padding-bottom: 0px; background-color: #56ABE3; margin-left: 0px"  >
            <div class="parent" style="margin-left: 0px;   margin-bottom: 10px ; height: 450px " >
              <div style=" margin-right: 30px;">
                @foreach($biopsias_3 as $imagen)              
                      <div class="box" style="border: 2px solid #004AC1; border-radius: 10px; background-color: white;  font-family: Helvetica; margin-bottom: 30px; padding-left: 0px; padding-right: 0px" class="col-md-12">
                        <div class="box-header " style="padding-left: 0px; padding-right: 0px" >
                          <div class="row" style="background-color: #004AC1; color: white; margin-top: 7px; margin-left: 10px; margin-right: 10px">
                            <div class="col-md-11 col-sm-11 col-10" style="text-align: center;"><span>
                              @if(!is_null($imagen->updated_at))
                                  @php 
                                  $dia =  Date('N',strtotime($imagen->updated_at)); 
                                  $mes =  Date('n',strtotime($imagen->updated_at)); @endphp
                                    <span style="font-family: 'Helvetica'; font-size: 14px" class="box-title";> 
                                      @if($dia == '1') Lunes 
                                         @elseif($dia == '2') Martes
                                         @elseif($dia == '3') Miércoles 
                                         @elseif($dia == '4') Jueves 
                                         @elseif($dia == '5') Viernes 
                                         @elseif($dia == '6') Sábado 
                                         @elseif($dia == '7') Domingo 
                                      @endif 
                                        {{substr($imagen->created_at,8,2)}} de 
                                      @if($mes == '1') Enero 
                                         @elseif($mes == '2') Febrero 
                                         @elseif($mes == '3') Marzo 
                                         @elseif($mes == '4') Abril 
                                         @elseif($mes == '5') Mayo 
                                         @elseif($mes == '6') Junio 
                                         @elseif($mes == '7') Julio 
                                         @elseif($mes == '8') Agosto 
                                         @elseif($mes == '9') Septiembre 
                                         @elseif($mes == '10') Octubre 
                                         @elseif($mes == '11') Noviembre 
                                         @elseif($mes == '12') Diciembre 
                                      @endif 
                                        del {{substr($imagen->updated_at,0,4)}}</span>
                              @endif
                              </span></div>
                              <div class="pull-right box-tools " >
                                <button   type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
                                  <i class="fa fa-minus"></i>
                                </button>
                              </div>
                          </div>
                        </div>
                        <!--Fila 2--> 
                        <div class="box-body">
                          <div class="row" style="padding-right: 15px">
                              <!--Columna de 9--> 
                            <div class="col-md-9 col-12">
                              <div style="border: 2px solid #004AC1; margin-bottom: 30px; margin-left: 15px ">
                                  <embed style="max-width: 900px; height: 300px" id="miIFrame" src="{{route('formulario.013B', ['id' =>$imagen->id_hc_procedimiento])}}" width="100%" type='application/pdf'> 
                              </div>
                            </div>
                              <!--Columna de 3--> 
                            <div class="col-md-3 col-12" style="padding-left: 0px">
                              <center>
                                <div class="col-md-12 col-5" style="padding-left: 0px; padding-right: 0px">
                                  <a class="btn btn-info btn_accion" target="_blank" href="{{route('formulario.013B', ['id' =>$imagen->id_hc_procedimiento])}}" style="height: 100%; width: 100%;  font-size: 14px">
                                      <div class="row" style="margin-left: 0px; margin-right: 0px">
                                        <div class="col-12" style="padding-left: 0px; padding-right: 0px">
                                          <label style="color: white; padding-left: 5px;  padding-top: 1px" >Ver en Pantalla Completa</label>
                                        </div>
                                      </div>
                                    </a>
                                </div>
                              </center>
                            </div>
                          </div>
                        </div>
                      </div>
                        <!--LLamada IFrame--> 
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
                @endforeach

                @foreach($biopsias_1 as $imagen)
                  @php 
                    $explotar = explode( '.', $imagen->nombre);
                    $extension = end($explotar);
                  @endphp
                  @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') || ($extension == 'JPG') || ($extension == 'JPEG') || ($extension == 'PNG'))
                      @php
                          $variable = explode('/' , asset('/hc_ima/'));
                          $d1 = $variable[3];
                          //$d2 = $variable[4]; 
                          $variable =  env('APP_URL').'/'.$d1.'/'.$imagen->nombre;
                      @endphp
                      <div class="box" style="border: 2px solid #004AC1; border-radius: 10px; background-color: white;  font-family: Helvetica; margin-bottom: 30px; padding-left: 0px; padding-right: 0px">
                         <!--@php
                            $fecha = substr($imagen->created_at, 0, 10);
                            $invert = explode( '-',$fecha);
                            $fecha_invert = $invert[2]."-".$invert[1]."-".$invert[0]; 
                          @endphp-->
                        <div class="box-header " style="padding-left: 0px; padding-right: 0px" >
                          <div class="row" style="background-color: #004AC1; color: white; margin-top: 7px; margin-left: 10px; margin-right: 10px">

                            <div class="col-md-11 col-sm-11 col-10" style="text-align: center;">
                              <span>
                                @if(!is_null($imagen->created_at))
                                    @php 
                                    $dia =  Date('N',strtotime($imagen->created_at)); 
                                    $mes =  Date('n',strtotime($imagen->created_at)); @endphp
                                      <span style="font-family: 'Helvetica'; font-size: 14px" class="box-title";> 
                                        @if($dia == '1') Lunes 
                                           @elseif($dia == '2') Martes
                                           @elseif($dia == '3') Miércoles 
                                           @elseif($dia == '4') Jueves 
                                           @elseif($dia == '5') Viernes 
                                           @elseif($dia == '6') Sábado 
                                           @elseif($dia == '7') Domingo 
                                        @endif 
                                          {{substr($imagen->created_at,8,2)}} de 
                                        @if($mes == '1') Enero 
                                           @elseif($mes == '2') Febrero 
                                           @elseif($mes == '3') Marzo 
                                           @elseif($mes == '4') Abril 
                                           @elseif($mes == '5') Mayo 
                                           @elseif($mes == '6') Junio 
                                           @elseif($mes == '7') Julio 
                                           @elseif($mes == '8') Agosto 
                                           @elseif($mes == '9') Septiembre 
                                           @elseif($mes == '10') Octubre 
                                           @elseif($mes == '11') Noviembre 
                                           @elseif($mes == '12') Diciembre 
                                        @endif 
                                          del {{substr($imagen->created_at,0,4)}}
                                      </span>
                                @endif
                              </span>
                            </div>
                            <div class="pull-right box-tools " >
                              <button   type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
                                <i class="fa fa-minus"></i>
                              </button>
                            </div>
                          </div>
                        </div>
                          <!--Fila 1--> 
                        <div class="box-body">
                          <div class="row" style="padding-right: 15px">
                            <!--Columna 9--> 
                            <div class="col-md-9 col-12" >
                              <div style="border: 2px solid #004AC1;  margin-bottom: 30px; margin-left: 15px">
                                <div class="row" id="imagen_solita">
                                    <div class="col-12">
                                        <img id="imafoto{{$imagen->id}}" src="{{asset('hc_ima/'.$imagen->nombre)}}" alt="Imagen Ingresada" style="max-width: 900px; max-height: 300px">
                                    </div>
                                </div>
                              </div>
                            </div>
                            <!--Columna 3--> 
                            <div class="col-md-3 col-12" style="padding-left: 0px">
                              <center>
                                <div class="col-md-12 col-5" style="padding-left: 0px; padding-right: 0px">
                                  <a class="btn btn-info btn_accion" href="{{asset('laboratorio_externo_descarga')}}/{{$imagen->id}}" style="height: 100%; width: 100%; font-size: 14px">
                                    <div class="row" style="margin-left: 0px; margin-right: 0px">
                                      <div class="col-12" style="padding-left: 0px; padding-right: 0px">
                                        <img width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
                                        <label style="color: white; padding-left: 5px;  padding-top: 1px" >Descargar</label>
                                      </div>
                                    </div>
                                  </a>
                                </div>
                              </center>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <script src="{{asset('/js/lupa.js')}}" > </script>
                      <script>
                            $(document).ready(function() {
                              $("#imafoto{{$imagen->id}}").mlens(
                              {
                                imgSrc: $("#imafoto{{$imagen->id}}").attr("data-big"),   // path of the hi-res version of the image
                                lensShape: "circle",                // shape of the lens (circle/square)
                                lensSize: 200,                  // size of the lens (in px)
                                borderSize: 1,                  // size of the lens border (in px)
                                borderColor: "#000000",                // color of the lens border (#hex)
                                zoomLevel: 2,  
                                borderRadius: 0,                // border radius (optional, only if the shape is square)
                                imgOverlay: $("#imafoto{{$imagen->id}}").attr("data-overlay"), // path of the overlay image (optional)
                                overlayAdapt: true, // true if the overlay image has to adapt to the lens size (true/false)
                                responsive: true 
                              });
                            });
                      </script>
                  @elseif(($extension == 'pdf') || ($extension == 'PDF'))
                      @php
                          $variable = explode('/' , asset('/hc_ima/'));
                          $d1 = $variable[3];
                          //$d2 = $variable[4];
                          $variable =  env('APP_URL').'/'.$d1.'/'.$imagen->nombre;
                      @endphp
              
                      <div class="box" style="border: 2px solid #004AC1; border-radius: 10px; background-color: white;  font-family: Helvetica; margin-bottom: 30px; padding-left: 0px; padding-right: 0px" class="col-md-12">
                           <!--@php
                            $fecha = substr($imagen->created_at, 0, 10);
                            $invert = explode( '-',$fecha);
                            $fecha_invert = $invert[2]."-".$invert[1]."-".$invert[0]; 
                          @endphp-->
                        <div class="box-header " style="padding-left: 0px; padding-right: 0px" >
                          <div class="row" style="background-color: #004AC1; color: white; margin-top: 7px; margin-left: 10px; margin-right: 10px">
                            <div class="col-md-11 col-sm-11 col-10" style="text-align: center;"><span>
                              @if(!is_null($imagen->created_at))
                                  @php 
                                  $dia =  Date('N',strtotime($imagen->created_at)); 
                                  $mes =  Date('n',strtotime($imagen->created_at)); @endphp
                                    <span style="font-family: 'Helvetica'; font-size: 14px" class="box-title";> 
                                      @if($dia == '1') Lunes 
                                         @elseif($dia == '2') Martes
                                         @elseif($dia == '3') Miércoles 
                                         @elseif($dia == '4') Jueves 
                                         @elseif($dia == '5') Viernes 
                                         @elseif($dia == '6') Sábado 
                                         @elseif($dia == '7') Domingo 
                                      @endif 
                                        {{substr($imagen->created_at,8,2)}} de 
                                      @if($mes == '1') Enero 
                                         @elseif($mes == '2') Febrero 
                                         @elseif($mes == '3') Marzo 
                                         @elseif($mes == '4') Abril 
                                         @elseif($mes == '5') Mayo 
                                         @elseif($mes == '6') Junio 
                                         @elseif($mes == '7') Julio 
                                         @elseif($mes == '8') Agosto 
                                         @elseif($mes == '9') Septiembre 
                                         @elseif($mes == '10') Octubre 
                                         @elseif($mes == '11') Noviembre 
                                         @elseif($mes == '12') Diciembre 
                                      @endif 
                                        del {{substr($imagen->created_at,0,4)}}</span>
                              @endif
                              </span></div>
                              <div class="pull-right box-tools " >
                                <button   type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
                                  <i class="fa fa-minus"></i>
                                </button>
                              </div>
                          </div>
                        </div>
                        <!--Fila 2--> 
                        <div class="box-body">
                          <div class="row" style="padding-right: 15px">
                              <!--Columna de 9--> 
                            <div class="col-md-9 col-12">
                              <div style="border: 2px solid #004AC1; margin-bottom: 30px; margin-left: 15px ">
                                  {{-- <embed style="max-width: 900px; height: 300px" id="miIFrame" src="{{URL::to('/')}}/../storage/app/hc_ima/{{$imagen->nombre}}" width="100%" type='application/pdf'>  --}}
															    <embed id="miIFrame" src="{{route('video.ver_archivo_biopsia',['id_archivo' => $imagen->nombre])}}" width="100%" type='application/pdf'>
                              </div>
                            </div>
                              <!--Columna de 3--> 
                            <div class="col-md-3 col-12" style="padding-left: 0px">
                              <center>
                                <div class="col-md-12 col-5" style="padding-left: 0px; padding-right: 0px">
                                  <a class="btn btn-info btn_accion" href="{{asset('laboratorio_externo_descarga')}}/{{$imagen->id}}" style="height: 100%; width: 100%; font-size: 14px">
                                    <div class="row" style="margin-left: 0px; margin-right: 0px"> 
                                      <div class="col-12" style="padding-left: 0px; padding-right: 0px">
                                        <img width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
                                        <label style="color: white; padding-left: 5px;  padding-top: 1px" >Descargar</label>
                                      </div>
                                    </div>
                                  </a>
                                </div>
                              </center>
                            </div>
                          </div>
                        </div>
                      </div>
                        <!--LLamada IFrame--> 
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
                        //$d2 = $variable[4];
                        $ruta = "http%3A%2F%2F186.70.157.2%3A86%2F".$d1."%2Fstorage%2Fapp%2Fhc_ima%2F".$imagen->nombre;
                    @endphp
                    <div class="box" style="border: 2px solid #004AC1; border-radius: 10px; background-color: white;  font-family: Helvetica; margin-bottom: 30px; padding-left: 0px; padding-right: 0px" class="col-md-12">
                          <!--@php
                            $fecha = substr($imagen->created_at, 0, 10);
                            $invert = explode( '-',$fecha);
                            $fecha_invert = $invert[2]."-".$invert[1]."-".$invert[0]; 
                          @endphp-->
                        <div class="box-header " style="padding-left: 0px; padding-right: 0px" >
                          <div class="row" style="background-color: #004AC1; color: white; margin-top: 7px; margin-left: 10px; margin-right: 10px">
                            
                            <div class="col-md-11 col-sm-11 col-10" style="text-align: center;">
                              <span>
                                @if(!is_null($imagen->created_at))
                                    @php 
                                    $dia =  Date('N',strtotime($imagen->created_at)); 
                                    $mes =  Date('n',strtotime($imagen->created_at)); @endphp
                                      <span style="font-family: 'Helvetica'; font-size: 14px" class="box-title";> 
                                        @if($dia == '1') Lunes 
                                           @elseif($dia == '2') Martes
                                           @elseif($dia == '3') Miércoles 
                                           @elseif($dia == '4') Jueves 
                                           @elseif($dia == '5') Viernes 
                                           @elseif($dia == '6') Sábado 
                                           @elseif($dia == '7') Domingo 
                                        @endif 
                                          {{substr($imagen->created_at,8,2)}} de 
                                        @if($mes == '1') Enero 
                                           @elseif($mes == '2') Febrero 
                                           @elseif($mes == '3') Marzo 
                                           @elseif($mes == '4') Abril 
                                           @elseif($mes == '5') Mayo 
                                           @elseif($mes == '6') Junio 
                                           @elseif($mes == '7') Julio 
                                           @elseif($mes == '8') Agosto 
                                           @elseif($mes == '9') Septiembre 
                                           @elseif($mes == '10') Octubre 
                                           @elseif($mes == '11') Noviembre 
                                           @elseif($mes == '12') Diciembre 
                                        @endif 
                                          del {{substr($imagen->created_at,0,4)}}
                                      </span>
                                @endif
                              </span>
                            </div>

                              <div class="pull-right box-tools " >
                                <button   type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
                                  <i class="fa fa-minus"></i>
                                </button>
                              </div>
                          </div>
                        </div>
                        <div class="box-body">
                          <div class="row" style="padding-right: 15px">
                            <div class="col-md-9 col-12">  
                              <div style="border: 2px solid #004AC1; margin-bottom: 30px; margin-left: 15px ">
                                <embed style="max-width: 900px; height: 300px" id="miIFrame" src="https://docs.google.com/viewer?hl=en&embedded=true&url={{$ruta}}" width="100%" style="border: none;">
                              </div>
                            </div>
                            <div class="col-md-3 col-12" style="padding-left: 0px">
                              <center>
                                <div class="col-md-12 col-5" style="padding-left: 0px; padding-right: 0px">
                                  <a class="btn btn-info btn_accion" href="{{asset('laboratorio_externo_descarga')}}/{{$imagen->id}}" style="height: 100%; width: 100%; font-size: 14px">
                                    <div class="row" style="margin-left: 0px; margin-right: 0px">
                                      <div class="col-12" style="padding-left: 0px; padding-right: 0px">
                                        <img width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
                                        <label style="color: white; padding-left: 5px;  padding-top: 1px" >Descargar</label>
                                      </div>
                                    </div>
                                  </a>
                                </div>
                              </center>
                            </div>
                          </div>
                        </div>
                    </div>
                  @endif
                @endforeach

                @foreach($biopsias_2 as $imagen)
                    @php
                      $explotar = explode( '.', $imagen->nombre);
                      $extension = end($explotar);
                    @endphp 
                    @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') || ($extension == 'JPG') || ($extension == 'JPEG') || ($extension == 'PNG'))
                      @php
                          $variable = explode('/' , asset('/hc_ima/'));
                          $d1 = $variable[3];
                          //$d2 = $variable[4]; 
                          $variable =  env('APP_URL').'/'.$d1.'/'.$imagen->nombre;
                      @endphp
                      <div class="box" style="border: 2px solid #004AC1; border-radius: 10px; background-color: white;  font-family: Helvetica; margin-bottom: 30px; padding-left: 0px; padding-right: 0px" class="col-md-12">
                           <!--@php
                            $fecha = substr($imagen->created_at, 0, 10);
                            $invert = explode( '-',$fecha);
                            $fecha_invert = $invert[2]."-".$invert[1]."-".$invert[0]; 
                          @endphp-->
                        <div class="box-header " style="padding-left: 0px; padding-right: 0px" >
                          <div class="row" style="background-color: #004AC1; color: white; margin-top: 7px; margin-left: 10px; margin-right: 10px">
                            <div class="col-md-11 col-sm-11 col-10" style="text-align: center;">
                              <span>
                                @if(!is_null($imagen->created_at))
                                    @php 
                                    $dia =  Date('N',strtotime($imagen->created_at)); 
                                    $mes =  Date('n',strtotime($imagen->created_at)); @endphp
                                    <span style="font-family: 'Helvetica'; font-size: 14px" class="box-title";> 
                                      @if($dia == '1') Lunes 
                                         @elseif($dia == '2') Martes
                                         @elseif($dia == '3') Miércoles 
                                         @elseif($dia == '4') Jueves 
                                         @elseif($dia == '5') Viernes 
                                         @elseif($dia == '6') Sábado 
                                         @elseif($dia == '7') Domingo 
                                      @endif 
                                        {{substr($imagen->created_at,8,2)}} de 
                                      @if($mes == '1') Enero 
                                         @elseif($mes == '2') Febrero 
                                         @elseif($mes == '3') Marzo 
                                         @elseif($mes == '4') Abril 
                                         @elseif($mes == '5') Mayo 
                                         @elseif($mes == '6') Junio 
                                         @elseif($mes == '7') Julio 
                                         @elseif($mes == '8') Agosto 
                                         @elseif($mes == '9') Septiembre 
                                         @elseif($mes == '10') Octubre 
                                         @elseif($mes == '11') Noviembre 
                                         @elseif($mes == '12') Diciembre 
                                      @endif 
                                        del {{substr($imagen->created_at,0,4)}}
                                    </span>
                                @endif
                              </span>
                            </div>
                            <div class="pull-right box-tools " >
                              <button   type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
                                <i class="fa fa-minus"></i>
                              </button>
                            </div>
                          </div>
                        </div>
                        <!--Fila 1-->
                        <div class="box-body"> 
                          <div class="row" style="padding-right: 15px">
                            <!--Columna 9--> 
                            <div class="col-md-9 col-12" >
                              <div style="border: 2px solid #004AC1;  margin-bottom: 30px; margin-left: 15px">
                                <div class="row" id="imagen_solita">
                                    <div class="col-12">
                                        <img id="imafoto{{$imagen->id}}" src="{{asset('hc_ima/'.$imagen->nombre)}}" alt="Imagen Ingresada" style="max-width: 900px; max-height: 300px">
                                        <!--<div class="col-md-3 col-md-offset-8" style="margin:20px 0;">
                                            <a class="btn btn-info btn_accion" style="height: 100%; font-size: 16px; color: white;" onclick="eliminar('{{$imagen->id}}');">Eliminar foto  </a>
                                        </div>-->
                                    </div>
                                </div>
                              </div>
                            </div>
                            <!--Columna 3--> 
                            <div class="col-md-3 col-12" style="padding-left: 0px">
                              <center>
                                <div class="col-md-12 col-5" style="padding-left: 0px; padding-right: 0px">
                                  <a class="btn btn-info btn_accion" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" style=" width: 100%; height: 100%; font-size: 14px">
                                    <div class="row" style="margin-left: 0px; margin-right: 0px">
                                      <div class="col-12" style="padding-left: 0px; padding-right: 0px">
                                        <img width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
                                        <label style="color: white; padding-left: 5px;  padding-top: 1px" >Descargar</label>
                                      </div>
                                    </div>
                                  </a>
                                </div>
                              </center>
                            </div>
                          </div>
                        </div> 
                      </div>
                      
                      <script src="{{asset('/js/lupa.js')}}" > </script>
                      <script>
                        $(document).ready(function() {
                          $("#imafoto{{$imagen->id}}").mlens(
                          {
                              imgSrc: $("#imafoto{{$imagen->id}}").attr("data-big"),   // path of the hi-res version of the image
                              lensShape: "circle",                // shape of the lens (circle/square)
                              lensSize: 200,                  // size of the lens (in px)
                              borderSize: 1,                  // size of the lens border (in px)
                              borderColor: "#000000",                // color of the lens border (#hex)
                              zoomLevel: 2,  
                              borderRadius: 0,                // border radius (optional, only if the shape is square)
                              imgOverlay: $("#imafoto{{$imagen->id}}").attr("data-overlay"), // path of the overlay image (optional)
                              overlayAdapt: true, // true if the overlay image has to adapt to the lens size (true/false)
                              responsive: true 
                          });
                        });
                      </script>
                    @elseif(($extension == 'pdf') || ($extension == 'PDF'))
                        @php
                            $variable = explode('/' , asset('/hc_ima/'));
                            $d1 = $variable[3];
                            //$d2 = $variable[4];
                            $variable =  env('APP_URL').'/'.$d1.'/'.$imagen->nombre;
                        @endphp
                
                        <div class="box" style="border: 2px solid #004AC1; border-radius: 10px; background-color: white;  font-family: Helvetica; margin-bottom: 30px; padding-left: 0px; padding-right: 0px" class="col-md-12">
                          <!--@php
                            $fecha = substr($imagen->created_at, 0, 10);
                            $invert = explode( '-',$fecha);
                            $fecha_invert = $invert[2]."-".$invert[1]."-".$invert[0]; 
                          @endphp-->
                          <div class="box-header " style="padding-left: 0px; padding-right: 0px" >
                          <div class="row" style="background-color: #004AC1; color: white; margin-top: 7px; margin-left: 10px; margin-right: 10px">
                            <div class="col-md-11 col-sm-11 col-10" style="text-align: center;"><span>
                              @if(!is_null($imagen->created_at))
                                  @php 
                                  $dia =  Date('N',strtotime($imagen->created_at)); 
                                  $mes =  Date('n',strtotime($imagen->created_at)); @endphp
                                    <span style="font-family: 'Helvetica'; font-size: 14px" class="box-title";> 
                                      @if($dia == '1') Lunes 
                                         @elseif($dia == '2') Martes
                                         @elseif($dia == '3') Miércoles 
                                         @elseif($dia == '4') Jueves 
                                         @elseif($dia == '5') Viernes 
                                         @elseif($dia == '6') Sábado 
                                         @elseif($dia == '7') Domingo 
                                      @endif 
                                        {{substr($imagen->created_at,8,2)}} de 
                                      @if($mes == '1') Enero 
                                         @elseif($mes == '2') Febrero 
                                         @elseif($mes == '3') Marzo 
                                         @elseif($mes == '4') Abril 
                                         @elseif($mes == '5') Mayo 
                                         @elseif($mes == '6') Junio 
                                         @elseif($mes == '7') Julio 
                                         @elseif($mes == '8') Agosto 
                                         @elseif($mes == '9') Septiembre 
                                         @elseif($mes == '10') Octubre 
                                         @elseif($mes == '11') Noviembre 
                                         @elseif($mes == '12') Diciembre 
                                      @endif 
                                        del {{substr($imagen->created_at,0,4)}}</span>
                              @endif
                              </span></div>

                              <div class="pull-right box-tools " >
                                <button   type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
                                  <i class="fa fa-minus"></i>
                                </button>
                              </div>
                          </div>
                          </div>
                          <!--Fila 2--> 
                          <div class="box-body"> 
                            <div class="row" style="padding-right: 15px" >
                                <!--Columna de 9--> 
                                <div class="col-md-9 col-12">
                                  <div style="border: 2px solid #004AC1;  margin-bottom: 30px; margin-left: 15px ">
                                      <embed style="max-width: 900px; height: 300px" id="miIFrame" src="{{URL::to('/')}}/../storage/app/hc_ima/{{$imagen->nombre}}" width="100%" type='application/pdf'> 
                                  </div>
                                </div>
                                <!--Columna de 3--> 
                                <div class="col-md-3 col-12" style="padding-left: 0px">
                                  <center>
                                    <div class="col-md-12 col-5" style="padding-left: 0px; padding-right: 0px">
                                      <a class="btn btn-info btn_accion" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" style="height: 100%; width: 100%;  font-size: 14px">
                                        <div class="row" style="margin-left: 0px; margin-right: 0px">
                                          <div class="col-12" style="padding-left: 0px; padding-right: 0px">
                                            <img width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
                                            <label style="color: white; padding-left: 5px;  padding-top: 1px" >Descargar</label>
                                          </div>
                                        </div>
                                      </a>
                                    </div>
                                  </center>
                                </div>
                              </div>
                            </div>
                          </div>
                          <!--LLamada IFrame--> 
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
                            //$d2 = $variable[4];
                            $ruta = "http%3A%2F%2F186.70.157.2%3A86%2F".$d1."%2Fstorage%2Fapp%2Fhc_ima%2F".$imagen->nombre;
                        @endphp
                        <div class="box" style="border: 2px solid #004AC1; border-radius: 10px; background-color: white;  font-family: Helvetica; margin-bottom: 30px; padding-left: 0px; padding-right: 0px" class="col-md-12">
                          <!--@php
                            $fecha = substr($imagen->created_at, 0, 10);
                            $invert = explode( '-',$fecha);
                            $fecha_invert = $invert[2]."-".$invert[1]."-".$invert[0]; 
                          @endphp-->
                          <div class="box-header " style="padding-left: 0px; padding-right: 0px" >
                            <div class="row" style="background-color: #004AC1; color: white; margin-top: 7px; margin-left: 10px; margin-right: 10px">
                              
                              <div class="col-md-11 col-sm-11 col-10" style="text-align: center;"><span>
                              @if(!is_null($imagen->created_at))
                                  @php 
                                  $dia =  Date('N',strtotime($imagen->created_at)); 
                                  $mes =  Date('n',strtotime($imagen->created_at)); @endphp
                                    <span style="font-family: 'Helvetica'; font-size: 14px" class="box-title";> 
                                      @if($dia == '1') Lunes 
                                         @elseif($dia == '2') Martes
                                         @elseif($dia == '3') Miércoles 
                                         @elseif($dia == '4') Jueves 
                                         @elseif($dia == '5') Viernes 
                                         @elseif($dia == '6') Sábado 
                                         @elseif($dia == '7') Domingo 
                                      @endif 
                                        {{substr($imagen->created_at,8,2)}} de 
                                      @if($mes == '1') Enero 
                                         @elseif($mes == '2') Febrero 
                                         @elseif($mes == '3') Marzo 
                                         @elseif($mes == '4') Abril 
                                         @elseif($mes == '5') Mayo 
                                         @elseif($mes == '6') Junio 
                                         @elseif($mes == '7') Julio 
                                         @elseif($mes == '8') Agosto 
                                         @elseif($mes == '9') Septiembre 
                                         @elseif($mes == '10') Octubre 
                                         @elseif($mes == '11') Noviembre 
                                         @elseif($mes == '12') Diciembre 
                                      @endif 
                                        del {{substr($imagen->created_at,0,4)}}</span>
                              @endif
                              </span></div>

                              <div class="pull-right box-tools " >
                                <button   type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
                                  <i class="fa fa-minus"></i>
                                </button>
                              </div>
                            </div>
                          </div>
                          <div class="box-body">
                            <div class="row" style="padding-right: 15px">
                              <div class="col-md-9 col-12">  
                                <div style="border: 2px solid #004AC1; margin-bottom: 30px; margin-left: 15px ">
                                  <embed style="max-width: 900px; max-height: 300px" id="miIFrame" src="https://docs.google.com/viewer?hl=en&embedded=true&url={{$ruta}}" width="100%" style="border: none;">
                                </div>
                              </div>
                              <div class="col-md-3 col-12" style="padding-left: 0px">
                                <center>
                                  <div class="col-md-12 col-5" style="padding-left: 0px; padding-right: 0px">
                                    <a class="btn btn-info btn_accion" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" style="height: 100%; width: 100%; font-size: 14px">
                                      <div class="row" style="margin-left: 0px; margin-right: 0px">
                                        <div class="col-12" style="padding-left: 0px; padding-right: 0px">
                                          <img width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
                                          <label style="color: white; padding-left: 5px;  padding-top: 1px" >Descargar</label>
                                        </div>
                                      </div>
                                    </a>
                                  </div>
                                </center>
                              </div>
                            </div>
                          </div>
                        </div>
                    @endif
                @endforeach
              </div>  
            </div>
          </div>
      </div>
    </div>
  </div>
</div>
