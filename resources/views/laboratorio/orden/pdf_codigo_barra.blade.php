
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
    <title>Codigo de barras</title>
    {!! Html::style('assets/css/pdf.css') !!}
  </head>
  <body>
    @foreach($examen_orden['arrayG'] as $key=>$value)
    @for($i=0;$i<intval($value["cantidad"]);$i++)
      <div  style=" padding: 13px 14.897637795px !important; width: 170.700787402px; text-align: center;">
            <span style="font-size: 9px;"> {{$value["nombre"]}} </span><br>
            <img width="120px" height="28px" src="data:image/png;base64, {{ DNS1D::getBarcodePNG(strval($value['id_examen_orden']), "C128")}}" alt="barcode"/><br/>
           <span style="font-size: 10px;">{{$value['cedula']}} /{{$value['id_examen_orden']}}</span><br>
           <span style="font-size: 10px;">{{$value['nombres']}}</span><br>
           <span style="font-size: 9px;">{{date('Y-m-d H:i:s')}}</span><br>

    </div>
    @endfor
  @endforeach
  @foreach($examen_orden['arrayU'] as $key=>$value)
    @for($i=0;$i<intval($value["cantidad"]);$i++)
      <div  style=" padding: 13px 14.897637795px !important; width: 169.700787402px; text-align: center;">
            <span style="font-size: 9px;"> {{$value["nombre"]}} </span><br>
            <img width="120px" height="28px" src="data:image/png;base64, {{ DNS1D::getBarcodePNG(strval($value['id_examen_orden']), "C128")}}" alt="barcode"/><br/>
           <span style="font-size: 10px;">{{$value['nombres']}}</span><br>
           <span style="font-size: 9px;">{{date('Y-m-d H:i:s')}}</span><br>

    </div>
    @endfor
  @endforeach


  <div  style=" padding: 13px 14.897637795px !important; width: 169.700787402px; text-align: center;">
        <img width="120px" height="28px" src="data:image/png;base64, {{ DNS1D::getBarcodePNG(strval($value['id_examen_orden']), "C128")}}" alt="barcode"/><br/>
       <span style="font-size: 10px;">{{$examen_orden['arrayUnico']['cedula']}} /{{$examen_orden['arrayUnico']['id_examen']}}</span><br>
       <span style="font-size: 10px;">{{$examen_orden['arrayUnico']['nombres']}}</span><br>
       <span style="font-size: 9px;">{{date('Y-m-d H:i:s')}}</span><br>

</div>





  </body>
</html>
