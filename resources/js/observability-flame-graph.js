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
    const searchInput = modal.querySelector('[data-flame-search]');
    const orientationButton = modal.querySelector('[data-flame-orientation]');
    const resetButton = modal.querySelector('[data-flame-reset]');
    const closeButton = modal.querySelector('[data-flame-close]');
    const totalLabel = modal.querySelector('[data-flame-total]');
    const unitLabel = modal.querySelector('[data-flame-unit]');
    const depthLabel = modal.querySelector('[data-flame-depth]');
    const matchLabel = modal.querySelector('[data-flame-matches]');
    const breadcrumb = modal.querySelector('[data-flame-breadcrumb]');
    const detailLabel = modal.querySelector('[data-flame-detail-label]');
    const detailMeta = modal.querySelector('[data-flame-detail-meta]');
    const state = {
        profileKey: data.profiles[0].key,
        orientation: 'flame',
        zoomId: null,
        search: '',
    };

    const activeProfile = () => data.profiles.find((profile) => profile.key === state.profileKey) ?? data.profiles[0];

    const showNodeDetails = (node, profile) => {
        const ownPercentage = node.value > 0 ? (node.self_value / node.value) * 100 : 0;
        const sourceLabel = node.source === 'trace' ? 'traza real' : node.source === 'derived' ? 'ruta derivada' : 'origen mixto';

        detailLabel.textContent = node.label;
        detailMeta.textContent = `${formatValue(node.value, profile.unit)} ${profile.unit} · ${node.percentage.toFixed(2)}% del perfil · ${formatValue(node.self_value, profile.unit)} propios (${ownPercentage.toFixed(2)}%) · ${sourceLabel}`;
    };

    const render = () => {
        const profile = activeProfile();
        const rootResult = state.zoomId ? findNode(profile.root, state.zoomId) : null;
        const zoomRoot = rootResult?.node ?? profile.root;
        const zoomPath = rootResult?.path ?? [profile.root];
        const chartWidth = Math.max(1180, stage.clientWidth - 40);
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
        svg.setAttribute('width', chartWidth);
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
                stroke: matchesSearch ? '#fde047' : '#17130f',
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
                fill: '#94a3b8',
                'font-size': 10,
                'text-anchor': anchor,
            });

            marker.textContent = `${percentage}%`;
            svg.append(marker);
        });

        totalLabel.textContent = formatValue(profile.total, profile.unit);
        unitLabel.textContent = profile.unit;
        depthLabel.textContent = String(relativeDepth + 1);
        matchLabel.textContent = String(matches);
        breadcrumb.textContent = zoomPath.map((node) => node.label).join(' › ');
        orientationButton.textContent = state.orientation === 'flame' ? 'Vista: llama' : 'Vista: carámbano';
        showNodeDetails(zoomRoot, profile);
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
    orientationButton.addEventListener('click', () => {
        state.orientation = state.orientation === 'flame' ? 'icicle' : 'flame';
        render();
    });
    resetButton.addEventListener('click', () => {
        state.zoomId = null;
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
