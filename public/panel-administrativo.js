(() => {
    const panel = document.querySelector('[data-admin-dashboard]');
    if (!panel) return;

    const opciones = panel.dataset;
    const inicial = JSON.parse(opciones.defaultOrder);
    const selector = panel.querySelector('[data-dashboard-chart-type]');
    const estado = panel.querySelector('[data-dashboard-status]');
    const editar = panel.querySelector('[data-dashboard-edit]');
    const herramientas = panel.querySelector('[data-dashboard-edit-tools]');
    const grupos = [...panel.querySelectorAll('[data-dashboard-lane]')];
    let guardado = JSON.parse(opciones.preferences);
    let actual = structuredClone(guardado);
    let editando = false;
    let guardando = false;
    let arrastrado = null;
    let grafico = null;

    const lienzo = panel.querySelector('#sales-chart');
    function dibujar() {
        if (!lienzo) return;
        if (!window.Chart) {
            const error = panel.querySelector('[data-dashboard-chart-error]');
            lienzo.hidden = true;
            error.textContent = opciones.chartError;
            error.hidden = false;
            return;
        }

        const { meses, series } = JSON.parse(opciones.chart);
        const estilo = getComputedStyle(panel);
        const texto = estilo.getPropertyValue('--bs-body-color').trim();
        const borde = estilo.getPropertyValue('--bs-border-color').trim();
        const tipo = actual.grafico;
        const dispersion = tipo === 'scatter';
        const formato = new Intl.NumberFormat(document.documentElement.lang || 'es', {
            style: 'currency', currency: 'USD',
        });
        grafico?.destroy();
        grafico = new window.Chart(lienzo, {
            type: tipo === 'area' ? 'line' : tipo,
            data: {
                labels: meses,
                datasets: series.map(({ id, titulo, valores }) => {
                    const color = id === 'ventas' ? '#4f9bff' : '#12bda6';
                    return {
                        label: titulo,
                        data: dispersion ? valores.map((y, x) => ({ x, y })) : valores,
                        borderColor: color,
                        backgroundColor: tipo === 'area' ? color + '38' : color,
                        fill: tipo === 'area',
                        pointRadius: dispersion ? 4 : 2,
                        borderWidth: 2,
                        maxBarThickness: 26,
                    };
                }),
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                color: texto,
                interaction: { mode: dispersion ? 'nearest' : 'index', intersect: dispersion },
                scales: {
                    x: { type: 'category', ticks: { color: texto }, grid: { display: false } },
                    y: { beginAtZero: true, ticks: { color: texto }, grid: { color: borde } },
                },
                plugins: {
                    legend: { position: 'bottom', labels: { color: texto, usePointStyle: true } },
                    tooltip: { callbacks: { label: item => item.dataset.label + ' · ' + formato.format(item.parsed.y) } },
                },
            },
        });
    }

    function mostrarOrden() {
        for (const grupo of grupos) {
            const tarjetas = [...grupo.children];
            const ordenadas = actual.distribucion[grupo.dataset.dashboardLane]
                .map(id => tarjetas.find(tarjeta => tarjeta.dataset.dashboardCard === id)).filter(Boolean);
            ordenadas.forEach((tarjeta, indice) => {
                if (grupo.children[indice] !== tarjeta) grupo.insertBefore(tarjeta, grupo.children[indice]);
                tarjeta.querySelectorAll('[data-dashboard-step]').forEach(boton => {
                    const destino = indice + Number(boton.dataset.dashboardStep);
                    boton.setAttribute('aria-disabled', String(destino < 0 || destino >= ordenadas.length));
                });
            });
        }
    }

    function mover(origen, destino, despues) {
        if (!origen || !destino || origen === destino || origen.parentElement !== destino.parentElement) return;
        const grupo = origen.parentElement;
        const visibles = [...grupo.children].map(tarjeta => tarjeta.dataset.dashboardCard);
        const id = origen.dataset.dashboardCard;
        visibles.splice(visibles.indexOf(id), 1);
        visibles.splice(visibles.indexOf(destino.dataset.dashboardCard) + Number(despues), 0, id);
        const clave = grupo.dataset.dashboardLane;
        let indice = 0;
        actual.distribucion[clave] = actual.distribucion[clave].map(valor => visibles.includes(valor) ? visibles[indice++] : valor);
        mostrarOrden();
        estado.textContent = opciones.moved;
    }

    function activarEditor(activo) {
        editando = activo;
        editar.hidden = activo;
        editar.setAttribute('aria-expanded', String(activo));
        herramientas.hidden = !activo;
        panel.querySelectorAll('[data-dashboard-mover]').forEach(control => { control.hidden = !activo; });
        (activo ? herramientas.querySelector('button') : editar).focus();
    }

    async function guardar() {
        if (guardando || !opciones.saveUrl) return;
        guardando = true;
        panel.querySelectorAll('.dashboard-tools button, [data-dashboard-chart-type]').forEach(control => { control.disabled = true; });
        try {
            const respuesta = await fetch(opciones.saveUrl, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': opciones.csrf },
                body: JSON.stringify(actual),
            });
            if (respuesta.status !== 204) throw new Error(String(respuesta.status));
            guardado = structuredClone(actual);
            estado.textContent = opciones.saved;
        } catch {
            estado.textContent = opciones.saveError;
            if (!editando) restaurar(guardado);
            return;
        } finally {
            guardando = false;
            panel.querySelectorAll('.dashboard-tools button, [data-dashboard-chart-type]').forEach(control => { control.disabled = false; });
        }
        if (editando) activarEditor(false);
    }

    function restaurar(preferencias) {
        actual = structuredClone(preferencias);
        mostrarOrden();
        if (selector) selector.value = actual.grafico;
        dibujar();
    }

    selector?.addEventListener('change', () => {
        actual.grafico = selector.value;
        dibujar();
        if (!editando) void guardar();
    });
    dibujar();
    if (lienzo) {
        new MutationObserver(dibujar).observe(document.documentElement, {
            attributes: true, attributeFilter: ['data-bs-theme'],
        });
    }
    if (!editar) return;
    mostrarOrden();
    editar.addEventListener('click', () => {
        estado.textContent = '';
        activarEditor(true);
    });
    herramientas.querySelector('[data-dashboard-save]').addEventListener('click', guardar);
    herramientas.querySelector('[data-dashboard-reset]').addEventListener('click', () => {
        restaurar({ distribucion: inicial, grafico: 'line' });
        estado.textContent = opciones.moved;
    });
    herramientas.querySelector('[data-dashboard-cancel]').addEventListener('click', () => {
        restaurar(guardado);
        estado.textContent = '';
        activarEditor(false);
    });
    panel.addEventListener('click', evento => {
        const boton = evento.target.closest('[data-dashboard-step]');
        if (!boton || !editando || guardando || boton.getAttribute('aria-disabled') === 'true') return;
        const tarjeta = boton.closest('[data-dashboard-card]');
        const avanza = boton.dataset.dashboardStep === '1';
        mover(tarjeta, avanza ? tarjeta.nextElementSibling : tarjeta.previousElementSibling, avanza);
        boton.focus({ preventScroll: true });
    });
    panel.addEventListener('dragstart', evento => {
        const agarre = evento.target.closest('[data-dashboard-grip]');
        if (!agarre || !editando || guardando) return;
        arrastrado = agarre.closest('[data-dashboard-card]');
        evento.dataTransfer.setData('text/plain', arrastrado.dataset.dashboardCard);
        evento.dataTransfer.effectAllowed = 'move';
        evento.dataTransfer.setDragImage(arrastrado, 20, 20);
        arrastrado.setAttribute('data-dragging', '');
    });
    function marcarDestino(destino = null) {
        panel.querySelector('[data-drop-target]')?.removeAttribute('data-drop-target');
        destino?.setAttribute('data-drop-target', '');
    }
    panel.addEventListener('dragover', evento => {
        const destino = evento.target.closest('[data-dashboard-card]');
        if (!arrastrado || !destino || destino === arrastrado || destino.parentElement !== arrastrado.parentElement) {
            marcarDestino();
            return;
        }
        evento.preventDefault();
        evento.dataTransfer.dropEffect = 'move';
        marcarDestino(destino);
    });
    panel.addEventListener('drop', evento => {
        const destino = panel.querySelector('[data-drop-target]');
        if (!arrastrado || !destino || !destino.contains(evento.target)) return;
        evento.preventDefault();
        const despues = Boolean(arrastrado.compareDocumentPosition(destino) & Node.DOCUMENT_POSITION_FOLLOWING);
        mover(arrastrado, destino, despues);
        terminarArrastre();
    });
    function terminarArrastre() {
        arrastrado?.removeAttribute('data-dragging');
        arrastrado = null;
        marcarDestino();
    }
    panel.addEventListener('dragend', terminarArrastre);
})();
