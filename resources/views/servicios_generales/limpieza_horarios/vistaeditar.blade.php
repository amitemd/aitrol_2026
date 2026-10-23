@extends('servicios_generales.limpieza_horarios.base')
@section('action-content')
@php
$fecha1 = date('Y-m-d\TH:i', strtotime($dato->frecuencia1));
$fecha2 = date('Y-m-d\TH:i', strtotime($dato->frecuencia2));
$fecha3 = date('Y-m-d\TH:i', strtotime($dato->frecuencia3));
$fecha4 = date('Y-m-d\TH:i', strtotime($dato->frecuencia4));
$fecha5 = date('Y-m-d\TH:i', strtotime($dato->frecuencia5));
$fecha6 = date('Y-m-d\TH:i', strtotime($dato->frecuencia6));
$fecha7 = date('Y-m-d\TH:i', strtotime($dato->frecuencia7));
$fecha8 = date('Y-m-d\TH:i', strtotime($dato->frecuencia8));
@endphp
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="col-md-10">

                <h4>Editar horarios</h4>

            </div>
            <div class="col-md-2">
                <button type="button" onclick="regresar()" class="btn btn-danger">Regresar</button>
            </div>
        </div>
        <div class="box-body">
            <form id="formulario" action="" class="form">
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-1">
                            <label for="">Hora 1</label>
                        </div>
                        <div class="col-md-2">
                            <input type="datetime-local" id="frecuencia1" name="frecuencia1" value="{{$fecha1}}" class="form-control">
                        </div>
                        <div class="col-md-1">
                            <label for="">Hora 2</label>
                        </div>
                        <div class="col-md-2">
                            <input type="datetime-local" id="frecuencia2" name="frecuencia2" class="form-control" value="{{$fecha2}}">
                        </div>
                        <div class="col-md-1">
                            <label for="">Hora 3</label>
                        </div>
                        <div class="col-md-2">
                            <input type="datetime-local" id="frecuencia3" name="frecuencia3" class="form-control" value="{{$fecha3}}">
                        </div>
                        <div class="col-md-1">
                            <label for="">Hora 4</label>
                        </div>
                        <div class="col-md-2">
                            <input type="datetime-local" id="frecuencia4" name="frecuencia4" class="form-control" value="{{$fecha4}}">
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-top: 2%;">
                        <div class="col-md-1">
                            <label for="">Hora 5</label>
                        </div>
                        <div class="col-md-2">
                            <input type="datetime-local" id="frecuencia5" name="frecuencia5" class="form-control" value="{{$fecha5}}">
                        </div>
                        <div class="col-md-1">
                            <label for="">Hora 6</label>
                        </div>
                        <div class="col-md-2">
                            <input type="datetime-local" id="frecuencia6" name="frecuencia6" class="form-control" value="{{$fecha6}}">
                        </div>
                        <div class="col-md-1">
                            <label for="">Hora 7</label>
                        </div>
                        <div class="col-md-2">
                            <input type="datetime-local" id="frecuencia7" class="form-control" name="frecuencia7" value="{{$fecha7}}">
                        </div>
                        <div class="col-md-1">
                            <label for="">Hora 8</label>
                        </div>
                        <div class="col-md-2">
                            <input type="datetime-local" id="frecuencia8" name="frecuencia8" class="form-control" value="{{$fecha8}}">
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-top: 2%;">
                        <div class="text-center">
                            <button type="button" onclick="editar({{$dato->id}})" class="btn btn-primary" style="margin-top:-4px;"><i class="glyphicon glyphicon-folder-open" style="margin-right:6px;"></i>EDITAR</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    async function editar(id) {
        var result = await this.editarTodo(id);
        if (await result.json() == 'ok') {
            Swal.fire({
                position: 'top-center',
                icon: 'success',
                title: 'EDITADO',
                showConfirmButton: false,
                timer: 1500
            })
            location.reload();
        }
    }
    async function editarTodo(id) {
        var cuerpo = {
            id: id,
            frecuencia8: document.getElementById("frecuencia8").value,
            frecuencia7: document.getElementById("frecuencia7").value,
            frecuencia6: document.getElementById("frecuencia6").value,
            frecuencia5: document.getElementById("frecuencia5").value,
            frecuencia4: document.getElementById("frecuencia4").value,
            frecuencia3: document.getElementById("frecuencia3").value,
            frecuencia2: document.getElementById("frecuencia2").value,
            frecuencia1: document.getElementById("frecuencia1").value,
        };
        var thisForm = document.getElementById('formulario');
        const resultado = await fetch("/sis_medico_prb/public/editar/frecuencia/completo", {
            method: 'POST',
            body: JSON.stringify(cuerpo),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
        });
        return resultado;
    }

    function regresar() {
        window.history.back();

    }
</script>

@endsection