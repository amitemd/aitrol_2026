@php
  $tiempo = strtotime($paciente->fecha_nacimiento);
  $ahora = time();
  $edad = ($ahora-$tiempo)/(60*60*24*365.25);
  $edad = floor($edad);

@endphp
<style type="text/css">
  .texto-vertical{
      writing-mode: vertical-lr;

  }
  .principal
  {
    padding-top: 350px;
    height: 500px;
    margin-left: 6px;
    padding-left: 7px;
  }
  body{
    margin: 0;
  }

</style>
<div class="principal">
  @if($agenda->id_empresa == '0992704152001')
  <img src="{{asset('/imagenes')}}/etiqueta.png" style="display: inline-block; margin-bottom: 15px; margin-left: 14px; width: 54px;" />
  @else
  <img src="{{asset('/imagenes')}}/etiqueta2.png" style="display: inline-block; margin-bottom: 15px; margin-left: 14px; width: 54px;" />
  @endif
  <div class="texto-vertical">
    <p style="display: inline-block; font-size: 11.5px;"><b> {{$procedimientos}}<br>SEGURO: {{$seguro->nombre}} &nbsp; ALERGIA: @if($alergia == 1){{"NO"}}@else{{"SI"}}@endif<br/>CI: {{$paciente->id}} &nbsp;&nbsp; EDAD: {{$edad}}<br/>{{$paciente->apellido1}} @if($paciente->apellido2 != 'N/A'){{$paciente->apellido2}}@endif {{$paciente->nombre1}} @if($paciente->nombre2 != 'N/A'){{$paciente->nombre2}}@endif</b></p>
  </div>

</div>
