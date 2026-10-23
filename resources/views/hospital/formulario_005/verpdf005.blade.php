<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Evolución Enfermeria</title>
</head>
<style>
    @page {
        margin-top: 60px;
        margin-left: 30px;
        margin-right: 30px;

    }

    #header {
        position: fixed;
        left: 20px;
        top: -10px;
        right: 20px;
        height: 150px;
        text-align: center;
    }

    /* #content { position: fixed; left: 20px; top: 10px; right: 20px; height: 150px; } */
    #footer {
        position: fixed;
        left: 20px;
        bottom: -90px;
        right: 20px;
        height: 150px;
    }


    .table {
        width: 100% !important;
        border: black 2px solid;
    }



    .small {
        font-size: x-small;
        font-weight: bold;
    }

    .texto {
        font-size: 5;

    }

    .alinear {

        text-align: center !important;
    }

    .celda-titulo td {
        background-color: #5B70B6;
        width: 90px;
        font-weight: bold;
    }

    .textarea {
        width: 100%;
    }

    .alineard {

        text-align: right !important;
    }

    .altura {
        height: 20px;
    }

    .espacio {

        padding: 10px;
    }


    .altura2 {
        height: 20px;
    }

    #paginacion {
        border: 1px solid #CCC;
        background-color: #E0E0E0;
        padding: .5em;
        overflow: hidden;
    }

    .derecha {
        float: right;
    }

    .izquierda {
        float: left;
    }

    .verde {
        background: #CBE6D6;
        font-size: 12px;
    }

    .todo {
        width: 100%;
    }
    .dato{
        font-size: 10px;
    }
</style>

