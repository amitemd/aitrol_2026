<div id="gateway-tracking" style="margin-top: 12px">
    <div>
        <button type="button" class="btn btn-default pull-right" id="gateway-refresh-all">Actualizar estados</button>
    </div>
    <div>
        <p>Consulta automática cada 60 segundos mientras esta pantalla esté visible. Se muestran los últimos estados confirmados.</p>
        <p id="gateway-notice" role="status" aria-live="polite"></p>
        <div id="gateway-pages"></div>
        @if(Auth::user()->id_tipo_usuario == 1)
        <details>
            <summary>Vincular un ticket enviado antes de esta actualización</summary>
            <form id="gateway-import-form" class="form-inline" style="margin-top: 12px">
                <label for="gateway-old-id">ID del ticket en Gateway</label>
                <input id="gateway-old-id" name="gateway_id" class="form-control" required maxlength="36" placeholder="ID al final de /tickets/…">
                <button class="btn btn-default" type="submit">Vincular ticket</button>
                <p class="help-block">Se vincula al administrador actual. Solo se aceptan tickets de esta sucursal.</p>
            </form>
        </details>
        @endif
    </div>
</div>
<script>
(function () {
    var list = document.getElementById('gateway-ticket-list');
    var notice = document.getElementById('gateway-notice');
    var button = document.getElementById('gateway-refresh-all');
    var pageUrl = {!! json_encode(route('gateway_tickets.list'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!};
    var csrf = {!! json_encode(csrf_token()) !!};
    var busy = false;
    var timer;
    var labels = {open: 'Recibido', assigned: 'Asignado', resolved: 'Resuelto'};
    var filters = document.getElementById('formi');
    var selectedState = filters.elements.estado.value;
    var stateMap = {'0': 'open', '1': 'assigned', '2': 'resolved'};
    pageUrl += '?' + ['desde', 'hasta', 'estado'].map(function (key) {
        return key + '=' + encodeURIComponent(filters.elements[key].value);
    }).join('&');
    var firstPageUrl = pageUrl;

    function applyState(row, state) {
        row.style.display = !selectedState || stateMap[selectedState] === state ? '' : 'none';
    }

    function request(url, data, done) {
        var xhr = new XMLHttpRequest();
        xhr.open(data === null ? 'GET' : 'POST', url, true);
        xhr.timeout = 30000;
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        if (data !== null) {
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
            xhr.setRequestHeader('Accept', 'application/json');
        }
        xhr.onload = function () {
            if (data === null) {
                done(xhr.status === 200 ? null : 'No se pudo cargar el listado. Revise su sesión y la instalación de la tabla gateway_tickets.', xhr.responseText);
                return;
            }
            var result;
            try { result = JSON.parse(xhr.responseText); } catch (e) { result = {}; }
            done(xhr.status >= 200 && xhr.status < 300 ? null : (result.error || 'No se pudo actualizar. Revise su sesión o intente nuevamente.'), result);
        };
        xhr.onerror = xhr.ontimeout = function () { done('Sin conexión. Se conserva el último estado confirmado.', {}); };
        xhr.send(data);
    }
    function finish() {
        busy = false;
        button.disabled = false;
        clearTimeout(timer);
        timer = setTimeout(function () { if (!document.hidden) { refresh(); } else { finish(); } }, 60000);
    }
    function refresh() {
        if (busy) { return; }
        busy = true;
        button.disabled = true;
        notice.textContent = 'Consultando Gateway…';
        var rows = list.querySelectorAll('[data-gateway-refresh]');
        var index = 0, failed = 0;
        function next() {
            if (index >= rows.length) {
                notice.textContent = !rows.length ? 'No hay tickets vinculados para consultar en Gateway.' : (failed ? 'Algunas consultas fallaron; sus estados anteriores se conservan.' : 'Consulta finalizada. Estados confirmados por Gateway.');
                if (rows.length && !failed && selectedState && !Array.prototype.some.call(rows, function (row) { return row.style.display !== 'none'; })) {
                    notice.textContent = 'No hay tickets de Gateway con el estado y las fechas seleccionados.';
                }
                finish();
                return;
            }
            var row = rows[index++];
            request(row.getAttribute('data-gateway-refresh'), '', function (error, result) {
                row.querySelector('.gateway-error').textContent = error || '';
                if (error) { failed++; } else {
                    var ticket = result.ticket;
                    row.querySelector('.gateway-status').textContent = labels[ticket.status] || ticket.status;
                    row.setAttribute('data-gateway-status', ticket.status);
                    applyState(row, ticket.status);
                    row.querySelector('.gateway-assignee').textContent = ticket.assigned_to ? 'Operador #' + ticket.assigned_to : 'Sin asignar';
                    row.querySelector('.gateway-resolution').textContent = ticket.resolution || '';
                    row.querySelector('.gateway-synced').textContent = result.synced_at;
                }
                next();
            });
        }
        next();
    }
    function load(url) {
        if (busy) { return; }
        busy = true;
        button.disabled = true;
        pageUrl = url;
        request(url, null, function (error, html) {
            if (error) { notice.textContent = error; finish(); return; }
            var fragment = document.createElement('div');
            fragment.innerHTML = html;
            var body = fragment.querySelector('tbody');
            if (!body) { notice.textContent = 'No se pudo cargar el listado. Actualice la página y revise su sesión.'; finish(); return; }
            list.innerHTML = body.innerHTML;
            var pages = fragment.querySelector('.gateway-pagination');
            document.getElementById('gateway-pages').innerHTML = pages ? pages.innerHTML : '';
            function collectRemaining(container) {
                var next = container.querySelector('.gateway-pagination a[rel="next"]');
                if (!selectedState || !next) {
                    if (selectedState) { document.getElementById('gateway-pages').innerHTML = ''; }
                    Array.prototype.forEach.call(list.querySelectorAll('[data-gateway-refresh]'), function (row) {
                        applyState(row, row.getAttribute('data-gateway-status'));
                    });
                    busy = false;
                    refresh();
                    return;
                }
                request(next.href, null, function (error, more) {
                    if (error) { notice.textContent = error; finish(); return; }
                    var part = document.createElement('div');
                    part.innerHTML = more;
                    var moreBody = part.querySelector('tbody');
                    if (!moreBody) { notice.textContent = 'No se pudo completar la búsqueda.'; finish(); return; }
                    list.insertAdjacentHTML('beforeend', moreBody.innerHTML);
                    collectRemaining(part);
                });
            }
            collectRemaining(fragment);
        });
    }
    button.onclick = function () { load(pageUrl); };
    document.getElementById('gateway-pages').addEventListener('click', function (event) {
        var link = event.target.closest('a');
        if (link) { event.preventDefault(); load(link.href); }
    });
    var form = document.getElementById('gateway-import-form');
    if (form) {
        form.onsubmit = function (event) {
            event.preventDefault();
            if (busy) { return; }
            busy = true;
            request({!! json_encode(route('gateway_tickets.import'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}, 'gateway_id=' + encodeURIComponent(document.getElementById('gateway-old-id').value.trim()), function (error) {
                busy = false;
                if (error) { notice.textContent = error; return; }
                form.reset();
                load(firstPageUrl);
            });
        };
    }
    load(pageUrl);
})();
</script>
