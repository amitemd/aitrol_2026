@extends('contable.ventas.base')
@section('action-content')

<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('wfact_venta.Contable')}}</a></li>
            <li class="breadcrumb-item"><a href="#">{{trans('wfact_venta.Ventas')}}</a></li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header">
            <div class="row head-title">
                <div class="col-md-12 cabecera">
                    <label class="color_texto" for="title">Asignar Egreso</label>
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <form method="POST" id="form_egreso">
                    {{ csrf_field() }}
                    <div class="col-md-12">
                        <div class="col-md-3">
                            <label for="title">Egresos</label>
                        </div>
                    </div>
                    <div class="col-md-12" style="text-align: center; margin-top: 10px; display: flex;   justify-content: center; gap: 63px;">
                        <p style="font-weight: 700; text-decoration: underline;">Puede seleccionar uno o mas egresos</p>
                        <button onclick="agregarFila()" class="btn btn-success" id="btn_agregar" type="button">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                        </button>
                    </div>
                    <input type="hidden" name="id_venta" value="{{$id_venta}}">
                    <input type="hidden" id="contador" name="contador" value="0">
                    <div class="col-md-12" id="container_egresos">

                    </div>
                    <div class="col-md-12" style="display: flex; justify-content: center; margin-top: 15px;">
                        <a onclick="guardar()" class="btn btn-primary" id="btn_guardar" name="btn_guardar">Guardar</a>
                    </div>
                </form>

            </div>
        </div>
</section>
<script>
    window.onload = () => {
        agregarFila()
        // setTimeout(() => {
        //     $('.select2_cuentas').select2({
        //         tags: false
        //     });
        // }, 500);
    }

    function guardar() {
        $.ajax({
            type: 'post',
            url: "{{route('ventas.guardar_asignar_egreso')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $('#form_egreso').serialize(),
            success: function(data) {
                alertas(data.status, `${data.status.toUpperCase()}...`, data.msj)
                if (data.status == "success") {
                    setTimeout(function() {
                        window.location.href = "{{route('venta_index')}}";
                    }, 1500)
                } else {
                    btn_guardar.style.display = 'initial'
                }
            },
            error: function(data) {
                btn_guardar.style.display = 'initial'
            }
        });
    }

    const agregarFila = () => {
        let contador = document.getElementById("contador").value;
        $("#container_egresos").append(crearFila(contador));
        document.getElementById("contador").value = parseInt(contador) + 1;
         $('.select2_cuentas').select2({
            tags: false
        });
    }

    const crearFila = (contador) => `
    <div id="fila_${contador}" class="col-md-12" style="display: flex; justify-content: center; margin-top: 10px;">
        <div class="col-md-4">
            <select name="id_egreso[]" id="id_egreso" class="form-control select2_cuentas egreso" style="">
                <option value="">Seleccione egreso...</option>
                @foreach ($egresos as $egreso)
                <option value="{{$egreso->id}}">{{$egreso->observacion}}</option>
                @endforeach
            </select>
        </div>
    </div>`
</script>
@endsection