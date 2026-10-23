(function (root) {
    'use strict';
    function col(n) { let text = ''; while (n > 0) { n--; text = String.fromCharCode(65 + n % 26) + text; n = Math.floor(n / 26); } return text; }
    function point(address) { const m = /^([A-Z]{1,3})([1-9][0-9]{0,6})$/.exec(address); if (!m) throw Error('#REF!'); return [m[1].split('').reduce(function (n, c) { return n * 26 + c.charCodeAt(0) - 64; }, 0), Number(m[2])]; }
    function calculate(sheet, address, trail, memo) {
        trail = trail || new Set(); memo = memo || new Map();
        if (memo.has(address)) return memo.get(address);
        if (trail.has(address) || trail.size > 100) throw Error('#CICLO!');
        const position = point(address); if (position[0] > sheet.cols || position[1] > sheet.rows) throw Error('#REF!');
        const value = sheet.cells[address] ? sheet.cells[address].value : '';
        if (!value.startsWith('=')) return value.trim() !== '' && Number.isFinite(Number(value)) ? Number(value) : value;
        trail.add(address);
        try {
            const result = parse(value.slice(1), function (ref) { return calculate(sheet, ref, trail, memo); });
            if (!Number.isFinite(result)) throw Error('#DIV/0!');
            memo.set(address, result); return result;
        } finally { trail.delete(address); }
    }
    function parse(source, resolve) {
        if (source.length > 300) throw Error('#FÓRMULA!');
        source = source.toUpperCase().replace(/\$/g, '').replace(/;/g, ',');
        const tokens = []; let i = 0;
        while (i < source.length) {
            if (/\s/.test(source[i])) { i++; continue; }
            const m = /^(?:\d+(?:\.\d*)?|\.\d+|[A-Z]+\d*|[+\-*/^():,])/.exec(source.slice(i));
            if (!m) throw Error('#FÓRMULA!'); tokens.push(m[0]); i += m[0].length;
        }
        let p = 0;
        function number(value) { if (typeof value === 'number') return value; if (value === '') return 0; throw Error('#VALOR!'); }
        function atom() {
            const t = tokens[p++];
            if (t === '+') return atom(); if (t === '-') return -atom();
            if (t === '(') { const value = expression(); if (tokens[p++] !== ')') throw Error('#FÓRMULA!'); return value; }
            if (/^(?:\d+(?:\.\d*)?|\.\d+)$/.test(t || '')) return Number(t);
            if (/^[A-Z]{1,3}[1-9][0-9]{0,6}$/.test(t || '')) return number(resolve(t));
            const functions = {SUMA: 'SUM', PROMEDIO: 'AVERAGE', CONTAR: 'COUNT', SUM: 'SUM', AVERAGE: 'AVERAGE', COUNT: 'COUNT', MIN: 'MIN', MAX: 'MAX'};
            if (!functions[t] || tokens[p++] !== '(') throw Error('#FÓRMULA!');
            const values = [];
            if (tokens[p] !== ')') do {
                if (/^[A-Z]{1,3}[1-9][0-9]{0,6}$/.test(tokens[p] || '') && tokens[p + 1] === ':') {
                    const start = point(tokens[p]); p += 2; const end = point(tokens[p++]);
                    if ((Math.abs(end[0] - start[0]) + 1) * (Math.abs(end[1] - start[1]) + 1) > 1000000) throw Error('#REF!');
                    for (let row = Math.min(start[1], end[1]); row <= Math.max(start[1], end[1]); row++) for (let c = Math.min(start[0], end[0]); c <= Math.max(start[0], end[0]); c++) { const value = resolve(col(c) + row); if (typeof value === 'number') values.push(value); }
                } else values.push(expression());
                if (tokens[p] !== ',') break; p++;
            } while (true);
            if (tokens[p++] !== ')') throw Error('#FÓRMULA!');
            const fn = functions[t], sum = values.reduce(function (a, b) { return a + b; }, 0);
            if (fn === 'COUNT') return values.length;
            if (fn === 'SUM') return sum;
            if (fn === 'AVERAGE') return values.length ? sum / values.length : NaN;
            return values.length ? (fn === 'MIN' ? values.reduce(function (a, b) { return Math.min(a, b); }, Infinity) : values.reduce(function (a, b) { return Math.max(a, b); }, -Infinity)) : 0;
        }
        function power() { const left = atom(); if (tokens[p] === '^') { p++; return Math.pow(left, power()); } return left; }
        function term() { let n = power(); while (tokens[p] === '*' || tokens[p] === '/') { const op = tokens[p++], right = power(); n = op === '*' ? n * right : n / right; } return n; }
        function expression() { let n = term(); while (tokens[p] === '+' || tokens[p] === '-') { const op = tokens[p++], right = term(); n = op === '+' ? n + right : n - right; } return n; }
        const result = expression(); if (p !== tokens.length) throw Error('#FÓRMULA!'); return result;
    }
    const api = {calculate: calculate, column: col, point: point};
    if (typeof module !== 'undefined' && module.exports) module.exports = api; else root.OfficeFormulas = api;
})(typeof window === 'undefined' ? this : window);
