  <html>
  <head>
      <meta charset="utf-8">
    <title>Receta IECED</title>
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
    <table style="width:100%;margin-left: 40px;margin-top: 50px;">
      <tr style="width:100%;">
        <td style="width:50%;text-align: center;"><img style="width:250px; height: 100px;" src="{{base_path().'/storage/app/logo/logo1391707460001.png'}}" /></td>
        <td style="width:50%;text-align: center;"><img style="width:250px;height: 100px;" src="{{base_path().'/storage/app/logo/logo1391707460001.png'}}" /></td>
      </tr>
      <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
      <tr style="width: 100%;">
        <td><b>Fecha: </b> {{substr($date, 8, 2)}}  de <?php $mes = substr($date, 5, 2); if($mes == 01){ echo "Enero";} if($mes == 02){ echo "Febrero";} if($mes == 03){ echo "Marzo";} if($mes == 04){ echo "Abril";} if($mes == 05){ echo "Mayo";} if($mes == 06){ echo "Junio";} if($mes == 07){ echo "Julio";} if($mes == '08'){ echo "Agosto";}  if($mes == '09'){ echo "Septiembre";} if($mes == '10'){ echo "Octubre";} if($mes == '11'){ echo "Noviembre";} if($mes == '12'){ echo "Diciembre";} ?> del {{substr($date, 0, 4)}}</td>
        <td><b>Fecha: </b> {{substr($date, 8, 2)}}  de <?php $mes = substr($date, 5, 2); if($mes == 01){ echo "Enero";} if($mes == 02){ echo "Febrero";} if($mes == 03){ echo "Marzo";} if($mes == 04){ echo "Abril";} if($mes == 05){ echo "Mayo";} if($mes == 06){ echo "Junio";} if($mes == 07){ echo "Julio";} if($mes == '08'){ echo "Agosto";}  if($mes == '09'){ echo "Septiembre";} if($mes == '10'){ echo "Octubre";} if($mes == '11'){ echo "Noviembre";} if($mes == '12'){ echo "Diciembre";} ?> del {{substr($date, 0, 4)}}</td>
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
    <br>

    <div class="div_tiny" style="width: 45%;line-height: 1.2;float:left;margin-left: 55px;"><?php echo $receta->rp;?></div>
    <div class="div_tiny" style="width: 45%;line-height: 1.2;float:left;margin-left: 40px;"><?php echo $receta->prescripcion;?></div>
  </body>
</html>
