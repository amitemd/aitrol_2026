@include('v3.clinical.operations._styles')
@include('v3.clinical.agenda._receipt_styles')
@php($taxReady = !isset($taxConfigured) || $taxConfigured)
@php($receiptEditable = (int) $orden->estado === -1)
@php($receiptInvoiced = !is_null($invoice ?? null))
<div class="co-page receipt-page re-reference">
  <header class="re-hero">
    <div><a class="re-back" href="{{$receiptBackUrl}}"><i class="fas fa-arrow-left"></i> Volver a la agenda</a><div class="re-title"><span class="re-icon"><i class="fas fa-receipt"></i></span><div><small>AGENDA MÉDICA</small><h1>Recibo de cobro <span>#{{$orden->id}}</span></h1></div></div></div>
    <div class="re-hero-actions">
      @if((int)$orden->estado===-1)
      <button type="button" class="btn re-hero-action re-hero-action--primary" onclick="emitir()" @if(!$taxReady) disabled title="Configure el IVA antes de emitir" @endif><i class="fas fa-check-circle"></i><span>Emitir recibo</span></button>
      @endif
      @if((int)$orden->estado===1)
      <a class="btn re-hero-action re-hero-action--primary" href="{{route('facturacion.imprimir_ride',array('id_orden'=>$orden->id))}}" target="_blank" rel="noopener"><i class="fas fa-print"></i><span>Imprimir</span></a>
      @if(!$receiptInvoiced)<a class="btn re-invoice" href="{{route('v3.agenda-receipts.invoice-preview',array('receipt'=>$orden->id))}}"><i class="fas fa-file-invoice-dollar"></i><span>Facturar recibo</span></a><button type="button" class="btn re-return" onclick="devolver()"><i class="fas fa-undo-alt"></i> Devolver</button>@else<a class="re-status is-issued" href="{{route('v3.agenda-receipts.invoice-preview',array('receipt'=>$orden->id))}}"><i class="fas fa-file-invoice"></i> Facturado · {{$invoice->nro_comprobante ?: '#'.$invoice->id}}</a>@endif
      @endif
      <span class="re-status re-hero-state {{(int)$orden->estado===1?'is-issued':''}}"><i class="fas {{(int)$orden->estado===1?'fa-check-circle':'fa-clock'}}"></i><span>{{(int)$orden->estado===1?'Emitido':'Pendiente de emitir'}}</span></span>
      <button type="button" class="btn re-hero-action re-hero-action--secondary" data-toggle="modal" data-target="#log_rc"><i class="fas fa-history"></i><span>Historial</span></button>
    </div>
  </header>
  @if(!$taxReady)
  <div class="re-tax-alert" role="alert">
    <span><i class="fas fa-exclamation-triangle"></i></span>
    <div><strong>El recibo está abierto, pero falta configurar el IVA.</strong><p>Puede revisar sus datos generales. Configure el porcentaje tributario para administrar productos y emitir el recibo.</p></div>
    <a class="btn btn-light" href="{{route('v3.global-settings.index')}}"><i class="fas fa-cog"></i> Configurar IVA</a>
  </div>
  @endif
  @if(!$receiptEditable)
  <div class="alert {{$receiptInvoiced?'alert-success':'alert-info'}} re-readonly-alert" role="status"><i class="fas fa-lock"></i><strong> {{$receiptInvoiced?'Recibo facturado.':'Recibo emitido.'}}</strong> {{$receiptInvoiced?'Está vinculado a la factura '.($invoice->nro_comprobante ?: '#'.$invoice->id).' y permanece bloqueado.':'Para modificar datos, productos o pagos, primero debe devolver el recibo.'}}</div>
  @endif
  <div class="re-summary"><div class="re-summary-item"><span class="re-summary-icon"><i class="fas fa-user-injured"></i></span><div><small>Paciente</small><strong>{{trim($paciente->apellido1.' '.$paciente->apellido2.' '.$paciente->nombre1.' '.$paciente->nombre2)}}</strong><span>Cédula: {{$paciente->id}}</span></div></div><div class="re-summary-item re-establishment">@if($empresa->logo)<img class="re-company-logo" src="{{asset('/logo').'/'.$empresa->logo}}" alt="{{$empresa->nombrecomercial}}">@endif<span class="re-summary-icon"><i class="fas fa-clinic-medical"></i></span><div><small>Establecimiento</small><strong>{{$empresa->nombrecomercial}}</strong><span>{{$empresa->direccion}}</span></div></div></div>
  <div class="rc-editor">
    @include('contable.nuevo_recibo._editor_modals')
    <div id="alerta_guardado" class="alert alert-success" role="status" style="display:none">Guardado correctamente</div><a id="ride" target="_blank" hidden></a>
    <form id="form_actualizar_cabecera" method="POST" autocomplete="off">
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_v3" value="1">
                <input type="hidden" name="id_orden" id="id_orden" value="{{$orden->id}}">
                <input type="hidden" name="empresa" id="id_empresa" value="{{$empresa->id}}">
                <input type="hidden" name="id_agenda" id="id_agenda" value="{{$agenda->id}}">
                <input type="hidden" name="id_doctor" id="id_doctor" value="{{$agenda->id_doctor1}}">
      <fieldset class="re-editor-fields" @if(!$receiptEditable) disabled @endif>
      <section class="re-section" id="rc-datos"><header><span class="re-step">01</span><div><h2>Datos del recibo</h2><p>Fecha y observación principal del cobro.</p></div></header><div class="re-grid"><div class="re-field "><label for="rc-f_procedimiento">Fecha de atención</label><input type="date" id="rc-f_procedimiento" name="f_procedimiento" value="{{date('Y-m-d',strtotime($agenda->fechaini))}}" readonly class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm"></div>
