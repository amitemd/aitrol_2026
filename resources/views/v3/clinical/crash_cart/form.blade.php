@include('v3.clinical.crash_cart._styles')
<main class="cart-shell">
    <header class="cart-hero"><div><small>ENFERMERÍA · CARRO DE PARO</small><h1>{{ $record->id ? 'Corregir control #'.$record->id : 'Registrar control diario' }}</h1><p>Verifique cada lote y registre la cantidad encontrada en el carro.</p></div><a class="cart-btn secondary" href="{{ route('v3.crash-cart.index') }}">Volver al listado</a></header>
    @if(!$record->id)
        <section class="cart-card"><form method="get" action="{{ route('v3.crash-cart.create') }}" class="cart-toolbar">
            <div class="cart-field"><label for="load-warehouse">Bodega del carro de paro</label><select id="load-warehouse" name="id_bodega" required><option value="">Seleccione una bodega</option>@foreach($warehouses as $warehouse)<option value="{{ $warehouse->id }}" {{ $record->id_bodega == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->nombre }}</option>@endforeach</select></div>
            <div class="cart-field"><label for="load-type">Tipo de control</label><select id="load-type" name="tipo"><option value="1" {{ $record->tipo == 1 ? 'selected' : '' }}>Medicamentos</option><option value="2" {{ $record->tipo == 2 ? 'selected' : '' }}>Insumos</option></select></div>
            <div class="cart-field"><label for="load-date">Fecha</label><input id="load-date" type="date" name="fecha" value="{{ $record->fecha }}" max="{{ date('Y-m-d') }}" required></div>
            <div class="cart-field"><label for="load-service">Servicio</label><input id="load-service" name="servicio" value="{{ $record->servicio }}" maxlength="100" required></div>
            <button class="cart-btn">Cargar productos</button>
        </form></section>
    @endif
    @if($record->id || $record->id_bodega)
        <section class="cart-card">
            <div class="cart-summary"><div><strong>Fecha</strong>{{ $record->fecha }}</div><div><strong>Servicio</strong>{{ $record->servicio }}</div><div><strong>Tipo</strong>{{ $record->tipo == 1 ? 'Medicamentos' : 'Insumos' }}</div><div><strong>Lotes / series</strong>{{ $details->count() }}</div></div>
            @if($historical)<div class="cart-note">Registro de fecha anterior. {{ $record->id ? 'Se conserva la referencia declarada al crear el control.' : 'Ingrese la cantidad de referencia que constaba ese día y explique su origen en la observación. Las existencias actuales no acreditan el saldo histórico.' }}</div>
            @else<div class="cart-note">La referencia corresponde al inventario al registrar el control. Una diferencia en el conteo queda documentada para su revisión; guardar el control no produce movimientos de inventario.</div>@endif
            @if($details->isEmpty())<div class="cart-empty">No hay productos disponibles de este tipo en la bodega. Verifique la bodega y sus ingresos de inventario.</div>
            @elseif($details->count() > 300)<div class="cart-error">Esta bodega contiene más de 300 series. Seleccione la bodega específica del carro de paro para realizar el control.</div>
            @elseif(($record->id && $permissions['update']) || (!$record->id && $permissions['create']))
                <div id="cart-save-error" class="cart-error" role="alert" hidden></div>
                <form id="cart-control-form" method="post" action="{{ $record->id ? route('v3.crash-cart.update', ['control' => $record->id]) : route('v3.crash-cart.store') }}">
                    {{ csrf_field() }}
                    <input type="hidden" name="id_bodega" value="{{ $record->id_bodega }}"><input type="hidden" name="fecha" value="{{ $record->fecha }}"><input type="hidden" name="tipo" value="{{ $record->tipo }}"><input type="hidden" name="servicio" value="{{ $record->servicio }}"><input type="hidden" name="version" value="{{ $record->version }}">
                    <div class="cart-toolbar" style="margin:16px 0"><div class="cart-field"><label for="save-person">Responsable del control</label><select name="responsable_id" id="save-person" required><option value="">Seleccione un responsable</option>@foreach($users as $person)<option value="{{ $person->id }}" {{ $record->responsable_id == $person->id ? 'selected' : '' }}>{{ $person->apellido1 }} {{ $person->apellido2 }} {{ $person->nombre1 }}</option>@endforeach</select></div>
                    <div class="cart-field" style="flex:2"><label for="save-note">Observación del control {{ $historical ? '(origen de la referencia)' : '' }}</label><input id="save-note" name="observacion" value="{{ $record->observacion }}" maxlength="2000" {{ $historical ? 'required' : '' }}></div></div>
                    <div class="cart-scroll"><table class="cart-table"><thead><tr><th>Producto / lote / serie</th><th>Presentación</th><th>Concentración</th><th>Vencimiento</th><th>Referencia</th><th>Conteo</th><th>Observación</th></tr></thead><tbody>
                    @foreach($details as $index => $line)
                        @php($expiry = $line->fecha_caducidad === '0000-00-00' ? '' : $line->fecha_caducidad)
                        @php($status = $carts->expiryStatus($expiry))
                        <tr data-cart-line><td class="cart-name"><strong>{{ $line->producto_nombre }}</strong><div class="cart-muted">{{ $line->producto_codigo }} · Lote: {{ $line->lote ?: '—' }}<br>Serie: {{ $line->serie ?: '—' }}</div><span class="cart-badge {{ $status === 'Vencido' ? 'danger' : ($status === 'Vigente' ? '' : 'warning') }}">{{ $status }}</span><input type="hidden" data-field="inventory_id" value="{{ $line->inventory_id }}"></td>
                        <td><input data-field="presentacion" aria-label="Presentación de {{ $line->producto_nombre }}" value="{{ $line->presentacion ?? '' }}" maxlength="100" {{ $record->tipo == 1 ? 'required' : '' }} placeholder="Ej. ampolla"></td>
                        <td><input data-field="concentracion" aria-label="Concentración de {{ $line->producto_nombre }}" value="{{ $line->concentracion ?? '' }}" maxlength="100" placeholder="Según etiqueta"></td>
                        <td><input data-field="fecha_caducidad" type="date" aria-label="Vencimiento de {{ $line->producto_nombre }}" value="{{ $expiry }}"></td>
                        <td><input data-field="referencia_cantidad" type="number" min="0" max="2147483647" step="1" aria-label="Referencia de {{ $line->producto_nombre }}" value="{{ $historical && !$record->id ? '' : $line->referencia_cantidad }}" {{ $historical && !$record->id ? '' : 'readonly' }} required></td>
                        <td><input data-field="stock" type="number" min="0" max="2147483647" step="1" aria-label="Conteo de {{ $line->producto_nombre }}" value="{{ $record->id ? $line->stock : '' }}" required placeholder="Contar"></td>
                        <td><input data-field="observacion" aria-label="Observación de {{ $line->producto_nombre }}" value="{{ $line->observacion ?? '' }}" maxlength="500"></td></tr>
                    @endforeach
                    </tbody></table></div>
                    <div class="cart-foot"><span class="cart-muted">{{ $record->id ? 'Cada corrección conserva la versión anterior en el historial.' : 'Complete todos los conteos antes de guardar.' }}</span><button id="cart-save" class="cart-btn" type="submit">{{ $record->id ? 'Guardar corrección' : 'Guardar control' }}</button></div>
                    <noscript><div class="cart-error">Active JavaScript para registrar todos los productos de forma segura.</div></noscript>
                </form>
            @else<div class="cart-note">Su usuario dispone de consulta. Solicite a Sistemas el permiso correspondiente para registrar o corregir controles.</div>@endif
        </section>
    @endif
