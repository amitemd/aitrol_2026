@extends('contable.importaciones.base')
@section('action-content')

<style>
    .form-control {
        border-radius: 7PX;
    }

    .dropdown-menu li a {
        font-size: 15px;
        color: white;
    }

    .checks {}

    .validar {
        border: 1px solid red;
        border-radius: 5px;
    }


    /* CSS CARGANDO */
    .loader-wrapper {
        width: 220px;
        height: 220px;
    }

    .swal2-container {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: horizontal;
        -webkit-box-direction: normal;
        -ms-flex-direction: row;
        flex-direction: row;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        padding: 10px;
        background-color: transparent;
        z-index: 1060;
    }

    .loader {
        box-sizing: border-box;
        width: 100%;
        height: 100%;
        border: 34px solid #162534;
        border-top-color: #4bc8eb;
        border-bottom-color: #f13a8f;
        border-radius: 50%;
        animation: rotate 5s linear infinite;
    }

    .loader-inner {
        border-top-color: #36f372;
        border-bottom-color: #fff;
        animation-duration: 2.5s;
    }

    @keyframes rotate {
        0% {
            transform: scale(1) rotate(360deg);
        }

        50% {
            transform: scale(.8) rotate(-360deg);
        }

        100% {
            transform: scale(1) rotate(360deg);
        }
    }

    .cargando {
        text-align: center;
        color: white;
        font-size: 38px;
        text-transform: uppercase;
        font-weight: 700;
        font-family: inherit;
    }

    * {
        font-family: var(--fuentePrincipal);
    }

    .contenedor-box {
        box-shadow: 0px 0px 5px 0px rgb(0 0 0 / 35%);
        -webkit-box-shadow: 0px 0px 5px 0px rgb(0 0 0 / 35%);
        -moz-box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, 0.35);
        padding: 18px 0px;
        border-radius: 7px;
        margin-bottom: 12px;
    }

    .btn {
        border-radius: 20px !important;
    }
    th{
        text-transform: uppercase;
        color: black;
        border-bottom: 1px solid black!important;
    }
    /* FIN CSS CARGANDO */
</style>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.Compras')}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.registrodeimportac')}}</li>
        </ol>
    </nav>

    <!-- ANIMACION DE CARGANDO-->
    <div id="cargando" style="overflow-y: auto;">

    </div>
    <!-- FIN DE ANIMACION DE CARGANDO-->
    <div class="box">
        <div class="box-header header_new">
            <form id="formulario">
                <div class="col-md-12">
                    <div class="col-md-8">
                        <h3 class="box-title">{{trans('contableM.importacompras-')}}</h3>
                    </div>
                </div>


                <div class="col-md-12" style="display: flex;justify-content: center;">
                    <div class="col-md-5 contenedor-box">
                        <div class="col-md-12">
                            <div class="form-group col-md-12 col-xs-12" style="text-align: center; margin: 0;">
                                <label class="texto" for="concepto">{{trans('contableM.concepto')}}: </label>
                            </div>
                            <div class="form-group col-md-12 col-xs-12 container-4">
                                <input class="form-control" type="text" id="concepto" name="concepto" placeholder="Ingrese Concepto..." />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-md-12 col-xs-12" style="text-align: center; margin: 0;">
                                <label class="texto" for="serie">{{trans('contableM.serieagrup')}}: </label>
                            </div>
                            <div class="form-group col-md-12 col-xs-12 container-4">
                                <input class="form-control" type="text" id="serie_agrup" name="serie_agrup" placeholder="Ingrese Serie..." autocomplete="off" />
                            </div>
                        </div>
                    </div>

                </div>



                <div class="col-md-12 text-left" style="display:flex; justify-content:center;">
                    <div class="col-md-4">
                        <div class="col-md-6">
                            <button id="guardar" onclick="verificarChecks(event)" class="btn btn-success" style="width: 100%;">
                                <i class="fa fa-plus-circle" aria-hidden="true"></i> {{trans('contableM.guardarche')}}
                            </button>
                        </div>
                        <div class="col-md-6 ">
                            <a id="guardar" href="{{route('importaciones.index')}}" class="btn btn-danger" style="width: 100%;">
                                <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i> {{trans('laboratorio.regresar')}}
                            </a>
                        </div>
                    </div>
                </div>



                {{ csrf_field() }}
                <div class="box-body dobra">
                    <div class="form-group col-md-12">
                        <div class="form-row">
                            <div id="resultados">
                            </div>
                            <div id="contenedor">
                                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                                    <div class="row">
                                        <div class=" col-md-12 " style="margin-top: 16px;">
                                            <table id="tabla" class="table" role="grid" aria-describedby="example2_info">
                                                <thead>
                                                    <tr class=''>
                                                        <th width="3%" >{{trans('contableM.id')}}</th>
                                                        <th width="6%"> <abbr title="{{trans('contableM.secimporta')}}"> SEC. IMP.</abbr> </th>
                                                        <th width="10%" >{{trans('contableM.proveedor')}}</th>
                                                        <th width="10%" >{{trans('contableM.concepto')}}</th>
                                                        <th width="10%" > <abbr title="{{trans('contableM.fechaautoriza')}}">FECH. AUT.</abbr> </th>
                                                        <th width="15%" > <abbr title="{{trans('contableM.secuenciafactura')}}"> SEC. FACT. </abbr></th>
                                                        <th width="15%" >{{trans('contableM.serie')}}</th>
                                                        <th width="10%" >{{trans('contableM.creadopor')}}</th>
                                                        <th width="10%" >{{trans('contableM.estado')}}</th>
                                                        <th width="15%" >{{trans('contableM.accion')}}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    @foreach($importaciones as $value)
                                                    <tr>
                                                        <td>{{$value->id}}</td>
                                                        <td> {{$value->secuencia_importacion}}</td>
                                                        <td>@if(isset($value->proveedor_da)) {{$value->proveedor_da->razonsocial}} @endif</td>
                                                        <td>{{$value->observacion}}</td>
                                                        <td>{{$value->fecha}} </td>
                                                        <td>{{ $value->secuencia_factura }}</td>
                                                        <td>{{ $value->serie }}</td>
                                                        <td> @if(isset($value->usuario)) {{ $value->usuario->nombre1 }} {{ $value->usuario->apellido1 }} @endif</td>
                                                        <td> @if($value->estado == 1) {{trans('contableM.activo')}} @else {{trans('contableM.inactivo')}} @endif</td>
                                                        <td>
                                                            <input class="form-check-input" type="checkbox" role="switch" name="checks[]" id="cbox{{$value->id}}" value="{{$value->id}}">
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
</section>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
</script>

