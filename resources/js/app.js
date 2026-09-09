// Uses Alpine's CSP-compliant build: the app's Content-Security-Policy has no
// 'unsafe-eval', which the regular alpinejs package requires internally.
import Alpine from '@alpinejs/csp';

import './sucursales-map';

window.Alpine = Alpine;

/**
 * A message that shows itself and then fades out on its own — used for the
 * small "Saved." confirmations next to the profile/password forms. Plain
 * x-data can't hold `setTimeout(() => ...)` under the CSP build (defining a
 * function from a string is exactly what it disallows), so this lives here.
 */
Alpine.data('autoHide', () => ({
    show: true,
    init() {
        const delay = Number(this.$el.dataset.autohideMs) || 2000;
        setTimeout(() => { this.show = false; }, delay);
    },
}));

/**
 * Búsqueda en vivo: re-consulta el listado conforme se escribe (con un
 * pequeño debounce) en lugar de esperar a que se envíe el formulario, y
 * reemplaza solo el contenedor de resultados sin recargar la página.
 */
Alpine.data('busquedaEnVivo', () => ({
    valor: '',
    timer: null,
    form: null,

    init() {
        // $el resolves to whichever element's directive is currently being
        // evaluated (the <input>, once buscar() runs from x-on:input), not
        // the x-data root — so the form itself is captured once, here.
        this.form = this.$el;
        this.valor = this.form.dataset.valorInicial || '';
    },

    buscar() {
        clearTimeout(this.timer);
        this.timer = setTimeout(() => this.ejecutar(), 350);
    },

    async ejecutar() {
        const target = document.getElementById(this.form.dataset.resultados);

        if (!target) {
            return;
        }

        // Parte de la URL actual (no de this.form.action) para no perder otros
        // filtros ya aplicados en la página, como ?alcaldia= o ?sucursal=.
        const url = new URL(window.location.href);
        if (this.valor) {
            url.searchParams.set('buscar', this.valor);
        } else {
            url.searchParams.delete('buscar');
        }
        url.searchParams.delete('page');

        const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });

        if (response.ok) {
            target.innerHTML = await response.text();
            window.history.replaceState({}, '', url);
        }
    },
}));

let toastSequence = 0;

Alpine.store('toasts', {
    items: [],

    push(type, message, duration = 5000) {
        if (!message) {
            return;
        }

        const id = ++toastSequence;
        this.items.push({ id, type, message, visible: true });

        setTimeout(() => this.dismiss(id), duration);
    },

    dismiss(id) {
        const toast = this.items.find((item) => item.id === id);

        if (toast) {
            toast.visible = false;
        }

        setTimeout(() => {
            this.items = this.items.filter((item) => item.id !== id);
        }, 250);
    },
});

/**
 * The confirmation modal (used e.g. for account deletion) needs a focus trap
 * with several helper methods. Under the CSP build, x-data can only be a
 * bare component name or a plain object of values — method bodies can't be
 * parsed out of an inline HTML attribute — so the logic lives here instead
 * and the Blade component just does x-data="confirmationModal".
 */
Alpine.data('confirmationModal', () => ({
    show: false,
    name: null,

    init() {
        this.name = this.$el.dataset.modalName || null;
        this.show = this.$el.dataset.modalShow === '1';

        this.$watch('show', (value) => {
            if (value) {
                document.body.classList.add('overflow-y-hidden');

                if (this.$el.hasAttribute('data-focusable')) {
                    setTimeout(() => this.firstFocusable()?.focus(), 100);
                }
            } else {
                document.body.classList.remove('overflow-y-hidden');
            }
        });
    },

    focusables() {
        const selector = 'a, button, input:not([type="hidden"]), textarea, select, details, [tabindex]:not([tabindex="-1"])';

        return [...this.$el.querySelectorAll(selector)].filter((el) => !el.hasAttribute('disabled'));
    },
    firstFocusable() {
        return this.focusables()[0];
    },
    lastFocusable() {
        return this.focusables().slice(-1)[0];
    },
    nextFocusableIndex() {
        return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1);
    },
    prevFocusableIndex() {
        return Math.max(0, this.focusables().indexOf(document.activeElement)) - 1;
    },
    nextFocusable() {
        return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable();
    },
    prevFocusable() {
        return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable();
    },
}));

