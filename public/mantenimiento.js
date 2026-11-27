(() => {
    'use strict';
    const panel = document.querySelector('[data-mantenimiento]');
    if (!panel) return;

    const aviso = panel.querySelector('[data-mantenimiento-error]');
    const tarjetas = [...panel.querySelectorAll('[data-mantenimiento-accion]')];
    const opciones = {
        pendiente: ['Pendiente', 'secondary'],
        ejecutando: ['En ejecución', 'primary'],
        correcto: ['Completado', 'success'],
        fallido: ['Fallido', 'danger'],
    };
    let enviando = false;
    let ocupado = false;
    let revision = 0;
    let temporizador;

    function actualizarBotones() {
        for (const tarjeta of tarjetas) {
            tarjeta.querySelector('button').disabled = enviando || ocupado || tarjeta.dataset.disponible !== '1';
        }
    }

    function mostrar(datos) {
        ocupado = datos.ocupado;
        for (const tarjeta of tarjetas) {
            const registro = datos.estados[tarjeta.dataset.mantenimientoAccion];
            const [estado, color] = opciones[registro?.estado] ?? ['Sin ejecutar', 'secondary'];
            const distintivo = tarjeta.querySelector('[data-mantenimiento-estado]');
            distintivo.className = 'badge text-bg-' + color;
            distintivo.textContent = estado;
            tarjeta.querySelector('[data-mantenimiento-detalle]').textContent = registro?.detalle ?? '';
            tarjeta.querySelector('[data-mantenimiento-fecha]').textContent = registro?.finalizado_at ?? registro?.iniciado_at ?? '';
        }
        actualizarBotones();
    }

    async function consultar() {
        const anterior = revision;
        try {
            const respuesta = await fetch(panel.dataset.estadoUrl, {headers: {Accept: 'application/json'}, cache: 'no-store'});
            if (!respuesta.ok) return;
            const datos = await respuesta.json();
            if (anterior === revision) mostrar(datos);
        } catch {
            // La siguiente consulta recupera el estado al restablecerse la conexión.
        } finally {
            clearTimeout(temporizador);
            temporizador = setTimeout(consultar, ocupado ? 1500 : 12000);
        }
    }

    panel.addEventListener('submit', async (evento) => {
        const formulario = evento.target;
        if (!formulario.matches('[data-mantenimiento-form]')) return;
        evento.preventDefault();
        if (enviando || ocupado) return;

        enviando = true;
        revision++;
        aviso.hidden = true;
        actualizarBotones();
        try {
            const respuesta = await fetch(formulario.action, {
                method: 'POST',
                headers: {Accept: 'application/json'},
                body: new FormData(formulario),
            });
            const datos = await respuesta.json();
            if (!respuesta.ok) throw new Error(datos.message || 'No se pudo iniciar la acción.');
            mostrar(datos);
        } catch (error) {
            aviso.textContent = error instanceof SyntaxError ? 'No se pudo iniciar la acción. Revisa el estado del servidor.' : error.message;
            aviso.hidden = false;
        } finally {
            revision++;
            enviando = false;
            actualizarBotones();
            clearTimeout(temporizador);
            consultar();
        }
    });

    consultar();
})();
