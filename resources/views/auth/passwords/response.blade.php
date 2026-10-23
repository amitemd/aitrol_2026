@if(!is_null($usuario))
<h3>Sus datos de Ingresos</h3>
<span>Estimado <b>{{$usuario->nombre1}} {{$usuario->apellido1}} {{$usuario->apellido2}}</b>.</span><br>
<span>Su correo de acceso es: <b>{{$usuario->email}}</b></span>
@else
<h3>Usuario No encontrado</h3>
<span>Ingrese Datos Correctos.</span>
@endif
