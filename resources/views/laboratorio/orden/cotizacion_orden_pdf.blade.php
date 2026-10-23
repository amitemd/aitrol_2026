<!DOCTYPE html>
<html>
<head>
  <style>
    @page { margin: 200px 70px; }
    #header { position: fixed; left: 0px; top: -180px; right: 0px; height: 150px; text-align: center; }
    #footer { position: fixed; left: 0px; bottom: -100px; right: 0px; height: 150px; }

    table td{
      padding: 0px !important;
    }

  </style>    

</head>
<body>
  
  <script type="text/php">
        if (isset($pdf)) {
          $font = $fontMetrics->getFont("Arial", "bold");
          $pdf->page_text(555, 80, "{PAGE_NUM}/{PAGE_COUNT}", $font, 9, array(0, 0, 0));
        }
  </script>
  
 
    <div id="header">
        
      <div class="col-md-12" style="width: 1122px;border-bottom: 0.5px solid #009a98;border-top: 0.5px solid #ec6c25;">
       <img width="1125" src="{{base_path().'/public/imagenes/orden_labs.png'}}" align=center>
      </div> 

      <div style="border-bottom: 1px solid #009a98;padding: 0px;width: 1122px;">
        <table style="font-size: 15px;color: #009a98;padding: 1px;">
          <tbody>
            <tr role="row">
              <td width="50" style="background-color: #e5f5f5;height: 20px;"><b>PACIENTE</b></td>
              <td width="206" style="color: black;">{{$orden->paciente->nombre1}} @if($orden->pnombre2=='N/A'||$orden->paciente->nombre2=='(N/A)') @else{{ $orden->paciente->nombre2 }} @endif {{$orden->paciente->apellido1}} @if($orden->paciente->apellido2=='N/A'||$orden->paciente->apellido2=='(N/A)') @else{{ $orden->paciente->apellido2 }} @endif</td>
              <td width="50" style="background-color: #e5f5f5;"><b>CEDULA</b></td>
              <td width="40" style="color: black;">{{$orden->id_paciente}}</td>
              <td width="45" style="background-color: #e5f5f5;"><b>EDAD</b></td>
              <td width="40" style="color: black;">{{$age}}</td>         
              <td width="50" style="background-color: #e5f5f5;"><b>SEXO</b></td>
              <td width="40" >@if($orden->paciente->sexo=='1')Masculino @elseif($orden->paciente->sexo=='2') Femenino @endif</td>
              <td width="50" style="background-color: #e5f5f5;"></td>
            </tr>
            <tr role="row" style="background-color: #e5f5f5;">
              <td width="50" ></td>
              <td width="200"></td>
              <td width="50" ></td>
              <td width="40"></td>
              <td width="50" ></td>
              <td width="40"></td>         
              <td width="50" ></td>
              <td width="40"></td>
              <td width="50" ></td>
            </tr>  
            <tr role="row">
              <td style="background-color: #e5f5f5;height: 20px;"><b>ANÁLISIS</b></td>
              <td>&nbsp;</td>
              <td style="background-color: #e5f5f5;"><b>FECHA</b></td>
              <td style="color: black;">{{substr($orden->created_at,0,10)}}</td>
              <td style="background-color: #e5f5f5;"><b>HORA</b></td>
              <td style="color: black;">{{substr($orden->created_at,10,10)}}</td>         
              <td style="background-color: #e5f5f5;"><b>PÁGINA</b></td>
              <td>&nbsp;</td>
              <td width="50" style="background-color: #e5f5f5;"></td>
            </tr>
          </tbody>
        </table>
      </div>  
              
    </div> 
  
    @php
      $nom = "";
      if(!is_null($firma)){
        $nom = "/storage/app/avatars/$firma->nombre";
      }
    @endphp

    @if($nom != "")
    <div id="footer" style="text-align: center;">
      <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'><span style='font-size:6.0pt;mso-bidi-font-size:11.0pt'><o:p><img width=250 height=100 src="{{base_path().$nom}}" align=center hspace=12></o:p></span>
      </p>
      <p>
        <hr style="width: 30%;margin-left: 202pt">
        <label class="control-label" style="font-family: 'Helvetica general';font-size: 20px;">Doctor (a) 
        </label>
      </p>
    </div>
    @endif
    <div id="content"> 
      <h4 style="color: #006666;"><center>Orden de laboratorio No. {{ $orden->id }}</center></h4>
        @php  $cambia = 0; $contador = 0; @endphp 
        @foreach($detalles as $examen)
          @php $contador ++; @endphp
          @if($cambia != $examen->id_examen_agrupador_labs)
            <div style="color: #006666;font-size: 18px;"><b>{{$examen->lnombre}}</b></div>
            @php $cambia = $examen->id_examen_agrupador_labs; @endphp 
          @endif
            <div style="width: 550px;padding-left: 50px;font-size: 20px;"> {{$contador}}.- {{$examen->descripcion}}</div>  
        @endforeach
        <p style="color: #006666;font-size: 20px;"><b>Diagnóstico:</b></p>
        @php echo $texto; @endphp
    </div>  