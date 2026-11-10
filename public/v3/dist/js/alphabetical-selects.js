(function (window, document) {
    'use strict';

    var catalogField = /(seguro|insurance|convenio|doctor|medico|m[eé]dico|especialidad|specialty|procedimiento|procedure|paciente|patient|sala|room|consultorio|bodega|warehouse|usuario|user|responsable|proveedor|supplier|cliente|client|producto|product|marca|brand|categoria|category|subseguro|pa[ií]s|country|ciudad|city|provincia|province|empresa|company|empleado|employee|cargo|position|examen|exam|laboratorio|farmacia|f[aá]rmaco|medicina|medicamento|cuenta|account|servicio|service|preparaci[oó]n|preparation|asistente|assistant|enfermer|anestesi|caja|banco|bank|sucursal|branch|rol|role|plantilla|template|impuesto|tax|divisa|currency|forma.?pago|payment|centro.?costo|cost.?center|hospital|diagn[oó]stico|diagnosis|cie10)/i;
    var orderedField = /(^|[_-])(estado|status|fecha|date|hora|time|d[ií]a|day|mes|month|a[nñ]o|year|intervalo|duration|prioridad|priority|orden|order|cortes[ií]a|sexo|gender|modalidad|ingreso|action|acci[oó]n)([_-]|$)/i;
    var placeholder = /^(seleccione|seleccionar|todos?|todas?|ninguno|ninguna|sin\b|elija|buscar)/i;
    var pending = [];
    var scheduled = false;
    var signatures = typeof window.WeakMap === 'function' ? new WeakMap() : null;
    var collator = window.Intl && window.Intl.Collator
        ? new window.Intl.Collator('es', { sensitivity: 'base', numeric: true, ignorePunctuation: true })
        : null;

    function text(option) {
        return (option.textContent || option.innerText || '').replace(/\s+/g, ' ').trim();
    }

    function compare(first, second) {
        var firstText = text(first);
        var secondText = text(second);
        if (collator) return collator.compare(firstText, secondText);
        return firstText.toLocaleLowerCase().localeCompare(secondText.toLocaleLowerCase());
    }

    function compareSelect2(first, second) {
        var firstText = String(first && first.text ? first.text : '').trim();
        var secondText = String(second && second.text ? second.text : '').trim();
        var firstIsPlaceholder = !first || first.id === '' || first.disabled || placeholder.test(firstText);
        var secondIsPlaceholder = !second || second.id === '' || second.disabled || placeholder.test(secondText);
        if (firstIsPlaceholder !== secondIsPlaceholder) return firstIsPlaceholder ? -1 : 1;
        if (collator) return collator.compare(firstText, secondText);
        return firstText.toLocaleLowerCase().localeCompare(secondText.toLocaleLowerCase());
    }

    function identity(select) {
        var label = '';
        if (select.id) {
            var linkedLabel = document.querySelector('label[for="' + select.id.replace(/"/g, '\\"') + '"]');
            if (linkedLabel) label = linkedLabel.textContent || '';
        }
        if (!label && select.parentNode) {
            var nearbyLabel = select.parentNode.querySelector('label');
            if (nearbyLabel) label = nearbyLabel.textContent || '';
        }
        return [select.name || '', select.id || '', select.className || '', label].join(' ');
    }

    function looksSequential(options) {
        if (!options.length) return true;
        var sequential = 0;
        for (var index = 0; index < options.length; index += 1) {
            var label = text(options[index]);
            if (/^\d+(?:[.,:]\d+)?(?:\s*(?:minutos?|horas?|d[ií]as?|meses?|a[nñ]os?))?$/i.test(label) || /^\d{1,2}:\d{2}/.test(label)) sequential += 1;
        }
        return sequential === options.length;
    }

    function shouldSort(select) {
        var requested = (select.getAttribute('data-order') || '').toLowerCase();
        if (requested === 'preserve' || requested === 'manual') return false;
        if (select.classList && select.classList.contains('js-agenda-status')) return false;
        if (requested === 'alpha' || select.hasAttribute('data-alphabetical')) return true;

        var field = identity(select);
        if (catalogField.test(field)) return true;
        if (orderedField.test(field)) return false;

        var options = Array.prototype.slice.call(select.options).filter(function (option) {
            return option.value !== '' && !placeholder.test(text(option));
        });
        return options.length >= 4 && !looksSequential(options);
    }

    function optionSignature(select) {
        return Array.prototype.map.call(select.options, function (option) {
            return option.value + '\u0000' + text(option);
        }).join('\u0001');
    }

    function sortContainer(container) {
        var options = Array.prototype.slice.call(container.children).filter(function (child) {
            return child.tagName === 'OPTION';
        });
        if (options.length < 2) return;

        var fixed = options.filter(function (option) {
            return option.value === '' || option.disabled || placeholder.test(text(option));
        });
        var sortable = options.filter(function (option) {
            return fixed.indexOf(option) === -1;
        });
        sortable.sort(compare);

        var fragment = document.createDocumentFragment();
        fixed.forEach(function (option) { fragment.appendChild(option); });
        sortable.forEach(function (option) { fragment.appendChild(option); });
        container.appendChild(fragment);
    }

    function sortSelect(select) {
        if (!select || select.tagName !== 'SELECT' || !shouldSort(select)) return;
        var before = optionSignature(select);
        if (signatures && signatures.get(select) === before) return;

        // Al mover las opciones el navegador cambia temporalmente la selección
        // y termina dejando activa la última. Conservamos la selección real,
        // incluido el option vacío inicial o un valor asignado por JavaScript.
        var selectedOptions = Array.prototype.slice.call(select.options).filter(function (option) {
            return option.selected;
        });
        var hadNoSelection = select.selectedIndex === -1;

        var groups = Array.prototype.slice.call(select.children).filter(function (child) {
            return child.tagName === 'OPTGROUP';
        });
        if (groups.length) {
            groups.forEach(sortContainer);
        } else {
            sortContainer(select);
        }

        Array.prototype.forEach.call(select.options, function (option) {
            option.selected = selectedOptions.indexOf(option) !== -1;
        });
        if (hadNoSelection) select.selectedIndex = -1;

        if (signatures) signatures.set(select, optionSignature(select));
    }

    function flush() {
        scheduled = false;
        var items = pending.splice(0, pending.length);
        items.forEach(sortSelect);
    }

    function queue(select) {
        if (!select || select.tagName !== 'SELECT' || pending.indexOf(select) !== -1) return;
        pending.push(select);
        if (!scheduled) {
            scheduled = true;
            window.setTimeout(flush, 0);
        }
    }

    function scan(root) {
        if (!root) return;
        if (root.tagName === 'SELECT') queue(root);
        if (root.querySelectorAll) Array.prototype.forEach.call(root.querySelectorAll('select'), queue);
    }

    function installSelect2Hook() {
        var $ = window.jQuery;
        if (!$ || !$.fn || !$.fn.select2 || $.fn.select2.aitrolAlphabetical) return;
        var original = $.fn.select2;
        var wrapped = function (configuration) {
            if (typeof configuration === 'string') return original.apply(this, arguments);
            return this.each(function () {
                var config = configuration && typeof configuration === 'object' ? $.extend({}, configuration) : {};
                if (shouldSort(this) && typeof config.sorter !== 'function') {
                    config.sorter = function (items) { return items.slice().sort(compareSelect2); };
                }
                original.call($(this), config);
            });
        };
        Object.keys(original).forEach(function (key) { wrapped[key] = original[key]; });
        wrapped.aitrolAlphabetical = true;
        $.fn.select2 = wrapped;
    }

    function start() {
        scan(document);
        if (!window.MutationObserver) return;
        new window.MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                var owner = mutation.target.tagName === 'SELECT' ? mutation.target : mutation.target.closest && mutation.target.closest('select');
                if (owner) queue(owner);
                Array.prototype.forEach.call(mutation.addedNodes || [], scan);
            });
        }).observe(document.body, { childList: true, subtree: true });
    }

    window.AitrolV3SortSelects = scan;
    installSelect2Hook();
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start);
    else start();
})(window, document);
