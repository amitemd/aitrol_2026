(() => {
    'use strict';

    document.addEventListener('click', (event) => {
        const boton = event.target.closest('[data-comercial-agregar], [data-comercial-quitar]');
        const formulario = boton?.closest('[data-comercial-documento]');
        if (!formulario) return;

        const contenedor = formulario.querySelector('[data-comercial-lineas]');
        if (boton.hasAttribute('data-comercial-agregar')) {
            if (contenedor.children.length >= 100) return;

            const plantilla = formulario.querySelector('[data-comercial-plantilla]');
            const indice = Number(formulario.dataset.siguienteLinea ?? contenedor.children.length);
            formulario.dataset.siguienteLinea = String(indice + 1);
            const linea = plantilla.content.cloneNode(true);
            for (const campo of linea.querySelectorAll('[id], [for], [name]')) {
                for (const atributo of ['id', 'for', 'name']) {
                    const valor = campo.getAttribute(atributo);
                    if (valor) campo.setAttribute(atributo, valor.replace('__INDICE__', indice));
                }
            }
            contenedor.append(linea);
            contenedor.lastElementChild.querySelector('input')?.focus();
        } else {
            if (contenedor.children.length === 1) return;

            formulario.dataset.siguienteLinea ??= String(contenedor.children.length);
            boton.closest('[data-comercial-linea]').remove();
            formulario.querySelector('[data-comercial-agregar]').focus();
        }

        Array.from(contenedor.children).forEach((linea, indice) => {
            for (const campo of linea.querySelectorAll('[name]')) {
                campo.name = campo.name.replace(/^lineas\[\d+\]/, 'lineas[' + indice + ']');
            }
        });
    });
})();