</main>
<script>
(function(){
    var form=document.getElementById('cart-control-form'); if(!form)return;
    var busy=false, dirty=false, error=document.getElementById('cart-save-error'), button=document.getElementById('cart-save');
    form.addEventListener('input',function(){dirty=true;});
    window.addEventListener('beforeunload',function(e){if(dirty){e.preventDefault();e.returnValue='';}});
    form.addEventListener('submit',function(e){
        e.preventDefault();if(busy||!form.reportValidity())return;
        var payload={}, rows=[];
        new FormData(form).forEach(function(value,key){payload[key]=value;});
        form.querySelectorAll('[data-cart-line]').forEach(function(row){var item={};row.querySelectorAll('[data-field]').forEach(function(input){item[input.dataset.field]=input.value===''?null:input.value;});rows.push(item);});
        payload.items=rows;busy=true;button.disabled=true;error.hidden=true;
        fetch(form.action,{method:'{{ $record->id ? 'PATCH' : 'POST' }}',credentials:'same-origin',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':payload._token},body:JSON.stringify(payload)})
        .then(function(response){return response.json().catch(function(){return {message:'La sesión venció o el servidor no pudo procesar la solicitud. Recargue e intente nuevamente.'};}).then(function(body){if(!response.ok||!body.redirect)throw body;return body;});})
        .then(function(body){dirty=false;window.location.assign(body.redirect);})
        .catch(function(body){var message=body.errors?Object.keys(body.errors).map(function(key){return body.errors[key].join(' ');}).join(' '):(body.message||'No se pudo guardar el control. Revise la conexión.');error.textContent=message;error.hidden=false;error.scrollIntoView({block:'center',behavior:'smooth'});busy=false;button.disabled=false;});
    });
})();
</script>
