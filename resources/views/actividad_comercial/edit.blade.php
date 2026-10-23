@extends('trabajo_campo.base')
@section('action-content')
@php
$fecha1 = date('Y-m-d\TH:i', strtotime($datos->fecha_desde));
if(!is_null($datos->fecha_hasta)){
$fecha2 = date('Y-m-d\TH:i', strtotime($datos->fecha_hasta));
}else{
$fecha2= date('Y-m-d\TH:i');
}


@endphp
<div class="box">
    <div class="box-header">
        <div class="col-md-10">
            <h5><b>{{trans('tecnicof.editfieldwork')}}</b></h5>
        </div>
        <div class="col-md-2 text-right">
            <button type="button" onclick="goBack()" class="btn btn-danger">
                <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('tecnicof.returnto')}}
            </button>
        </div>
    </div>
    <div class="separator"></div>
    <div class="box-body">
        <div class="form-group col-md-6">
            <label for="fecha" class="col-md-4 texto">{{trans('tecnicof.from')}}</label>
            <div class="col-md-8">
                <input id="fechaInicio" name="fechaInicio" type="datetime-local" value="{{$fecha1}}" class="form-control" placeholder="{{trans('turnerof.fecha_ini')}}" readonly>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label for="fecha" class="col-md-4 texto">{{trans('tecnicof.to')}}</label>
            <div class="col-md-8">
                <input id="fechaFin" name="fechaFin" type="datetime-local" value="{{$fecha2}}" class="form-control" placeholder="{{trans('turnerof.fecha_fin')}}" autofocus>
            </div>
        </div>

        <div class="form-group col-md-12">
            <label for="fecha" class="col-md-2 texto">{{trans('tecnicof.location')}}</label>
            <div class="col-md-10">
                <input id="lugar" name="lugar" type="text" class="form-control" readonly placeholder="{{trans('tecnicof.location')}}" value="{{$datos->lugar}}">
            </div>
        </div>
        <div class="form-group col-md-12">
            <label for="fecha" class="col-md-2 texto">{{trans('tecnicof.activities')}}</label>
            <div class="col-md-10">
                <textarea id="obs" name="obs" type="text" class="form-control" placeholder="{{trans('tecnicof.activities')}}" readonly>"{{$datos->observaciones}} </textarea>
            </div>
        </div>
        <div class="form-group col-md-12">
            <div class="text-center">
                <button class="btn btn-primary" type="button" onclick="edit()">
                    {{trans('tecnicof.edit')}}
                </button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
    function goBack() {
        window.history.back()
    }

    function edit() {

        if (document.getElementById("fechaFin").value == '') {
            Swal.fire({
                position: 'top-center',
                icon: 'error',
                title: `{{trans('turnerof.fechahastanopuedevacio')}}`,
                showConfirmButton: false,
                timer: 1500
            });



        } else {

            $.ajax({
                url: "{{route('trabajo_campo_edit_form')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: {
                    'fecha_desde': document.getElementById("fechaInicio").value,
                    'fecha_hasta': document.getElementById("fechaFin").value,
                    'obs': document.getElementById("obs").value,
                    'id': '{{$datos->id}}'
                },
                type: 'POST',
                dataType: 'json',
                success: function(data) {

                    if (data == 'ok') {

                        Swal.fire({
                            position: 'top-center',
                            icon: 'success',
                            title: `{{trans('turnerof.ediexito')}}`,
                            showConfirmButton: false,
                            timer: 1500
                        });

                        location.href = "{{route('trabajo_campo_index')}}";
                    }

                },
                error: function(xhr, status) {
                    alert(`{{trans('laboratorio.existioproblema')}}`);
                },
            });
        }





    }
</script>

@endsection