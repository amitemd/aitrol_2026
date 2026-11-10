<x-new-system.layout
    :title="$title"
    :user="$newSystemUser"
    :companies="$newSystemCompanies"
    :current-company="$newSystemCurrentCompany"
>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/clinical-worklists.css') }}?v={{ filemtime(public_path('css/clinical-worklists.css')) }}">
    @endpush

    @php
        $query = $filters;
        $legacyQuery = [
            'fecha' => $filters['desde'],
            'fecha_hasta' => $filters['hasta'],
            'cedula' => $filters['cedula'],
            'nombres' => $filters['paciente'],
            'id_doctor1' => $filters['doctor'],
            'id_seguro' => $filters['seguro'],
            'espid' => $filters['especialidad'],
            'id_procedimiento' => $filters['procedimiento'],
        ];
    @endphp

    <div class="clinical-worklist-page">
        <section class="clinical-worklist-hero">
            <span class="clinical-worklist-hero__icon" aria-hidden="true"><i class="fa fa-stethoscope"></i></span>
            <div class="clinical-worklist-hero__copy">
                <span class="clinical-worklist-eyebrow">Gestión clínica</span>
                <h2>Consultas y procedimientos</h2>
                <p>Consulta la agenda, su trazabilidad y documentación desde el sistema principal.</p>
            </div>
            <div class="clinical-worklist-hero__actions">
                <a class="btn" href="{{ route('new-system.scheduled-care.export', $query) }}"><i class="fa fa-download"></i> Exportar agenda</a>
                <a class="btn btn--outline" href="{{ route('new-system.scheduled-care.export-patients', $query) }}"><i class="fa fa-file-excel-o"></i> Exportar pacientes</a>
            </div>
        </section>

        <section class="clinical-worklist-stats" aria-label="Resumen de atenciones">
            <article><i class="fa fa-list-alt"></i><div><strong>{{ $summary['total'] }}</strong><span>Atenciones encontradas</span></div></article>
            <article><i class="fa fa-stethoscope"></i><div><strong>{{ $summary['consultations'] }}</strong><span>Consultas</span></div></article>
            <article><i class="fa fa-medkit"></i><div><strong>{{ $summary['procedures'] }}</strong><span>Procedimientos</span></div></article>
            <article><i class="fa fa-check-circle"></i><div><strong>{{ $summary['attended'] }}</strong><span>Atendidas o cerradas</span></div></article>
        </section>

        <section class="clinical-worklist-card">
            <header class="clinical-worklist-card__header">
                <div class="clinical-worklist-card__heading">
                    <span><i class="fa fa-calendar-check-o"></i></span>
                    <div><h3>Atenciones agendadas</h3><p>Cada fila corresponde a una agenda única con sus procedimientos relacionados.</p></div>
                </div>
                <span class="clinical-worklist-count">{{ $rows->total() }} registro(s)</span>
            </header>

            <form method="GET" action="{{ route('new-system.scheduled-care.index') }}" class="clinical-worklist-filters clinical-worklist-filters--scheduled">
                @if ($filters['estado'] !== '')<input type="hidden" name="estado" value="{{ $filters['estado'] }}">@endif
                @if ($filters['seguro_tipo'] !== '')<input type="hidden" name="seguro_tipo" value="{{ $filters['seguro_tipo'] }}">@endif
                <div class="form-group"><label for="care-type">Tipo</label><select id="care-type" class="form-control" name="tipo"><option value="2" @selected($filters['tipo'] === '2')>Todos</option><option value="0" @selected($filters['tipo'] === '0')>Consultas</option><option value="1" @selected($filters['tipo'] === '1')>Procedimientos</option></select></div>
                <div class="form-group"><label for="care-from">Desde</label><input id="care-from" class="form-control" type="date" name="desde" value="{{ $filters['desde'] }}"></div>
                <div class="form-group"><label for="care-to">Hasta</label><input id="care-to" class="form-control" type="date" name="hasta" value="{{ $filters['hasta'] }}"></div>
                <div class="form-group"><label for="care-id">Cédula</label><input id="care-id" class="form-control" name="cedula" value="{{ $filters['cedula'] }}" placeholder="Identificación"></div>
                <div class="form-group"><label for="care-patient">Paciente</label><input id="care-patient" class="form-control" name="paciente" value="{{ $filters['paciente'] }}" placeholder="Nombres o apellidos"></div>
                @foreach (['doctor' => 'Doctor', 'seguro' => 'Seguro', 'especialidad' => 'Especialidad', 'procedimiento' => 'Procedimiento'] as $catalog => $label)
                    <div class="form-group {{ $catalog === 'procedimiento' ? 'form-group--wide' : '' }}">
                        <label for="care-{{ $catalog }}">{{ $label }}</label>
                        <select id="care-{{ $catalog }}" class="form-control js-clinical-catalog" name="{{ $catalog }}" data-catalog="{{ $catalog }}" data-placeholder="Buscar {{ mb_strtolower($label) }}">
                            <option value="">Todos</option>
                            @if ($catalogs[$catalog])<option value="{{ $catalogs[$catalog]['id'] }}" selected>{{ $catalogs[$catalog]['text'] }}</option>@endif
                        </select>
                    </div>
                @endforeach
                <div class="clinical-worklist-filter-actions">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Buscar</button>
                    <a class="btn btn-default" href="{{ route('new-system.scheduled-care.index') }}" title="Limpiar filtros"><i class="fa fa-eraser"></i></a>
                </div>
            </form>

            <nav class="clinical-worklist-shortcuts" aria-label="Reportes de consultas y procedimientos">
                <a href="{{ route('consultam.pastelpentax', $legacyQuery) }}"><i class="fa fa-bar-chart"></i> Estadísticas</a>
                <a href="{{ route('new-system.scheduled-care.export', $query) }}"><i class="fa fa-download"></i> Descargar agenda</a>
                <a href="{{ route('masterhc.masterhc', $legacyQuery) }}"><i class="fa fa-file-text-o"></i> Historia clínica</a>
            </nav>

            <div class="clinical-worklist-table-wrap">
                <table class="table table-hover clinical-worklist-table clinical-worklist-table--wide">
                    <thead><tr><th>Fecha / hora</th><th>Paciente</th><th>Doctor</th><th>Sala</th><th>Procedimientos</th><th>Seguro / empresa</th><th>Responsable</th><th>Estado</th><th>Origen</th><th>Documentos</th><th>Acción</th></tr></thead>
                    <tbody>
                    @forelse ($rows as $row)
                        @php
                            $patientName = trim($row->paciente_apellido1.' '.$row->paciente_apellido2.' '.$row->paciente_nombre1.' '.$row->paciente_nombre2);
                            $doctorName = trim($row->doctor_apellido1.' '.$row->doctor_apellido2.' '.$row->doctor_nombre1);
                            $origin = $row->omni === 'OM' ? 'Hospitalario / emergencia' : ((int) $row->proc_consul === 1 ? 'Procedimiento' : 'Ambulatorio');
                        @endphp
                        <tr>
                            <td><strong>{{ date('d/m/Y', strtotime($row->fechaini)) }}</strong><small>{{ date('H:i', strtotime($row->fechaini)) }}</small></td>
                            <td><strong>{{ $patientName ?: 'Paciente no localizado' }}</strong><small>{{ $row->id_paciente }}</small></td>
                            <td>{{ $doctorName ?: 'Sin asignar' }}<small>{{ $row->especialidad_nombre ?: 'Sin especialidad' }}</small></td>
                            <td>{{ $row->sala_nombre ?: 'Sin sala' }}<small>{{ $row->hospital_nombre }}</small></td>
                            <td>{{ $row->procedimiento_nombre ?: ((int) $row->proc_consul === 0 ? 'Consulta médica' : 'Sin procedimiento') }}@foreach ($row->additional_procedures as $procedure)<small>+ {{ $procedure->nombre }}</small>@endforeach</td>
                            <td>{{ $row->seguro_nombre ?: 'Sin seguro' }}<small>{{ $row->empresa_corta ?: $row->empresa_nombre }}</small></td>
                            <td>{{ trim($row->responsable_apellido1.' '.$row->responsable_nombre1) ?: 'Sistema' }}</td>
                            <td><span class="clinical-worklist-badge">{{ $row->status_label }}</span></td>
                            <td>{{ $origin }}</td>
                            <td><i class="fa fa-file-text-o text-info"></i> {{ $row->document_count }}</td>
                            <td><a class="btn btn-sm btn-primary" href="{{ route('new-system.scheduled-care.detail', ['id' => $row->id]) }}"><i class="fa fa-eye"></i> Ver detalle</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="11" class="clinical-worklist-empty"><i class="fa fa-calendar-times-o"></i><strong>No hay atenciones para los filtros seleccionados.</strong></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <footer class="clinical-worklist-pagination"><span>Mostrando {{ $rows->firstItem() ?: 0 }} a {{ $rows->lastItem() ?: 0 }} de {{ $rows->total() }}</span>{{ $rows->appends($filters)->links('pagination::bootstrap-3') }}</footer>
        </section>
    </div>

    @push('scripts')
        <script src="{{ asset('bower_components/select2/dist/js/select2.full.js') }}"></script>
        <script>window.aitrolClinicalCatalogUrl = @json(route('new-system.scheduled-care.catalog'));</script>
        <script src="{{ asset('js/clinical-worklists.js') }}?v={{ filemtime(public_path('js/clinical-worklists.js')) }}"></script>
    @endpush
</x-new-system.layout>
