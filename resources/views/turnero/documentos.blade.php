<!DOCTYPE html>
<html>

<head>
    <title>Turnero</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <link rel="stylesheet" href="{{ asset('/css/jquery-ui.css') }}" type="text/css" media="all">
    <link href="{{ asset('/css/wickedpicker2.css') }}" rel="stylesheet" type='text/css' media="all" />
    <link href="{{ asset('/css/style2.css') }}" rel='stylesheet' type='text/css' />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link href="{{ asset("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css")}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("/bower_components/select2/dist/css/select2.min.css")}}" rel="stylesheet" type="text/css" />
    <!--webfonts-->
    <link href="//fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">
    <link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
      page. However, you can choose any other skin. Make sure you
      apply the skin class to the body tag so the changes take effect.
      -->

    <link href="{{ asset("/bower_components/AdminLTE/dist/css/skins/_all-skins.min.css")}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/app-template.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/css/dropzone.css')}}">
    <script type="text/javascript" src="{{ asset('/js/jquery-2.1.4.min.js') }}"></script>
    <script src="{{ asset('/js/jquery-ui.js') }}"></script>
    <link href="{{ asset('/css/icheck/all.css') }}" rel="stylesheet">
    <script src="{{ asset('/js/icheck.js') }}"></script>
    <!--//webfonts-->
    <style type="text/css">
        h3 {
            border-bottom: none !important;
            text-align: center;
            margin-bottom: 20px;
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
            background-color: #F8F9FA;
            font-family: 'Roboto', sans-serif;

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
            fill: rgb(0, 0, 0);
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



        input+svg {
            cursor: pointer;
        }

        input[class="super-happy"]:hover+svg,
        input[class="super-happy"]:checked+svg,
        input[class="super-happy"]:focus+svg {
            fill: rgb(0, 109, 217);
        }

        input[class="happy"]:hover+svg,
        input[class="happy"]:checked+svg,
        input[class="happy"]:focus+svg {
            fill: rgb(0, 204, 79);
        }

        input[class="neutral"]:hover+svg,
        input[class="neutral"]:checked+svg,
        input[class="neutral"]:focus+svg {
            fill: rgb(232, 214, 0);
        }

        input[class="sad"]:hover+svg,
        input[class="sad"]:checked+svg,
        input[class="sad"]:focus+svg {
            fill: rgb(229, 132, 0);
        }

        input[class="super-sad"]:hover+svg,
        input[class="super-sad"]:checked+svg,
        input[class="super-sad"]:focus+svg {
            fill: rgb(239, 42, 16);
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
                height: 500px;
                width: 400px;
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
        }

        .efecto {
            position: relative;
            background-color: #04AA6D;
            border: none;
            font-size: 28px;
            color: #FFFFFF;
            padding: 20px;
            width: 200px;
            text-align: center;
            -webkit-transition-duration: 0.4s;
            /* Safari */
            transition-duration: 0.4s;
            text-decoration: none;
            overflow: hidden;
            cursor: pointer;
        }

        .efecto:after {
            content: "";
            background: #90EE90;
            display: block;
            position: absolute;
            padding-top: 300%;
            padding-left: 350%;
            margin-left: -20px !important;
            margin-top: -120%;
            opacity: 0;
            transition: all 0.8s
        }

        .efecto:active:after {
            padding: 0;
            margin: 0;
            opacity: 1;
            transition: 0s
        }
    </style>
</head>

<body>
    <img class="centrado" style="position: absolute; top:0; width: 100%; z-index: 1;" src="{{asset('/imagenes/cabecera-arriba.png')}}">
    <img class="centrado" style="position: absolute; top:30px; right: 19px;z-index: 2;" src="{{asset('/imagenes/logo1.png')}}">
    <h1 style="margin: 91px 0 0 0 !important; color: black !important;z-index: 3;"></h1>
    <form method="POST" action="{{route('turnero_teclado_numerico')}}">
        {{ csrf_field() }}
        <input type="hidden" name="hospital" value="{{$hospital}}">
        <input type="hidden" name="sala" value="{{$sala}}">
        <input type="hidden" name="proc" value="{{$procedimiento}}">
        <div class="container-md">
            <div class="col-md-12">
                <div class="row" style="margin-left:35%">
                    <div class="col-md-4">
                        <button type="submit" style="height:150px;width:200px;margin-left:2%;float:left;background:#9ACFDD" value="1" name="campo" class="efecto"><span style="color: white;font-weight:bold;font-size:20px">Cedula</span></button>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" style="height:150px;width:200px;margin-left:2%;float:left;background:#9ACFDD" value="2" name="campo" class="efecto"><span style="color: white;font-weight:bold;font-size:20px">Pasaporte </span></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="w3lsfooteragileits" style="position: relative;">
        <img class="centrado" style="position: absolute; bottom: 0; width: 100%;z-index: 1;" src="{{asset('/imagenes/cabecera-abajo.png')}}">
    </div>
</body>

</html>