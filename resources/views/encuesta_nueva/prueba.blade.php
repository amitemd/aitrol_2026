<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Subir foto</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('bddrnet.subirDocValidacion') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="archivo" class="col-md-4 control-label">Archivo</label>
                            <input name="id_usuario" type="hidden" id="id_usuario" value="9666666666">
                            <input name="tipo" type="hidden" id="tipo" value="1">
                            <div class="col-md-6">
                                <input id="cedula_r" type="file" class="form-control" name="cedula_r" value="" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Subir
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>




