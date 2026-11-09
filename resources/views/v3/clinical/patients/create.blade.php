<style>
    .pc-page {
        padding: 22px
    }

    .pc-hero {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 24px 28px;
        margin-bottom: 16px;
        border-radius: 17px;
        color: #fff;
        background: linear-gradient(115deg, #075b87, #098daf, #16aaa3)
    }

    .pc-hero h1 {
        margin: 3px 0;
        font-size: 27px
    }

    .pc-back {
        padding: 9px 14px;
        border-radius: 9px;
        background: #fff;
        color: #08779b !important;
        font-weight: 800
    }

    .pc-card {
        margin-bottom: 15px;
        border: 1px solid #cee2eb;
        border-radius: 14px;
        background: #fff;
        overflow: hidden
    }

    .pc-title {
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 13px 18px;
        background: #eef8fb;
        color: #123e5b
    }

    .pc-title i {
        display: grid;
        place-items: center;
        width: 34px;
        height: 34px;
        border-radius: 9px;
        color: #fff;
        background: #0795b5
    }

    .pc-title h2 {
        margin: 0;
        font-size: 17px;
        font-weight: 800
    }

    .pc-title small {
        display: block;
        color: #668497
    }

    .pc-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 13px;
        padding: 17px
    }

    .pc-field label {
        display: block;
        margin-bottom: 5px;
        color: #173f59;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase
    }

    .pc-field input,
    .pc-field select,
    .pc-field textarea {
        width: 100%;
        min-height: 40px;
        padding: 8px 10px;
        border: 1px solid #c4dbe5;
        border-radius: 8px;
        box-sizing: border-box
    }

    .pc-span2 {
        grid-column: span 2
    }

    .pc-all {
        grid-column: 1/-1
    }

    .pc-age {
        padding: 8px 10px;
        border-radius: 8px;
        background: #eaf7fa;
        color: #14627c;
        font-weight: 700
    }

    .pc-minor {
        padding: 14px;
        border: 1px solid #a9dceb;
        border-radius: 10px;
        background: #eefaff
    }

    .pc-minor-title {
        margin-bottom: 11px;
        color: #087293;
        font-weight: 800
    }

    .pc-minor-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 13px
    }

    .pc-minor-help {
        grid-column: 1/-1;
        margin: 0;
        color: #496d7e
    }

    .pc-required {
        color: #d43f3a
    }

    .pc-actions {
        display: flex;
        justify-content: flex-end;
        gap: 9px;
        padding: 17px;
        border-top: 1px solid #dce9ee
    }

    .pc-actions .btn {
        border-radius: 8px;
        font-weight: 700
    }

    .pc-hidden {
        display: none
    }

    .pc-note {
        margin: 0 17px 17px;
        padding: 10px 12px;
        border-left: 4px solid #0c9abb;
        background: #eff9fb;
        color: #426678
    }

    .pc-uppercase {
        text-transform: uppercase
    }

    #patient-type-warning .pc-config-link {
        border: 1px solid #805b00 !important;
        border-radius: 7px;
        background: #fff !important;
        color: #674900 !important;
        box-shadow: 0 2px 5px rgba(103, 73, 0, .16);
        font-weight: 800
    }

    #patient-type-warning .pc-config-link:hover,
    #patient-type-warning .pc-config-link:focus {
        border-color: #674900 !important;
        background: #674900 !important;
        color: #fff !important;
        box-shadow: 0 3px 8px rgba(103, 73, 0, .28)
    }

    @media(max-width:1050px) {
        .pc-grid {
            grid-template-columns: repeat(2, 1fr)
        }
    }

    @media(max-width:620px) {
        .pc-page {
            padding: 12px
        }

        .pc-grid,
        .pc-minor-grid {
            grid-template-columns: 1fr
        }

        .pc-span2,
        .pc-all,
        .pc-minor-help {
            grid-column: 1
        }

        .pc-hero {
            align-items: flex-start;
            flex-direction: column;
            gap: 12px
        }
    }
