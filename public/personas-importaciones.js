(() => {
    const estado = document.querySelector('[data-personas-estado]');
    if (!estado) {
        return;
    }

    async function actualizar() {
        if (!estado.isConnected || !estado.querySelector('[data-importaciones-activas]')) {
            return;
        }

        if (!document.hidden) {
            try {
                const response = await fetch(estado.dataset.personasEstado, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    cache: 'no-store',
                    redirect: 'error',
                });
                if (response.ok) {
                    estado.innerHTML = await response.text();
                }
            } catch {
                // El historial visible se conserva hasta la siguiente consulta.
            }
        }

        setTimeout(actualizar, 3000);
    }

    setTimeout(actualizar, 3000);
})();
