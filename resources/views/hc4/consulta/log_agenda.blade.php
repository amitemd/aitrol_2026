<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
  <div class="table-responsive col-md-12">
    <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Hora</th>
          <th>Descripción del Cambio</th>
          <th>Usuario</th>
          <th>Observación</th>
        </tr>
      </thead>
      <tbody>
      @foreach ($logs as $log)
        @php 
          $doc_ant = Sis_medico\User::find($log->id_doctor1_ant);
          $doc_des = Sis_medico\User::find($log->id_doctor1);
        @endphp
        <tr >
          <td>{{ substr($log->created_at,0,10) }}</a></td>
          <td>{{ substr($log->created_at,11,5) }}</a></td>
          <td>@if($log->descripcion!=null){{ $log->descripcion }} / @endif @if($log->descripcion2!=null){{ $log->descripcion2 }} / @endif @if($log->descripcion3!='CAMBIO: '){{ $log->descripcion3 }} @endif</a></td>
          <td>{{ $log->user_crea->nombre1 }} {{ $log->user_crea->apellido1 }}</a></td>
          <td>{{ $log->observaciones_ant }}/{{ $log->observaciones }}</a></td>
      </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>