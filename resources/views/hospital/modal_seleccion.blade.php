<style type="text/css">
    ·image:hover{
        cursor: pointer;
    }
    .seleccionada_imagen:hover{
      transform:scale(0.9);
        -ms-transform:scale(0.9); // IE 9 
        -moz-transform:scale(0.9); // Firefox 
        -webkit-transform:scale(0.9); // Safari and Chrome 
        -o-transform:scale(0.9) ;
        -webkit-filter: grayscale(80%); 
        filter: grayscale(80%);
    }
</style>

    <div class="row" id="imagen_solita" style="text-align: center;">
        <div class="col-md-12">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span>
            </button>
        </div>

        <div class="col-12">
            <h1>Seleccione el Tipo de Formato a Descargar</h1>
        </div>

        <div class="col-12">
            <div class="row">
                <div class="col-md-3 col-sm-4 col-6 imagen" style="text-align: center;">
                    <a target="_blank" href="{{route('hc_reporte.descargar', ['id'=> $id_protocolo, 'tipo' => '1' ])}}">
                        <img class="seleccionada_imagen" src="{{asset('formato/1.jpg')}}" width="90%">
                    </a>
                </div>
                <div class="col-md-3 col-sm-4 col-6 imagen" style="text-align: center;">
                    <a target="_blank" href="{{route('hc_reporte.descargar', ['id'=> $id_protocolo, 'tipo' => '2' ])}}">
                        <img class="seleccionada_imagen" src="{{asset('formato/2.jpg')}}" width="90%">
                    </a>
                </div>
                <div class="col-md-3 col-sm-4 col-6 imagen" style="text-align: center;">
                    <a target="_blank" href="{{route('hc_reporte.descargar', ['id'=> $id_protocolo, 'tipo' => '3' ])}}">
                        <img class="seleccionada_imagen" src="{{asset('formato/3.jpg')}}" width="90%">
                    </a>
                </div>
                <div class="col-md-3 col-sm-4 col-6 imagen" style="text-align: center;">
                    <a target="_blank" href="{{route('hc_reporte.descargar', ['id'=> $id_protocolo, 'tipo' => '4' ])}}">
                        <img class="seleccionada_imagen" src="{{asset('formato/4.jpg')}}" width="90%">
                    </a>
                </div>
                <div class="col-md-3 col-sm-4 col-6 imagen" style="text-align: center;">
                    <a target="_blank" href="{{route('hc_reporte.descargar', ['id'=> $id_protocolo, 'tipo' => '5' ])}}">
                        <img class="seleccionada_imagen" src="{{asset('formato/5.jpg')}}" width="90%">
                    </a>
                </div>
                <div class="col-md-3 col-sm-4 col-6 imagen" style="text-align: center;">
                    <a target="_blank" href="{{route('hc_reporte.descargar', ['id'=> $id_protocolo, 'tipo' => '6' ])}}">
                        <img class="seleccionada_imagen" src="{{asset('formato/6.jpg')}}" width="90%">
                    </a>
                </div>
                <div class="col-md-3 col-sm-4 col-6 imagen" style="text-align: center;">
                    <a target="_blank" href="{{route('hc_reporte.descargar', ['id'=> $id_protocolo, 'tipo' => '7' ])}}">
                        <img class="seleccionada_imagen" src="{{asset('formato/7.jpg')}}" width="90%">
                    </a>
                </div>
                <div class="col-md-3 col-sm-4 col-6 imagen" style="text-align: center;">
                    <a target="_blank" href="{{route('hc_reporte.descargar', ['id'=> $id_protocolo, 'tipo' => '8' ])}}">
                        <img class="seleccionada_imagen" src="{{asset('formato/8.jpg')}}" width="90%">
                    </a>
                </div>
                <div class="col-md-3 col-sm-4 col-12 imagen" style="text-align: center;">
                    <a target="_blank" href="{{route('hc_reporte.descargar', ['id'=> $id_protocolo, 'tipo' => '9' ])}}">
                        <img class="seleccionada_imagen" src="{{asset('formato/9.jpg')}}" width="90%">
                    </a>
                </div>
            </div>
        </div>  

        <br>
        <div class="col-12" style="height: 35px;"></div>
    </div>