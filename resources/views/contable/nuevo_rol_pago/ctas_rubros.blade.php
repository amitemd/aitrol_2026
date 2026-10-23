
<div class="table-responsive col-md-12">
    <div class="table-responsive col-md-12">
    <table id="example3" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info" style="width: 100%; <?php count($pago_rubros)==0?'display: none;':''?>">
        <thead>
        <tr class='well-dark'>
            <th width="55%" >Detalle</th>
            <th width="20%" >Debe</th>
            <th width="20%" >Haber</th>
            <th width="5%" style="padding-left: 10px;" >Accion</th>
        </tr>
        </thead>
        <tbody id="body">
        @php $debe = 0; $haber = 0; @endphp
        @if(count($pago_rubros)>0)
        @if($cuentas_rol->id_asiento_planilla != null)
            
            @foreach ($pago_rubros as $value)
            @php $debe += $value->debe; $haber += $value->haber; @endphp
            <tr>
                <td>{{$value->id_plan_cuentas}} - {{$value->descripcion}}</td>
                <td style="text-align: right;">{{number_format($value->debe, 2, ',', ' ')}}</td>
                <td style="text-align: right;">{{number_format($value->haber, 2, ',', ' ')}}</td>
                <td>
                    
                </td>
            </tr>
            @endforeach
            <tr>
            <td></td>
                <td style="text-align: right;">{{number_format($debe, 2, ',', ' ')}}</td>
                <td style="text-align: right;">{{number_format($haber, 2, ',', ' ')}}</td>
                <td>
                    
                </td>  
            </tr>  
        @else
            @foreach ($pago_rubros as $value)
            @php $debe += $value->debe; $haber += $value->haber; @endphp
            <tr>
                <td>{{$value->id_plan_cuentas}} - {{$value->descripcion}}</td>
                <td style="text-align: right;">
                    <input type="text" id="debe_{{$value->id}}" name="debe[{{$value->id}}]" value="{{number_format($value->debe, 2, '.', ' ')}}" onchange="guardar('{{$value->id}}')" style="text-align: right;">
                </td>
                <td style="text-align: right;"><input type="text" id="haber_{{$value->id}}" name="haber[{{$value->id}}]" value="{{number_format($value->haber, 2, '.', ' ')}}" onchange="guardar('{{$value->id}}')" style="text-align: right;"></td>
                <td>
                    <button type="button" class="btn btn-danger" onclick="anularRubro('{{$value->id}}')"><i class="glyphicon glyphicon-trash"></i></button>
                </td>
            </tr>
            @endforeach
            <tr>
                <td></td>
                <td style="text-align: right;">{{number_format($debe, 2, ',', ' ')}}</td>
                <td style="text-align: right;">{{number_format($haber, 2, ',', ' ')}}</td>
                <td>
                    
                </td>  
            </tr>
        @endif
        @endif
        
        </tbody>
    </table>
    </div>
</div>