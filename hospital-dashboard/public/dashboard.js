const chart = document.querySelector('#activity-chart');
const series = JSON.parse(chart.dataset.series);
const months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

function drawChart() {
    const width = chart.clientWidth;
    const height = chart.clientHeight;
    if (!width || !height || !series.length) return;

    const scale = window.devicePixelRatio || 1;
    chart.width = width * scale;
    chart.height = height * scale;
    const context = chart.getContext('2d');
    context.scale(scale, scale);
    const left = 35;
    const right = width - 14;
    const top = 15;
    const bottom = height - 29;
    const max = Math.max(100, Math.ceil(Math.max(...series.map(row => row.total)) / 100) * 100);
    const x = index => left + (right - left) * index / Math.max(1, series.length - 1);
    const y = value => bottom - (bottom - top) * value / max;

    context.font = '11px system-ui';
    context.textBaseline = 'middle';
    context.fillStyle = '#506a65';
    for (let step = 0; step <= 4; step++) {
        const value = max * step / 4;
        context.strokeStyle = '#dcebe5';
        context.setLineDash([4, 4]);
        context.beginPath();
        context.moveTo(left, y(value));
        context.lineTo(right, y(value));
        context.stroke();
        context.fillText(value, 2, y(value));
    }

    context.textAlign = 'center';
    series.forEach((row, index) => {
        if (width > 450 || index % 2 === 0 || index === series.length - 1) {
            context.fillText(months[row.month - 1], x(index), bottom + 16);
        }
    });

    const gradient = context.createLinearGradient(0, top, 0, bottom);
    gradient.addColorStop(0, '#72c7ae45');
    gradient.addColorStop(1, '#edf8f500');
    context.beginPath();
    series.forEach((row, index) => {
        if (index === 0) context.moveTo(x(index), y(row.total));
        else context.lineTo(x(index), y(row.total));
    });
    context.lineTo(x(series.length - 1), bottom);
    context.lineTo(left, bottom);
    context.closePath();
    context.fillStyle = gradient;
    context.fill();

    context.setLineDash([]);
    context.lineWidth = 2.3;
    for (const [key, color] of [['total', '#164c43'], ['admitted', '#23886c']]) {
        context.strokeStyle = color;
        context.beginPath();
        series.forEach((row, index) => {
            if (index === 0) context.moveTo(x(index), y(row[key]));
            else context.lineTo(x(index), y(row[key]));
        });
        context.stroke();

        series.forEach((row, index) => {
            context.beginPath();
            context.arc(x(index), y(row[key]), 3, 0, Math.PI * 2);
            context.fillStyle = color;
            context.fill();
        });
    }
}

new ResizeObserver(drawChart).observe(chart);
const dialog = document.querySelector('#register-dialog');
document.querySelector('[data-register]').addEventListener('click', () => dialog.showModal());
document.querySelector('[data-close]').addEventListener('click', () => dialog.close());
if (dialog.dataset.open === '1') dialog.showModal();

const links = document.querySelectorAll('.side-link');
function selectSection() {
    const section = location.hash || '#contenido';
    links.forEach(link => {
        if (link.hash === section) link.setAttribute('aria-current', 'location');
        else link.removeAttribute('aria-current');
    });
    if (section === '#pacientes') document.querySelector('#pacientes').open = true;
}
window.addEventListener('hashchange', selectSection);
selectSection();
