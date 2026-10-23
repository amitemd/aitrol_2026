<?php
/*if (Auth::user()->id_tipo_usuario != 1 || (isset($usuario->new_system) && $usuario->new_system == 1)) {
    $laravelUrl = asset('dashboard');
    header("Location: {$laravelUrl}");
    exit();
}*/
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <base href="{{url('./')}}">
    <link rel="icon" type="image/png" href="{{url('v3/')}}/dist/img/AdminLTELogo.png" />
    <link REL="SHORTCUT ICON" HREF="{{url('v3/')}}/dist/img/AdminLTELogo.png" />@yield('title')@yield('css')<script src="{{url('v3/')}}/plugins/jquery/jquery.min.js"></script>
</head>

<body class="hold-transition sidebar-mini layout-fixed text-sm">
    <div class="wrapper">
        <div class="preloader flex-column justify-content-center align-items-center"><img class="animation__shake" src="{{url('v3/')}}/dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60"></div>@yield('header')@yield('menu')<div class="content-wrapper ">@yield('contenido')</div>@yield('footer')@yield('variables')<aside class="control-sidebar control-sidebar-dark"></aside>
    </div>@yield('js')
</body>

</html>