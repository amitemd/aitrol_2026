(() => {
    const archivo = document.querySelector('[data-archivo-documentos]');
    const dialogo = archivo?.querySelector('[data-archivo-dialogo]');
    if (!dialogo) return;

    const titulo = dialogo.querySelector('#archivo-documentos-titulo');
    const contenido = dialogo.querySelector('[data-archivo-contenido]');
    const logo = archivo.querySelector('[data-archivo-logo]');

    archivo.addEventListener('click', (evento) => {
        const tarjeta = evento.target.closest('[data-archivo-abrir]');
        if (!tarjeta || dialogo.open) return;

        const detalle = tarjeta.nextElementSibling;
        const hoja = detalle.content.cloneNode(true);
        if (logo) {
            hoja.querySelector('[data-archivo-emisor]')?.prepend(logo.content.cloneNode(true));
        }
        titulo.textContent = tarjeta.dataset.archivoAbrir;
        contenido.replaceChildren(hoja);
        dialogo.showModal();
    });

    dialogo.addEventListener('close', () => contenido.replaceChildren());
})();
