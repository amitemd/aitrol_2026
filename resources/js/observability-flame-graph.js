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

const subprocessColor = (subprocess, requestDuration) => {
    if (subprocess.measured === false) {
        return ['#dbeafe', '#60a5fa', '#1d4ed8'];
    }

    const duration = Number(subprocess.duration_ms) || 0;
    const ratio = requestDuration > 0 ? duration / requestDuration : 0;

    if (ratio >= 0.5 || duration >= 1000) {
        return ['#fecaca', '#fb7185', '#dc2626'];
    }

    if (ratio >= 0.25 || duration >= 500) {
        return ['#fed7aa', '#fb923c', '#ea580c'];
    }

    if (ratio >= 0.1 || duration >= 100) {
        return ['#fef3c7', '#facc15', '#ca8a04'];
    }

    return ['#bbf7d0', '#4ade80', '#15803d'];
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
            contextTitle.textContent = 'Flame graph de acciones integrado con Auditoría';
            contextDescription.textContent = 'Cada franja superior representa una acción HTTP. La raíz AITROL está en la base y las llamadas medidas se apilan por profundidad; el ancho representa su duración.';
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
        const subprocesses = Array.isArray(process.subprocesses) ? process.subprocesses : [];

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

        subprocesses
            .filter((subprocess) => Number(subprocess.depth ?? 1) === 1)
            .forEach((subprocess) => inspectorArchitecture.append(inspectorItem(
                `${subprocess.type || 'Subproceso'} · ${formatValue(Number(subprocess.duration_ms) || 0, 'ms')} ms`,
                `${subprocess.name}${subprocess.measured === false ? ' · tiempo aún no instrumentado internamente' : ' · medición real'}`,
                subprocess.measured === false ? 'slate' : 'blue',
            )));

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

    const renderBlockTimeline = () => {
        updateModeLayout();

        const timeline = data.timeline ?? {};
        const allProcesses = (Array.isArray(timeline.processes) ? [...timeline.processes] : [])
            .sort((left, right) => Number(left.timestamp_ms || 0) - Number(right.timestamp_ms || 0));
        const chartWidth = Math.max(360, stage.clientWidth - 16);
        const chartHeight = Math.max(380, stage.clientHeight - 20);
        const margins = { top: 30, right: 20, bottom: 76, left: 78 };
        const plotWidth = Math.max(200, chartWidth - margins.left - margins.right);
        const plotHeight = Math.max(220, chartHeight - margins.top - margins.bottom);
        const maximumVisibleProcesses = Math.max(4, Math.min(18, Math.floor(plotWidth / 82)));
        const processes = allProcesses.slice(-maximumVisibleProcesses);
        const measuredDurations = processes
            .map((process) => Number(process.duration_ms))
            .filter((duration) => Number.isFinite(duration) && duration > 0);
        const maximumDuration = Math.max(1, ...measuredDurations);
        const searchTerm = state.search.trim().toLocaleLowerCase('es');
        const barSlot = plotWidth / Math.max(1, processes.length);
        const barWidth = Math.max(44, Math.min(132, barSlot * 0.88));
        const rootHeight = 28;
        const stackHeight = Math.max(140, plotHeight - rootHeight - 6);
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
            const y = margins.top + stackHeight - (stackHeight * ratio);
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

        const tickIndexes = [...new Set([0, 0.25, 0.5, 0.75, 1].map(
            (ratio) => Math.round(Math.max(0, processes.length - 1) * ratio),
        ))];

        tickIndexes.forEach((processIndex) => {
            const process = processes[processIndex];
            const x = margins.left + ((processIndex + 0.5) * barSlot);
            const [date = '', time = ''] = String(process?.date ?? '').split(/\s+/, 2);
            const line = createSvgElement('line', {
                x1: x,
                y1: margins.top,
                x2: x,
                y2: margins.top + stackHeight + rootHeight,
                stroke: '#e2e8f0',
                'stroke-width': 1,
            });
            const dateLabel = createSvgElement('text', {
                x,
                y: margins.top + stackHeight + rootHeight + 20,
                fill: '#475569',
                'font-size': 9,
                'font-weight': 800,
                'text-anchor': processIndex === 0 ? 'start' : processIndex === processes.length - 1 ? 'end' : 'middle',
            });
            const timeLabel = createSvgElement('text', {
                x,
                y: margins.top + stackHeight + rootHeight + 34,
                fill: '#0369a1',
                'font-size': 9,
                'font-weight': 800,
                'text-anchor': processIndex === 0 ? 'start' : processIndex === processes.length - 1 ? 'end' : 'middle',
            });

            dateLabel.textContent = date;
            timeLabel.textContent = time;
            svg.append(line, dateLabel, timeLabel);
        });

        processes.forEach((process, index) => {
            const duration = Number(process.duration_ms);
            const hasDuration = Number.isFinite(duration) && duration > 0;
            const x = margins.left + (index * barSlot) + ((barSlot - barWidth) / 2);
            const abnormalLogs = Array.isArray(process.abnormal_logs) ? process.abnormal_logs : [];
            const risk = abnormalLogs.some((log) => ['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR'].includes(log.level))
                ? 'critical'
                : abnormalLogs.length > 0 || process.risk === 'warning' ? 'warning' : process.risk ?? 'normal';
            const subprocesses = (Array.isArray(process.subprocesses) ? process.subprocesses : [])
                .filter((subprocess) => Number(subprocess.depth ?? 1) === 1)
                .filter((subprocess) => Number(subprocess.duration_ms) > 0)
                .sort((left, right) => Number(left.offset_ms || 0) - Number(right.offset_ms || 0));
            const summarizedSubprocesses = subprocesses.length > 10
                ? [
                    ...subprocesses.slice(0, 9),
                    {
                        id: `other-${process.id}`,
                        name: `Otras ${subprocesses.length - 9} operaciones medidas`,
                        type: 'Agrupado',
                        duration_ms: subprocesses.slice(9).reduce(
                            (total, subprocess) => total + (Number(subprocess.duration_ms) || 0),
                            0,
                        ),
                        measured: true,
                        depth: 1,
                    },
                ]
                : subprocesses;
            const effectiveSubprocesses = summarizedSubprocesses.length > 0 ? summarizedSubprocesses : [{
                id: `process-${process.id}`,
                name: 'Proceso sin desglose interno',
                type: 'Aplicación',
                duration_ms: hasDuration ? duration : 0,
                measured: false,
                depth: 1,
            }];
            const searchable = [
                process.label,
                process.route,
                process.controller,
                process.level,
                process.date,
                ...effectiveSubprocesses.flatMap((subprocess) => [subprocess.name, subprocess.type]),
            ]
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
            const title = createSvgElement('title');
            const route = [process.method, process.route].filter(Boolean).join(' ') || process.label;
            const audit = auditByProcess[process.id] ?? { technical: [], activities: [] };
            const auditCount = (audit.technical?.length ?? 0) + (audit.activities?.length ?? 0);
            const processHeight = hasDuration ? Math.max(24, (duration / maximumDuration) * stackHeight) : 8;
            const stackBottom = margins.top + stackHeight;
            const rootY = stackBottom;
            const rootColors = {
                critical: ['#991b1b', '#e11d48'],
                warning: ['#b45309', '#f59e0b'],
                normal: ['#075985', '#0284c7'],
            };
            const [rootStart, rootEnd] = rootColors[risk] ?? rootColors.normal;
            const rootGradientId = `timeline-root-${index}`;
            const rootGradient = createSvgElement('linearGradient', {
                id: rootGradientId,
                x1: '0%',
                y1: '0%',
                x2: '100%',
                y2: '0%',
            });

            rootGradient.append(
                createSvgElement('stop', { offset: '0%', 'stop-color': rootStart }),
                createSvgElement('stop', { offset: '100%', 'stop-color': rootEnd }),
            );
            definitions.append(rootGradient);

            const root = createSvgElement('rect', {
                x,
                y: rootY,
                width: barWidth,
                height: rootHeight,
                rx: 5,
                fill: `url(#${rootGradientId})`,
                stroke: matchesSearch ? '#1d4ed8' : '#ffffff',
                'stroke-width': matchesSearch ? 3 : 1.5,
                opacity: searchTerm !== '' && !matchesSearch ? 0.22 : 1,
            });
            const rootText = createSvgElement('text', {
                x: x + (barWidth / 2),
                y: rootY + 11,
                fill: '#ffffff',
                'font-size': 8.5,
                'font-weight': 900,
                'text-anchor': 'middle',
                'pointer-events': 'none',
            });
            const rootDuration = createSvgElement('text', {
                x: x + (barWidth / 2),
                y: rootY + 22,
                fill: '#e0f2fe',
                'font-size': 8,
                'font-weight': 800,
                'text-anchor': 'middle',
                'pointer-events': 'none',
            });
            const rootCharacters = Math.max(7, Math.floor(barWidth / 6.2));
            const rootLabel = String(process.route || process.label || 'Proceso');

            rootText.textContent = rootLabel.length > rootCharacters
                ? `${rootLabel.slice(0, rootCharacters - 1)}…`
                : rootLabel;
            rootDuration.textContent = hasDuration ? `${formatValue(duration, 'ms')} ms` : 'sin tiempo';
            group.append(root, rootText, rootDuration);

            let accumulatedHeight = 0;

            effectiveSubprocesses.forEach((subprocess, subprocessIndex) => {
                const subprocessDuration = Math.max(0, Number(subprocess.duration_ms) || 0);
                const segmentHeight = hasDuration
                    ? (subprocessDuration / duration) * processHeight
                    : 1;
                const remainingHeight = Math.max(0, processHeight - accumulatedHeight);
                const height = Math.min(Math.max(0.75, segmentHeight), remainingHeight);

                if (height <= 0) {
                    return;
                }
                const widthReduction = Math.min(barWidth * 0.34, subprocessIndex * Math.max(1.5, barWidth * 0.035));
                const segmentWidth = Math.max(12, barWidth - widthReduction);
                const segmentX = x + ((barWidth - segmentWidth) / 2);
                const segmentY = stackBottom - accumulatedHeight - height;
                const [lightColor, middleColor, darkColor] = subprocessColor(subprocess, duration);
                const gradientId = `timeline-process-${index}-${subprocessIndex}`;
                const gradient = createSvgElement('linearGradient', {
                    id: gradientId,
                    x1: '0%',
                    y1: '0%',
                    x2: '100%',
                    y2: '0%',
                });
                const segment = createSvgElement('rect', {
                    x: segmentX,
                    y: segmentY,
                    width: segmentWidth,
                    height,
                    rx: Math.min(5, height / 3),
                    fill: `url(#${gradientId})`,
                    stroke: '#ffffff',
                    'stroke-width': 1.2,
                    opacity: searchTerm !== '' && !matchesSearch ? 0.18 : 0.96,
                });
                const segmentTitle = createSvgElement('title');

                gradient.append(
                    createSvgElement('stop', { offset: '0%', 'stop-color': lightColor }),
                    createSvgElement('stop', { offset: '52%', 'stop-color': middleColor }),
                    createSvgElement('stop', { offset: '100%', 'stop-color': darkColor }),
                );
                definitions.append(gradient);
                segmentTitle.textContent = `${subprocess.type || 'Subproceso'} · ${subprocess.name}\nDuración: ${formatValue(subprocessDuration, 'ms')} ms\nProceso: ${route}\nFecha y hora: ${process.date}\nMedición: ${subprocess.measured === false ? 'tiempo restante no desglosado' : 'medición real'}`;
                segment.append(segmentTitle);
                group.append(segment);

                if (height >= 18 && segmentWidth >= 58) {
                    const label = createSvgElement('text', {
                        x: segmentX + 5,
                        y: segmentY + Math.min(14, height - 4),
                        fill: subprocess.measured === false ? '#172554' : '#172033',
                        'font-size': 8.5,
                        'font-weight': 900,
                        'pointer-events': 'none',
                    });
                    const availableCharacters = Math.max(6, Math.floor((segmentWidth - 10) / 5.2));
                    const labelText = `${subprocess.name} · ${formatValue(subprocessDuration, 'ms')} ms`;

                    label.textContent = labelText.length > availableCharacters
                        ? `${labelText.slice(0, availableCharacters - 1)}…`
                        : labelText;
                    group.append(label);
                }

                accumulatedHeight += height;
            });

            const stackFrames = Array.isArray(process.stack_frames) ? process.stack_frames : [];
            const stackSummary = stackFrames
                .map((frame) => typeof frame === 'string' ? frame : frame.label)
                .filter(Boolean)
                .join(' → ');

            title.textContent = `${route}\nFecha y hora: ${process.date}\nDuración total: ${hasDuration ? `${formatValue(duration, 'ms')} ms` : 'No registrada'}\nSubprocesos medidos: ${effectiveSubprocesses.length}\nCadena técnica: ${stackSummary || 'Sin cadena registrada'}\nAuditorías: ${auditCount}\nAlertas de log: ${abnormalLogs.length}`;
            group.append(title);

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

        xLabel.textContent = 'Fecha y hora de ejecución · cada grupo es un proceso · más reciente a la derecha';
        svg.append(xLabel);

        totalLabel.textContent = String(processes.length);
        totalCaption.textContent = 'Procesos visibles';
        unitLabel.textContent = allProcesses.length > processes.length ? `de ${allProcesses.length}` : 'procesos';
        depthLabel.textContent = String(processes.length);
        countLabel.textContent = 'Acciones';
        matchLabel.textContent = String(matches);
        breadcrumb.textContent = processes.length > 0
            ? `${processes[0].date} → ${processes[processes.length - 1].date}`
            : 'Sin marcas de tiempo disponibles';
        yAxisLabel.textContent = 'Tiempo total y subprocesos apilados (ms)';
        xAxisLabel.textContent = 'Eje X · fecha y hora · Eje Y · duración acumulada (ms)';
        detailLabel.textContent = processes.length > 0
            ? 'Seleccione un proceso para unir tiempos reales, arquitectura, auditoría y logs'
            : 'No hay procesos con fecha válida para representar';
        detailMeta.textContent = `${allProcesses.length > processes.length ? `Se muestran los ${processes.length} procesos más recientes de ${allProcesses.length}. ` : ''}Cada base contiene el total; los bloques superiores suman ese tiempo. Azul: no desglosado · verde: rápido · amarillo: moderado · rojo: lento.`;
        stage.scrollTop = 0;

        if (processes.length > 0) {
            const initialProcess = [...processes].sort(
                (left, right) => Number(right.duration_ms || 0) - Number(left.duration_ms || 0),
            )[0];

            showProcessDetails(initialProcess);
        }
    };

    const renderActionFlameTimeline = () => {
        updateModeLayout();

        const timeline = data.timeline ?? {};
        const allProcesses = (Array.isArray(timeline.processes) ? [...timeline.processes] : [])
            .filter((process) => Number.isFinite(Number(process.timestamp_ms)))
            .sort((left, right) => Number(left.timestamp_ms) - Number(right.timestamp_ms));
        const chartWidth = Math.max(420, stage.clientWidth - 16);
        const chartHeight = Math.max(440, stage.clientHeight - 20);
        const margins = { top: 78, right: 12, bottom: 42, left: 56 };
        const plotWidth = Math.max(300, chartWidth - margins.left - margins.right);
        const maximumVisibleActions = Math.max(2, Math.min(6, Math.floor(plotWidth / 205)));
        const processes = allProcesses.slice(-maximumVisibleActions);
        const searchTerm = state.search.trim().toLocaleLowerCase('es');
        const actionSlot = plotWidth / Math.max(1, processes.length);
        const actionGap = Math.max(3, Math.min(8, actionSlot * 0.025));
        const actionWidth = Math.max(30, actionSlot - (actionGap * 2));
        const isActionSpan = (subprocess) => subprocess.type === 'Acción HTTP';
        const hasActionSpan = (process) => (
            Array.isArray(process.subprocesses)
            && process.subprocesses.some(isActionSpan)
        );
        const subprocessDepth = (process, subprocess) => {
            const recordedDepth = Math.max(1, Number(subprocess.depth) || 1);

            if (hasActionSpan(process) && subprocess.measured !== false) {
                return Math.max(2, recordedDepth);
            }

            return recordedDepth + 1;
        };
        const maximumSpanDepth = Math.max(
            1,
            ...processes.flatMap((process) => (
                Array.isArray(process.subprocesses)
                    ? process.subprocesses
                        .filter((subprocess) => !isActionSpan(subprocess))
                        .map((subprocess) => subprocessDepth(process, subprocess))
                    : [1]
            )),
        );
        const maximumDepth = Math.min(10, maximumSpanDepth);
        const availableStackHeight = Math.max(220, chartHeight - margins.top - margins.bottom);
        const frameHeight = Math.max(24, Math.min(39, availableStackHeight / (maximumDepth + 1)));
        const plotBottom = chartHeight - margins.bottom;
        const palettes = [
            { header: '#dbeafe', accent: '#2563eb', text: '#1e3a8a' },
            { header: '#dcfce7', accent: '#16a34a', text: '#14532d' },
            { header: '#fef3c7', accent: '#f59e0b', text: '#78350f' },
            { header: '#f3e8ff', accent: '#7e22ce', text: '#581c87' },
            { header: '#cffafe', accent: '#0891b2', text: '#164e63' },
            { header: '#ffe4e6', accent: '#e11d48', text: '#881337' },
        ];
        const totalDuration = processes.reduce(
            (total, process) => total + Math.max(0, Number(process.duration_ms) || 0),
            0,
        );
        let matches = 0;

        svg.replaceChildren();
        svg.setAttribute('viewBox', `0 0 ${chartWidth} ${chartHeight}`);
        svg.setAttribute('width', '100%');
        svg.setAttribute('height', chartHeight);
        emptyState.classList.toggle('hidden', processes.length > 0);
        emptyState.classList.toggle('grid', processes.length === 0);

        const definitions = createSvgElement('defs');

        svg.append(definitions);

        for (let depth = 0; depth <= maximumDepth; depth += 1) {
            const y = plotBottom - ((depth + 0.5) * frameHeight);
            const guideY = plotBottom - ((depth + 1) * frameHeight);
            const label = createSvgElement('text', {
                x: margins.left - 16,
                y: y + 3,
                fill: '#334155',
                'font-size': 10,
                'font-weight': 900,
                'text-anchor': 'middle',
            });

            label.textContent = String(depth);
            svg.append(
                createSvgElement('line', {
                    x1: margins.left,
                    y1: guideY,
                    x2: margins.left + plotWidth,
                    y2: guideY,
                    stroke: '#dbe4f0',
                    'stroke-width': 1,
                    'stroke-dasharray': '3 5',
                }),
                label,
            );
        }

        const axis = createSvgElement('line', {
            x1: margins.left - 1,
            y1: margins.top - 4,
            x2: margins.left - 1,
            y2: plotBottom,
            stroke: '#64748b',
            'stroke-width': 1.2,
        });
        const axisArrow = createSvgElement('path', {
            d: `M ${margins.left - 5} ${margins.top + 2} L ${margins.left - 1} ${margins.top - 5} L ${margins.left + 3} ${margins.top + 2}`,
            fill: 'none',
            stroke: '#64748b',
            'stroke-width': 1.2,
        });

        svg.append(axis, axisArrow);

        const globalRoot = createSvgElement('rect', {
            x: margins.left,
            y: plotBottom - frameHeight + 2,
            width: plotWidth,
            height: frameHeight - 4,
            rx: 5,
            fill: '#e2e8f0',
            stroke: '#475569',
            'stroke-width': 1.4,
        });
        const globalRootLabel = createSvgElement('text', {
            x: margins.left + (plotWidth / 2),
            y: plotBottom - (frameHeight / 2) + 4,
            fill: '#0f172a',
            'font-size': 11,
            'font-weight': 900,
            'text-anchor': 'middle',
            'pointer-events': 'none',
        });

        globalRootLabel.textContent = `AITROL · ${processes.length} acciones · ${formatValue(totalDuration, 'ms')} ms acumulados`;
        svg.append(globalRoot, globalRootLabel);

        const renderFrame = ({ process, subprocess, processIndex, x, width, depth, palette, matchesSearch }) => {
            if (width < 0.7 || depth > maximumDepth) {
                return;
            }

            const duration = Number(subprocess.duration_ms) || 0;
            const y = plotBottom - ((depth + 1) * frameHeight) + 1;
            const height = frameHeight - 2;
            const [lightColor, middleColor, darkColor] = subprocessColor(subprocess, Number(process.duration_ms) || 0);
            const gradientId = `action-frame-${processIndex}-${String(subprocess.id).replace(/[^a-z0-9_-]/gi, '')}`;
            const gradient = createSvgElement('linearGradient', {
                id: gradientId,
                x1: '0%',
                y1: '0%',
                x2: '100%',
                y2: '0%',
            });
            const group = createSvgElement('g', {
                role: 'button',
                tabindex: '0',
                class: 'cursor-pointer outline-none',
                'aria-label': `${subprocess.type}, ${subprocess.name}, ${duration} milisegundos`,
            });
            const rectangle = createSvgElement('rect', {
                x: x + 0.5,
                y,
                width: Math.max(0.5, width - 1),
                height,
                rx: 3,
                fill: `url(#${gradientId})`,
                stroke: matchesSearch ? '#1d4ed8' : '#ffffff',
                'stroke-width': matchesSearch ? 2.5 : 1,
                opacity: searchTerm !== '' && !matchesSearch ? 0.22 : 1,
            });
            const accent = createSvgElement('rect', {
                x: x + 1,
                y: y + 1,
                width: Math.min(5, Math.max(2, width * 0.08)),
                height: Math.max(1, height - 2),
                rx: 2,
                fill: palette.accent,
                'pointer-events': 'none',
            });
            const title = createSvgElement('title');

            gradient.append(
                createSvgElement('stop', { offset: '0%', 'stop-color': lightColor }),
                createSvgElement('stop', { offset: '58%', 'stop-color': middleColor }),
                createSvgElement('stop', { offset: '100%', 'stop-color': darkColor }),
            );
            definitions.append(gradient);
            title.textContent = `${subprocess.type || 'Subproceso'} · ${subprocess.name}\nDuración: ${formatValue(duration, 'ms')} ms\nAcción: ${process.method || ''} ${process.route || process.label}\nEjecutado: ${process.date}\nProfundidad: ${depth}`;
            group.append(rectangle, accent, title);

            if (width >= 58) {
                const text = createSvgElement('text', {
                    x: x + 8,
                    y: y + (height / 2) + 4,
                    fill: '#172033',
                    'font-size': width >= 105 ? 9.5 : 8.5,
                    'font-weight': 900,
                    'pointer-events': 'none',
                });
                const availableCharacters = Math.max(6, Math.floor((width - 14) / 5.4));
                const textValue = `${subprocess.name} (${formatValue(duration, 'ms')} ms)`;

                text.textContent = textValue.length > availableCharacters
                    ? `${textValue.slice(0, availableCharacters - 1)}…`
                    : textValue;
                group.append(text);
            }

            const selectFrame = () => {
                showProcessDetails(process);
                detailLabel.textContent = `${subprocess.type || 'Subproceso'} · ${subprocess.name}`;
                detailMeta.textContent = `${process.date} · ${formatValue(duration, 'ms')} ms · profundidad ${depth} · ${process.method || ''} ${process.route || process.label}`;
            };

            group.addEventListener('pointerenter', selectFrame);
            group.addEventListener('focus', selectFrame);
            group.addEventListener('click', selectFrame);
            group.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    selectFrame();
                }
            });
            svg.append(group);
        };

        processes.forEach((process, processIndex) => {
            const palette = palettes[processIndex % palettes.length];
            const groupX = margins.left + (processIndex * actionSlot) + actionGap;
            const duration = Math.max(0.001, Number(process.duration_ms) || 0.001);
            const subprocesses = Array.isArray(process.subprocesses)
                ? process.subprocesses.filter((subprocess) => !isActionSpan(subprocess))
                : [];
            const searchable = [
                process.date,
                process.label,
                process.route,
                process.controller,
                ...subprocesses.flatMap((subprocess) => [subprocess.name, subprocess.type]),
            ].filter(Boolean).join(' ').toLocaleLowerCase('es');
            const matchesSearch = searchTerm !== '' && searchable.includes(searchTerm);
            const header = createSvgElement('rect', {
                x: groupX,
                y: 8,
                width: actionWidth,
                height: 55,
                rx: 6,
                fill: palette.header,
                stroke: matchesSearch ? '#1d4ed8' : '#cbd5e1',
                'stroke-width': matchesSearch ? 2.5 : 1,
                opacity: searchTerm !== '' && !matchesSearch ? 0.3 : 1,
            });
            const methodIcon = createSvgElement('text', {
                x: groupX + 15,
                y: 31,
                fill: palette.text,
                'font-size': 17,
                'font-weight': 900,
                'text-anchor': 'middle',
            });
            const numberCircle = createSvgElement('circle', {
                cx: groupX + 39,
                cy: 26,
                r: 11,
                fill: palette.accent,
            });
            const numberLabel = createSvgElement('text', {
                x: groupX + 39,
                y: 30,
                fill: '#ffffff',
                'font-size': 10,
                'font-weight': 900,
                'text-anchor': 'middle',
            });
            const routeLabel = createSvgElement('text', {
                x: groupX + 56,
                y: 22,
                fill: palette.text,
                'font-size': 10.5,
                'font-weight': 900,
            });
            const dateLabel = createSvgElement('text', {
                x: groupX + 56,
                y: 38,
                fill: palette.text,
                'font-size': 8.5,
                'font-weight': 700,
            });
            const durationLabel = createSvgElement('text', {
                x: groupX + 56,
                y: 52,
                fill: palette.text,
                'font-size': 9,
                'font-weight': 900,
            });
            const separator = createSvgElement('line', {
                x1: groupX - actionGap,
                y1: 8,
                x2: groupX - actionGap,
                y2: plotBottom,
                stroke: palette.accent,
                'stroke-width': 1,
                'stroke-dasharray': '4 4',
                opacity: 0.65,
            });
            const route = [process.method, process.route].filter(Boolean).join(' ') || process.label;
            const availableRouteCharacters = Math.max(8, Math.floor((actionWidth - 64) / 6));

            methodIcon.textContent = process.method === 'GET'
                ? '⌕'
                : process.method === 'POST'
                    ? '+'
                    : ['PUT', 'PATCH'].includes(process.method)
                        ? '✎'
                        : process.method === 'DELETE'
                            ? '×'
                            : '•';
            numberLabel.textContent = String(process.process_number || processIndex + 1);
            routeLabel.textContent = route.length > availableRouteCharacters
                ? `${route.slice(0, availableRouteCharacters - 1)}…`
                : route;
            dateLabel.textContent = process.date;
            durationLabel.textContent = `${formatValue(duration, 'ms')} ms`;
            svg.append(separator, header, methodIcon, numberCircle, numberLabel, routeLabel, dateLabel, durationLabel);

            const actionY = plotBottom - (2 * frameHeight) + 1;
            const actionGradientId = `action-root-${processIndex}`;
            const actionGradient = createSvgElement('linearGradient', {
                id: actionGradientId,
                x1: '0%',
                y1: '0%',
                x2: '100%',
                y2: '0%',
            });
            const actionGroup = createSvgElement('g', {
                role: 'button',
                tabindex: '0',
                class: 'cursor-pointer outline-none',
            });
            const actionRectangle = createSvgElement('rect', {
                x: groupX + 0.5,
                y: actionY,
                width: actionWidth - 1,
                height: frameHeight - 2,
                rx: 4,
                fill: `url(#${actionGradientId})`,
                stroke: matchesSearch ? '#1d4ed8' : '#ffffff',
                'stroke-width': matchesSearch ? 2.5 : 1,
                opacity: searchTerm !== '' && !matchesSearch ? 0.24 : 1,
            });
            const actionText = createSvgElement('text', {
                x: groupX + (actionWidth / 2),
                y: actionY + (frameHeight / 2) + 4,
                fill: '#ffffff',
                'font-size': 10,
                'font-weight': 900,
                'text-anchor': 'middle',
                'pointer-events': 'none',
            });

            actionGradient.append(
                createSvgElement('stop', { offset: '0%', 'stop-color': palette.accent }),
                createSvgElement('stop', { offset: '100%', 'stop-color': palette.text }),
            );
            definitions.append(actionGradient);
            actionText.textContent = route.length > Math.max(8, Math.floor(actionWidth / 6))
                ? `${route.slice(0, Math.max(7, Math.floor(actionWidth / 6) - 1))}…`
                : route;
            actionGroup.append(actionRectangle, actionText);
            actionGroup.addEventListener('pointerenter', () => showProcessDetails(process));
            actionGroup.addEventListener('focus', () => showProcessDetails(process));
            actionGroup.addEventListener('click', () => showProcessDetails(process));
            svg.append(actionGroup);

            subprocesses.forEach((subprocess) => {
                const spanDuration = Math.max(0, Number(subprocess.duration_ms) || 0);
                const spanOffset = Math.max(0, Number(subprocess.offset_ms) || 0);
                const width = Math.min(actionWidth, Math.max(1, actionWidth * (spanDuration / duration)));
                const x = Math.min(
                    groupX + actionWidth - width,
                    groupX + (actionWidth * (spanOffset / duration)),
                );
                const depth = Math.min(maximumDepth, subprocessDepth(process, subprocess));
                const frameMatches = searchTerm !== '' && [subprocess.name, subprocess.type]
                    .filter(Boolean)
                    .join(' ')
                    .toLocaleLowerCase('es')
                    .includes(searchTerm);

                renderFrame({
                    process,
                    subprocess,
                    processIndex,
                    x,
                    width,
                    depth,
                    palette,
                    matchesSearch: frameMatches,
                });
                matches += frameMatches ? 1 : 0;
            });

            matches += matchesSearch ? 1 : 0;
        });

        if (processes.length > 0) {
            const finalSeparatorX = margins.left + (processes.length * actionSlot);

            svg.append(createSvgElement('line', {
                x1: finalSeparatorX,
                y1: 8,
                x2: finalSeparatorX,
                y2: plotBottom,
                stroke: '#94a3b8',
                'stroke-width': 1,
                'stroke-dasharray': '4 4',
            }));
        }

        const xLabel = createSvgElement('text', {
            x: margins.left + (plotWidth / 2),
            y: plotBottom + 24,
            fill: '#334155',
            'font-size': 10.5,
            'font-weight': 900,
            'text-anchor': 'middle',
        });

        xLabel.textContent = 'Acciones ordenadas por fecha y hora · más reciente a la derecha';
        svg.append(xLabel);

        totalLabel.textContent = String(processes.length);
        totalCaption.textContent = 'Acciones visibles';
        unitLabel.textContent = allProcesses.length > processes.length ? `de ${allProcesses.length}` : 'acciones';
        depthLabel.textContent = String(maximumDepth);
        countLabel.textContent = 'Profundidad máxima';
        matchLabel.textContent = String(matches);
        breadcrumb.textContent = processes.length > 0
            ? `${processes[0].date} → ${processes[processes.length - 1].date}`
            : 'Sin acciones medidas para representar';
        yAxisLabel.textContent = 'Profundidad de llamadas';
        xAxisLabel.textContent = 'Eje X · acciones cronológicas · Eje Y · profundidad técnica · ancho · duración';
        detailLabel.textContent = processes.length > 0
            ? 'Seleccione una acción o subproceso para revisar tiempos, auditoría y logs'
            : 'No hay procesos con fecha y duración válidas';
        detailMeta.textContent = `${allProcesses.length > processes.length ? `Se muestran las ${processes.length} acciones más recientes de ${allProcesses.length}. ` : ''}El ancho de cada bloque representa su tiempo dentro de la acción principal.`;
        stage.scrollTop = 0;

        if (processes.length > 0) {
            showProcessDetails(processes[processes.length - 1]);
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
            renderActionFlameTimeline();

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
