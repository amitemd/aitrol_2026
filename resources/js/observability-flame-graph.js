const SVG_NAMESPACE = 'http://www.w3.org/2000/svg';
const FRAME_HEIGHT = 31;
const AXIS_HEIGHT = 28;

const createSvgElement = (name, attributes = {}) => {
    const element = document.createElementNS(SVG_NAMESPACE, name);

    Object.entries(attributes).forEach(([key, value]) => element.setAttribute(key, String(value)));

    return element;
};

const hashLabel = (label) => [...label].reduce((hash, character) => ((hash << 5) - hash) + character.charCodeAt(0), 0);

const frameColor = (node) => {
    const variation = Math.abs(hashLabel(node.label)) % 18;

    if (node.source === 'derived') {
        return `hsl(${190 + variation} 78% ${42 + (variation % 8)}%)`;
    }

    if (node.source === 'mixed') {
        return `hsl(${270 + variation} 65% ${47 + (variation % 7)}%)`;
    }

    return `hsl(${8 + variation} 84% ${51 + (variation % 8)}%)`;
};

const processPalette = (level) => ({
    EMERGENCY: ['#fb7185', '#7f1d1d'],
    ALERT: ['#f43f5e', '#b91c1c'],
    CRITICAL: ['#f472b6', '#9f1239'],
    ERROR: ['#fb923c', '#e11d48'],
    WARNING: ['#fde047', '#f97316'],
    NOTICE: ['#67e8f9', '#2563eb'],
    INFO: ['#5eead4', '#0e7490'],
    DEBUG: ['#c4b5fd', '#6d28d9'],
}[level] ?? ['#cbd5e1', '#475569']);

const flamePath = (x, y, width, height) => {
    const effectiveWidth = Math.max(8, width);
    const right = x + effectiveWidth;
    const bottom = y + height;
    const center = x + (effectiveWidth * 0.52);

    return [
        `M ${x} ${bottom}`,
        `C ${x + (effectiveWidth * 0.04)} ${y + (height * 0.62)}, ${x + (effectiveWidth * 0.2)} ${y + (height * 0.58)}, ${x + (effectiveWidth * 0.25)} ${y + (height * 0.3)}`,
        `C ${x + (effectiveWidth * 0.36)} ${y + (height * 0.5)}, ${x + (effectiveWidth * 0.42)} ${y + (height * 0.36)}, ${center} ${y}`,
        `C ${x + (effectiveWidth * 0.62)} ${y + (height * 0.36)}, ${x + (effectiveWidth * 0.78)} ${y + (height * 0.4)}, ${right} ${y + (height * 0.58)}`,
        `L ${right} ${bottom}`,
        'Z',
    ].join(' ');
};

const findNode = (node, id, path = []) => {
    const nextPath = [...path, node];

    if (node.id === id) {
        return { node, path: nextPath };
    }

    for (const child of node.children ?? []) {
        const result = findNode(child, id, nextPath);

        if (result) {
            return result;
        }
    }

    return null;
};

const maximumRelativeDepth = (node, depth = 0) => Math.max(
    depth,
    ...(node.children ?? []).map((child) => maximumRelativeDepth(child, depth + 1)),
);

const formatValue = (value, unit) => {
    const maximumDecimals = unit === 'ms' ? 2 : 0;

    return new Intl.NumberFormat('es-EC', { maximumFractionDigits: maximumDecimals }).format(value);
};

const formatElapsedTime = (milliseconds) => {
    const totalMilliseconds = Math.max(0, Number(milliseconds) || 0);

    if (totalMilliseconds < 1000) {
        return `${formatValue(totalMilliseconds, 'ms')} ms`;
    }

    const totalSeconds = Math.floor(totalMilliseconds / 1000);
    const hours = Math.floor(totalSeconds / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);
    const seconds = totalSeconds % 60;

    if (hours > 0) {
        return `${hours} h ${minutes} min`;
    }

    if (minutes > 0) {
        return `${minutes} min ${seconds} s`;
    }

    return `${seconds} s`;
};

