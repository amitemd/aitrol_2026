

@php
$fecha=date('Y-m-d');
$hora = date('H:i:s');
@endphp

<?php
$fechaNacimiento = new DateTime($orden->pfecha_nacimiento);
$hoy = new DateTime();
$edad = $hoy->diff($fechaNacimiento)->y;

$fechaNacimiento = new DateTime($orden->pfecha_nacimiento);
$diaNacimiento = $fechaNacimiento->format('d');

$fechaNacimiento = new DateTime($orden->pfecha_nacimiento);
$mesNacimiento = $fechaNacimiento->format('m');
?>


<style type="text/css">
    @page {
        margin: 0 0;
        
    }

    /** Define now the real margins of every page in the PDF **/
    /* body {
        margin-top: 20px;
        margin-left: 20px;
        margin-right: 20px;
        margin-bottom: 1px;
        font-size: 10px;
    } */

    /** Define the header rules **/
    header {
        position: fixed;
        top: 0cm;
        left: 0cm;
        right: 0cm;
        height: 1cm;
    }

    /** Define the footer rules **/
    footer {
        position: fixed;
        bottom: 0cm;
        left: 0cm;
        right: 0cm;
        height: 2cm;
    }

    table {
        width: 100px;
        border-collapse: collapse;
    }

    .table2d {
        border: none;
        text-align: center;
    }

    .titulo {
        background-color: #C7C3C2;
    }

    p {
        padding-top: 0;
        padding-bottom: 0;
        margin: 0;
    }

    div {
        padding-top: 0;
        padding-bottom: 0;
    }

    .txt_vertical {
        writing-mode: vertical-lr;
        transform: rotate(-90deg);
        letter-spacing: 2px;
        word-wrap: break-word;
    }

    .td-border {
        border-top: 1px solid;
        border-bottom: 1px solid;
    }


    .td-not-border-top {
        border-top: 1px solid;
        border-left: 1px solid;
        border-right: 1px solid;
    }

    .td-not-border-bottom {
        border-bottom: 1px solid;
        border-left: 1px solid;
        border-right: 1px solid;
    }

    .td-not-border-right {
        border-left: 1px solid;
        border-right: 1px solid;
        border-bottom: 1px solid;
    }

    .td-not-border-left {
        border-bottom: 1px solid;
        border-top: 1px solid;
        border-right: 1px solid;
    }

    .td-border-top {
        border-top: 1px solid;
    }

    .td-border-bottom {
        border-bottom: 1px solid;
    }

    .td-border-left {
        border-left: 1px solid;
    }

    .td-border-right {
        border-right: 1px solid;
    }

    .bg-blue {
        background-color: #DDEBF7;
        font-size: 8px;
    }

    .bg-green {
        background-color: #BADAC8;
    }

    .bg-yellow {
        background-color: #F1FD77;
    }

    .bg-purple {
        background-color: #0AB3F7 !important;
        text-align: center;
        font-size: 0.5vw;
    }

    .bg-turquoise {
        background-color: #40E0D0;
    }

    .bg-celeste {
        background-color: #00BFFF;
    }

    .text-center {
        text-align: center;
        /*font-size: 9px;*/
        font-size: 1.5vw;
    }


    .texto-izquierda-arriba {
        text-align: left;
        vertical-align: top;
    }

    .justify-text {
        text-align: justify;
    }

    .align-left {
        text-align: left;
    }

    td.contenedor-texto {
        padding: 20px;
        text-align: left;
    }

    .page-break-before {
        page-break-before: always;
    }

    .page-break-after {
        page-break-after: always;
    }

    ol {
        list-style-type: decimal;
        margin-left: 20px;
    }
    .cuadro1{
        width: 30%;
        float: left;
    }
    .cuadro2{
        width: 30%;
        float: left;
    }
    .cuadro3{
        width: 30%;
        float: right;
    }
    .div_clear{
        clear: both;
    }
    .cuadro11{
        width: 44%;
        position: relative;
    }
    .cuadro12{
        width: 4%;
        position: relative;
        left: 45%;
        
    }
    .cuadro13{
        width: 44%;
        position: relative;
        left: 50%;
        
    }
    .cuadro14{
        width: 4%;
        position: relative;
        left: 95%;
        
    }
    .cuadro21{
        width: 91%;
        position: relative;
    }
    .cuadro22{
        width: 5%;
        position: relative;
        left: 92%;
        
    }
    .cuadro31{
        width: 42%;
        position: relative;
        
    }
    .cuadro32{
        width: 4%;
        position: relative;
        left: 45%;
        
    }
    .cuadro33{
        width: 42%;
        position: relative;
        left: 50%;
        
    }
    .cuadro34{
        width: 4%;
        position: relative;
        left: 94%;
        
    }

    .outer-box {
        width: 33%;
        display: inline-block;
        vertical-align: top;
        /*background-color: red;*/
        
    }
    .outer-box2 {
        width: 33%;
        display: inline-block;
        vertical-align: top;
        /*background-color: green;*/
        
    }
    .outer-box3 {
        width: 33%;
        display: inline-block;
        vertical-align: top;
        /*background-color: yellow;*/
    }
    .outer-space {
        width: 100%;
        display: block;
        height: 10px;
    }

    .inner-box {
        display: block;
        width: 90%;
        background-color: #DDEBF7;
        /*font-size: 12px;*/
        font-size: 0.7vw;
        float: left;
        border-top: 1px solid black;
        /*border-right: 1px solid black;*/
    }
    .inner-box2 {
        display: block;
        width: 10%;
        float: left;
        border-top: 1px solid black;
        /*border-right: 1px solid black;*/
        text-align: center;
        /*font-size: 8px;*/
        font-size: 0.7vw;
    }
    .inner-box3 {
        display: block;
        width: 44%;
        background-color: #DDEBF7;
        /*font-size: 8px;*/
        font-size: 0.7vw;
        float: left;
        border-top: 1px solid black;
        /*border-right: 1px solid black;*/
        /*font-size: 8px;*/
    }

    .inner-box4 {
        display: block;
        width: 6%;
        float: right;
        border-top: 1px solid black;
        /*border-right: 1px solid black;*/
        text-align: center;
        /*font-size: 8px;*/
        font-size: 0.7vw;
    }
    .contenido{
        width: 100%;
    }

    .inner-box21 {
        width: 94%;
        background-color: #DDEBF7;
        /*font-size: 12px;*/
        font-size: 0.7vw;
        float: left;
        border-top: 1px solid black;
        /*border-right: 1px solid black;*/
    }
    .inner-box22 {
        width: 5%; 
        float: right;
        border-top: 1px solid black;
        /*border-right: 1px solid black;*/
        /*font-size: 8px;*/
        font-size: 0.7vw;
    }
    .molecular{
        position: relative;
        width: 66%;
        top: -50px;

    }
    .contenido1{
        margin-top: 30px;
        text-align: center;
    }
    .table-footer{
        position: relative;
        top: -50px;   
    }
    .table2d {
        border: none;
        text-align: center;
    }

    .titulo {
        background-color: #C7C3C2;
    }

    p {
        padding-top: 0;
        padding-bottom: 0;
        margin: 0;
    }

    div {
        padding-top: 0;
        padding-bottom: 0;
    }

    .txt_vertical {
        writing-mode: vertical-lr;
        transform: rotate(-90deg);
        letter-spacing: 2px;
        word-wrap: break-word;
    }

    .td-border2 {
        border: 1px solid;
    }

    .td-not-border-bottom {
        border-top: 1px solid;
        border-left: 1px solid;
        border-right: 1px solid;
    }

    .td-not-border-top {
        border-bottom: 1px solid;
        border-left: 1px solid;
        border-right: 1px solid;
    }

    .td-not-border-right {
        border-bottom: 1px solid;
        border-left: 1px solid;
        border-top: 1px solid;
    }

    .td-not-border-left {
        border-bottom: 1px solid;
        border-top: 1px solid;
        border-right: 1px solid;
    }

    .td-border-top {
        border-top: 1px solid;
    }

    .td-border-bottom {
        border-bottom: 1px solid;
    }

    .td-border-left {
        border-left: 1px solid;
    }

    .td-border-right {
        border-right: 1px solid;
    }

    .bg-blue {
        background-color: #DDEBF7;
    }

    .bg-green {
        /*background-color: #BADAC8;*/
        background-color: #DDEBF7 !important;
    }

    .bg-green2 {
        /*background-color: #BADAC8;*/
        background-color: #DDEBF7 !important;
        color: black;
        border-bottom: 1px solid;
    }

    .bg-green3 {
        /*background-color: #BADAC8;*/
        border-bottom: 1px solid;
    }

    .bg-yellow {
        background-color: #F1FD77;
    }

    .text-center {
        text-align: center;
    }

    .text-left {
        text-align: left;
    }

    .text-right {
        text-align: right;
    }

    .text-right {
        text-align: right;
    }
        
