(() => {
    const archivo = document.querySelector('[data-archivo-documentos]');
    const dialogo = archivo?.querySelector('[data-archivo-dialogo]');
    if (!dialogo) return;

    const titulo = dialogo.querySelector('#archivo-documentos-titulo');
    const contenido = dialogo.querySelector('[data-archivo-contenido]');

    archivo.addEventListener('click', (evento) => {
        const tarjeta = evento.target.closest('[data-archivo-abrir]');
        if (!tarjeta || dialogo.open) return;

        const detalle = tarjeta.nextElementSibling;
        titulo.textContent = tarjeta.dataset.archivoAbrir;
        contenido.replaceChildren(detalle.content.cloneNode(true));
        dialogo.showModal();
    });

    dialogo.addEventListener('close', () => contenido.replaceChildren());
})();
