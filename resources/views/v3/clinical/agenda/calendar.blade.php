@include('v3.clinical.operations._styles')
@include('v3.clinical.agenda._doctor_styles')
<div class="co-page da-page">
  <section class="co-hero">
    <div class="co-hero__main">
      <div class="co-hero__icon"><i class="fas fa-user-md"></i></div>
      <div>
        <div class="co-eyebrow">AGENDA MÉDICA · V3</div>
        <h1>Dr(a). {{$doctor->display_name}}</h1>
        <p>Consultas, procedimientos y reuniones en una sola vista {{$viewMode==='dia'?'diaria':'semanal'}}.</p>
      </div>
    </div><a class="btn btn-light" href="{{route('v3.clinical-agenda.index')}}"><i class="fas fa-arrow-left mr-1"></i> Directorio</a>
  </section>
  @if(session('agenda_success'))<div class="alert alert-success da-alert">{{session('agenda_success')}}</div>@endif
  @if($errors->any())<div class="alert alert-danger da-alert">{{$errors->first()}}</div>@endif
  <div class="da-live-message" id="doctorAgendaMessage" role="status" hidden></div>
  <section class="co-card da-board" id="doctorAgendaBoard" data-week-endpoint="{{route('v3.doctor-agenda.week',array('doctor'=>$doctor->id))}}" aria-live="polite">@include('v3.clinical.agenda._calendar_week')</section>
</div>
<div class="modal fade" id="doctorActivityModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl da-agenda-modal" role="document">
    <div class="modal-content" id="doctorActivityModalContent"></div>
  </div>
</div>
<div class="modal fade da-receipt-modal" id="doctorReceiptModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content" id="doctorReceiptModalContent"><div class="da-modal-loading"><span class="da-spinner"></span><strong>Cargando recibos…</strong></div></div>
  </div>
</div>
<div class="modal fade da-label-modal" id="doctorLabelModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header da-modal-head">
        <div><small>AGENDA MÉDICA · V3</small><h5 class="modal-title"><i class="fas fa-tags mr-2"></i>Etiqueta del paciente</h5><span>Vista previa lista para impresión</span></div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body" id="doctorLabelPreview"><div class="da-modal-loading"><span class="da-spinner"></span><strong>Cargando etiqueta…</strong></div></div>
      <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button><button type="button" class="btn btn-primary js-print-label"><i class="fas fa-print mr-1"></i> Imprimir etiqueta</button></div>
    </div>
  </div>
</div>
<div class="modal fade da-id-document-modal" id="doctorIdDocumentModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document"><div class="modal-content">
    <div class="modal-header da-modal-head"><div><small>DOCUMENTACIÓN · V3</small><h5 class="modal-title"><i class="fas fa-id-card mr-2"></i>Documento de identificación</h5><span>Vista previa del archivo registrado</span></div><button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button></div>
    <div class="modal-body"><div class="da-document-loading" id="doctorIdDocumentLoading"><span class="da-spinner"></span><strong>Cargando documento…</strong></div><iframe id="doctorIdDocumentFrame" title="Vista previa del documento de identificación"></iframe></div>
    <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button><button type="button" class="btn btn-primary js-print-id-document"><i class="fas fa-print mr-1"></i> Imprimir</button></div>
  </div></div>
</div>
<div class="modal fade da-payment-preview-modal" id="agendaPaymentFileModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document"><div class="modal-content">
    <div class="modal-header da-modal-head"><div><small>COMPROBANTE DE PAGO · V3</small><h5 class="modal-title"><i class="fas fa-file-invoice mr-2"></i><span id="agendaPaymentFileTitle">Vista previa del comprobante</span></h5><span>Visualiza el archivo sin salir de la agenda</span></div><button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button></div>
    <div class="modal-body"><div class="da-document-loading" id="agendaPaymentFileLoading"><span class="da-spinner"></span><strong>Cargando comprobante…</strong></div><iframe id="agendaPaymentFileFrame" title="Vista previa del comprobante de pago"></iframe></div>
    <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button></div>
  </div></div>
