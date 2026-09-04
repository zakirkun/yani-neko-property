(() => {
    'use strict';

    const header = document.querySelector('[data-site-header]');
    const nav = document.querySelector('#site-nav');
    const navToggle = document.querySelector('.nav-toggle');
    const navBackdrop = document.querySelector('.nav-backdrop');

    if (header && nav && navToggle && navBackdrop) {
        const closeNavigation = (returnFocus = false) => {
            nav.classList.remove('open');
            navToggle.setAttribute('aria-expanded', 'false');
            navToggle.setAttribute('aria-label', 'Open navigation');
            document.body.classList.remove('nav-open');

            if (returnFocus) navToggle.focus();
        };

        const openNavigation = () => {
            nav.classList.add('open');
            navToggle.setAttribute('aria-expanded', 'true');
            navToggle.setAttribute('aria-label', 'Close navigation');
            document.body.classList.add('nav-open');
        };

        navToggle.addEventListener('click', () => {
            navToggle.getAttribute('aria-expanded') === 'true' ? closeNavigation() : openNavigation();
        });

        navBackdrop.addEventListener('click', () => closeNavigation(true));
        nav.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => closeNavigation()));

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && nav.classList.contains('open')) closeNavigation(true);
        });

        const desktop = window.matchMedia('(min-width: 901px)');
        desktop.addEventListener('change', (event) => {
            if (event.matches) closeNavigation();
        });
    }

    const filterToggle = document.querySelector('.filter-toggle');
    const filterBody = document.querySelector('#property-filters');

    if (filterToggle && filterBody) {
        const mobile = window.matchMedia('(max-width: 760px)');
        const hasActiveFilters = Number(filterToggle.dataset.activeFilters) > 0;
        const label = filterToggle.querySelector('[data-filter-label]');

        const setFiltersOpen = (open) => {
            filterToggle.setAttribute('aria-expanded', String(open));
            filterBody.hidden = !open;
            if (label) label.textContent = open ? 'Hide filters' : 'Show filters';
        };

        const syncFilters = () => {
            if (mobile.matches) {
                setFiltersOpen(hasActiveFilters);
            } else {
                setFiltersOpen(true);
            }
        };

        filterToggle.addEventListener('click', () => {
            setFiltersOpen(filterToggle.getAttribute('aria-expanded') !== 'true');
        });
        mobile.addEventListener('change', syncFilters);
        syncFilters();
    }
})();