<div class="re-field "><label for="rc-f_emision">Fecha de emisión</label><input type="datetime-local" id="rc-f_emision" name="f_emision" value="{{date('Y-m-d',strtotime($orden->nueva_fecha))}}T{{date('H:i',strtotime($orden->nueva_fecha))}}" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" onchange="guardar_auto()" min="{{date('Y-m-d')}}T00:00"></div>
<div class="re-field"><label for="observacion">Observaciones del recibo</label><textarea class="form-control" name="observacion" id="observacion" cols="3" rows="2" placeholder="Añade una nota breve para este recibo" onchange="guardar_auto()">{{ $orden->observacion}}</textarea></div></div></section>
      <section class="re-section" id="rc-cobertura"><header><span class="re-step">02</span><div><h2>Cobertura y caja</h2><p>El seguro corresponde a la agenda; selecciona la caja que recibe el pago.</p></div></header><div class="re-grid"><div class="re-field "><label for="id_seguro">Seguro / convenio de la agenda</label><select class="form-control input-sm" name="id_seguro" id="id_seguro" required aria-readonly="true">
                                                @foreach($seguros as $seguro)
                                                <option selected value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                                                @endforeach
                                            </select></div>
<div class="re-field " id="ident_nivel"><div class="re-label-row"><label for="id_nivel">Nivel</label>@if(Auth::user()->isSystemUser())<span class="re-field-tools"><a href="{{route('v3.insurance-levels.index')}}" target="_blank" rel="noopener" title="Gestionar niveles de seguro"><i class="fas fa-layer-group"></i> Niveles</a> · <a href="{{route('v3.product-tariffs.index')}}" target="_blank" rel="noopener" title="Gestionar precios por seguro"><i class="fas fa-tags"></i> Tarifarios</a></span>@endif</div><select name="id_nivel" id="id_nivel" class="form-control input-sm" onchange="guardar_auto()">
                                                <option value="" @if(!$orden->id_nivel) selected @endif>Seleccione...</option>
                                                @foreach($receiptLevels as $level)<option value="{{$level->id_nivel}}" @if((string)$orden->id_nivel===(string)$level->id_nivel) selected @endif>{{$level->nombre}}</option>@endforeach
                                            </select></div>
<div class="re-field "><label for="oda">Número de ODA</label><input type="text" name="oda" class="form-control input-sm" placeholder="# ODA" id="oda" value="{{ $orden->numero_oda }}" onchange="guardar_auto()"></div>
<div class="re-field re-cash-field"><div class="re-label-row"><label for="caja">Caja de cobro</label>@if((int)Auth::user()->id_tipo_usuario===1)<span class="re-field-tools"><a href="{{route('v3.cash-registers.index')}}" target="_blank" rel="noopener" title="Administrar cajas"><i class="fas fa-cog"></i> Administrar</a><button type="button" class="btn btn-link" id="rc-refresh-cajas" title="Actualizar lista"><i class="fas fa-sync-alt"></i></button></span>@endif</div><select id="caja" name="caja" class="form-control" required onchange="guardar_auto()">
                                                <option value="">Seleccione...</option>
                                                @foreach($cajas as $caja)
                                                <option @if($orden->caja == $caja->id) selected @endif value="{{$caja->id}}">{{$caja->descripcion}}</option>
                                                @endforeach
                                            </select></div></div></section>
      <section class="re-section" id="rc-cliente"><header><span class="re-step">03</span><div><h2>Datos del cliente</h2><p>Información de la persona o empresa a la que se emite el recibo.</p></div><button class="btn btn-outline-primary" type="button" onclick="actualizar_cliente()"><i class="fas fa-user-edit"></i> Crear / actualizar cliente</button></header><div class="re-grid"><div class="re-field "><label for="tipo_identificacion">Tipo de identificación</label><select id="tipo_identificacion" name="tipo_identificacion" class="form-control input-sm" required >
                                                <option @if($orden->tipo_identificacion=='4') selected='selected' @endif value="4">RUC</option>
                                                <option @if($orden->tipo_identificacion=='5') selected='selected' @endif value="5">CEDULA</option>
                                                <option @if($orden->tipo_identificacion=='6') selected='selected' @endif value="6">PASAPORTE</option>
                                                <option @if($orden->tipo_identificacion=='8') selected='selected' @endif value="8">CEDULA EXTRANJERA</option>
                                            </select></div>
