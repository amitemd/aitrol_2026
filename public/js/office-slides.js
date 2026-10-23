(function () {
    'use strict';
    const suite = window.OfficeSuite; if (!suite || suite.kind !== 'presentation') return;
    const slides = suite.data.slides, list = document.getElementById('slide-list'), canvas = document.getElementById('slide-canvas'), show = document.getElementById('slide-show');
    let active = 0, presenting = 0;
    function blank() { return {title: 'Nueva diapositiva', body: '', background: '#ffffff', color: '#17465c', fontSize: 28, layout: 'content', image: ''}; }
    function paintBackground(target, slide) {
        const background = document.createElement('div'); background.className = 'slide-background'; background.setAttribute('aria-hidden', 'true');
        (slide.backgroundLayers || []).forEach(function (layer) {
            const element = document.createElement(layer.image ? 'img' : 'div');
            if (layer.image) { element.src = layer.image; element.alt = ''; } else element.style.backgroundColor = layer.color;
            element.style.left = layer.x + '%'; element.style.top = layer.y + '%'; element.style.width = layer.width + '%'; element.style.height = layer.height + '%';
            background.appendChild(element);
        });
        target.appendChild(background);
    }
    function paint(target, slide) {
        target.replaceChildren(); target.style.backgroundColor = slide.background; target.style.color = slide.color; target.dataset.layout = slide.layout; paintBackground(target, slide);
        if (slide.elements && slide.elements.length) {
            slide.elements.forEach(function (item) {
                const element = document.createElement(item.type === 'image' ? 'img' : 'div'); element.className = 'slide-original-element';
                Object.assign(element.style, {left: item.x + '%', top: item.y + '%', width: item.width + '%', height: item.height + '%', transform: 'rotate(' + (item.rotation || 0) + 'deg)'});
                if (item.type === 'image') { element.src = item.image; element.alt = 'Imagen original'; }
                else {
                    element.textContent = item.text; Object.assign(element.style, {fontFamily: '"' + item.fontFamily + '", Arial, sans-serif', fontSize: (item.fontSize / 9.6) + 'cqw', color: item.color, fontWeight: item.bold ? '700' : '400', textAlign: item.align});
                    if (target === canvas) {
                        element.contentEditable = 'plaintext-only'; element.setAttribute('aria-label', 'Editar texto de la diapositiva');
                        element.addEventListener('input', function () { item.text = element.innerText; const firstText = slide.elements.find(function (entry) { return entry.type === 'text'; }); if (item === firstText) slide.title = item.text.slice(0, 300); suite.changed(); });
                    }
                }
                target.appendChild(element);
            });
            return;
        }
        const heading = document.createElement('h2'); heading.textContent = slide.title;
        const body = document.createElement('div'); body.className = 'slide-body'; body.textContent = slide.body; body.style.fontSize = (slide.fontSize / 10) + 'cqw';
        target.appendChild(heading); target.appendChild(body); const pictures = slide.images && slide.images.length ? slide.images : (slide.image ? [slide.image] : []); target.classList.toggle('with-image', pictures.length > 0);
        if (pictures.length) { const gallery = document.createElement('div'); gallery.className = 'slide-gallery'; pictures.forEach(function (src, index) { const image = document.createElement('img'); image.src = src; image.alt = 'Imagen ' + (index + 1) + ' de la diapositiva'; gallery.appendChild(image); }); target.appendChild(gallery); }
    }
    function renderList() {
        list.replaceChildren(); slides.forEach(function (slide, i) { const button = document.createElement('button'); button.type = 'button'; button.className = 'slide-thumbnail' + (i === active ? ' active' : ''); button.setAttribute('aria-label', 'Diapositiva ' + (i + 1) + ': ' + slide.title); button.setAttribute('aria-current', i === active ? 'true' : 'false'); const preview = document.createElement('div'); preview.style.background = slide.background; preview.style.color = slide.color; preview.style.position = "relative"; preview.style.overflow = "hidden"; if (slide.elements && slide.elements.length) { preview.className = 'slide-canvas'; paint(preview, slide); } else paintBackground(preview, slide); const number = document.createElement('small'); number.textContent = i + 1; const text = document.createElement('strong'); text.textContent = slide.title || 'Sin título'; if (!slide.elements || !slide.elements.length) preview.appendChild(text); button.appendChild(number); button.appendChild(preview); button.addEventListener('click', function () { active = i; render(); }); list.appendChild(button); });
    }
    function render() {
        const slide = slides[active]; renderList(); paint(canvas, slide);
        ['title','body','font','layout','color','image','remove-image'].forEach(function (key) { document.getElementById('slide-' + key).disabled = !!(slide.elements && slide.elements.length); });
        ['title', 'body', 'layout', 'background', 'color'].forEach(function (key) { document.getElementById('slide-' + key).value = slide[key]; });
        document.getElementById('slide-font').value = slide.fontSize;
        document.getElementById('slide-delete').disabled = slides.length === 1; document.getElementById('slide-up').disabled = active === 0; document.getElementById('slide-down').disabled = active === slides.length - 1;
    }
    ['title', 'body', 'layout', 'background', 'color', 'font'].forEach(function (key) {
        document.getElementById('slide-' + key).addEventListener('input', function (event) { slides[active][key === 'font' ? 'fontSize' : key] = key === 'font' ? Number(event.target.value) : event.target.value; suite.changed(); paint(canvas, slides[active]); renderList(); });
    });
    document.getElementById('slide-add').addEventListener('click', function () { if (slides.length >= 100) { suite.error('Máximo: 100 diapositivas.'); return; } slides.splice(active + 1, 0, blank()); active++; suite.changed(); render(); });
    document.getElementById('slide-duplicate').addEventListener('click', function () { if (slides.length >= 100) { suite.error('Máximo: 100 diapositivas.'); return; } slides.splice(active + 1, 0, JSON.parse(JSON.stringify(slides[active]))); active++; suite.changed(); render(); });
    document.getElementById('slide-delete').addEventListener('click', function () { if (slides.length <= 1 || !window.confirm('¿Eliminar esta diapositiva?')) return; slides.splice(active, 1); active = Math.max(0, active - 1); suite.changed(); render(); });
    ['up', 'down'].forEach(function (direction) { document.getElementById('slide-' + direction).addEventListener('click', function () { const next = active + (direction === 'up' ? -1 : 1); if (next < 0 || next >= slides.length) return; const slide = slides.splice(active, 1)[0]; slides.splice(next, 0, slide); active = next; suite.changed(); render(); }); });
    document.getElementById('slide-image').addEventListener('change', function (event) {
        const file = event.target.files[0], target = slides[active]; event.target.value = ''; if (!file) return;
        if (!['image/png', 'image/jpeg'].includes(file.type) || file.size > 1000000) { suite.error('Selecciona una imagen PNG o JPG de hasta 1 MB.'); return; }
        const reader = new FileReader(); reader.onload = function () { const image = new Image(); image.onload = function () { if (!slides.includes(target)) return; target.images = (target.images && target.images.length ? target.images : (target.image ? [target.image] : [])); if (target.images.length >= 50) { suite.error('Máximo 50 imágenes por diapositiva.'); return; } target.images.push(reader.result); target.image = target.images[0]; target.layout = 'split'; suite.changed(); render(); }; image.onerror = function () { suite.error('No se pudo abrir la imagen.'); }; image.src = reader.result; }; reader.onerror = function () { suite.error('No se pudo leer la imagen.'); }; reader.readAsDataURL(file);
    });
    document.getElementById('slide-remove-image').addEventListener('click', function () { slides[active].image = ''; slides[active].images = []; suite.changed(); render(); });
    function showSlide() { paint(document.getElementById('slide-show-canvas'), slides[presenting]); document.getElementById('slide-position').textContent = (presenting + 1) + ' / ' + slides.length; document.getElementById('slide-prev').disabled = presenting === 0; document.getElementById('slide-next').disabled = presenting === slides.length - 1; }
    function step(delta) { presenting = Math.max(0, Math.min(slides.length - 1, presenting + delta)); showSlide(); }
    document.getElementById('slide-present').addEventListener('click', function () { presenting = active; show.showModal(); showSlide(); });
    document.getElementById('slide-prev').addEventListener('click', function () { step(-1); }); document.getElementById('slide-next').addEventListener('click', function () { step(1); }); document.getElementById('slide-close').addEventListener('click', function () { show.close(); });
    show.addEventListener('keydown', function (event) { if (event.key === 'ArrowRight' || event.key === ' ') { event.preventDefault(); step(1); } if (event.key === 'ArrowLeft') { event.preventDefault(); step(-1); } });
    render();
})();
