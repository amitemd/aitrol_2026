(() => {
    'use strict';

    const states = new WeakMap();
    let leaflet;

    function resource(tag, url, integrity) {
        return new Promise((resolve, reject) => {
            const element = document.createElement(tag);
            element.integrity = integrity;
            element.crossOrigin = 'anonymous';
            if (tag === 'link') {
                element.rel = 'stylesheet';
                element.href = url;
            } else {
                element.src = url;
            }
            element.addEventListener('load', resolve, { once: true });
            element.addEventListener('error', () => {
                element.remove();
                reject(new Error('No se pudo cargar el mapa.'));
            }, { once: true });
            document.head.append(element);
        });
    }

    function loadLeaflet() {
        leaflet ??= Promise.all([
            resource('link', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', 'sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY='),
            resource('script', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', 'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo='),
        ]).catch((error) => {
            leaflet = undefined;
            throw error;
        });
        return leaflet;
    }

    function showPanel(form, name) {
        form.querySelectorAll('[data-ficha-tab]').forEach((button) => {
            const selected = button.dataset.fichaTab === name;
            button.classList.toggle('active', selected);
            button.setAttribute('aria-pressed', String(selected));
        });
        form.querySelectorAll('[data-ficha-panel]').forEach((panel) => {
            panel.hidden = panel.dataset.fichaPanel !== name;
        });
        if (name === 'distribucion') {
            requestAnimationFrame(() => states.get(form)?.map?.invalidateSize());
        }
    }

    async function openMap(form, button, state) {
        const box = form.querySelector('[data-ficha-mapa]');
        const status = form.querySelector('[data-ficha-mapa-estado]');
        if (state.map) {
            box.hidden = !box.hidden;
            button.textContent = box.hidden ? 'Abrir mapa' : 'Ocultar mapa';
            if (!box.hidden) state.map.invalidateSize();
            return;
        }

        button.disabled = true;
        status.textContent = 'Cargando mapa…';
        try {
            await loadLeaflet();
            if (!form.isConnected) return;
            const lat = form.elements.namedItem('latitud');
            const lng = form.elements.namedItem('longitud');
            const hasCoordinates = () => lat.value !== '' && lng.value !== '' && lat.validity.valid && lng.validity.valid;
            const point = () => [Number(lat.value), Number(lng.value)];

            box.hidden = false;
            const map = L.map(box).setView(hasCoordinates() ? point() : [-1.831239, -78.183406], hasCoordinates() ? 15 : 6);
            state.map = map;
            const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            });
            tiles.on('tileerror', () => {
                status.textContent = 'El mapa base no está disponible. Puedes ingresar las coordenadas manualmente.';
            });
            tiles.addTo(map);
            const marker = L.circleMarker([0, 0], { radius: 8, color: '#0d6efd', fillOpacity: .9 });
            function syncMarker() {
                if (hasCoordinates()) marker.setLatLng(point()).addTo(map);
                else marker.remove();
            }

            map.on('click', ({ latlng }) => {
                const position = latlng.wrap();
                lat.value = position.lat.toFixed(6);
                lng.value = position.lng.toFixed(6);
                syncMarker();
            });
            for (const input of [lat, lng]) {
                input.addEventListener('change', () => {
                    syncMarker();
                    if (hasCoordinates()) map.panTo(point());
                });
            }
            syncMarker();
            button.textContent = 'Ocultar mapa';
            status.textContent = 'Haz clic en el mapa para guardar el punto de entrega.';
        } catch {
            status.textContent = 'No se pudo cargar el mapa. Puedes ingresar las coordenadas manualmente.';
        } finally {
            button.disabled = false;
        }
    }

    function setup(form) {
        if (!form || states.has(form)) return;
        const contacts = form.querySelector('[data-ficha-contactos]');
        const add = form.querySelector('[data-ficha-contacto-agregar]');
        const indices = [...contacts.children].map((entry) => Number(entry.dataset.indice));
        const state = { nextContact: Math.max(-1, ...indices) + 1 };
        states.set(form, state);
        add.disabled = contacts.children.length >= 10;
        const invalid = form.querySelector('.is-invalid')?.closest('[data-ficha-panel]');
        showPanel(form, invalid?.dataset.fichaPanel ?? 'generales');

        form.addEventListener('click', (event) => {
            const tab = event.target.closest('[data-ficha-tab]');
            if (tab) showPanel(form, tab.dataset.fichaTab);
            if (event.target.closest('[data-ficha-contacto-agregar]') && contacts.children.length < 10) {
                const template = form.querySelector('[data-ficha-contacto-template]');
                const html = template.innerHTML.replaceAll('__indice__', String(state.nextContact++));
                contacts.insertAdjacentHTML('beforeend', html);
                contacts.lastElementChild.querySelector('input').focus();
            }
            const remove = event.target.closest('[data-ficha-contacto-quitar]');
            if (remove) {
                remove.closest('[data-ficha-contacto]').remove();
            }
            add.disabled = contacts.children.length >= 10;
            if (remove) add.focus();

            const mapButton = event.target.closest('[data-ficha-mapa-abrir]');
            if (mapButton) openMap(form, mapButton, state);
        });
        form.addEventListener('aitrol:ficha-error', (event) => showPanel(form, event.target.dataset.fichaPanel));
        form.addEventListener('invalid', (event) => {
            const panel = event.target.closest('[data-ficha-panel]');
            if (panel?.hidden && !form.querySelector('[data-ficha-panel]:not([hidden]) :invalid')) {
                showPanel(form, panel.dataset.fichaPanel);
            }
        }, true);

        const photo = form.elements.namedItem('foto');
        const preview = form.querySelector('[data-ficha-foto]');
        const original = preview.getAttribute('src');
        let previewUrl;
        photo.addEventListener('change', () => {
            if (previewUrl) URL.revokeObjectURL(previewUrl);
            previewUrl = photo.files[0] ? URL.createObjectURL(photo.files[0]) : null;
            const src = previewUrl ?? original;
            if (src) preview.src = src;
            else preview.removeAttribute('src');
            preview.hidden = !src;
        });
        form.closest('dialog')?.addEventListener('close', () => {
            if (previewUrl) URL.revokeObjectURL(previewUrl);
            state.map?.remove();
        }, { once: true });
    }

    document.addEventListener('aitrol:form-ready', (event) => {
        if (event.target.matches('[data-ficha-cliente]')) setup(event.target);
    });
    setup(document.querySelector('[data-ficha-cliente]'));
})();