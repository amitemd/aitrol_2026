@extends('layouts.app-template')
@section('content')


<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Mono:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">


<style type="text/css">
    .color_rojo {
        font-size: 15pt;
        font-weight: bold;
        color: red;
    }

    * {
        font-family: 'Poppins', sans-serif;
    }
</style>

<div class="content-wrapper cnt_wrapper">
    <!-- Content Header (Page header) -->
    @php
    $empresa = Sis_medico\Empresa::find(Session::get('id_empresa'));
    @endphp
    <div>
        @if($empresa->logo!=null)
        <img src="{{asset('/logo').'/'.$empresa->logo}}" style="width:100px;height: 40px; margin-left: 11px;">
        @endif
        <span class="color_rojo">
            @if(isset($empresa)) {{$empresa->id}} {{$empresa->nombrecomercial}} @else GASTROCLINICA @endif
        </span>
        <section class="content">
            <div class="box">
                @yield('action-content')
            </div>
        </section>
    </div>
    <!-- /.content -->
</div>
@endsection