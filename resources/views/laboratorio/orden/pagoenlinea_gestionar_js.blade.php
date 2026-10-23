<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
  <div class="row">
    <div class="table-responsive col-md-12" style="min-height: 210px;">
      <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;overflow: none;">
        <thead>
          <tr role="row">
            <th width="5">Fecha</th>
            <th width="5">Fecha Disponibilidad</th>
            <th width="5">Cedula</th>
            <th width="10">Nombres</th>
            <th width="10">Pres/Dom</th>
            <th width="5">Cant.</th>
            <th width="5">Valor</th> 
            <th width="15">Ver</th>                
            <th width="10">Acción</th>
          </tr>
        </thead>
        <tbody>
            @foreach ($ordenes as $value) <!--FOR EACH PARA LOS PENDIENTES PUBLICOS ABAJO HAY OTRO-->
            <tr role="row">
                <td style="font-size: 11px;">{{substr($value->updated_at,0,10)}}</td>
                <td style="font-size: 11px;">{{substr($value->fecha_tentativa,0,10)}}</td>
                <td style="font-size: 11px;">{{$value->id_paciente}}</td>
                <td style="font-size: 11px;">{{$value->paciente->apellido1}} @if($value->paciente->apellido2=='N/A'||$value->paciente->apellido2=='(N/A)') @else{{ $value->paciente->apellido2 }} @endif {{$value->paciente->nombre1}} @if($value->paciente->nombre2=='N/A'||$value->paciente->nombre2=='(N/A)') @else{{ $value->paciente->pnombre2 }} @endif </td>
                <td>@if($value->pres_dom=='1') DOMICILIO @else  PRESENCIAL @endif</td>
                <td>{{$value->cantidad}}</td>
                <td>{{$value->total_valor}}</td>
                <td>
                    <button type="button" class="btn btn-info btn-xs" onclick="window.open('{{ route('cotizador.imprimir', ['id' => $value->id]) }}','_blank');">
                        <span style=" font-size: 9px">Cotización</span>
                    </button>
                </td> 
                <td>
                    <a data-toggle="modal" data-target="#gestionar_orden" class="btn btn-warning btn-xs" href="{{ route('orden.pagoenlinea_gestionar_orden',['id' => $value->id])}}">
                        <span class="glyphicon glyphicon-credit-card"></span> Gestionar
                    </a>
                </td>         
            </tr>
            @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
    