    @if(!is_null($cama_id) ||!empty($cama_id))
    @php
    $paciente = Sis_medico\Paciente::where('id',$cama_id->id_paciente)->first();
    $camilla = Sis_medico\Camilla::where('id',$cama_id->camilla)->first();
    $hospital = Sis_medico\Hospital::where('id',$cama_id->id_hospital)->first();
    @endphp
    @if($cama_id->estado_uso == 2)
    @if($cama_id->edad >= 13)

    <body @if($cama_id->nivel_riesgo >= 0 && $cama_id->nivel_riesgo <= 25)  style="background-color: green;margin-top:200px;line-height:80px;"
    @elseif($cama_id->nivel_riesgo >= 25 && $cama_id->nivel_riesgo <= 50) style="background-color: yellow;margin-top:200px;line-height:80px;"
    @elseif($cama_id->nivel_riesgo > 50) style="background-color: red;margin-top:200px;line-height:80px;"    
    @endif >
        <div id="color" style="text-align:center;">
            <h2 style="color: black;">@if($camilla == '' || is_null($camilla)) @else {{$camilla->id}} @endif</h2>
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <h2 style="font-size:40px;color:black;font-weight:bold;"><strong>Nombre del Paciente </strong>
                        <br> @if(empty($paciente->nombre1)) || (empty($paciente->nombre2)) ||
                        (empty($paciente->apellido1)) (empty($paciente->apellido2))
                        @else {{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}}
                        {{$paciente->apellido2}}@endif
                    </h2>
                    <h2 style="font-size:40px;color:blackhite;font-weight:bold;"><strong>Cédula </strong><br>
                        {{$paciente->id}}
                    </h2><br>
                </div>
            </div>
        </div>
    </body>
    @elseif($cama_id->edad >= 0 && $cama_id->edad <= 12)
    <body @if($cama_id->nivel_riesgo >= 0 && $cama_id->nivel_riesgo <= 1)  style="background-color: green;margin-top:200px;line-height:80px;"
    @elseif($cama_id->nivel_riesgo >= 2 && $cama_id->nivel_riesgo <= 4) style="background-color: yellow;margin-top:200px;line-height:80px;"
    @elseif($cama_id->nivel_riesgo > 4  && $cama_id->nivel_riesgo <= 6) style="background-color: red;margin-top:200px;line-height:80px;" @endif >
        <div id="color" style="text-align:center;">
            <h2 style="color: black;">@if($camilla == '' || is_null($camilla)) @else {{$camilla->id}} @endif</h2>
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <h2 style="font-size:40px;color:black;font-weight:bold;"><strong>Nombre del Paciente </strong>
                        <br> @if(empty($paciente->nombre1)) || (empty($paciente->nombre2)) ||
                        (empty($paciente->apellido1)) (empty($paciente->apellido2))
                        @else {{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}}
                        {{$paciente->apellido2}}@endif
                    </h2>
                    <h2 style="font-size:40px;color:black;font-weight:bold;"><strong>Cédula </strong><br>
                        {{$paciente->id}}
                    </h2><br>
                </div>
            </div>
        </div>
    </body>
    @endif

    @elseif($cama_id->estado_uso== 1)
    <body style="background-color: green">
        <div id="color">
            <div class="form-row">
                <div class="col-md-4 mb-3">
                </div>
            </div>
        </div>
    </body>
    @elseif($cama_id->estado_uso== 3)

    <body style="background-color: yellow">
        <div id="color">
            <div class="form-row">
                <div class="col-md-4 mb-3">
                </div>
            </div>
        </div>
    </body>
    @elseif($cama_id->estado_uso== 4)

    <body style="background-color: violet">
        <div id="color">
            <div class="form-row">
                <div class="col-md-4 mb-3">
                </div>
            </div>
        </div>
    </body>
    @endif
    @else
    <body style="background-color: green">
        <div id="color">
            <div class="form-row">
                <div class="col-md-4 mb-3">
                </div>
            </div>
        </div>
    </body>
    @endif
    @if(!empty($cama_id) || !is_null($cama_id))
    <input type="hidden" value="{{$cama_id->camilla}}" id="camitas">
    <input type="hidden" value="{{$cama_id->estado_uso}}" id="estado">
    @endif
    @if(empty($cama_id) && empty($cama_id))
    <input type="hidden" value="{{$id_cama}}" id="camilla">
    @endif
    <script src="{{asset('js/jquery.min.js')}}"></script>
    <script src="{{asset('js/app.js')}}"></script>
    <script type="text/javascript">
        var camitas = $("#camitas").val();
        var estado = $("#estado").val();
        var camilla_libre = $("#camilla").val();
        $(document).ready(function() {
            function actualizar() {
                $.ajax({
                    url: "{{route('actualizar_estado')}}",
                    data: {
                        'data': camitas,
                        'estado': estado,
                        'camilla': camilla_libre,
                    },
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data == "No") {
                            location.reload();
                        }
                        if (data == "Listo") {
                            location.reload();
                        }

                    },
                    error: function(xhr) {
                        //alert('Existió un problema');
                        console.log(xhr);
                    },
                });
            }
            setInterval(actualizar, 8000);
        });
    </script>