(() => {
    'use strict';

    const formato = new Intl.NumberFormat('es-EC', { maximumFractionDigits: 1 });

    function preparar(contenedor) {
        for (const tarjeta of contenedor.querySelectorAll('[data-ficha-adjunto]')) {
            const campo = tarjeta.querySelector('input[type="file"]');
            const seleccion = tarjeta.querySelector('[data-adjunto-seleccion]');
            const lista = tarjeta.querySelector('[data-adjunto-lista]');
            const vista = tarjeta.querySelector('[data-adjunto-vista]');
            const original = vista?.getAttribute('src');
            let temporal;

            function revocar() {
                if (temporal) URL.revokeObjectURL(temporal);
                temporal = null;
            }

            campo.addEventListener('change', () => {
                lista.replaceChildren();
                for (const archivo of campo.files) {
                    const fila = document.createElement('li');
                    const icono = document.createElement('i');
                    icono.className = 'bi ' + (/\.pdf$/i.test(archivo.name) ? 'bi-file-earmark-pdf' : 'bi-image');
                    icono.setAttribute('aria-hidden', 'true');
                    const nombre = document.createElement('span');
                    nombre.textContent = archivo.name;
                    const tamano = document.createElement('small');
                    const enMegabytes = archivo.size >= 1024 * 1024;
                    tamano.textContent = formato.format(archivo.size / (enMegabytes ? 1024 * 1024 : 1024))
                        + (enMegabytes ? ' MB' : ' KB');
                    fila.append(icono, nombre, tamano);
                    lista.append(fila);
                }

                seleccion.hidden = campo.files.length === 0;
                if (!vista) return;
                revocar();
                if (campo.files[0]?.type.startsWith('image/')) temporal = URL.createObjectURL(campo.files[0]);
                const fuente = temporal ?? original;
                if (fuente) vista.src = fuente;
                else vista.removeAttribute('src');
                vista.hidden = !fuente;
            });

            tarjeta.querySelector('[data-adjunto-limpiar]').addEventListener('click', () => {
                campo.value = '';
                campo.dispatchEvent(new Event('change', { bubbles: true }));
                campo.focus();
            });
            tarjeta.closest('dialog')?.addEventListener('close', revocar, { once: true });
        }
    }

    preparar(document);
    document.addEventListener('aitrol:form-ready', (evento) => preparar(evento.target));
})();
