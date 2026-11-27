(() => {
    'use strict';

    const confirmados = new WeakSet();
    const pendientes = new WeakSet();

    function presentar(opciones) {
        const dialogo = [...document.querySelectorAll('dialog[open]')].at(-1);
        const oscuro = (document.body.dataset.bsTheme || document.documentElement.dataset.bsTheme) === 'dark';

        return window.Swal.fire({
            target: dialogo || document.body,
            theme: oscuro ? 'dark' : 'light',
            heightAuto: false,
            buttonsStyling: false,
            customClass: {
                popup: 'aitrol-alerta',
                actions: 'gap-2',
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-secondary',
            },
            ...opciones,
        });
    }

    document.addEventListener('submit', async (evento) => {
        const formulario = evento.target;
        const mensaje = formulario.dataset?.accionConfirmar;
        if (!mensaje || confirmados.has(formulario)) return;

        evento.preventDefault();
        evento.stopImmediatePropagation();
        if (pendientes.has(formulario)) return;

        pendientes.add(formulario);
        const boton = evento.submitter;
        formulario.closest('[popover]')?.hidePopover();
        try {
            const resultado = window.Swal
                ? await presentar({
                    icon: 'warning',
                    titleText: formulario.dataset.confirmarTitulo || 'Confirmar acción',
                    text: mensaje,
                    showCancelButton: true,
                    confirmButtonText: formulario.dataset.confirmarBoton || 'Confirmar',
                    cancelButtonText: formulario.dataset.confirmarCancelar || 'Cancelar',
                    focusCancel: true,
                    reverseButtons: true,
                    allowOutsideClick: false,
                })
                : {isConfirmed: window.confirm(mensaje)};

            if (resultado.isConfirmed) {
                confirmados.add(formulario);
                formulario.requestSubmit(boton || undefined);
            }
        } finally {
            confirmados.delete(formulario);
            pendientes.delete(formulario);
        }
    }, true);

    document.addEventListener('DOMContentLoaded', async () => {
        const mensajes = [...document.querySelectorAll('[data-aitrol-mensaje]')];
        if (!window.Swal || !mensajes.length) return;

        const errores = mensajes.filter((mensaje) => mensaje.dataset.aitrolMensaje === 'error');
        const avisos = errores.length ? errores : mensajes;
        const tipo = avisos[0].dataset.aitrolMensaje;
        const titulos = {error: 'Revisa la información', warning: 'Atención', info: 'Información', success: 'Operación completada'};
        const textos = [...new Set(avisos.map((mensaje) => mensaje.textContent.trim()))];

        if (tipo === 'success') {
            document.querySelectorAll('.alert-success, .access-status').forEach((mensaje) => {
                if (textos.includes(mensaje.textContent.trim())) mensaje.hidden = true;
            });
        }

        await presentar({icon: tipo, titleText: titulos[tipo], text: textos.join('\n'), confirmButtonText: 'Aceptar'});
    });
})();
