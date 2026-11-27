(() => {
    'use strict';

    let activo;
    document.addEventListener('beforetoggle', (evento) => {
        const menu = evento.target;
        if (!menu.matches?.('[data-acciones-menu]')) return;
        if (evento.newState === 'closed') {
            if (activo === menu) activo = null;
            return;
        }
        activo = menu;
        const boton = document.querySelector('[popovertarget="' + menu.id + '"]');
        if (!boton) return;
        const ancla = boton.getBoundingClientRect();
        menu.style.top = ancla.bottom + 4 + 'px';
        menu.style.left = ancla.right + 'px';
        requestAnimationFrame(() => {
            if (!menu.matches(':popover-open')) return;
            const caja = menu.getBoundingClientRect();
            const izquierda = Math.max(8, Math.min(ancla.right - caja.width, innerWidth - caja.width - 8));
            const arriba = ancla.bottom + caja.height + 12 > innerHeight ? ancla.top - caja.height - 4 : ancla.bottom + 4;
            menu.style.left = izquierda + 'px';
            menu.style.top = Math.max(8, arriba) + 'px';
        });
    }, true);

    function cerrar() {
        if (activo?.matches(':popover-open')) activo.hidePopover();
    }

    document.addEventListener('click', (evento) => {
        if (evento.target.closest('[data-acciones-menu] a')) requestAnimationFrame(cerrar);
    });
    window.addEventListener('resize', cerrar);
    document.addEventListener('scroll', cerrar, true);
})();
