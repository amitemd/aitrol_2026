<!DOCTYPE html>
<html>

<head>
    <title>{{trans('newEncuestas.encuesta')}}</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <link rel="stylesheet" href="{{ asset('/css/jquery-ui.css') }}" type="text/css" media="all">
    <link href="{{ asset('/css/wickedpicker2.css') }}" rel="stylesheet" type='text/css' media="all" />
    <!--webfonts-->
    <link href="//fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">
    <link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("/bower_components/AdminLTE/dist/css/skins/_all-skins.min.css")}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/app-template.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/css/dropzone.css')}}">
    <script src="{{ asset ("/hc4/js/jquery.js") }}" type="text/javascript"></script>
    <link href="{{ asset('/css/icheck/all.css') }}" rel="stylesheet">
    <script src="{{ asset('/js/icheck.js') }}"></script>
    <script src="{{ asset ("/plugins/sweet_alert/sweetalert.min.js") }}"></script>
    <style type="text/css">
        h3 {
            border-bottom: none !important;
            text-align: center;

        }

        .centrado {
            width: 200px;
        }

        h1 {
            margin: 0 auto !important;
        }

        @import url('https://fonts.googleapis.com/css?family=Roboto');

        body,
        html {
            display: grid;
            height: 100%;
            width: 100%;
            background-color: rgb(242, 242, 242);
            font-family: 'Roboto', sans-serif;
            color: rgba(0, 0, 16, 0.8);
            font-weight: 700;
            padding: 0;
            margin: 0;
        }

        a:link,
        a:visited,
        a:hover,
        a:active {
            color: rgba(0, 0, 16, 0.8);
            text-decoration: none;
        }

        a:hover,
        a:active {
            border-bottom: 0.1em solid rgba(0, 0, 16, 0.8);
            color: rgba(0, 0, 16, 0.8);
            text-decoration: none;
        }

        span {
            color: rgba(0, 0, 16, 0.4);
            font-size: 70%;
        }

        header {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            margin: auto;
            width: 34.6rem;
        }

        header h1 {
            font-size: 2.8em;
        }

        .card {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            margin: auto;
            -webkit-box-shadow: 0 0.5rem 1rem rgba(0, 0, 16, 0.19), 0 0.3rem 0.3rem rgba(0, 0, 16, 0.23);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 16, 0.19), 0 0.3rem 0.3rem rgba(0, 0, 16, 0.23);
            background-color: rgb(255, 255, 255);
            padding: 0.8rem;
            width: 33rem;
        }

        .rating-container {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-pack: justify;
            -ms-flex-pack: justify;
            justify-content: space-between;
            padding: 0.4rem 0.8rem;
            width: 100%;
        }

        .rating-text p {
            color: rgba(0, 0, 16, 0.8);
            font-size: 1.3rem;
            padding: 0.3rem;
        }

        .rating {
            background-color: rgba(0, 0, 16, 0.8);
            padding: 0.4rem 0.4rem 0.1rem 0.4rem;
            border-radius: 2.2rem;
        }

        svg {
            fill: rgb(178, 178, 178);
            height: 3.6rem;
            width: 3.6rem;
            margin: 0.2rem;
        }

        .rating-form-2 svg {
            height: 3rem;
            width: 3rem;
            margin: 0.5rem;
        }

        #radios label {
            position: relative;
        }

        input[type="radio"] {
            position: absolute;
            opacity: 0;
        }

        input[type="radio"]+svg {
            -webkit-transition: all 0.2s;
            transition: all 0.2s;
        }

        input+svg {
            cursor: pointer;
        }

        input[class="super-happy"]:hover+svg,
        input[class="super-happy"]:checked+svg,
        input[class="super-happy"]:focus+svg {
            fill: rgb(22, 193, 40);
        }


        input[class="neutral"]:hover+svg,
        input[class="neutral"]:checked+svg,
        input[class="neutral"]:focus+svg {
            fill: rgb(232, 214, 0);
        }



        input[class="sad"]:hover+svg,
        input[class="sad"]:checked+svg,
        input[class="sad"]:focus+svg {
            fill: rgb(230, 31, 31);
        }

        input[class="yes"]:hover+svg,
        input[class="yes"]:checked+svg,
        input[class="yes"]:focus+svg {
            fill: rgb(29, 241, 52);
        }

        input[class="no"]:hover+svg,
        input[class="no"]:checked+svg,
        input[class="no"]:focus+svg {
            fill: rgb(230, 31, 31);
        }


        footer {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-pack: end;
            -ms-flex-pack: end;
            justify-content: flex-end;
            text-align: right;
            width: 34.6rem;
            margin: auto;
        }

        footer p {
            font-size: 1.3em;
        }

        @media screen and (max-width: 650px) and (max-height: 700px) {

            body,
            html {
                font-size: 0.7rem;
            }

            header h1 {
                font-size: 4em;
            }

            footer p {
                font-size: 2em;
            }
        }

        @media screen and (max-height: 700px) {

            body,
            html {
                font-size: 0.7rem;
                height: 100%;
            }

            header h1 {
                font-size: 4em;
            }

            footer p {
                font-size: 2em;
            }
        }

        @media screen and (max-width: 650px) {

            body,
            html {
                font-size: 0.7rem;
            }

            header h1 {
                font-size: 4em;
            }

            footer p {
                font-size: 2em;
            }
        }

        @media screen and (max-width: 450px) and (max-height: 550px) {

            body,
            html {
                font-size: 0.6rem;
            }

            header h1 {
                font-size: 4.6em;
            }

            footer p {
                font-size: 3em;
            }
        }

        @media screen and (max-height: 550px) {

            body,
            html {
                font-size: 0.6rem;
            }

            header h1 {
                font-size: 4.6em;
            }

            footer p {
                font-size: 3em;
            }
        }

        @media screen and (max-width: 450px) {

            body,
            html {
                font-size: 0.6rem;
            }

            header h1 {
                font-size: 4.6em;
            }

            footer p {
                font-size: 3em;
            }
        }

        @media screen and (max-width: 400px) and (max-height: 500px) {

            body,
            html {
                height: 100%;
                width: 100%;
            }
        }

        @media screen and (max-height: 500px) {

            body,
            html {
                height: 500px;
            }
        }

        @media screen and (max-width: 400px) {

            body,
            html {
                width: 400px;
            }

            .boton:hover {
                cursor: pointer;
            }


        }

       
    </style>
