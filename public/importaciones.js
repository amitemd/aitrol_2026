(() => {
    const carga = document.querySelector('[data-importacion-carga]');
    if (carga) {
        const boton = carga.querySelector('button[type="submit"]');
        const estadoCarga = carga.querySelector('[data-importacion-carga-estado]');

        carga.addEventListener('submit', () => {
            boton.disabled = true;
            estadoCarga.hidden = false;
        });
        window.addEventListener('pageshow', () => {
            boton.disabled = false;
            estadoCarga.hidden = true;
        });
    }

    const formulario = document.querySelector('[data-personas-token]');
    formulario?.addEventListener('submit', async (evento) => {
        evento.preventDefault();
        const boton = formulario.querySelector('button[type="submit"]');
        const error = formulario.querySelector('[data-personas-token-error]');
        const resultado = formulario.querySelector('[data-personas-token-resultado]');
        const campo = resultado.querySelector('input');
        boton.disabled = true;
        error.hidden = true;

        try {
            const respuesta = await fetch(formulario.action, {
                method: 'POST',
                body: new FormData(formulario),
                headers: { Accept: 'application/json' },
                cache: 'no-store',
                redirect: 'error',
            });
            const datos = await respuesta.json();
            if (!respuesta.ok) {
                throw new Error(datos.errors?.nombre?.[0] ?? datos.message);
            }

            campo.value = datos.token;
            resultado.hidden = false;
            campo.focus();
            campo.select();
        } catch (fallo) {
            error.textContent = fallo.message || 'No se pudo generar el token. Vuelve a intentarlo.';
            error.hidden = false;
        } finally {
            boton.disabled = false;
        }
    });

    const estado = document.querySelector('[data-importacion-estado]');
    if (!estado) {
        return;
    }
    let ultimoEstado = estado.innerHTML.trim();

    async function actualizar() {
        if (!estado.isConnected || !estado.querySelector('[data-importaciones-activas]')) {
            return;
        }

        if (!document.hidden) {
            try {
                const response = await fetch(estado.dataset.importacionEstado, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    cache: 'no-store',
                    redirect: 'error',
                });
                if (response.ok) {
                    const contenido = (await response.text()).trim();
                    if (contenido !== ultimoEstado) {
                        estado.innerHTML = contenido;
                        ultimoEstado = contenido;
                    }
                }
            } catch {
                // El historial visible se conserva hasta la siguiente consulta.
            }
        }

        setTimeout(actualizar, 2000);
    }

    setTimeout(actualizar, 2000);
})();