</div>
<style>
.da-tools-grid .js-id-document-modal>span{display:flex;flex-direction:column;align-items:center;line-height:1.15}.da-tools-grid .da-doc-status{display:block;margin-top:3px;font-size:8px;font-weight:700;opacity:.85}.da-tools-grid .js-id-document-modal.is-unavailable{background:linear-gradient(135deg,#647985,#81939c)!important}.da-id-document-modal{z-index:1080}.da-id-document-modal .modal-dialog{width:94vw;max-width:1180px}.da-id-document-modal .modal-content{overflow:hidden;border:0;border-radius:14px;box-shadow:0 24px 70px rgba(5,42,61,.3)}.da-id-document-modal .modal-body{height:74vh;min-height:480px;padding:0;background:#eaf2f5}.da-id-document-modal iframe{display:none;width:100%;height:100%;border:0;background:#fff}.da-document-loading{display:flex;height:100%;align-items:center;justify-content:center;gap:12px;color:#315c70;font-weight:800}@media(max-width:680px){.da-id-document-modal .modal-dialog{width:auto;margin:0}.da-id-document-modal .modal-content{min-height:100vh;border-radius:0}.da-id-document-modal .modal-body{height:calc(100vh - 140px);min-height:0}}
.da-payment-preview-modal{z-index:1090}.da-payment-preview-modal .modal-dialog{width:94vw;max-width:1180px}.da-payment-preview-modal .modal-content{overflow:hidden;border:0;border-radius:14px;box-shadow:0 24px 70px rgba(5,42,61,.3)}.da-payment-preview-modal .modal-body{height:74vh;min-height:480px;padding:0;background:#eaf2f5}.da-payment-preview-modal iframe{display:none;width:100%;height:100%;border:0;background:#fff}@media(max-width:680px){.da-payment-preview-modal .modal-dialog{width:auto;margin:0}.da-payment-preview-modal .modal-content{min-height:100vh;border-radius:0}.da-payment-preview-modal .modal-body{height:calc(100vh - 140px);min-height:0}}
.da-receipt-modal{z-index:1095}.da-receipt-modal.show{background:rgba(4,31,47,.56)}.da-receipt-modal .modal-dialog{width:92vw;max-width:1120px}.da-receipt-modal .modal-content{overflow:hidden;border:0;border-radius:15px;box-shadow:0 25px 75px rgba(4,36,53,.34)}.da-receipt-modal .rc-modal-head{align-items:center;padding:15px 19px}.rc-modal-head__actions{display:flex;align-items:center;gap:13px}.rc-modal-head__actions .btn{border:0;border-radius:8px;color:#075679;font-size:11px;font-weight:850}.rc-modal-head__actions .close{position:static;margin:0;padding:5px}.rc-modal-body{max-height:68vh;overflow-y:auto;padding:14px 16px;background:#f4f9fb}.rc-modal-error[hidden]{display:none!important}.rc-modal-summary{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:9px;margin-bottom:12px}.rc-modal-summary>div{display:grid;grid-template-columns:36px minmax(0,1fr);grid-template-rows:auto auto;column-gap:10px;align-items:center;padding:11px 13px;border:1px solid #d4e5ed;border-radius:10px;background:#fff}.rc-modal-summary>div>span{display:grid;grid-row:1/3;width:36px;height:36px;place-items:center;border-radius:9px}.rc-modal-summary .is-blue{color:#087da4;background:#e2f3fa}.rc-modal-summary .is-amber{color:#93630a;background:#fff0d1}.rc-modal-summary .is-green{color:#19734f;background:#e2f4ec}.rc-modal-summary .is-teal{color:#087f82;background:#ddf5f2}.rc-modal-summary small{color:#718998;font-size:9px;font-weight:800;text-transform:uppercase}.rc-modal-summary strong{color:#123f5d;font-size:17px}.rc-modal-panel{overflow:hidden;border:1px solid #d4e5ed;border-radius:11px;background:#fff}.rc-modal-panel>header{display:flex;align-items:center;gap:9px;padding:10px 13px;border-bottom:1px solid #dbe8ed;background:#eef8fb}.rc-modal-panel>header>div{display:grid;width:30px;height:30px;place-items:center;border-radius:8px;color:#087fa2;background:#daf2f7}.rc-modal-panel>header strong,.rc-modal-panel>header small{display:block}.rc-modal-panel>header strong{color:#123f5d;font-size:13px}.rc-modal-panel>header small{color:#718998;font-size:9.5px}.rc-modal-table-wrap{overflow-x:auto;padding:7px 11px}.rc-modal-table{width:100%;min-width:870px;border-collapse:separate;border-spacing:0}.rc-modal-table th{padding:9px 10px;border-bottom:1px solid #dce7ec;background:#f5f9fb;color:#5b7585;font-size:9px;font-weight:850;text-transform:uppercase}.rc-modal-table th:first-child{border-radius:7px 0 0 7px}.rc-modal-table th:last-child{border-radius:0 7px 7px 0}.rc-modal-table td{padding:11px 10px;border-bottom:1px solid #e5eef2;color:#294c61;font-size:10.5px;vertical-align:middle}.rc-modal-table tr:last-child td{border-bottom:0}.rc-number,.rc-date{display:inline-flex;align-items:center;gap:6px;white-space:nowrap}.rc-number{color:#075a7c;font-weight:850}.rc-number i{color:#11a1a8}.rc-date i{color:#7993a2}.rc-state{display:inline-flex;align-items:center;gap:5px;padding:5px 8px;border-radius:16px;font-size:9px;font-weight:850;white-space:nowrap}.rc-state.is-issued{color:#19734f;background:#e2f4ec}.rc-state.is-pending{color:#8a6016;background:#fff2d8}.rc-detail{min-width:190px;max-width:330px}.rc-detail>span{display:block;line-height:1.4;overflow-wrap:anywhere}.rc-detail em{color:#899eaa}.rc-value{text-align:right;color:#075a7c!important;font-size:12px!important;font-weight:900;white-space:nowrap}.rc-actions{display:flex;justify-content:flex-end;gap:5px;white-space:nowrap}.rc-action{display:inline-flex;align-items:center;gap:4px;min-height:28px;padding:5px 8px;border:1px solid;border-radius:7px;background:#fff;font:inherit;font-size:9px;font-weight:850;text-decoration:none!important;cursor:pointer}.rc-action.is-edit{color:#087c9b;border-color:#a9d4df}.rc-action.is-print{color:#187151;border-color:#afdbc8}.rc-action.is-void{color:#c8444b;border-color:#efb7bb}.rc-action.is-proforma{color:#657584;border-color:#cdd8de}.rc-action:hover{background:#f0f8fa}.rc-action:disabled{opacity:.55;cursor:wait}.rc-empty td{height:190px;text-align:center!important;color:#78909f!important}.rc-empty i,.rc-empty strong,.rc-empty span{display:block}.rc-empty i{margin-bottom:9px;color:#b7ced8;font-size:29px}.rc-empty strong{margin-bottom:3px;color:#365d72;font-size:13px}.rc-modal-footer{justify-content:space-between;padding:10px 16px;color:#718998;background:#fbfdfe;font-size:10px}.rc-modal-footer .btn{border-radius:8px;font-size:11px;font-weight:800}@media(max-width:760px){.da-receipt-modal .modal-dialog{width:auto;margin:0}.da-receipt-modal .modal-content{min-height:100vh;border-radius:0}.rc-modal-body{max-height:calc(100vh - 126px)}.rc-modal-summary{grid-template-columns:repeat(2,minmax(0,1fr))}.rc-modal-head__actions .btn span{display:none}.rc-action span{display:none}}@media(max-width:430px){.rc-modal-summary{grid-template-columns:1fr}}
</style>
<script>
  (function() {
    var board = document.getElementById('doctorAgendaBoard'),
      message = document.getElementById('doctorAgendaMessage'),
      modal = document.getElementById('doctorActivityModal'),
      content = document.getElementById('doctorActivityModalContent'),
      labelModal = document.getElementById('doctorLabelModal'),
      labelPreview = document.getElementById('doctorLabelPreview'),
      labelUrl = '',
      idDocumentModal = document.getElementById('doctorIdDocumentModal'),
      idDocumentFrame = document.getElementById('doctorIdDocumentFrame'),
      idDocumentLoading = document.getElementById('doctorIdDocumentLoading'),
      idDocumentObjectUrl = '',
      paymentFileModal = document.getElementById('agendaPaymentFileModal'),
      paymentFileFrame = document.getElementById('agendaPaymentFileFrame'),
      paymentFileLoading = document.getElementById('agendaPaymentFileLoading'),
      paymentFileTitle = document.getElementById('agendaPaymentFileTitle'),
      paymentFileObjectUrl = '',
      receiptModal = document.getElementById('doctorReceiptModal'),
      receiptContent = document.getElementById('doctorReceiptModalContent'),
      receiptListUrl = '';
    if (!board || !window.fetch) return;

    function headers(json) {
      var value = {
        'X-Requested-With': 'XMLHttpRequest'
      };
      if (json) value.Accept = 'application/json';
      return value;
    }

    function note(text, type) {
      message.className = 'da-live-message alert alert-' + (type || 'success');
      message.textContent = text;
      message.hidden = false;
      window.setTimeout(function() {
        message.hidden = true;
      }, 5000);
    }

    function errorText(data, fallback) {
      if (data && data.errors) {
        for (var key in data.errors)
          if (data.errors.hasOwnProperty(key)) return Array.isArray(data.errors[key]) ? data.errors[key][0] : data.errors[key];
      }
      return data && data.message ? data.message : fallback;
    }

    function agendaAlert(options) {
      if (typeof window.mostrarSweetAlertV3 === 'function') return window.mostrarSweetAlertV3(options);
      if (window.Swal) return window.Swal.fire(options);
      if (options.showCancelButton) return Promise.resolve({ isConfirmed: window.confirm(options.text || options.title) });
      window.alert(options.text || options.title);
      return Promise.resolve({ isConfirmed: true });
    }

    function validationMessages(data, fallback) {
      var messages = [];
      if (data && data.errors) {
        Object.keys(data.errors).forEach(function(key) {
          var values = Array.isArray(data.errors[key]) ? data.errors[key] : [data.errors[key]];
          values.forEach(function(value) {
            if (value && messages.indexOf(String(value)) === -1) messages.push(String(value));
          });
        });
      }
      if (!messages.length && data && data.message) messages.push(String(data.message));
      if (!messages.length) messages.push(fallback);
      return messages;
    }

    function showAgendaErrors(messages, form, firstField, title) {
      var list = '<div style="text-align:left"><p style="margin:0 0 10px">Revisa la siguiente información:</p><ul style="margin:0;padding-left:20px">';
      messages.forEach(function(messageText) { list += '<li style="margin-bottom:6px">' + escapeHtml(messageText) + '</li>'; });
      list += '</ul></div>';
      var roomCreateLink = form ? form.querySelector('.js-room-create-link') : null;
      if (roomCreateLink) {
        list += '<div style="margin-top:14px;padding:12px;border:1px solid #f0cf8d;border-radius:8px;background:#fff9e9;text-align:left">' +
          '<strong style="display:block;margin-bottom:7px;color:#744e00">No existen salas o consultorios activos.</strong>' +
          '<a href="' + escapeHtml(roomCreateLink.href) + '" target="_blank" rel="noopener" style="display:inline-block;padding:8px 12px;border-radius:6px;color:#fff;background:#087fb5;font-weight:700;text-decoration:none">Crear sala o consultorio</a>' +
          '<small style="display:block;margin-top:7px;color:#876d38">Se abrirá el mantenimiento en otra pestaña. Después de crearla, vuelve a abrir Nueva agenda.</small></div>';
      }
      return agendaAlert({
        icon: 'warning',
        title: title || 'Faltan datos para guardar',
        html: list,
        confirmButtonText: 'Revisar formulario',
        confirmButtonColor: '#087fb5'
      }).then(function() {
        if (!firstField || !form) return;
        var field = form.querySelector('[name="' + firstField.replace(/"/g, '') + '"]');
        if (!field) return;
        field.focus();
        if (window.jQuery && field.tagName === 'SELECT' && window.jQuery(field).hasClass('select2-hidden-accessible')) {
          window.jQuery(field).select2('open');
        }
      });
    }

    function clientValidation(form) {
      var typeField = form.querySelector('[name="tipo"]'),
        type = typeField ? parseInt(typeField.value, 10) : 0,
        rules = [
          ['inicio', 'Seleccione la fecha y hora de inicio.'],
          ['fin', 'Seleccione la fecha y hora de fin.']
        ];
      if (type !== 2) {
        rules.push(['id_paciente', 'Seleccione el paciente de la atención.']);
        rules.push(['id_sala', 'Seleccione una sala o consultorio.']);
        rules.push(['id_seguro', 'Seleccione un seguro o convenio.']);
      }
      if (type === 1) rules.push(['id_procedimiento', 'Seleccione el procedimiento que se realizará.']);

      var messages = [], firstField = null;
      rules.forEach(function(rule) {
        var field = form.querySelector('[name="' + rule[0] + '"]');
        if (!field || String(field.value || '').trim() !== '') return;
        if (!firstField) firstField = rule[0];
        messages.push(rule[1]);
      });
      var start = form.querySelector('[name="inicio"]'), end = form.querySelector('[name="fin"]');
      if (start && end && start.value && end.value && new Date(end.value).getTime() <= new Date(start.value).getTime()) {
        if (!firstField) firstField = 'fin';
        messages.push('La fecha y hora de fin debe ser posterior al inicio.');
      }
      if (!form.getAttribute('data-appointment-id') && start && start.value && new Date(start.value).getTime() <= Date.now()) {
        if (!firstField) firstField = 'inicio';
        messages.push('No se puede crear una agenda en una fecha u hora anterior a la actual.');
      }
      return { messages: messages, firstField: firstField };
    }

    function refresh(fragmentUrl, pageUrl, push) {
      board.classList.add('is-loading');
      return fetch(fragmentUrl, {
        credentials: 'same-origin',
        headers: headers(false)
      }).then(function(response) {
        if (!response.ok) throw new Error('No se pudo actualizar la agenda.');
        return response.text();
      }).then(function(html) {
        board.innerHTML = html;
        if (push && pageUrl) history.pushState({}, '', pageUrl);
      }).catch(function(error) {
        note(error.message, 'danger');
      }).then(function() {
        board.classList.remove('is-loading');
      });
    }
    document.addEventListener('change', function(event) {
      var dateInput = event.target.closest('.js-agenda-date');
      if (!dateInput || !dateInput.value) return;
      var pageUrl = new URL(dateInput.getAttribute('data-page-url'), window.location.href),
        fragmentUrl = new URL(dateInput.getAttribute('data-fragment-url'), window.location.href);
      pageUrl.searchParams.set('fecha', dateInput.value);
      fragmentUrl.searchParams.set('fecha', dateInput.value);
      refresh(fragmentUrl.toString(), pageUrl.toString(), true);
    });
    document.addEventListener('click', function(event) {
      var receiptTrigger = event.target.closest('.js-receipt-modal');
      if (receiptTrigger) {
        event.preventDefault();
        openReceiptModal(receiptTrigger.href);
        return;
      }
      var voidReceipt = event.target.closest('.js-void-receipt-modal');
      if (voidReceipt) {
        event.preventDefault();
        voidReceiptFromModal(voidReceipt);
        return;
      }
      var nav = event.target.closest('.js-agenda-nav');
      if (nav) {
        event.preventDefault();
        refresh(nav.getAttribute('data-fragment-url'), nav.href, true);
        return;
      }
      var labelTrigger = event.target.closest('.js-label-modal');
      if (labelTrigger) {
        event.preventDefault();
        openLabelModal(labelTrigger.href);
        return;
      }
      var documentTrigger = event.target.closest('.js-id-document-modal');
      if (documentTrigger) {
        event.preventDefault();
        openIdDocumentModal(documentTrigger);
        return;
      }
      var printIdDocument = event.target.closest('.js-print-id-document');
      if (printIdDocument) {
        event.preventDefault();
        if (idDocumentFrame.contentWindow) { idDocumentFrame.contentWindow.focus(); idDocumentFrame.contentWindow.print(); }
        return;
      }
      var printLabel = event.target.closest('.js-print-label');
      if (printLabel) {
        event.preventDefault();
        printLabelDocument();
        return;
      }
      var trigger = event.target.closest('.js-activity-modal,.js-certificate-modal');
      if (trigger) {
        event.preventDefault();
        var modalUrl = trigger.href;
        if (trigger.classList.contains('js-new-agenda-button')) {
          var dateInput = board.querySelector('.js-new-agenda-date'),
            selectedDate = dateInput ? dateInput.value : '';
          if (!selectedDate) {
            note('Seleccione la fecha en la que desea crear la agenda.', 'warning');
            if (dateInput) dateInput.focus();
            return;
          }
          var now = new Date(),
            pad = function(value) { return String(value).padStart(2, '0'); },
            localDate = now.getFullYear() + '-' + pad(now.getMonth() + 1) + '-' + pad(now.getDate());
          if (selectedDate < localDate) {
            note('No se puede crear una agenda en una fecha anterior a la actual.', 'warning');
            dateInput.focus();
            return;
          }
          var startTime = '08:00';
          if (selectedDate === localDate) {
            now.setMinutes(now.getMinutes() + 1);
            selectedDate = now.getFullYear() + '-' + pad(now.getMonth() + 1) + '-' + pad(now.getDate());
            startTime = pad(now.getHours()) + ':' + pad(now.getMinutes());
            dateInput.value = selectedDate;
          }
          var url = new URL(modalUrl, window.location.href);
          url.searchParams.set('inicio', selectedDate + 'T' + startTime);
          modalUrl = url.toString();
        }
        openModal(modalUrl, trigger.classList.contains('js-certificate-modal'));
        return;
      }
      var cancel = event.target.closest('.js-cancel-activity');
      if (cancel) {
        event.preventDefault();
        agendaAlert({
          icon: 'warning',
          title: '¿Cancelar esta agenda?',
          text: 'La agenda quedará marcada como cancelada y se conservará su trazabilidad.',
          input: 'textarea',
          inputLabel: 'Motivo de cancelación',
          inputPlaceholder: 'Explique por qué se cancela la agenda…',
          inputAttributes: { maxlength: 1000, 'aria-label': 'Motivo de cancelación' },
          target: modal,
          focusConfirm: false,
          focusCancel: false,
          didOpen: function() {
            var input = window.Swal && typeof window.Swal.getInput === 'function' ? window.Swal.getInput() : null;
            if (input) window.setTimeout(function() { input.focus(); }, 0);
          },
          inputValidator: function(value) {
            return !value || !String(value).trim() ? 'Ingrese el motivo de la cancelación.' : undefined;
          },
          showCancelButton: true,
          confirmButtonText: '<i class="fas fa-ban"></i> Sí, cancelar',
          cancelButtonText: 'No, mantener agenda',
          confirmButtonColor: '#d33',
          cancelButtonColor: '#6c8797',
          reverseButtons: true
        }).then(function(result) {
          if (result && result.isConfirmed) cancelActivity(cancel, String(result.value || '').trim());
        });
      }
    });
    document.addEventListener('toggle', function(event) {
      var group = event.target;
      if (!group.matches || !group.matches('.da-agenda-modal .da-tool-group') || !group.open) return;
      var container = group.closest('.da-tool-groups');
      if (!container) return;
      [].slice.call(container.querySelectorAll('.da-tool-group')).forEach(function(other) {
        if (other !== group) other.open = false;
      });
    }, true);

    function openModal(url, compactCertificate) {
      var dialog = modal.querySelector('.modal-dialog');
      if (dialog) dialog.classList.toggle('da-certificate-dialog', !!compactCertificate);
      content.innerHTML = '<div class="da-modal-loading"><span class="da-spinner"></span><strong>Cargando agenda…</strong></div>';
      if (window.jQuery) window.jQuery(modal).modal('show');
      fetch(url, {
        credentials: 'same-origin',
        headers: headers(false)
      }).then(function(response) {
        return response.text().then(function(body) {
          if (!response.ok) {
            var reference = body.match(/AIT-[0-9A-Z-]+/);
            throw new Error('No se pudo abrir la agenda.' + (reference ? ' Código: ' + reference[0] : ''));
          }
          return body;
        });
      }).then(function(html) {
        content.innerHTML = html;
        syncFields(content);
      }).catch(function(error) {
        content.innerHTML = '<div class="modal-body"><div class="alert alert-danger">' + escapeHtml(error.message) + '</div><p class="text-muted">Actualiza el módulo y limpia la caché de rutas y vistas del servidor.</p><button class="btn btn-light" data-dismiss="modal">Cerrar</button></div>';
      });
    }

    function syncFields(root) {
      var type = root.querySelector('.js-activity-type'),
        patient = root.querySelector('.js-patient-select'),
        appointmentTypeDisplay = root.querySelector('.js-appointment-type-display'),
        appointmentTypeValue = root.querySelector('.js-appointment-type-value'),
        appointmentTypeHelp = root.querySelector('.js-appointment-type-help'),
        patientCreate = root.querySelector('[data-patient-create]'),
        form = root.querySelector('.js-activity-form'), startInput = root.querySelector('[name="inicio"]'),
        endInput = root.querySelector('[name="fin"]'), availabilityBox = root.querySelector('.da-availability__slots'),
        refreshAvailability = root.querySelector('.js-refresh-availability'), status = root.querySelector('.js-agenda-status'), availabilityTimer = null;
      if (!type) return;
      if (window.jQuery && window.jQuery.fn.select2) {
        window.jQuery(root).find('.js-activity-form select').each(function() {
          var select = window.jQuery(this);
          if (select.hasClass('select2-hidden-accessible')) select.select2('destroy');
          if (select.hasClass('js-agenda-status')) return;
          var searchUrl = select.attr('data-search-url');
          select.removeAttr('data-ajax--url data-minimum-input-length');
          select.removeData('ajax--url').removeData('search-url').removeData('minimum-input-length');
          var options = {
            theme: 'default',
            width: '100%',
            dropdownParent: window.jQuery(modal),
            minimumResultsForSearch: 0
          };
          if (searchUrl) {
            options.minimumInputLength = 3;
            options.placeholder = select.find('option[value=""]').text();
            options.allowClear = true;
            options.language = {
              inputTooShort: function() { return 'Escribe al menos 3 caracteres para buscar.'; },
              searching: function() { return 'Buscando…'; },
              noResults: function() { return 'No se encontraron coincidencias.'; },
              errorLoading: function() { return 'No se pudo realizar la búsqueda. Intenta nuevamente.'; }
            };
            options.ajax = {
              url: searchUrl, dataType: 'json', delay: 250,
              data: function(params) { return { q: params.term }; },
              processResults: function(data) { return { results: data.results || [] }; }
            };
          }
          select.select2(options);
          select.off('select2:open.agendaFocus').on('select2:open.agendaFocus', function() {
            window.setTimeout(function() {
              var searchField = modal.querySelector('.select2-container--open .select2-search__field');
              if (searchField) searchField.focus();
            }, 0);
          });
        });
      }

      function show(elements, visible) {
        elements.forEach(function(el) {
          el.classList.toggle('is-agenda-hidden', !visible);
        });
      }

      function sync() {
        var value = parseInt(type.value, 10);
        show(root.querySelectorAll('.da-patient-fields'), value !== 2);
        show(root.querySelectorAll('.da-procedure-fields'), value === 1);
        show(root.querySelectorAll('.da-meeting-fields'), value === 2);
        var procedure = root.querySelector('[name="id_procedimiento"]');
        if (procedure) procedure.required = value === 1;
        if (value !== 1) {
          if (procedure) procedure.value = '';
          ['id_doctor2', 'id_doctor3'].forEach(function(name) {
            var assistant = root.querySelector('.da-procedure-fields [name="' + name + '"]');
            if (assistant) assistant.value = '';
          });
        }
      }
      function loadAvailability() {
        if (!form || !availabilityBox || !startInput || !startInput.value) return;
        var date = startInput.value.substring(0, 10), url = form.getAttribute('data-availability-url'), exclude = form.getAttribute('data-appointment-id');
        if (!url || !/^\d{4}-\d{2}-\d{2}$/.test(date)) return;
        availabilityBox.innerHTML = '<span class="da-availability__hint"><i class="fas fa-spinner fa-spin mr-1"></i> Consultando horarios libres…</span>';
        var query = '?fecha=' + encodeURIComponent(date) + '&tipo=' + encodeURIComponent(type.value);
        if (exclude) query += '&excluir=' + encodeURIComponent(exclude);
        fetch(url + query, { credentials: 'same-origin', headers: headers(true) }).then(function(response) {
          return response.json().then(function(data) { if (!response.ok) throw new Error(errorText(data, 'No se pudo consultar la disponibilidad.')); return data; });
        }).then(function(data) {
          var items = data.items || [];
          if (!items.length) { availabilityBox.innerHTML = '<span class="da-availability__hint da-availability__hint--empty"><i class="fas fa-calendar-times mr-1"></i> No existen intervalos libres para esta fecha y tipo de agenda.</span>'; return; }
          availabilityBox.innerHTML = items.map(function(item) { return '<button type="button" class="da-availability__slot" data-start="' + escapeHtml(item.start) + '" data-end="' + escapeHtml(item.end) + '"><i class="far fa-clock mr-1"></i>' + escapeHtml(item.label) + '<small>Turno de ' + item.minutes + ' minutos</small></button>'; }).join('');
        }).catch(function(error) { availabilityBox.innerHTML = '<span class="da-availability__hint da-availability__hint--empty"><i class="fas fa-exclamation-circle mr-1"></i>' + escapeHtml(error.message) + '</span>'; });
      }
      function scheduleAvailability() { window.clearTimeout(availabilityTimer); availabilityTimer = window.setTimeout(loadAvailability, 180); }
      function syncConfirmedReagenda() {
        if (!form || form.getAttribute('data-confirmed') !== '1' || !status) return;
        var enabled = String(status.value) === '2';
        root.querySelectorAll('.js-reagendar-field').forEach(function(field) {
          if (!enabled && field.hasAttribute('data-confirmed-value')) field.value = field.getAttribute('data-confirmed-value');
          if (field.tagName === 'SELECT') {
            field.disabled = !enabled;
            if (window.jQuery && window.jQuery(field).hasClass('select2-hidden-accessible')) window.jQuery(field).trigger('change.select2');
          } else field.readOnly = !enabled;
        });
        root.querySelectorAll('.js-confirmed-lock-value').forEach(function(hidden) {
          var name = hidden.getAttribute('data-confirmed-field');
          if (['id_sala', 'id_doctor2', 'id_doctor3'].indexOf(name) !== -1) hidden.disabled = enabled && !!root.querySelector('.js-reagendar-field[name="' + name + '"]');
        });
        root.querySelectorAll('.js-reagendar-availability').forEach(function(box) { box.classList.toggle('is-agenda-hidden', !enabled); });
        if (enabled) loadAvailability();
      }
      function syncStatusPresentation() {
        if (!status) return;
        var card = root.querySelector('.js-agenda-status-card'), icon = root.querySelector('.js-agenda-status-icon'), help = root.querySelector('.js-agenda-status-help');
        if (!card) return;
        var states = {
          '0': { icon: 'fa-clock', help: 'La cita está pendiente de confirmación.' },
          '1': { icon: 'fa-check-circle', help: 'La cita quedará confirmada para la fecha programada.' },
          '2': { icon: 'fa-calendar-alt', help: 'Habilita el horario y los recursos para reprogramar la cita.' },
          '3': { icon: 'fa-ban', help: 'Suspende la cita y la retira de la agenda activa.' },
          '4': { icon: 'fa-hospital-user', help: 'Registra la admisión del paciente para iniciar la atención.' },
          '-1': { icon: 'fa-user-times', help: 'Marca que el paciente no asistió a la cita.' }
        }, value = String(status.value), state = states[value] || states['0'];
        card.setAttribute('data-status', value);
        var field = card.closest('.da-status-field');
        if (field) field.setAttribute('data-status', value);
        if (icon) icon.className = 'fas ' + state.icon + ' js-agenda-status-icon';
        if (help) help.textContent = state.help;
      }
      type.addEventListener('change', sync);
      if (window.jQuery) {
        window.jQuery(type).off('change.agendaActivityFields').on('change.agendaActivityFields', function() { sync(); scheduleAvailability(); });
      }
      type.addEventListener('change', scheduleAvailability);
      if (startInput) startInput.addEventListener('change', scheduleAvailability);
      if (refreshAvailability) refreshAvailability.addEventListener('click', loadAvailability);
      if (availabilityBox) availabilityBox.addEventListener('click', function(event) { var slot = event.target.closest('.da-availability__slot'); if (!slot) return; startInput.value = slot.getAttribute('data-start'); endInput.value = slot.getAttribute('data-end'); availabilityBox.querySelectorAll('.da-availability__slot').forEach(function(item) { item.classList.toggle('is-selected', item === slot); }); });
      function syncPatientDefaults(data) {
        var option = patient.options[patient.selectedIndex],
          appointmentType = data && data.appointment_type != null ? data.appointment_type : (option ? option.getAttribute('data-appointment-type') : null);
        if (appointmentTypeValue && appointmentTypeDisplay) {
          if (!patient.value) appointmentType = 0;
          if (appointmentType != null) {
            appointmentType = String(parseInt(appointmentType, 10) === 1 ? 1 : 0);
            appointmentTypeDisplay.value = appointmentType;
            appointmentTypeValue.value = appointmentType;
            if (window.jQuery && window.jQuery(appointmentTypeDisplay).hasClass('select2-hidden-accessible')) window.jQuery(appointmentTypeDisplay).trigger('change.select2');
            if (appointmentTypeHelp) appointmentTypeHelp.textContent = appointmentType === '1' ? 'El paciente ya tiene agendas anteriores: consecutiva.' : 'El paciente no tiene agendas anteriores: primera vez.';
          }
        }
      }
      if (patient) {
        patient.addEventListener('change', function() { syncPatientDefaults(); });
        if (window.jQuery) window.jQuery(patient).on('select2:select.agendaInsurance', function(event) {
          syncPatientDefaults(event.params.data);
        });
        syncPatientDefaults();
      }
      if (patientCreate && form) {
        patientCreate.addEventListener('click', function() {
          var url = new URL(patientCreate.href, window.location.href);
          if (startInput) url.searchParams.set('agenda_inicio', startInput.value);
          if (endInput) url.searchParams.set('agenda_fin', endInput.value);
          url.searchParams.set('agenda_tipo', type.value);
          patientCreate.href = url.toString();
        });
      }
      if (status && form && form.getAttribute('data-confirmed') === '1') {
        status.addEventListener('change', syncConfirmedReagenda);
        if (window.jQuery) window.jQuery(status).off('change.confirmedReagenda select2:select.confirmedReagenda').on('change.confirmedReagenda select2:select.confirmedReagenda', syncConfirmedReagenda);
      }
      if (status) status.addEventListener('change', syncStatusPresentation);
      sync();
      syncStatusPresentation();
      syncConfirmedReagenda();
      if (!form || form.getAttribute('data-confirmed') !== '1' || (status && String(status.value) === '2')) loadAvailability();
      syncPaymentFiles(root);
    }

    function syncPaymentFiles(root) {
      var panel = root.querySelector('.js-payment-files');
      if (!panel) return;
      var list = panel.querySelector('.js-payment-files-list'),
        input = panel.querySelector('.js-payment-file-input'),
        fileName = panel.querySelector('.js-payment-file-name'),
        uploadStatus = panel.querySelector('.js-payment-file-status'),
        form = root.querySelector('.js-activity-form'),
        listUrl = panel.getAttribute('data-list-url'),
        uploadUrl = panel.getAttribute('data-upload-url'),
        uploading = false;

      function setUploadState(state, message) {
        panel.classList.remove('is-uploading', 'is-upload-success', 'is-upload-error', 'has-selected-file');
        if (state) panel.classList.add(state);
        if (uploadStatus) uploadStatus.innerHTML = message || 'La carga iniciará automáticamente.';
      }

      function resetPicker() {
        input.value = '';
        input.disabled = false;
        uploading = false;
      }

      function uploadSelectedFile() {
        if (uploading || !input.files || !input.files.length) return;
        var file = input.files[0], extension = (file.name.split('.').pop() || '').toLowerCase();
        fileName.textContent = file.name;
        panel.classList.add('has-selected-file');
        if (['pdf', 'jpg', 'jpeg', 'png'].indexOf(extension) === -1) {
          setUploadState('is-upload-error', '<i class="fas fa-exclamation-circle mr-1"></i> Formato no permitido. Seleccione un PDF, JPG o PNG.');
          resetPicker();
          return;
        }
        if (file.size > 10 * 1024 * 1024) {
          setUploadState('is-upload-error', '<i class="fas fa-exclamation-circle mr-1"></i> El archivo supera el tamaño máximo de 10 MB.');
          resetPicker();
          return;
        }
        var data = new FormData(), token = form ? form.querySelector('[name="_token"]') : null;
        data.append('archivo', file);
        if (token) data.append('_token', token.value);
        uploading = true;
        input.disabled = true;
        setUploadState('is-uploading', '<i class="fas fa-spinner fa-spin mr-1"></i> Subiendo archivo…');
        fetch(uploadUrl, { method:'POST', credentials:'same-origin', headers:headers(true), body:data })
          .then(function(response) { return response.json().then(function(json) { if (!response.ok) throw new Error(errorText(json, 'No se pudo cargar el archivo.')); return json; }); })
          .then(function(data) {
            resetPicker();
            fileName.textContent = 'PDF, JPG o PNG · máximo 10 MB';
            setUploadState('is-upload-success', '<i class="fas fa-check-circle mr-1"></i> ' + escapeHtml(data.message || 'Archivo cargado correctamente.'));
            load();
          })
          .catch(function(error) {
            resetPicker();
            setUploadState('is-upload-error', '<i class="fas fa-exclamation-circle mr-1"></i> ' + escapeHtml(error.message || 'No se pudo cargar el archivo.'));
          });
      }

      input.addEventListener('change', uploadSelectedFile);

      function fileRow(item) {
        var label = item.description || item.name || 'Archivo de agenda';
        return '<article class="da-payment-file" data-file-id="' + escapeHtml(item.id) + '"><div class="da-payment-file__icon"><i class="fas fa-file-alt"></i></div><div class="da-payment-file__meta"><strong>' + escapeHtml(label) + '</strong><small>' + escapeHtml(item.date || '') + (item.uploader ? ' · ' + escapeHtml(item.uploader) : '') + '</small></div><div class="da-payment-file__actions"><a class="btn btn-sm btn-outline-primary js-payment-file-view" href="' + escapeHtml(item.view_url) + '" data-file-title="' + escapeHtml(label) + '"><i class="fas fa-eye"></i><span> Ver</span></a><button class="btn btn-sm btn-outline-danger js-payment-file-delete" type="button" data-delete-url="' + escapeHtml(item.delete_url) + '"><i class="fas fa-trash-alt"></i><span> Eliminar</span></button></div></article>';
      }

      function render(items) {
        if (!items || !items.length) {
          list.innerHTML = '<p class="da-payment-files__empty"><i class="far fa-folder-open mr-1"></i> Aún no se han cargado comprobantes o archivos para esta agenda.</p>';
          return;
        }
        list.innerHTML = items.map(fileRow).join('');
      }

      function load() {
        list.innerHTML = '<p class="da-payment-files__empty"><i class="fas fa-spinner fa-spin mr-1"></i> Cargando archivos…</p>';
        fetch(listUrl, { credentials: 'same-origin', headers: headers(true) })
          .then(function(response) { return response.json().then(function(data) { if (!response.ok) throw new Error(errorText(data, 'No se pudieron cargar los archivos.')); return data; }); })
          .then(function(data) { render(data.items || []); })
          .catch(function(error) { list.innerHTML = '<p class="da-payment-files__empty is-error"><i class="fas fa-exclamation-circle mr-1"></i>' + escapeHtml(error.message) + '</p>'; });
      }

      list.addEventListener('click', function(event) {
        var view = event.target.closest('.js-payment-file-view');
        if (view) {
          event.preventDefault();
          openPaymentFileModal(view.href, view.getAttribute('data-file-title'));
          return;
        }
        var button = event.target.closest('.js-payment-file-delete');
        if (!button) return;
        event.preventDefault();
        agendaAlert({ icon:'warning', title:'¿Eliminar este archivo?', text:'Esta acción quitará el comprobante de la agenda.', showCancelButton:true, confirmButtonText:'Sí, eliminar', cancelButtonText:'Cancelar', confirmButtonColor:'#d33', cancelButtonColor:'#6c8797', reverseButtons:true })
          .then(function(result) {
            if (!result || !result.isConfirmed) return null;
            button.disabled = true;
            var data = new FormData(), token = form ? form.querySelector('[name="_token"]') : null;
            data.append('_method', 'DELETE');
            if (token) data.append('_token', token.value);
            return fetch(button.getAttribute('data-delete-url'), { method:'POST', credentials:'same-origin', headers:headers(true), body:data })
              .then(function(response) { return response.json().then(function(json) { if (!response.ok) throw new Error(errorText(json, 'No se pudo eliminar el archivo.')); return json; }); })
              .then(function(data) { load(); return agendaAlert({ icon:'success', title:'Archivo eliminado', text:data.message || 'El archivo se eliminó correctamente.', confirmButtonText:'Aceptar', confirmButtonColor:'#15966b' }); })
              .catch(function(error) { button.disabled = false; return agendaAlert({ icon:'error', title:'No se pudo eliminar el archivo', text:error.message || 'Inténtelo nuevamente.', confirmButtonText:'Entendido', confirmButtonColor:'#087fb5' }); });
          });
      });
      load();
    }

    function openLabelModal(url) {
      labelUrl = url;
      labelPreview.innerHTML = '<div class="da-modal-loading"><span class="da-spinner"></span><strong>Cargando etiqueta…</strong></div>';
      if (window.jQuery) window.jQuery(labelModal).modal('show');
      var previewUrl = new URL(url, window.location.href);
      previewUrl.searchParams.set('preview', '1');
      fetch(previewUrl.toString(), { credentials: 'same-origin', headers: headers(false) })
        .then(function(response) {
          if (!response.ok) throw new Error('No se pudo cargar la etiqueta del paciente.');
          return response.text();
        }).then(function(html) {
          labelPreview.innerHTML = html;
        }).catch(function(error) {
          labelPreview.innerHTML = '<div class="alert alert-danger mb-0">' + escapeHtml(error.message) + '</div>';
        });
    }

    function printLabelDocument() {
      if (!labelUrl) return;
      var button = labelModal.querySelector('.js-print-label'), frame = document.createElement('iframe');
      button.disabled = true;
      frame.setAttribute('aria-hidden', 'true');
      frame.style.position = 'fixed'; frame.style.right = '0'; frame.style.bottom = '0';
      frame.style.width = '1px'; frame.style.height = '1px'; frame.style.border = '0';
      frame.onload = function() {
        window.setTimeout(function() {
          try { frame.contentWindow.focus(); frame.contentWindow.print(); }
          finally { button.disabled = false; window.setTimeout(function() { frame.remove(); }, 1000); }
        }, 150);
      };
      frame.src = labelUrl;
      document.body.appendChild(frame);
    }

    function openReceiptModal(url, refreshOnly) {
      if (!receiptModal || !receiptContent) return;
      receiptListUrl = url || receiptListUrl;
      if (!receiptListUrl) return;
      if (modal && receiptModal.parentNode !== modal) modal.appendChild(receiptModal);
      receiptContent.innerHTML = '<div class="da-modal-loading"><span class="da-spinner"></span><strong>Cargando recibos…</strong></div>';
      if (!refreshOnly && window.jQuery) window.jQuery(receiptModal).modal('show');
      fetch(receiptListUrl, { credentials:'same-origin', headers:headers(false) })
        .then(function(response) {
          if (!response.ok || response.redirected) throw new Error('No se pudo cargar el listado de recibos.');
          return response.text();
        }).then(function(html) {
          receiptContent.innerHTML = html;
        }).catch(function(error) {
          receiptContent.innerHTML = '<div class="modal-header da-modal-head"><div><small>AGENDA MÉDICA · V3</small><h5 class="modal-title"><i class="fas fa-receipt mr-2"></i>Recibos de cobro</h5></div><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div><div class="modal-body"><div class="alert alert-danger mb-0">' + escapeHtml(error.message) + '</div></div>';
        });
    }

    function voidReceiptFromModal(button) {
      agendaAlert({
        icon: 'warning',
        title: '¿Anular este recibo?',
        text: 'El recibo pendiente se retirará del listado y se conservará su registro de auditoría.',
        showCancelButton: true,
        confirmButtonText: 'Sí, anular',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#c8444b',
        target: receiptModal
      }).then(function(result) {
        if (!result.isConfirmed) return;
        var tokenMeta = document.querySelector('meta[name="csrf-token"]'),
          tokenInput = document.querySelector('input[name="_token"]'),
          token = tokenMeta ? tokenMeta.getAttribute('content') : (tokenInput ? tokenInput.value : ''),
          data = new FormData();
        data.append('id_orden', button.getAttribute('data-id'));
        if (token) data.append('_token', token);
        button.disabled = true;
        fetch(button.getAttribute('data-url'), {
          method: 'POST', credentials: 'same-origin',
          headers: { 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-TOKEN':token, Accept:'application/json' },
          body: data
        }).then(function(response) {
          if (!response.ok) throw new Error('No se pudo anular el recibo.');
          openReceiptModal(receiptListUrl, true);
        }).catch(function(error) {
          var errorBox = receiptContent.querySelector('.rc-modal-error');
          if (errorBox) { errorBox.textContent = error.message; errorBox.hidden = false; }
          button.disabled = false;
        });
      });
    }

    function openIdDocumentModal(trigger) {
      if (trigger.getAttribute('data-available') !== '1') {
        agendaAlert({ icon:'info', title:'Documento no cargado', text:'El paciente aún no tiene registrada una copia de su cédula o documento de identificación.', confirmButtonText:'Entendido', confirmButtonColor:'#087fb5' });
        return;
      }
      if (idDocumentObjectUrl) { URL.revokeObjectURL(idDocumentObjectUrl); idDocumentObjectUrl = ''; }
      idDocumentFrame.style.display = 'none'; idDocumentFrame.src = 'about:blank'; idDocumentLoading.style.display = 'flex';
      if (window.jQuery) window.jQuery(idDocumentModal).modal('show');
      fetch(trigger.href, { credentials:'same-origin', headers:headers(false) })
        .then(function(response) { if (!response.ok) throw new Error('El documento ya no está disponible.'); return response.blob(); })
        .then(function(blob) {
          idDocumentObjectUrl = URL.createObjectURL(blob);
          idDocumentFrame.onload = function() { idDocumentLoading.style.display = 'none'; idDocumentFrame.style.display = 'block'; };
          idDocumentFrame.src = idDocumentObjectUrl;
        }).catch(function(error) {
          idDocumentLoading.innerHTML = '<div class="alert alert-warning mb-0"><i class="fas fa-exclamation-triangle mr-1"></i>' + escapeHtml(error.message) + '</div>';
        });
    }

    function openPaymentFileModal(url, title) {
      if (!paymentFileModal || !paymentFileFrame || !paymentFileLoading) return;
      if (paymentFileObjectUrl) { URL.revokeObjectURL(paymentFileObjectUrl); paymentFileObjectUrl = ''; }
      if (paymentFileTitle) paymentFileTitle.textContent = title || 'Vista previa del comprobante';
      paymentFileFrame.style.display = 'none';
      paymentFileFrame.src = 'about:blank';
      paymentFileLoading.innerHTML = '<span class="da-spinner"></span><strong>Cargando comprobante…</strong>';
      paymentFileLoading.style.display = 'flex';
      // Mantener la vista previa dentro del modal de agenda evita que el
      // control de foco de Bootstrap bloquee el iframe anidado.
      if (modal && paymentFileModal.parentNode !== modal) modal.appendChild(paymentFileModal);
      if (window.jQuery) window.jQuery(paymentFileModal).modal('show');
      fetch(url, { credentials:'same-origin', headers:headers(false) })
        .then(function(response) { if (!response.ok) throw new Error('El comprobante ya no está disponible.'); return response.blob(); })
        .then(function(blob) {
          paymentFileObjectUrl = URL.createObjectURL(blob);
          paymentFileFrame.onload = function() {
            paymentFileLoading.style.display = 'none';
            paymentFileFrame.style.display = 'block';
          };
          paymentFileFrame.src = paymentFileObjectUrl;
        }).catch(function(error) {
          paymentFileLoading.innerHTML = '<div class="alert alert-warning mb-0"><i class="fas fa-exclamation-triangle mr-1"></i>' + escapeHtml(error.message) + '</div>';
        });
    }
    if (window.jQuery && paymentFileModal) {
      window.jQuery(paymentFileModal).on('click.paymentPreview', '[data-dismiss="modal"]', function(event) {
        // El preview está dentro del modal de agenda para conservar el foco.
        // Detenemos el evento aquí para que Bootstrap no cierre también el padre.
        event.preventDefault();
        event.stopImmediatePropagation();
        window.jQuery(paymentFileModal).modal('hide');
      }).on('hidden.bs.modal', function() {
        paymentFileFrame.src = 'about:blank';
        if (paymentFileObjectUrl) { URL.revokeObjectURL(paymentFileObjectUrl); paymentFileObjectUrl = ''; }
      });
    }
    if (window.jQuery && receiptModal) {
      window.jQuery(receiptModal).on('click.receiptModal', '[data-dismiss="modal"]', function(event) {
        event.preventDefault();
        event.stopImmediatePropagation();
        window.jQuery(receiptModal).modal('hide');
      }).on('keydown.receiptModal', function(event) {
        if (event.which !== 27) return;
        event.preventDefault();
        event.stopImmediatePropagation();
        window.jQuery(receiptModal).modal('hide');
      }).on('hidden.bs.modal', function() {
        receiptContent.innerHTML = '<div class="da-modal-loading"><span class="da-spinner"></span><strong>Cargando recibos…</strong></div>';
        if (modal && window.jQuery(modal).hasClass('show')) document.body.classList.add('modal-open');
      });
    }
    content.addEventListener('submit', function(event) {
      var form = event.target.closest('.js-activity-form');
      if (!form) return;
      event.preventDefault();
      submitActivity(form);
    });

    function submitActivity(form) {
      var submit = form.querySelector('.js-save-activity'),
        error = form.closest('.modal-body').querySelector('.da-modal-error'),
        formData = new FormData(form),
        status = form.querySelector('.js-agenda-status');
      if (form.getAttribute('data-confirmed') === '1' && status && String(status.value) === '2') formData.set('reagendar_confirmado', '1');
      var localErrors = clientValidation(form);
      if (localErrors.messages.length) {
        showAgendaErrors(localErrors.messages, form, localErrors.firstField);
        return;
      }
      submit.disabled = true;
      error.hidden = true;
      fetch(form.action, {
        method: 'POST',
        credentials: 'same-origin',
        headers: headers(true),
        body: formData
      }).then(function(response) {
        return response.text().then(function(body) {
          var data = {};
          try { data = body ? JSON.parse(body) : {}; } catch (parseError) {}
          if (!response.ok) throw { responseStatus: response.status, data: data };
          return data;
        });
      }).then(function(data) {
        return agendaAlert({
          icon: 'success',
          title: 'Agenda guardada',
          text: data.message || 'La agenda se guardó correctamente.',
          confirmButtonText: 'Aceptar',
          confirmButtonColor: '#15966b'
        }).then(function() {
          if (window.jQuery) window.jQuery(modal).modal('hide');
          return refreshCurrent();
        });
      }).catch(function(reason) {
        var status = reason && reason.responseStatus,
          data = reason && reason.data,
          fallback = status === 419
            ? 'La sesión venció. Actualiza la página e inicia sesión nuevamente.'
            : (status === 403
              ? 'No tienes permisos para guardar esta agenda.'
              : (status >= 500 ? 'El servidor encontró un problema al guardar. Inténtalo nuevamente o comunícate con Sistemas.' : 'No fue posible conectar con el servidor. Verifica tu conexión e inténtalo nuevamente.')),
          messages = validationMessages(data, fallback),
          firstField = data && data.errors ? Object.keys(data.errors)[0] : null;
        showAgendaErrors(messages, form, firstField, firstField === 'agenda' || !status ? 'No se pudo guardar la agenda' : 'Faltan datos para guardar');
      }).then(function() {
        submit.disabled = false;
      });
    }

    function cancelActivity(button, cancellationReason) {
      button.disabled = true;
      var form = button.closest('form'),
        token = form.querySelector('[name="_token"]'),
        data = new FormData();
      data.append('_method', 'DELETE');
      if (token) data.append('_token', token.value);
      data.append('observacion_cancelacion', cancellationReason || '');
      fetch(button.getAttribute('data-cancel-url'), {
        method: 'POST',
        credentials: 'same-origin',
        headers: headers(true),
        body: data
      }).then(function(response) {
        return response.json().then(function(json) {
          if (!response.ok) throw new Error(errorText(json, 'No se pudo cancelar.'));
          return json;
        });
      }).then(function(data) {
        return agendaAlert({
          icon: 'success',
          title: 'Agenda cancelada',
          text: data.message || 'La agenda se canceló correctamente.',
          confirmButtonText: 'Aceptar',
          confirmButtonColor: '#087fb5'
        }).then(function() {
          if (window.jQuery) window.jQuery(modal).modal('hide');
          return refreshCurrent();
        });
      }).catch(function(error) {
        return agendaAlert({
          icon: 'error',
          title: 'No se puede cancelar la agenda',
          text: error.message || 'Esta agenda no permite ser cancelada.',
          confirmButtonText: 'Entendido',
          confirmButtonColor: '#087fb5'
        }).then(function() {
          button.disabled = false;
        });
      });
    }

    function refreshCurrent() {
      var params = new URLSearchParams(window.location.search);
      return refresh(board.getAttribute('data-week-endpoint') + (params.toString() ? '?' + params.toString() : ''), null, false);
    }

    function escapeHtml(text) {
      var div = document.createElement('div');
      div.textContent = text;
      return div.innerHTML;
    }
    window.addEventListener('popstate', refreshCurrent);
    if (window.jQuery) window.jQuery(modal).on('hidden.bs.modal', function(event) {
      // La vista previa del comprobante es una modal hija. Su evento hidden
      // también llega hasta aquí; no se debe vaciar el editor de la agenda.
      if (event.target !== modal) return;
      content.innerHTML = '';
    });
    if (window.jQuery) window.jQuery(idDocumentModal).on('hidden.bs.modal', function() {
      idDocumentFrame.onload = null;
      idDocumentFrame.src = 'about:blank';
      idDocumentFrame.style.display = 'none';
      idDocumentLoading.style.display = 'flex';
      idDocumentLoading.innerHTML = '<span class="da-spinner"></span><strong>Cargando documento…</strong>';
      if (idDocumentObjectUrl) { URL.revokeObjectURL(idDocumentObjectUrl); idDocumentObjectUrl = ''; }
      if (window.jQuery(modal).hasClass('show')) document.body.classList.add('modal-open');
    });
    if (window.jQuery) window.jQuery(labelModal).on('hidden.bs.modal', function() {
      if (window.jQuery(modal).hasClass('show')) document.body.classList.add('modal-open');
    });
  })();
</script>
