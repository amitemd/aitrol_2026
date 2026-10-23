  <html>
  <head>
    <title>Receta IECED</title>
  </head>
  <body>
    <style type="text/css">
      .div_tiny p{
        margin: 0;
      }
    </style>
    @php
        $date = $historia->fecha_atencion;
    @endphp
    <table style="width:100%;">
      <tr style="width:100%;">
        <td style="width:50%;"><img style="width:530px; height: 100px;" src="{{base_path().'/storage/app/logo/membrete.jpg'}}" /></td>
        <td style="width:50%;"><img style="width:530px;height: 100px;" src="{{base_path().'/storage/app/logo/membrete.jpg'}}" /></td>
      </tr>
      <tr style="width: 100%;">
        <td><b>Fecha: </b> Guayaquil, {{substr($date, 8, 2)}}  de <?php $mes = substr($date, 5, 2); if($mes == 01){ echo "Enero";} if($mes == 02){ echo "Febrero";} if($mes == 03){ echo "Marzo";} if($mes == 04){ echo "Abril";} if($mes == 05){ echo "Mayo";} if($mes == 06){ echo "Junio";} if($mes == 07){ echo "Julio";} if($mes == '08'){ echo "Agosto";}  if($mes == '09'){ echo "Septiembre";} if($mes == '10'){ echo "Octubre";} if($mes == '11'){ echo "Noviembre";} if($mes == '12'){ echo "Diciembre";} ?> del {{substr($date, 0, 4)}}</td>
        <td><b>Fecha: </b> Guayaquil, {{substr($date, 8, 2)}}  de <?php $mes = substr($date, 5, 2); if($mes == 01){ echo "Enero";} if($mes == 02){ echo "Febrero";} if($mes == 03){ echo "Marzo";} if($mes == 04){ echo "Abril";} if($mes == 05){ echo "Mayo";} if($mes == 06){ echo "Junio";} if($mes == 07){ echo "Julio";} if($mes == '08'){ echo "Agosto";}  if($mes == '09'){ echo "Septiembre";} if($mes == '10'){ echo "Octubre";} if($mes == '11'){ echo "Noviembre";} if($mes == '12'){ echo "Diciembre";} ?> del {{substr($date, 0, 4)}}</td>
      </tr>
      <tr style="width: 100%;">
        <td><b>Nombre: </b> {{ $paciente->nombre1}} @if($paciente->nombre2 != "(N/A)"){{ $paciente->nombre2}}@endif {{ $paciente->apellido1}} @if($paciente->apellido2 != "(N/A)"){{ $paciente->apellido2}}@endif</td>
        <td><b>Nombre: </b> {{ $paciente->nombre1}} @if($paciente->nombre2 != "(N/A)"){{ $paciente->nombre2}}@endif {{ $paciente->apellido1}} @if($paciente->apellido2 != "(N/A)"){{ $paciente->apellido2}}@endif</td>
      </tr>
      <tr style="width: 100%;">
        <td style="width: 12%;text-align: right; padding-right: 100px;" ><b>Edad: </b>{{$edad}} años</td>
        <td style="width: 12%;text-align: right; padding-right: 100px;" ><b>Edad: </b>{{$edad}} años</td>
      </tr>
      <tr>
        <td><b>Rp:</b></td>
        <td><b>Prescripción:</b></td>
      </tr>
      <!--tr>
        <td><textarea style="width: 95%; height: 400px;border: none;"><?php echo $receta->rp;?></textarea></td>
        <td><textarea style="width: 95%; height: 400px;border: none;"><?php echo $receta->prescripcion;?></textarea></td>
      </tr-->
      @foreach($detalles as $value)
      @php $genericos = $medicinas->where('id',$value->id_medicina)->first()->genericos; @endphp
      <tr style="font-size: 13px !important;">
        <td><p style="width: 95%; border: none; margin:0">{{$value->medicina->nombre}}( @foreach($genericos as $gen) {{$gen->generico->nombre}} @endforeach ): {{$value->cantidad}}</p></td>
        <td><p style="width: 95%; border: none;margin:0">{{$value->medicina->nombre}}: {{$value->dosis}}</p></td>
      </tr>
      @endforeach

      <!--tr style="font-size: 14px !important;width: 100%;">
        <td style="vertical-align: top; border: solid 1px;"><div class="div_tiny" style="width: 95%;line-height: 1.2;"><?php echo $receta->rp;?></div></td>
        <td style="vertical-align: top; border: solid 1px;"><div class="div_tiny" style="width: 90%;line-height: 1.2;"><?php echo $receta->prescripcion;?></div></td>
      </tr-->

    </table>
    <div class="div_tiny" style="width: 50%;line-height: 1.2;float:left;"><?php echo $receta->rp;?></div>
    <div class="div_tiny" style="width: 48%;line-height: 1.2;float:right;"><?php echo $receta->prescripcion;?></div>
  </body>
</html>
