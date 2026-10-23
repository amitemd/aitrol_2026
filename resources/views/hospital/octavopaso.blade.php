<?php
date_default_timezone_set('America/Guayaquil');
$fecha = date("Y-m-d");
?>
<style>
    canvas {
        border: 1px solid #000;
        cursor: crosshair
    }

    .arriba {
        margin-top: 4px;
        text-align: left;
    }
    .bordernone{
        border: 3px solid black;
    }
    .cuadre{
        max-height: 100px!important;
    }
</style>
<div class="card" id="octavopaso">
    <div class="card-header bg bg-primary">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-1">
                </div>
                <div class="col-md-9">
                    <label style="color: white;" class="control_label">{{trans('paso2.ExamenFisicoyDiagnostico')}}</label>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary btn-xs" type="button" onclick="regresar()"> <i class="fa fa-remove"></i> </button>
                </div>
            </div>


        </div>
    </div>
    <div class="card-body">
        <div class="row" style="padding-top: 10px; text-align:center;">
            <div class="col-md-7">
                <canvas id="canvas" width=400 height=500></canvas>
            </div>
            <div class="col-md-5">
                <div class="row">
                    <!--                         <option value="239" style="background-color: blue;">Azul</option>
                        <option value="56" style="background-color: yelow;">Amarillo</option>
                        <option value="0" style="background-color: black;">Negro</option>
                        <option value="100" style="background-color: black;">Olivo</option>
                        <option value="180" style="background-color: black;">Teal</option>
                        <option value="300" style="background-color: black;">Fuchsia</option>
                        <option value="16" style="background-color: black;">Coral</option>
                        <option value="275" style="background-color: black;">Indigo</option>
                        <option value="120"></option> -->
                    <div class="col-md-6" style="text-align: left;">

                        <label>{{trans('paso2.Limpiar')}}</label>

                    </div>
                    <div class="col-md-6">

                        <button class="btn btn-danger btn-xs" type="button" onclick="remove()"> <i class="fa fa-trash"></i> </button>

                    </div>
                   <!--  <label>{{trans('paso2.Escoger Color')}}</label>
                    <select class="form-control" name="color" onchange="a(this)" id="changeColor">
                        <option value="">{{trans('paso2.Seleccione')}}</option>
                        @foreach($colores as $value)

                        <option value="{{$value->hsl}}" style="background-color: {{$value->hex}};">{{$value->nombre}}</option>


                        @endforeach
                    </select> -->
                    <div class="col-md-12">
                        <label>{{trans('paso2.Clasificaciones')}}</label>
                    </div>
                    @foreach($colores as $colores)
                    <div class="col-md-2 arriba" >
                        {{$colores->id}} .-
                    </div>
                    <div class="col-md-2 arriba cuadre" onclick="a('{{$colores->hex}}',this)" style="background-color: {{$colores->hex}};">

                    </div>

                    <div class="col-md-8 arriba">
                        <label> {{$colores->tipo}} </label>
                    </div>
                    @endforeach
                </div>


            </div>
            <div class="col-md-12">
                <button class="btn btn-primary" type="button" onclick="guardar8(this)"> <i class="fa fa-save"></i> &nbsp; @if($ho==null) {{trans('paso2.Guardar')}} @else Actualizar @endif </button>
            </div>


        </div>
    </div>

</div>
<script>
    var GlobalColor = "hsl(" + (Math.random() * 360) + ", 100%, 85%)";
    var img = new Image;
    img.onload = setup;
    @if($ho != null)
    img.src = '{{asset("hc_ima")}}/{{$ho->url_imagen}}';
    @else
    img.src = "{{asset('body.png')}}";
    @endif

    function a(d,e) {
        //console.log($(d).val());
        $('.cuadre').each(function(){
            $(this).removeClass('bordernone');
        });
        if(d==0){
            //alert('aa')
            GlobalColor =d;
        }else{
            GlobalColor = d;
        }
        $(e).addClass('bordernone');
        //GlobalColor = "hsl(" + ($(d).val()) + ", 100%, 85%)";
    }

    function setup() {
        var canvas = document.querySelector("canvas"),
            ctx = canvas.getContext("2d"),
            lastPos, isDown = false;

        ctx.drawImage(this, 0, 0, canvas.width, canvas.height); // draw duck        
        ctx.lineCap = "round"; // make lines prettier
        ctx.lineWidth = 16;
        ctx.globalCompositeOperation = "multiply"; // KEY MODE HERE

        canvas.onmousedown = function(e) {
            isDown = true;
            lastPos = getPos(e);
            ctx.strokeStyle = GlobalColor;
        };
        window.onmousemove = function(e) {
            if (!isDown) return;
            var pos = getPos(e);
            ctx.beginPath();
            ctx.moveTo(lastPos.x, lastPos.y);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            lastPos = pos;
        };
        window.onmouseup = function(e) {
            isDown = false
        };

        function getPos(e) {
            var rect = canvas.getBoundingClientRect();
            return {
                x: e.clientX/0.80 - rect.left,
                y: e.clientY/0.80 - rect.top
            }
        }
    }

    function remove() {
        var canvas = document.querySelector('canvas');
        var context = canvas.getContext('2d');
        context.clearRect(0, 0, canvas.width, canvas.height);
        var img = new Image;
        img.onload = setup;
        img.src = "{{asset('body.png')}}";
    }

    function regresar() {
        console.log("add");
        $("#octavopaso").remove();
        $("#cambio8").show();
    }

    function guardar8(e) {
        var canvas = document.getElementById('canvas');
        var blob = canvas.toDataURL();
        var id = $("#solicitudGeneral").val();
        console.log(blob);
        // PREPARE FORM DATA TO SEND VIA POST
        //ar formData = new FormData();
        //formData.append('croppedImage', blob, 'sampleimage.png');
       
        canvas.toBlob(function(blob) {
            const formData = new FormData();
            formData.append('my-file', blob, 'filename.png');

            // Post via axios or other transport method
            $.ajax({
                url: "{{route('hospital.octavosave')}}?id=" + id, // upload url
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                processData: false,
                contentType: false,
                data: formData,
                success: function(data) {
                    
                    Swal.fire("Mensaje: ",`{{trans('proforma.GuardadoCorrectamente')}}`,"success");
                    $(e).attr('disabled', 'disabled');
                    octavo_paso();

                },
                error: function(xhr, status, error) {
                    alert('Error, contactase con el programador');
                }
            });
        });

    }
</script>