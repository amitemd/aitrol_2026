(() => {
    'use strict';

    const entry = document.querySelector('[data-cliente-dialog-open]');
    const dialog = document.querySelector('[data-cliente-dialog]');
    if (!entry || !dialog || typeof dialog.showModal !== 'function') {
        return;
    }

    const form = dialog.querySelector('form');
    const feedback = dialog.querySelector('[data-cliente-errores]');
    const status = document.querySelector('[data-cliente-estado]');
    const list = document.querySelector('[data-clientes-listado]');
    const buttons = [...form.querySelectorAll('button')];
    let submitting = false;

    function resetErrors() {
        feedback.hidden = true;
        feedback.replaceChildren();
        form.querySelectorAll('.is-invalid').forEach((field) => {
            field.classList.remove('is-invalid');
            field.removeAttribute('aria-invalid');
        });
    }

    function showErrors(messages, fields = {}) {
        const errors = document.createElement('ul');
        errors.className = 'mb-0';
        messages.forEach((message) => {
            const item = document.createElement('li');
            item.textContent = message;
            errors.append(item);
        });
        feedback.replaceChildren(errors);
        feedback.hidden = false;

        for (const name of Object.keys(fields)) {
            const input = form.elements.namedItem(name);
            if (input instanceof HTMLElement) {
                input.classList.add('is-invalid');
                input.setAttribute('aria-invalid', 'true');
            }
        }
        form.querySelector('.is-invalid')?.focus();
    }

    entry.addEventListener('click', (event) => {
        if (event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;

        event.preventDefault();
        form.reset();
        resetErrors();
        dialog.showModal();
    });

    dialog.querySelectorAll('[data-cliente-dialog-close]').forEach((button) => {
        button.addEventListener('click', () => dialog.close());
    });
    dialog.addEventListener('cancel', (event) => {
        if (submitting) event.preventDefault();
    });

    async function refreshList() {
        const response = await fetch(location.href, { headers: { Accept: 'text/html' } });
        if (!response.ok) throw new Error();

        const html = new DOMParser().parseFromString(await response.text(), 'text/html');
        const latest = html.querySelector('[data-clientes-listado]');
        if (!latest) throw new Error();
        list.replaceChildren(...latest.childNodes);
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (submitting) return;

        submitting = true;
        resetErrors();
        buttons.forEach((button) => { button.disabled = true; });

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { Accept: 'application/json' },
                body: new FormData(form),
            });
            if ([401, 419].includes(response.status)) {
                showErrors(['Tu sesión venció. Vuelve a entrar para registrar el cliente.']);
                return;
            }
            const payload = await response.json();
            if (response.status === 422) {
                showErrors(Object.values(payload.errors).flat(), payload.errors);
                return;
            }
            if (response.status === 409) {
                showErrors([payload.message], { identificacion: true });
                return;
            }
            if (!response.ok) throw new Error();

            dialog.close();
            status.hidden = false;
            status.classList.remove('alert-warning');
            status.classList.add('alert-success');
            status.textContent = payload.message;
            try {
                await refreshList();
            } catch {
                status.classList.remove('alert-success');
                status.classList.add('alert-warning');
                status.textContent = 'El cliente se registró. Usa Buscar para actualizar el listado.';
            }
        } catch {
            showErrors(['No se pudo confirmar el registro. Revisa el listado antes de volver a intentar.']);
        } finally {
            submitting = false;
            buttons.forEach((button) => { button.disabled = false; });
        }
    });
})();