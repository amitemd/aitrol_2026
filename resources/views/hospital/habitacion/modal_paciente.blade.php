<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="myModalCosto">{{trans('hospitalizacion.ListadodePacientes')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
        <div class="modal-body">
            {{ csrf_field() }}
            <div class="col-md-12">
              <b>{{trans('hospitalizacion.HABITACION')}} # {{$cama->id_habitacion}} - CODIGO # {{$cama->habitacion->codigo}} </b>
            </div>
            <div class="col-md-12" style="margin-top: 10px;">
            <b> {{trans('hospitalizacion.CAMA')}} # {{$cama->codigo}}</b>
            </div>
            
            <div class="table table-responsive col-md-12" style="margin-top: 10px;">
            <table class="table table-striped table-hover-animation">
              <thead>
                <tr>
                  <th>{{trans('hospitalizacion.Fecha')}}</th>
                  <th>{{trans('hospitalizacion.Paciente')}}</th>
                  <th>{{trans('hospitalizacion.Causa')}}</th>
                  <th>{{trans('hospitalizacion.Doctor')}}</th>
                  <th>{{trans('hospitalizacion.Sala')}}</th>
                  <th>{{trans('hospitalizacion.Observación')}}</th>
                  <th>{{trans('hospitalizacion.Estado')}}</th>
                  <th>{{trans('hospitalizacion.Acción')}}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($enespera as $y)
                <tr>
                  <td>{{$y->fecha}}</td>
                  <td>{{$y->paciente->apellido2}} {{$y->paciente->apellido1}} {{$y->paciente->nombre1}}</td>
                  <td>{{$y->causa}}</td>
                  <td>{{$y->doctor->apellido1}} {{$y->doctor->nombre1}}</td>
                  <td>{{$y->sala->nombre_sala}}</td>
                  <td>{{$y->observaciones}}</td>
                  <td>@if($y->estado==1) <span class="badge badge-light-danger"> Pendiente</span> @elseif($y->estado==2) {{trans('hospitalizacion.ENHABITACION')}}  @endif</td>
                  <td> @if($y->estado==1) <a class="btn btn-warning btn-xs"  type="button" href="{{route('cuartos.asignar_paciente',['id'=>$cama->id,'id_paciente'=>$y->id])}}"> <i class="fa fa-eye"></i> &nbsp; </button> @else <a href="#" class="btn btn-success"></a> @endif </td>
                </tr>
                @endforeach
              </tbody>
            </table>
            </div>

        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-secondary" data-dismiss="modal">{{trans('hospitalizacion.Cerrar')}}</button>
        </div>
    
</div>
