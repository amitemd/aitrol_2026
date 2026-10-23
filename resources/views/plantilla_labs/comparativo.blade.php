@extends('laboratorio.orden.base')
@section('action-content')
<!-- <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Mono:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet"> -->
<?php
$idPlanilla = isset($planilla->id) ? $planilla->id : $planilla_derivado->id;
?>
<style>
    .contenedor {
        max-width: 95%;
        margin: 0 auto;
    }
/* 
    * {
        font-family: 'Poppins', sans-serif;
    } */

    .titulo {
        font-weight: bold;
        color: red;
    }

    .body {
        background-color: #ffff;
        border-radius: 5px;
    }

    .conten {
        position: fixed;
        bottom: 10px;
    }

    /*notificacion de barra*/
    .notEmail {
        display: none;
        position: relative;
        width: 320px;
        height: 90px;
        margin-bottom: 5px;
        background-color: #ffffff;
        padding: 5px;
        border: 1px solid rgba(0, 0, 0, 0.5);
        box-sizing: border-box;
        overflow: hidden;
        -webkit-border-radius: 0 5px 5px 0;
        border-radius: 0 5px 5px 0;
        -webkit-box-shadow: 9px 5px 7px 0 rgba(0, 0, 0, 0.3);
        box-shadow: 9px 5px 7px 0 rgba(0, 0, 0, 0.3);
        animation-name: slideIn;
        animation-duration: 1s;
        animation-timing-function: ease-out;
        animation-iteration-count: 1;
        opacity: 1;
        animation-delay: 0s;
    }

    .mensaje_not {
        float: left;
        width: 60%;
        height: 100%;
        text-align: center;
        line-height: 90px;
        font-size: 0.9em;
    }

    .notEmail>img {
        float: right;
        height: 100%;
        background-size: contain;

    }

    @keyframes slideIn {
        0% {
            margin-left: -500px;
            opacity: 0;
        }

        100% {
            margin-left: 0px;
            opacity: 1;
        }
    }
