const showcase = document.querySelector('[data-auth-showcase]');
const slides = [...showcase.querySelectorAll('[data-showcase-slide]')];
const selectors = [...showcase.querySelectorAll('[data-showcase-select]')];
const controls = showcase.querySelector('[data-showcase-controls]');
const playback = showcase.querySelector('[data-showcase-playback]');
const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
const canvas = showcase.querySelector('canvas');
const context = canvas.getContext('2d');

const points = Array.from({ length: 19 }, () => {
    const angle = Math.random() * Math.PI * 2;
    const speed = .000035 + Math.random() * .00003;

    return {
        x: Math.random(),
        y: Math.random(),
        dx: Math.cos(angle) * speed,
        dy: Math.sin(angle) * speed,
    };
});

let current = 0;
let paused = false;
let rotation;
let animation;
let previousFrame;
let width;
let height;

function show(index) {
    current = index % slides.length;
    slides.forEach((slide, position) => {
        const active = position === current;
        slide.classList.toggle('is-active', active);
        slide.setAttribute('aria-hidden', String(!active));
        selectors[position].setAttribute('aria-current', String(active));
    });
}

function drawNetwork() {
    context.clearRect(0, 0, width, height);
    const range = Math.min(width, height) * .48;
    const pointPositions = points.map(point => ({ x: point.x * width, y: point.y * height }));

    for (let index = 0; index < pointPositions.length; index++) {
        const from = pointPositions[index];

        for (const to of pointPositions.slice(index + 1)) {
            const distance = Math.hypot(to.x - from.x, to.y - from.y);
            if (distance >= range) continue;

            context.beginPath();
            context.moveTo(from.x, from.y);
            context.lineTo(to.x, to.y);
            context.strokeStyle = 'rgba(196, 222, 255, ' + .32 * (1 - distance / range) + ')';
            context.stroke();
        }

        context.beginPath();
        context.arc(from.x, from.y, 1.35, 0, Math.PI * 2);
        context.fillStyle = 'rgba(213, 233, 255, .5)';
        context.fill();
    }
}

function resizeNetwork() {
    const bounds = canvas.getBoundingClientRect();
    width = bounds.width;
    height = bounds.height;
    const ratio = Math.min(window.devicePixelRatio || 1, 2);
    canvas.width = Math.round(width * ratio);
    canvas.height = Math.round(height * ratio);
    context.setTransform(ratio, 0, 0, ratio, 0, 0);
    drawNetwork();
}

function animateNetwork(time) {
    const elapsed = Math.min(time - (previousFrame ?? time), 40);
    previousFrame = time;

    for (const point of points) {
        point.x = Math.min(1, Math.max(0, point.x + point.dx * elapsed));
        point.y = Math.min(1, Math.max(0, point.y + point.dy * elapsed));
        if (point.x === 0 || point.x === 1) point.dx *= -1;
        if (point.y === 0 || point.y === 1) point.dy *= -1;
    }

    drawNetwork();
    animation = window.requestAnimationFrame(animateNetwork);
}

function updatePlayback() {
    window.clearInterval(rotation);
    window.cancelAnimationFrame(animation);
    previousFrame = undefined;
    playback.hidden = reducedMotion.matches;
    playback.textContent = paused ? 'Reanudar' : 'Pausar';

    if (paused || reducedMotion.matches || document.hidden) return;

    rotation = window.setInterval(() => show(current + 1), 7000);
    if (context) animation = window.requestAnimationFrame(animateNetwork);
}

controls.hidden = false;
selectors.forEach((selector, index) => {
    selector.addEventListener('click', () => {
        show(index);
        updatePlayback();
    });
});
playback.addEventListener('click', () => {
    paused = !paused;
    updatePlayback();
});
reducedMotion.addEventListener('change', updatePlayback);
document.addEventListener('visibilitychange', updatePlayback);

if (context) {
    resizeNetwork();
    window.addEventListener('resize', resizeNetwork, { passive: true });
}

updatePlayback();
