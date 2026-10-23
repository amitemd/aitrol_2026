<div class="modal-header">
    <div class="col-md-10"><h3>Log de Agenda: {{$agenda->paciente->apellido1}} {{$agenda->paciente->nombre1}}</h3></div>
    <div class="col-md-2">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span>
    </button>
    </div>
</div>
<div class="modal-body">
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
          <tr>
            <td>{{substr($agenda->created_at, 0,10)}}</td>
            <td>{{substr($agenda->created_at, 11,5)}}</td>
            <td>Creacion de Agenda</td>
            <td>{{ $agenda->user_crea->nombre1 }} {{ $agenda->user_crea->apellido1 }}</a></td>
            <td></td>
          </tr>
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
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>
