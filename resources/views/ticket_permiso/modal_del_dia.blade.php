<div class="modal-header" style="text-align:center">
    <h5 class="modal-title" style="font-weight: bold">HISTORIAL DEL DIA</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">


    <table class="table table-hover">
        <thead>
            <tr>
                <th class="th">Fecha</th>
                <th class="th">{{ trans('tecnicof.user') }}</th>
                <th class="th">{{ trans('tecnicof.request') }}</th>
                <th class="th">{{ trans('tecnicof.reason') }}</th>
                <th class="th">{{ trans('tecnicof.departament') }}</th>
                <th class="th">APROBACION</th>
                <th class="th">Estado</th>
                <th class="th">Historial</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datos as $value)
                <tr>
                    <td>{{ substr($value->fecha_registro, 0, 11) }}</td>
                    <td style="text-align: left;">{{ $value->cedula }}- @if (!is_null($value->nombre))
                            {{ $value->nombre->nombre1 }} @if (!is_null($value->nombre->nombre2))
                                {{ $value->nombre->nombre2 }}
                                @endif {{ $value->nombre->apellido1 }} @if (!is_null($value->nombre->apellido2))
                                    {{ $value->nombre->apellido2 }}
                                @endif
                            @endif
                    </td>

                    <td>
                        @if (!is_null($value->id))
                            {{ $value->id }}
                        @endif
                    </td>

                    <td>{{ $value->tipo_permiso }}</td>


                    <td>{{ $value->departamento }}</td>
                    <td
                        @if ($value->estado_solicitud == '0') bgcolor='#FF3636' 
                @elseif($value->estado_solicitud == '1') 
                    bgcolor='#99EA65'
                @elseif($value->estado_solicitud == '-1') 
                    bgcolor = '#E3F536';
                    @elseif($value->estado_solicitud == '2') 
                    bgcolor='#2e98e2' @endif>
                        @if ($value->estado_solicitud == '0')
                            No aprobado
                        @elseif($value->estado_solicitud == '2')
                            Devuelto
                        @elseif($value->estado_solicitud == '-1')
                            Por aprobar
                        @elseif($value->estado_solicitud == '1')
                            Aprobado
                        @endif
                    </td>
                    <td
                        @if ($value->estado_solicitud == '0') bgcolor='#FF3636' 
                    @elseif($value->estado_solicitud == '1') 
                        bgcolor='#99EA65'
                    @elseif($value->estado_solicitud == '-1') 
                        bgcolor = '#E3F536';
                    @elseif($value->estado_solicitud == '2') 
                        bgcolor = '#65b2ff'; @endif>
                        @if ($value->estado_solicitud == '0')
                            {{ trans('tecnicof.notapproved') }}
                        @elseif($value->estado_solicitud == '-1')
                            {{ trans('tecnicof.byabrrobar') }}
                        @elseif($value->estado_solicitud == '2')
                            Devuelto
                        @elseif($value->estado_solicitud == '1')
                            Aprobado
                        @endif
                    </td>

                    <td>
                        @php
                            $historial = Sis_medico\Log_Devulta::where('id_ticket_permiso', $value->id)->get();
                            
                        @endphp
                        <a id="open_modal_dia"
                            data-remote="{{ route('ticketpermisos.modal_historial', ['id' => $value->id]) }}"
                            class="btn btn-info" data-toggle="modal" data-target="#modal_historial">
                            @if (!empty($historial))
                                <span class="badge badge-light">{{ $historial->count() }}</span>
                            @else
                                <i class="fa fa-history" aria-hidden="true"></i>
                            @endif
                        </a>

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="row">
        <div class="col-sm-5">
            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">
                {{ trans('tecnicof.showing') }} {{ 1 + ($datos->currentPage() - 1) * $datos->perPage() }} /
                {{ count($datos) + ($datos->currentPage() - 1) * $datos->perPage() }}
                {{ trans('tecnicof.of') }} {{ $datos->total() }} {{ trans('tecnicof.records') }}
            </div>
        </div>
        <div class="col-sm-7">
            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                {{ $datos->appends(Request::only(['id', 'nombre']))->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#open_modal_dia').on('click', function() {
            $("#modal_historial").css('z-index', 3000);

        });
    });
</script>
