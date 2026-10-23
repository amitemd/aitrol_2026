
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
    <title>Codigo de barras</title>
    {!! Html::style('assets/css/pdf.css') !!}
  </head>
  <body>
      <div  style=" padding: 13px 14.897637795px !important; width: 169.700787402px; text-align: center;">
          <img width="120px" height="28px" src="data:image/png;base64, {{ DNS1D::getBarcodePNG("$data", "C128")}}" alt="barcode"   /><br>
          <span style="font-size: 11px;">{{$paciente->id}} / {{$data}}</span><br>
          <span style="font-size: 7px;">{{$paciente->nombre1}} @if($paciente->nombre2 != "(N/A)"){{ $paciente->nombre2}}@endif {{ $paciente->apellido1}} @if($paciente->apellido2 != "(N/A)"){{ $paciente->apellido2}}@endif</span><br>
          <span style="font-size: 9px;">{{date('Y-m-d H:i:s')}}</span>
      </div>
  </body>
</html>
