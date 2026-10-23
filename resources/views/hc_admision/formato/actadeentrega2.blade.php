@php
  $id_empresa  = \Session::get('id_empresa');
  $empresa  =  \Sis_medico\Empresa::find($id_empresa);
@endphp
<!DOCTYPE html>
<html>
	<head>
	  
	    <title>CERTIFICADO {{$agenda->id_paciente}}</title>

	  	<style>	
	  		@page { margin: 25px 25px;}
		    #header { position: fixed; left: 20px; top: 30px; right: 20px; height: 150px; text-align: center;}
		    #content { position: fixed; left: 50px; top: 470px; right: 55px; height: 300px; }
		    #footer { position: fixed; left: 0px; bottom: 200px; right: 30px; height: 100px; }
		    p {
    			font-family:  sans-serif;
    			font-size: 18px;
    			text-align:justify;
    			font-style: italic;
			}
			.dg > p {
				font-family: sans-serif;
    			font-size: 12px;
    			text-align:justify;
    			font-style: italic;
			}
      td{
        font-family: sans-serif;
          font-size: 18px;
          text-align:justify;
          font-style: italic;  
      }
			
		</style>
	</head>
	<body >	  
		<div id="header">
			
    @php
    
      $dia = substr($agenda->fechaini, 8, 2);
      $mes = substr($agenda->fechaini, 5, 2);
      $anio = substr($agenda->fechaini, 0, 4);

      $txtmes='';
      if($mes == 01){ $txtmes = "Enero";} if($mes == 02){ $txtmes = "Febrero";} if($mes == 03){ $txtmes = "Marzo";} if($mes == 04){ $txtmes = "Abril";} if($mes == 05){ $txtmes = "Mayo";} if($mes == 06){ $txtmes = "Junio";} if($mes == 07){ $txtmes = "Julio";} if($mes == '08'){ $txtmes = "Agosto";}  if($mes == '09'){ $txtmes = "Septiembre";} if($mes == '10'){ $txtmes = "Octubre";} if($mes == '11'){ $txtmes = "Noviembre";} if($mes == '12'){ $txtmes = "Diciembre";}

    @endphp 
			<p><img src="{{base_path().'/public/imagenes/ae_header.png'}}" align=center hspace=12></p>
      <br><br>
			<h4 align="center" style="font-size: 25px;">Acta de Entrega Informe Médico</h4>
      <!--p>Guayaquil, <b>{{$dia}}</b> de <b>{{$txtmes}}</b> del <b>{{$anio}}</b> </p-->
      
      <p><span lang=ES>{{ucfirst(strtolower($empresa->ciudad))}}, @if($data->tipo=='0')______ @else {{substr($date,8,2)}} @endif de <?php $mes = substr($date, 5, 2); if($mes == 01){ echo "Enero";} if($mes == 02){ echo "Febrero";} if($mes == 03){ echo "Marzo";} if($mes == 04){ echo "Abril";} if($mes == 05){ echo "Mayo";} if($mes == 06){ echo "Junio";} if($mes == 07){ echo "Julio";} if($mes == '08'){ echo "Agosto";}  if($mes == '09'){ echo "Septiembre";} if($mes == '10'){ echo "Octubre";} if($mes == '11'){ echo "Noviembre";} if($mes == '12'){ echo "Diciembre";} ?> del {{substr($date, 0, 4)}}</span></p>
		</div>

    

		<div id="content" > 
        
        <p>Yo, <b>{{$agenda->paciente->nombre1}} @if($agenda->paciente->nombre2!='(N/A)'){{$agenda->paciente->nombre2}}@endif {{$agenda->paciente->apellido1}} @if($agenda->paciente->apellido2!='(N/A)'){{$agenda->paciente->apellido2}}@endif </b> (nombres y apellidos del paciente o acompañante con número de cédula <b>{{$agenda->id_paciente}}</b>, mediante la presente dejo constancia que he recibido el informe médico físicamente y a su vez se me ha explicado de manera detallada y he comprendido toda la información recibida.</p>
        <p>Adicional se deja constancia de lo siguiente:</p>
        <br></br>
        <!--p>Se recibió biopsia    <input type="checkbox" name="peliculas"> </p>
        <p>No se requirió biopsia    <input type="checkbox" name="peliculas1"></p>
        <p>IECED hará el estudio de la biopsia    <input type="checkbox" name="peliculas2"></p-->
        <table>
          <tr>
            <td>Se recibió biopsia</td>
            <td style="border: 1px solid black;width: 30px;">&nbsp;</td>
          </tr>
          <tr>
            <td>No se requirió biopsia</td>
            <td style="border: 1px solid black;width: 30px;"></td>
          </tr>
          <tr>
            <td>{{$empresa->nombre_corto}} hará el estudio de la biopsia</td>
            <td style="border: 1px solid black;width: 30px;"></td>
          </tr>  
        </table>
       <br></br><br></br><br></br><br></br><br></br><br></br><br></br><br></br>
      <div>
        <div style="width:50%; float:left;text-align: center !important;">
            <p>____________________</p>
            <p>Firma del Paciente</p>
            <p> Nombres y Apellidos:</p>
            <p>{{$agenda->paciente->nombre1}} @if($agenda->paciente->nombre2!='(N/A)'){{$agenda->paciente->nombre2}}@endif {{$agenda->paciente->apellido1}} @if($agenda->paciente->apellido2!='(N/A)'){{$agenda->paciente->apellido2}}@endif</p>
        </div>
        <div style="width:50%; float:left;text-align: center !important;">
            <p>___________________</p>
            <p>Firma del acompañante</p>
            <p>Nombres y Apellidos:</p>
            <p>{{$agenda->paciente->nombre1familiar}} @if($agenda->paciente->nombre2!='(N/A)'){{$agenda->paciente->nombre2familiar}}@endif {{$agenda->paciente->apellido1familiar}} @if($agenda->paciente->apellido2!='(N/A)'){{$agenda->paciente->apellido2familiar}}@endif</p>
        </div>
      </div>
    </div>


		<div id="footer">
		  <p><img src="{{base_path().'/public/imagenes/ae_footer.png'}}" ></p> 	
		</div>
	</body>
</html>		