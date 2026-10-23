
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Codigo de barras</title>
    {!! Html::style('assets/css/pdf.css') !!}
  </head>
  <body>
  <style type="text/css">
    *{
      margin: 0
      ;
      padding: 0;
    }
  </style>

    <main>
        <div style="text-align: center; padding-top: 30px"> <img style="width: 80%;" src="data:image/png;base64, {{ DNS1D::getBarcodePNG("$data->codigo", "C128",2,30)}}" alt="barcode"/></div>
        <div style="text-align: center;" class="no">{{ $data->nombre }} ({{ $data->marca->nombre }})
        </div>
  </body>
</html>
