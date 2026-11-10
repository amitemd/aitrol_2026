<x-new-system.layout
    :title="$title"
    :user="$newSystemUser"
    :companies="$newSystemCompanies"
    :current-company="$newSystemCurrentCompany"
>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/clinical-worklists.css') }}?v={{ filemtime(public_path('css/clinical-worklists.css')) }}">
    @endpush

    @php
        $reportFilters = [
            'fecha' => $filters['desde'],
            'fecha_hasta' => $filters['hasta'],
            'nombres' => $filters['paciente'],
            'cedula' => $filters['cedula'],
            'proc_consul' => '2',
            'espid' => $filters['especialidad'],
            'id_doctor1' => $filters['doctor'],
            'id_seguro' => $filters['seguro'],
        ];
    @endphp

    <div class="clinical-worklist-page">
        <section class="clinical-worklist-hero">
            <span class="clinical-worklist-hero__icon" aria-hidden="true"><i class="fa fa-user-md"></i></span>
            <div class="clinical-worklist-hero__copy">
                <span class="clinical-worklist-eyebrow">Atención clínica</span>
                <h2>Pacientes del día</h2>
                <p>Consulta las atenciones y accede a la información clínica desde el sistema principal.</p>
            </div>
            <div class="clinical-worklist-hero__actions">
                <a class="btn" href="{{ route('historia_clinica.reporte_hc', $reportFilters) }}"><i class="fa fa-file-text-o"></i> Historia clínica</a>
                <a class="btn btn--outline" href="{{ route('historia_clinica.reporte_hc_iess', $reportFilters) }}"><i class="fa fa-download"></i> Formato IESS</a>
            </div>
        </section>

        <section class="clinical-worklist-card">
            <header class="clinical-worklist-card__header">
                <div class="clinical-worklist-card__heading">
                    <span><i class="fa fa-filter"></i></span>
                    <div><h3>Filtros clínicos</h3><p>Busca por paciente, cédula, periodo, profesional, especialidad o seguro.</p></div>
                </div>
            </header>

            <form class="clinical-worklist-filters" method="GET" action="{{ route('new-system.daily-patients.index') }}">
                <div class="form-group"><label for="daily-from">Fecha inicial</label><input id="daily-from" class="form-control" type="date" name="desde" value="{{ $filters['desde'] }}"></div>
                <div class="form-group"><label for="daily-to">Fecha final</label><input id="daily-to" class="form-control" type="date" name="hasta" value="{{ $filters['hasta'] }}"></div>
                <div class="form-group"><label for="daily-patient">Paciente</label><input id="daily-patient" class="form-control" name="paciente" value="{{ $filters['paciente'] }}" placeholder="Apellidos o nombres"></div>
                <div class="form-group"><label for="daily-id">Cédula</label><input id="daily-id" class="form-control" name="cedula" value="{{ $filters['cedula'] }}" inputmode="numeric" autocomplete="off" placeholder="Número de identificación"></div>
                <div class="form-group"><label for="daily-specialty">Especialidad</label><select id="daily-specialty" class="form-control" name="especialidad"><option value="">Todas</option>@foreach ($catalogs['specialties'] as $item)<option value="{{ $item->id }}" @selected((string) $filters['especialidad'] === (string) $item->id)>{{ $item->nombre }}</option>@endforeach</select></div>
                <div class="form-group"><label for="daily-doctor">Doctor</label><select id="daily-doctor" class="form-control" name="doctor"><option value="">Todos</option>@foreach ($catalogs['doctors'] as $item)<option value="{{ $item->id }}" @selected((string) $filters['doctor'] === (string) $item->id)>{{ trim($item->apellido1.' '.$item->apellido2.' '.$item->nombre1.' '.$item->nombre2) }}</option>@endforeach</select></div>
                <div class="form-group"><label for="daily-insurance">Seguro / convenio</label><select id="daily-insurance" class="form-control" name="seguro"><option value="">Todos</option>@foreach ($catalogs['insurances'] as $item)<option value="{{ $item->id }}" @selected((string) $filters['seguro'] === (string) $item->id)>{{ $item->nombre }}</option>@endforeach</select></div>
                <div class="clinical-worklist-filter-actions">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Buscar</button>
                    <a class="btn btn-default" href="{{ route('new-system.daily-patients.index') }}" title="Limpiar filtros"><i class="fa fa-eraser"></i></a>
                </div>
            </form>
        </section>

        <section class="clinical-worklist-card">
            <header class="clinical-worklist-card__header">
                <div class="clinical-worklist-card__heading">
                    <span><i class="fa fa-stethoscope"></i></span>
                    <div><h3>Atenciones encontradas</h3><p>Cada fila representa una atención y mantiene una sola identidad clínica del paciente.</p></div>
                </div>
                <span class="clinical-worklist-count">{{ $rows->total() }} atención(es)</span>
            </header>

            <div class="clinical-worklist-table-wrap">
                <table class="table table-hover clinical-worklist-table">
                    <thead><tr><th>Paciente</th><th>Fecha y hora</th><th>Doctor</th><th>Atención</th><th>Seguro</th><th>Historia clínica</th><th>Estado</th><th>Acción</th></tr></thead>
                    <tbody>
                    @forelse ($rows as $row)
                        @php
                            $patientName = trim($row->patient_lastname1.' '.$row->patient_lastname2.' '.$row->patient_name1.' '.$row->patient_name2);
                            $doctorName = trim($row->doctor_lastname.' '.$row->doctor_name);
                            $careType = (int) $row->proc_consul === 1 ? 'Procedimiento' : ((int) $row->proc_consul === 4 ? 'Hospitalaria' : 'Consulta');
                            $procedures = array_filter([$row->procedure_name]);
                            foreach ($row->additional_procedures as $procedure) {
                                if ($procedure->nombre && ! in_array($procedure->nombre, $procedures, true)) {
                                    $procedures[] = $procedure->nombre;
                                }
                            }
                        @endphp
                        <tr>
                            <td><strong>{{ $patientName }}</strong><small>{{ $row->id_paciente }} · Nac. {{ $row->fecha_nacimiento ? date('d/m/Y', strtotime($row->fecha_nacimiento)) : 'no registrada' }}</small></td>
                            <td>{{ date('d/m/Y H:i', strtotime($row->fechaini)) }}<small>{{ $row->room_name ?: 'Sin sala asignada' }}</small></td>
                            <td>{{ $doctorName ?: 'Sin asignar' }}<small>{{ $row->specialty_name }}</small></td>
                            <td><span class="clinical-worklist-badge">{{ $careType }}</span><small>{{ $procedures ? implode(' · ', $procedures) : 'Sin procedimiento registrado' }}</small></td>
                            <td>{{ $row->insurance_name ?: 'Sin seguro' }}</td>
                            <td>@if ($row->history_id)<span class="clinical-worklist-badge"><i class="fa fa-check-circle"></i> Creada</span><small>{{ $row->diagnosis_count }} diagnóstico(s)</small>@else<span class="clinical-worklist-muted">Pendiente de apertura</span>@endif</td>
                            <td>{{ (int) $row->estado_cita === 4 ? 'Confirmada' : 'Agendada' }}</td>
                            <td><a class="btn btn-sm btn-primary" href="{{ route('new-system.scheduled-care.detail', ['id' => $row->id]) }}"><i class="fa fa-folder-open-o"></i> Ver atención</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="clinical-worklist-empty"><i class="fa fa-calendar-check-o"></i><strong>No existen atenciones para los filtros seleccionados.</strong></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <footer class="clinical-worklist-pagination"><span>Mostrando {{ $rows->firstItem() ?: 0 }} a {{ $rows->lastItem() ?: 0 }} de {{ $rows->total() }}</span>{{ $rows->appends($filters)->links('pagination::bootstrap-3') }}</footer>
        </section>
    </div>
</x-new-system.layout>
