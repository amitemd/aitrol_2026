const navigation = document.querySelector('#navigation');
const mobileMenu = document.querySelector('.mobile-menu');
const navLinks = [...navigation.querySelectorAll('nav a')];

function setMenuOpen(isOpen) {
    mobileMenu.setAttribute('aria-expanded', String(isOpen));
    navigation.classList.toggle('is-open', isOpen);
}

mobileMenu.addEventListener('click', () => setMenuOpen(!navigation.classList.contains('is-open')));
navigation.addEventListener('click', (event) => {
    if (event.target.closest('a')) setMenuOpen(false);
});
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && navigation.classList.contains('is-open')) {
        setMenuOpen(false);
        mobileMenu.focus();
    }
});

const searchInput = document.querySelector('#section-search');
const searchResults = document.querySelector('#search-results');
const searchArea = searchInput.closest('.section-search');

function normalizeSearch(value) {
    return value.toLocaleLowerCase('es').normalize('NFD').replace(/\p{Diacritic}/gu, '');
}

function updateSearch() {
    const query = normalizeSearch(searchInput.value.trim());
    searchResults.replaceChildren();
    searchResults.hidden = !query;
    if (!query) return;

    const matches = navLinks.filter((link) => normalizeSearch(link.textContent).includes(query));
    if (!matches.length) {
        const message = document.createElement('li');
        message.setAttribute('role', 'status');
        message.textContent = 'Sin coincidencias';
        searchResults.append(message);
        return;
    }

    for (const link of matches) {
        const item = document.createElement('li');
        const result = document.createElement('a');
        result.href = link.href;
        result.textContent = link.textContent.trim();
        item.append(result);
        searchResults.append(item);
    }
}

searchInput.addEventListener('input', updateSearch);
searchInput.addEventListener('focus', updateSearch);
searchArea.addEventListener('focusout', (event) => {
    if (!searchArea.contains(event.relatedTarget)) searchResults.hidden = true;
});
searchResults.addEventListener('click', (event) => {
    if (event.target.closest('a')) searchResults.hidden = true;
});
searchArea.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape' || searchResults.hidden) return;
    event.stopPropagation();
    searchInput.focus();
    searchResults.hidden = true;
});
searchInput.addEventListener('keydown', (event) => {
    if (event.key !== 'Enter' && event.key !== 'ArrowDown') return;
    const firstResult = searchResults.querySelector('a');
    if (!firstResult || searchResults.hidden) return;
    event.preventDefault();
    if (event.key === 'Enter') firstResult.click();
    else firstResult.focus();
});

for (const trigger of document.querySelectorAll('[data-dialog-open]')) {
    trigger.addEventListener('click', () => document.getElementById(trigger.dataset.dialogOpen).showModal());
}

const agenda = document.querySelector('#agenda');
if (agenda) {
    const days = [...agenda.querySelectorAll('[data-day]')];
    const calendarDate = agenda.querySelector('#calendar-date');
    const todayEvents = agenda.querySelector('#today-events');
    const emptyAgenda = agenda.querySelector('#empty-agenda');

    for (const day of days) {
        day.addEventListener('click', () => {
            for (const option of days) option.setAttribute('aria-pressed', String(option === day));
            calendarDate.textContent = day.dataset.label;
            const isToday = day.dataset.day === agenda.dataset.today;
            todayEvents.hidden = !isToday;
            emptyAgenda.hidden = isToday;
        });
    }
}

const chart = document.querySelector('#patient-chart');
if (chart) {
    const chartData = {
        week: {
            name: 'semanal',
            labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
            attended: [985, 1640, 1450, 2090, 1910, 1410, 1110],
            admitted: [430, 800, 620, 1040, 840, 640, 460],
        },
        month: {
            name: 'mensual',
            labels: ['Sem. 1', 'Sem. 2', 'Sem. 3', 'Sem. 4'],
            attended: [1490, 1740, 2370, 2110],
            admitted: [720, 1030, 890, 1220],
        },
        year: {
            name: 'anual',
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            attended: [600, 1130, 930, 550, 890, 2280, 2640, 1310, 2160, 1890, 1580, 2500],
            admitted: [1350, 1020, 1180, 1870, 1280, 770, 1850, 1420, 630, 1310, 1640, 1190],
        },
    };
    const periods = [...document.querySelectorAll('[data-period]')];
    const chartLabels = document.querySelector('#chart-labels');
    const chartArea = chart.querySelector('#chart-area');
    const chartAttended = chart.querySelector('#chart-attended');
    const chartAdmitted = chart.querySelector('#chart-admitted');

    function seriesPath(values) {
        const points = values.map((value, index) => ({
            x: index * 750 / (values.length - 1),
            y: 221 - (value / 3000) * 220,
        }));
        let path = `M${points[0].x} ${points[0].y}`;
        for (let index = 1; index < points.length; index++) {
            const from = points[index - 1];
            const to = points[index];
            const midpoint = (from.x + to.x) / 2;
            path += ` C${midpoint} ${from.y} ${midpoint} ${to.y} ${to.x} ${to.y}`;
        }
        return path;
    }

    function renderChart(period) {
        const data = chartData[period];
        const attended = seriesPath(data.attended);
        chartArea.setAttribute('d', attended + ' L750 221 H0 Z');
        chartAttended.setAttribute('d', attended);
        chartAdmitted.setAttribute('d', seriesPath(data.admitted));
        chart.setAttribute('aria-label', `Evolución ${data.name} de pacientes. Atendidos: ${data.attended.join(', ')}. Hospitalizados: ${data.admitted.join(', ')}.`);
        chartLabels.classList.toggle('chart-labels--annual', period === 'year');
        chartLabels.replaceChildren(...data.labels.map((label) => {
            const element = document.createElement('span');
            element.textContent = label;
            return element;
        }));
        for (const option of periods) option.setAttribute('aria-pressed', String(option.dataset.period === period));
    }

    for (const option of periods) option.addEventListener('click', () => renderChart(option.dataset.period));
    renderChart('year');
}
