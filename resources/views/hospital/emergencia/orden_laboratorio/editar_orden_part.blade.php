<style>
    .hospital-lab-editor { padding:20px; color:#174763; }
    .hospital-lab-editor .lab-filter-grid { display:grid; grid-template-columns:repeat(2,minmax(160px,1fr)) auto; gap:14px; align-items:end; }
    .hospital-lab-editor .lab-filter-grid label { display:block; font-weight:600; }
    .hospital-lab-editor .lab-actions { display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin:16px 0; }
    .hospital-lab-editor .btn { padding:9px 14px; border-radius:6px; }
    .hospital-lab-editor .btn-outline-primary { background:#fff; color:#0877a8 !important; border:1px solid #9cbfce; }
    .hospital-lab-editor .btn-primary { background:#0877a8; color:#fff !important; }
    .hospital-lab-editor .lab-profile { max-width:360px; }
    .hospital-lab-editor .lab-feedback { min-height:22px; margin:10px 0; color:#246650; }
    .hospital-lab-editor .lab-feedback.is-error { background:#fff1f0; color:#a52c28; padding:10px; border-radius:6px; }
    .hospital-lab-editor .lab-list { max-height:600px; overflow:auto; border:1px solid #d7e6ee; border-radius:8px; padding:14px; }
    .hospital-lab-editor .lab-exam-group { margin-bottom:18px; }
    .hospital-lab-editor .lab-exam-group h5 { background:#edf6fa; padding:10px; color:#124464; border-radius:5px; }
    .hospital-lab-editor .lab-exam-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:8px; }
    .hospital-lab-editor .lab-exam-option { display:flex; align-items:flex-start; gap:9px; border:1px solid #d8e5ec; border-radius:6px; padding:10px; cursor:pointer; margin:0; color:#174763; }
    .hospital-lab-editor .lab-exam-option.is-selected { background:#e4f4ff; border-color:#0877a8; }
    .hospital-lab-editor input[type=checkbox] { appearance:auto !important; opacity:1 !important; position:static !important; width:17px; height:17px; flex-shrink:0; margin:2px 0; accent-color:#0877a8; }
    .hospital-lab-editor .lab-exam-option small { display:block; color:#617584; }
    .hospital-lab-editor .lab-catalog-summary { color:#526d80; margin:0 0 12px; }
    .hospital-lab-editor .lab-catalog-empty { padding:22px; text-align:center; color:#526d80; }
    @media(max-width:900px) { .hospital-lab-editor .lab-exam-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media(max-width:600px) { .hospital-lab-editor .lab-filter-grid, .hospital-lab-editor .lab-exam-grid { grid-template-columns:1fr; } }
</style>
<div class="hospital-lab-editor" id="hospital-lab-editor-{{ $orden->id }}">
    <h4>Orden #{{ $orden->id }} · {{ $orden->seguro->nombre }}</h4>
    <p>Busque y marque los exámenes. Cada cambio se guarda en esta orden y se refleja en el formulario 010A.</p>
    <form class="js-lab-search" action="{{ route('hospital.laboratory.catalog', ['id_solicitud' => $solicitud->id, 'order' => $orden->id]) }}" method="get">
        @csrf
        <div class="lab-filter-grid">
            <div><label for="lab-search-{{ $orden->id }}">Examen: nombre o código</label><input type="text" id="lab-search-{{ $orden->id }}" class="form-control" name="buscador" placeholder="Ej.: hemograma" autocomplete="off"></div>
            <div><label for="lab-group-{{ $orden->id }}">Agrupador</label><input type="text" id="lab-group-{{ $orden->id }}" class="form-control" name="buscador2" placeholder="Ej.: hematología" autocomplete="off"></div>
            <button class="btn btn-primary" type="submit">Buscar</button>
        </div>
        <div class="lab-actions">
            <label><input type="checkbox" name="seleccionados" value="1"> Ver seleccionados</label>
            <button class="btn btn-outline-primary js-lab-reset" type="button">Limpiar filtros</button>
            <strong class="js-lab-count">{{ count($detalles_ch) }} seleccionados</strong>
        </div>
    </form>
    @if((int) $orden->estado === -1)
        <div class="lab-actions">
            <label for="lab-profile-{{ $orden->id }}">Perfil opcional</label>
            <select class="form-control lab-profile" id="lab-profile-{{ $orden->id }}" @if($protocolos->isEmpty()) disabled @endif>
                <option value="">{{ $protocolos->isEmpty() ? 'No tiene perfiles guardados' : 'Seleccione un perfil' }}</option>
                @foreach($protocolos as $protocolo)<option value="{{ $protocolo->id }}">{{ $protocolo->nombre }}</option>@endforeach
            </select>
            <button class="btn btn-outline-primary js-lab-profile" type="button" @if($protocolos->isEmpty()) disabled @endif>Aplicar perfil</button>
            <button class="btn btn-outline-primary js-lab-clear" type="button">Quitar todos los exámenes</button>
        </div>
    @else
        <p>Esta solicitud ya está confirmada y se muestra en modo de consulta.</p>
    @endif
    <div class="lab-feedback" role="status" aria-live="polite"></div>
    <div class="lab-list">@include('hospital.emergencia.orden_laboratorio.listado')</div>
</div>
<script>
(function () {
    var root = $('#hospital-lab-editor-{{ $orden->id }}');
    var form = root.find('.js-lab-search'), list = root.find('.lab-list'), feedback = root.find('.lab-feedback');
    var request = null, timer = null, version = 0, saving = false;
    function message(text, error) { feedback.text(text).toggleClass('is-error', !!error); }
    function errorMessage(xhr) { return xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'No se pudo completar la operación. Actualice la lista e inténtelo de nuevo.'; }
    function search() {
        clearTimeout(timer);
        if (saving) return;
        if (request) request.abort();
        var currentVersion = ++version;
        message('Buscando exámenes…');
        request = $.ajax({url:form.attr('action'), method:'GET', data:form.serialize(), dataType:'html'})
            .done(function (html) {
                if (currentVersion !== version) return;
                list.html(html);
                root.find('.js-lab-count').text(list.find('[data-selected-count]').attr('data-selected-count') + ' seleccionados');
                message('Lista actualizada.');
            }).fail(function (xhr, status) { if (status !== 'abort' && currentVersion === version) message(errorMessage(xhr), true); });
    }
    function busy(value) {
        saving = value;
        var controls = root.find('input,button,select').add(root.closest('.laboratory-order-item').find('form[action$="/confirmar"] button'));
        controls.each(function () {
            if (value) { $(this).data('lab-disabled', this.disabled); this.disabled = true; }
            else { this.disabled = !!$(this).data('lab-disabled'); }
        });
    }
    function save(data, checkbox) {
        if (saving) return;
        clearTimeout(timer); ++version; if (request) request.abort();
        data._token = form.find('[name=_token]').val();
        busy(true); message('Guardando selección…');
        $.ajax({url:@json(route('hospital.laboratory.selection', ['id_solicitud' => $solicitud->id, 'order' => $orden->id])), method:'POST', data:data, dataType:'json'})
            .done(function (result) {
                var ids = result.selected.map(String);
                root.find('.js-lab-exam').each(function () { this.checked = ids.indexOf(this.value) !== -1; $(this).closest('label').toggleClass('is-selected', this.checked); });
                root.find('.js-lab-count').text(result.count + ' seleccionados');
                list.find('[data-selected-count]').attr('data-selected-count', result.count);
                list.find('.lab-catalog-summary').text(list.find('.js-lab-exam').length + ' exámenes mostrados · ' + result.count + ' seleccionados en la orden.');
                message('Selección guardada. ' + result.count + ' exámenes en la orden.');
                busy(false);
                if (data.action === 'profile' || data.action === 'clear' || form.find('[name=seleccionados]').prop('checked')) search();
            }).fail(function (xhr) {
                if (checkbox) checkbox.checked = !checkbox.checked;
                busy(false); message(errorMessage(xhr), true);
            });
    }
    form.on('submit', function (event) { event.preventDefault(); search(); });
    form.find('input[type=text]').on('input', function () { form.find('[name=seleccionados]').prop('checked', false); clearTimeout(timer); timer = setTimeout(search, 300); });
    form.find('[name=seleccionados]').on('change', search);
    root.on('click', '.js-lab-reset', function () { form.find('input[type=text]').val(''); form.find('[name=seleccionados]').prop('checked', false); search(); });
    root.on('change', '.js-lab-exam', function () { save({action:this.checked ? 'add' : 'remove', exam_id:this.value}, this); });
    root.on('click', '.js-lab-profile', function () {
        var id = root.find('.lab-profile').val();
        if (!id) { message('Seleccione un perfil para aplicarlo.', true); return; }
        if (confirm('Se reemplazarán los exámenes de esta orden por los del perfil seleccionado. ¿Desea continuar?')) save({action:'profile',profile_id:id});
    });
    root.on('click', '.js-lab-clear', function () { if (confirm('¿Desea quitar todos los exámenes de esta orden?')) save({action:'clear'}); });
})();
</script>
