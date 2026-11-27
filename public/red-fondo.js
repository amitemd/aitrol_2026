(() => {
    'use strict';

    const canvas = document.querySelector('[data-red-fondo]');
    const dibujo = canvas?.getContext('2d');
    if (!dibujo) return;

    const reducir = matchMedia('(prefers-reduced-motion: reduce)');
    let puntos = [];
    let ancho = 0;
    let alto = 0;
    let cuadro = 0;
    let anterior = 0;
    let visible = true;
    let color;

    function leerColor() {
        color = getComputedStyle(canvas).getPropertyValue('--aitrol-red-fondo').trim();
    }

    function ajustar() {
        const nuevoAncho = canvas.clientWidth;
        const nuevoAlto = canvas.clientHeight;
        if (nuevoAncho === ancho && nuevoAlto === alto) return;
        puntos.forEach((punto) => {
            if (ancho && alto) {
                punto.x *= nuevoAncho / ancho;
                punto.y *= nuevoAlto / alto;
            }
        });
        ancho = nuevoAncho;
        alto = nuevoAlto;
        const escala = Math.min(devicePixelRatio || 1, 1.5);
        canvas.width = Math.round(ancho * escala);
        canvas.height = Math.round(alto * escala);
        dibujo.setTransform(escala, 0, 0, escala, 0, 0);
        const cantidad = Math.min(62, Math.max(16, Math.round(ancho * alto / 20500)));
        while (puntos.length < cantidad && ancho && alto) {
            const angulo = Math.random() * Math.PI * 2;
            const velocidad = 4 + Math.random() * 5;
            puntos.push({ x: Math.random() * ancho, y: Math.random() * alto, vx: Math.cos(angulo) * velocidad, vy: Math.sin(angulo) * velocidad });
        }
        if (puntos.length > cantidad) puntos = puntos.slice(0, cantidad);
        activar();
    }

    function avanzar(tiempo) {
        puntos.forEach((punto) => {
            punto.x += punto.vx * tiempo;
            punto.y += punto.vy * tiempo;
            if (punto.x <= 0 || punto.x >= ancho) punto.vx *= -1;
            if (punto.y <= 0 || punto.y >= alto) punto.vy *= -1;
            punto.x = Math.max(0, Math.min(ancho, punto.x));
            punto.y = Math.max(0, Math.min(alto, punto.y));
        });
        dibujo.clearRect(0, 0, ancho, alto);
        dibujo.lineWidth = .7;
        const alcance = Math.min(156, Math.max(100, ancho / 5));
        for (let i = 0; i < puntos.length; i++) {
            const a = puntos[i];
            for (let j = i + 1; j < puntos.length; j++) {
                const b = puntos[j];
                const distancia = Math.hypot(a.x - b.x, a.y - b.y);
                if (distancia > alcance) continue;
                dibujo.beginPath();
                dibujo.moveTo(a.x, a.y);
                dibujo.lineTo(b.x, b.y);
                dibujo.strokeStyle = 'rgba(' + color + ',' + .18 * (1 - distancia / alcance) + ')';
                dibujo.stroke();
            }
            dibujo.beginPath();
            dibujo.arc(a.x, a.y, 1.25, 0, Math.PI * 2);
            dibujo.fillStyle = 'rgba(' + color + ',.35)';
            dibujo.fill();
        }
    }

    function animar(ahora) {
        if (!anterior || ahora - anterior >= 32) {
            avanzar(anterior ? Math.min(ahora - anterior, 80) / 1000 : 0);
            anterior = ahora;
        }
        cuadro = requestAnimationFrame(animar);
    }

    function activar() {
        if (reducir.matches || !visible || document.hidden || !ancho || !alto) {
            cancelAnimationFrame(cuadro);
            cuadro = 0;
            anterior = 0;
        } else if (!cuadro) {
            cuadro = requestAnimationFrame(animar);
        }
    }

    leerColor();
    new MutationObserver(leerColor).observe(document.documentElement, { attributes: true, attributeFilter: ['data-bs-theme'] });
    new ResizeObserver(ajustar).observe(canvas);
    new IntersectionObserver(([entrada]) => {
        visible = entrada.isIntersecting;
        activar();
    }).observe(canvas);
    document.addEventListener('visibilitychange', activar);
    reducir.addEventListener('change', activar);
})();
