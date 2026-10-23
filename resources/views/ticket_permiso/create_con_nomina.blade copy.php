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
<style type="text/css">
  .alerta_correcto{
        position: absolute;
        z-index: 9999;
        top: 200px;
        right: 100px;
    }
   .ui-autocomplete
        {
             overflow-x: hidden;
              max-height: 200px;
              width:1px;
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            float: left;
            display: none;
            min-width: 160px;
            _width: 160px;
            padding: 4px 0;
            margin: 2px 0 0 0;
            list-style: none;
            background-color: #fff;
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
            *border-right-width: 2px;
            *border-bottom-width: 2px;
        }
</style>
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-md-10">
                    <h3 class="box-title">CREAR SOLICITUD DE PERMISOS</h3>
                </div>
                <div class="col-md-2">
                    <input type="button" class="btn btn-danger" value="Regresar" onclick="goBack()">
                </div>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="row">
                <div class="col-md-1">
                    <label for="">Buscar Usuario</label>
                </div>
                <div class="col-md-6">
                    <input class="form-control" type="text" id="vh_usuario" name="vh_usuario">
                </div>
                <div class="col-md-1">
                    <label for="">Cedula</label>
                </div>
                <div class="col-md-3">
                    <input class="form-control" type="text" id="vh_cedula" name="vh_cedula" readonly>
                </div>
                <!--div class="col-md-6">
                    <input type="text" id="usuario" autocomplete="of" style="width:100%">
                    <div id="result1">
                    </div>
                </div-->
                <div class="col-md-12"></div>

                <!--div class="col-md-1">
                    <label for="">Nombres</label>
                </div>
                <div class="col-md-3">
                    <input type="text" id="usuario" utocomplete="of">
                    <div id="result">
                    </div>
                </div-->
            
            </div>
            <form id="form_guardar">
                <div class="row" style="margin-top:5px;">
                    <div class="col-md-2">
                        <label for="">Fecha Registro</label>
                    </div>
                    <div class="col-md-3">
                        <input type="date" id="fecha" class="form-control">
                    </div>
                    <div class="col-md-1">
                        <label for="">Cédula</label>
                    </div>
                    <div class="col-md-2">
                        <input type="text" id="cedula" class="form-control">

                    </div>
                    <div class="col-md-1">
                        <label for="">Cargo</label>
                    </div>
                    <div class="col-md-2">
                        <input type="text" id="cargo" class="form-control">
                    </div>
                    <div class="col-md-1">
                        <label for="">Departamento</label>
                    </div>
                    <div class="col-md-2">
                        <input type="text" id="area" class="form-control">
                    </div>
                </div>
            </form>    
            <div class="col-md-5">
                <div class="row" style="margin-top:5px;">
                    <div class="col-md-3">
                        <label for="">1.Permiso por fallecimiento</label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" class="check" onclick="veri(this)" id="fallecimiento">
                    </div>

                    <div class="col-md-3">
                        <label for="">6.Permiso medico</label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" class="check" onclick="veri(this)" id="medico">
                    </div>
                </div>
                <div class="row" style="margin-top:5px;">
                    <div class="col-md-3">
                        <label for="">2.Licencia por maternidad</label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" class="check" onclick="veri(this)" id="maternidad">
                    </div>

                    <div class="col-md-3">
                        <label for="">7.Permiso Personal</label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" class="check" onclick="veri(this)" id="personal">
                    </div>
                </div>

                <div class="row" style="margin-top:5px;">
                    <div class="col-md-3">
                        <label for="">3.Licencia por paternidad</label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" class="check" onclick="veri(this)" id="paternidad">
                    </div>

                    <div class="col-md-3">
                        <label for="">8.Vacaciones</label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" class="check" onclick="veri(this)" id="vacaciones">
                    </div>
                </div>

                <div class="row" style="margin-top:5px;">
                    <div class="col-md-3">
                        <label for="">4.Calamidad domestica</label>
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
                        <label for="">5.Olvido de marcación</label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" class="check" onclick="veri(this)" id="olvido">
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="row">
                    <div style="text-align: center;">
                        <label for="">Para permiso en dias</label>
                    </div>
                </div>
                <div class="row">
               
                  <div id="ocultar-si">
                    <div class="col-md-2">
                        <label for="">Fecha desde</label>
                    </div>
                    <div class="col-md-4">
                        <input type="date" id="desde" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label for="">Fecha hasta</label>
                    </div>
                    <div class="col-md-4">
                        <input type="date" id="hasta" class="form-control">
                    </div>
                  </div>
                </div>
                <div class="row">
                    <div style="text-align: center;">
                        <label for="">Para permiso en horas</label>
                    </div>
                </div>
                <div class="row">
                 <div id="ocultar-no">
                    <div class="col-md-2">
                        <label for="">Sale</label>
                    </div>
                    <div class="col-md-4">
                        <input type="time" id="sale" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label for="">Ingresa</label>
                    </div>
                    <div class="col-md-4">
                        <input type="time" id="ingresa" class="form-control">
                    </div>
                    <div>
                </div>
                <div class="row">
                    <div style="text-align: center;">
                        <label for="">Para Olvido de Marcación o Atrasos</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <label for="">Ingreso</label>
                    </div>
                    <div class="col-md-4">
                        <input type="time" id="ingreso" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label for="">Salida</label>
                    </div>
                    <div class="col-md-4">
                        <input type="time" id="salida" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div style="text-align: center;">
                        <label for="">Subir Documento</label>
                    </div>
                </div>
                <div class="row">
                    <input type="file" name="userfile" id="userfile" accept="application/pdf" />
                </div>
            </div>
        </div>
        <div class="row" style="margin-left: 20px;">
            <p>*Todos los permisos por horas son recuperables,la forma de recuperar devera ser detallada en el presente documento</p>
            <p>*A todos los permisos debera adjuntarse el soporte respectivo, a excepción de los puntos 7 y 8 especificados en la presente acción de personal</p>
        </div>
        <div class="row" style="margin-left: 20px;">
            <div class="col-md-12">
                <textarea name="obs" placeholder="Observaciones" id="obs" style="width: 100%;"></textarea>
            </div>
        </div>

    </div>
    <div class="row">
        <div style="text-align: center;margin-bottom:10px;">
            <button class="btn btn-danger" id="guardar">
                Guardar
            </button>
        </div>
    </div>


