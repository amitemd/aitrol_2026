Todos <input type="checkbox" name="ctodos" id="ctodos" onclick="sel_todos()" >
<div class="table-responsive col-md-12">
<table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
    <thead>
        <tr>
            <th width="5%">Sel.</th>
            <th width="5%">Id</th>
            <th width="10%">Identificacion</th>
            <th width="20%">Nombres</th>
            <th width="10%">Fecha <br> Ingreso</th>
            <th width="5%">Area</th>
            <th width="15%">Cargo</th>
            <th width="15%">Sueldo <br>Mensual</th>
            <th width="10%">Anticipo <br> 1RA quinc</th>
            <th width="5%">Asiento</th>
        </tr>
    </thead> 
    <tbody>
        @php $sum_anticipos = 0; @endphp
        @foreach($valor_anticipos as $valor)
        
        @php
        $sum_anticipos += $valor->valor_anticipo;
        if(!is_null($valor->id_user)){
            $inf_nomina = Sis_medico\Ct_Nomina::where('id_user',$valor->id_user)->where('id_empresa',$empresa->id)->orderBy('updated_at', 'desc')->first();
            $user = Sis_medico\User::find($valor->id_user);
        }
        @endphp
        <tr>
            <td>@if($valor->id_asiento_cabecera == null)<input type="checkbox" name="roles[]" value="{{$valor->id}}">@endif</td>    
            <td>{{$valor->id}}</td>
            <td>{{$valor->id_user}}</td>
            <td>@if(!is_null($inf_nomina)) {{$user->apellido1}}  {{$user->apellido2}}  {{$user->nombre1}}  {{$user->nombre2}} @endif</td>
            <!-- <td>{{$valor->apellido1}} {{$valor->apellido2}} {{$valor->nombre1}} </td> -->
            <td>@if(!is_null($inf_nomina)) {{$inf_nomina->fecha_ingreso}} @endif</td>
            <!-- <td>{{$valor->fecha_ingreso}}</td> -->
            <td>@if(!is_null($inf_nomina)) {{$inf_nomina->area}} @endif</td>
            <!-- <td>{{$valor->area}}</td> -->
            <td>@if(!is_null($inf_nomina)) {{$inf_nomina->cargo}} @endif</td>
            <!-- <td>{{$valor->cargo}}</td> -->
            <td>@if(!is_null($inf_nomina)) {{$inf_nomina->sueldo_neto}} @endif</td>
            <!-- <td>{{$valor->sueldo_neto}}</td> -->
            <td>
                <input id="val_anticipo" type="text" class="form-control" name="val_anticipo{{$valor->id}}" onchange="actualiza_anticipo('{{$valor->id}}');" @if(!is_null($valor->valor_anticipo)) value="{{$valor->valor_anticipo}}" @else value="0.00" @endif autocomplete="off" onkeypress="return isNumberKey(event)" onblur="checkformat(this);" @if($valor->id_asiento_cabecera != null) readonly @endif>
            </td>
            <td>@if($valor->id_asiento_cabecera != null) <a class="btn btn-primary btn-sm" href="{{route('librodiario.revisar',['id'=>$valor->id_asiento_cabecera])}}" target="_blank">Asiento</a> @endif</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td><b> Total Registros:</b></td>
            <td style="text-align:center;">{{count($valor_anticipos)}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><b>Total:</b></td>
            <td>{{number_format($sum_anticipos,2, '.', '')}}</td>
        </tr>
    </tfoot>
</table>
</div>

<script type="text/javascript">
    $('#example2').DataTable({
        'paging': false,
        'lengthChange': false,
        'searching': false,
        'ordering': true,
        'info': false,
        'autoWidth': false,
        'order': [
            [2, 'asc']
        ]
    });

    function sel_todos(){
        // Get the checkbox
        var checkBox = document.getElementById("ctodos");

        // If the checkbox is checked, display the output text
        if (checkBox.checked == true){
            $(':checkbox').prop('checked', true);    
        } else {
            $(':checkbox').prop('checked', false);    
        }
    }
</script>    