(function () {
    'use strict';
    const suite = window.OfficeSuite; if (!suite || suite.kind !== 'spreadsheet') return;
    const formulas = window.OfficeFormulas, grid = document.getElementById('sheet-grid'), formula = document.getElementById('sheet-formula'), tabs = document.getElementById('sheet-tabs');
    let active = 0, address = 'A1', renaming = false, rowStart = 1, colStart = 1;
    const pageRows = 50, pageCols = 12;
    const sheet = function () { return suite.data.sheets[active]; };
    suite.data.sheets.forEach(function (s) { if (Array.isArray(s.cells)) s.cells = {}; });
    function cell() { return Object.assign({value: '', bold: false, color: '#243b53', background: '#ffffff', align: 'left', format: 'general'}, sheet().cells[address] || {}); }
    function modify(values) { sheet().cells[address] = Object.assign({}, cell(), values); suite.changed(); }
    function formatted(key, memo) {
        try {
            const value = formulas.calculate(sheet(), key, new Set(), memo), model = sheet().cells[key] || {};
            if (typeof value !== 'number') return value;
            if (model.format === 'currency') return '$' + value.toFixed(2);
            if (model.format === 'number') return value.toFixed(2);
            if (model.format === 'percent') return (value * 100).toFixed(2) + '%';
            return Number(value.toPrecision(12)).toString();
        } catch (e) { return e.message.startsWith('#') ? e.message : '#FÓRMULA!'; }
    }
    function refresh() {
        const memo = new Map();
        grid.querySelectorAll('input[data-cell]').forEach(function (input) {
            const model = sheet().cells[input.dataset.cell] || {};
            if (input !== document.activeElement) input.value = formatted(input.dataset.cell, memo);
            input.style.fontWeight = model.bold ? '700' : '400'; input.style.color = model.color || '#243b53'; input.style.backgroundColor = model.background || '#ffffff'; input.style.textAlign = model.align || 'left';
            input.classList.toggle('sheet-selected', input.dataset.cell === address);
        });
    }
    function select(key) {
        address = key; document.getElementById('sheet-address').textContent = key; formula.value = cell().value;
        document.getElementById('sheet-bold').setAttribute('aria-pressed', String(Boolean(cell().bold)));
        ['color', 'background', 'align', 'format'].forEach(function (name) { document.getElementById('sheet-' + name).value = cell()[name]; });
    }
    function render() {
        const selected = formulas.point(address); rowStart = Math.floor((selected[1] - 1) / pageRows) * pageRows + 1; colStart = Math.floor((selected[0] - 1) / pageCols) * pageCols + 1;
        grid.replaceChildren();
        const head = document.createElement('thead'), row = document.createElement('tr'); row.appendChild(document.createElement('th'));
        for (let c = colStart; c <= Math.min(sheet().cols, colStart + pageCols - 1); c++) { const th = document.createElement('th'), label = document.createElement('span'); label.textContent = formulas.column(c); th.appendChild(label); th.scope = 'col'; row.appendChild(th); }
        head.appendChild(row); grid.appendChild(head); const body = document.createElement('tbody');
        for (let r = rowStart; r <= Math.min(sheet().rows, rowStart + pageRows - 1); r++) {
            const tr = document.createElement('tr'), th = document.createElement('th'); th.textContent = r; th.scope = 'row'; tr.appendChild(th);
            for (let c = colStart; c <= Math.min(sheet().cols, colStart + pageCols - 1); c++) {
                const td = document.createElement('td'), input = document.createElement('input'), key = formulas.column(c) + r;
                input.dataset.cell = key; input.setAttribute('aria-label', 'Celda ' + key); input.maxLength = 2000; input.autocomplete = 'off'; input.spellcheck = false; td.appendChild(input); tr.appendChild(td);
            }
            body.appendChild(tr);
        }
        grid.appendChild(body); tabs.replaceChildren();
        suite.data.sheets.forEach(function (s, index) { const tab = document.createElement('button'); tab.type = 'button'; tab.textContent = s.name; tab.setAttribute('role', 'tab'); tab.setAttribute('aria-selected', String(index === active)); tab.className = index === active ? 'active' : ''; tab.addEventListener('click', function () { active = index; address = 'A1'; rowStart = colStart = 1; render(); }); tabs.appendChild(tab); });
        document.getElementById('sheet-dimensions').textContent = 'Filas ' + rowStart + '–' + Math.min(sheet().rows, rowStart + pageRows - 1) + ' de ' + sheet().rows + ' · Columnas ' + formulas.column(colStart) + '–' + formulas.column(Math.min(sheet().cols, colStart + pageCols - 1)) + ' de ' + formulas.column(sheet().cols); select(address); refresh();
    }
    grid.addEventListener('focusin', function (event) { if (!event.target.dataset.cell) return; select(event.target.dataset.cell); event.target.value = cell().value; refresh(); });
    grid.addEventListener('input', function (event) { if (!event.target.dataset.cell) return; modify({value: event.target.value}); formula.value = event.target.value; });
    grid.addEventListener('focusout', function () { setTimeout(refresh, 0); });
    grid.addEventListener('keydown', function (event) {
        if (!event.target.dataset.cell) return;
        const p = formulas.point(address);
        if (event.key === 'Enter' || event.key === 'ArrowDown' || event.key === 'ArrowUp') {
            event.preventDefault(); p[1] = Math.min(sheet().rows, Math.max(1, p[1] + (event.key === 'ArrowUp' ? -1 : 1)));
            address = formulas.column(p[0]) + p[1]; if (p[1] < rowStart || p[1] >= rowStart + pageRows) render();
            const next = grid.querySelector('[data-cell="' + formulas.column(p[0]) + p[1] + '"]'); if (next) next.focus();
        }
    });
    grid.addEventListener('paste', function (event) {
        if (!event.target.dataset.cell) return;
        const text = event.clipboardData.getData('text/plain'); if (!/[\t\n]/.test(text)) return;
        event.preventDefault(); const start = formulas.point(address), values = text.replace(/\r/g, '').replace(/\n$/, '').split('\n').map(function (r) { return r.split('\t'); });
        const cols = Math.max.apply(null, values.map(function (r) { return r.length; }));
        if (start[1] + values.length - 1 > 1048576 || start[0] + cols - 1 > 16384) { suite.error('El bloque supera el máximo de 1.048.576 filas o 16.384 columnas.'); return; }
        const keys = new Set(Object.keys(sheet().cells));
        values.forEach(function (r, y) { r.forEach(function (v, x) { keys.add(formulas.column(start[0] + x) + (start[1] + y)); }); });
        if (keys.size > 100000 || values.some(function (r) { return r.some(function (v) { return v.length > 2000; }); })) { suite.error('La hoja admite 100.000 celdas con datos, de hasta 2.000 caracteres cada una.'); return; }
        sheet().rows = Math.max(sheet().rows, start[1] + values.length - 1); sheet().cols = Math.max(sheet().cols, start[0] + cols - 1);
        values.forEach(function (r, y) { r.forEach(function (v, x) { const key = formulas.column(start[0] + x) + (start[1] + y); sheet().cells[key] = Object.assign({}, sheet().cells[key] || {}, {value: v}); }); });
        suite.changed(); render();
    });
    formula.addEventListener('input', function () { modify({value: formula.value}); refresh(); });
    formula.addEventListener('keydown', function (event) { if (event.key === 'Enter') { event.preventDefault(); refresh(); grid.querySelector('[data-cell="' + address + '"]').focus(); } });
    document.getElementById('sheet-bold').addEventListener('click', function () { modify({bold: !cell().bold}); select(address); refresh(); });
    ['color', 'background', 'align', 'format'].forEach(function (name) { document.getElementById('sheet-' + name).addEventListener('change', function (event) { const value = {}; value[name] = event.target.value; modify(value); refresh(); }); });
    document.getElementById('sheet-add-row').addEventListener('click', function () { if (sheet().rows >= 1048576) { suite.error('Máximo: 1.048.576 filas por hoja.'); return; } sheet().rows = Math.min(1048576, sheet().rows + 20); suite.changed(); render(); });
    document.getElementById('sheet-add-column').addEventListener('click', function () { if (sheet().cols >= 16384) { suite.error('Máximo: 16.384 columnas por hoja.'); return; } sheet().cols++; suite.changed(); render(); });
    const dialog = document.getElementById('sheet-name-dialog');
    document.getElementById('sheet-add').addEventListener('click', function () { if (suite.data.sheets.length >= 20) { suite.error('Máximo: 20 hojas.'); return; } renaming = false; document.getElementById('sheet-name').value = 'Hoja ' + (suite.data.sheets.length + 1); dialog.showModal(); });
    document.getElementById('sheet-rename').addEventListener('click', function () { renaming = true; document.getElementById('sheet-name').value = sheet().name; dialog.showModal(); });
    dialog.addEventListener('close', function () {
        if (dialog.returnValue !== 'save') return;
        const name = document.getElementById('sheet-name').value.trim();
        if (!name || /[\\/\[\]:*?]/.test(name) || suite.data.sheets.some(function (s, i) { return (!renaming || i !== active) && s.name.toLowerCase() === name.toLowerCase(); })) { suite.error('Escribe un nombre único sin / \\ [ ] : * ?'); return; }
        if (renaming) sheet().name = name; else { suite.data.sheets.push({name: name, rows: 40, cols: 12, cells: {}}); active = suite.data.sheets.length - 1; address = 'A1'; }
        suite.changed(); render();
    });
    document.getElementById('sheet-delete').addEventListener('click', function () { if (suite.data.sheets.length === 1) { suite.error('El libro debe conservar al menos una hoja.'); return; } if (!window.confirm('¿Eliminar esta hoja y sus celdas?')) return; suite.data.sheets.splice(active, 1); active = Math.max(0, active - 1); address = 'A1'; suite.changed(); render(); });
    const pager = document.createElement('div'); pager.className = 'suite-toolbar'; pager.setAttribute('aria-label', 'Navegar por la hoja');
    function moveTo(c, r) { address = formulas.column(Math.min(sheet().cols, Math.max(1, c))) + Math.min(sheet().rows, Math.max(1, r)); render(); }
    [['← Columnas', -pageCols, 0], ['Columnas →', pageCols, 0], ['↑ Filas', 0, -pageRows], ['Filas ↓', 0, pageRows]].forEach(function (item) { const b = document.createElement('button'); b.type = 'button'; b.className = 'office-button'; b.textContent = item[0]; b.addEventListener('click', function () { moveTo(colStart + item[1], rowStart + item[2]); }); pager.appendChild(b); });
    const jump = document.createElement('input'); jump.placeholder = 'Ir a celda, ej. A1500'; jump.setAttribute('aria-label', 'Dirección de celda');
    function go() { try { const p = formulas.point(jump.value.trim().toUpperCase()); if (p[0] > sheet().cols || p[1] > sheet().rows) throw Error(); moveTo(p[0], p[1]); } catch (e) { suite.error('Escribe una celda dentro de la hoja.'); } }
    jump.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); go(); } }); pager.appendChild(jump);
    const goButton = document.createElement('button'); goButton.type = 'button'; goButton.className = 'office-button'; goButton.textContent = 'Ir'; goButton.addEventListener('click', go); pager.appendChild(goButton);
    grid.closest('.sheet-scroll').before(pager);
    render();
})();
