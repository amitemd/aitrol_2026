@extends('ticket_permiso.base')
@section('action-content')

<style>
    .stilos:focus {

        background: whitesmoke;
    }

    #estilos:hover {
        color: blanchedalmond;
    }

    th {

        text-align: center;
    }

    .hola {
        z-index: 999999 !important;
        z-index: 999999999 !important;
        z-index: 99999999999999 !important;
        position: absolute;
        top: 0px;
        left: 0px;
        float: left;
        display: block;
        min-width: 160px;
        padding: 4px 0;
        margin: 0 0 10px 25px;
        list-style: none;
        background-color: #ffffff;
        border-color: #ccc;
        border-color: rgba(0, 0, 0, 0.2);
        border-style: solid;
        border-width: 1px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
    }

    .check {
        height: 20px;
        width: 20px;
    }
</style>
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-md-10">
                    <h3 class="box-title">{{trans('tecnicof.uploaddocument')}}</h3>
                </div>
                <div class="col-md-2">
                    <input type="button" class="btn btn-danger" value="Regresar" onclick="goBack()">
                </div>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="col-md-5">
                <div class="row" style="margin-top:5px;">
                    <div class="col-md-3">
                        <label for="">1.Permiso por fallecimiento</label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" class="check" onclick="veri(this)" id="fallecimiento">
                    </div>

                    <div class="col-md-3">
                        <label for="">6.{{trans('tecnicof.medicalleave')}}</label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" class="check" onclick="veri(this)" id="medico">
                    </div>
                </div>
                <div class="row" style="margin-top:5px;">
                    <div class="col-md-3">
                        <label for="">2.{{trans('tecnicof.maternityleave')}}</label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" class="check" onclick="veri(this)" id="maternidad">
                    </div>

                    <div class="col-md-3">
                        <label for="">7.{{trans('tecnicof.personalleave')}}</label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" class="check" onclick="veri(this)" id="personal">
                    </div>
                </div>

                <div class="row" style="margin-top:5px;">
                    <div class="col-md-3">
                        <label for="">3.{{trans('tecnicof.paternityleave')}}</label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" class="check" onclick="veri(this)" id="paternidad">
                    </div>

                    <div class="col-md-3">
                        <label for="">8.{{trans('tecnicof.vacations')}}</label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" class="check" onclick="veri(this)" id="vacaciones">
                    </div>
                </div>

                <div class="row" style="margin-top:5px;">
                    <div class="col-md-3">
                        <label for="">4.{{trans('tecnicof.domesticcalamity')}}</label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" class="check" onclick="veri(this)" id="domestica">
                    </div>
                    <div class="col-md-3">
                        <label for="">9.Atrasos</label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" class="check" onclick="veri(this)" id="atraso">
                    </div>
                </div>
                <div class="row" style="margin-top:5px;">
                    <div class="col-md-3">
                        <label for="">5.{{trans('tecnicof.forgetfulnessofmarking')}}</label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" class="check" onclick="veri(this)" id="olvido">
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="row">
                    <div style="text-align: center;">
                        <label for="">{{trans('tecnicof.leaveindays')}}</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <label for="">{{trans('tecnicof.from')}}</label>
                    </div>
                    <div class="col-md-4">
                        <input type="date" id="desde" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label for="">{{trans('tecnicof.to')}}</label>
                    </div>
                    <div class="col-md-4">
                        <input type="date" id="hasta" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div style="text-align: center;">
                        <label for="">{{trans('tecnicof.leaveinhours')}}</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <label for="">{{trans('tecnicof.leave')}}</label>
                    </div>
                    <div class="col-md-4">
                        <input type="time" id="sale" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label for="">{{trans('tecnicof.enter')}}</label>
                    </div>
                    <div class="col-md-4">
                        <input type="time" id="ingresa" class="form-control">
                    </div>
                </div>

                <div class="row">
                    <div style="text-align: center;">
                        <label for="">{{trans('tecnicof.forgotten')}}</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <label for="">{{trans('tecnicof.enter')}}</label>
                    </div>
                    <div class="col-md-4">
                        <input type="time" id="ingreso" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label for="">{{trans('tecnicof.leave')}}</label>
                    </div>
                    <div class="col-md-4">
                        <input type="time" id="salida" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div style="text-align: center;">
                        <label for="">{{trans('tecnicof.uploaddocument')}}</label>
                    </div>
                </div>
                <div class="row">
                    <input type="file" name="userfile" id="userfile" accept="application/pdf" />
                </div>
            </div>
        </div>
        <div class="row" style="margin-left: 20px;">
            <p>*{{trans('tecnicof.hourly')}}</p>
            <p>*{{trans('tecnicof.attached')}}</p>
        </div>
        <div class="row" style="margin-left: 20px;">
            <textarea name="obs" id="obs" placeholder="Observaciones" id="obs" style="width: 100%;"></textarea>
        </div>
        <div class="row">
            <div style="text-align: center;margin-bottom:10px;">
                <button class="btn btn-danger" id="guardar">
                    {{trans('tecnicof.save')}}
                </button>
            </div>
        </div>
    </div>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script type="text/javascript">
    document.getElementById("desde").addEventListener('change', function() {

        let fecha = new Date(this.value);
        let fechaPrueba = new Date(fecha.getFullYear(),fecha.getMonth(),parseInt(fecha.getDate()+1));
        let fechaActual = new Date();
        let fechaPrueba1 = new Date(fechaActual.getFullYear(),fechaActual.getMonth(),parseInt(fechaActual.getDate()));
        if(fechaPrueba.getTime() < fechaPrueba1.getTime()){
            swal("Fecha pasada!", "Incorrecto", "error");
            document.getElementById("hasta").value = "";
        }
    });
    document.getElementById("hasta").addEventListener('change', function() {
        let fecha = new Date(this.value);
        let fechaPrueba = new Date(fecha.getFullYear(),fecha.getMonth(),parseInt(fecha.getDate()+1));
        let fechaActual = new Date();
        let fechaPrueba1 = new Date(fechaActual.getFullYear(),fechaActual.getMonth(),parseInt(fechaActual.getDate()));

        let desdeFecha = document.getElementById("desde").value;
        let fechaFormat = new Date(desdeFecha);
        var timeDiff = Math.abs(fechaFormat.getTime() - fecha.getTime());
        var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));

        if(diffDays > 15 ){

            swal("Solo son permitidos 15 dias!", "Incorrecto", "error");
            document.getElementById("hasta").value = "";
            document.getElementById("desde").value = "";
        }

        if(fechaPrueba.getTime() < fechaPrueba1.getTime()){
            swal("Fecha pasada!", "Incorrecto", "error");
            document.getElementById("hasta").value = "";
        }
    });

    function veri(dato) {

        var datoEvitar = dato.id;
        const fechaDesde = new Date();
        const fecha = fechaDesde.getMonth() + 1;
        if (datoEvitar == 'olvido' || datoEvitar == 'atraso' || datoEvitar == 'teletrabajo') {
            document.getElementById("ocultar-si").style.visibility = "hidden";
        } else {
            document.getElementById("ocultar-si").style.visibility = "visible";
        }
        if(datoEvitar == 'vacaciones' && fecha == 1){
         swal("Incorrecto!", "Solo se puede seleccionar vacaciones en Enero", "error");
         document.getElementById(datoEvitar).checked = false;
        }
        var array = document.getElementsByClassName("check");
        for (let index = 0; index < array.length; index++) {
            if (array[index].id != datoEvitar) {
                document.getElementById(array[index].id).checked = false;
            }
        }
    }

    document.getElementById("guardar").addEventListener('click', function() {
        var my_var = <?php echo json_encode($valida); ?>;


        var res = '';
        var todo = document.getElementsByClassName('check');
        for (let index = 0; index < todo.length; index++) {

            if (todo[index].checked == true) {

                res = todo[index].id;
            }
        }
        var arrayCompleto = [

            {
                'fecha_desde': document.getElementById('desde').value
            }, {
                'fecha_hasta': document.getElementById('hasta').value
            },
            {
                'sala': document.getElementById('sale').value
            }, {
                'ingresa': document.getElementById('ingresa').value
            },
            {
                'ingreso': document.getElementById('ingreso').value
            }, {
                'salida': document.getElementById('salida').value
            },
            {
                'permiso': res
            },
            {
                'observaciones': document.getElementById("obs").value
            }
        ];
        var my_var = <?php echo json_encode($valida); ?>;

        enviar(my_var, arrayCompleto);
    });



    function enviar(my_var, arrayCompleto) {
        console.log("aqui")
        $.ajax({
            url: "{{route('ticketpermisos.save')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: {
                'datos_nomina': my_var,
                'todo': arrayCompleto,
            },
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                var url = "{{route('ticketpermisos.index')}}"
                if (data == 'ok') {

                    swal("Guardado!", "Correcto", "success");
                    window.location = url;
                }
            },
            error: function(xhr, status) {
                alert('Existió un problema');
                //console.log(xhr);
            },
        });
    }

    function enviar(my_var, arrayCompleto) {
        console.log("aqui2")
        var fd = new FormData();
        fd.append('userfile', $('#userfile')[0].files[0]);

        $.ajax({
            url: "{{route('ticketpermisos.subir_pdf1')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            processData: false,
            contentType: false,
            data: fd,
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                $.ajax({
                    url: "{{route('ticketpermisos.save')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    data: {
                        'id': data,
                        'datos_nomina': my_var,
                        'todo': arrayCompleto,
                    },
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        var url = "{{route('ticketpermisos.index')}}"
                        if (data == 'ok') {
                            swal("Guardado!", "Correcto", "success");
                            window.location = url;
                        }
                    },
                    error: function(xhr, status) {
                        alert('Existió un problema');

                    },
                });
            },
            error: function(xhr, status) {
                alert('Existió un problema');
            },
        });
    }

    function goBack() {
        window.history.back()
    }
</script>


@endsection