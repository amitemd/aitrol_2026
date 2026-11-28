(() => {
    'use strict';

    document.addEventListener('click', (event) => {
        const boton = event.target.closest('[data-plan-expandir]');
        if (!boton) return;

        const cuenta = boton.closest('[data-cuenta-nivel]');
        const nivel = Number(cuenta.dataset.cuentaNivel);
        const abrir = boton.getAttribute('aria-expanded') === 'false';
        boton.setAttribute('aria-expanded', String(abrir));

        for (let fila = cuenta.nextElementSibling; fila && Number(fila.dataset.cuentaNivel) > nivel; fila = fila.nextElementSibling) {
            fila.hidden = !abrir || Number(fila.dataset.cuentaNivel) !== nivel + 1;
            fila.querySelector('[data-plan-expandir]')?.setAttribute('aria-expanded', 'false');
        }
    });
})();
