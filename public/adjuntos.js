(() => {
    'use strict';

    const formato = new Intl.NumberFormat('es-EC', { maximumFractionDigits: 1 });
    const formatosFoto = new Set(['image/jpeg', 'image/png', 'image/webp']);

    async function optimizarFoto(archivo, limite) {
        const foto = await createImageBitmap(archivo);
        try {
            const lienzo = document.createElement('canvas');
            const dibujo = lienzo.getContext('2d');
            for (const dimension of [1024, 768, 512]) {
                const escala = Math.min(1, dimension / Math.max(foto.width, foto.height));
                lienzo.width = Math.max(1, Math.round(foto.width * escala));
                lienzo.height = Math.max(1, Math.round(foto.height * escala));
                dibujo.drawImage(foto, 0, 0, lienzo.width, lienzo.height);
                const imagen = await new Promise((resolver) => lienzo.toBlob(resolver, 'image/webp', 0.9));
                if (imagen && ['image/webp', 'image/png'].includes(imagen.type) && imagen.size <= limite) {
                    const extension = imagen.type === 'image/webp' ? 'webp' : 'png';
                    return new File([imagen], archivo.name.replace(/\.[^.]+$/, '') + '.' + extension, { type: imagen.type });
                }
            }

            throw new Error('La foto supera el límite disponible.');
        } finally {
            foto.close();
        }
    }

    function preparar(contenedor) {
        for (const tarjeta of contenedor.querySelectorAll('[data-ficha-adjunto]')) {
            const campo = tarjeta.querySelector('input[type="file"]');
            const seleccion = tarjeta.querySelector('[data-adjunto-seleccion]');
            const lista = tarjeta.querySelector('[data-adjunto-lista]');
            const vista = tarjeta.querySelector('[data-adjunto-vista]');
            const estado = tarjeta.querySelector('[data-adjunto-estado]');
            const limite = Number(tarjeta.dataset.adjuntoLimite);
            const maximo = Number(tarjeta.dataset.adjuntoMaximo);
            const original = vista?.getAttribute('src');
            let temporal;
            let revision = 0;

            function informar(mensaje = '', error = false) {
                if (!estado) return;
                estado.hidden = !mensaje;
                estado.textContent = mensaje;
                estado.classList.toggle('text-danger', error);
            }

            function revocar() {
                if (temporal) URL.revokeObjectURL(temporal);
                temporal = null;
            }

            campo.addEventListener('change', async () => {
                const seleccionActual = ++revision;
                lista.replaceChildren();
                for (const archivo of campo.files) {
                    const fila = document.createElement('li');
                    const icono = document.createElement('i');
                    const iconoArchivo = /\.pdf$/i.test(archivo.name) ? 'bi-file-earmark-pdf'
                        : archivo.type.startsWith('image/') ? 'bi-image' : 'bi-file-earmark';
                    icono.className = 'bi ' + iconoArchivo;
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

                if (!limite) return;
                informar();
                campo.setCustomValidity('');
                const foto = campo.files[0];
                if (!foto) return;
                if (foto.type && !formatosFoto.has(foto.type)) {
                    const mensaje = 'La foto debe tener formato JPG, PNG o WebP.';
                    campo.setCustomValidity(mensaje);
                    informar(mensaje, true);
                    return;
                }
                if (foto.size > maximo) {
                    const mensaje = 'La foto debe pesar como máximo 3 MB.';
                    campo.setCustomValidity(mensaje);
                    informar(mensaje, true);
                    return;
                }
                if (foto.size <= limite) return;

                const preparando = 'Optimizando la foto…';
                campo.setCustomValidity(preparando);
                informar(preparando);
                try {
                    const optimizada = await optimizarFoto(foto, limite);
                    if (seleccionActual !== revision) return;
                    const archivos = new DataTransfer();
                    archivos.items.add(optimizada);
                    campo.files = archivos.files;
                    campo.dispatchEvent(new Event('change', { bubbles: true }));
                    informar('Foto optimizada. Ya puedes guardar.');
                } catch {
                    if (seleccionActual !== revision) return;
                    const mensaje = 'No se pudo optimizar la foto. Selecciona un JPG, PNG o WebP más ligero.';
                    campo.setCustomValidity(mensaje);
                    informar(mensaje, true);
                }
            });

            tarjeta.querySelector('[data-adjunto-limpiar]').addEventListener('click', () => {
                campo.value = '';
                campo.dispatchEvent(new Event('change', { bubbles: true }));
                campo.focus();
            });
            tarjeta.closest('dialog')?.addEventListener('close', () => {
                revision++;
                revocar();
            }, { once: true });
        }
    }

    preparar(document);
    document.addEventListener('aitrol:form-ready', (evento) => preparar(evento.target));
})();
