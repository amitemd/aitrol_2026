(function () {
    'use strict';

    var card = document.getElementById('la-agenda-card');
    if (!card || card.getAttribute('data-agenda-ready') === '1') return;
    card.setAttribute('data-agenda-ready', '1');

    var endpoint = card.getAttribute('data-endpoint');
    var today = card.getAttribute('data-today');
    var pendingRequest = null;

    function setLoading(active) {
        var layer = card.querySelector('.la-agenda-loading');
        if (!layer) return;
        layer.hidden = !active;
        layer.setAttribute('aria-hidden', active ? 'false' : 'true');
    }

    function showError(message) {
        var old = card.querySelector('.la-agenda-error');
        if (old) old.parentNode.removeChild(old);
        var calendar = card.querySelector('.la-agenda-calendar');
        if (!calendar) return;
        var alert = document.createElement('div');
        alert.className = 'la-agenda-error';
        alert.setAttribute('role', 'alert');
        alert.textContent = message;
        calendar.insertBefore(alert, calendar.firstChild);
    }

    function loadDate(date, updateHistory) {
        if (!/^\d{4}-\d{2}-\d{2}$/.test(date || '')) {
            showError('Selecciona una fecha válida.');
            return;
        }

        if (pendingRequest && pendingRequest.readyState !== 4) pendingRequest.abort();
        var request = new XMLHttpRequest();
        pendingRequest = request;
        var url = endpoint + '?fecha=' + encodeURIComponent(date);
        setLoading(true);
        request.open('GET', url, true);
        request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        request.setRequestHeader('Accept', 'application/json');
        request.onreadystatechange = function () {
            if (request.readyState !== 4 || request !== pendingRequest) return;
            setLoading(false);
            if (request.status >= 200 && request.status < 300) {
                try {
                    var response = JSON.parse(request.responseText);
                    var current = card.querySelector('.la-agenda-calendar');
                    if (!current || !response.html) throw new Error('Respuesta incompleta');
                    var holder = document.createElement('div');
                    holder.innerHTML = response.html;
                    var replacement = holder.firstElementChild;
                    if (!replacement) throw new Error('Calendario vacío');
                    current.parentNode.replaceChild(replacement, current);
                    if (updateHistory !== false && window.history && window.history.pushState) {
                        window.history.pushState({laboratoryAgendaDate: response.date}, '', response.url || url);
                    }
                } catch (error) {
                    showError('No se pudo actualizar el calendario. Inténtalo nuevamente.');
                }
            } else if (request.status !== 0) {
                showError('No se pudo consultar la agenda. La información anterior se conservó.');
            }
        };
        request.onerror = function () {
            if (request !== pendingRequest) return;
            setLoading(false);
            showError('No hay conexión con el servidor. La información anterior se conservó.');
        };
        request.send(null);
    }

    card.addEventListener('click', function (event) {
        var target = event.target;
        while (target && target !== card && !target.classList.contains('js-la-agenda-date') && !target.classList.contains('js-la-agenda-submit')) {
            target = target.parentNode;
        }
        if (!target || target === card) return;
        event.preventDefault();
        if (target.classList.contains('js-la-agenda-date')) {
            loadDate(target.getAttribute('data-date'), true);
            return;
        }
        var field = card.querySelector('.js-la-agenda-form [name="fecha"]');
        loadDate(field ? field.value : '', true);
    });

    card.addEventListener('submit', function (event) {
        if (!event.target.classList.contains('js-la-agenda-form')) return;
        event.preventDefault();
        var field = event.target.querySelector('[name="fecha"]');
        loadDate(field ? field.value : '', true);
    });

    window.addEventListener('popstate', function () {
        var match = window.location.search.match(/[?&]fecha=([^&]+)/);
        loadDate(match ? decodeURIComponent(match[1]) : today, false);
    });
})();
