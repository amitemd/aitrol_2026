@include('v3.clinical.operations._styles')
<style>
    .adr-page { padding: 13px; }
    .adr-card { overflow: hidden; background: #fff; border: 1px solid #d5e6ed; border-radius: 13px; box-shadow: 0 7px 20px rgba(27, 76, 99, .06); }
    .adr-card-head { display: flex; align-items: center; justify-content: space-between; gap: 15px; padding: 13px 16px; border-bottom: 1px solid #dbe9ef; background: linear-gradient(90deg, #f0fafc, #f8fcfd); }
    .adr-card-title { display: flex; align-items: center; gap: 10px; }
    .adr-card-title>i { display: grid; place-items: center; width: 34px; height: 34px; border-radius: 9px; color: #087fa6; background: #d9f3f7; }
    .adr-card-title h2 { margin: 0; color: #103b57; font-size: 15px; font-weight: 800; }
    .adr-card-title p { margin: 2px 0 0; color: #668092; font-size: 10.5px; }
    .adr-count { padding: 5px 10px; border-radius: 999px; color: #08799d; background: #dff4f8; font-size: 10px; font-weight: 800; white-space: nowrap; }
    .adr-card-body { padding: 14px 16px 16px; }
    .adr-controls { display: grid; grid-template-columns: minmax(260px, 1fr) auto; gap: 14px; align-items: end; margin-bottom: 11px; }
    .adr-field label { display: block; margin-bottom: 5px; color: #234b63; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .35px; }
    .adr-field .form-control { height: 38px; border-color: #bed5df; border-radius: 8px; color: #143d55; box-shadow: none; }
    .adr-field .form-control:focus { border-color: #1395b5; box-shadow: 0 0 0 3px rgba(19, 149, 181, .11); }
    .adr-scope { display: inline-flex; gap: 4px; padding: 3px; border: 1px solid #cfe1e8; border-radius: 9px; background: #f4f9fb; }
    .adr-scope .btn { min-width: 112px; border: 0; border-radius: 7px; font-size: 11px; font-weight: 800; }
    .adr-scope .btn.active { background: #078ab2; color: #fff; box-shadow: 0 3px 8px rgba(7, 138, 178, .2); }
    .adr-table-wrap { overflow: hidden; border: 1px solid #d7e5eb; border-radius: 10px; }
    .adr-table { margin: 0; border: 0 !important; font-size: 11px; }
    .adr-table th { padding: 8px 10px !important; border: 0 !important; border-bottom: 1px solid #d3e3ea !important; color: #32596e; background: #edf7fa; font-size: 9.5px; text-transform: uppercase; letter-spacing: .35px; }
    .adr-table td { padding: 8px 10px !important; border: 0 !important; border-bottom: 1px solid #e4edf1 !important; vertical-align: middle !important; }
    .adr-table tbody tr:last-child td { border-bottom: 0 !important; }
    .adr-table tbody tr:hover { background: #f8fcfd; }
    .adr-table tbody tr.is-selected { background: #fbfeff; }
    .adr-table .adr-required-column { display: none; }
    .adr-document { display: flex; align-items: center; gap: 10px; }
    .adr-document-icon { display: grid; place-items: center; flex: 0 0 30px; width: 30px; height: 30px; border-radius: 8px; color: #1687aa; background: #e7f7fa; }
    .adr-document b { color: #153d56; font-size: 11px; }
    .adr-code { display: inline-block; margin-top: 2px; color: #708897; font-size: 9px; font-weight: 700; }
    .adr-type { display: inline-flex; align-items: center; gap: 5px; padding: 4px 8px; border-radius: 999px; color: #376278; background: #eef6f8; font-size: 9.5px; font-weight: 700; white-space: nowrap; }
    .adr-note { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 9px 11px; border-radius: 8px; color: #4d6878; background: #f7fafb; font-size: 10.5px; margin-bottom: 11px; }
    .adr-save-status { display: inline-flex; align-items: center; gap: 6px; flex: 0 0 auto; min-height: 30px; padding: 5px 10px; border: 1px solid #cfe3ec; border-radius: 999px; background: #f6fbfd; color: #456474; font-weight: 600; }
    .adr-save-status.is-saving { border-color: #b9d8f5; background: #eef7ff; color: #1769aa; }
    .adr-save-status.is-saved { border-color: #a9dec1; background: #edf9f2; color: #16834a; }
    .adr-save-status.is-error { border-color: #f2b8b5; background: #fff1f0; color: #c4312c; }
    .adr-check { display: inline-flex; align-items: center; justify-content: center; margin: 0; cursor: pointer; }
    .adr-check input { position: absolute; width: 1px; height: 1px; opacity: 0; }
    .adr-check span { position: relative; width: 23px; height: 23px; border: 2px solid #8ca5b3; border-radius: 6px; background: #fff; box-shadow: 0 1px 2px rgba(18, 69, 116, .08); transition: background .16s ease, border-color .16s ease, box-shadow .16s ease, transform .12s ease; }
    .adr-check:hover span { border-color: #1476d4; box-shadow: 0 0 0 4px rgba(20, 118, 212, .10); }
    .adr-check input:focus-visible + span { border-color: #1476d4; box-shadow: 0 0 0 4px rgba(20, 118, 212, .18); }
    .adr-check input:checked + span { border-color: #1476d4; background: #1476d4; }
    .adr-check input:checked + span::after { content: ''; position: absolute; left: 7px; top: 3px; width: 6px; height: 11px; border: solid #fff; border-width: 0 2px 2px 0; transform: rotate(45deg); }
    .adr-check input:active + span { transform: scale(.92); }
    .adr-check input:disabled + span { border-color: #d6e0e5; background: #edf1f3; box-shadow: none; cursor: not-allowed; opacity: .7; }
    .adr-loading { opacity: .55; pointer-events: none; }
    .adr-back { display: inline-flex; align-items: center; gap: 7px; }
    @media (max-width: 760px) { .adr-controls { grid-template-columns: 1fr; } .adr-scope { display: flex; } .adr-scope .btn { flex: 1; min-width: 0; } .adr-note { align-items: flex-start; flex-direction: column; } .adr-card-head { align-items: flex-start; } }
</style>
<div class="co-page adr-page">
    <section class="co-hero">
        <div class="co-hero__main">
            <div class="co-hero__icon"><i class="fas fa-file-upload"></i></div>
            <div>
                <div class="co-eyebrow">CONFIGURACIÓN CLÍNICA · V3</div>
                <h1>Documentos requeridos por seguro</h1>
                <p>Seleccione los documentos que Recepción debe solicitar según seguro y tipo de atención.</p>
            </div>
        </div>
        <a class="btn btn-light adr-back" href="{{ $backUrl }}"><i class="fas fa-arrow-left"></i> Regresar</a>
    </section>
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif
    <div id="adrContent">@include('v3.clinical.agenda_documents.requirements_content')</div>
</div>
<script>
    (function () {
        var content = document.getElementById('adrContent');
        if (!content || !window.fetch) return;

        function load(url) {
            var target = new URL(url, window.location.origin);
            target.searchParams.set('partial', '1');
            content.classList.add('adr-loading');
            fetch(target.toString(), {
                credentials: 'same-origin',
                headers: {'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html'}
            }).then(function (response) {
                if (!response.ok) throw new Error('No se pudo cargar la configuración.');
                return response.text();
            }).then(function (html) {
                content.innerHTML = html;
                target.searchParams.delete('partial');
                window.history.replaceState({}, '', target.toString());
            }).catch(function (error) {
                alert(error.message || 'No se pudo cargar la configuración.');
            }).then(function () {
                content.classList.remove('adr-loading');
            });
        }

        function setSaveStatus(form, state, message) {
            var saveState = form._adrSaveState,
                status = saveState ? saveState.status : null,
                icons = {saving: 'fa-spinner fa-spin', saved: 'fa-check-circle', error: 'fa-exclamation-circle'};
            if (!status) return;
            status.className = 'adr-save-status is-' + state;
            status.textContent = '';
            var icon = document.createElement('i');
            icon.className = 'fas ' + icons[state];
            status.appendChild(icon);
            status.appendChild(document.createTextNode(' ' + message));
        }

        function saveNext(form) {
            var state = form._adrSaveState;
            if (!state || state.saving || !state.pending) return;

            var data = state.pending;
            state.pending = null;
            state.saving = true;
            setSaveStatus(form, 'saving', 'Guardando...');

            fetch(form.action, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'},
                body: data
            }).then(function (response) {
                return response.json().catch(function () { return {}; }).then(function (payload) {
                    if (!response.ok || !payload.ok) {
                        var message = payload.message || (payload.errors ? Object.values(payload.errors)[0][0] : '') || 'No se pudo guardar la configuración.';
                        throw new Error(message);
                    }
                    return payload;
                });
            }).then(function () {
                if (!state.pending) setSaveStatus(form, 'saved', 'Cambios guardados');
            }).catch(function (error) {
                setSaveStatus(form, 'error', error.message || 'Error al guardar');
            }).then(function () {
                state.saving = false;
                saveNext(form);
            });
        }

        function autoSave(form) {
            if (!form._adrSaveState) {
                var note = form.previousElementSibling;
                form._adrSaveState = {
                    saving: false,
                    pending: null,
                    status: note ? note.querySelector('.adr-save-status') : null
                };
            }
            form._adrSaveState.pending = new FormData(form);
            saveNext(form);
        }

        function refreshSelectionSummary(form) {
            var rows = form.querySelectorAll('tbody tr'),
                selected = form.querySelectorAll('.js-adr-use:checked').length,
                badge = content.querySelector('.adr-count');
            rows.forEach(function (row) {
                var checkbox = row.querySelector('.js-adr-use');
                if (checkbox) row.classList.toggle('is-selected', checkbox.checked);
            });
            if (badge) badge.textContent = selected + ' de ' + form.querySelectorAll('.js-adr-use').length + ' activos';
        }

        document.addEventListener('change', function (event) {
            if (event.target.id === 'adrInsurance') {
                var filter = event.target.closest('#adrFilter');
                if (!filter) return;
                var url = new URL(filter.action || window.location.href, window.location.origin);
                url.search = new URLSearchParams(new FormData(filter)).toString();
                load(url.toString());
                return;
            }

            if (!event.target.matches('.js-adr-use, .js-adr-required')) return;
            var form = event.target.closest('#adrRequirementsForm');
            if (!form) return;

            if (event.target.classList.contains('js-adr-use')) {
                var required = event.target.closest('tr').querySelector('.js-adr-required');
                if (required) {
                    required.disabled = !event.target.checked;
                    if (!event.target.checked) required.checked = false;
                }
            }
            refreshSelectionSummary(form);
            autoSave(form);
        });

        document.addEventListener('click', function (event) {
            var link = event.target.closest('.js-adr-scope');
            if (!link) return;
            event.preventDefault();
            load(link.href);
        });

        document.addEventListener('submit', function (event) {
            if (event.target.id === 'adrFilter') {
                event.preventDefault();
                var url = new URL(event.target.action || window.location.href, window.location.origin);
                url.search = new URLSearchParams(new FormData(event.target)).toString();
                load(url.toString());
                return;
            }
            if (event.target.id === 'adrRequirementsForm') event.preventDefault();
        });
    })();
</script>