</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">

    $("#vh_usuario").autocomplete({
        source: function( request, response ) {

            $.ajax({
                url:"{{route('ticketpermisos.vh_buscar_usuario')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                data: {
                    term: request.term
                      },
                dataType: "json",
                type: 'post',
                success: function(data){
                    //console.log(data);
                    response(data);
                }
            })
        },
        minLength: 2,
        select: function(data, ui){
            //console.log("++"+ui.item.codigo);
            $('#vh_cedula').val(ui.item.id);
            //enfermeria_nombre_2(ui.item.codigo);
        }
    } );


    document.getElementById("fecha").addEventListener('change', function() {
        let fecha = new Date(this.value);
        let fechaPrueba = new Date(fecha.getFullYear(),fecha.getMonth(),parseInt(fecha.getDate()+1));
        let fechaActual = new Date();
        let fechaPrueba1 = new Date(fechaActual.getFullYear(),fechaActual.getMonth(),parseInt(fechaActual.getDate()));
        if(fechaPrueba.getTime() < fechaPrueba1.getTime()){
            swal("Fecha pasada!", "Incorrecto", "error");
            document.getElementById("hasta").value = "";
        }
    });
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
    document.getElementById("usuario").addEventListener('keydown', function() {
        let usuario = document.getElementById("usuario").value;
        //console.log(usuario);

        $.ajax({
            url: "{{route('ticketpermisos.vh_buscar_usuario')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: {
                'data': usuario,
            },
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                console.log(data); 
                var resultado = document.getElementById("result");
                var list = '';
                if (data.length > 0) {
                    document.getElementById("result").style.visibility = "visible";
                    for (let i = 0; i < data.length; i++) {
                        var nombreCompleto = data[i]['nombre1'] + ' ' + data[i]['nombre2'];
                        list += '<li  onclick="oprimir(this)" name="' + nombreCompleto + '" id=' + data[i]['id'] + '>' + data[i]['nombre1'] + ' ' + data[i]['nombre2'] + '</li>';
                    }
                    resultado.innerHTML = '<ul class="hola" style="margin-top:30px;">' + list + '</ul>';
                }
            },
            error: function(xhr, status) {
                alert('Existió un problema');
                //console.log(xhr);
            },
        });

    });


   

    function veri(dato) {

        var datoEvitar = dato.id;
        const fechaDesde = new Date();
        const fecha = fechaDesde.getMonth()+1;

         if(datoEvitar == 'olvido' ||  datoEvitar == 'atraso'){
           document.getElementById("ocultar-si").style.visibility = "hidden";
        }else{
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

    function oprimir(valor) {
        var te = valor.getAttribute("name");
        document.getElementById("result").style.visibility = "hidden";
        document.getElementById("usuario").value = te;
        document.getElementById("cedula").value = valor.id;
        $.ajax({
            url: "{{route('ticketpermisos.verificar')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: {
                'id': valor.id,
            },
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                console.log(data);
                var area = '';
                if (data.area == 1) {
                    area = 'ADMINISTRATIVA';
                } else {
                    area = 'MEDICA';
                }
                if (data.cargo != undefined) {
                    document.getElementById("cargo").value = data.cargo;
                }
                if (area != undefined) {
                    document.getElementById("area").value = area;
                }
                var url = "{{route('ticket_soporte_tecnico.index')}}"
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


    function oprimir1(valor) {
        var te = valor.getAttribute("name");
        document.getElementById("result1").style.visibility = "hidden";
        document.getElementById("apellido").value = te;
        document.getElementById("cedula").value = valor.id;
        $.ajax({
            url: "{{route('ticketpermisos.verificar')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: {
                'id': valor.id,
            },
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                console.log(data);
                var area = '';
                if (data.area == 1) {
                    area = 'ADMINISTRATIVA';
                } else {
                    area = 'MEDICA';
                }
                if (data.cargo != undefined) {
                    document.getElementById("cargo").value = data.cargo;
                }
                if (area != undefined) {
                    document.getElementById("area").value = area;
                }
                var url = "{{route('ticket_soporte_tecnico.index')}}"
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

    window.addEventListener('click', function(e) {
        document.getElementById("result").style.visibility = "hidden";
    });
    window.addEventListener('click', function(e) {
        document.getElementById("result1").style.visibility = "hidden";
    });



    document.getElementById("guardar").addEventListener('click', function() {

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

        var my_var = [

            {
                'cedula': document.getElementById("cedula").value
            },
            {
                'cargo': document.getElementById("cargo").value
            },
            {
                'area': document.getElementById("area").value
            },
            {
                'permiso': res,
            },
            {
                'permiso': res,
            },



        ];

        enviar(my_var, arrayCompleto);
    });



    function enviar(my_var, arrayCompleto) {

        var fd = new FormData();
        fd.append('userfile', $('#userfile')[0].files[0]);
        if ($("#usuario").val() == '' || $("#cedula").val() == '') {
            swal("Error!", "Hay campos vacios", "error");
        } else {
            $.ajax({
                url: "{{route('ticketpermisos.subir_documento')}}",
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
                        url: "{{route('ticketpermisos.save_sin_dato')}}",
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

    }

    function goBack() {
        window.history.back()
    }
</script>


@endsection