</style>
@php($agendaContext = $agendaContext ?? array())
@php($fromAgenda = !empty($agendaContext['doctor']))
@php($agendaReturnUrl = $fromAgenda ? route('v3.doctor-agenda.create', array_filter(array('doctor'=>$agendaContext['doctor'],'inicio'=>$agendaContext['inicio'],'fin'=>$agendaContext['fin'],'tipo'=>$agendaContext['tipo']), function($value){ return $value !== null && $value !== ''; })) : route('v3.patients.index'))
<div class="pc-page">
    <section class="pc-hero">
        <div><small>GESTIÓN CLÍNICA · V3</small>
            <h1><i class="fas fa-user-plus"></i> Agregar nuevo paciente</h1>
            <div>Registro completo del paciente y de la persona responsable de su cobertura.</div>
        </div><a class="pc-back" href="{{ $agendaReturnUrl }}"><i class="fas fa-arrow-left"></i> {{ $fromAgenda ? 'Volver a nueva agenda' : 'Pacientes' }}</a>
    </section>
    @php($typeProblem = $errors->first('patient_user_type') ?: ($patientTypeProblem ?? null))
    @if ($typeProblem)
        <div class="alert alert-warning" role="alert" id="patient-type-warning" data-message="{{ $typeProblem }}">
            <strong><i class="fas fa-user-tag mr-1" aria-hidden="true"></i> Configure el tipo de usuario del paciente</strong>
            <p class="mb-2">{{ $typeProblem }}</p>
            @if(auth()->user() && auth()->user()->isSystemUser())
                <a class="btn btn-sm pc-config-link" href="{{ route('tipo_usuario-management.index') }}" target="_blank" rel="noopener">Administrar tipos de usuario <i class="fas fa-external-link-alt ml-1" aria-hidden="true"></i></a>
            @else
                <small>Solicite la configuración al administrador del sistema.</small>
            @endif
            <small class="d-block mt-2">Después de configurar el catálogo, vuelva a guardar. No se creará automáticamente ningún tipo de usuario.</small>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger"><b>No se pudo registrar el paciente:</b>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="post" action="{{ route('v3.patients.store') }}" id="patient-form">{{ csrf_field() }}
        @if($fromAgenda)
            <input type="hidden" name="agenda_doctor" value="{{ $agendaContext['doctor'] }}">
            <input type="hidden" name="agenda_inicio" value="{{ $agendaContext['inicio'] }}">
            <input type="hidden" name="agenda_fin" value="{{ $agendaContext['fin'] }}">
            <input type="hidden" name="agenda_tipo" value="{{ $agendaContext['tipo'] }}">
            <div class="alert alert-info"><i class="fas fa-calendar-plus mr-1"></i> Al guardar el paciente regresarás a la nueva agenda y quedará seleccionado automáticamente.</div>
        @endif
        <section class="pc-card">
            <header class="pc-title"><i class="fas fa-user"></i>
                <div>
                    <h2>Datos del paciente</h2><small>Identificación, nombres y datos personales.</small>
                </div>
            </header>
            <div class="pc-grid">
                <div class="pc-field"><label>Tipo de documento <span class="pc-required">*</span></label><select
                        name="tipo_documento" required>
                        <option value="1" {{ old('tipo_documento', 1) == 1 ? 'selected' : '' }}>Cédula</option>
                        <option value="2" {{ old('tipo_documento') == 2 ? 'selected' : '' }}>Pasaporte</option>
                        <option value="3" {{ old('tipo_documento') == 3 ? 'selected' : '' }}>Otro documento</option>
                    </select></div>
                <div class="pc-field"><label>Identificación <span class="pc-required">*</span></label><input
                        name="id" maxlength="13" value="{{ old('id') }}" required autocomplete="off"></div>
                <div class="pc-field"><label>Parentesco / responsable <span class="pc-required">*</span></label><select
                        name="parentesco" id="parentesco" data-order="preserve" autocomplete="off" required>
                        @foreach (['Principal', 'Padre/Madre', 'Conyugue', 'Hijo(a)', 'Hermano(a)', 'Sobrino(a)', 'Nieto(a)', 'Primo(a)', 'Familiar'] as $item)
                            <option value="{{ $item }}"
                                {{ $item === 'Principal' ? 'selected' : '' }}>{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="pc-field"><label>Seguro / convenio <span class="pc-required">*</span></label><select
                        name="id_seguro" id="patientInsurance" data-order="preserve" autocomplete="off" required>
                        <option value="" selected>Seleccione</option>
                        @foreach ($insurers as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="pc-field"><label>Primer nombre <span class="pc-required">*</span></label><input
                        class="pc-uppercase" name="nombre1" maxlength="60" value="{{ old('nombre1') }}" required></div>
                <div class="pc-field"><label>Segundo nombre</label><input class="pc-uppercase" name="nombre2"
                        maxlength="60" value="{{ old('nombre2') }}"></div>
                <div class="pc-field"><label>Primer apellido <span class="pc-required">*</span></label><input
                        class="pc-uppercase" name="apellido1" maxlength="60" value="{{ old('apellido1') }}" required>
                </div>
                <div class="pc-field"><label>Segundo apellido <span class="pc-required">*</span></label><input
                        class="pc-uppercase" name="apellido2" maxlength="60" value="{{ old('apellido2') }}" required>
                </div>
                <div class="pc-field"><label>Fecha de nacimiento <span class="pc-required">*</span></label><input
                        type="date" id="fecha_nacimiento" name="fecha_nacimiento" max="{{ date('Y-m-d') }}"
                        value="{{ old('fecha_nacimiento') }}" required></div>
                <div class="pc-field"><label>Edad</label>
                    <div class="pc-age" id="edad">Seleccione la fecha</div>
                </div>
                <div class="pc-field"><label>Sexo <span class="pc-required">*</span></label><select name="sexo"
                        required>
                        <option value="">Seleccione</option>
                        <option value="1" {{ old('sexo') == 1 ? 'selected' : '' }}>Masculino</option>
                        <option value="2" {{ old('sexo') == 2 ? 'selected' : '' }}>Femenino</option>
                    </select></div>
                <div class="pc-field"><label>País <span class="pc-required">*</span></label><select name="id_pais"
                        required>
                        <option value="">Seleccione</option>
                        @foreach ($countries as $item)
                            <option value="{{ $item->id }}" {{ old('id_pais') == $item->id ? 'selected' : '' }}>
                                {{ $item->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="pc-all pc-minor" id="minor-parent-card" style="display:none" aria-hidden="true">
                    <div class="pc-minor-title"><i class="fas fa-child"></i> Paciente menor de edad: registre la cédula
                        del padre o de la madre</div>
                    <div class="pc-minor-grid">
                        <div class="pc-field"><label>Cédula del padre</label><input id="father_id" name="father_id"
                                inputmode="numeric" pattern="[0-9]{10}" maxlength="10" value="{{ old('father_id') }}"
                                placeholder="Ingrese la cédula del padre"></div>
                        <div class="pc-field"><label>Cédula de la madre</label><input id="mother_id" name="mother_id"
                                inputmode="numeric" pattern="[0-9]{10}" maxlength="10"
                                value="{{ old('mother_id') }}" placeholder="Ingrese la cédula de la madre"></div>
                        <p class="pc-minor-help">Debe ingresar al menos una de las dos cédulas, con 10 dígitos.</p>
                    </div>
                </div>
                <div class="pc-field"><label>Teléfono domicilio <span class="pc-required">*</span></label><input
                        name="telefono1" maxlength="60" value="{{ old('telefono1') }}" required></div>
                <div class="pc-field"><label>Teléfono celular</label><input name="telefono2" maxlength="60"
                        value="{{ old('telefono2') }}"></div>
                <div class="pc-field"><label>Correo del paciente <span class="pc-required">*</span></label><input
                        type="email" id="patient_email" name="patient_email" maxlength="191"
                        value="{{ old('patient_email') }}" required></div>
                <div class="pc-field"><label>Ocupación</label><input class="pc-uppercase" name="ocupacion"
                        maxlength="60" value="{{ old('ocupacion') }}"></div>
                <div class="pc-field"><label>Ciudad</label><input class="pc-uppercase" name="ciudad"
                        maxlength="255" value="{{ old('ciudad') }}"></div>
                <div class="pc-field pc-span2"><label>Dirección</label><input class="pc-uppercase" name="direccion"
                        maxlength="255" value="{{ old('direccion') }}"></div>
                <div class="pc-field"><label>Alergias</label><input class="pc-uppercase" name="alergias"
                        maxlength="255" value="{{ old('alergias') }}"></div>
            </div>
        </section>
        <section class="pc-card pc-hidden" id="responsible-card">
            <header class="pc-title"><i class="fas fa-user-shield"></i>
                <div>
                    <h2>Persona responsable</h2><small>Información del titular o representante del paciente.</small>
                </div>
            </header>
            <div class="pc-grid">
                <div class="pc-field"><label>Identificación <span class="pc-required">*</span></label><input
                        name="responsible_id" maxlength="13" value="{{ old('responsible_id') }}"></div>
                <div class="pc-field"><label>Primer nombre <span class="pc-required">*</span></label><input
                        class="pc-uppercase" name="responsible_nombre1" maxlength="60"
                        value="{{ old('responsible_nombre1') }}"></div>
                <div class="pc-field"><label>Segundo nombre</label><input class="pc-uppercase"
                        name="responsible_nombre2" maxlength="60" value="{{ old('responsible_nombre2') }}"></div>
                <div class="pc-field"><label>Primer apellido <span class="pc-required">*</span></label><input
                        class="pc-uppercase" name="responsible_apellido1" maxlength="60"
                        value="{{ old('responsible_apellido1') }}"></div>
                <div class="pc-field"><label>Segundo apellido <span class="pc-required">*</span></label><input
                        class="pc-uppercase" name="responsible_apellido2" maxlength="60"
                        value="{{ old('responsible_apellido2') }}"></div>
                <div class="pc-field"><label>Fecha de nacimiento <span class="pc-required">*</span></label><input
                        type="date" name="responsible_fecha_nacimiento" max="{{ date('Y-m-d') }}"
                        value="{{ old('responsible_fecha_nacimiento') }}"></div>
                <div class="pc-field"><label>Correo <span class="pc-required">*</span></label><input type="email"
                        name="responsible_email" maxlength="191" value="{{ old('responsible_email') }}"></div>
                <div class="pc-field"><label>País <span class="pc-required">*</span></label><select
                        name="responsible_id_pais">
                        <option value="">Seleccione</option>
                        @foreach ($countries as $item)
                            <option value="{{ $item->id }}"
                                {{ old('responsible_id_pais') == $item->id ? 'selected' : '' }}>{{ $item->nombre }}</option>
                        @endforeach
                    </select></div>
                <div class="pc-field"><label>Teléfono domicilio <span class="pc-required">*</span></label><input
                        name="responsible_telefono1" maxlength="60" value="{{ old('responsible_telefono1') }}">
                </div>
                <div class="pc-field"><label>Teléfono celular</label><input name="responsible_telefono2"
                        maxlength="60" value="{{ old('responsible_telefono2') }}"></div>
            </div>
            <p class="pc-note"><i class="fas fa-info-circle"></i> Este bloque se utiliza cuando la cobertura pertenece
                a otra persona.</p>
        </section>
        <section class="pc-card">
            <header class="pc-title"><i class="fas fa-bullhorn"></i>
                <div>
                    <h2>Origen del paciente</h2><small>Información equivalente al registro utilizado en
                        producción.</small>
                </div>
            </header>
            <div class="pc-grid">
                <div class="pc-field"><label>Origen <span class="pc-required">*</span></label><select name="origen"
                        id="origen" data-order="preserve" autocomplete="off" required>
                        <option value="" selected>Seleccione</option>
                        <option>MEDIO IMPRESO</option>
                        <option>MEDIO DIGITAL</option>
                        <option>REFERIDO</option>
                        <option>OTRO</option>
                    </select></div>
                <div class="pc-field pc-span2"><label>Detalle del origen</label><input class="pc-uppercase"
                        name="origen_detalle" maxlength="255" value="{{ old('origen_detalle') }}"
                        placeholder="Revista, Facebook, Instagram, Google, etc."></div>
                <div class="pc-field"><label>Referido por</label><input class="pc-uppercase" name="referido"
                        maxlength="255" value="{{ old('referido') }}"></div>
            </div>
            <div class="pc-actions"><a class="btn btn-light"
                    href="{{ $agendaReturnUrl }}">Cancelar</a><button class="btn btn-primary"
                    type="submit"><i class="fas fa-save mr-1"></i> Guardar paciente</button></div>
        </section>
    </form>
</div>
<script>
    (function () {
        function showPatientTypeWarning() {
            var warning = document.getElementById('patient-type-warning');
            if (!warning || !window.Swal || typeof window.Swal.fire !== 'function') return;
            window.Swal.fire({icon: 'warning', title: 'Configure el tipo de usuario',
                text: warning.getAttribute('data-message'), confirmButtonText: 'Entendido',
                confirmButtonColor: '#087fa5', width: '32rem'});
        }
        if (document.readyState === 'complete') showPatientTypeWarning();
        else window.addEventListener('load', showPatientTypeWarning, {once: true});
    })();
    (function() {
        var relation = document.getElementById('parentesco'),
            insurance = document.getElementById('patientInsurance'),
            origin = document.getElementById('origen'),
            birth = document.getElementById('fecha_nacimiento'),
            card = document.getElementById('responsible-card'),
            age = document.getElementById('edad'),
            minorCard = document.getElementById('minor-parent-card'),
            father = document.getElementById('father_id'),
            mother = document.getElementById('mother_id');

        function toggleResponsible() {
            var show = relation.value !== 'Principal';
            card.classList.toggle('pc-hidden', !show);
            document.getElementById('patient_email').required = !show;
            card.querySelectorAll('input,select').forEach(function(el) {
                var optional = el.name === 'responsible_nombre2' || el.name === 'responsible_telefono2';
                el.required = show && !optional;
            });
        }

        function validateParents(minor) {
            var invalid = minor && !father.value.trim() && !mother.value.trim();
            father.setCustomValidity(invalid ? 'Ingrese la cédula del padre o de la madre.' : '');
        }

        function showMinorFields(show) {
            minorCard.style.display = show ? 'block' : 'none';
            minorCard.setAttribute('aria-hidden', show ? 'false' : 'true');
            validateParents(show);
        }

        function calculateAge() {
            if (!birth.value) {
                age.textContent = 'Seleccione la fecha';
                showMinorFields(false);
                return;
            }
            var parts = birth.value.split('-'),
                born = new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2])),
                now = new Date(),
                years = now.getFullYear() - born.getFullYear();
            if (now.getMonth() < born.getMonth() || (now.getMonth() === born.getMonth() && now.getDate() < born
                    .getDate())) years--;
            var minor = years >= 0 && years < 18;
            age.textContent = years + ' año(s) · ' + (minor ? 'MENOR DE EDAD' : (years >= 65 ? 'TERCERA EDAD' :
                'ADULTO'));
            showMinorFields(minor);
        }
        function applyInitialSelections() {
            relation.value = 'Principal';
            insurance.value = '';
            origin.value = '';
            toggleResponsible();
        }
        relation.addEventListener('change', toggleResponsible);
        birth.addEventListener('change', calculateAge);
        birth.addEventListener('input', calculateAge);
        father.addEventListener('input', calculateAge);
        mother.addEventListener('input', calculateAge);
        applyInitialSelections();
        window.addEventListener('load', applyInitialSelections);
        window.addEventListener('pageshow', applyInitialSelections);
        window.setTimeout(applyInitialSelections, 0);
        calculateAge();
        document.querySelectorAll('.pc-uppercase').forEach(function(el) {
            el.addEventListener('input', function() {
                var start = this.selectionStart;
                this.value = this.value.toUpperCase();
                this.setSelectionRange(start, start);
            });
        });
    })();
</script>
