(function () {
    'use strict';

    var page = document.querySelector('[data-clinical-agenda]');

    if (!page) {
        return;
    }

    var form = page.querySelector('[data-directory-filters]');
    var grid = page.querySelector('[data-doctor-grid]');
    var count = page.querySelector('[data-doctor-count]');
    var timer = null;
    var requestController = null;
    var requestNumber = 0;

    if (!form || !grid || !count) {
        return;
    }

    function filterUrl() {
        var url = new URL(form.action, window.location.origin);

        new FormData(form).forEach(function (value, key) {
            var cleanValue = String(value).trim();

            if (cleanValue !== '') {
                url.searchParams.set(key, cleanValue);
            }
        });

        return url;
    }

    function updateDirectory() {
        var url = filterUrl();
        var currentRequest = ++requestNumber;

        if (requestController) {
            requestController.abort();
        }

        requestController = window.AbortController ? new AbortController() : null;
        grid.classList.add('is-loading');

        fetch(url.toString(), {
            credentials: 'same-origin',
            headers: {'Accept': 'text/html'},
            signal: requestController ? requestController.signal : undefined
        }).then(function (response) {
            if (!response.ok) {
                throw new Error('Unable to refresh the clinical directory.');
            }

            return response.text();
        }).then(function (html) {
            var nextPage;
            var nextGrid;
            var nextCount;

            if (currentRequest !== requestNumber) {
                return;
            }

            nextPage = new DOMParser().parseFromString(html, 'text/html');
            nextGrid = nextPage.querySelector('[data-doctor-grid]');
            nextCount = nextPage.querySelector('[data-doctor-count]');

            if (!nextGrid || !nextCount) {
                throw new Error('The clinical directory response is incomplete.');
            }

            grid.innerHTML = nextGrid.innerHTML;
            count.textContent = nextCount.textContent;
            window.history.replaceState({}, '', url.toString());
        }).catch(function (error) {
            if (error.name !== 'AbortError') {
                window.location.assign(url.toString());
            }
        }).then(function () {
            if (currentRequest === requestNumber) {
                grid.classList.remove('is-loading');
            }
        });
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        updateDirectory();
    });

    form.querySelector('[name="buscar"]').addEventListener('input', function () {
        window.clearTimeout(timer);
        timer = window.setTimeout(updateDirectory, 400);
    });

    form.querySelector('[name="especialidad"]').addEventListener('change', updateDirectory);
}());