</style>
<section class="content">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="col-md-12">
                    <h3 class="titulo">Empresa: {{ $empresa->id }} - {{ $empresa->nombrecomercial }}</h3>
                </div>
                <div class="col-md-12">
                    <h3 class="box-title">Comparativo</h3>
                </div>
                <div class="col-md-12 d-flex ">
                    <div class="col-md-11">
                        <h5><span style="font-weight: bold;">Paciente:</span> {{$orden->id_paciente}} - {{ $orden->paciente->apellido1 }} {{ $orden->paciente->apellido2 }} {{ $orden->paciente->nombre1 }} {{ $orden->paciente->nombre2 }}</h5>
                    </div>
                    <div class="col-md-1">
                        <a href="{{route('orden.index_control')}}" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Regresar</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="col-md-12 body">
        @if($certificar == 1)
        <!-- <div class="col-md-12" style="padding: 12px;">
            <button class="btn btn-primary" id="btnreprocesar" onclick="reprocesar()">REPROCESAR</button>
        </div> -->
        @endif
    </div>
    <form id="formulario">
        <input type="hidden" name="id_orden" value="{{$orden->id}}">
        <input type="hidden" name="id_planilla" value="<?= $idPlanilla; ?>">
        <div class="col-md-12 body">
            <table class="table ">
                <thead>
                    <th width="20%">Serie</th>
                    <th>Nombre</th>
                    <th>Fecha</th>
                    <th>Cantidad</th>
                    <th>Bodega</th>
                    <th>Acción</th>
                    <!-- <th></th> -->
                </thead>
                <tbody id="table_insumos">
                  @php echo $filas; @endphp
                </tbody>
            </table>
            <div class="col-md-12">
                <center>
                    <h3><b> Derivados </b></h3>
                </center>
            </div>
            <div class="col-md-12">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Examen</th>
                            <th>Tipo</th>
                            <th>Valor</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="tabla_derivado">
                        <?php
                        if (isset($planilla_derivado->detalles)) {
                            foreach ($planilla_derivado->detalles as $det) {
                                $sitio = explode('.', $_SERVER['HTTP_HOST']);
                        ?>
                                <input type="hidden" name="detalles[]" value="{{$det->id}}">
                                <input type="hidden" name="codigos[]" value="{{$det->codigo}}">
                                <input type="hidden" name="bodega[]" value="{{$det->id_bodega}}">
                                <input type="hidden" name="examenDev[]" value="{{$det->derivado->examen->nombre}}">
                                <input type="hidden" name="tipoExamenDev[]" value="{{$det->derivado->tipo_derivado->nombre}}">
                                <input type="hidden" name="nombrebodega[]" value="{{$det->nombre}} {{strtoupper($sitio[0])}}">
                                <tr>
                                    <td>@if(isset($det->derivado)) {{$det->derivado->examen->nombre}} @endif</td>
                                    <td>@if(isset($det->derivado)) {{$det->derivado->tipo_derivado->nombre}} @endif</td>
                                    <td>@if(isset($det->derivado)) {{$det->derivado->valor}} @endif</td>
                                </tr>
                        <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-12" style="margin-bottom: 1rem;">
                <label for="">Observacion</label>
                <textarea @if($certificar==0) disabled @endif name="observacion_planilla" class="form-control" id="" cols="5" rows="5">@if(!is_null($planilla)) @if($planilla->aprobado == 1) {{$planilla->observacion}} @else Paciente: {{ $orden->paciente->apellido1 }} {{ $orden->paciente->apellido2 }} {{ $orden->paciente->nombre1 }} {{ $orden->paciente->nombre2 }} @endif @endif</textarea>
            </div>
            @if($certificar == 1)
            <div class="col-md-12" style="margin-bottom: 1rem;">
                <center><button onclick="guardar()" id="btn_guardar" type="button" class="btn btn-primary"><i class="fa fa-check-square-o" aria-hidden="true"></i></button> </center>
            </div>
            @endif
        </div>
    </form>
    <div class="conten">
        <div id="notificacion" class="notEmail">
            <div class="mensaje_not">
                Correo enviado con éxito 1
            </div>
            <img src="">
        </div>
    </div>
</section>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const cambiarEstado = id => {
        let check = document.getElementById('checkbox' + id).checked;
        let check_aprobado = document.getElementById('check_aprobado' + id)
        console.log("Cambios de estado", check);
        if (check) {
            check_aprobado.value = 1;
            checkUpdate(id, 1)
        } else {
            check_aprobado.value = 0;
            checkUpdate(id, 0)
        }
    }
    const guardar = () => {
        Swal.fire({
            title: 'Esta seguro que quiere aprobar?',
            text: "",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, continuar!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('btn_guardar').style.display = 'none';
                document.getElementById('btnreprocesar').style.display = 'none';
                store()
            }
        })
    }
    const store = () => {
        // e.preventDefault();
        $.ajax({
            type: "get",
            url: `{{ route('laboratorio.plantilla.storePlanilla') }}`,
            dataType: "json",
            data: $("#formulario").serialize(),
            success: function(data) {
                console.log(data);
                if (data.status == "success") {
                    //let id_asiento_costo = `<a style="color:red; font-weight:bold;" target="_blank" href="{{url('contable/contabilidad/libro/edit')}}/${data.asiento}">ASIENTO COSTO : ${data.asiento_costo}`;
                    let id_asiento_costo = '';
                    alertas(data.status, data.status, `${data.msj} <br> <b>${id_asiento_costo}</b>`);
                    /*document.getElementById('btn_guardar').style.display = 'initial';
                    document.getElementById('btnreprocesar').style.display = 'initial';*/
                    return;
                } else {
                    alertas(data.status, data.status, data.msj)
                    document.getElementById('btn_guardar').style.display = 'initial';
                    document.getElementById('btnreprocesar').style.display = 'initial';
                }
            },
            error: function(data) {
                alertas("error", "Error", "Ocurrio un error...")
                document.getElementById('btn_guardar').style.display = 'initial';
                document.getElementById('btnreprocesar').style.display = 'initial';
            }
        })
    }
    const checkUpdate = (id, estado) => {
        // e.preventDefault();
        $.ajax({
            type: "get",
            url: `{{ route('plantillacontrollabs.checksUpdate') }}`,
            dataType: "json",
            data: {
                'id': id,
                'estado': estado
            },
            success: function(data) {},
            error: function(data) {}
        })
    }

    function reprocesar() {
        Swal.fire({
            title: 'Esta Seguro de Reprocesar?',
            text: 'Al Reprocesar los productos solo cogeremos lo que esten en STOCK, y que tengan seleccionado Codigo Siempre en Producto',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            denyButtonText: 'No Enviar',
            showLoaderOnConfirm: true,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'get',
                    url: "{{route('plantillacontrollabs.repocesar_serie', ['id' => $idPlanilla])}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    success: function(data) {
                        if (data.estado == "error") {
                            if (data.respuesta == 'Faltan Datos') {
                                mostrar = "";
                                for (var k in data.valores) {
                                    mostrar = mostrar + " <br> <b>Producto:</b> " + data.valores[k].producto + "  <span class='label label-danger'> <b >Error:</b> " + data.valores[k].error + ' </span> ';
                                    //console.log(k, data.valores[k]);
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: data.respuesta,
                                    html: mostrar
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: data.estado,
                                    text: data.respuesta
                                });
                            }

                        } else {
                            console.log(data);
                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito!!',
                                text: 'Registro guardado con éxito'
                            });
                            //location.reload(true);
                        }

                    },
                    error: function(data) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al reprocesar',
                            text: 'Por favor comunicarse con Soporte!'
                        });
                        console.log(data);
                    }
                });

            }
        });
    }
</script>

<script>
    // const alertas = (icon, title, text) => {
    //     Swal.fire({
    //         icon: `${icon}`,
    //         title: `${title}`,
    //         html: `${text}`,
    //     })
    // }

    function estilos() {
        document.getElementById("notificacion").style.display = "block";
        $("#notificacion").delay(6500).fadeOut(1500, "swing");
    }
</script>


@endsection