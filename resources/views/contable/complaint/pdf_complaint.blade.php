<!DOCTYPE html>
<html>
@php
$fecha=date('Y-m-d');
@endphp

<head>
  <title>PDF COMPLAINT</title>
  <style>
    @page {
      margin: 15 0;
    }

    * {
      font-size: 15px;
      text-transform: capitalize;
    }

    /** Define now the real margins of every page in the PDF **/
    body {
      margin-top: 40px;
      margin-left: 20px;
      margin-right: 20px;
      margin-bottom: 1px;
      font-size: 10px;
    }

    /** Define the header rules **/
    header {
      position: fixed;
      top: 0cm;
      left: 0cm;
      right: 0cm;
      height: 1cm;
    }

    /** Define the footer rules **/
    footer {
      position: fixed;
      bottom: 0cm;
      left: 0cm;
      right: 0cm;
      height: 2cm;
    }

    table {
      width: 100px;
      border-collapse: collapse;
    }

    .table2d {
      border: none;
      text-align: center;
    }

    .titulo {
      background-color: #C7C3C2;
    }

    p {
      padding-top: 0;
      padding-bottom: 0;
      margin: 0;
    }

    div {
      padding-top: 0;
      padding-bottom: 0;
    }

    .txt_vertical {
      writing-mode: vertical-lr;
      transform: rotate(-90deg);
      letter-spacing: 2px;
      word-wrap: break-word;
    }

    .td-border {
      border: 1px solid;
    }

    .td-not-border-bottom {
      border-top: 1px solid;
      border-left: 1px solid;
      border-right: 1px solid;
    }

    .td-not-border-top {
      border-bottom: 1px solid;
      border-left: 1px solid;
      border-right: 1px solid;
    }

    .td-not-border-right {
      border-bottom: 1px solid;
      border-left: 1px solid;
      border-top: 1px solid;
    }

    .td-not-border-left {
      border-bottom: 1px solid;
      border-top: 1px solid;
      border-right: 1px solid;
    }

    .td-border-top {
      border-top: 1px solid;
    }

    .td-border-bottom {
      border-bottom: 1px solid;
    }

    .td-border-left {
      border-left: 1px solid;
    }

    .td-border-right {
      border-right: 1px solid;
    }

    .bg-blue {
      background-color: #DDEBF7;
    }

    .bg-green {
      background-color: #BADAC8;
    }

    .bg-yellow {
      background-color: #F1FD77;
    }

    .text-center {
      text-align: center;
    }

    .text-left {
      text-align: left;
    }

    .text-right {
      text-align: right;
    }

    .text-right {
      text-align: right;
    }

    .page-break-before {
      page-break-before: always;
    }

    .page-break-after {
      page-break-after: always;
    }
  </style>
</head>

