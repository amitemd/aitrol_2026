@extends('ticket_soporte_tecnico.base')
@section('action-content')
<style>
.gateway-compose { max-width: 1160px; margin: 0 auto; padding: 24px 18px; }
.gateway-compose .box { background: #fff; border: 1px solid #dce5ed; border-radius: 14px; overflow: hidden; box-shadow: 0 6px 24px rgba(30,65,95,.06); }
.gateway-compose .box-header { padding: 24px 28px; background: #f7fafc; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
.gateway-compose .box-title { font-size: 22px; font-weight: 600; color: #163d60; }
.gateway-compose .box-body { padding: 28px; }
.gateway-compose .form-control { border-radius: 8px; border-color: #cedbe6; min-height: 44px; box-shadow: none; }
.gateway-compose .form-control:focus { border-color: #2e78b7; box-shadow: 0 0 0 3px rgba(46,120,183,.12); }
.gateway-compose label { color: #29465e; margin-bottom: 8px; }
.gateway-compose .form-group { margin-bottom: 22px; }
.gateway-compose .help-block { font-size: 12px; color: #64788b; margin: 8px 0 0; line-height: 1.6; }
.gateway-compose .btn { border-radius: 8px; padding: 10px 18px; font-weight: 600; }
.gateway-compose .btn-primary { background: #1968a8; border-color: #1968a8; }
.gateway-compose .gateway-intro { color: #64788b; margin-bottom: 24px; }
.gateway-fields { display: grid; grid-template-columns: minmax(0, 1fr) 220px; gap: 20px; }
.gateway-upload { border: 2px dashed #bacfe0; border-radius: 12px; background: #f7fbff; padding: 24px; display: flex; align-items: center; gap: 18px; }
.gateway-upload-icon { color: #1968a8; background: #e5f0fa; border-radius: 12px; padding: 16px; font-size: 26px; }
.gateway-upload-copy { flex: 1; }
.gateway-upload-copy strong { display: block; color: #234963; font-size: 15px; }
.gateway-picker { position: relative; overflow: hidden; flex-shrink: 0; }
.gateway-picker input { position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
.gateway-picker:focus-within { outline: 3px solid #86b7dd; outline-offset: 3px; }
.gateway-files { list-style: none; padding: 0; margin: 12px 0 0; }
.gateway-files li { padding: 10px 14px; border: 1px solid #e0e8ee; border-radius: 8px; margin-top: 7px; display: flex; gap: 12px; align-items: center; }
.gateway-files .file-name { flex: 1; overflow-wrap: anywhere; min-width: 0; }
.gateway-files .file-size { color: #64788b; white-space: nowrap; font-size: 12px; }
.gateway-files li.invalid { border-color: #d9534f; color: #a52e2a; }
.gateway-footer { border-top: 1px solid #e5ecf2; margin-top: 26px; padding-top: 20px; display: flex; align-items: center; justify-content: space-between; gap: 18px; }
.gateway-reference { font-size: 11px; color: #718292; overflow-wrap: anywhere; }
.gateway-origin { font-size: 12px; color: #64788b; overflow-wrap: anywhere; }
@media(max-width: 680px) { .gateway-fields { grid-template-columns: 1fr; gap: 0; } .gateway-compose .box-body, .gateway-compose .box-header { padding: 18px; } .gateway-upload { flex-wrap: wrap; padding: 18px; } .gateway-picker { width: 100%; } .gateway-footer { align-items: stretch; flex-direction: column; } .gateway-compose .box-title { font-size: 18px; } }
</style>
<section class="content gateway-compose">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Nueva solicitud de soporte</h3>
            <a href="{{ route('ticket_soporte_tecnico.index') }}" class="btn btn-default pull-right">Volver a soporte</a>
        </div>
        <div class="box-body">
            @if(session('gateway_success'))
                <div class="alert alert-success" role="status">
                    {{ session('gateway_success.duplicate') ? 'El ticket ya estaba registrado en Gateway.' : 'Ticket enviado correctamente a Gateway.' }}
                    <br>Ticket: <strong>{{ session('gateway_success.id') }}</strong>
                    <br>Referencia: {{ session('gateway_success.external_id') }}
                    <br><a href="{{ route('ticket_soporte_tecnico.index') }}">Ver estado en Soporte Técnico</a>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            @if(!$configured)
                <div class="alert alert-warning">El envío a Gateway está pendiente de configuración por el administrador.</div>
            @endif
            <p class="gateway-intro">Cuéntenos qué está pasando. El equipo de soporte recibirá su solicitud y podrá revisar la evidencia adjunta.</p>
            <form method="post" enctype="multipart/form-data" action="{{ route('gateway_tickets.store') }}" id="gateway-ticket-form">
                {{ csrf_field() }}
                <input type="hidden" name="external_id" value="{{ $externalId }}">
                <div class="gateway-fields">
                <div class="form-group">
                    <label for="gateway-title">Título</label>
                    <input class="form-control" id="gateway-title" name="title" maxlength="200" required placeholder="Ej.: No puedo imprimir desde recepción" value="{{ old('title') }}">
                </div>
                <div class="form-group">
                    <label for="gateway-priority">Prioridad</label>
                    <select class="form-control" id="gateway-priority" name="priority" required>
                        @foreach(['low' => 'Baja', 'normal' => 'Normal', 'high' => 'Alta', 'urgent' => 'Urgente'] as $value => $label)
                            <option value="{{ $value }}" {{ old('priority', 'normal') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                </div>
                <div class="form-group">
                    <label for="gateway-description">Descripción</label>
                    <textarea class="form-control" id="gateway-description" name="description" rows="6" maxlength="20000" required placeholder="¿Qué intentaba hacer? ¿Qué mensaje aparece y desde cuándo ocurre?">{{ old('description') }}</textarea>
                    <p class="help-block">Incluya el área y los pasos para reproducir el problema.</p>
                </div>
                <div class="form-group">
                    <label for="gateway-attachments">Evidencia <span class="text-muted">(opcional)</span></label>
                    <div class="gateway-upload">
                        <span class="fa fa-cloud-upload gateway-upload-icon" aria-hidden="true"></span>
                        <div class="gateway-upload-copy"><strong>Una imagen ayuda a entender el problema</strong><p class="help-block" id="gateway-file-help">PDF, JPG, PNG o WebP · Hasta 5 archivos · 5 MB por archivo</p></div>
                        <span class="btn btn-primary gateway-picker"><i class="fa fa-paperclip" aria-hidden="true"></i> Seleccionar evidencia
                            <input type="file" id="gateway-attachments" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.webp" aria-label="Seleccionar evidencia" aria-describedby="gateway-file-help gateway-file-status">
                        </span>
                    </div>
                    <p id="gateway-file-status" class="help-block" aria-live="polite">Sin archivos seleccionados.</p>
                    <ul id="gateway-file-list" class="gateway-files"></ul>
                    <button type="button" id="gateway-clear-files" class="btn btn-default btn-sm" style="display:none; margin-top:10px">Quitar archivos</button>
                    <p class="help-block">Los enlaces de descarga se incluirán en el ticket. Quien tenga el enlace podrá acceder a la evidencia.</p>
                    @if($errors->any())<p class="help-block">Si la evidencia ya se guardó, reintente sin volver a seleccionarla.</p>@endif
                </div>
                <div class="gateway-footer">
                <div><p class="gateway-origin">Sucursal de origen: <strong>{{ $originUrl }}</strong></p><span class="gateway-reference">Referencia: {{ $externalId }}</span></div>
                <button type="submit" class="btn btn-primary" {{ !$configured ? 'disabled' : '' }}>Enviar ticket</button>
                </div>
            </form>
        </div>
    </div>
</section>
<script>
document.getElementById('gateway-ticket-form').addEventListener('submit', function (event) {
    var input = document.getElementById('gateway-attachments');
    var files = input.files;
    var error = files.length > 5 ? 'Seleccione como máximo 5 archivos.' : '';
    for (var i = 0; i < files.length; i++) {
        if (files[i].size > 5 * 1024 * 1024) { error = 'Cada archivo debe pesar como máximo 5 MB.'; }
    }
    input.setCustomValidity(error);
    if (error) { event.preventDefault(); input.reportValidity(); return; }
    var button = this.querySelector('button[type="submit"]');
    button.disabled = true;
    button.textContent = 'Enviando...';
});
function renderGatewayFiles() {
    var input = document.getElementById('gateway-attachments');
    input.setCustomValidity('');
    var list = document.getElementById('gateway-file-list');
    list.textContent = '';
    var files = input.files;
    document.getElementById('gateway-file-status').textContent = files.length > 5 ? 'Seleccione como máximo 5 archivos.' : (files.length ? files.length + ' archivo(s) seleccionado(s).' : 'Sin archivos seleccionados.');
    document.getElementById('gateway-clear-files').style.display = files.length ? 'inline-block' : 'none';
    for (var i = 0; i < files.length; i++) {
        var row = document.createElement('li');
        var name = document.createElement('span');
        name.className = 'file-name'; name.textContent = files[i].name;
        var size = document.createElement('span');
        size.className = 'file-size'; size.textContent = (files[i].size / 1024 / 1024).toFixed(2) + ' MB';
        if (files[i].size > 5 * 1024 * 1024) { row.className = 'invalid'; size.textContent += ' — Supera 5 MB'; }
        row.appendChild(name); row.appendChild(size); list.appendChild(row);
    }
}
document.getElementById('gateway-attachments').addEventListener('change', renderGatewayFiles);
document.getElementById('gateway-clear-files').addEventListener('click', function () { document.getElementById('gateway-attachments').value = ''; renderGatewayFiles(); });
window.addEventListener('pageshow', function () {
    var button = document.querySelector('#gateway-ticket-form button[type="submit"]');
    button.disabled = {{ $configured ? 'false' : 'true' }};
    button.textContent = 'Enviar ticket';
    renderGatewayFiles();
});
</script>
@endsection
