@extends('informe_tecnico.base')
@section('action-content')
<style>
    .td-border {
        border: 1px solid black;
        width: 100%;
        margin-top: 5px;
    }

    td,
    th {
        padding: 10px;
    }

    .bold {
        font-weight: bold;
    }

    .borderT {
        border: 1px solid black;
    }



    .border-left {
        border-right: 1px solid black;
    }


    .border-top {
        border-top: 1px solid black;
    }

    .centrado {
        text-align: center;
    }

    .sin-borde {
        border: none;
        border-top: none;
    }
</style>
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-md-10">
                    <h3 class="box-title">DATOS</h3>
                </div>
            </div>
        </div>

        <a href="{{route('formulario007.informe_tecnico',['cabecera'=>$archivo_plano->id])}}" class="btn btn-info btn-xs" target="_blanck">Informe tecnico Pdf</a>
        <input type="hidden" name="id_hc_procedimiento" id="id_hc_procedimiento" value="{{$hc_procedimiento->id}}">

        <div class="box-body">
            <table class="td-border">
                <tr>
                    <td rowspan="2">
                        SERVICIO GASTROENTEROLOGIA
                    </td>
                    <td>
                        <label for="fecha_elaboracion">Fecha de Elaboración:</label>
                    </td>
                    <td>
                        <input class="form-control" id="fecha_elaboracion" name="fecha_elaboracion" value="@if(!is_null($archivo_plano)) {{ substr($archivo_plano->fecha_ing, 0, 10)}} @endif" readonly>
                    </td>
                </tr>
                <tr>
                    <td>
                        Área-Proceso:
                    </td>
                    <td>
                        <span class="text-danger">AUDITORIA MEDICA</span>
                    </td>
                </tr>
            </table>
            <table class="td-border">
                <tr>
                    <td>
                        <span>INFORME TÉCNICO-MÉDICO PARA LA JUSTIFICACIÓN DE PRESTACIONES ADICIONALES A LAS AUTORIZADAS</span>
                    </td>
                    <td>
                        Página: 1
                    </td>
                </tr>
            </table>
            <table class="td-border">
                <tr>
                    <td style="text-align: center;" colspan="2">
                        <span class="bold">
                            DATOS GENERALES
                        </span>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%;">
                        <span class="bold">
                            NOMBRES Y APELLIDOS DE PACIENTE:
                        </span>

                    </td>
                    <td style="width: 50%;">
                        <input id="nombrePaciente" name="nombrePaciente"  style="width: 50%;" value="@if(!is_null($archivo_plano)) {{$archivo_plano->paciente->nombre1}} {{$archivo_plano->paciente->nombre2}} {{$archivo_plano->paciente->apellido1}} {{$archivo_plano->paciente->apellido2}} @endif" readonly>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%;">
                        <span class="bold">
                            CÉDULA DE IDENTIDAD:
                        </span>
                    </td>
                    <td style="width: 50%;">
                        <input id="cedulaPaciente" name="cedulaPaciente"  style="width: 50%;" value="@if(!is_null($archivo_plano)) {{$archivo_plano->id_paciente}} @endif" readonly >
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%;">
                        <span class="bold">
                            CÓDIGO DE VALIDACIÓN QUE AUTORIZÓ PRESTACIÓN:
                        </span>
                    </td>
                    <td style="width: 50%;">
                        <input type="text" class="form-control" style="width: 50%;" name="cod_validacion" id="cod_validacion" value="@if(!is_null($archivo_plano)) {{$archivo_plano->codigo_iess}} @endif" readonly>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%;">
                        <span class="bold">
                            FECHA DE INGRESO DEL PACIENTE:
                        </span>

                    </td>
                    <td style="width: 50%;">
                        <input class="form-control" style="width: 50%;" name="fecha_ingreso" id="fecha_ingreso" value="@if(!is_null($archivo_plano)) {{ substr($archivo_plano->fecha_ing, 0, 10)}} @endif" readonly>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%;">
                        <span class="bold">
                            MÉDICO SOLICITANTE:
                        </span>

                    </td>
                    <td style="width: 50%;">
                        <input class="form-control" style="width: 50%;" name="doctor_solicitante" id="doctor_solicitante" value="@if(!is_null($hc_procedimiento)) @if($hc_procedimiento->doctor_firma != null) {{ $hc_procedimiento->doctor_firma->apellido1}} {{ $hc_procedimiento->doctor_firma->apellido2}} {{ $hc_procedimiento->doctor_firma->nombre1}} {{ $hc_procedimiento->doctor_firma->nombre2}}  @endif @endif" readonly >
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%;">
                        <span class="bold">
                            ESPECIALIDAD / SUBESPECIALIDAD:
                        </span>
                    </td>
                    <td style="width: 50%;">
                        @if(!is_null($hc_procedimiento))
                            @php 
                                $especialidad = null;$txt_esp = '';
                                if($hc_procedimiento->doctor_firma != null){
                                    $uesp = Sis_medico\User_espe::where('usuid', $hc_procedimiento->doctor_firma->id)->first();
                                    if(!is_null($uesp)){
                                        $especialidad = Sis_medico\Especialidad::find($uesp->espid);
                                        if(!is_null($especialidad)){
                                            $txt_esp = $especialidad->nombre;
                                        }
                                    } 
                                }
                            @endphp
                            <input class="form-control" style="width: 50%;" name="doctor_solicitante" id="doctor_solicitante" value="{{ $txt_esp }}" readonly >

                         @endif  
                    </td>
                </tr>
            </table>
            <table class="td-border">
                <tr>
                    <td style="text-align: center;" colspan="2">
                        <span class="bold">
                            DIAGNÓSTICOS (CIE-10):
                        </span>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%;">
                        <span class="bold">
                            DIAGNÓSTICO(S) QUE JUSTIFICARON EL CÓDIGO DE VALIDACIÓN:
                        </span>

                    </td>
                    @php 
                        $cie10 = Sis_medico\Cie_10_3::where('id',$archivo_plano->cie10)->where('estado','1')->first(); 
                        if(is_null($cie10)){
                            $cie10 = Sis_medico\Cie_10_4::where('id',$archivo_plano->cie10)->where('estado','1')->first();
                        }
                    @endphp
                    <td style="width: 50%;">
                        <input class="form-control" style="width: 50%;" name="doctor_solicitante" id="doctor_solicitante" value="{{ $archivo_plano->cie10 }} @if(!is_null($cie10)) {{ $cie10->descripcion }} @endif" readonly >
                    </td>
                </tr>
                
                    @php
                        if(isset($cie10_eg[0])){
                            $c10 = $cie10_eg[0];
                            $c3 = Sis_medico\Cie_10_3::find($c10->cie10);
                            $c4 = Sis_medico\Cie_10_4::find($c10->cie10);

                            $texto_2 = '';
                            if(!is_null($c3)){
                                $texto_2 = $c3->descripcion;
                            }
                            if(!is_null($c4)){
                                $texto_2 = $c4->descripcion;
                            }
                        }
        
                        
                        
                    @endphp
                
                <tr>
                    <td style="width: 50%;">
                        <span class="bold">
                            DIGANÓSTICOS ACTUALES QUE JUSTIFICARÁN LA PRESTACIÓN ADICIONAL:
                        </span>
                    </td>
                    <td style="width: 50%;">
                        <input class="form-control" style="width: 50%;" name="doctor_solicitante" id="doctor_solicitante" value="@if(isset($cie10_eg[0])){{ $c10->cie10 }} {{ $texto_2 }} @endif" readonly >
                    </td>
                </tr>
            </table>
            <table class="td-border">
                <tr>
                    <td style="text-align: center;" colspan="2">
                        <span class="bold">
                            CUADRO CLÍNICO DEL PACIENTE QUE JUSTIFICARÁ PRESTACIÓN ADICIONAL
                        </span>
                    </td>
                </tr>

                <tr>
                    <td style="width: 100%;">
                        @if(!is_null($evolucion))
                       {{ strip_tags($evolucion->cuadro_clinico) }}
                       @endif
                       <br>Diagnostico: 
                            @if(isset($cie10_eg[0])){{ $c10->cie10 }} {{ $texto_2 }} @endif
                        <br>Conclusion:
                        @if(!is_null($protocolo))
                            {{ strip_tags($protocolo->conclusion) }}
                        @endif
                    </td>
                </tr>
            </table>
            <input type="hidden" name="contador" id="contador" value="0">
            <div class="row">
                <div class="col-md-12"><b>Cargar Protocolo</b></div>
                <div class="col-md-10">
                    <select id="protocolo" name="protocolo" class="js-data-id-protocolo form-control" style="width:100%;" autocomplete="off" >

                    </select>
                </div>
                <div class="col-md-2">
                        <button type="button" class="btn btn-success" onclick="cargar_protocolo($('#protocolo').val())">Agregar</button>
                </div>
            </div>                
            <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="6" style="width: 45%; " class="text-center"> PROTOCOLOS UTILIZADOS QUE JUSTIFICAN LA PRESTACIÓN ADICIONAL REALIZADA:</td>
                </tr>
                <tr>
                    <td class="text-center" rowspan="2" style="width: 5%;">NO.</td>
                    <td class="text-center" rowspan="2" style="width: 23%;">PROTOCOLOS</td>
                    <td class="text-center" colspan="3">TIPO * (SELECCIONE CON UNA X)</td>
                    <td class="text-center" rowspan="2" style="width: 30%;">OBERVACIONES</td>
                    <td class="text-center" rowspan="2" style="width: 30%;">ACCION</td>
                </tr>
                <tr>
                    <td class="text-center">NAC</td>
                    <td class="text-center">INTERN.</td>
                    <td class="text-center">MBE</td>
                </tr>@php $cont = 0; @endphp
                @foreach($protos as $proto)
                @php $cont = $cont + 1; @endphp
                <tr>
                    <td class="text-center">{{ $cont }}</td>
                    <td class="text-center">{{ $proto->maestro_codigo->protocolos }}</td>
                    <td class="text-center">@if($proto->nac == '1') X @endif</td>
                    <td class="text-center">@if($proto->intern == '1') X @endif</td>
                    <td class="text-center">@if($proto->mbe == '1') X @endif</td>
                    <td class="text-center">{{ $proto->observaciones }}</td>
                    <td class="text-center"><button class="btn btn-danger" onclick="eliminar_proto('{{ $proto->id }}')">Eliminar</button></td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="6">*TIPO: NACIONALES (NAC), INTERNACIONALES (INTERN), BUENAS PRACTICAS MÉDICAS BASADAS EN MEDICNA BASADA EN EVIDENCIA (MBE)</td>
                </tr>
            </tbody>
        </table>
            <br>
            <table class="td-border">
                <tr>
                    <td class="borderT centrado" colspan="3">
                        <span class="bold ">
                            PROCEDIMIENTOS ADICIONALES AL AUTORIZADO QUE SERÁN JUSTIFICADOS CON ESTE INFORME:
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="borderT centrado" style="width:33.33%">
                        NO.
                    </td>
                    <td class="borderT centrado" style="width:33.33%">
                        COD. TARIFARIO
                    </td>
                    <td class="borderT centrado" style="width:33.33%">
                        DESCRIPCIÓN
                    </td>
                </tr>@php $cont = 0; @endphp
                @foreach($detalles as $detalle)
                @php $cont = $cont + 1; @endphp
                <tr>
                    <td class="borderT">
                        {{ $cont }}
                    </td>
                    <td class="borderT">
                        {{ $detalle->codigo }}
                    </td>
                    <td class="borderT">
                        {{ $detalle->descripcion }}
                    </td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="3">
                        <span>
                            Nota: Este informe no justifica la prescripción de medicamentos fuera del Cuadro Nacional de Medicamentos Básicos sin la autorización debida según la normativa legal vigente para el caso, Homologaciones de procedimientos y/o laboratorios no autorizadas por el Ministerio de Salud Pública, facturación indebida de acuerdo al Tarifario de Prestaciones de Salud pertinente y otras situaciones de igual similitud.
                        </span>
                    </td>
                </tr>
            </table>
            <table class="td-border">
                <tr>
                    <td class="borderT centrado" style="width:20%">
                    </td>
                    <td class="borderT centrado bold" style="width:20%">
                        NOMBRE
                    </td>
                    <td class="borderT centrado bold" style="width:20%">
                        CARGO/ESPECIALIDAD
                    </td>
                    <td class="borderT centrado bold" style="width:20%">
                        FIRMA Y SELLO
                    </td>
                </tr>
                <tr>
                    <td class="borderT">
                        Elaborado por: 
                    </td>
                    <td class="borderT">
                    @if(!is_null($hc_procedimiento)) @if($hc_procedimiento->doctor_firma != null) {{ $hc_procedimiento->doctor_firma->apellido1}} {{ $hc_procedimiento->doctor_firma->apellido2}} {{ $hc_procedimiento->doctor_firma->nombre1}} {{ $hc_procedimiento->doctor_firma->nombre2}}  @endif @endif
                    </td>
                    <td class="borderT">
                        <input class="form-control" style="width: 50%;" name="doctor_solicitante" id="doctor_solicitante" value="{{ $txt_esp }}" readonly >
                    </td>
                    <td class="borderT">
                    </td>
                </tr>

                <tr>
                    <td class="borderT">
                        Revisado por: 
                    </td>
                    <td class="borderT">
                    @if(!is_null($hc_procedimiento)) @if($hc_procedimiento->doctor_firma != null) {{ $hc_procedimiento->doctor_firma->apellido1}} {{ $hc_procedimiento->doctor_firma->apellido2}} {{ $hc_procedimiento->doctor_firma->nombre1}} {{ $hc_procedimiento->doctor_firma->nombre2}}  @endif @endif
                    </td>
                    <td class="borderT">
                        <input class="form-control" style="width: 50%;" name="doctor_solicitante" id="doctor_solicitante" value="{{ $txt_esp }}" readonly >
                    </td>
                    <td class="borderT">
                    </td>
                </tr>
                <tr>
                    <td class="borderT">
                        Aprobado por: 
                    </td>
                    <td class="borderT">
                    @if(!is_null($hc_procedimiento)) @if($hc_procedimiento->doctor_firma != null) {{ $hc_procedimiento->doctor_firma->apellido1}} {{ $hc_procedimiento->doctor_firma->apellido2}} {{ $hc_procedimiento->doctor_firma->nombre1}} {{ $hc_procedimiento->doctor_firma->nombre2}}  @endif @endif
                    </td>
                    <td class="borderT">
                        <input class="form-control" style="width: 50%;" name="doctor_solicitante" id="doctor_solicitante" value="{{ $txt_esp }}" readonly >

                    </td>
                    <td class="borderT">
                    </td>
                </tr>
            </table>

        </div>
    </div>
