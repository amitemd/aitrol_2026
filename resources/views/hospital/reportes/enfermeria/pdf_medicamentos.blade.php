<!DOCTYPE html>
<html lang="en">
<style>
.td_center {
    text-align: center;
  }
</style>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>KARDEX DE ENFERMERIA</title>
</head>
<body>
    <table border="1" width="100%">
        <caption> KARDEX DE ENFERMERIA </caption>
        <tr>
            <th>Establecimiento</th>
            <th>Nombre </th>
            <th> Apellido </th>
            <th>Sexo(M-F)</th>
            <th>N° Hoja</th>
            <th>N° Historia Clinica</th>
        </tr>
        <tr>

            <td class= "td_center" style="height: 20px"> {{$agenda->empresa->nombrecomercial}}  </td>
            <td class= "td_center" style="height: 20px"> {{$agenda->paciente->nombre1}} {{$agenda->paciente->nombre2}}   </td>
            <td class= "td_center" style="height: 20px">  {{$agenda->paciente->apellido1}} {{$agenda->paciente->apellido2}}  </td>
            @php
            $sexo = '';
            if($agenda->paciente->sexo == 1){
                $sexo = 'M';
            }
            if($agenda->paciente->sexo == 2){
                $sexo = 'F';
            }   
            @endphp         
            <td class= "td_center" style="height: 20px"> {{$sexo}} </td>
            <td class= "td_center" style="height: 20px">    </td>
            <td class= "td_center" style="height: 20px">   </td>

        </tr>
    </table>
    <table border="1" width="100%">
        <tr>
            <th>1. Medicamento</th>
            <th colspan="15">2. Administración</th>
        </tr>
        <tr>
            <td rowspan="3" style="text-align: center" >Presentación, Vía, Dósis Unitaria, Frecuencia</td>
            <td colspan="15" style="text-align: center">Día y mes</td>
        </tr>
        
        <tr>
            <td colspan="3"> @if(isset($fechas_prod[0])) {{$fechas_prod[0]}} @endif </td>
            <td colspan="3"> @if(isset($fechas_prod[1])) {{$fechas_prod[1]}} @endif </td>
            <td colspan="3"> @if(isset($fechas_prod[2])) {{$fechas_prod[2]}} @endif  </td>
            <td colspan="3"> @if(isset($fechas_prod[3])) {{$fechas_prod[3]}} @endif  </td>
            <td colspan="3"> @if(isset($fechas_prod[4])) {{$fechas_prod[4]}} @endif </td>
        </tr>

        <tr>
            @for ($i = 0; $i < 5; $i++)
                <td>Hora</td>
                <td>Ini</td>
                <td>Fun</td>
            @endfor
        </tr>
        @foreach($arr_ins as $key => $value)
 
            <tr>
                
                <td rowspan="7"> {{$value}}</td>
                
            </tr>
            @for ($h = 0; $h < 6; $h++)
                <tr>
                @for ($k = 0; $k < 5; $k++)
                        <td style="height: 10px"> @if(isset($fechas_prod[$k])) @if(isset($cab_fecha[$fechas_prod[$k]]))  @if(isset($cab_fecha[$fechas_prod[$k]])) @php $arr2 = $cab_fecha[$fechas_prod[$k]]; @endphp  @if(isset($arr2[$h])) @if($arr2[$h][0] == $key) {{$arr2[$h][1]}} @endif @endif @endif @endif @endif </td>
                        <td style="height: 10px"> @if(isset($fechas_prod[$k])) @if(isset($cab_fecha[$fechas_prod[$k]]))  @if(isset($cab_fecha[$fechas_prod[$k]])) @php $arr2 = $cab_fecha[$fechas_prod[$k]]; @endphp  @if(isset($arr2[$h])) @if($arr2[$h][0] == $key) {{$arr2[$h][2]}} @endif @endif @endif @endif @endif </td>
                        <td style="height: 10px"> @if(isset($fechas_prod[$k])) @if(isset($cab_fecha[$fechas_prod[$k]]))  @if(isset($cab_fecha[$fechas_prod[$k]])) @php $arr2 = $cab_fecha[$fechas_prod[$k]]; @endphp  @if(isset($arr2[$h])) @if($arr2[$h][0] == $key) {{$arr2[$h][3]}} @endif @endif @endif @endif @endif</td>
                @endfor
                </tr>
            @endfor

        @endforeach

                

</body>
</html>