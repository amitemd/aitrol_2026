(() => {
    'use strict';

    function preparar(galeria) {
        const entrada = galeria.querySelector('[type="file"]');
        const zona = galeria.querySelector('[data-galeria-zona]');
        const lista = galeria.querySelector('[data-galeria-lista]');
        const orden = galeria.querySelector('[name="orden_fotos"]');
        const version = galeria.querySelector('[name="version_fotos"]');
        const estado = galeria.querySelector('[data-galeria-estado]');
        const contador = galeria.querySelector('[data-galeria-contador]');
        const maximo = 8;
        const formatos = ['image/jpeg', 'image/png', 'image/webp'];
        const fotos = JSON.parse(galeria.dataset.imagenes).map(foto => ({ ...foto, clave: 'e:' + foto.id }));
        let movida = null;
        let profundidad = 0;

        function informar(mensaje, error = false) {
            estado.hidden = !mensaje;
            estado.textContent = mensaje;
            estado.classList.toggle('text-danger', error);
        }

        function boton(etiqueta, icono, ejecutar, deshabilitado = false) {
            const boton = document.createElement('button');
            boton.type = 'button';
            boton.className = 'btn btn-sm btn-outline-secondary';
            boton.disabled = deshabilitado;
            boton.title = etiqueta;
            boton.setAttribute('aria-label', etiqueta);
            const dibujo = document.createElement('i');
            dibujo.className = 'bi ' + icono;
            dibujo.setAttribute('aria-hidden', 'true');
            boton.append(dibujo);
            boton.addEventListener('click', ejecutar);
            return boton;
        }

        function mover(desde, hasta) {
            if (desde === hasta || hasta < 0 || hasta >= fotos.length) return;
            fotos.splice(hasta, 0, fotos.splice(desde, 1)[0]);
            prepararEnvio();
            mostrar();
            informar('Nuevo orden preparado. Guarda el producto para aplicarlo.');
        }

        function mostrar() {
            contador.textContent = `${fotos.length} / ${maximo}`;
            lista.replaceChildren();
            fotos.forEach((foto, indice) => {
                const tarjeta = document.createElement('li');
                tarjeta.className = 'producto-galeria-tarjeta';
                tarjeta.draggable = true;
                const imagen = document.createElement('img');
                imagen.className = 'producto-galeria-foto';
                imagen.alt = `Imagen ${indice + 1}`;
                imagen.src = foto.url;
                imagen.draggable = false;
                tarjeta.append(imagen);
                if (indice === 0) {
                    const portada = document.createElement('span');
                    portada.className = 'badge text-bg-primary producto-galeria-portada';
                    portada.textContent = 'Portada';
                    tarjeta.append(portada);
                }
                const acciones = document.createElement('div');
                acciones.className = 'producto-galeria-acciones';
                acciones.append(
                    boton('Usar como portada', 'bi-star', () => mover(indice, 0), indice === 0),
                    boton('Mover antes', 'bi-arrow-left', () => mover(indice, indice - 1), indice === 0),
                    boton('Mover después', 'bi-arrow-right', () => mover(indice, indice + 1), indice === fotos.length - 1),
                    boton('Quitar imagen', 'bi-trash3', () => {
                        if (foto.archivo) URL.revokeObjectURL(foto.url);
                        fotos.splice(indice, 1);
                        prepararEnvio();
                        mostrar();
                        informar('Imagen retirada de la selección. Guarda para aplicar el cambio.');
                    }),
                );
                tarjeta.append(acciones);
                tarjeta.addEventListener('dragstart', evento => {
                    movida = indice;
                    evento.dataTransfer.setData('text/plain', String(indice));
                    evento.dataTransfer.effectAllowed = 'move';
                    tarjeta.classList.add('en-arrastre');
                });
                tarjeta.addEventListener('dragend', () => {
                    movida = null;
                    tarjeta.classList.remove('en-arrastre');
                });
                tarjeta.addEventListener('dragover', evento => {
                    if (movida === null) return;
                    evento.preventDefault();
                    evento.dataTransfer.dropEffect = 'move';
                    if (movida !== indice) tarjeta.classList.add('arrastra-sobre');
                });
                tarjeta.addEventListener('dragleave', evento => {
                    if (!tarjeta.contains(evento.relatedTarget)) tarjeta.classList.remove('arrastra-sobre');
                });
                tarjeta.addEventListener('drop', evento => {
                    evento.preventDefault();
                    if (movida !== null) mover(movida, indice);
                    tarjeta.classList.remove('arrastra-sobre');
                });
                lista.append(tarjeta);
            });
        }

        function prepararEnvio() {
            const archivos = new DataTransfer();
            orden.value = JSON.stringify(fotos.map(foto => {
                if (!foto.archivo) return foto.clave;
                const indice = archivos.items.length;
                archivos.items.add(foto.archivo);
                return 'n:' + indice;
            }));
            version.disabled = orden.disabled = false;
            entrada.files = archivos.files;
        }

        function agregar(archivos) {
            let rechazados = 0;
            for (const foto of archivos) {
                if (fotos.length >= maximo || !formatos.includes(foto.type) || foto.size === 0 || foto.size > 3 * 1024 * 1024) {
                    rechazados++;
                    continue;
                }
                fotos.push({ archivo: foto, url: URL.createObjectURL(foto) });
            }
            prepararEnvio();
            mostrar();
            informar(rechazados
                ? `Se omitieron ${rechazados} imágenes. Revisa el formato, el tamaño y el límite de la galería.`
                : 'Imágenes listas. Guarda el producto para subirlas.', rechazados > 0);
        }

        entrada.addEventListener('change', () => agregar([...entrada.files]));
        zona.addEventListener('dragenter', evento => {
            if (!evento.dataTransfer.types.includes('Files')) return;
            evento.preventDefault();
            profundidad++;
            zona.classList.add('arrastra-sobre');
        });
        zona.addEventListener('dragover', evento => {
            if (!evento.dataTransfer.types.includes('Files')) return;
            evento.preventDefault();
            evento.dataTransfer.dropEffect = 'copy';
        });
        zona.addEventListener('dragleave', () => {
            profundidad = Math.max(0, profundidad - 1);
            if (profundidad === 0) zona.classList.remove('arrastra-sobre');
        });
        zona.addEventListener('drop', evento => {
            evento.preventDefault();
            profundidad = 0;
            zona.classList.remove('arrastra-sobre');
            if (evento.dataTransfer.files.length) agregar([...evento.dataTransfer.files]);
        });
        galeria.closest('dialog')?.addEventListener('close', () => {
            for (const foto of fotos) if (foto.archivo) URL.revokeObjectURL(foto.url);
        }, { once: true });

        mostrar();
    }

    document.querySelectorAll('[data-galeria-producto]').forEach(preparar);
    document.addEventListener('aitrol:form-ready', evento => {
        const galeria = evento.target.querySelector('[data-galeria-producto]');
        if (galeria) preparar(galeria);
    });
})();
