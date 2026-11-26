(() => {
    'use strict';

    const list = document.querySelector('[data-ajax-list]');
    const dialog = document.querySelector('[data-ajax-dialog]');
    if (!list || !dialog || typeof dialog.showModal !== 'function') return;

    const status = document.querySelector('[data-ajax-status]');
    const title = dialog.querySelector('[data-ajax-title]');
    const body = dialog.querySelector('[data-ajax-body]');
    const errors = dialog.querySelector('[data-ajax-errors]');
    let listUrl = new URL(location.href);
    let listRequest;
    let formRequest;
    let saving = false;

    function notify(message, failure = false) {
        status.classList.toggle('alert-danger', failure);
        status.classList.toggle('alert-success', !failure);
        status.hidden = false;
        status.textContent = message;
    }

    function clearErrors(form) {
        errors.hidden = true;
        errors.replaceChildren();
        form?.querySelectorAll('[aria-invalid="true"]').forEach((input) => {
            input.removeAttribute('aria-invalid');
            input.classList.remove('is-invalid');
        });
    }

    function showErrors(messages, form, fields) {
        if (!dialog.open) {
            notify(messages.join(' '), true);
            return;
        }

        const details = document.createElement('ul');
        details.className = 'mb-0';
        for (const message of messages) {
            const item = document.createElement('li');
            item.textContent = message;
            details.append(item);
        }
        errors.replaceChildren(details);
        errors.hidden = false;
        for (const name of Object.keys(fields)) {
            const input = form.elements.namedItem(name);
            if (input instanceof HTMLElement) {
                input.classList.add('is-invalid');
                input.setAttribute('aria-invalid', 'true');
            }
        }
        form.querySelector('[aria-invalid="true"]')?.focus();
    }

    async function loadHtml(url, signal) {
        const response = await fetch(url, { headers: { Accept: 'text/html' }, signal });
        if (!response.ok) throw new Error('No se pudo cargar la información.');
        const page = new DOMParser().parseFromString(await response.text(), 'text/html');
        if (page.querySelector('[data-login-form]')) throw new Error('Tu sesión venció. Vuelve a iniciar sesión.');
        return page;
    }

    async function loadList(url, afterSave = false) {
        listRequest?.abort();
        const controller = new AbortController();
        listRequest = controller;
        list.setAttribute('aria-busy', 'true');
        try {
            const page = await loadHtml(url, controller.signal);
            const updated = page.querySelector('[data-ajax-list]');
            if (!updated) throw new Error('No se pudo cargar el listado.');
            if (controller.signal.aborted) return;
            list.replaceChildren(...updated.childNodes);
            listUrl = url;
            history.replaceState(history.state, '', url);
        } catch (error) {
            if (controller.signal.aborted) return;
            notify(afterSave ? 'El cambio se guardó. Usa Buscar para actualizar el listado.' : error.message, true);
        } finally {
            if (listRequest === controller) list.removeAttribute('aria-busy');
        }
    }

    async function openForm(link) {
        formRequest?.abort();
        const controller = new AbortController();
        formRequest = controller;
        title.textContent = link.dataset.ajaxOpen;
        clearErrors();
        body.textContent = 'Cargando formulario…';
        dialog.showModal();

        try {
            const page = await loadHtml(link.href, controller.signal);
            const form = page.querySelector('[data-ajax-form]');
            if (!form) throw new Error('No se pudo abrir el formulario.');
            if (controller.signal.aborted) return;
            form.querySelector(':scope > .card-header')?.remove();
            body.replaceChildren(form);
            (form.querySelector('[autofocus]') || form.querySelector('input:not([type="hidden"]), select'))?.focus();
        } catch (error) {
            if (!controller.signal.aborted) body.textContent = error.message;
        }
    }

    async function save(form) {
        if (saving) return;
        saving = true;
        clearErrors(form);
        const buttons = [...form.querySelectorAll('button'), dialog.querySelector('.btn-close')];
        buttons.forEach((button) => { button.disabled = true; });
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { Accept: 'application/json' },
                body: new FormData(form),
            });

            if ([401, 419].includes(response.status)) {
                showErrors(['Tu sesión venció. Vuelve a iniciar sesión.'], form, {});
                return;
            }

            const result = await response.json();
            if ([409, 422].includes(response.status)) {
                const fields = result.errors ?? {};
                const messages = Object.values(fields).flat();
                showErrors(messages.length ? messages : [result.message], form, fields);
                return;
            }
            if (response.status === 403) {
                showErrors(['Tu cuenta no tiene permiso para realizar esta acción.'], form, {});
                return;
            }
            if (!response.ok) throw new Error();

            if (dialog.open) dialog.close();
            notify(result.message);
            if (result.menuChanged) window.dispatchEvent(new Event('aitrol:menu-changed'));
            await loadList(listUrl, true);
        } catch {
            showErrors(['No se pudo confirmar el cambio. Revisa el listado antes de volver a intentar.'], form, {});
        } finally {
            buttons.forEach((button) => { button.disabled = false; });
            saving = false;
        }
    }

    document.addEventListener('click', (event) => {
        const close = event.target.closest('[data-ajax-close]');
        if (close && dialog.contains(close)) {
            event.preventDefault();
            if (!saving) dialog.close();
            return;
        }
        if (event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;

        const link = event.target.closest('a[data-ajax-open], a[data-ajax-list-link], .pagination a[href]');
        if (!link || link.origin !== location.origin) return;
        if (link.hasAttribute('data-ajax-open')) {
            event.preventDefault();
            if (!dialog.open && !saving) openForm(link);
        } else if (list.contains(link)) {
            event.preventDefault();
            loadList(new URL(link.href));
        }
    });

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (form.matches('[data-ajax-search]')) {
            event.preventDefault();
            const url = new URL(form.action);
            const search = new FormData(form).get('q')?.trim();
            if (search) url.searchParams.set('q', search);
            loadList(url);
        } else if ((dialog.contains(form) && form.matches('[data-ajax-form]')) || form.matches('[data-ajax-action]')) {
            event.preventDefault();
            save(form);
        }
    });

    dialog.addEventListener('cancel', (event) => {
        if (saving) event.preventDefault();
    });
    dialog.addEventListener('close', () => {
        formRequest?.abort();
        body.replaceChildren();
    });
})();