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
        $patientName = trim($care->paciente_apellido1.' '.$care->paciente_apellido2.' '.$care->paciente_nombre1.' '.$care->paciente_nombre2);
        $doctorName = trim($care->doctor_apellido1.' '.$care->doctor_apellido2.' '.$care->doctor_nombre1);
    @endphp

    <div class="clinical-worklist-page">
        <section class="clinical-worklist-hero clinical-worklist-hero--compact">
            <span class="clinical-worklist-hero__icon" aria-hidden="true"><i class="fa fa-file-text-o"></i></span>
            <div class="clinical-worklist-hero__copy">
                <span class="clinical-worklist-eyebrow">Atención clínica #{{ $care->id }}</span>
                <h2>{{ $patientName ?: 'Paciente no localizado' }}</h2>
                <p>{{ $care->id_paciente }} · {{ date('d/m/Y H:i', strtotime($care->fechaini)) }}</p>
            </div>
            <div class="clinical-worklist-hero__actions">
                <a class="btn" href="{{ route('new-system.scheduled-care.index') }}"><i class="fa fa-arrow-left"></i> Volver al listado</a>
            </div>
        </section>

        <div class="clinical-detail-grid">
            <section class="clinical-worklist-card clinical-detail-card clinical-detail-card--wide">
                <header class="clinical-worklist-card__header"><div class="clinical-worklist-card__heading"><span><i class="fa fa-user"></i></span><div><h3>Paciente y atención</h3><p>Información vinculada a la agenda seleccionada.</p></div></div></header>
                <div class="clinical-detail-data">
                    <article><span>Paciente</span><strong>{{ $patientName ?: 'No disponible' }}</strong></article>
                    <article><span>Identificación</span><strong>{{ $care->id_paciente }}</strong></article>
                    <article><span>Fecha de nacimiento</span><strong>{{ $care->fecha_nacimiento ? date('d/m/Y', strtotime($care->fecha_nacimiento)) : 'No registrada' }}</strong></article>
                    <article><span>Doctor</span><strong>{{ $doctorName ?: 'Sin asignar' }}</strong><small>{{ $care->especialidad_nombre ?: 'Sin especialidad' }}</small></article>
                    <article><span>Estado</span><strong><em class="clinical-worklist-badge">{{ $care->status_label }}</em></strong></article>
                    <article><span>Seguro</span><strong>{{ $care->seguro_nombre ?: 'No registrado' }}</strong></article>
                    <article class="clinical-detail-data__full"><span>Dirección</span><strong>{{ $care->direccion ?: 'No registrada' }}</strong></article>
                </div>
            </section>

            <section class="clinical-worklist-card clinical-detail-card">
                <header class="clinical-worklist-card__header"><div class="clinical-worklist-card__heading"><span><i class="fa fa-map-marker"></i></span><div><h3>Ubicación</h3><p>Trazabilidad de la atención.</p></div></div></header>
                <div class="clinical-detail-data">
                    <article><span>Hospital / unidad</span><strong>{{ $care->hospital_nombre ?: 'Consulta externa' }}</strong></article>
                    <article><span>Sala</span><strong>{{ $care->sala_nombre ?: 'Sin sala' }}</strong></article>
                    <article><span>Historia vinculada</span><strong>{{ $care->history_id ?: 'No creada' }}</strong></article>
                    <article><span>Documentos archivados</span><strong>{{ $care->document_count }}</strong></article>
                </div>
            </section>

            <section class="clinical-worklist-card clinical-detail-card">
                <header class="clinical-worklist-card__header"><div class="clinical-worklist-card__heading"><span><i class="fa fa-medkit"></i></span><div><h3>Consulta y procedimientos</h3><p>Prestaciones asociadas.</p></div></div></header>
                <div class="clinical-detail-data">
                    <article class="clinical-detail-data__full"><span>Prestación principal</span><strong>{{ $care->procedimiento_nombre ?: ((int) $care->proc_consul === 0 ? 'Consulta médica' : 'Sin procedimiento registrado') }}</strong></article>
                    <article class="clinical-detail-data__full"><span>Procedimientos adicionales</span><strong>@forelse ($care->additional_procedures as $procedure)<em class="clinical-worklist-badge">{{ $procedure->nombre }}</em>@empty Ninguno @endforelse</strong></article>
                </div>
            </section>

            <section class="clinical-worklist-card clinical-detail-card">
                <header class="clinical-worklist-card__header"><div class="clinical-worklist-card__heading"><span><i class="fa fa-heartbeat"></i></span><div><h3>Antecedentes y alertas</h3><p>Resumen clínico disponible.</p></div></div></header>
                <div class="clinical-detail-data">
                    <article class="clinical-detail-data__full"><span>Alergias</span><strong>{{ $care->alergias ?: 'Sin alergias registradas' }}</strong></article>
                    <article><span>Patológicos</span><strong>{{ $care->antecedentes_pat ?: 'Sin información' }}</strong></article>
                    <article><span>Familiares</span><strong>{{ $care->antecedentes_fam ?: 'Sin información' }}</strong></article>
                    <article class="clinical-detail-data__full"><span>Quirúrgicos</span><strong>{{ $care->antecedentes_quir ?: 'Sin información' }}</strong></article>
                </div>
            </section>
        </div>
    </div>
</x-new-system.layout>
