@extends('insumos.modulo_bodega.base')
@section('action-content')

<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Mono:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">

<style type="text/css">
* {
    font-family: 'Poppins', sans-serif;
}
</style>
<!-- Main content -->
<section class="content">

    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-9">
                <h3 class="box-title">Modulo Bodega</h3>
            </div>
        </div>

        <!-- /.box-header -->
        @foreach($modulos as $value)

        <div class="box-header">
            <div class="col-md-12">
                <h3 class="box-title"><b>{{strtoupper($value->nombre_modulo)}}</b></h3>
            </div>
        </div>
        <div class="box-body dobra">
            <div class="col-md-6">
                @foreach($value->modulo_bodega as $mod)
                <div class="row">
                    <select name="id_bodega" id="id_bodega" class="form-control select2" onchange="cambia_bodega(this,'{{$mod->id}}');">
                        <option value="">Seleccione...</option>
                        @foreach ($bodegas as $b)
                        <option @if($mod->id_bodega == $b->id) selected @endif value="{{$b->id}}">{{$b->nombre}}</option>
                        @endforeach
                    </select>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach


    </div>
</section>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    $(document).ready(function() {

        $('.select2').select2({
            tags: false
        });
    });

    function alertas(icon, title, text) {
        Swal.fire({
            icon: `${icon}`,
            title: `${title}`,
            text: `${text}`,
            showConfirmButton: false,
            timer: 1200

        })
    }

    function cambia_bodega(obj, id) {
        let id_bodega = document.getElementById('id_bodega').value;
        $.ajax({
            url: "{{route('modulobodega.store')}}",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_modulobodega': id,
                'id_bodega': obj.value,
            },
            success: function(data) {
                if (data.respuesta == 'success') {
                    alertas(data.respuesta, data.titulos, data.msj);
                } else {
                    alertas(data.respuesta, data.titulos, data.msj);
                    
                }
            },
            error: function(data) {

            }
        });
    }
</script>
@endsection