const initializeLogFlameGraph = () => {
    const modal = document.querySelector('#log-flame-graph-modal');
    const openButton = document.querySelector('[data-flame-open]');
    const data = window.AitrolLogFlameGraph;

    if (!modal || !openButton || !data || !Array.isArray(data.profiles) || data.profiles.length === 0) {
        return;
    }

    const svg = modal.querySelector('[data-flame-svg]');
    const stage = modal.querySelector('[data-flame-stage]');
    const emptyState = modal.querySelector('[data-flame-empty]');
    const profileSelect = modal.querySelector('[data-flame-profile]');
    const profileWrapper = modal.querySelector('[data-flame-profile-wrapper]');
    const searchInput = modal.querySelector('[data-flame-search]');
    const searchWrapper = modal.querySelector('[data-flame-search-wrapper]');
    const modeButtons = [...modal.querySelectorAll('[data-flame-mode-option]')];
    const orientationButton = modal.querySelector('[data-flame-orientation]');
    const resetButton = modal.querySelector('[data-flame-reset]');
    const closeButton = modal.querySelector('[data-flame-close]');
    const totalCaption = modal.querySelector('[data-flame-total-caption]');
    const totalLabel = modal.querySelector('[data-flame-total]');
    const unitLabel = modal.querySelector('[data-flame-unit]');
    const depthLabel = modal.querySelector('[data-flame-depth]');
    const countLabel = modal.querySelector('[data-flame-count-label]');
    const matchWrapper = modal.querySelector('[data-flame-match-wrapper]');
    const matchLabel = modal.querySelector('[data-flame-matches]');
    const breadcrumb = modal.querySelector('[data-flame-breadcrumb]');
    const yAxisLabel = modal.querySelector('[data-flame-y-axis]');
    const xAxisLabel = modal.querySelector('[data-flame-x-axis]');
    const detailLabel = modal.querySelector('[data-flame-detail-label]');
    const detailMeta = modal.querySelector('[data-flame-detail-meta]');
    const diagnosis = modal.querySelector('[data-flame-diagnosis]');
    const chart = modal.querySelector('[data-flame-chart]');
    const legend = modal.querySelector('[data-flame-legend]');
    const state = {
        profileKey: data.profiles[0].key,
        mode: 'diagnosis',
        orientation: 'flame',
        zoomId: null,
        search: '',
    };

    const activeProfile = () => data.profiles.find((profile) => profile.key === state.profileKey) ?? data.profiles[0];

    const updateModeLayout = () => {
        const isDiagnosis = state.mode === 'diagnosis';
        const isTimeline = state.mode === 'timeline';
        const isStack = state.mode === 'stack';

        diagnosis.classList.toggle('hidden', !isDiagnosis);
        chart.classList.toggle('hidden', isDiagnosis);
        chart.classList.toggle('grid', !isDiagnosis);
        searchWrapper.classList.toggle('hidden', isDiagnosis);
        searchWrapper.classList.toggle('flex', !isDiagnosis);
        profileWrapper.classList.toggle('hidden', !isStack);
        profileWrapper.classList.toggle('flex', isStack);
        orientationButton.classList.toggle('hidden', !isStack);
        resetButton.classList.toggle('hidden', !isStack);
        matchWrapper.classList.toggle('hidden', isDiagnosis || state.search === '');
        legend.classList.toggle('hidden', isDiagnosis);
        legend.classList.toggle('flex', !isDiagnosis);

        modeButtons.forEach((button) => {
            const isActive = button.dataset.flameModeOption === state.mode;

            button.classList.toggle('bg-blue-700', isActive);
            button.classList.toggle('text-white', isActive);
            button.classList.toggle('shadow-sm', isActive);
            button.classList.toggle('text-slate-600', !isActive);
            button.setAttribute('aria-pressed', String(isActive));
        });

        if (isTimeline) {
            searchInput.placeholder = 'Buscar proceso, ruta o controlador';
        } else if (isStack) {
            searchInput.placeholder = 'Buscar función o frame';
        }
    };

    const showNodeDetails = (node, profile) => {
        const ownPercentage = node.value > 0 ? (node.self_value / node.value) * 100 : 0;
        const sourceLabel = node.source === 'trace' ? 'traza real' : node.source === 'derived' ? 'ruta derivada' : 'origen mixto';

        detailLabel.textContent = node.label;
        detailMeta.textContent = `${formatValue(node.value, profile.unit)} ${profile.unit} · ${node.percentage.toFixed(2)}% del perfil · ${formatValue(node.self_value, profile.unit)} propios (${ownPercentage.toFixed(2)}%) · ${sourceLabel}`;
    };

    const showProcessDetails = (process) => {
        const duration = process.duration_ms === null
            ? 'duración no registrada'
            : `${formatValue(process.duration_ms, 'ms')} ms`;
        const route = [process.method, process.route].filter(Boolean).join(' ');
        const technicalContext = [route, process.controller].filter(Boolean).join(' · ');

        detailLabel.textContent = `Proceso #${process.process_number} · ${process.label}`;
        detailMeta.textContent = `${process.date} · ${duration} · ${process.level} · ${process.environment}${technicalContext ? ` · ${technicalContext}` : ''}`;
    };

    const renderStack = () => {
        updateModeLayout();

        const profile = activeProfile();
        const rootResult = state.zoomId ? findNode(profile.root, state.zoomId) : null;
        const zoomRoot = rootResult?.node ?? profile.root;
        const zoomPath = rootResult?.path ?? [profile.root];
        const chartWidth = Math.max(360, stage.clientWidth - 16);
        const relativeDepth = maximumRelativeDepth(zoomRoot);
        const chartHeight = ((relativeDepth + 1) * FRAME_HEIGHT) + AXIS_HEIGHT;
        const searchTerm = state.search.trim().toLocaleLowerCase('es');
        const frames = [];

        const collectFrames = (node, x, width, depth) => {
            frames.push({ node, x, width, depth });
            let childX = x;

            (node.children ?? []).forEach((child) => {
                const childWidth = node.value > 0 ? width * (child.value / node.value) : 0;

                collectFrames(child, childX, childWidth, depth + 1);
                childX += childWidth;
            });
        };

        collectFrames(zoomRoot, 0, chartWidth, 0);
        svg.replaceChildren();
        svg.setAttribute('viewBox', `0 0 ${chartWidth} ${chartHeight}`);
        svg.setAttribute('width', '100%');
        svg.setAttribute('height', chartHeight);
        emptyState.classList.toggle('hidden', frames.length > 0);
        emptyState.classList.toggle('grid', frames.length === 0);

        let matches = 0;

        frames.forEach(({ node, x, width, depth }) => {
            if (width < 0.35) {
                return;
            }

            const y = state.orientation === 'flame'
                ? chartHeight - AXIS_HEIGHT - ((depth + 1) * FRAME_HEIGHT)
                : AXIS_HEIGHT + (depth * FRAME_HEIGHT);
            const matchesSearch = searchTerm !== '' && node.label.toLocaleLowerCase('es').includes(searchTerm);
            const group = createSvgElement('g', {
                role: 'button',
                tabindex: '0',
                'aria-label': `${node.label}, ${node.percentage.toFixed(2)} por ciento`,
                class: 'cursor-pointer outline-none',
            });
            const rectangle = createSvgElement('rect', {
                x: x + 0.5,
                y: y + 0.5,
                width: Math.max(0, width - 1),
                height: FRAME_HEIGHT - 2,
                rx: 3,
                fill: frameColor(node),
                stroke: matchesSearch ? '#1d4ed8' : '#fff',
                'stroke-width': matchesSearch ? 3 : 1,
                opacity: searchTerm !== '' && !matchesSearch ? 0.38 : 1,
            });
            const title = createSvgElement('title');

            title.textContent = `${node.label}\n${formatValue(node.value, profile.unit)} ${profile.unit} (${node.percentage.toFixed(2)}%)\nPropio: ${formatValue(node.self_value, profile.unit)} ${profile.unit}`;
            group.append(rectangle, title);

            if (width > 52) {
                const text = createSvgElement('text', {
                    x: x + 7,
                    y: y + 19,
                    fill: '#fff',
                    'font-size': 11,
                    'font-weight': 700,
                    'pointer-events': 'none',
                });
                const availableCharacters = Math.max(3, Math.floor((width - 13) / 6.4));

                text.textContent = node.label.length > availableCharacters
                    ? `${node.label.slice(0, Math.max(1, availableCharacters - 1))}…`
                    : node.label;
                group.append(text);
            }

            const childrenWeight = (node.children ?? []).reduce((sum, child) => sum + child.value, 0);
            const exposedWidth = node.value > 0 ? width * Math.max(0, (node.value - childrenWeight) / node.value) : 0;

            if (exposedWidth > 1) {
                const exposedEdge = state.orientation === 'flame' ? y + 1 : y + FRAME_HEIGHT - 2;
                group.append(createSvgElement('line', {
                    x1: x + width - exposedWidth,
                    y1: exposedEdge,
                    x2: x + width - 1,
                    y2: exposedEdge,
                    stroke: '#fef3c7',
                    'stroke-width': 3,
                    'pointer-events': 'none',
                }));
            }

            const zoomToNode = () => {
                state.zoomId = node.id === zoomRoot.id ? state.zoomId : node.id;
                render();
            };

            group.addEventListener('click', zoomToNode);
            group.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    zoomToNode();
                }
            });
            group.addEventListener('pointerenter', () => showNodeDetails(node, profile));
            group.addEventListener('focus', () => showNodeDetails(node, profile));
            svg.append(group);
            matches += matchesSearch ? 1 : 0;
        });

        [0, 25, 50, 75, 100].forEach((percentage) => {
            const x = (chartWidth * percentage) / 100;
            const y = state.orientation === 'flame' ? chartHeight - 8 : 18;
            const anchor = percentage === 0 ? 'start' : percentage === 100 ? 'end' : 'middle';
            const marker = createSvgElement('text', {
                x,
                y,
                fill: '#475569',
                'font-size': 10,
                'text-anchor': anchor,
            });

            marker.textContent = `${percentage}%`;
            svg.append(marker);
        });

        totalLabel.textContent = formatValue(profile.total, profile.unit);
        totalCaption.textContent = 'Peso total';
        unitLabel.textContent = profile.unit;
        depthLabel.textContent = String(relativeDepth + 1);
        countLabel.textContent = 'Profundidad';
        matchLabel.textContent = String(matches);
        breadcrumb.textContent = zoomPath.map((node) => node.label).join(' › ');
        orientationButton.textContent = state.orientation === 'flame' ? 'Orientación: raíz abajo' : 'Orientación: raíz arriba';
        yAxisLabel.textContent = 'Profundidad';
        xAxisLabel.textContent = 'Eje X · proporción del perfil';
        updateModeLayout();
        showNodeDetails(zoomRoot, profile);

        if (state.orientation === 'flame' && state.zoomId === null) {
            requestAnimationFrame(() => {
                stage.scrollTop = stage.scrollHeight;
            });
        }
    };

    const renderTimeline = () => {
        updateModeLayout();

        const timeline = data.timeline ?? {};
        const processes = Array.isArray(timeline.processes) ? timeline.processes : [];
        const chartWidth = Math.max(360, stage.clientWidth - 16);
        const labelWidth = Math.min(380, Math.max(180, chartWidth * 0.34));
        const plotWidth = chartWidth - labelWidth;
        const rowHeight = 46;
        const topAxisHeight = 48;
        const chartHeight = Math.max(120, topAxisHeight + (processes.length * rowHeight) + 8);
        const span = Math.max(1, Number(timeline.span_ms) || 1);
        const searchTerm = state.search.trim().toLocaleLowerCase('es');
        let matches = 0;

        svg.replaceChildren();
        svg.setAttribute('viewBox', `0 0 ${chartWidth} ${chartHeight}`);
        svg.setAttribute('width', '100%');
        svg.setAttribute('height', chartHeight);
        emptyState.classList.toggle('hidden', processes.length > 0);
        emptyState.classList.toggle('grid', processes.length === 0);

        const definitions = createSvgElement('defs');

        ['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR', 'WARNING', 'NOTICE', 'INFO', 'DEBUG', 'DEFAULT'].forEach((level) => {
            const [startColor, endColor] = processPalette(level);
            const gradient = createSvgElement('linearGradient', {
                id: `process-gradient-${level.toLowerCase()}`,
                x1: '0%',
                y1: '0%',
                x2: '100%',
                y2: '100%',
            });

            gradient.append(
                createSvgElement('stop', { offset: '0%', 'stop-color': startColor }),
                createSvgElement('stop', { offset: '100%', 'stop-color': endColor }),
            );
            definitions.append(gradient);
        });
        svg.append(definitions);

        [0, 25, 50, 75, 100].forEach((percentage) => {
            const x = labelWidth + ((plotWidth * percentage) / 100);
            const tickValue = (span * percentage) / 100;
            const anchor = percentage === 0 ? 'start' : percentage === 100 ? 'end' : 'middle';
            const gridLine = createSvgElement('line', {
                x1: x,
                y1: topAxisHeight - 3,
                x2: x,
                y2: chartHeight,
                stroke: '#cbd5e1',
                'stroke-width': 1,
                'stroke-dasharray': '3 5',
                opacity: 0.45,
            });
            const tick = createSvgElement('text', {
                x,
                y: 16,
                fill: '#0f172a',
                'font-size': 11,
                'font-weight': 800,
                'text-anchor': anchor,
            });
            const exactTick = createSvgElement('text', {
                x,
                y: 32,
                fill: '#64748b',
                'font-size': 8,
                'font-weight': 650,
                'text-anchor': anchor,
            });

            tick.textContent = percentage === 0 ? 'Inicio' : `+${formatElapsedTime(tickValue)}`;
            exactTick.textContent = `${formatValue(tickValue, 'ms')} ms`;
            svg.append(gridLine, tick, exactTick);
        });

        processes.forEach((process, index) => {
            const y = topAxisHeight + (index * rowHeight);
            const offset = Math.max(0, Number(process.offset_ms) || 0);
            const duration = process.duration_ms === null ? null : Math.max(0, Number(process.duration_ms) || 0);
            const x = labelWidth + ((offset / span) * plotWidth);
            const measuredWidth = duration === null ? 0 : (duration / span) * plotWidth;
            const width = duration === null ? 18 : Math.max(18, measuredWidth);
            const searchable = [process.label, process.route, process.controller, process.level, process.date]
                .filter(Boolean)
                .join(' ')
                .toLocaleLowerCase('es');
            const matchesSearch = searchTerm !== '' && searchable.includes(searchTerm);
            const group = createSvgElement('g', {
                role: 'button',
                tabindex: '0',
                'aria-label': `Proceso ${process.process_number}, ${process.date}, ${process.label}`,
                class: 'cursor-pointer outline-none',
            });
            const rowBackground = createSvgElement('rect', {
                x: 0,
                y,
                width: chartWidth,
                height: rowHeight,
                fill: index % 2 === 0 ? '#ffffff' : '#f8fafc',
            });
            const processNumber = createSvgElement('text', {
                x: 10,
                y: y + 28,
                fill: '#1d4ed8',
                'font-size': 10,
                'font-weight': 800,
            });
            const processDate = createSvgElement('text', {
                x: 58,
                y: y + 17,
                fill: '#0891b2',
                'font-size': 9,
                'font-weight': 750,
            });
            const processLabel = createSvgElement('text', {
                x: 58,
                y: y + 34,
                fill: '#0f172a',
                'font-size': 10,
                'font-weight': 700,
            });
            const separator = createSvgElement('line', {
                x1: labelWidth - 10,
                y1: y + 4,
                x2: labelWidth - 10,
                y2: y + rowHeight - 4,
                stroke: '#cbd5e1',
                'stroke-width': 1,
            });
            const supportedLevels = ['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR', 'WARNING', 'NOTICE', 'INFO', 'DEBUG'];
            const gradientLevel = supportedLevels.includes(process.level) ? process.level : 'DEFAULT';
            const flame = createSvgElement('path', {
                d: flamePath(x, y + 5, width, rowHeight - 10),
                fill: `url(#process-gradient-${gradientLevel.toLowerCase()})`,
                stroke: matchesSearch ? '#1d4ed8' : '#fff',
                'stroke-width': matchesSearch ? 3 : 1,
                opacity: searchTerm !== '' && !matchesSearch ? 0.3 : 1,
            });
            const innerFlameWidth = Math.max(7, width * 0.34);
            const innerFlame = createSvgElement('path', {
                d: flamePath(
                    x + Math.max(3, width * 0.33),
                    y + 16,
                    innerFlameWidth,
                    rowHeight - 21,
                ),
                fill: '#fff7ed',
                opacity: searchTerm !== '' && !matchesSearch ? 0.08 : 0.38,
                'pointer-events': 'none',
            });
            const durationLine = createSvgElement('line', {
                x1: x,
                y1: y + rowHeight - 4,
                x2: x + Math.max(1, measuredWidth),
                y2: y + rowHeight - 4,
                stroke: '#fef3c7',
                'stroke-width': 2,
                opacity: duration === null ? 0 : 0.85,
                'pointer-events': 'none',
            });
            const title = createSvgElement('title');
            const durationLabel = duration === null ? 'No registrada' : `${formatValue(duration, 'ms')} ms`;
            const routeLabel = [process.method, process.route].filter(Boolean).join(' ');
            const fixedLabel = routeLabel || process.label;
            const maximumFixedCharacters = Math.max(16, Math.floor((labelWidth - 68) / 5.8));

            processNumber.textContent = `#${process.process_number}`;
            processDate.textContent = process.date;
            processLabel.textContent = fixedLabel.length > maximumFixedCharacters
                ? `${fixedLabel.slice(0, maximumFixedCharacters - 1)}…`
                : fixedLabel;
            title.textContent = `Proceso #${process.process_number}\nFecha y hora: ${process.date}\nDuración: ${durationLabel}\n${process.label}${routeLabel ? `\nRuta: ${routeLabel}` : ''}${process.controller ? `\nControlador: ${process.controller}` : ''}`;
            group.append(rowBackground, processNumber, processDate, processLabel, separator, flame, innerFlame, durationLine, title);

            if (width > 80) {
                const processText = createSvgElement('text', {
                    x: x + 7,
                    y: y + 29,
                    fill: process.level === 'WARNING' ? '#422006' : '#fff',
                    'font-size': 10,
                    'font-weight': 700,
                    'pointer-events': 'none',
                });
                const availableCharacters = Math.max(4, Math.floor((width - 14) / 6));
                const label = routeLabel || process.label;

                processText.textContent = label.length > availableCharacters
                    ? `${label.slice(0, Math.max(1, availableCharacters - 1))}…`
                    : label;
                group.append(processText);
            }

            group.addEventListener('pointerenter', () => showProcessDetails(process));
            group.addEventListener('focus', () => showProcessDetails(process));
            group.addEventListener('click', () => showProcessDetails(process));
            svg.append(group);
            matches += matchesSearch ? 1 : 0;
        });

        totalLabel.textContent = formatElapsedTime(span);
        totalCaption.textContent = 'Ventana analizada';
        unitLabel.textContent = 'de ventana';
        depthLabel.textContent = String(processes.length);
        countLabel.textContent = 'Eventos';
        matchLabel.textContent = String(matches);
        breadcrumb.textContent = timeline.start_at && timeline.end_at
            ? `${timeline.start_at} → ${timeline.end_at}`
            : 'Sin marcas de tiempo disponibles';
        yAxisLabel.textContent = 'Eventos';
        xAxisLabel.textContent = 'Eje X · tiempo transcurrido (ms)';
        updateModeLayout();
        detailLabel.textContent = processes.length > 0
            ? 'Pase el cursor sobre un proceso para ver su detalle'
            : 'No hay procesos con fecha válida para representar';
        detailMeta.textContent = timeline.truncated
            ? `Se muestran ${processes.length} de ${timeline.total_processes} procesos para mantener una visualización fluida.`
            : 'Los procesos sin duration_ms se representan como una marca puntual y no como un tiempo inventado.';
        stage.scrollTop = 0;
    };

    const renderDiagnosis = () => {
        const analysis = data.analysis ?? {};
        const bottleneck = analysis.primary_bottleneck;

        totalLabel.textContent = formatValue(Number(analysis.total_duration_ms) || 0, 'ms');
        totalCaption.textContent = 'Tiempo medido';
        unitLabel.textContent = 'ms';
        depthLabel.textContent = String(Number(analysis.measured_processes) || 0);
        countLabel.textContent = 'Solicitudes medidas';
        breadcrumb.textContent = 'Análisis basado exclusivamente en tiempos registrados';
        detailLabel.textContent = bottleneck
            ? `Principal candidato: ${bottleneck.route}`
            : 'No hay duraciones suficientes para detectar un cuello de botella';
        detailMeta.textContent = bottleneck
            ? `${formatValue(bottleneck.total_duration_ms, 'ms')} ms acumulados · ${formatValue(bottleneck.contribution_percentage, '%')}% del tiempo medido · máximo ${formatValue(bottleneck.maximum_duration_ms, 'ms')} ms`
            : 'Los eventos sin duration_ms permanecen disponibles en Cronología y Pilas.';
        updateModeLayout();
    };

    const render = () => {
        if (state.mode === 'diagnosis') {
            renderDiagnosis();

            return;
        }

        if (state.mode === 'timeline') {
            renderTimeline();

            return;
        }

        renderStack();
    };

    const openModal = () => {
        if (typeof modal.showModal === 'function') {
            modal.showModal();
        } else {
            modal.setAttribute('open', '');
        }

        document.body.classList.add('overflow-hidden');
        requestAnimationFrame(render);
    };

    const closeModal = () => {
        if (typeof modal.close === 'function') {
            modal.close();
        } else {
            modal.removeAttribute('open');
        }

        document.body.classList.remove('overflow-hidden');
    };

    openButton.addEventListener('click', openModal);
    closeButton.addEventListener('click', closeModal);
    modal.addEventListener('close', () => document.body.classList.remove('overflow-hidden'));
    profileSelect.addEventListener('change', () => {
        state.profileKey = profileSelect.value;
        state.zoomId = null;
        render();
    });
    searchInput.addEventListener('input', () => {
        state.search = searchInput.value;
        render();
    });
    modeButtons.forEach((button) => {
        button.addEventListener('click', () => {
            state.mode = button.dataset.flameModeOption;
            state.zoomId = null;
            stage.scrollTop = 0;
            render();
        });
    });
    orientationButton.addEventListener('click', () => {
        state.orientation = state.orientation === 'flame' ? 'icicle' : 'flame';
        render();
    });
    resetButton.addEventListener('click', () => {
        state.zoomId = null;
        stage.scrollLeft = 0;
        stage.scrollTop = 0;
        render();
    });

    let resizeTimer;
    window.addEventListener('resize', () => {
        window.clearTimeout(resizeTimer);
        resizeTimer = window.setTimeout(() => {
            if (modal.open) {
                render();
            }
        }, 120);
    });
};

document.addEventListener('DOMContentLoaded', initializeLogFlameGraph);
