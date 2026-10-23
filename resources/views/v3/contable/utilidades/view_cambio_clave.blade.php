<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{$title}}</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ url('v3/') }}/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="{{ url('v3/') }}/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="{{ url('v3/') }}/dist/css/adminlte.min.css?v=3.2.0">

</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="{{ url('v3/admin') }}"><b><?= $info->nombre_info ?></b><?= ' V.' . $info->version_info ?></a>
        </div>
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Estás a solo un paso de tu nueva contraseña, cambia tu contraseña ahora.</p>
                <form action="{{ url('v3/') }}/utilidades/cambiarclave" method="post">
                    {{ csrf_field() }}
                    <div class="input-group mb-3">
                        <input type="password" id="clave" name="clave" class="form-control"
                            placeholder="Contraseña" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" id="clave_" name="clave_"
                            placeholder="Confirmar contraseña" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block" id="enviar">Cambiar contraseña</button>
                        </div>
                    </div>
                </form>
                <?php
                $color = '#e85454';
                if (session('mensaje') == 'Su clave fue cambiada correctamente...') {
                    $color = '#5c8e67';
                }
                if (session('mensaje') != null) {
                    echo '<br/><div class= "card text-center card-warning card-header card-danger" style="background-color:' . $color . ';color: #ffffff;">' . session('mensaje') . '</div>';
                    session(['mensaje' => '']);
                }
                ?>
            </div>
        </div>
    </div>
    <script src="{{ url('v3/') }}/plugins/jquery/jquery.min.js"></script>
    <script src="{{ url('v3/') }}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
