(function (window, document) {
    'use strict';

    var options = window.AdminLTEOptions || {};
    options.enableControlTreeView = true;
    window.AdminLTEOptions = options;

    function normalizeText(value) {
        var text = String(value || '').toLocaleLowerCase('es').trim();

        if (typeof text.normalize === 'function') {
            text = text.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        }

        return text;
    }

    function directChild(element, selector) {
        var children = Array.prototype.slice.call(element.children);

        return children.find(function (child) {
            return child.matches(selector);
        }) || null;
    }

    function listItems(list) {
        return Array.prototype.slice.call(list.children).filter(function (child) {
            return child.tagName === 'LI';
        });
    }

    function resetList(list) {
        listItems(list).forEach(function (item) {
            var submenu = directChild(item, 'ul.treeview-menu');

            item.hidden = false;
            item.classList.remove('search-open');

            if (submenu) {
                submenu.style.removeProperty('display');
                submenu.classList.remove('menu-open');
                resetList(submenu);
            }
        });
    }

    function revealList(list) {
        listItems(list).forEach(function (item) {
            var submenu = directChild(item, 'ul.treeview-menu');

            item.hidden = false;
            item.classList.remove('search-open');

            if (submenu) {
                submenu.style.removeProperty('display');
                submenu.classList.remove('menu-open');
                revealList(submenu);
            }
        });
    }

    function filterList(list, term) {
        var listHasMatch = false;

        listItems(list).forEach(function (item) {
            if (item.classList.contains('header')) {
                item.hidden = true;

                return;
            }

            var link = directChild(item, 'a');
            var submenu = directChild(item, 'ul.treeview-menu');
            var ownMatch = link !== null && normalizeText(link.textContent).includes(term);
            var descendantMatch = submenu !== null && filterList(submenu, term);
            var itemMatches = ownMatch || descendantMatch;

            if (ownMatch && submenu) {
                revealList(submenu);
            }

            item.hidden = !itemMatches;

            if (submenu) {
                if (itemMatches) {
                    submenu.style.display = 'block';
                    submenu.classList.add('menu-open');
                    item.classList.add('search-open');
                } else {
                    submenu.style.removeProperty('display');
                    submenu.classList.remove('menu-open');
                    item.classList.remove('search-open');
                }
            }

            listHasMatch = listHasMatch || itemMatches;
        });

        return listHasMatch;
    }

    function updateTreeAccessibility(menu) {
        Array.prototype.slice.call(menu.querySelectorAll('li.treeview')).forEach(function (item, index) {
            var link = directChild(item, 'a');
            var submenu = directChild(item, 'ul.treeview-menu');

            if (!link || !submenu) {
                return;
            }

            if (!submenu.id) {
                submenu.id = 'new-system-submenu-' + index;
            }

            var isExpanded = item.classList.contains('active')
                || item.classList.contains('search-open')
                || submenu.classList.contains('menu-open')
                || submenu.style.display === 'block';

            link.setAttribute('role', 'button');
            link.setAttribute('aria-controls', submenu.id);
            link.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
        });
    }

    function createSearchStatus(search) {
        var status = document.getElementById('new-system-menu-search-status');

        if (status) {
            return status;
        }

        status = document.createElement('span');
        status.id = 'new-system-menu-search-status';
        status.className = 'sr-only';
        status.setAttribute('aria-live', 'polite');
        search.parentNode.appendChild(status);

        return status;
    }

    function initializeNavigation() {
        var menu = document.querySelector('.sidebar-menu');

        if (!menu) {
            return;
        }

        var search = document.getElementById('new-system-menu-search');
        var searchStatus = search ? createSearchStatus(search) : null;

        updateTreeAccessibility(menu);

        menu.addEventListener('click', function (event) {
            var link = event.target.closest('a');

            if (!link || link.parentElement === null || !link.parentElement.classList.contains('treeview')) {
                return;
            }

            window.setTimeout(function () {
                updateTreeAccessibility(menu);
            }, 550);
        });

        if (!search) {
            return;
        }

        search.setAttribute('aria-controls', 'new-system-sidebar-menu');
        menu.id = menu.id || 'new-system-sidebar-menu';

        search.addEventListener('input', function () {
            var term = normalizeText(search.value);

            if (term === '') {
                resetList(menu);
                searchStatus.textContent = '';
            } else {
                filterList(menu, term);

                var visibleItems = menu.querySelectorAll('li:not(.header):not([hidden])').length;
                searchStatus.textContent = visibleItems === 1
                    ? '1 opción encontrada.'
                    : visibleItems + ' opciones encontradas.';
            }

            updateTreeAccessibility(menu);
        });

        search.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') {
                return;
            }

            search.value = '';
            search.dispatchEvent(new Event('input'));
            search.focus();
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeNavigation);
    } else {
        initializeNavigation();
    }
}(window, document));