/**
 * Allows any script (or an inline Blade snippet reading session flash data)
 * to trigger a toast without depending on Alpine's own init timing:
 *   window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: '...' } }))
 */
window.addEventListener('toast', (event) => {
    const { type = 'info', message = '' } = event.detail || {};
    Alpine.store('toasts').push(type, message);
});

Alpine.start();

/**
 * Reads any flashed session messages off data-* attributes on <body> and
 * turns them into toasts. Data attributes (not an inline <script>) because
 * the CSP forbids inline script execution.
 */
document.addEventListener('DOMContentLoaded', () => {
    const { flashStatus, flashError, flashWarning } = document.body.dataset;

    if (flashStatus) window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: flashStatus } }));
    if (flashError) window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: flashError } }));
    if (flashWarning) window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'warning', message: flashWarning } }));
});

/**
 * Global "processing" overlay: shown on every same-page form submit and
 * every internal link click, so the user always gets feedback that the
 * system is working while the next page loads. The overlay never needs to
 * be hidden manually — the browser tears it down when it navigates away.
 */
document.addEventListener('DOMContentLoaded', () => {
    const overlay = document.getElementById('global-loading-overlay');

    if (!overlay) {
        return;
    }

    const showOverlay = () => {
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');
    };

    document.addEventListener('submit', (event) => {
        const form = event.target;

        if (
            form instanceof HTMLFormElement &&
            !event.defaultPrevented &&
            !form.hasAttribute('data-no-loading')
        ) {
            showOverlay();
        }
    });

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');

        if (!link || link.hasAttribute('data-no-loading') || link.hasAttribute('download')) {
            return;
        }

        if (link.target === '_blank' || event.metaKey || event.ctrlKey) {
            return;
        }

        const href = link.getAttribute('href');

        if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:')) {
            return;
        }

        const url = new URL(link.href, window.location.href);

        if (url.origin === window.location.origin) {
            showOverlay();
        }
    });

    // Restore the overlay to hidden if the page is served from the
    // back/forward cache (otherwise a "back" navigation could show a
    // spinner frozen from before the previous submit).
    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
        }
    });
});

/**
 * Light/dark toggle. The chosen theme is written to a cookie (not
 * localStorage) so the server can render the correct `dark` class on <html>
 * on every request — no flash of the wrong theme on navigation, and no
 * inline bootstrap script needed under the CSP.
 */
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('theme-toggle');

    if (!toggle) {
        return;
    }

    toggle.addEventListener('click', () => {
        const isDark = document.documentElement.classList.toggle('dark');
        const theme = isDark ? 'oscuro' : 'claro';

        document.cookie = `tema=${theme};path=/;max-age=31536000;samesite=lax`;
        toggle.setAttribute('aria-label', isDark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro');
    });
});

/**
 * Sidebar colapsable (escritorio). Igual que el tema, el estado se guarda en
 * una cookie para que el servidor renderice el ancho correcto de una vez.
 */
document.addEventListener('DOMContentLoaded', () => {
    const toggles = document.querySelectorAll('[data-sidebar-toggle]');

    if (!toggles.length) {
        return;
    }

    const setLabel = (collapsed) => {
        const text = collapsed ? 'Expandir' : 'Colapsar';
        toggles.forEach((toggle) => {
            toggle.setAttribute('aria-label', `${text} barra lateral`);
            const label = toggle.querySelector('.sidebar-label');
            if (label) {
                label.textContent = text;
            }
        });
    };

    setLabel(document.documentElement.classList.contains('sidebar-collapsed'));

    toggles.forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const isCollapsed = document.documentElement.classList.toggle('sidebar-collapsed');

            document.cookie = `sidebar=${isCollapsed ? 'colapsado' : 'expandido'};path=/;max-age=31536000;samesite=lax`;
            setLabel(isCollapsed);
        });
    });
});
