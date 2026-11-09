  <html>
  <head>
	<meta charset="UTF-8">
    <title>Receta medica</title>
    <style type="text/css">
      .div_tiny p{
        margin: 0;
      }
      @page { margin: 380px 0px 150px 0px }
      #header { position: fixed; left: 0px; bottom: 0px; top: -380px; right: 0px; height: 250px; text-align: center;}
      #footer { position: fixed; left: 0px; bottom: -150px; right: 0px; height: 150px; text-align: center; }
      #footer table { width: 100%; border-collapse: collapse; }
      #footer img { width: 500px; height: auto; display: block; margin: 0 auto; }
      #watermark { position: fixed; left: 0px; top: 165px; right: 0px; z-index: -1; text-align: center; }
      #watermark table { width: 100%; border-collapse: collapse; }
      #watermark td { text-align: center; }
      #watermark img { width: 500px; height: auto; display: inline-block; }
      body { margin: 0px; }
    </style>
  </head>
  <body >
    @php 
        $date = $historia->fecha_atencion;
        $empresa= Sis_medico\Empresa::find($id_empresa); 
        $firmaPath = !is_null($firma) && !empty($firma->nombre)
          ? app(\Sis_medico\Services\DoctorSignatureService::class)->pathForFilename($firma->nombre) : null;
    @endphp
    <div style="col-md-12" id="header">
      <table style="width:100%;margin-left: 40px;margin-top: 50px;">
      <tr style="width:100%; margin:0;">
          <td style="width:50%;text-align: center;">
	<img style="width:430px; height:auto; max-height: 126px;" src="{{public_path('assets/images/logo_receta_borja.png')}}" alt="Borja Clinic" />
	</td>
          <td style="width:50%;text-align: center;">
	<img style="width:430px; height:auto; max-height: 126px;" src="{{public_path('assets/images/logo_receta_borja.png')}}" alt="Borja Clinic" />
	</td>
      </tr>
      <tr style="width: 100%;">
          <td><b>Fecha: </b> {{substr($receta->created_at, 8, 2)}}  de <?php $mes = substr($receta->created_at, 5, 2); if($mes == 01){ echo "Enero";} if($mes == 02){ echo "Febrero";} if($mes == 03){ echo "Marzo";} if($mes == 04){ echo "Abril";} if($mes == 05){ echo "Mayo";} if($mes == 06){ echo "Junio";} if($mes == 07){ echo "Julio";} if($mes == '08'){ echo "Agosto";}  if($mes == '09'){ echo "Septiembre";} if($mes == '10'){ echo "Octubre";} if($mes == '11'){ echo "Noviembre";} if($mes == '12'){ echo "Diciembre";} ?> del {{substr($receta->created_at, 0, 4)}}</td>
          <td><b>Fecha: </b> {{substr($receta->created_at, 8, 2)}}  de <?php $mes = substr($receta->created_at, 5, 2); if($mes == 01){ echo "Enero";} if($mes == 02){ echo "Febrero";} if($mes == 03){ echo "Marzo";} if($mes == 04){ echo "Abril";} if($mes == 05){ echo "Mayo";} if($mes == 06){ echo "Junio";} if($mes == 07){ echo "Julio";} if($mes == '08'){ echo "Agosto";}  if($mes == '09'){ echo "Septiembre";} if($mes == '10'){ echo "Octubre";} if($mes == '11'){ echo "Noviembre";} if($mes == '12'){ echo "Diciembre";} ?> del {{substr($receta->created_at, 0, 4)}}</td>
      </tr>
      <tr style="width: 100%;">
        <td><b>Nombre: </b> {{ $paciente->nombre1}} @if($paciente->nombre2 != "(N/A)"){{ $paciente->nombre2}}@endif {{ $paciente->apellido1}} @if($paciente->apellido2 != "(N/A)"){{ $paciente->apellido2}}@endif</td>
        <td><b>Nombre: </b> {{ $paciente->nombre1}} @if($paciente->nombre2 != "(N/A)"){{ $paciente->nombre2}}@endif {{ $paciente->apellido1}} @if($paciente->apellido2 != "(N/A)"){{ $paciente->apellido2}}@endif</td>
      </tr>
      <tr style="width: 100%;">
          <td ><b>CI.</b> {{$paciente->id}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Edad: </b>{{$edad}} años</td>
          <td ><b>CI.</b> {{$paciente->id}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Edad: </b>{{$edad}} años</td>
      </tr>
        <tr style="width: 100%;">
            <td ><b>Alergia: </b>
              {{$principioActivo}}
            </td> 
            <td ><b>Alergia:</b> 
              {{$principioActivo}}
            </td>
        </tr>
        <tr>
        <td><b>Rp:</b></td>
        <td><b>Prescripción:</b></td>
      </tr>
      </table>
    </div>
    <div id="footer">
      <table>
        <tr>
          <td style="width:50%;"><img style="width:100%; height:100%; max-height: 126px;" src="{{public_path('assets/images/pie_receta_borja.png')}}" alt="Información de contacto Borja Clinic" /></td>
          <td style="width:50%;"><img style="width:100%; height:100%; max-height: 126px;" src="{{public_path('assets/images/pie_receta_borja.png')}}" alt="Información de contacto Borja Clinic" /></td>
        </tr>
      </table>
    </div>
    <div id="watermark">
      <table>
        <tr>
          <td style="width:50%; text-align:center;"><img src="{{public_path('assets/images/fondo_receta_borja.png')}}" alt="" /></td>
          <td style="width:50%; text-align:center;"><img src="{{public_path('assets/images/fondo_receta_borja.png')}}" alt="" /></td>
        </tr>
      </table>
    </div>
    <table style="width:100%;margin-left: 40px;margin-top: 10px; ">
      <!--tr>
        <td><textarea style="width: 95%; height: 400px;border: none;"><?php echo $receta->rp;?></textarea></td>
        <td><textarea style="width: 95%; height: 400px;border: none;"><?php echo $receta->prescripcion;?></textarea></td>
      </tr-->
      @foreach($detalles as $value)      
      @php 
        $medicina = $medicinas->where('id',$value->id_medicina)->first();
        $genericos = $medicina->genericos; 
      @endphp
      <tr style="padding-top: 5px; width:100%;">
        <td style="width:50%;text-align: left;"><p style="width: 95%; border: none; margin:0; font-size: 22px !important;">{{$medicina->nombre}} @foreach($genericos as $gen) ({{$gen->generico->nombre}} ) @endforeach : {{$value->cantidad}} {{ $value->detalle_cantidad}}</p></td>
        <!-- <td><p style="width: 95%; border: none; margin:0; font-size: 16px !important;">{{$value->nombre}}: {{$value->cantidad}} {{ $value->detalle_cantidad}}</p></td> -->
        <td style="width:50%;text-align: left;"><p style="width: 95%; border: none; margin:0; font-size: 22px !important;">{{$value->nombre}}: {{$value->dosis}}</p></td>
      </tr>
      @endforeach

      <tr style="font-size: 22px !important;width: 100%;">
        <td style="vertical-align: top; width:50%; text-align: left;"><div class="div_tiny" style="width: 95%;line-height: 1.2; "><?php echo $receta->rp; ?></div></td>
        <td style="vertical-align: top; width:50%; text-align: left;"><div class="div_tiny" style="width: 90%;line-height: 1.2; "><?php echo $receta->prescripcion; ?></div></td>
      </tr>
    </table>

    
    <div style="clear: both;"></div>
      <div style="float: left;width: 45%;text-align: center;">
        @if($firmaPath)
          <img width="400" height="150" src="{{$firmaPath}}" style="position:relative; z-index: 999;" align="center" hspace="12"><br>
        @endif  
      </div>
      <div style="float: left;width: 45%;text-align: center;">
        @if($firmaPath)
          <img width="400" height="150" src="{{$firmaPath}}" style="position:relative; z-index: 999;" align="center" hspace="12"><br>
        @endif  
      </div>
    
  </body>
</html>
