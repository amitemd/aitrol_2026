const normalize = (value) => value
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLowerCase();

const initializeModuleCatalog = () => {
    const search = document.querySelector('#module-search');
    const cards = [...document.querySelectorAll('.module-card')];

    if (!search || cards.length === 0) {
        return;
    }

    const filters = [...document.querySelectorAll('.category-filter')];
    const results = document.querySelector('#results-count');
    const emptyState = document.querySelector('#empty-state');
    let activeCategory = 'all';

    const refresh = () => {
        const term = normalize(search.value.trim());
        let visible = 0;

        cards.forEach((card) => {
            const categoryMatches = activeCategory === 'all' || card.dataset.category === activeCategory;
            const textMatches = normalize(card.dataset.search ?? '').includes(term);
            const shouldShow = categoryMatches && textMatches;

            card.classList.toggle('hidden', !shouldShow);
            visible += shouldShow ? 1 : 0;
        });

        results.textContent = `Mostrando ${visible} ${visible === 1 ? 'módulo' : 'módulos'}`;
        emptyState.classList.toggle('hidden', visible !== 0);
    };

    filters.forEach((filter) => {
        filter.addEventListener('click', () => {
            activeCategory = filter.dataset.category;

            filters.forEach((item) => {
                const active = item === filter;
                item.classList.toggle('bg-cyan-400', active);
                item.classList.toggle('text-slate-950', active);
                item.classList.toggle('border', !active);
                item.classList.toggle('border-white/10', !active);
                item.classList.toggle('bg-white/5', !active);
                item.classList.toggle('text-slate-300', !active);
            });

            refresh();
        });
    });

    search.addEventListener('input', refresh);
};

document.addEventListener('DOMContentLoaded', initializeModuleCatalog);