<div class="re-field "><label for="cedula">RUC / cédula</label><input id="cedula" maxlength="13" type="text" class="form-control" name="cedula" value="{{$orden->identificacion}}" placeholder="Ruc/Cédula" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onchange="buscar();" required></div>
<div class="re-field re-span-2"><label for="razon_social">Razón social</label><input type="text" class="form-control input-sm" name="razon_social" id="razon_social" value="{{$orden->razon_social}}" placeholder="Razon Social" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required></div>
<div class="re-field "><label for="ciudad">Ciudad</label><input type="text" class="form-control input-sm" name="ciudad" id="ciudad" value="{{$orden->ciudad}}" placeholder="Ciudad" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"></div>
<div class="re-field re-span-2"><label for="direccion">Dirección</label><input type="text" class="form-control input-sm" name="direccion" id="direccion" value="{{$orden->direccion}}" placeholder="Dirección" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"></div>
<div class="re-field "><label for="telefono">Teléfono</label><input type="text" class="form-control input-sm" name="telefono" id="telefono" value="{{$orden->telefono}}" placeholder="Teléfono" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"></div>
<div class="re-field re-span-2"><label for="email">Correo electrónico</label><input type="text" class="form-control input-sm" name="email" id="email" value="{{$orden->email}}" placeholder="Mail"></div></div></section>
      <section class="re-section" id="rc-diagnosticos"><header><span class="re-step">04</span><div><h2>Diagnósticos</h2><p>Copia editable de los diagnósticos registrados por el médico en esta consulta.</p></div><button class="btn btn-outline-primary" type="button" id="rc-refresh-diagnosticos" onclick="refrescar_diagnosticos()"><i class="fas fa-sync-alt"></i> Refrescar</button></header><div class="re-grid"><div class="re-field re-diagnosticos-field"><label for="diagnosticos_consulta">Diagnósticos de la consulta</label><textarea class="form-control" name="diagnosticos_consulta" id="diagnosticos_consulta" rows="5" placeholder="La consulta todavía no tiene diagnósticos registrados." onchange="guardar_auto()">{{ $orden->diagnosticos_consulta }}</textarea><div class="re-diagnosticos-meta"><span id="diagnosticos_estado" role="status" aria-live="polite"></span><span>Máximo 299 caracteres: <strong id="diagnosticos_contador">0</strong>/299</span></div></div></div></section>
      <section class="re-section" id="rc-productos"><header><span class="re-step">05</span><div><h2>Productos y servicios</h2><p>Revisa los conceptos, cantidades y valores del cobro.</p></div>@if(Auth::user()->isSystemUser())<a class="btn btn-outline-primary" href="{{url('v3/inventario/listadeproductos')}}" target="_blank" rel="noopener"><i class="fas fa-boxes"></i> Administrar productos</a>@endif</header><div class="re-async" id="detalles_orden"></div></section>
      <section class="re-section" id="rc-pagos"><header><span class="re-step">06</span><div><h2>Formas de pago</h2><p>Registra los pagos antes de emitir el recibo.</p></div>@if(Auth::user()->isSystemUser())<div class="re-head-actions"><a class="btn btn-outline-primary" href="{{route('v3.payment-types.index')}}" target="_blank" rel="noopener"><i class="fas fa-cog"></i> Administrar formas de pago</a><a class="btn btn-outline-primary" href="{{route('v3.card-types.index')}}" target="_blank" rel="noopener"><i class="fas fa-credit-card"></i> Gestionar tipos de tarjeta</a></div>@endif</header><div class="re-async" id="detalles_forma_pago"></div></section>
      </fieldset>
      <footer class="re-actions"><span><i class="fas fa-info-circle"></i> {{$receiptEditable ? 'Revisa los datos y pagos antes de emitir.' : ($receiptInvoiced ? 'El recibo está facturado y bloqueado.' : 'El recibo está bloqueado para edición.')}}</span><div>@if($receiptEditable)<button class="btn btn-outline-primary saves" type="button" onclick="guardar_cabecera()"><i class="fas fa-save"></i> Guardar cambios</button><button class="btn btn-primary" type="button" onclick="emitir()" @if(!$taxReady) disabled title="Configure el IVA antes de emitir" @endif><i class="fas fa-check-circle"></i> Emitir recibo</button>@endif @if((int)$orden->estado===1 && !$receiptInvoiced)<button class="btn re-return" type="button" onclick="devolver()"><i class="fas fa-undo-alt"></i> Devolver</button><a class="btn re-invoice" href="{{route('v3.agenda-receipts.invoice-preview',array('receipt'=>$orden->id))}}"><i class="fas fa-file-invoice-dollar"></i><span>Facturar recibo</span></a>@elseif($receiptInvoiced)<a class="btn re-invoice" href="{{route('v3.agenda-receipts.invoice-preview',array('receipt'=>$orden->id))}}"><i class="fas fa-file-invoice"></i><span>Ver factura</span></a>@endif @if((int)$orden->estado===1)<a class="btn btn-primary" id="printer" href="{{route('facturacion.imprimir_ride',array('id_orden'=>$orden->id))}}" target="_blank" rel="noopener"><i class="fas fa-print"></i> Imprimir recibo</a>@endif</div></footer>
    </form>
  </div>