</section>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {

        $(".select2").select2();

        $('.js-data-nombre-ruc').select2({
            minimumInputLength: 3,
            cache: true,
            ajax: {
                url: "{{ route('ventas.buscarpaciente_nombre') }}",
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term
                    }
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {

                            return {
                                text: item.value,
                                id: item.nombre
                            }
                        })
                    };
                },
            },
            language: {
                inputTooShort: function() {
                    return 'Por favor ingrese 3 caracteres....';
                },
            },
        })


        $(".js-data-id-ruc").select2({
            minimumInputLength: 3,
            cache: true,
            ajax: {
                url: "{{ route('ventas.buscarpaciente') }}",
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term
                    }
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {

                            return {
                                text: item.nombre,
                                id: item.value
                            }
                        })
                    };
                },
            },
            language: {
                inputTooShort: function() {
                    return 'Por favor ingrese 3 caracteres....';
                },
            },
        });

        $(".js-data-id-protocolo").select2({
            placeholder: "Seleccione protocolo",
            allowClear: true,
            ajax: {
                url: "{{ route('formulario007.buscarProtocolo') }}",
                data: function (params) {
                var query = {
                    search: params.term,
                    type: 'public'
                }
                return query;
                },
                processResults: function (data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    console.log(data);
                    return {
                        results: data
                    };
                }
            }
        });
      
    });

    function agregarFila() {
        let contador = Number($("#contador").val());
        $("#contenido").append(`
            <tr class='fila${contador}'>
                <td rowspan="2"  style="width: 10%;" class="borderT centrado">
                    <input type="text" class="form-control" name="numero" id="numero">
                </td>
                <td rowspan="2"  style="width: 30%;height: auto;" class="borderT centrado">
                    <select id="protocolo" name="protocolo" class="js-data-id-protocolo form-control" style="width:500px;" autocomplete="off" onchange="agregarFila()"></select>
                </td>
            </tr>
            <tr class='fila${contador}'>
                <td class="borderT centrado">
                    <input type="checkbox" name="rad_na" id="nac">
                </td>
                <td class="borderT centrado">
                    <input type="checkbox"  name="rad_inter" id="inter">
                </td>
                <td class="borderT centrado">
                    <input type="checkbox" name="rad_mbe" id="mbe">
                </td>
                <td  style="width: 30%;" class="borderT centrado">
                    <input type="text" class="form-control" name="observaciones" id="observaciones">
                </td>
                 <td class="borderT centrado">
                    <button class="btn btn-danger" onclick="eliminarFila(${contador})"><i class="fa fa-trash" aria-hidden="true"></i></button>
                     <button class="btn btn-success" onclick="guardar(${contador})"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>
                </td>
            </tr>
        `);
        $("#contador").val(contador + 1);
       

    }

    /*$(".js-data-id-protocolo").select2({

        minimumInputLength: 3,
        cache: true,
        ajax: {
            url: "{{ route('formulario007.buscarProtocolo') }}",
            delay: 25,
            data: function(params) {
                return {
                    term: params.term
                }
            },
            processResults: function(data) {
                return {
                    results: $.map(data, function(item) {


                        return {
                            text: item.nombre,
                            id: item.value,
                            numero: item.numero,
                            observaciones: item.observaciones,
                            nac: item.nac,
                            intern: item.intern,
                            mbe: item.mbe,
                        }
                    })
                };
            },
        },
        language: {
            inputTooShort: function() {
                return 'Por favor ingrese 3 caracteres....';
            },
        },
        }).on("change", function(e) {
        let selectedOption = $(this).select2("data")[0];
        $(this).closest('tr').find("#numero").val(selectedOption.numero);
        $(this).closest('tr').next().find("#observaciones").val(selectedOption.observaciones);
        $(this).closest('tr').next().find("#nac").prop("checked", selectedOption.nac === 1);
        $(this).closest('tr').next().find("#inter").prop("checked", selectedOption.intern === 1);
        $(this).closest('tr').next().find("#mbe").prop("checked", selectedOption.mbe === 1);
    });*/

    

    function eliminarFila(contador) {
        $(".fila" + contador).remove();
    }

    function guardar(indice) {

        let datosTabla = [];

        $(".fila" + indice).each(function(index, row) {
            let filaData = {};

            let numero = $(row).find("#numero").val();
            if (numero !== undefined) filaData.numero = numero;

            let protocolo = $(row).find("#protocolo").val();
            if (protocolo !== undefined) filaData.protocolo = protocolo;

            let rad_nac = $(row).find("#nac").prop('checked');
            if (rad_nac !== undefined) filaData.rad_nac = rad_nac;

            let rad_inter = $(row).find("#inter").prop('checked');
            if (rad_inter !== undefined) filaData.rad_inter = rad_inter;

            let rad_mbe = $(row).find("#mbe").prop('checked');
            if (rad_mbe !== undefined) filaData.rad_mbe = rad_mbe;

            let observaciones = $(row).find("#observaciones").val();
            if (observaciones !== undefined) filaData.observaciones = observaciones;
            if (Object.keys(filaData).length > 0) {
                datosTabla.push(filaData);
            }

        });

        let obj3 = {
            ...datosTabla[0],
            ...datosTabla[1]
        };
        $.ajax({
            type: 'post',
            url: "{{route('formulario007.guardar_auditoria')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {

                'id_ap_cabecera' : '{{ $archivo_plano->id }}',
                'datos': obj3,
            },
            success: function(data) {

                if (data.status) {
                    alert(data.msj);
                    location.reload();
                } else {
                    alert(data.msj);
                }

            },
            error: function(data) {
                console.log(data);
            }
        });
    }

    function cargar_protocolo(id){

        $.ajax({
            type: 'post',
            url: "{{route('formulario007.guardar_auditoria')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {

                'id_ap_cabecera' : '{{ $archivo_plano->id }}',
                'id_maestro_codigo': id,
            },
            success: function(data) {

                if (data.status) {
                    alert(data.msj);
                    location.reload();
                } else {
                    alert(data.msj);
                }

            },
            error: function(data) {
                console.log(data);
            }
        });

    }

    function eliminar_proto(id){
       
        $.ajax({
            url: "{{ request()->is('msp_informe_tecnico/*') ? route('formulario007_msp.eliminar_protocolo', ['id' => '__ID__']) : route('formulario007.eliminar_protocolo', ['id' => '__ID__']) }}".replace('__ID__', id),
            type: "get",
            datatype: 'json',
            
            success: function(data) {
                // crearCard(e, ruta, titulo, descripcion);
                location.reload();
            },
            error: function(data) {
    
            }
        })
    }

</script>
@endsection