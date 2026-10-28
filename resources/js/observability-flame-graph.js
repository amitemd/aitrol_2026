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
    const showErrorsButton = modal.querySelector('[data-flame-show-errors]');
    const jumpModeButtons = [...modal.querySelectorAll('[data-flame-jump-mode]')];
    const contextTitle = modal.querySelector('[data-flame-context-title]');
    const contextDescription = modal.querySelector('[data-flame-context-description]');
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
    const inspectorTitle = modal.querySelector('[data-flame-inspector-title]');
    const inspectorRisk = modal.querySelector('[data-flame-inspector-risk]');
    const inspectorDate = modal.querySelector('[data-flame-inspector-date]');
    const inspectorDuration = modal.querySelector('[data-flame-inspector-duration]');
    const inspectorCorrelation = modal.querySelector('[data-flame-inspector-correlation]');
    const inspectorArchitecture = modal.querySelector('[data-flame-inspector-architecture]');
    const inspectorAudit = modal.querySelector('[data-flame-inspector-audit]');
    const inspectorAuditCount = modal.querySelector('[data-flame-inspector-audit-count]');
    const inspectorLogs = modal.querySelector('[data-flame-inspector-logs]');
    const inspectorLogCount = modal.querySelector('[data-flame-inspector-log-count]');
    const auditByProcess = window.AitrolAuditByProcess ?? {};
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
            searchInput.placeholder = 'Buscar ruta, controlador, contrato o repositorio';
            contextTitle.textContent = 'Flame graph integrado con Auditoría';
            contextDescription.textContent = 'El ancho muestra tiempo inclusivo acumulado y cada nivel representa la cadena técnica. Seleccione un bloque para correlacionar controlador, servicios, contratos, repositorios, auditoría y logs.';
        } else if (isStack) {
            searchInput.placeholder = 'Buscar función o frame';
            contextTitle.textContent = 'Pila jerárquica de ejecución';
            contextDescription.textContent = 'El ancho representa el peso relativo y la altura muestra la profundidad de llamadas. Seleccione un bloque para ampliar su rama.';
        } else {
            contextTitle.textContent = 'Diagnóstico priorizado';
            contextDescription.textContent = 'Empiece por la ruta con mayor tiempo acumulado y compare su P95 con el máximo observado.';
        }

        jumpModeButtons.forEach((button) => {
            const isCurrentMode = button.dataset.flameJumpMode === state.mode;

            button.disabled = isCurrentMode;
            button.classList.toggle('opacity-50', isCurrentMode);
            button.classList.toggle('cursor-default', isCurrentMode);
        });
    };

    const showNodeDetails = (node, profile) => {
        const ownPercentage = node.value > 0 ? (node.self_value / node.value) * 100 : 0;
        const sourceLabel = node.source === 'trace' ? 'traza real' : node.source === 'derived' ? 'ruta derivada' : 'origen mixto';

        detailLabel.textContent = node.label;
        detailMeta.textContent = `${formatValue(node.value, profile.unit)} ${profile.unit} · ${node.percentage.toFixed(2)}% del perfil · ${formatValue(node.self_value, profile.unit)} propios (${ownPercentage.toFixed(2)}%) · ${sourceLabel}`;
    };

    const emptyInspectorMessage = (message) => {
        const element = document.createElement('p');

        element.className = 'rounded-xl bg-slate-50 p-3 leading-5 text-slate-500';
        element.textContent = message;

        return element;
    };

    const inspectorItem = (title, value, tone = 'slate') => {
        const element = document.createElement('article');
        const heading = document.createElement('p');
        const description = document.createElement('p');
        const tones = {
            slate: 'border-slate-200 bg-slate-50',
            blue: 'border-blue-200 bg-blue-50',
            amber: 'border-amber-200 bg-amber-50',
            rose: 'border-rose-200 bg-rose-50',
        };

        element.className = `rounded-xl border p-3 ${tones[tone] ?? tones.slate}`;
        heading.className = 'break-words font-black text-slate-900';
        description.className = 'mt-1 break-all leading-4 text-slate-600';
        heading.textContent = title;
        description.textContent = value || 'Sin detalle adicional';
        element.append(heading, description);

        return element;
    };

    const showProcessDetails = (process) => {
        const duration = process.duration_ms === null
            ? 'duración no registrada'
            : `${formatValue(process.duration_ms, 'ms')} ms`;
        const route = [process.method, process.route].filter(Boolean).join(' ');
        const technicalContext = [route, process.controller].filter(Boolean).join(' · ');
        const risk = process.risk === 'critical' || process.risk === 'warning' ? process.risk : 'normal';
        const riskLabels = { critical: 'Crítico', warning: 'Advertencia', normal: 'Normal' };
        const architecture = process.architecture ?? {};
        const audit = auditByProcess[process.id] ?? { technical: [], activities: [], warnings: [] };
        const technicalEvents = Array.isArray(audit.technical) ? audit.technical : [];
        const activities = Array.isArray(audit.activities) ? audit.activities : [];
        const abnormalLogs = Array.isArray(process.abnormal_logs) ? process.abnormal_logs : [];

        detailLabel.textContent = `Proceso #${process.process_number} · ${process.label}`;
        detailMeta.textContent = `${process.date} · ${duration} · ${process.level} · ${process.environment}${technicalContext ? ` · ${technicalContext}` : ''}`;

        inspectorTitle.textContent = route || process.label;
        inspectorDate.textContent = process.date;
        inspectorDuration.textContent = duration;
        inspectorCorrelation.textContent = process.correlation_id || 'Registro histórico sin identificador';
        inspectorRisk.textContent = abnormalLogs.length > 0 ? 'Requiere revisión' : riskLabels[risk];
        inspectorRisk.className = `rounded-full px-2.5 py-1 text-[10px] font-black ${abnormalLogs.length > 0 || risk === 'critical' ? 'bg-rose-100 text-rose-700' : risk === 'warning' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700'}`;

        inspectorArchitecture.replaceChildren();
        const controller = architecture.controller || process.controller;

        if (controller) {
            inspectorArchitecture.append(inspectorItem('Controlador / método', `${controller}${architecture.method ? `@${architecture.method}` : ''}`, 'blue'));
        }

        (architecture.components ?? []).forEach((component) => {
            inspectorArchitecture.append(inspectorItem(
                component.type || 'Componente',
                component.implementation ? `${component.name} → ${component.implementation}` : component.name,
            ));
        });

        if (inspectorArchitecture.childElementCount === 0) {
            inspectorArchitecture.append(emptyInspectorMessage('Esta entrada histórica no contiene metadatos de arquitectura.'));
        }

        inspectorAudit.replaceChildren();
        technicalEvents.forEach((event) => inspectorAudit.append(inspectorItem(
            event.module || event.controller || 'Auditoría técnica',
            [event.date, event.model, event.view, event.query_time, event.observation, event.match].filter(Boolean).join(' · '),
            'blue',
        )));
        activities.forEach((activity) => inspectorAudit.append(inspectorItem(
            activity.description || 'Actividad de usuario',
            [activity.date, activity.user, activity.match].filter(Boolean).join(' · '),
            'blue',
        )));
        inspectorAuditCount.textContent = String(technicalEvents.length + activities.length);

        if (inspectorAudit.childElementCount === 0) {
            inspectorAudit.append(emptyInspectorMessage('No se encontró una acción de auditoría compatible en la misma ventana temporal.'));
        }

        inspectorLogs.replaceChildren();
        abnormalLogs.forEach((log) => inspectorLogs.append(inspectorItem(
            `${log.level} · ${log.date}`,
            [log.message, log.details ? String(log.details).slice(0, 600) : null].filter(Boolean).join(' · '),
            ['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR'].includes(log.level) ? 'rose' : 'amber',
        )));
        inspectorLogCount.textContent = String(abnormalLogs.length);

        if (abnormalLogs.length === 0) {
            inspectorLogs.append(emptyInspectorMessage('La solicitud no tiene errores ni advertencias correlacionados.'));
        }
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

    const renderBlockTimeline = () => {
        updateModeLayout();

        const timeline = data.timeline ?? {};
        const processes = Array.isArray(timeline.processes) ? timeline.processes : [];
        const chartWidth = Math.max(360, stage.clientWidth - 16);
        const chartHeight = Math.max(380, stage.clientHeight - 20);
        const margins = { top: 28, right: 22, bottom: 58, left: 72 };
        const plotWidth = Math.max(200, chartWidth - margins.left - margins.right);
        const plotHeight = Math.max(220, chartHeight - margins.top - margins.bottom);
        const span = Math.max(1, Number(timeline.span_ms) || 1);
        const measuredDurations = processes
            .map((process) => Number(process.duration_ms))
            .filter((duration) => Number.isFinite(duration) && duration > 0);
        const maximumDuration = Math.max(1, ...measuredDurations);
        const searchTerm = state.search.trim().toLocaleLowerCase('es');
        const barSlot = plotWidth / Math.max(1, processes.length);
        const barWidth = Math.max(7, Math.min(34, barSlot * 0.72));
        let matches = 0;

        svg.replaceChildren();
        svg.setAttribute('viewBox', `0 0 ${chartWidth} ${chartHeight}`);
        svg.setAttribute('width', '100%');
        svg.setAttribute('height', chartHeight);
        emptyState.classList.toggle('hidden', processes.length > 0);
        emptyState.classList.toggle('grid', processes.length === 0);
        const definitions = createSvgElement('defs');

        svg.append(definitions);

        [0, 0.25, 0.5, 0.75, 1].forEach((ratio) => {
            const y = margins.top + plotHeight - (plotHeight * ratio);
            const value = maximumDuration * ratio;
            const line = createSvgElement('line', {
                x1: margins.left,
                y1: y,
                x2: margins.left + plotWidth,
                y2: y,
                stroke: ratio === 0 ? '#94a3b8' : '#dbeafe',
                'stroke-width': ratio === 0 ? 1.5 : 1,
                'stroke-dasharray': ratio === 0 ? '0' : '4 5',
            });
            const label = createSvgElement('text', {
                x: margins.left - 10,
                y: y + 4,
                fill: '#475569',
                'font-size': 10,
                'font-weight': 700,
                'text-anchor': 'end',
            });

            label.textContent = `${formatValue(value, 'ms')} ms`;
            svg.append(line, label);
        });

        [0, 0.25, 0.5, 0.75, 1].forEach((ratio) => {
            const x = margins.left + (plotWidth * ratio);
            const elapsed = span * ratio;
            const line = createSvgElement('line', {
                x1: x,
                y1: margins.top,
                x2: x,
                y2: margins.top + plotHeight,
                stroke: '#e2e8f0',
                'stroke-width': 1,
            });
            const label = createSvgElement('text', {
                x,
                y: margins.top + plotHeight + 22,
                fill: '#475569',
                'font-size': 10,
                'font-weight': 700,
                'text-anchor': ratio === 0 ? 'start' : ratio === 1 ? 'end' : 'middle',
            });

            label.textContent = ratio === 0 ? 'Inicio' : `+${formatElapsedTime(elapsed)}`;
            svg.append(line, label);
        });

        processes.forEach((process, index) => {
            const offset = Math.max(0, Number(process.offset_ms) || 0);
            const duration = Number(process.duration_ms);
            const hasDuration = Number.isFinite(duration) && duration > 0;
            const height = hasDuration ? Math.max(5, (duration / maximumDuration) * plotHeight) : 5;
            const centeredX = margins.left + ((offset / span) * plotWidth);
            const x = Math.max(margins.left, Math.min(margins.left + plotWidth - barWidth, centeredX - (barWidth / 2)));
            const y = margins.top + plotHeight - height;
            const abnormalLogs = Array.isArray(process.abnormal_logs) ? process.abnormal_logs : [];
            const risk = abnormalLogs.some((log) => ['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR'].includes(log.level))
                ? 'critical'
                : abnormalLogs.length > 0 || process.risk === 'warning' ? 'warning' : process.risk ?? 'normal';
            const colors = {
                critical: ['#ef4444', '#be123c'],
                warning: ['#fbbf24', '#f97316'],
                normal: ['#22c55e', '#0891b2'],
            };
            const [startColor, endColor] = colors[risk] ?? colors.normal;
            const searchable = [process.label, process.route, process.controller, process.level, process.date]
                .filter(Boolean)
                .join(' ')
                .toLocaleLowerCase('es');
            const matchesSearch = searchTerm !== '' && searchable.includes(searchTerm);
            const gradientId = `timeline-block-${index}`;
            const gradient = createSvgElement('linearGradient', {
                id: gradientId,
                x1: '0%',
                y1: '0%',
                x2: '0%',
                y2: '100%',
            });
            const group = createSvgElement('g', {
                role: 'button',
                tabindex: '0',
                'aria-label': `Proceso ${process.process_number}, ${process.date}, ${process.label}`,
                class: 'cursor-pointer outline-none',
            });
            const rectangle = createSvgElement('rect', {
                x,
                y,
                width: barWidth,
                height,
                rx: Math.min(8, barWidth / 3),
                fill: `url(#${gradientId})`,
                stroke: matchesSearch ? '#1d4ed8' : '#ffffff',
                'stroke-width': matchesSearch ? 3 : 1.5,
                opacity: searchTerm !== '' && !matchesSearch ? 0.2 : 0.95,
            });
            const title = createSvgElement('title');
            const route = [process.method, process.route].filter(Boolean).join(' ') || process.label;
            const audit = auditByProcess[process.id] ?? { technical: [], activities: [] };
            const auditCount = (audit.technical?.length ?? 0) + (audit.activities?.length ?? 0);

            gradient.append(
                createSvgElement('stop', { offset: '0%', 'stop-color': startColor }),
                createSvgElement('stop', { offset: '100%', 'stop-color': endColor }),
            );
            definitions.append(gradient);
            title.textContent = `${route}\nFecha y hora: ${process.date}\nDuración: ${hasDuration ? `${formatValue(duration, 'ms')} ms` : 'No registrada'}\nAuditorías: ${auditCount}\nAlertas de log: ${abnormalLogs.length}`;
            group.append(rectangle, title);

            const selectProcess = () => showProcessDetails(process);

            group.addEventListener('pointerenter', selectProcess);
            group.addEventListener('focus', selectProcess);
            group.addEventListener('click', selectProcess);
            group.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    selectProcess();
                }
            });
            svg.append(group);
            matches += matchesSearch ? 1 : 0;
        });

        const xLabel = createSvgElement('text', {
            x: margins.left + (plotWidth / 2),
            y: chartHeight - 8,
            fill: '#334155',
            'font-size': 11,
            'font-weight': 800,
            'text-anchor': 'middle',
        });

        xLabel.textContent = 'Momento de ejecución dentro de la ventana analizada';
        svg.append(xLabel);

        totalLabel.textContent = formatElapsedTime(span);
        totalCaption.textContent = 'Ventana analizada';
        unitLabel.textContent = 'de ventana';
        depthLabel.textContent = String(processes.length);
        countLabel.textContent = 'Acciones';
        matchLabel.textContent = String(matches);
        breadcrumb.textContent = timeline.start_at && timeline.end_at
            ? `${timeline.start_at} → ${timeline.end_at}`
            : 'Sin marcas de tiempo disponibles';
        yAxisLabel.textContent = 'Duración (ms)';
        xAxisLabel.textContent = 'Eje X · momento de ejecución · Eje Y · duración (ms)';
        detailLabel.textContent = processes.length > 0
            ? 'Seleccione un bloque para unir rendimiento, arquitectura, auditoría y logs'
            : 'No hay procesos con fecha válida para representar';
        detailMeta.textContent = 'Verde: normal · amarillo: advertencia o lentitud · rojo: error o duración crítica.';
        stage.scrollTop = 0;

        if (processes.length > 0) {
            const initialProcess = [...processes].sort(
                (left, right) => Number(right.duration_ms || 0) - Number(left.duration_ms || 0),
            )[0];

            showProcessDetails(initialProcess);
        }
    };

    const renderExecutionFlameGraph = () => {
        updateModeLayout();

        const timeline = data.timeline ?? {};
        const processes = Array.isArray(timeline.processes) ? timeline.processes : [];
        const measuredProcesses = processes.filter((process) => Number(process.duration_ms) > 0);
        const usesDuration = measuredProcesses.length > 0;
        const plottedProcesses = usesDuration ? measuredProcesses : processes;
        const weightFor = (process) => usesDuration ? Number(process.duration_ms) : 1;
        const severityRank = { EMERGENCY: 8, ALERT: 7, CRITICAL: 6, ERROR: 5, WARNING: 4, NOTICE: 3, INFO: 2, DEBUG: 1 };
        const root = {
            label: 'AITROL',
            detail: 'Aplicación AITROL',
            type: 'Aplicación',
            source: 'architecture',
            level: 'DEBUG',
            value: 0,
            selfValue: 0,
            processIds: new Set(),
            children: new Map(),
            earliest: null,
            latest: null,
        };
        const processById = new Map(processes.map((process) => [process.id, process]));
        const appendProcess = (node, process, weight) => {
            node.value += weight;
            node.processIds.add(process.id);
            node.level = (severityRank[process.level] ?? 0) > (severityRank[node.level] ?? 0)
                ? process.level
                : node.level;
            node.earliest = node.earliest === null || process.timestamp_ms < node.earliest
                ? process.timestamp_ms
                : node.earliest;
            node.latest = node.latest === null || process.timestamp_ms > node.latest
                ? process.timestamp_ms
                : node.latest;
        };

        plottedProcesses.forEach((process) => {
            const fallbackFrames = [
                { label: 'AITROL', detail: 'Aplicación AITROL', type: 'Aplicación', source: 'architecture' },
                { label: [process.method, process.route].filter(Boolean).join(' ') || process.label, detail: process.route || process.label, type: 'Ruta', source: 'architecture' },
                ...(process.controller ? [{ label: `Controlador · ${process.controller.split('\\').pop()}`, detail: process.controller, type: 'Controlador', source: 'architecture' }] : []),
            ];
            const frames = Array.isArray(process.stack_frames) && process.stack_frames.length > 0
                ? process.stack_frames
                : fallbackFrames;
            const weight = Math.max(0.001, weightFor(process));
            let node = root;

            appendProcess(node, process, weight);

            frames.slice(frames[0]?.label === 'AITROL' ? 1 : 0).forEach((frame) => {
                const label = typeof frame === 'string' ? frame : frame.label;

                if (!label) {
                    return;
                }

                const detail = typeof frame === 'string' ? frame : frame.detail || frame.label;
                const type = typeof frame === 'string' ? 'Frame' : frame.type || 'Componente';
                const source = typeof frame === 'string' ? 'trace' : frame.source || 'architecture';
                const key = `${type}|${label}`;

                if (!node.children.has(key)) {
                    node.children.set(key, {
                        label,
                        detail,
                        type,
                        source,
                        level: 'DEBUG',
                        value: 0,
                        selfValue: 0,
                        processIds: new Set(),
                        children: new Map(),
                        earliest: null,
                        latest: null,
                    });
                }

                node = node.children.get(key);
                appendProcess(node, process, weight);
            });

            node.selfValue += weight;
        });

        const orderedChildren = (node) => [...node.children.values()].sort((left, right) => {
            const labelComparison = left.label.localeCompare(right.label, 'es', { sensitivity: 'base' });

            return labelComparison !== 0 ? labelComparison : right.value - left.value;
        });
        const treeDepth = (node, depth = 0) => Math.max(
            depth,
            ...orderedChildren(node).map((child) => treeDepth(child, depth + 1)),
        );
        const representativeProcess = (node) => [...node.processIds]
            .map((id) => processById.get(id))
            .filter(Boolean)
            .sort((left, right) => {
                const severityDifference = (severityRank[right.level] ?? 0) - (severityRank[left.level] ?? 0);

                return severityDifference !== 0
                    ? severityDifference
                    : Number(right.duration_ms || 0) - Number(left.duration_ms || 0);
            })[0];
        const riskFor = (node) => {
            if (['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR'].includes(node.level)) {
                return 'critical';
            }

            if (node.level === 'WARNING') {
                return 'warning';
            }

            return 'normal';
        };
        const fillFor = (node, percentage, depth) => {
            const risk = riskFor(node);

            if (risk === 'critical') {
                return depth % 2 === 0 ? '#dc2626' : '#f43f5e';
            }

            if (risk === 'warning') {
                return depth % 2 === 0 ? '#f97316' : '#f59e0b';
            }

            if (percentage >= 35) {
                return depth % 2 === 0 ? '#f59e0b' : '#fbbf24';
            }

            if (percentage >= 12) {
                return depth % 2 === 0 ? '#14b8a6' : '#22c55e';
            }

            return depth % 2 === 0 ? '#0ea5e9' : '#38bdf8';
        };
        const chartWidth = Math.max(520, stage.clientWidth - 16);
        const chartHeight = Math.max(440, stage.clientHeight - 16);
        const margins = { top: 38, right: 18, bottom: 46, left: 58 };
        const plotWidth = Math.max(300, chartWidth - margins.left - margins.right);
        const plotHeight = Math.max(300, chartHeight - margins.top - margins.bottom);
        const maximumDepth = treeDepth(root);
        const rowHeight = Math.max(17, Math.min(34, plotHeight / Math.max(1, maximumDepth + 1)));
        const visibleDepth = Math.max(0, Math.floor(plotHeight / rowHeight) - 1);
        const totalWeight = Math.max(0.001, root.value);
        const searchTerm = state.search.trim().toLocaleLowerCase('es');
        let matches = 0;

        svg.replaceChildren();
        svg.setAttribute('viewBox', `0 0 ${chartWidth} ${chartHeight}`);
        svg.setAttribute('width', '100%');
        svg.setAttribute('height', chartHeight);
        emptyState.classList.toggle('hidden', plottedProcesses.length > 0);
        emptyState.classList.toggle('grid', plottedProcesses.length === 0);

        [0, 0.25, 0.5, 0.75, 1].forEach((ratio) => {
            const x = margins.left + (plotWidth * ratio);
            const value = totalWeight * ratio;
            const line = createSvgElement('line', {
                x1: x,
                y1: margins.top,
                x2: x,
                y2: margins.top + plotHeight,
                stroke: '#dbeafe',
                'stroke-width': ratio === 0 || ratio === 1 ? 1.4 : 1,
                'stroke-dasharray': ratio === 0 || ratio === 1 ? '0' : '4 5',
            });
            const label = createSvgElement('text', {
                x,
                y: chartHeight - 12,
                fill: '#475569',
                'font-size': 10,
                'font-weight': 800,
                'text-anchor': ratio === 0 ? 'start' : ratio === 1 ? 'end' : 'middle',
            });

            label.textContent = usesDuration ? `${formatValue(value, 'ms')} ms` : `${formatValue(value, 'muestras')} acciones`;
            svg.append(line, label);
        });

        for (let depth = 0; depth <= Math.min(maximumDepth, visibleDepth); depth += 1) {
            const y = margins.top + plotHeight - ((depth + 0.5) * rowHeight);
            const label = createSvgElement('text', {
                x: margins.left - 10,
                y: y + 3,
                fill: '#64748b',
                'font-size': 9,
                'font-weight': 800,
                'text-anchor': 'end',
            });

            label.textContent = String(depth);
            svg.append(label);
        }

        const renderNode = (node, x, width, depth) => {
            if (depth > visibleDepth || width < 0.7) {
                return;
            }

            const y = margins.top + plotHeight - ((depth + 1) * rowHeight);
            const height = Math.max(12, rowHeight - 2);
            const percentage = (node.value / totalWeight) * 100;
            const searchable = [node.label, node.detail, node.type]
                .filter(Boolean)
                .join(' ')
                .toLocaleLowerCase('es');
            const matchesSearch = searchTerm !== '' && searchable.includes(searchTerm);
            const group = createSvgElement('g', {
                role: 'button',
                tabindex: '0',
                'aria-label': `${node.type}: ${node.label}`,
                class: 'cursor-pointer outline-none',
            });
            const rectangle = createSvgElement('rect', {
                x: x + 0.5,
                y,
                width: Math.max(0.5, width - 1),
                height,
                rx: Math.min(4, height / 4),
                fill: fillFor(node, percentage, depth),
                stroke: matchesSearch ? '#1d4ed8' : '#ffffff',
                'stroke-width': matchesSearch ? 3 : 1,
                opacity: searchTerm !== '' && !matchesSearch ? 0.22 : 0.96,
            });
            const title = createSvgElement('title');
            const representative = representativeProcess(node);
            const audit = representative ? auditByProcess[representative.id] ?? { technical: [], activities: [] } : { technical: [], activities: [] };
            const auditCount = (audit.technical?.length ?? 0) + (audit.activities?.length ?? 0);
            const abnormalCount = representative?.abnormal_logs?.length ?? 0;
            const timeRange = representative
                ? `${representative.date}${node.processIds.size > 1 ? ` · ${node.processIds.size} solicitudes agrupadas` : ''}`
                : 'Sin fecha disponible';

            title.textContent = `${node.type}: ${node.detail}\nPeso inclusivo: ${formatValue(node.value, usesDuration ? 'ms' : 'muestras')} ${usesDuration ? 'ms' : 'acciones'} (${percentage.toFixed(2)}%)\n${timeRange}\nAuditorías: ${auditCount}\nAlertas de log: ${abnormalCount}\nOrigen: ${node.source === 'trace' ? 'traza real' : 'arquitectura inspeccionada'}`;
            group.append(rectangle, title);

            if (width >= 42) {
                const text = createSvgElement('text', {
                    x: x + 6,
                    y: y + (height / 2) + 3.5,
                    fill: riskFor(node) === 'warning' && percentage >= 35 ? '#422006' : '#ffffff',
                    'font-size': Math.max(8, Math.min(10, height * 0.38)),
                    'font-weight': 800,
                    'pointer-events': 'none',
                });
                const maximumCharacters = Math.max(4, Math.floor((width - 12) / 6.2));

                text.textContent = node.label.length > maximumCharacters
                    ? `${node.label.slice(0, maximumCharacters - 1)}…`
                    : node.label;
                group.append(text);
            }

            const selectNode = () => {
                if (representative) {
                    showProcessDetails(representative);
                    detailLabel.textContent = `${node.type} · ${node.label}`;
                    detailMeta.textContent = `${formatValue(node.value, usesDuration ? 'ms' : 'muestras')} ${usesDuration ? 'ms inclusivos' : 'acciones'} · ${percentage.toFixed(2)}% del perfil · ${node.processIds.size} ${node.processIds.size === 1 ? 'solicitud' : 'solicitudes'} · ${node.source === 'trace' ? 'traza real' : 'arquitectura inspeccionada'}`;
                }
            };

            group.addEventListener('pointerenter', selectNode);
            group.addEventListener('focus', selectNode);
            group.addEventListener('click', selectNode);
            group.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    selectNode();
                }
            });
            svg.append(group);
            matches += matchesSearch ? 1 : 0;

            let childX = x;

            orderedChildren(node).forEach((child) => {
                const childWidth = width * (child.value / node.value);

                renderNode(child, childX, childWidth, depth + 1);
                childX += childWidth;
            });
        };

        if (plottedProcesses.length > 0) {
            renderNode(root, margins.left, plotWidth, 0);
        }

        totalLabel.textContent = formatValue(root.value, usesDuration ? 'ms' : 'muestras');
        totalCaption.textContent = usesDuration ? 'Tiempo inclusivo' : 'Acciones representadas';
        unitLabel.textContent = usesDuration ? 'ms' : 'acciones';
        depthLabel.textContent = String(maximumDepth);
        countLabel.textContent = 'Profundidad máxima';
        matchLabel.textContent = String(matches);
        breadcrumb.textContent = timeline.start_at && timeline.end_at
            ? `${timeline.start_at} → ${timeline.end_at}`
            : 'Sin marcas de tiempo disponibles';
        yAxisLabel.textContent = 'Profundidad de la pila';
        xAxisLabel.textContent = usesDuration
            ? 'Eje X · tiempo inclusivo acumulado (ms) · Eje Y · profundidad técnica'
            : 'Eje X · frecuencia de acciones · Eje Y · profundidad técnica';
        detailLabel.textContent = plottedProcesses.length > 0
            ? 'Seleccione un bloque para revisar su arquitectura, auditoría y anomalías'
            : 'No hay procesos con datos suficientes para representar';
        detailMeta.textContent = usesDuration
            ? `${measuredProcesses.length} solicitudes medidas. ${processes.length - measuredProcesses.length} sin duración no se mezclan con los milisegundos.`
            : 'No hay duration_ms; el ancho representa frecuencia y no tiempo.';
        stage.scrollTop = 0;

        if (plottedProcesses.length > 0) {
            const initialProcess = [...plottedProcesses].sort(
                (left, right) => Number(right.duration_ms || 0) - Number(left.duration_ms || 0),
            )[0];

            showProcessDetails(initialProcess);
        }
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
            renderExecutionFlameGraph();

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
    jumpModeButtons.forEach((button) => {
        button.addEventListener('click', () => {
            state.mode = button.dataset.flameJumpMode;
            state.zoomId = null;
            stage.scrollTop = 0;
            render();
        });
    });
    showErrorsButton?.addEventListener('click', () => {
        const errorProfile = data.profiles.find((profile) => profile.key === 'errors');

        if (!errorProfile) {
            return;
        }

        state.mode = 'stack';
        state.profileKey = errorProfile.key;
        state.zoomId = null;
        profileSelect.value = errorProfile.key;
        stage.scrollTop = 0;
        render();
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
