 <html>
  <head>
    <title>Receta CIR</title>
  </head>
  <body>
    <style type="text/css">
      .div_tiny p{
        margin: 0;
      }
      @page { margin: 0px; }
      body { margin: 0px; }
    </style>
    @php
        $date = $historia->fecha_atencion;
    @endphp
    <table style="width:100%;margin-left: 40px;margin-top: 20px;">
      <tr style="width:100%;">
        <td style="width:50%;text-align: center;"><img  src="{{base_path().'/storage/app/logo/logocir.jpg'}}" /></td>
        <td style="width:50%;text-align: center;"><img  src="{{base_path().'/storage/app/logo/logocir.jpg'}}" /></td>
      </tr>
      <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
      <tr style="width: 100%;">
        <td><b>Fecha: </b> Guayaquil, {{substr($date, 8, 2)}}  de <?php $mes = substr($date, 5, 2);if ($mes == 01) {echo "Enero";}if ($mes == 02) {echo "Febrero";}if ($mes == 03) {echo "Marzo";}if ($mes == 04) {echo "Abril";}if ($mes == 05) {echo "Mayo";}if ($mes == 06) {echo "Junio";}if ($mes == 07) {echo "Julio";}if ($mes == '08') {echo "Agosto";}if ($mes == '09') {echo "Septiembre";}if ($mes == '10') {echo "Octubre";}if ($mes == '11') {echo "Noviembre";}if ($mes == '12') {echo "Diciembre";}?> del {{substr($date, 0, 4)}}</td>
        <td><b>Fecha: </b> Guayaquil, {{substr($date, 8, 2)}}  de <?php $mes = substr($date, 5, 2);if ($mes == 01) {echo "Enero";}if ($mes == 02) {echo "Febrero";}if ($mes == 03) {echo "Marzo";}if ($mes == 04) {echo "Abril";}if ($mes == 05) {echo "Mayo";}if ($mes == 06) {echo "Junio";}if ($mes == 07) {echo "Julio";}if ($mes == '08') {echo "Agosto";}if ($mes == '09') {echo "Septiembre";}if ($mes == '10') {echo "Octubre";}if ($mes == '11') {echo "Noviembre";}if ($mes == '12') {echo "Diciembre";}?> del {{substr($date, 0, 4)}}</td>
      </tr>
      <tr style="width: 100%;">
        <td><b>Nombre: </b> {{ $paciente->nombre1}} @if($paciente->nombre2 != "(N/A)"){{ $paciente->nombre2}}@endif {{ $paciente->apellido1}} @if($paciente->apellido2 != "(N/A)"){{ $paciente->apellido2}}@endif</td>
        <td><b>Nombre: </b> {{ $paciente->nombre1}} @if($paciente->nombre2 != "(N/A)"){{ $paciente->nombre2}}@endif {{ $paciente->apellido1}} @if($paciente->apellido2 != "(N/A)"){{ $paciente->apellido2}}@endif</td>
      </tr>
      <tr style="width: 100%;">
        <td ><b>CI.</b> {{$paciente->id}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Edad: </b>{{$edad}} años</td>
        <td ><b>CI.</b> {{$paciente->id}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Edad: </b>{{$edad}} años</td>
      </tr>
      <tr>
        <td><b>Rp:</b></td>
        <td><b>Prescripción:</b></td>
      </tr>
      <!--tr>
        <td><textarea style="width: 95%; height: 400px;border: none;"><?php echo $receta->rp; ?></textarea></td>
        <td><textarea style="width: 95%; height: 400px;border: none;"><?php echo $receta->prescripcion; ?></textarea></td>
      </tr-->
      @foreach($detalles as $value)
      @php 
        $medicina = $medicinas->where('id',$value->id_medicina)->first();
        $genericos = $medicina->genericos; 
      @endphp
      <tr style="padding-top: 5px">
      <td><p style="width: 95%; border: none; margin:0; font-size: 22px !important;">{{$medicina->nombre}} @foreach($genericos as $gen) ( {{$gen->generico->nombre}} ) @endforeach : {{$value->cantidad}} {{ $value->detalle_cantidad}}</p></td> 
      <!-- <td><p style="width: 95%; border: none; margin:0; font-size: 22px !important;">{{$value->nombre}}: {{$value->cantidad}} {{ $value->detalle_cantidad}}</p></td> -->
      <td><p style="width: 95%; border: none; margin:0; font-size: 22px !important;">{{$value->nombre}}: {{$value->dosis}}</p></td>
      </tr>
      @endforeach

      <!--tr style="font-size: 14px !important;width: 100%;">
        <td style="vertical-align: top; border: solid 1px;"><div class="div_tiny" style="width: 95%;line-height: 1.2;"><?php echo $receta->rp; ?></div></td>
        <td style="vertical-align: top; border: solid 1px;"><div class="div_tiny" style="width: 90%;line-height: 1.2;"><?php echo $receta->prescripcion; ?></div></td>
      </tr-->

    </table>
    <br>

    <div class="div_tiny" style="width: 46%;line-height: 1.2;float:left;margin-left: 55px;padding: 0;margin-right: -70px"><?php echo $receta->rp; ?></div>
    <div class="div_tiny" style="width: 46%;line-height: 1.2;float:left;margin-left: 80px;padding: 0;"><?php echo $receta->prescripcion; ?></div>
    <div style="clear: both;"></div>
    <div style="position: fixed; left: 20px; bottom: 20px; right: 20px; height: 150px;">
      <div style="float: left;width: 45%;text-align: center;">
        @if(!is_null($firma))
          <img width=200 height=auto src="{{base_path().'/storage/app/avatars/'.$firma->nombre}}" style="" align=center hspace=12><br>
        @endif
      </div>
      <div style="float: left;width: 45%;text-align: center;">
        @if(!is_null($firma))
          <img width=200 height=auto src="{{base_path().'/storage/app/avatars/'.$firma->nombre}}" style="" align=center hspace=12><br>
        @endif
      </div>
    </div>
    <div style="clear: both;"></div>
    <div style="position: fixed; left: 20px; bottom: -80px; right: 20px; height: 150px;">
      <div style="float: left;width: 50%;text-align: center;">
        <img width=500 height=auto src="{{base_path().'/storage/app/logo/footercir.jpg'}}" />
      </div>
      <div style="float: left;width: 50%;text-align: center;">
        <img width=500 height=auto src="{{base_path().'/storage/app/logo/footercir.jpg'}}" />
      </div>
    </div>

  </body>
</html>