</style>

     
   
       
        <div class="contenido1">
            @php 
                $cant        = count($hematologia); 
                if($cant < 22){
                    $cant = 22;
                }
                $cant_coag   = count($coagulacion); $x_coag = 12;
                if($cant_coag < 12 ){
                    $x_coag = $x_coag - $cant_coag;
                }
                
                $cant2       = count($quimica);
                if($cant2 < 34){
                    $cant2 = 34;
                }

                
                $cant_orina  = count($orina); $x_orina = 18;
                if($cant_orina < 18 ){
                    $x_orina = $x_orina - $cant_orina;
                }
                 

                $cant3       = count($inmunologia);
                $cant4       = count($heces);
                $cant5       = count($tumorales);
                $cant6       = count($citologia);
                $cant7       = count($cardiacos);
                $cant8       = count($hormonas);
               
                $cant9          = count($farmacos);
                $cant10         = count($serologia);
                $cant11         = count($biologia);
            @endphp
            <div class="outer-box">
                <div class="bg-purple"><b>HEMATOLÓGIA</b></div>
               
                @foreach($hematologia as $hm)
                <div class="inner-box">{{$hm->nombre}}</div>
                <div class="inner-box2"><input type="checkbox" id="{{$hm->id}}" name="{{$hm->id}}" value="{{$hm->id}}" onclick="cargar_examen('{{$hm->id}}')" @if($detalles->where('id_examen',$hm->id)->first() != null) checked @endif/></div>
                <div class="div_clear"></div>
                @endforeach
              
                <div class="div_clear"></div>
                <div class="bg-purple"><b>INMUNOLOGÍA / INFECCIOSAS</b></div>
                @foreach($inmunologia as $im) 
                <div class="inner-box">{{$im->nombre}}</div>
                <div class="inner-box2"><input type="checkbox" id="{{$im->id}}" value="{{$im->id}}" onclick="cargar_examen('{{$im->id}}')" @if($detalles->where('id_examen',$im->id)->first() != null) checked @endif /></div>
                <div class="div_clear"></div>
                @endforeach
                
                <div class="div_clear"></div>
                <div class="bg-purple"><b>MARCADORES CARDIACOS/VASCULARES</b></div>
                @foreach($cardiacos as $ca) 
                <div class="inner-box">{{$ca->nombre}}</div>
                <div class="inner-box2"><input type="checkbox" id="{{$ca->id}}" value="{{$ca->id}}" onclick="cargar_examen('{{$ca->id}}')" @if($detalles->where('id_examen',$ca->id)->first() != null) checked @endif /></div>
                <div class="div_clear"></div>
                @endforeach
              
                <div class="div_clear"></div>
                <div class="bg-purple"><b>MARCADORES TUMORALES</b></div>
                @foreach($tumorales as $tu) 
                    <div class="inner-box">{{$tu->nombre}}</div>
                    <div class="inner-box2"><input type="checkbox" id="{{$tu->id}}" value="{{$tu->id}}" onclick="cargar_examen('{{$tu->id}}')" @if($detalles->where('id_examen',$tu->id)->first() != null) checked @endif /></div>
                    <div class="div_clear"></div>
                @endforeach
            
                <div class="div_clear"></div>
                <div class="bg-purple"><b>NIVELES DE FÁRMACOS TERAPÉUTICAS</b></div>
                @foreach($farmacos as $fa) 
                    <div class="inner-box">{{$fa->nombre}}</div>
                    <div class="inner-box2"><input type="checkbox" id="{{$fa->id}}" value="{{$fa->id}}" onclick="cargar_examen('{{$fa->id}}')" @if($detalles->where('id_examen',$fa->id)->first() != null) checked @endif /></div>  
                    <div class="div_clear"></div>
                @endforeach
                <div class="div_clear"></div>
               
            </div>
            <div class="outer-box2">
                <div class="bg-purple"><b>COAGULACIÓN Y HEMOSTASIA</b></div>
                @foreach($coagulacion as $value) 
                <div class="inner-box21">{{$value->nombre}}</div>
                <div class="inner-box22"><input type="checkbox" id="{{$value->id}}" value="{{$value->id}}" onclick="cargar_examen('{{$value->id}}')" @if($detalles->where('id_examen',$value->id)->first() != null) checked @endif /></div>
                <div class="div_clear"></div>   
                @endforeach

                <div class="div_clear"></div> 
                <div class="bg-purple"><b>ORINA</b></div>
                @foreach($orina as $value) 
                <div class="inner-box21">{{$value->nombre}}</div>
                <div class="inner-box22"><input type="checkbox" id="{{$value->id}}" value="{{$value->id}}" onclick="cargar_examen('{{$value->id}}')" @if($detalles->where('id_examen',$value->id)->first() != null) checked @endif /></div>
                <div class="div_clear"></div>   
                @endforeach
             
              
                <div class="div_clear"></div> 
                <div class="bg-purple"><b>INMUNOSUPRESORES</b></div>
                @foreach($inmunosupresor as $value) 
                <div class="inner-box21">{{$value->nombre}}</div>
                <div class="inner-box22"><input type="checkbox" id="{{$value->id}}" value="{{$value->id}}" onclick="cargar_examen('{{$value->id}}')" @if($detalles->where('id_examen',$value->id)->first() != null) checked @endif /></div>
                <div class="div_clear"></div>   
                @endforeach
               
                <div class="div_clear"></div> 
                <div class="bg-purple"><b>GASES Y ELECTROLITOS</b></div>
                @foreach($gases as $value) 
                <div class="inner-box21">{{$value->nombre}}</div>
                <div class="inner-box22"><input type="checkbox" id="{{$value->id}}" value="{{$value->id}}" onclick="cargar_examen('{{$value->id}}')" @if($detalles->where('id_examen',$value->id)->first() != null) checked @endif /></div>
                <div class="div_clear"></div>   
                @endforeach
               
                <div class="div_clear"></div> 
                <div class="bg-purple"><b>MEDICINA TRANSFUSIONAL</b></div>
                @foreach($trasfusional as $value) 
                <div class="inner-box21">{{$value->nombre}}</div>
                <div class="inner-box22"><input type="checkbox" id="{{$value->id}}" value="{{$value->id}}" onclick="cargar_examen('{{$value->id}}')" @if($detalles->where('id_examen',$value->id)->first() != null) checked @endif /></div>
                <div class="div_clear"></div>   
                @endforeach
              
                <div class="bg-purple"><b>HORMONAS</b></div>
                @foreach($hormonas as $ho) 
                <div class="inner-box">{{$ho->nombre}}</div>
                <div class="inner-box2"><input type="checkbox" id="{{$ho->id}}" value="{{$ho->id}}" onclick="cargar_examen('{{$ho->id}}')" @if($detalles->where('id_examen',$ho->id)->first() != null) checked @endif /></div>
                <div class="div_clear"></div>
                @endforeach
                <div class="div_clear"></div>   
                <div class="div_clear"></div><div class="bg-purple"><b>MICROBIOLOGÍA</b></div>
                @foreach($biologia as $bi) 
                <div class="inner-box">{{$bi->nombre}}</div>
                <div class="inner-box2"><input type="checkbox" id="{{$bi->id}}" value="{{$bi->id}}" onclick="cargar_examen('{{$bi->id}}')" @if($detalles->where('id_examen',$bi->id)->first() != null) checked @endif /></div>  
                <div class="div_clear"></div>
                @endforeach
           
                <div class="div_clear"></div>

                <div class="bg-purple"><b>SEROLOGÍA</b></div>
                @foreach($serologia as $se) 
                    <div class="inner-box">{{$se->nombre}}</div>
                    <div class="inner-box2"><input type="checkbox" id="{{$se->id}}" value="{{$se->id}}" onclick="cargar_examen('{{$se->id}}')" @if($detalles->where('id_examen',$se->id)->first() != null) checked @endif /></div>
                    <div class="div_clear"></div>
                @endforeach
                <div class="div_clear"></div>
               
                
            </div>

            <div class="outer-box3">
                <div class="bg-purple"><b>QUÍMICA SANGUÍNEA</b></div>
                @foreach($quimica as $qu)
                    <div class="inner-box">{{$qu->nombre}}</div>
                    <div class="inner-box2"><input type="checkbox" id="{{$qu->id}}" value="{{$qu->id}}" onclick="cargar_examen('{{$qu->id}}')" @if($detalles->where('id_examen',$qu->id)->first() != null) checked @endif /></div> 
                    <div class="div_clear"></div>
                @endforeach
          
                <div class="div_clear"></div>
                <div class="bg-purple"><b>HECES</b></div>
                @foreach($heces as $he) 
                    <div class="inner-box">{{$he->nombre}}</div>
                    <div class="inner-box2"><input type="checkbox" id="{{$he->id}}" value="{{$he->id}}" onclick="cargar_examen('{{$he->id}}')" @if($detalles->where('id_examen',$he->id)->first() != null) checked @endif /></div> 
                    <div class="div_clear"></div>
                @endforeach
                <div class="div_clear"></div>
               
                <div class="bg-purple"><b>CITOQUÍMICO Y BACTERIOLÓGICO DE LÍQUIDOS</b></div>
                @foreach($citologia as $ci) 
                    <div class="inner-box">{{$ci->nombre}} </div>
                    <div class="inner-box2"><input type="checkbox" id="{{$ci->id}}" value="{{$ci->id}}" onclick="cargar_examen('{{$ci->id}}')" @if($detalles->where('id_examen',$ci->id)->first() != null) checked @endif /></div>
                    <div class="div_clear"></div>
                @endforeach
                <div class="div_clear"></div>
        
            </div> 

            <div class="bg-purple" style="border-bottom: 1px solid black;"><b>A. VIH / ITS</b></div>
            <div class="row" style="margin: 0;">
                @foreach($vih as $val)
                <div class="col-md-3 bg-green2">{{$val->nombre}}</div>
                <div class="col-md-1 bg-green3"><input type="checkbox" id="{{$val->id}}" value="{{$val->id}}" onclick="cargar_examen('{{$val->id}}')" @if($detalles->where('id_examen',$val->id)->first() != null) checked @endif /></div>
                @endforeach
            </div>
        </div>

       
        
        
     
<script type="text/javascript">

    function cargar_examen(id_examen){
        console.log($('#'+id_examen));
        if( $('#'+id_examen).is(':checked') ) {
            guardar_examen(id_examen);
        }else{
            eliminar_examen(id_examen);
        }
    }

    function guardar_examen(id_examen){

        $.ajax({
            async: true,
            type: "GET",
            url: "{{url('vt_ptv_laboratorio_iess/crear')}}/"+"{{$orden->id}}/"+id_examen,
            data: "",
            datatype: "html",
            success: function(datahtml){
                

            },
            error:  function(){
                alert('error al cargar');
            }
        }); 

    }

    function eliminar_examen(id_examen){
        
        $.ajax({
            async: true,
            type: "GET",
            url: "{{url('vt_ptv_laboratorio_iess/eliminar')}}/"+"{{$orden->id}}/"+id_examen,
            data: "",
            datatype: "html",
            success: function(datahtml){
                

            },
            error:  function(){
                alert('error al cargar');
            }
        }); 

    }
</script> 
                        
                     