</head>


<body>
    <div class="box" style="background-color: #F8F9FA;z-index: 3;width: 100%; position: relative;">
        <div class="box-header" style="padding: 15px;">
            <div class="row">
                <table style="border: 1px solid;width:100%!important;">
                    <thead>
                        <th width="30%">
                            @if(!is_null($empresa->logo))
                            <!-- sin logo hasta despues -->
                            @endif
                        </th>
                        <th width="40%">
                            {{$encuesta->descripcion}}
                        </th>
                        <th width="30%">
                            VERS. 0.4
                        </th>
                    </thead>
                </table>
            </div>
        </div>
        <div class="box-body" style="padding: 15px;">
            <form class="form-vertical" id="form_guardar">
                {{ csrf_field() }}
                <input type="hidden" name="id_master" value="{{$id}}">
                <input type="hidden" name="ip_cliente" id="ip_cliente">
                <div class="row">
                    <div class="col-md-12" style="border: 1px solid !important; font-size: 14px !important; padding: 5px;">
                        <label><?php echo $encuesta->observacion; ?></label>
                    </div>
                </div>
                <br>

                <div class="form-group row">
                    <div class="form-group col-md-6">
                        <label style="color: black">{{ trans('newEncuestas.CedulaPaciente')}}:</label>
                    </div>
                    <div class="form-group col-md-6">
                        <input type="text" style="width: 30%;border: 1px solid black;" class="form-control" name="cedula" maxlength="13" required>
                    </div>
                    <br>
                </div>
                <div class="row">

                    @foreach($arreglo as $value)
                    @php
                   
                    $cuenta = count($value);
                    @endphp
                    @if($cuenta >0)
                    @php
                    $formato = $value[0]->grupopregunta->tipo_calificacion;

                    @endphp

                    @if($formato == 1)
                    <div class="col-md-12" style="border: 1px solid !important; padding: 10px;">
                        @foreach($value as $preg)
                        <span style="color: black !important;font-size: 16px;">{{$preg->nombre}}</span><br>
                        <textarea name="{{$preg->id}}" style="width: 99%; height: 70px;"> </textarea>
                        <br><br>
                        @endforeach
                    </div>
                    @endif

                    @if($formato ==2)
                    <div class="col-md-12" style="border: 1px solid !important; padding: 10px;">
                        @foreach($value as $preg)
                        <table style="border: 1px solid;width:100%!important;">
                            <tbody>
                                <tr>
                                    <td width="20%" style="vertical-align: middle; border-color: black; font-size: 13px;">{{$preg->nombre}}</td>
                                    <td></td><td></td><td></td><td></td>
                                </tr>
                                <tr>    
                                    @foreach($tiempos as $tp)
                                    <td width="20%" style="text-align: center; border-color: black;vertical-align: middle;font-size: 13px;">
                                        {{$tp}}min <input type="radio" class="check2" name="{{$preg->id}}" value="{{$tp}}">
                                    </td>
                                    @endforeach
                                    <td width="20%" style="text-align: center; border-color: black;vertical-align: middle; font-size: 13px;">{{trans('encuestas.otros')}}<input type="radio" class="check2" name="{{$preg->id}}" value="otros"><input type="number" class="check2" name="val2{{$preg->id}}"></td>
                                </tr>
                            </tbody>
                        </table>
                        @endforeach
                    </div>
                    @endif

                    @if($formato == 3)
                    <div class="col-md-12" style="border: 1px solid !important; padding: 10px;">
                        <table style="border: 1px solid;width:100%!important;">
                            <thead>
                                <tr style="font-size: 13px;text-align: center;">
                                    <th width="30%" style=" font-size: 16px; vertical-align: middle;border-color:black;">{{trans('newEncuestas.criteriosdeevaluacion')}}</th>
                                    <th width="5%" style="padding: 0 14px;border-color:black;">{{trans('newEncuestas.buena')}}</th>
                                    <th width="5%" style="padding: 0 14px;border-color:black;">{{trans('newEncuestas.regular')}}</th>
                                    <th width="5%" style="padding: 0 14px;border-color:black;">{{trans('newEncuestas.mala')}}<br></th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($value as $preg)
                                <tr role="row" class="odd">
                                    <td style="border-color:black;font-size: 13px; vertical-align: middle;">{{$preg->nombre}}</td>
                                    <td style="text-align: center;">
                                        <label for="super-happy{{$preg->id}}">
                                            <input type="radio" id="super-happy{{$preg->id}}" name="{{$preg->id}}" class="super-happy" value="10" />
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-emoji-smile" viewBox="0 0 16 16">
                                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                                <path d="M4.285 9.567a.5.5 0 0 1 .683.183A3.498 3.498 0 0 0 8 11.5a3.498 3.498 0 0 0 3.032-1.75.5.5 0 1 1 .866.5A4.498 4.498 0 0 1 8 12.5a4.498 4.498 0 0 1-3.898-2.25.5.5 0 0 1 .183-.683zM7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5zm4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5z" />
                                            </svg>
                                        </label>
                                    </td>
                                    <td style="text-align: center;">
                                        <label for="neutral{{$preg->id}}">
                                            <input type="radio" class="neutral" name="{{$preg->id}}" id="neutral{{$preg->id}}" value="5" />
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-emoji-expressionless" viewBox="0 0 16 16">
                                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                                <path d="M4 10.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5zm5 0a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z" />
                                            </svg>
                                        </label>
                                    </td>
                                    <td style="text-align: center;">
                                        <label for="sad{{$preg->id}}">
                                            <input type="radio" class="sad" name="{{$preg->id}}" id="sad{{$preg->id}}" value="0" />
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-emoji-frown" viewBox="0 0 16 16">
                                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                                <path d="M4.285 12.433a.5.5 0 0 0 .683-.183A3.498 3.498 0 0 1 8 10.5c1.295 0 2.426.703 3.032 1.75a.5.5 0 0 0 .866-.5A4.498 4.498 0 0 0 8 9.5a4.5 4.5 0 0 0-3.898 2.25.5.5 0 0 0 .183.683zM7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5zm4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5z" />
                                            </svg>
                                        </label>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                    @if($formato == 4)
                    <div class="col-md-12" style="border: 1px solid !important; padding: 10px;">
                        <table style="border: 1px solid;width:100%!important;">
                            <thead>
                                <tr>
                                    <th width="30%"></th>
                                    <th width="5%">{{trans('newEncuestas.si')}}</th>
                                    <th width="5%">{{trans('newEncuestas.no')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($value as $preg)
                                <tr>
                                    <td style="border-color:black;font-size: 13px; vertical-align: middle;">{{$preg->nombre}}</td>
                                    <td style="text-align: center;">
                                        <label for="yes{{$preg->id}}">
                                            <input type="radio" class="yes" name="{{$preg->id}}" id="yes{{$preg->id}}" value="1" />
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                                                <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z" />
                                            </svg>
                                        </label>
                                    </td>
                                    <td style="text-align: center;">
                                        <label for="no{{$preg->id}}">
                                            <input type="radio" class="no" name="{{$preg->id}}" id="no{{$preg->id}}" value="0" />
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                                            </svg>
                                        </label>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif


                    @endif
                    @endforeach

                </div>
            </form>
            <br>
            <div class="row" style="margin-bottom: 50px;">
                <center>
                <button class="boton btn btn-primary btn-lg" style="width: 12%;z-index: 9999;position: static; padding: 10px;color: white;border-radius: 20px;" id="boton_enviar" onclick="guardar()">{{trans('newEncuestas.guardar')}}</button>
                <span style="height: 30px;">&nbsp;</span>
                </center>
            </div>
        </div>
        
    </div>

    <!-- <div class="w3lsfooteragileits" style="position: relative;">
		<p style="color: black;z-index: 2; position: relative;"> &nbsp; </p>
		<img class="centrado" style="position: absolute; bottom: -150px; width: 100%;z-index: 99;" src="{{asset('/imagenes/footer.png')}}">

	</div> -->
    
</body>

</html>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('.check2').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
            increaseArea: '20%' // optional
        });
    });

    function alertas(icon, title, msj) {
        Swal.fire({
            icon: icon,
            title: title,
            html: msj
        })
    }

    function validar_campos() {
        let campo = document.querySelectorAll(".validar")
        let validar = false;

        for (let i = 0; i < campo.length; i++) {

            if (campo[i].value.trim() <= 0) {
                campo[i].style.border = '2px solid #CD6155';
                campo[i].style.borderRadius = '4px';
                validar = true;
            } else {
                campo[i].style.border = '1px solid #d2d6de';
                campo[i].style.borderRadius = '0px';
            }
        }
        return validar;
    }

    async function guardar(){
        $('#boton_enviar').attr("disabled", true);
        if (!validar_campos()) {

            const request = await fetch("https://ipinfo.io/json?token=b001b89ccff295");
			const jsonResponse = await request.json();
			$("#ip_cliente").val(jsonResponse.ip);
			$.ajax({
				type: 'post',
				url: "{{ route('sinlogin.encuestas_nuevo_guardar')}}",
				datatype: 'json',
				data: $("#form_guardar").serialize(),
				success: function(data) {
					alertas(data.respuesta, `${data.respuesta.toUpperCase()}...`, data.msj)
                    if (data.respuesta == "success") {
                       
                        setTimeout(function() {
                            location.reload();
                        }, 1500)
                    }else{
                        $('#boton_enviar').attr("disabled", false);
                    }
				},
				error: function(data) {
					$('#boton_enviar').removeAttr("disabled");
					console.log(data);
					

				}
			})
        }else {
            
            $('#boton_enviar').prop("disabled", false);
            alertas('error', 'ERROR', `{{trans('newEncuestas.camposvacios')}}`)
        }
    }
</script>