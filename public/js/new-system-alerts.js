(function (window, document) {
    'use strict';

    var configuration = window.AitrolAlertConfiguration || {};
    var labels = configuration.labels || {};
    var messages = configuration.messages || {};

    function messageToText(message, visited) {
        if (message === null || typeof message === 'undefined') {
            return '';
        }

        if (typeof message === 'string' || typeof message === 'number' || typeof message === 'boolean') {
            return String(message).trim();
        }

        if (Array.isArray(message)) {
            return message
                .map(function (item) {
                    return messageToText(item, visited);
                })
                .filter(Boolean)
                .join('\n');
        }

        if (typeof message !== 'object') {
            return '';
        }

        var seen = visited || new WeakSet();

        if (seen.has(message)) {
            return '';
        }

        seen.add(message);

        var preferredKeys = ['message', 'error', 'detail', 'description', 'errors'];

        for (var index = 0; index < preferredKeys.length; index += 1) {
            var key = preferredKeys[index];

            if (Object.prototype.hasOwnProperty.call(message, key)) {
                var text = messageToText(message[key], seen);

                if (text) {
                    return text;
                }
            }
        }

        return '';
    }

    function normalizeOptions(options) {
        var normalized = typeof options === 'string' ? { text: options } : (options || {});

        return Object.assign({
            heightAuto: false,
            buttonsStyling: false,
            customClass: {
                popup: 'aitrol-swal-popup',
                title: 'aitrol-swal-title',
                content: 'aitrol-swal-content',
                htmlContainer: 'aitrol-swal-content',
                confirmButton: 'aitrol-swal-button aitrol-swal-button-primary',
                cancelButton: 'aitrol-swal-button aitrol-swal-button-secondary',
                actions: 'aitrol-swal-actions'
            }
        }, normalized);
    }

    function fire(options) {
        if (!window.Swal || typeof window.Swal.fire !== 'function') {
            window.console.error('SweetAlert2 no está disponible.', options);

            return Promise.resolve({ isConfirmed: false, isDismissed: true });
        }

        return window.Swal.fire(normalizeOptions(options));
    }

    function notification(icon, message, title) {
        var normalizedMessage = messageToText(message);

        return fire({
            icon: icon,
            title: title || labels[icon] || labels.information,
            text: normalizedMessage || labels.information || '',
            showCancelButton: false,
            showDenyButton: false,
            confirmButtonText: labels.confirm
        });
    }

    function confirmation(options) {
        var settings = typeof options === 'string' ? { text: options } : (options || {});

        return fire(Object.assign({
            icon: 'warning',
            title: labels.confirmation,
            showCancelButton: true,
            allowOutsideClick: false,
            returnFocus: true,
            focusCancel: true,
            reverseButtons: true,
            confirmButtonText: labels.confirm,
            cancelButtonText: labels.cancel
        }, settings));
    }

    var api = {
        fire: fire,
        success: function (message, title) {
            return notification('success', message, title);
        },
        error: function (message, title) {
            return notification('error', message, title);
        },
        warning: function (message, title) {
            return notification('warning', message, title);
        },
        info: function (message, title) {
            return notification('info', message, title);
        },
        confirm: confirmation
    };

    window.AitrolAlerts = api;
    window.alert = function (message) {
        return api.info(message);
    };

    function showFlashMessage() {
        var priorities = ['error', 'warning', 'success', 'info'];

        priorities.some(function (type) {
            var message = messageToText(messages[type]);

            if (!message) {
                return false;
            }

            notification(type, message, labels[type]);

            return true;
        });
    }

    function confirmFormSubmission(event) {
        var form = event.target.closest('form[data-swal-confirm]');

        if (!form || form.dataset.swalConfirmed === 'true') {
            return;
        }

        event.preventDefault();

        var submitter = event.submitter;
        confirmation({
            title: form.dataset.swalTitle || labels.confirmation,
            text: form.dataset.swalConfirm,
            confirmButtonText: form.dataset.swalConfirmButton || labels.confirm,
            cancelButtonText: form.dataset.swalCancelButton || labels.cancel
        }).then(function (result) {
            if (!result.isConfirmed) {
                return;
            }

            form.dataset.swalConfirmed = 'true';

            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit(submitter || undefined);

                return;
            }

            form.submit();
        });
    }

    function confirmLinkNavigation(event) {
        var link = event.target.closest('a[data-swal-confirm]');

        if (!link) {
            return;
        }

        event.preventDefault();

        confirmation({
            title: link.dataset.swalTitle || labels.confirmation,
            text: link.dataset.swalConfirm,
            confirmButtonText: link.dataset.swalConfirmButton || labels.confirm,
            cancelButtonText: link.dataset.swalCancelButton || labels.cancel
        }).then(function (result) {
            if (result.isConfirmed) {
                window.location.assign(link.href);
            }
        });
    }

    document.addEventListener('submit', confirmFormSubmission);
    document.addEventListener('click', confirmLinkNavigation);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', showFlashMessage, { once: true });
    } else {
        showFlashMessage();
    }
}(window, document));
