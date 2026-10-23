
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
    <title>Codigo de barras</title>
    {!! Html::style('assets/css/pdf.css') !!}
  </head>
  <body>
      <div  style=" padding: 13px 14.897637795px !important; width: 169.700787402px; text-align: center;">
          <img width="120px" height="28px" src="data:image/png;base64, {{ DNS1D::getBarcodePNG("$activo->codigo", "C128")}}" alt="barcode"   /><br>
          <span style="font-size: 11px;">{{$activo->codigo}}</span><br>
          <span style="font-size: 7px;">{{$activo->nombre}}</span><br>
          
      </div>
  </body>
</html>