<body>
    <div class="todo">
        <table class="table" border="1" cellspacing="0" cellpadding="0" style="margin-right:25px;">
            <thead>
                <tr class="alinear">
                    <th class="texto espacio verde" scope="col">ESTABLECIMIENTO</th>
                    <th class="texto verde" scope="col">NOMBRE</th>
                    <th class="texto verde" scope="col">APELLIDO</th>
                    <th class="texto verde" colspan="2" scope="col">SEXO</th>
                    <th class="texto verde" scope="col">
                        NUMERO DE HOJA
                    </th>
                    <th class="texto verde" scope="col">HISTORIA CLINICA</th>
                </tr>
            </thead>
            <tbody>
                <tr class="alinear">
                    <td rowspan="2" class="dato" >HOSPITAL</td>
                    <td rowspan="2" class="dato">{{$datoPaciente->nombre1}} {{$datoPaciente->nombre2}}</td>
                    <td rowspan="2" class="dato">{{$datoPaciente->apellido1}} {{$datoPaciente->apellido2}}</td>
                    <td class="dato espacio verde">M</td>
                    <td class="dato espacio verde">F</td>
                    <td rowspan="2">1</td>
                    <td rowspan="2">{{$evolucion->id_solicitud}}</td>
                </tr>
                <tr class="alinear">
                    <td class="texto espacio">@if($datoPaciente->sexo == 1) X @endif</td>
                    <td class="texto">@if($datoPaciente->sexo == 2) X @endif</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="todo" style="margin-top: 5px;">
        <table class="table" border="1" cellspacing="0" cellpadding="0" style="margin-right:25px;">
            <tr class="alinear">
                <td style="width:10px" class="texto espacio verde">FECHA</td>
                <td> {{substr($evolucion->created_at,0,10)}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr class="alinear">
                <td style="width:10px" class="texto espacio verde">DIA DE INTERNACIÒN</td>
                <td>{{substr($evolucion->dato->fecha_ingreso,0,10)}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr class="alinear">
                <td style="width:10px" class="texto espacio verde">DIA POSTQUIRURGICO</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>
    <div class="todo" style="margin-top: 5px;">
        <table class="table" border="1" cellspacing="0" cellpadding="0" style="margin-right:25px;">
            <th>
                @if(is_null($evolucion->imagen_signovitales))
                    <img src="{{asset('grafico.png')}}" style="background-size: cover !important;">
                @else
                    <img src='{{base_path().'/storage/app/hc_ima/'.$evolucion->imagen_signovitales}}' style="background-size: cover !important;">
                @endif
            </th>
        </table>
    </div>
    <div class="todo" style="margin-top: 5px;">
        <table class="table" border="1" cellspacing="0" cellpadding="0" style="margin-right:25px;">
            <tr class="alinear">
                <td style="width:10px" class="texto espacio verde">FRECUENCIA RESPIRATORIA</td>
                <td>{{$evolucion->frec_respiratoria}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr class="alinear">
                <td style="width:10px" class="texto espacio verde">PRESIÒN ARTERIAL</td>
                <td>{{$evolucion->presion_arterial}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>
    <div>
        <table class="table" border="1" cellspacing="0" cellpadding="0" style="margin-right:25px;">
            <th style="background-color: #F0A9C3">1. BALANCE HIDRICO</th>
        </table>
    </div>
    <div class="todo">
        <table class="table" border="1" cellspacing="0" cellpadding="0" style="margin-right:25px;">
            <tr>
                <td class="texto espacio verde" ROWSPAN=3>Ingresos CC</td>
                <td style="width:20px" class="texto espacio verde">Parental</td>
                <td>{{$evolucion->parenteral}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="width:20px" class="texto espacio verde">Via Oral</td>
                <td>{{$evolucion->via_oral}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="width:20px" class="texto espacio verde">Total</td>
                <td>{{$evolucion->total_ingreso}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>
    <div style="page-break-after: always"></div>
    <div>
        <table class="table" border="1" cellspacing="0" cellpadding="0" style="margin-right:25px;">
            <tr>
                <td class="texto espacio verde" ROWSPAN=4>Eliminaciones CC</td>
                <td style="width:20px" class="texto espacio verde">Orina</td>
                <td>{{$evolucion->orina}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="width:20px" class="texto espacio verde">Drenaje</td>
                <td>{{$evolucion->drenaje}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="width:20px" class="texto espacio verde">Otros</td>
                <td>{{$evolucion->otros_elimina}}   </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="width:20px" class="texto espacio verde">Total</td>
                <td>{{$evolucion->total_elimina}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>
    <div>
        <table class="table" border="1" cellspacing="0" cellpadding="0" style="margin-right:25px;">
            <th style="background-color: #F0A9C3">3. MEDICIONES Y ACTIVIDADES</th>
        </table>
    </div>

    <div>
        <table class="table" border="1" cellspacing="0" cellpadding="0" style="margin-right:25px;">
            <tr>
                <td class="texto espacio verde">Aseo / Baño</td>
                <td>{{$evolucion->aseo_bano}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>



            </tr>
            <tr>
                <td style="width:20px" class="texto espacio verde">Peso Kg</td>
                <td>{{$evolucion->peso}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>


            </tr>
            <tr>
                <td style="width:20px" class="texto espacio verde">Dieta Administrada</td>
                <td>{{$evolucion->dieta}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>


            </tr>
            <tr>
                <td style="width:20px" class="texto espacio verde">Numero de Comidas</td>
                <td>{{$evolucion->num_comidas}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="width:20px" class="texto espacio verde">Numero de Micciones</td>
                <td>{{$evolucion->num_micciones}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="width:20px" class="texto espacio verde">Numero de Deposiciones</td>
                <td>{{$evolucion->num_deposiciones}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="width:20px" class="texto espacio verde">Actividad Fisica</td>
                <td>{{$evolucion->actividad_fisica}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="width:20px" class="texto espacio verde">Cambio de Sonda</td>
                <td>{{$evolucion->cambio_sonda}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="width:20px" class="texto espacio verde">Recanalizacion Via</td>
                <td>{{$evolucion->recanalizacion}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="width:20px" class="texto espacio verde">Responsable</td>
                <td>{{$evolucion->responsable}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>
    <div>
        <span>SNS-MSP / HCU-form.4 / 2007</span>
        
        <span style="float: right;margin-left:10px !important">SIGNOS VITALES(1)</span>
    </div>




</body>

</html>