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

    function normalizePath(value) {
        var path = String(value || '/').split('?')[0].split('#')[0];

        try {
            path = decodeURIComponent(path);
        } catch (error) {
            path = String(value || '/');
        }

        path = ('/' + path).replace(/\/{2,}/g, '/').replace(/\/$/, '');

        return path === '' ? '/' : path.toLocaleLowerCase('es');
    }

    function pathMatches(currentPath, targetPath) {
        return targetPath !== '/'
            && (currentPath === targetPath || currentPath.indexOf(targetPath + '/') === 0);
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

    function linkPath(link) {
        var href = link.getAttribute('href');

        if (!href || href === '#' || href.indexOf('javascript:') === 0) {
            return null;
        }

        try {
            var url = new window.URL(href, window.location.origin);

            if (url.origin !== window.location.origin) {
                return null;
            }

            return normalizePath(url.pathname);
        } catch (error) {
            return null;
        }
    }

    function routePrefixMatch(item, currentPath) {
        var prefixes = item.getAttribute('data-route-prefixes');

        if (!prefixes) {
            return 0;
        }

        return prefixes.split('|').reduce(function (longestMatch, prefix) {
            var normalizedPrefix = normalizePath(prefix.trim());

            return pathMatches(currentPath, normalizedPrefix)
                ? Math.max(longestMatch, normalizedPrefix.length)
                : longestMatch;
        }, 0);
    }

    function openCurrentRoute(item) {
        item.classList.add('active', 'current-menu-item');

        var currentLink = directChild(item, 'a');

        if (currentLink) {
            currentLink.setAttribute('aria-current', 'page');
        }

        var parent = item.parentElement;

        while (parent && parent.classList.contains('treeview-menu')) {
            parent.classList.add('menu-open');
            parent.style.display = 'block';

            var parentItem = parent.parentElement;

            if (!parentItem || parentItem.tagName !== 'LI') {
                break;
            }

            parentItem.classList.add('active', 'has-current-route');

            var parentLink = directChild(parentItem, 'a');

            if (parentLink) {
                parentLink.setAttribute('aria-expanded', 'true');
            }

            parent = parentItem.parentElement;
        }
    }

    function activateCurrentRoute(menu) {
        var currentPath = normalizePath(window.location.pathname);
        var bestItem = null;
        var bestMatchLength = 0;

        Array.prototype.slice.call(menu.querySelectorAll('li')).forEach(function (item) {
            var matchLength = routePrefixMatch(item, currentPath);
            var link = directChild(item, 'a');
            var targetPath = link ? linkPath(link) : null;

            if (targetPath && pathMatches(currentPath, targetPath)) {
                matchLength = Math.max(matchLength, targetPath.length);
            }

            if (matchLength > bestMatchLength) {
                bestItem = item;
                bestMatchLength = matchLength;
            }
        });

        if (bestItem) {
            openCurrentRoute(bestItem);
        }
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

        activateCurrentRoute(menu);
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