</div>
@include('contable.nuevo_recibo._editor_scripts')
<script>
var DIAGNOSTICOS_LIMITE = 299;
var diagnosticosLimiteAvisado = false;

function actualizar_contador_diagnosticos(mostrarAlerta) {
    var textarea = document.getElementById('diagnosticos_consulta');
    var contador = document.getElementById('diagnosticos_contador');
    if (!textarea || !contador) return;

    if (textarea.value.length > DIAGNOSTICOS_LIMITE) {
        textarea.value = textarea.value.substring(0, DIAGNOSTICOS_LIMITE);
        if (mostrarAlerta && !diagnosticosLimiteAvisado) {
            diagnosticosLimiteAvisado = true;
            receiptAlert({
                title: 'Límite de caracteres',
                icon: 'warning',
                text: 'Solo se pueden ingresar 299 caracteres en los diagnósticos.'
            });
        }
    } else if (textarea.value.length < DIAGNOSTICOS_LIMITE) {
        diagnosticosLimiteAvisado = false;
    }

    contador.textContent = textarea.value.length;
}

document.getElementById('diagnosticos_consulta').addEventListener('input', function() {
    actualizar_contador_diagnosticos(true);
});
actualizar_contador_diagnosticos(false);

function refrescar_diagnosticos() {
    var button = document.getElementById('rc-refresh-diagnosticos');
    var textarea = document.getElementById('diagnosticos_consulta');
    var estado = document.getElementById('diagnosticos_estado');
    var original = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando...';
    estado.className = '';
    estado.textContent = 'Actualizando diagnósticos...';

    fetch("{{route('v3.agenda-receipts.diagnoses-refresh', array('id' => $orden->id))}}", {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('#form_actualizar_cabecera input[name="_token"]').value
        }
    }).then(function(response) {
        return response.json().then(function(data) {
            if (!response.ok) throw new Error(data.message || 'No se pudieron actualizar los diagnósticos.');
            return data;
        });
    }).then(function(data) {
        textarea.value = data.diagnosticos || '';
        actualizar_contador_diagnosticos(false);
        estado.className = 'is-success';
        estado.textContent = data.mensaje;
    }).catch(function(error) {
        estado.className = 'is-error';
        estado.textContent = error.message;
    }).finally(function() {
        button.disabled = false;
        button.innerHTML = original;
    });
}
</script>
@if((int)Auth::user()->id_tipo_usuario===1)
<script>
document.getElementById('rc-refresh-cajas').addEventListener('click', function() {
    var button = this, select = document.getElementById('caja'), previous = select.value, selected = select.options[select.selectedIndex];
    button.disabled = true;
    fetch("{{route('v3.cash-registers.index',array('opciones'=>1))}}", {credentials:'same-origin', headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}})
        .then(function(response) { if (!response.ok) throw new Error('No se pudieron cargar las cajas.'); return response.json(); })
        .then(function(data) {
            select.replaceChildren(new Option('Seleccione...', ''));
            data.results.forEach(function(item) { select.add(new Option(item.descripcion, item.id)); });
            if (previous && !data.results.some(function(item) { return String(item.id) === previous; }) && selected) select.add(new Option(selected.text, previous));
            select.value = previous;
        }).catch(function(error) { receiptAlert({title:'Cajas de cobro', icon:'error', text:error.message}); })
        .finally(function() { button.disabled = false; });
});
</script>
@endif
