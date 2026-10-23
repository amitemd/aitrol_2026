 <html>
  <head>
    <title>Receta EndoscopyNet</title>
    <style type="text/css">
      .div_tiny p{
        margin: 0;
      }
      @page { margin: 380px 0px 0px 0px }
      #header { position: fixed; left: 0px; bottom: 0px; top: -380px; right: 0px; height: 250px; text-align: center;}
      body { margin: 0px; }
    </style>
  </head>
  <body >
    
    @php
        $date = $historia->fecha_atencion;
        $empresa= Sis_medico\Empresa::find($id_empresa);
    @endphp
    <div style="col-md-12" id="header">
      <table style="width:100%;margin-left: 25px;margin-top: 50px;">
        <tr style="width:100%;">
          <td style="width:50%;text-align: center;">
            <img style="width:400px; height: 150px;" src="{{public_path().'/imagenes/endoscopynet_gastroquito.png'}}" />
          </td>
          <td style="width:50%;text-align: center;">
            <img style="width:400px;height: 150px;" src="{{public_path().'/imagenes/endoscopynet_gastroquito.png'}}" />
          </td>
        </tr>
        <tr style="width: 100%;">
          <td><b>Fecha: </b> @if(!is_null($empresa)) {{$empresa->ciudad}}, @endif {{substr($date, 8, 2)}}  de <?php $mes = substr($date, 5, 2); if($mes == 01){ echo "Enero";} if($mes == 02){ echo "Febrero";} if($mes == 03){ echo "Marzo";} if($mes == 04){ echo "Abril";} if($mes == 05){ echo "Mayo";} if($mes == 06){ echo "Junio";} if($mes == 07){ echo "Julio";} if($mes == '08'){ echo "Agosto";}  if($mes == '09'){ echo "Septiembre";} if($mes == '10'){ echo "Octubre";} if($mes == '11'){ echo "Noviembre";} if($mes == '12'){ echo "Diciembre";} ?> del {{substr($date, 0, 4)}}</td>
          <td><b>Fecha: </b> @if(!is_null($empresa)) {{$empresa->ciudad}}, @endif {{substr($date, 8, 2)}}  de <?php $mes = substr($date, 5, 2); if($mes == 01){ echo "Enero";} if($mes == 02){ echo "Febrero";} if($mes == 03){ echo "Marzo";} if($mes == 04){ echo "Abril";} if($mes == 05){ echo "Mayo";} if($mes == 06){ echo "Junio";} if($mes == 07){ echo "Julio";} if($mes == '08'){ echo "Agosto";}  if($mes == '09'){ echo "Septiembre";} if($mes == '10'){ echo "Octubre";} if($mes == '11'){ echo "Noviembre";} if($mes == '12'){ echo "Diciembre";} ?> del {{substr($date, 0, 4)}}</td>
        </tr>
        <tr style="width: 100%;">
          <td><b>Nombre: </b> {{ $paciente->nombre1}} @if($paciente->nombre2 != "(N/A)"){{ $paciente->nombre2}}@endif {{ $paciente->apellido1}} @if($paciente->apellido2 != "(N/A)"){{ $paciente->apellido2}}@endif</td>
          <td><b>Nombre: </b> {{ $paciente->nombre1}} @if($paciente->nombre2 != "(N/A)"){{ $paciente->nombre2}}@endif {{ $paciente->apellido1}} @if($paciente->apellido2 != "(N/A)"){{ $paciente->apellido2}}@endif</td>
        </tr>
        <tr style="width: 100%;">
          <td ><b>CI.</b> {{$paciente->id}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Edad: </b>{{$edad}} años</td>
          <td ><b>CI.</b> {{$paciente->id}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Edad: </b>{{$edad}} años</td>
        </tr>
        <tr style="width: 100%">
            <td ><b>Alergia: </b>
              {{$principioActivo}}
            </td> 
            <td ><b>Alergia:</b> 
              {{$principioActivo}}
            </td>
        </tr>
        <tr style="width: 100%">
          <td><b>Rp:</b></td>
          <td><b>Prescripción:</b></td>
        </tr>
      </table>
    </div>
    <table style="width:100%;margin-left: 40px;margin-top: 10px; ">
      @foreach($detalles as $value)
      @php 
        $medicina = $medicinas->where('id',$value->id_medicina)->first();
        $genericos = $medicina->genericos; 
      @endphp
      <tr style="padding-top: 5px; width:100%;">
        <td style="width:50%;text-align: left;"><p style="width: 95%; border: none; margin:0; font-size: 22px !important;">{{$medicina->nombre}} @foreach($genericos as $gen) ({{$gen->generico->nombre}} ) @endforeach : {{$value->cantidad}} {{ $value->detalle_cantidad}}</p></td>
        <td style="width:50%;text-align: left;"><p style="width: 95%; border: none; margin:0; font-size: 22px !important;">{{$value->nombre}}: {{$value->dosis}}</p></td>
      </tr>
      @endforeach
    </table>
    <br>

    <div class="div_tiny" style="width: 46%;line-height: 1.2;float:left;margin-left: 30px; padding: 0; margin-right: -80px; font-size: 22px !important;">
      <?php echo $receta->rp;?>
    </div>
    <div class="div_tiny" style="width: 46%;line-height: 1.2;float:left;margin-left: 130px; padding: 0; font-size: 22px !important;">
      <?php echo $receta->prescripcion;?>
    </div>
    <div style="clear: both;"></div>
      <div style="float: left;width: 45%;text-align: center;">
        @if(!is_null($firma))
          <img width=180 height=130 src="{{base_path().'/storage/app/avatars/'.$firma->nombre}}" style="position:relative; z-index: 999;" align=center hspace=12><br>
        @endif  
      </div>
      <div style="float: left;width: 45%;text-align: center;">
        @if(!is_null($firma))
          <img width=180 height=130 src="{{base_path().'/storage/app/avatars/'.$firma->nombre}}" style="position:relative; z-index: 999;" align=center hspace=12><br>
        @endif  
      </div>
    <div style="position: fixed; left: 20px; bottom: 28px; right: 20px; height: 150px;">
      <div style="float: left;width: 50%;text-align: center;">
        @if(!is_null($logo_emp))
          @if(file_exists(base_path().'/storage/app/logo/'.$logo_emp->logo_receta))
            <img width=850 height=170 src="{{base_path().'/storage/app/logo/'.$logo_emp->logo_receta}}" style="" align=center ><br>
          @endif        
        @endif  
      </div>
      <div style="float: left;width: 50%;text-align: center;">
        @if(!is_null($logo_emp))
          @if(file_exists(base_path().'/storage/app/logo/'.$logo_emp->logo_receta))
            <img width=850 height=170 src="{{base_path().'/storage/app/logo/'.$logo_emp->logo_receta}}" style="" align=center><br>
          @endif
        @endif  
      </div>
    </div> 
  </body>
</html>
