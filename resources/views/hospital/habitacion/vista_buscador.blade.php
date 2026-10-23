<table class="table table-hover">
    <thead>
        <tr>
            <th>{{ trans('hospitalizacion.Fecha') }}</th>
            <th>{{ trans('hospitalizacion.Cedula') }}</th>
            <th>{{ trans('hospitalizacion.Paciente') }}</th>
            <th>{{ trans('hospitalizacion.Doctor') }}</th>
            <th>{{ trans('hospitalizacion.Estado') }}</th>
            <th>{{ trans('emergencia.Prioridad') }}</th>
            <th>{{ trans('hospitalizacion.Accion') }}</th>
        </tr>
    </thead>
    <tbody>
        @php
            $paso = Sis_medico\HoEstadoPaso::find($ho_estado_paso);
            $id_paso = $paso->id;
        @endphp
        @foreach ($solicitudes as $solicitud)
            <tr>
                <td>{{ substr($solicitud->agenda->fechaini, 0, 10) }}</td>
                <td>{{ $solicitud->agenda->id_paciente }}</td>
                <td>{{ $solicitud->agenda->paciente->apellido1 }}
                    {{ $solicitud->agenda->paciente->apellido2 }}
                    {{ $solicitud->agenda->paciente->nombre1 }}</td>
                <td>Dr. {{ $solicitud->agenda->doctor1->apellido1 }}
                    {{ $solicitud->agenda->doctor1->nombre1 }}</td>
                <td>{{$solicitud->nombre_paso}}</td>
                <td>

                    @if (!is_null($paso->ruta))
                        @if ($id_paso == 4)
                            <a href="{{ route($paso->ruta, ['tipo' => 1, 'id_solicitud' => $solicitud->id]) }}"
                                class="btn btn-info" type="button"><i class="fa fa-file"></i> Ver
                                Detalle</a>
                        @elseif($id_paso == 5)
                            <a href="{{ route($paso->ruta, ['tipo' => 0, 'id_solicitud' => $solicitud->id]) }}"
                                class="btn btn-info" type="button"><i class="fa fa-file"></i> Ver
                                Detalle</a>
                        @else
                            <a href="{{ route($paso->ruta, ['id_solicitud' => $solicitud->id]) }}"
                                class="btn btn-info" type="button"><i class="fa fa-file"></i> Ver
                                Detalle</a>
                        @endif
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>