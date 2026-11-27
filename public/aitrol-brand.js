(() => {
    'use strict';

    const canvas = document.querySelector('[data-aitrol-mark]');
    if (!canvas || document.documentElement.hasAttribute('data-workspace-embedded')) return;

    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    const staticIcon = canvas.previousElementSibling;
    const reduceMotion = matchMedia('(prefers-reduced-motion: reduce)');
    const rays = [
        [0, -23, 2], [16, -17, 14], [24, -2, -5], [17, 15, 7],
        [1, 24, -11], [-17, 17, 4], [-24, 1, 11], [-16, -14, -15],
    ];
    let frame = 0;
    let lastPaint = -Infinity;
    let start;

    function draw(time) {
        const yaw = .72 * Math.sin(time + .35);
        const tilt = .55 * Math.sin(time * .83 + .5);
        const spin = time + .22 * Math.sin(time * 2);
        const expansion = 1 + .09 * Math.sin(time * 2 - .4);
        const points = rays.map(([x, y, z], index) => {
            const depth = z * Math.cos(yaw) - x * Math.sin(yaw);
            const side = x * Math.cos(yaw) + z * Math.sin(yaw);
            const rise = y * Math.cos(tilt) - depth * Math.sin(tilt);
            const near = y * Math.sin(tilt) + depth * Math.cos(tilt);
            const scale = 78 / (78 - near);
            const radius = scale * expansion;
            return {
                x: 36 + (side * Math.cos(spin) - rise * Math.sin(spin)) * radius,
                y: 36 + (side * Math.sin(spin) + rise * Math.cos(spin)) * radius,
                scale,
                hue: 174 + index * 6,
            };
        }).sort((a, b) => a.scale - b.scale);

        ctx.setTransform(canvas.width / 72, 0, 0, canvas.height / 72, 0, 0);
        ctx.clearRect(0, 0, 72, 72);
        ctx.lineCap = 'round';

        for (const { x, y, scale, hue } of points) {
            const color = `hsl(${hue} 93% ${Math.min(82, 47 + scale * 22)}%)`;
            const line = ctx.createLinearGradient(36, 36, x, y);
            line.addColorStop(0, '#edfffc');
            line.addColorStop(.42, color);
            line.addColorStop(1, color);
            ctx.strokeStyle = line;
            ctx.lineWidth = 2.2 * scale;
            ctx.beginPath();
            ctx.moveTo(36, 36);
            ctx.lineTo(x, y);
            ctx.stroke();
            ctx.fillStyle = color;
            ctx.beginPath();
            ctx.arc(x, y, 2.6 * scale, 0, 2 * Math.PI);
            ctx.fill();
        }

        const nucleus = ctx.createRadialGradient(35, 35, 0, 36, 36, 4.4);
        nucleus.addColorStop(0, '#ffffff');
        nucleus.addColorStop(.5, '#dbfff9');
        nucleus.addColorStop(1, '#63dfff');
        ctx.fillStyle = nucleus;
        ctx.beginPath();
        ctx.arc(36, 36, 4.05, 0, 2 * Math.PI);
        ctx.fill();
    }

    function animate(now) {
        start ??= now;
        if (now - lastPaint >= 32) {
            draw((now - start) / 760);
            lastPaint = now;
        }
        frame = requestAnimationFrame(animate);
    }

    function sync() {
        cancelAnimationFrame(frame);
        const enabled = !reduceMotion.matches && !document.hidden;
        if (enabled) {
            draw(0);
            start = undefined;
            lastPaint = -Infinity;
            frame = requestAnimationFrame(animate);
        }
        staticIcon.hidden = enabled;
        canvas.hidden = !enabled;
    }

    reduceMotion.addEventListener('change', sync);
    document.addEventListener('visibilitychange', sync);
    sync();
})();
