    <form id="form_manchester" method="post" action="{{ route('manchester.guardar') }}" data-patient-lookup="{{ route('emergencia.pacientes.buscar') }}" data-return-url="{{ route('hospital.emergencia') }}" novalidate>
        {{ csrf_field() }}
        <nav class="ea-steps" aria-label="Pasos del ingreso">
            <button type="button" data-ea-step="0" aria-current="step"><span>1</span><div><strong>Paciente</strong><small>Buscar o registrar</small></div></button>
            <button type="button" data-ea-step="1"><span>2</span><div><strong>Triaje</strong><small>Motivo y prioridad</small></div></button>
            <button type="button" data-ea-step="2"><span>3</span><div><strong>Confirmar</strong><small>Revisar e ingresar</small></div></button>
        </nav>

        <div class="ea-workspace">
            <div class="ea-main">
                <div id="ea-error" class="ea-error" role="alert" tabindex="-1" hidden></div>
                <section class="ea-card" data-ea-panel="0" aria-labelledby="ea-patient-title">
                    <div class="ea-section-heading"><span class="ea-section-number">01</span><div><h2 id="ea-patient-title" tabindex="-1">¿Quién ingresa?</h2><p>Busque por identificación o nombres. Se consultará primero el registro de Personas.</p></div></div>
                    <div class="ea-grid ea-search-fields">
                        <div><label for="id_paciente">Cédula o identificación</label><div class="ea-input-action"><input class="form-control" id="id_paciente" name="id_paciente" maxlength="13" autocomplete="off" placeholder="Ingrese la identificación" required><button type="button" id="ea-lookup" class="ea-button ea-button-secondary" aria-label="Buscar por identificación"><i class="fas fa-search" aria-hidden="true"></i> Buscar</button></div></div>
                        <div><label for="nombre">O busque por apellidos y nombres</label><input class="form-control" id="nombre" autocomplete="off" placeholder="Escriba al menos 3 letras"></div>
                    </div>
                    <div id="paciente-emergencia-estado" class="ea-notice" role="status" hidden></div>
                    <div class="ea-new-patient"><span>¿Es su primera visita?</span><button type="button" id="nuevo-paciente-emergencia" class="ea-button ea-button-secondary"><i class="fas fa-plus" aria-hidden="true"></i> Nuevo paciente</button></div>
                    <div class="ea-identity" id="ea-identity" hidden>
                        <div class="ea-identity-title"><i class="fas fa-user-check" aria-hidden="true"></i> <strong id="ea-identity-heading">Paciente seleccionado</strong> <span id="ea-identity-action">Verifique o corrija sus nombres</span></div>
                        <div class="ea-grid">
                            <div><label for="apellido1">Apellido 1</label><input class="form-control" id="apellido1" name="apellido1" maxlength="60" required></div>
                            <div><label for="apellido2">Apellido 2</label><input class="form-control" id="apellido2" name="apellido2" maxlength="60"></div>
                            <div><label for="nombre1">Nombre 1</label><input class="form-control" id="nombre1" name="nombre1" maxlength="60" required></div>
                            <div><label for="nombre2">Nombre 2</label><input class="form-control" id="nombre2" name="nombre2" maxlength="60"></div>
                        </div>
                        <details class="ea-details"><summary>Ver dato adicional de identificación</summary><div class="ea-grid">
                            <div><label for="ea-identity-sex">Sexo registrado</label><input type="hidden" id="sexo" name="sexo"><input class="form-control" id="ea-identity-sex" readonly aria-describedby="ea-identity-note"></div>
                        </div><p id="ea-identity-note">Los cambios de apellidos y nombres se guardarán al registrar el ingreso. Los demás datos se completan en la ficha del paciente.</p></details>
                        <a id="completar-paciente-emergencia" class="ea-text-link" target="_blank" rel="noopener" hidden>Completar ficha en Pacientes <i class="fas fa-external-link-alt" aria-hidden="true"></i></a>
                    </div>
                    <div class="ea-notice"><strong>Puede guardar desde este paso.</strong> Si no conoce la identidad, use «Nuevo paciente» y marque «No se conoce la identificación». El sistema genera un código provisional.</div>
                    <label class="ec-confirm" for="ea-critical"><input id="ea-critical" type="checkbox" name="critical_arrival" value="1"><span>Ingreso crítico: requiere atención inmediata. El triaje se completará después.</span></label>
                </section>

                <section class="ea-card" data-ea-panel="1" aria-labelledby="ea-triage-title" hidden>
                    <div class="ea-section-heading"><span class="ea-section-number">02</span><div><h2 id="ea-triage-title" tabindex="-1">Motivo y prioridad de atención</h2><p>{{ !empty($classificationDemo) ? 'Simulación: el color de ejemplo cambia al completar Glasgow.' : 'Glasgow sugiere un color según la tabla indicada; el profesional lo revisa y confirma.' }}</p></div></div>
                    <label for="mot_consulta">Motivo de consulta <span class="ea-required">*</span></label>
                    <textarea class="form-control" id="mot_consulta" name="mot_consulta" rows="3" minlength="5" maxlength="1000" required placeholder="Describa el motivo y los hallazgos de la valoración inicial"></textarea>
                    @include('hospital.emergencia.classification', ['classificationPrefix'=>'ea', 'classificationLegacy'=>true, 'classificationPriorities'=>$prioridad])
                    <div class="ea-subheading"><h3>Signos vitales</h3><span>Registre los valores disponibles</span></div>
                    <div class="ea-vitals">
                        @foreach([
                            ['presion_art_sis', 'P. sistólica', 'mmHg', 20, 300],
                            ['presion_art_dias', 'P. diastólica', 'mmHg', 10, 200],
                            ['frec_cardiaca', 'Frec. cardíaca', 'lpm', 1, 300],
                            ['frec_resp', 'Frec. respiratoria', 'rpm', 1, 100],
                            ['temperatura', 'Temperatura', '°C', 25, 45],
                            ['satura_oxigeno', 'Saturación O₂', '%', 0, 100]
                        ] as $vital)
                        <div><label for="{{ $vital[0] }}">{{ $vital[1] }}</label><div class="ea-unit-input"><input class="form-control" type="number" inputmode="decimal" step="0.01" min="{{ $vital[3] }}" max="{{ $vital[4] }}" id="{{ $vital[0] }}" name="{{ $vital[0] }}" aria-describedby="unit-{{ $vital[0] }}"><span id="unit-{{ $vital[0] }}">{{ $vital[2] }}</span></div></div>
                        @endforeach
                    </div>
                    <div class="ea-grid"><div><label for="ea-pain">Dolor referido (0–10)</label><input class="form-control" type="number" id="ea-pain" name="pain_score" min="0" max="10" step="1" placeholder="Sin evaluar"></div></div>
                    <details class="ea-details"><summary>Más datos de valoración <span>Tipo de emergencia y otros</span></summary>
                        <div class="ea-grid ea-grid-three">
                            <div><label for="tipos_emergencia">Tipo de emergencia</label><select class="form-control" id="tipos_emergencia" name="tipos_emergencia"><option value="">Sin registrar</option>@foreach($tipos_emergencia as $type)<option value="{{ $type->id }}">{{ $type->nombre }}</option>@endforeach</select></div>
                            <div><label for="embarazo_p">Embarazo / puerperio</label><select class="form-control" id="embarazo_p" name="embarazo_p"><option value="">Sin registrar</option><option value="2">No aplica</option><option value="0">No</option><option value="1">Sí</option></select></div>
                            <div><label for="est_conciencia">Estado de conciencia</label><select class="form-control" id="est_conciencia" name="est_conciencia"><option value="">Sin registrar</option><option value="1">Consciente</option><option value="0">Inconsciente</option></select></div>
                            <div><label for="peso">Peso (kg)</label><input class="form-control" id="peso" name="peso" type="number" min="0" step="0.01"></div>
                            <div><label for="talla">Talla (cm)</label><input class="form-control" id="talla" name="talla" type="number" min="0" step="0.01"></div>
                            <div><label for="total_capilar">Llenado capilar (seg.)</label><input class="form-control" id="total_capilar" name="total_capilar" type="number" min="0" step="0.01"></div>
                            <div><label for="reac_pupilar">Reacción pupilar</label><select class="form-control" id="reac_pupilar" name="reac_pupilar"><option value="">Sin registrar</option><option value="Si">Sí</option><option value="No">No</option></select></div>
                        </div>
                    </details>
                </section>

                <section class="ea-card" data-ea-panel="2" aria-labelledby="ea-review-title" hidden>
                    <div class="ea-section-heading"><span class="ea-section-number">03</span><div><h2 id="ea-review-title" tabindex="-1">Todo listo para el ingreso</h2><p>Revise los datos antes de registrar la llegada y el triaje.</p></div></div>
                    <dl class="ea-review">
                        <div><dt>Paciente</dt><dd id="ea-review-name"></dd></div>
                        <div><dt>Identificación</dt><dd id="ea-review-id"></dd></div>
                        <div><dt>Prioridad</dt><dd id="ea-review-priority"></dd></div>
                        <div><dt>Glasgow</dt><dd data-gcs-summary>Sin evaluar</dd></div>
                        <div><dt>Motivo de consulta</dt><dd id="ea-review-reason"></dd></div>
                    </dl>
                    <details class="ea-details"><summary>Revisar signos vitales y valoración adicional</summary><dl id="ea-review-extra" class="ea-review"></dl></details>
                    <div class="ea-notice"><i class="fas fa-info-circle" aria-hidden="true"></i> Al confirmar se registra el ingreso y el paciente aparece en la cola de Emergencia. La atención médica se inicia desde allí.</div>
                </section>

                <footer class="ea-footer">
                    <button class="ea-button ea-button-secondary" type="button" id="ea-previous" hidden><i class="fas fa-arrow-left" aria-hidden="true"></i> Anterior</button>
                    <span id="ea-progress" role="status">Paso 1 de 3</span>
                    <button class="ea-button ea-button-primary" type="submit" id="ea-save-minimal" value="MINIMAL">Guardar ingreso mínimo</button>
                    <button class="ea-button ea-button-secondary" type="button" id="ea-next">Completar triaje ahora <i class="fas fa-arrow-right" aria-hidden="true"></i></button>
                    <button class="ea-button ea-button-primary" type="submit" id="guardar-ingreso-emergencia" hidden><i class="fas fa-check" aria-hidden="true"></i> Confirmar ingreso</button>
                </footer>
            </div>
            <aside class="ea-sidebar">
                <div class="ea-card ea-summary"><span class="ea-eyebrow">INGRESO ACTUAL</span><div class="ea-avatar" aria-hidden="true"><i class="far fa-user"></i></div><h3 id="ea-summary-name">Seleccione un paciente</h3><p id="ea-summary-id">Sus datos aparecerán aquí.</p><div class="ea-summary-priority"><span>{{ !empty($classificationDemo) ? 'Color de ejemplo' : 'Prioridad pendiente de confirmar' }}</span><strong id="ea-summary-priority">Pendiente de valoración</strong></div><p class="ea-save-note"><i class="far fa-clock" aria-hidden="true"></i> {{ !empty($classificationDemo) ? 'Prueba local: no guarda registros.' : 'El ingreso se guarda al confirmar.' }}</p></div>
                <div class="ea-sidebar-help"><h3>Solo lo necesario para empezar</h3><p>Contacto, cobertura y demás datos administrativos se completan en la ficha del paciente.</p><a class="ea-text-link" href="{{ route('v3.patients.index', ['pendientes_emergencia' => 1]) }}" target="_blank" rel="noopener">Pacientes por completar <i class="fas fa-external-link-alt" aria-hidden="true"></i></a></div>
            </aside>
        </div>
    </form>
