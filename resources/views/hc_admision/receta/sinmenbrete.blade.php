<html>
  <head>
    <title>Receta IECED</title>
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
        $empresa= Sis_medico\Empresa::find($id_empresa);
    @endphp
    <div style="width:100%;height: 19px;">&nbsp;</div>
    <br>
    <br>

    <table style="width:100%;margin-left: 30px;margin-top: 3px !important;">
      <tr style="width:100%;">
        <td style="width:50%;"><img style="width:500px; height: 106px;" src="{{base_path().'/storage/app/logo/membrete2.jpg'}}" /></td>
        <td style="width:50%;"><img style="width:500px;height: 106px;" src="{{base_path().'/storage/app/logo/membrete2.jpg'}}" /></td>
      </tr>

      <tr style="width: 100%;">
        <td style="margin-top: 11px;"><b>Fecha: </b> @if(!is_null($empresa)) {{$empresa->ciudad}}, @endif {{substr($date, 8, 2)}} de <?php $mes = substr($date, 5, 2);if ($mes == 01) {echo "Enero";}if ($mes == 02) {echo "Febrero";}if ($mes == 03) {echo "Marzo";}if ($mes == 04) {echo "Abril";}if ($mes == 05) {echo "Mayo";}if ($mes == 06) {echo "Junio";}if ($mes == 07) {echo "Julio";}if ($mes == '08') {echo "Agosto";}if ($mes == '09') {echo "Septiembre";}if ($mes == '10') {echo "Octubre";}if ($mes == '11') {echo "Noviembre";}if ($mes == '12') {echo "Diciembre";}?> del {{substr($date, 0, 4)}}</td>
        <td style="margin-top: 11px;"><b>Fecha: </b> @if(!is_null($empresa)) {{$empresa->ciudad}}, @endif {{substr($date, 8, 2)}} de <?php $mes = substr($date, 5, 2);if ($mes == 01) {echo "Enero";}if ($mes == 02) {echo "Febrero";}if ($mes == 03) {echo "Marzo";}if ($mes == 04) {echo "Abril";}if ($mes == 05) {echo "Mayo";}if ($mes == 06) {echo "Junio";}if ($mes == 07) {echo "Julio";}if ($mes == '08') {echo "Agosto";}if ($mes == '09') {echo "Septiembre";}if ($mes == '10') {echo "Octubre";}if ($mes == '11') {echo "Noviembre";}if ($mes == '12') {echo "Diciembre";}?> del {{substr($date, 0, 4)}}</td>
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
      <tr style="font-size: 13px !important;">
        <td><p style="width: 95%; border: none; margin:0; font-size: 20px !important;">{{$value->medicina->nombre}}( @foreach($genericos as $gen) {{$gen->generico->nombre}} @endforeach ): {{$value->cantidad}}</p></td>
        <td><p style="width: 95%; border: none; margin:0; font-size: 20px !important;">{{$value->medicina->nombre}}: {{$value->dosis}}</p></td>
      </tr>
      @endforeach
       <!--tr style="font-size: 14px !important;width: 100%;">
        <td style="vertical-align: top; border: none;"><div class="div_tiny" style="width: 95%;line-height: 1.2;"><?php echo $receta->rp; ?></div></td>
        <td style="vertical-align: top; border: none;"><div class="div_tiny" style="width: 90%;line-height: 1.2; margin-left: 65px;"><?php echo $receta->prescripcion; ?></div></td>
      </tr-->
    </table>
      <div class="div_tiny" style="width: 45%;line-height: 1.2; float:left; margin-left: 65px !important;"><?php echo $receta->rp; ?></div>
      <div class="div_tiny" style="width: 45%;line-height: 1.2; float:left; margin-left: 65px !important;"><?php echo $receta->prescripcion; ?></div>
      <div style="clear: both;"></div>
      <div style="float: left;width: 45%;text-align: center;">
        @if(!is_null($firma))
          <img width=180 height=130 src="{{base_path().'/storage/app/avatars/'.$firma->nombre}}" style="" align=center ><br>
        @endif  
      </div>
      <div style="float: left;width: 45%;text-align: center;">
        @if(!is_null($firma))
          <img width=180 height=130 src="{{base_path().'/storage/app/avatars/'.$firma->nombre}}" style="" align=center><br>
        @endif  
      </div>
  </body>
</html>