<script>
    const cargando = accion => {
        let divCargando = document.getElementById("cargando");
        if (accion == 1) {
            let content = `<div id="cargando_hijo" class="loader-wrapper">
                                <div class="loader">
                                    <div class="loader loader-inner"></div>
                                </div>
                                <div class="cargando">Cargando...</div>
                            </div>`;


            divCargando.classList.add("swal2-container", "swal2-center", "swal2-backdrop-show")

            $('#cargando').append(content);
        } else {
            let hijo = document.getElementById("cargando_hijo");
            divCargando.removeChild(hijo)
            divCargando.classList.remove("swal2-container", "swal2-center", "swal2-backdrop-show")
        }


    }

    function verificarChecks(e) {
        e.preventDefault();
        // cargando(1);
        console.log("verificando...")
        let concepto = document.getElementById("concepto");
        let serie = document.getElementById("serie_agrup");
        let validar = false;
        console.log(concepto)
        if (concepto.value.trim() == "") {
            concepto.classList.add("validar");
            validar = true;
        } else {
            concepto.classList.remove("validar");
            validar = false;
        }

        if (serie.value.trim() == "") {
            serie.classList.add("validar");
            validar = true;
        } else {
            serie.classList.remove("validar");
            validar = false;
        }

        if (!validar) {
            document.getElementById("guardar").disabled = true;

            $.ajax({
                url: "{{route('importaciones.store_agrupada')}}",
                type: "get",
                beforeSend: function() {
                    Swal.fire({
                        text: "Cargando...",
                        imageUrl: 'https://c.tenor.com/XK37GfbV0g8AAAAi/loading-cargando.gif',
                        imageWidth: 200,
                        imageHeight: 200,
                        imageAlt: 'Custom image',
                    })
                },
                dataType: 'json',
                data: $('#formulario').serialize(),
                success: function(data) {
                    Swal.close();
                    if (data.respuesta == 'success') {
                        alertas(data.respuesta, data.msj);
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        // cargando(0);
                        document.getElementById("guardar").disabled = false;
                        alertas(data.respuesta, data.msj);
                    }
                },
                error: function(data) {
                    Swal.close();

                    //cargando(0)
                }
            })
        } else {
            // cargando(0);
            Swal.close();

            alertas('error', "{{trans('contableM.aggconceposerie')}}");
        }

    }

    // const alertas = (icon, msj) => {
    //     Swal.fire({
    //         position: 'center',
    //         icon: `${icon}`,
    //         title: `${msj}`,
    //         showConfirmButton: false,
    //         timer: 1500
    //     })
    // }
</script>

@endsection