<body>
  <header>
  </header>
  <main>
    <table style="height: 80px; width: 80%;">
      <tr>
        <td>
          <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" style="height: 150px; width: 300px; margin: 15px; display: inline-block;">
          <p style="text-align: right; font-size: 12;"><b>Fecha:</b> &nbsp;&nbsp;{{$fecha}}</p>
        </td>
      </tr>
    </table>
    <table border="1" style="width: 100%;">
      <td></td>
    </table>
    <br>
    <table style="width: 100%">
      <tbody>
        <tr>
          <td class="text-center" style="font-size: 15px;"><b>
              Registro de tramitación de reclamaciones de los clientes
            </b></td>
        </tr>
      </tbody>
    </table>
    <br>
    <br>
    <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
      <thead>
        <tr>
          <td colspan="4" style="font-size: 20px; height: 40px;" class="bg-blue"><b>Información sobre reclamaciones</b></td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="bg-blue" style="height: 30px; width: 18%; font-size: 11px;"><b>Nombre de la Institucion:</b></td>
          <td class="text-center">{{$data->nombre}}</td>
          <td class="bg-blue" style="height: 30px; width: 18%; font-size: 11px;"><b>Medico Responsable:</b></td>
          <td class="text-center">{{$data->medico}}</td>
        </tr>
        <tr>
          <td class="bg-blue" style="height: 30px; width: 18%; font-size: 11px;"><b>Especialista Responsable de la Cuenta</b></td>
          <td class="text-center">{{$data->especialista}}</td>
          <td class="bg-blue" style="height: 30px; width: 18%; font-size: 11px;"><b>Nombre del Procedimiento</b></td>
          <td class="text-center">{{$data->procedimiento}}</td>
        </tr>
        <tr>
          <td class="bg-blue" style="height: 30px; width: 18%; font-size: 11px;"><b>Código</b></td>
          <td class="text-center">{{$data->codigo}}</td>
          <td class="bg-blue" style="height: 30px; width: 18%; font-size: 11px;"><b>Cantidad</b></td>
          <td class="text-center">{{$data->cantidad}}</td>
        </tr>
        <tr>
          <td class="bg-blue" style="height: 30px; width: 18%; font-size: 11px;"><b>Numero de Lote</b></td>
          <td class="text-center">{{$data->numero_lote}}</td>
          <td class="bg-blue" style="height: 30px; width: 18%; font-size: 11px;"><b>Dispositivo Nuevo o Reutilizado</b></td>
          <td class="text-center">{{$data->dispositivo}}</td>
        </tr>
        <tr>
          <td class="bg-blue" style="height: 30px; width: 18%; font-size: 11px;"><b>Fecha de Vencimiento</b></td>
          <td class="text-center">{{$data->fecha_vencimiento}}</td>
          <td class="bg-blue" style="height: 30px; width: 18%; font-size: 11px;"><b>Fecha que se Reporta</b></td>
          <td class="text-center">{{$data->fecha_reporte}}</td>
        </tr>
        <tr>
          <td class="bg-blue" style="height: 30px; width: 18%; font-size: 11px;"><b> Fecha que se Daño</b></td>
          <td class="text-center">{{$data->fecha_danio}}</td>
          <td class="bg-blue" style="height: 30px; width: 18%; font-size: 11px;"><b>Descripcion del Dispositivo</b></td>
          <td class="text-center">{{$data->descripcion}}</td>
        </tr>
        <tr>
          <td class="bg-blue" style="height: 30px; width: 18%; font-size: 11px;"><b>Resolucion del Procedimiento</b></td>
          <td colspan="3" class="text-center">{{$data->resolucion}}</td>
        </tr>
        <tr>
          <td class="bg-blue" style="height: 30px; width: 18%; font-size: 11px;"><b>Observación</b></td>
          <td colspan="3" class="text-center">{{$data->observacion}}</td>
        </tr>
        <tr>
          <td rowspan="3" class="bg-blue" style="height: 30px; width: 18%; font-size: 11px;"><b>Investigación externa</td>
          <td colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" @if($data->momento_dano == "antes") checked @endif>&nbsp;&nbsp;&nbsp;Antes</td>
        </tr>
        <tr>
          <td colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" @if($data->momento_dano == "durante") checked @endif>&nbsp;&nbsp;&nbsp;Durante</td>
        </tr>
        <tr>
          <td colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" @if($data->momento_dano == "despues") checked @endif>&nbsp;&nbsp;&nbsp;Después</td>
        </tr>
        <tr>
          <td class="bg-blue" style="height: 30px;"><b>Condicion del paciente:</b></td>
          <td colspan="3" class="text-center">{{$data->condicion}}</td>
        </tr>
        <tr>
          <td colspan="4" class="bg-blue text-center" style="height: 30px; font-size: 11px;"><b>Descripcion de lo sucedido con el Dispositivo</b></td>
        </tr>
        <tr>
          <td colspan="4" style="height: 70px; width: 90%; text-center">
            <textarea style="border: none;"><b>{{$data->descripcion_suceso}}</b></textarea>
          </td>
        </tr>
        <tr>
          <td colspan="4" class="bg-blue text-center" style="height: 30px; font-size: 11px;"><b>Documento de Entrega</b></td>
        </tr>
        <tr>
          <td colspan="4" style="height: 125px; width: 100%; text-align: center; vertical-align: middle ;">
            @if($data->files != null)
            <img style="height: 500px; width: 500px; margin: 15px; display: inline-block;" src="{{ public_path().'/storage/'.$data->files }}">
            @endif
          </td>
        </tr>
      </tbody>
    </table>
  </main>
</body>
</html>