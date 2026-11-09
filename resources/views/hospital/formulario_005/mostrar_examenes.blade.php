<style type="text/css">
    .parent {
        overflow-y: scroll;
        height: 462px;
    }

    .parent::-webkit-scrollbar {
        width: 8px;
    }

    /* this targets the default scrollbar (compulsory) */
    .parent::-webkit-scrollbar-thumb {
        background: #004AC1;
        border-radius: 10px;
    }

    .parent::-webkit-scrollbar-track {
        width: 10px;
        background-color: #004AC1;
        box-shadow: inset 0px 0px 0px 3px #56ABE3;
    }

    .boton-lab {
        font-size: 14px;
        width: 90%;
        background-color: #004AC1;
        color: white;
        text-align: center;
        margin-bottom: 10px;
    }

    .exam-results {
        margin: 0 !important;
        padding: 0 !important;
        color: #24475f;
    }

    .exam-results > .row {
        margin: 0;
    }

    .exam-results__column {
        padding: 0 !important;
        margin: 0 !important;
    }

    .exam-results__section {
        margin: 0 0 18px !important;
        padding: 0 !important;
        overflow: hidden;
        border: 1px solid #d7e7ef !important;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(15, 61, 91, .08);
    }

    .exam-results__section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        min-height: 64px;
        padding: 13px 18px;
        background: linear-gradient(105deg, #064b7a 0%, #087db5 60%, #1db8c8 100%);
        color: #fff;
    }

    .exam-results__section-title {
        display: flex;
        align-items: center;
        gap: 11px;
        min-width: 0;
    }

    .exam-results__section-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 38px;
        width: 38px;
        height: 38px;
        border: 1px solid rgba(255, 255, 255, .35);
        border-radius: 10px;
        background: rgba(255, 255, 255, .12);
        font-size: 17px;
    }

    .exam-results__section-title h3 {
        margin: 0;
        color: #fff;
        font-family: 'Helvetica general3', Helvetica, Arial, sans-serif;
        font-size: 17px;
        font-weight: 700;
    }

    .exam-results__section-title small {
        display: block;
        margin-top: 2px;
        color: rgba(255, 255, 255, .82);
        font-size: 11px;
    }

    .exam-results__patient {
        min-width: 230px;
        padding: 7px 11px;
        border: 1px solid rgba(255, 255, 255, .28);
        border-radius: 10px;
        background: rgba(255, 255, 255, .1);
        text-align: right;
    }

    .exam-results__patient small,
    .exam-results__patient strong {
        display: block;
        color: #fff;
    }

    .exam-results__patient small {
        margin-bottom: 2px;
        color: rgba(255, 255, 255, .76);
        font-size: 9px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .exam-results__patient strong {
        overflow: hidden;
        font-size: 12px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .exam-results__section-body {
        padding: 16px 18px !important;
    }

    .exam-results__orders {
        margin: 0 !important;
    }

    .exam-order {
        margin: 0 0 12px !important;
        overflow: hidden;
        border: 1px solid #dce8ef !important;
        border-radius: 12px !important;
        box-shadow: none !important;
    }

    .exam-order:last-child {
        margin-bottom: 0 !important;
    }

    .exam-order__header {
        display: flex !important;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        min-height: 54px;
        padding: 9px 14px !important;
        border: 0 !important;
        background: linear-gradient(105deg, #0c5e91, #159dbb) !important;
        text-align: left !important;
    }

    .exam-order__date {
        color: #fff !important;
        font-size: 14px;
        font-weight: 700;
    }

    .exam-order__tools {
        position: static !important;
        float: none !important;
        margin-left: auto;
    }

    .exam-order__toggle {
        width: 38px;
        height: 38px;
        padding: 0;
        border: 1px solid rgba(255, 255, 255, .2) !important;
        border-radius: 9px !important;
        background: rgba(0, 75, 142, .68) !important;
        color: #fff !important;
    }

    .exam-order > .box-body {
        border-top: 0;
        background: #fff;
    }

    .exam-order__body {
        padding: 16px !important;
    }

    .exam-order__summary {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(250px, 320px);
        gap: 14px;
        margin-bottom: 14px;
    }

    .exam-order__detail-card,
    .exam-order__progress-card {
        overflow: hidden;
        border: 1px solid #d7e7ef;
        border-radius: 11px;
        background: #fff;
    }

    .exam-order__detail-head,
    .exam-order__tests-head {
        display: flex;
        align-items: center;
        gap: 10px;
        min-height: 45px;
        padding: 10px 14px;
        background: #c9f3f6;
        color: #164a67;
    }

    .exam-order__detail-head i,
    .exam-order__tests-head i {
        color: #079ab3;
        font-size: 17px;
    }

    .exam-order__detail-head strong,
    .exam-order__tests-head strong {
        display: block;
        font-size: 13px;
        font-weight: 700;
    }

    .exam-order__detail-head small {
        display: block;
        color: #618092;
        font-size: 10px;
    }

    .exam-order__meta {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        padding: 15px;
    }

    .exam-order__meta-item {
        min-width: 0;
        padding: 11px 12px;
        border-radius: 9px;
        background: #f5fafc;
    }

    .exam-order__meta-label {
        display: block;
        margin-bottom: 4px;
        color: #6f8797;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .exam-order__meta-value {
        display: block;
        overflow-wrap: anywhere;
        color: #173f5b;
        font-size: 12px;
        font-weight: 700;
    }

    .exam-order__progress-card {
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 15px;
        background: #f8fcfd;
    }

    .exam-order__progress-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 8px;
        color: #315269;
        font-size: 12px;
        font-weight: 700;
    }

    .exam-order__progress-card .progress {
        height: 10px;
        margin: 0 0 13px;
        overflow: hidden;
        border-radius: 999px;
        background: #e7edf2;
        box-shadow: none;
    }

    .exam-order__download {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        width: 100%;
        min-height: 38px;
        margin: 0;
        border: 0;
        border-radius: 9px;
        background: #ef5350;
        color: #fff !important;
        font-size: 12px;
        font-weight: 700;
    }

    .exam-order__download.is-upload {
        background: #24c875;
    }

    .exam-order__download.is-upload:hover,
    .exam-order__download.is-upload:focus {
        background: #1eae67;
    }

    .exam-order__download.is-results {
        background: #ef5350;
    }

    .exam-order__download.is-checking {
        background: #91a7b4;
        cursor: wait;
        opacity: .85;
    }

    .exam-order__tests {
        overflow: hidden;
        border: 1px solid #d7e7ef;
        border-radius: 11px;
        background: #fff;
    }

    .exam-order__tests-body {
        padding: 12px;
    }

    .exam-result-group {
        margin-top: 8px;
        padding: 7px 10px !important;
        border: 0 !important;
        border-radius: 7px 7px 0 0;
        background: #e5f8fa !important;
        text-align: left !important;
    }

    .exam-result-group label {
        margin: 0;
        padding: 0 !important;
        background: transparent !important;
        color: #15506d !important;
        font-size: 11px !important;
        font-weight: 700;
    }

    .exam-result-column-head {
        min-height: 31px;
        padding: 8px 7px;
        background: #f3fafc;
        color: #315269 !important;
        font-size: 9px;
        letter-spacing: .03em;
        text-transform: uppercase;
    }

    .exam-order__tests table td {
        border-color: #e2edf2;
        color: #315269;
        vertical-align: middle;
    }

    .exam-results__empty {
        display: flex;
        align-items: center;
        gap: 11px;
        min-height: 72px;
        padding: 16px;
        border: 1px dashed #bcd9e6;
        border-radius: 10px;
        background: #f7fbfd;
        color: #6c8494;
    }

    .exam-results__empty i {
        color: #19a9bf;
        font-size: 20px;
    }

    .exam-results__table-wrap {
        overflow: hidden;
        border: 1px solid #dce8ef;
        border-radius: 10px;
    }

    .exam-results__table {
        width: 100%;
        margin: 0 !important;
        border: 0 !important;
        background: #fff;
    }

    .exam-results__table thead th {
        padding: 10px 13px;
        border: 0 !important;
        background: #c9f3f6 !important;
        color: #174866;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .03em;
        text-transform: uppercase;
    }

    .exam-results__table tbody td {
        padding: 11px 13px;
        border: 0 !important;
        border-bottom: 1px solid #e4eef3 !important;
        color: #315269;
        vertical-align: middle;
    }

    .exam-results__table tbody tr:last-child td {
        border-bottom: 0 !important;
    }

    .exam-results__view {
        min-width: 82px;
        border: 0;
        border-radius: 8px;
        background: #117bb5;
    }

    @media (max-width: 767px) {
        .exam-results__section-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .exam-results__patient {
            width: 100%;
            min-width: 0;
            text-align: left;
        }

        .exam-results__section-body {
            padding: 12px !important;
        }

        .exam-results__table-wrap {
            overflow-x: auto;
        }

        .exam-order__summary,
        .exam-order__meta {
            grid-template-columns: 1fr;
        }
    }
</style>
<div id="lab_index" class="container-fluid exam-results">
    <div class="row">
        <div class="col-12 exam-results__column">
            <!--Agrupamos la fila en 12 columnas-->
            <div class="col-12 exam-results__section">
                <!--Titulo-->
                <div class="exam-results__section-header">
                    <div class="exam-results__section-title">
                        <span class="exam-results__section-icon"><i class="fa fa-flask"></i></span>
                        <div>
                            <h3>Resultados de Exámenes</h3>
                            <small>Órdenes y resultados recientes</small>
                        </div>
                    </div>
                    @if(!is_null($paciente))
                    <div class="exam-results__patient">
                        <small>Paciente</small>
                        <strong>{{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}} {{$paciente->nombre2}}</strong>
                    </div>
                    @endif
                </div>
                <div class="exam-results__section-body">
                    <!--Hacemos el llamado al estilo parent-->
                    <div>
                        <div class="exam-results__orders">
                            <!-- FOREACH DE LAS ORDENES DE LABORATORIO-->
                            @if(count($ordenes) === 0)
                            <div class="exam-results__empty">
                                <i class="fa fa-flask"></i>
                                <span>No existen resultados de laboratorio registrados.</span>
                            </div>
                            @endif
                            @foreach($ordenes as $value)
                            <div class="box exam-order">
                                <!--Cabecera-->
                                <div class="box-header with-border exam-order__header">
                                    <!--@if(!is_null($value))
                        @php
                          $fecha = substr($value->fecha_orden,0,10);
                          $invert = explode( '-',$fecha);
                          $fecha_invert = $invert[2]."-".$invert[1]."-".$invert[0]; 
                        @endphp
                         <span> <b style="font-family: 'Helvetica';" class="box-title">{{$fecha_invert}}</b></span>
                      @endif-->

                                    @if(!is_null($value))
                                    @php
                                    $fecha_orden = $value->fecha_orden;
                                    $dia = Date('N',strtotime($value->fecha_orden));
                                    $mes = Date('n',strtotime($value->fecha_orden));
                                    @endphp

                                    <span class="exam-order__date"> <b class="box-title exam-order__date">
                                            @if($dia == '1') Lunes
                                            @elseif($dia == '2') Martes
                                            @elseif($dia == '3') Miércoles
                                            @elseif($dia == '4') Jueves
                                            @elseif($dia == '5') Viernes
                                            @elseif($dia == '6') Sábado
                                            @elseif($dia == '7') Domingo
                                            @endif
                                            {{substr($value->fecha_orden,8,2)}} de
                                            @if($mes == '1') Enero
                                            @elseif($mes == '2') Febrero
                                            @elseif($mes == '3') Marzo
                                            @elseif($mes == '4') Abril
                                            @elseif($mes == '5') Mayo
                                            @elseif($mes == '6') Junio
                                            @elseif($mes == '7') Julio
                                            @elseif($mes == '8') Agosto
                                            @elseif($mes == '9') Septiembre
                                            @elseif($mes == '10') Octubre
                                            @elseif($mes == '11') Noviembre
                                            @elseif($mes == '12') Diciembre
                                            @endif
                                            del {{substr($value->fecha_orden,0,4)}}</b></span>
                                    @endif


                                    <div class="box-tools exam-order__tools">
                                        <button type="button" class="btn btn-info btn-sm exam-order__toggle" data-toggle="collapse" data-target="#demo{{$value->id}}" onclick="move({{$value->id}})" aria-label="Mostrar detalle de la orden">
                                            <i class="fa fa-plus" id="port{{$value->id}}"></i>
                                        </button>
                                    </div>
                                </div>
                                <!--Cuerpo-->
                                <div class="box-body collapse exam-order__body" id="demo{{$value->id}}">
                                    @php
                                    $nombreDoctor = trim(implode(' ', array_filter([
                                        $value->dnombre1,
                                        $value->dnombre2,
                                        $value->dapellido1,
                                        $value->dapellido2,
                                    ])));
                                    if ($nombreDoctor === '') {
                                        $nombreDoctor = trim($value->cnombre1.' '.$value->capellido1);
                                    }
                                    @endphp
                                    <div class="exam-order__summary">
                                        <section class="exam-order__detail-card">
                                            <header class="exam-order__detail-head">
                                                <i class="fa fa-clipboard"></i>
                                                <div>
                                                    <strong>{{trans('transquirofano.DetalledelaOrdendeLaboratorio')}}</strong>
                                                    <small>Orden de laboratorio #{{$value->id}}</small>
                                                </div>
                                            </header>
                                            <div class="exam-order__meta">
                                                <div class="exam-order__meta-item">
                                                    <span class="exam-order__meta-label">{{trans('transquirofano.Médico')}}</span>
                                                    <span class="exam-order__meta-value">{{$nombreDoctor !== '' ? $nombreDoctor : 'No registrado'}}</span>
                                                </div>
                                                <div class="exam-order__meta-item">
                                                    <span class="exam-order__meta-label">{{trans('transquirofano.Seguro')}}</span>
                                                    <span class="exam-order__meta-value">{{$value->snombre ?: 'No registrado'}}</span>
                                                </div>
                                            </div>
                                        </section>
                                        <aside class="exam-order__progress-card">
                                            <div class="exam-order__progress-title">
                                                <span>{{trans('transquirofano.Progreso')}}</span>
                                                <span id="sp{{$value->id}}">0%</span>
                                            </div>
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="td{{$value->id}}"></div>
                                            </div>
                                            <button id="resultAction{{$value->id}}" type="button" class="btn exam-order__download is-checking" disabled>
                                                <i class="fa fa-spinner fa-spin"></i>
                                                <span>Verificando resultados...</span>
                                            </button>
                                        </aside>
                                    </div>
                                    <!--Contenedor Listado de Examenes de Laboratorio-->
                                    <section class="exam-order__tests" id="{{$value->id}}">
                                        <header class="exam-order__tests-head">
                                            <i class="fa fa-list-alt"></i>
                                            <strong>{{trans('transquirofano.Listadodeexamenesdelaboratorio')}}</strong>
                                        </header>
                                            <div class="exam-order__tests-body">
                                                <!--Realizamos la Consulta a la tabla Examen_Detalle-->
                                                <div class="row">
                                                    @php
                                                    $orden = Sis_medico\Examen_Orden::find($value->id);
                                                    $detalle = Sis_medico\Examen_Detalle::where('id_examen_orden',$orden->id)->join('examen as e','e.id','id_examen')->select('examen_detalle.*','e.secuencia')->orderBy('e.secuencia')->get();
                                                    $resultados = $orden->resultados;
                                                    @endphp
                                                    @if($orden->seguro->tipo=='0')
                                                    @php
                                                    $agrupador = Sis_medico\Examen_Agrupador::all();
                                                    @endphp
                                                    @else
                                                    @php
                                                    $agrupador = Sis_medico\Examen_Agrupador_labs::orderBy('secuencia')->get();
                                                    @endphp
                                                    @endif
                                                    @php
                                                    $parametros = Sis_medico\Examen_Parametro::orderBy('orden')->get();
                                                    @endphp
                                                    <br>
                                                    @foreach($agrupador as $value)
                                                    @php
                                                    $i_agrupador=0;
                                                    $xycont = 0;
                                                    @endphp
                                                    @foreach($detalle as $value_detalle)
                                                    @php
                                                    $i=0;
                                                    $xycont ++;
                                                    if($orden->seguro->tipo == '0' ){
                                                    $id_agrupador = $value_detalle->examen->id_agrupador;
                                                    }else{
                                                    $agrupador_part = DB::table('examen_agrupador_sabana')->where('id_examen',$value_detalle->examen->id)->first();
                                                    $id_agrupador = 0;

                                                    if(!is_null($agrupador_part)){
                                                    $id_agrupador = $agrupador_part->id_examen_agrupador_labs;
                                                    }
                                                    }
                                                    @endphp
                                                    @if($value_detalle->examen->no_resultado=='0')
                                                    @if($id_agrupador == $value->id)
                                                    @php
                                                    if($value_detalle->examen->sexo_n_s=='0'){
                                                    $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo','3');
                                                    }else{
                                                    $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo',$orden->paciente->sexo);
                                                    }
                                                    @endphp
                                                    <div class="col-12">
                                                        @if($i_agrupador == 0)
                                                        <!--Contenedor Nombre Agrupador-->
                                                        <div class="exam-result-group">
                                                            <!--Nombre Agrupador-->
                                                            <label>{{$value->nombre}}</label>
                                                            @php
                                                            $i_agrupador=1;
                                                            @endphp
                                                        </div>
                                                        <div class="exam-result-column-head" style="width: 28%;float:left;"><b>{{trans('transquirofano.NOMBRE')}}</b></div>
                                                        <div class="exam-result-column-head" style="width: 18%;float:left;"><b>{{trans('transquirofano.RESULTADO')}}</b></div>
                                                        <div class="exam-result-column-head" style="width: 13%;float:left;"><b>{{trans('transquirofano.UNIDADES')}}</b></div>
                                                        <div class="exam-result-column-head" style="width: 40%;float:left;"><b>{{trans('transquirofano.REFERENCIA')}}</b></div>
                                                        <div style="clear:both;"></div>
                                                        @endif
                                                        @if($i == 0)
                                                        @if($parametro_nuevo->count() > 1)
                                                        @if($value_detalle->id_examen!='639')
                                                        <div class="col-12" style="border-bottom: 1px solid #004AC1;padding: 0px">
                                                            <b>{{$value_detalle->examen->nombre}}</b>
                                                        </div>
                                                        @if($value_detalle->examen->texto!=null)
                                                        <div style="border-bottom: 1px solid #009a98;padding: 0px;">
                                                            <?php echo $value_detalle->examen->texto; ?>
                                                        </div>
                                                        @endif
                                                        @endif
                                                        @endif
                                                        @php
                                                        $i = 1;
                                                        @endphp
                                                        @endif
                                                        <div style="clear:both;"></div>
                                                        <div class="col-12" @if($parametro_nuevo->count() == 1) style="padding-left: 10px;" @endif>
                                                            <table id="example2" style="font-size: 12px;  width: 100%">
                                                                <tbody>
                                                                    @if($value_detalle->id_examen=='661')
                                                                    <tr style="font-size: 12px;">
                                                                        <td style="width: 20%;"><b>{{trans('transquirofano.GRADO')}}</b></td>
                                                                        <td style="width: 10%;"><b>{{trans('transquirofano.%CELULAS')}}</b></td>
                                                                        <td style="width: 8%;"><b>{{trans('transquirofano.LAPPUNTOS')}}</b></td>
                                                                        <td style="width: 40%;"><b>{{trans('transquirofano.REFERENCIA')}}</b></td>
                                                                    </tr>
                                                                    @endif
                                                                    @if($value_detalle->id_examen=='661')
                                                                    @foreach($parametro_nuevo as $value_agrupador)
                                                                    @if($value_agrupador->unidad1=='L.A.P. PUNTOS')
                                                                    @php
                                                                    $resultado = $orden->resultados->where('id_parametro', $value_agrupador->id)->first();
                                                                    @endphp
                                                                    <tr role="row">
                                                                        <td style="width: 30%;">
                                                                            {{$value_agrupador->nombre}}
                                                                        </td>
                                                                        @php
                                                                        $hermano = DB::table('examen_parametro')->where('id_examen',$value_detalle->id_examen)->where('orden',$value_agrupador->orden)->where('unidad1','% CELULAS')->first();
                                                                        $resultado_2 = null;
                                                                        if(!is_null($hermano)){
                                                                        $resultado_2 = $orden->resultados->where('id_parametro', $hermano->id)->first();
                                                                        }
                                                                        @endphp
                                                                        <td style="width: 20%;">
                                                                            <div style="word-wrap: break-word;">
                                                                                @if(!is_null($hermano))
                                                                                @if(!is_null($resultado_2))
                                                                                @if($resultado_2->certificado=='1')
                                                                                {{$resultado_2->valor}}
                                                                                @else
                                                                                {{"0"}}
                                                                                @endif
                                                                                @endif
                                                                                @endif
                                                                            </div>
                                                                        </td>
                                                                        <td style="width: 10%;">
                                                                            @if(!is_null($resultado))
                                                                            {{$resultado->valor}}
                                                                            @else
                                                                            {{"0"}}
                                                                            @endif
                                                                        </td>
                                                                        <td style="width: 40%;">
                                                                            @if($value_agrupador->texto_referencia == "")
                                                                            {{$value_agrupador->valor1}} - {{$value_agrupador->valor1g}}
                                                                            @else
                                                                            <?php echo
                                                                            $value_agrupador->texto_referencia;
                                                                            ?>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    @endif
                                                                    @endforeach

                                                                    @elseif($value_detalle->id_examen=='639')
                                                                    <center>
                                                                        <b>{{trans('transquirofano.ALERGIASAALIMPORIGG59')}}</b>
                                                                    </center>
                                                                    <br>
                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <div style="background-color: #ACCBEE;font-size: 12px;text-align: center;">{{trans('transquirofano.ReacciónLeve')}}</div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div style="background-color: #8AB0DB;font-size: 12px;text-align: center;">{{trans('transquirofano.ReacciónModerada')}}</div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div style="background-color: #376EAC;color: white;font-size: 12px;text-align: center;">{{trans('transquirofano.ReacciónFuerte')}}</div>
                                                                        </div>
                                                                    </div>
                                                                    <br>
                                                                    @php $cuenta=0;
                                                                    @endphp
                                                                    <tr role="row">
                                                                        @foreach($parametro_nuevo as $value_agrupador)
                                                                        @php

                                                                        $rvalor=0;

                                                                        $resultado = $resultados->where('id_parametro',$value_agrupador->id)->first();

                                                                        if(!is_null($resultado)){
                                                                        $rvalor=$resultado->valor;

                                                                        }


                                                                        @endphp

                                                                        @if($value_agrupador->orden=='46' || $value_agrupador->orden=='48')
                                                                        @if($cuenta == 0)
                                                                    <tr> @endif
                                                                        <td style="width: 3%; font-size: 10px; padding:0;">
                                                                            {{$value_agrupador->sec}}
                                                                        </td>
                                                                        <td style="width: 12%;  font-size: 10px;padding:0;">
                                                                            {{$value_agrupador->nombre}}
                                                                        </td>
                                                                        <td style="width: 3%;padding:0;">
                                                                        </td>
                                                                        <td style="width: 3%;padding:0;">
                                                                        </td>
                                                                        <td style="width: 3%; background-color:#A8A8A8; border: 1px solid #FFF;text-align: center; font-size: 13px;padding:0;">@if($value_agrupador->orden=='48') <span style="color: white">x</span> @else &nbsp; @endif
                                                                        </td>
                                                                        <td style="width: 3%;  font-size: 10px; padding:0;">
                                                                            &nbsp;
                                                                        </td>
                                                                        @php $cuenta++; @endphp
                                                                        @if($cuenta == 2)

                                                                    </tr>
                                                                    @endif
                                                                    @else
                                                                    @if($cuenta == 0) <tr> @endif
                                                                        <td style="width: 3%;  font-size: 10px; padding:0;">
                                                                            {{$value_agrupador->sec}}
                                                                        </td>
                                                                        <td style="width: 12%;  font-size: 10px;padding:0;">
                                                                            {{$value_agrupador->nombre}}
                                                                        </td>
                                                                        <td style="width: 3%; background-color:#ACCBEE ; border: 1px solid #FFF;text-align: center;font-size: 13px;padding:0;">
                                                                            @if($rvalor==1) @if($resultado->certificado=='1') X @endif @else &nbsp; @endif
                                                                        </td>
                                                                        <td style="width: 3%; background-color:#8AB0DB; border: 1px solid #FFF;text-align: center; font-size: 13px;padding:0;">
                                                                            @if($rvalor==2) @if($resultado->certificado=='1') X @endif @else &nbsp; @endif
                                                                        </td>
                                                                        <td style="width: 3%; background-color:#376EAC; border: 1px solid #FFF;text-align: center; font-size: 13px;padding:0;">
                                                                            @if($rvalor==3) <span style="color: white">@if($resultado->certificado=='1') X @endif</span> @else &nbsp; @endif
                                                                        </td>
                                                                        <td style="width: 3%;  font-size: 10px; padding:0;">
                                                                            &nbsp;
                                                                        </td>
                                                                        @php $cuenta++; @endphp
                                                                        @if($cuenta == 2)
                                                                    </tr>
                                                                    @php
                                                                    $cuenta=0;
                                                                    @endphp
                                                                    @endif  
                                                                    @endif
                                                                    @endforeach

                                                                    </tr>
                                                                    <table>
                                                                        <tr role="row">
                                                                            <br>
                                                                            <br>
                                                                            <div>{{trans('transquirofano.*19PescadoBlanco')}}</div>
                                                                            <div>{{trans('transquirofano.*20PescadodeAguaDulce')}}</div>
                                                                            <div>{{trans('transquirofano.*22MariscoMix')}}</div>
                                                                            <div>{{trans('transquirofano.*30Mezclasdepimientos')}}</div>
                                                                            <div>{{trans('transquirofano.*31Leguminosas')}}</div>
                                                                            <div>{{trans('transquirofano.*33MelónMix:MelónySandía')}}</div>
                                                                            <div style="text-align: justify;">{{trans('transquirofano.Sisusresultadosindican')}}</div>
                                                                            <div>{{trans('transquirofano.Algunaspersonas')}}</div>


                                                                        </tr>
                                                                    </table>

                                                                    @else
                                                                    
                                                                    @if(!(($value_detalle->id_examen == '414' || $value_detalle->id_examen == '412' || $value_detalle->id_examen == '680') && $orden->created_at<'2018-12-17' && $orden->id!='1543' && $orden->id!='1291'))
                                                                        @foreach($parametro_nuevo as $value_agrupador)
                                                                        @php
                                                                        $resultado = $orden->resultados
                                                                        ->where('id_parametro', $value_agrupador->id)
                                                                        ->first();
                                                                        @endphp
                                                                     
                                                                        @if(!is_null($resultado))
                                                                        @if($resultado->certificado=='1')
                                                                        <tr role="row">

                                                                            <td style="width: 30%;@if($parametro_nuevo->count() == 1) font-weight: 700;padding-left: -3px;font-size: 14px; @endif">
                                                                                {{$value_agrupador->nombre}}
                                                                            </td>

                                                                            <td style="width: 20%;">
                                                                                <div style="word-wrap: break-word;">
                                                                                    @if(!is_null($resultado))
                                                                                    {{$resultado->valor}}
                                                                                    @else
                                                                                    {{"0"}}
                                                                                    @endif
                                                                                </div>
                                                                            </td>
                                                                            @php
                                                                            $fl_ref = 0;
                                                                            $ref_ant = Sis_medico\Examen_Parametro_Referencia_Anterior::where('id_parametro',$value_agrupador->id)->where('fecha_valida','>',$orden->fecha_orden)->orderBy('fecha_valida','asc')->first();
                                                                            if(!is_null($ref_ant)){
                                                                            $fl_ref = 1;
                                                                            }
                                                                            @endphp
                                                                            <td style="width: 10%;">
                                                                                @if($fl_ref) {{$ref_ant->unidad1}} @else {{$value_agrupador->unidad1}} @endif
                                                                            </td>

                                                                            <td style="width: 40%;">
                                                                                @if($fl_ref)
                                                                                @if($ref_ant->texto_referencia == "")
                                                                                {{$ref_ant->valor1}} - {{$ref_ant->valor1g}}
                                                                                @else
                                                                                <?php echo $ref_ant->texto_referencia; ?>
                                                                                @endif
                                                                                @else
                                                                                @if($value_agrupador->texto_referencia == "")
                                                                                {{$value_agrupador->valor1}} - {{$value_agrupador->valor1g}}
                                                                                @else
                                                                                <?php echo $value_agrupador->texto_referencia; ?>
                                                                                @endif
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                        @endif
                                                                        @endif
                                                                        @endforeach

                                                                        @if($value_detalle->examen->tiene_detalle=='1')
                                                                        @php
                                                                        $sub_resultados = DB::table('examen_sub_resultado')
                                                                        ->where('id_orden',$orden->id)
                                                                        ->where('estado','1')
                                                                        ->where('id_examen',$value_detalle
                                                                        ->id_examen)->get();
                                                                        @endphp
                                                                        <tr>
                                                                            <td colspan="4" id="sub_tabla">
                                                                                <table class="table b-table">
                                                                                    <tbody>
                                                                                        @foreach($sub_resultados as $sub)
                                                                                        <tr>
                                                                                            <td style="width: 300px !important;padding-top: 1px;padding-bottom: 1px;">{{$sub->campo1}}</td>
                                                                                            <td style="width: 300px !important;padding-top: 1px;padding-bottom: 1px;">{{$sub->campo2}}</td>
                                                                                            <td style="width: 300px !important;padding-top: 1px;padding-bottom: 1px;">{{$sub->campo3}}</td>
                                                                                        </tr>
                                                                                        @endforeach
                                                                                    </tbody>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                        @endif
                                                                        @endif
                                                                        @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    @endif
                                                    @endforeach
                                                    @endforeach
                                                </div>
                                            </div>
                                    </section>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function move(id) {
        var icon = document.getElementById("port" + id);
        if (!icon) return;
        icon.className = icon.className.indexOf('fa-minus') !== -1 ? 'fa fa-plus' : 'fa fa-minus';
    }

    function descargar(id_or) {
        var cert = $('#sp' + id_or).text();
        if (cert == '0%') {
            alert("Sin Exámenes Ingresados");
        } else {
            window.open("{{url('hospital/mostrar/resultados/imprimir')}}/" + id_or, '_blank');

        }
    }
    $(document).ready(function($) {
        @foreach($ordenes as $value)
        $.ajax({
            type: 'get',
            url: "{{ route('valida.puede_imprimir',['id_orden' => $value->id]) }}",
            success: function(data) {
                console.log(data);
                if (data.cant_par == 0) {
                    var pct = 0;
                    //console.log(pct);  
                } else {
                    var pct = data.certificados / data.cant_par * 100;
                    //console.log(pct); 
                }
                //alert(pct);
                //$('#st').val(pct);
                if (pct <= 0) {
                    document.getElementById("{{$value->id}}").style.display = "none";
                }
                $('#td{{$value->id}}').css("width", Math.round(pct) + "%");
                $('#td{{$value->id}}').attr('aria-valuenow', Math.round(pct));
                $('#sp{{$value->id}}').text(Math.round(pct) + "%");
                var actionButton = $('#resultAction{{$value->id}}');
                actionButton.prop('disabled', false).removeClass('is-checking is-upload is-results').off('click');
                if (pct <= 0) {
                    actionButton.addClass('is-upload');
                    actionButton.find('i').attr('class', 'fa fa-upload');
                    actionButton.find('span').text('Subir resultados');
                    actionButton.on('click', function() {
                        window.open("{{route('hospital.laboratory.index', ['id_solicitud' => $solicitud->id])}}", '_blank');
                    });
                } else {
                    actionButton.addClass('is-results');
                    actionButton.find('i').attr('class', 'fa fa-download');
                    actionButton.find('span').text("{{trans('transquirofano.Resultados')}}");
                    actionButton.on('click', function() {
                        descargar({{$value->id}});
                    });
                }
                if (pct < 10) {
                    $('#td{{$value->id}}').addClass("progress-bar-danger");
                } else if (pct >= 10 && pct < 90) {
                    $('#td{{$value->id}}').addClass("progress-bar-warning");
                } else {
                    $('#td{{$value->id}}').addClass("progress-bar-success");
                }
            },

            error: function(data) {
                var actionButton = $('#resultAction{{$value->id}}');
                actionButton.prop('disabled', true).removeClass('is-upload is-results').addClass('is-checking');
                actionButton.find('i').attr('class', 'fa fa-exclamation-triangle');
                actionButton.find('span').text('No se pudo verificar');
            }

        });

        @endforeach
    });
</script>
