<html>
  <head>
      <meta charset="utf-8">
    <title>Receta medica</title>
    <style type="text/css">
      .div_tiny p{
        margin: 0;
      }
      @page { margin: 0px; }
      body { margin: 0px; }
    </style>
  </head>
  <body>

    @php

        $date = $historia->fecha_atencion;

    @endphp
    <div style="width:100%;height: 19px;">&nbsp;</div>
    <br>
    <br>

    <table style="width:100%;margin-left: 30px;margin-top: 3px !important;">
      <tr style="width:100%;">
        <td style="width:50%;"><img style="width:500px; height: 106px;" src="" /></td>
        <td style="width:50%;"><img style="width:500px;height: 106px;" src="" /></td>
      </tr>

      <tr style="width: 100%;">
        <td style="margin-top: 11px;"><b>Fecha: </b> {{substr($receta->created_at, 8, 2)}} de <?php $mes = substr($receta->created_at, 5, 2);if ($mes == 01) {echo "Enero";}if ($mes == 02) {echo "Febrero";}if ($mes == 03) {echo "Marzo";}if ($mes == 04) {echo "Abril";}if ($mes == 05) {echo "Mayo";}if ($mes == 06) {echo "Junio";}if ($mes == 07) {echo "Julio";}if ($mes == '08') {echo "Agosto";}if ($mes == '09') {echo "Septiembre";}if ($mes == '10') {echo "Octubre";}if ($mes == '11') {echo "Noviembre";}if ($mes == '12') {echo "Diciembre";}?> del {{substr($receta->created_at, 0, 4)}}</td>
        <td style="margin-top: 11px;"><b>Fecha: </b> {{substr($receta->created_at, 8, 2)}} de <?php $mes = substr($receta->created_at, 5, 2);if ($mes == 01) {echo "Enero";}if ($mes == 02) {echo "Febrero";}if ($mes == 03) {echo "Marzo";}if ($mes == 04) {echo "Abril";}if ($mes == 05) {echo "Mayo";}if ($mes == 06) {echo "Junio";}if ($mes == 07) {echo "Julio";}if ($mes == '08') {echo "Agosto";}if ($mes == '09') {echo "Septiembre";}if ($mes == '10') {echo "Octubre";}if ($mes == '11') {echo "Noviembre";}if ($mes == '12') {echo "Diciembre";}?> del {{substr($receta->created_at, 0, 4)}}</td>
      </tr>
      
      <tr style="width: 100%; padding-right: 10px;">
        <td><b>Nombre: </b> {{ $paciente->nombre1}} @if($paciente->nombre2 != "(N/A)"){{ $paciente->nombre2}}@endif {{ $paciente->apellido1}} @if($paciente->apellido2 != "(N/A)"){{ $paciente->apellido2}}@endif</td>
        <td><b>Nombre: </b> {{ $paciente->nombre1}} @if($paciente->nombre2 != "(N/A)"){{ $paciente->nombre2}}@endif {{ $paciente->apellido1}} @if($paciente->apellido2 != "(N/A)"){{ $paciente->apellido2}}@endif</td>
      </tr>
      <tr style="width: 100%;">
          <td ><b>CI.</b> {{$paciente->id}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Edad: </b>{{$edad}} años</td>
          <td ><b>CI.</b> {{$paciente->id}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Edad: </b>{{$edad}} años</td>
      </tr>
        <tr style="width: 100%">
        <td><b>Rp:</b></td>
        <td><b>Prescripción:</b></td>
      </tr>
      <tr>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
      </tr>
     
      @foreach($detalles as $value)
      @php 
        $medicina = $medicinas->where('id',$value->id_medicina)->first();
        $genericos = $medicina->genericos; 
      @endphp
      <tr>
        <td><p style="width: 95%; border: none; margin:0; font-size: 22px !important;">{{$value->medicina->nombre}}( @foreach($genericos as $gen) {{$gen->generico->nombre}} @endforeach ): {{$value->cantidad}}</p></td>
        <td><p style="width: 95%; border: none; margin:0; font-size: 22px !important;">{{$value->medicina->nombre}}: {{$value->dosis}}</p></td>
      </tr>
      @endforeach
      
    </table>
      <div class="div_tiny" style="width: 45%;line-height: 1.2; float:left; margin-left: 65px !important;"><?php echo $receta->rp; ?></div>
      <div class="div_tiny" style="width: 45%;line-height: 1.2; float:left; margin-left: 65px !important;"><?php echo $receta->prescripcion; ?></div>
  </body>
</html>
