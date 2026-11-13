// Bella Girls - Scripts de interacción, Tema Oficial (Claro / Nocturno) y Sidebar Hamburguesa

const THEME_STORAGE_KEY = 'bg-theme';
const SIDEBAR_STORAGE_KEY = 'bg-sidebar';

/* ==========================================================================
   1. GESTIÓN DE TEMA (CLARO / NOCTURNO)
   ========================================================================== */

/**
 * Obtiene el tema guardado en localStorage de forma segura.
 */
function getStoredTheme() {
    try {
        return localStorage.getItem(THEME_STORAGE_KEY);
    } catch (e) {
        return null;
    }
}

/**
 * Guarda el tema en localStorage de forma segura.
 */
function setStoredTheme(theme) {
    try {
        localStorage.setItem(THEME_STORAGE_KEY, theme);
    } catch (e) {
        // En caso de que el almacenamiento esté bloqueado o restringido
    }
}

/**
 * Determina el tema preferido:
 * 1. Elección guardada en localStorage.
 * 2. Preferencia del sistema operativo (prefers-color-scheme).
 */
function getPreferredTheme() {
    const stored = getStoredTheme();
    if (stored === 'light' || stored === 'dark') {
        return stored;
    }
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        return 'dark';
    }
    return 'light';
}

/**
 * Aplica el tema al documento (atributo data-theme en <html>).
 */
function applyTheme(theme) {
    const validTheme = theme === 'dark' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', validTheme);

    // Actualizar atributos de accesibilidad en los botones de tema
    const buttons = document.querySelectorAll('.theme-toggle-btn');
    const label = validTheme === 'dark' ? 'Cambiar a modo claro' : 'Cambiar a modo nocturno';
    buttons.forEach(btn => {
        btn.setAttribute('aria-label', 'Cambiar tema');
        btn.setAttribute('title', label);
    });
}

/**
 * Alterna entre el tema claro y nocturno.
 */
function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme') || getPreferredTheme();
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    applyTheme(newTheme);
    setStoredTheme(newTheme);
}

/**
 * Inicialización de oyentes del tema y botones interactivos.
 */
function initTheme() {
    const theme = getPreferredTheme();
    applyTheme(theme);

    // Asignar eventos de clic y teclado a todos los botones de cambio de tema
    const buttons = document.querySelectorAll('.theme-toggle-btn');
    buttons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            toggleTheme();
        });

        btn.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleTheme();
            }
        });
    });

    // Si el usuario no ha forzado un tema en localStorage, escuchar cambios del sistema
    if (window.matchMedia) {
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        mediaQuery.addEventListener('change', function(e) {
            if (!getStoredTheme()) {
                applyTheme(e.matches ? 'dark' : 'light');
            }
        });
    }
}

/* ==========================================================================
   2. GESTIÓN DEL MENÚ LATERAL (SIDEBAR) EXTENDER / COLAPSAR
   ========================================================================== */

/**
 * Verifica si la pantalla actual corresponde a escritorio (>= 992px)
 */
function isDesktop() {
    return window.innerWidth >= 992;
}

/**
 * Lee el estado del sidebar guardado en localStorage.
 */
function getStoredSidebar() {
    try {
        return localStorage.getItem(SIDEBAR_STORAGE_KEY);
    } catch (e) {
        return null;
    }
}

/**
 * Guarda el estado del sidebar en localStorage.
 */
function setStoredSidebar(state) {
    try {
        localStorage.setItem(SIDEBAR_STORAGE_KEY, state);
    } catch (e) {}
}

/**
 * Actualiza los atributos aria-expanded y tooltips de los botones hamburguesa.
 */
function updateSidebarAria() {
    const sidebar = document.getElementById('sidebar');
    const toggleBtns = document.querySelectorAll('.sidebar-toggle-btn');
    if (!toggleBtns.length) return;

    let isExpanded = true;
    if (isDesktop()) {
        isExpanded = !document.documentElement.classList.contains('sidebar-collapsed');
    } else {
        isExpanded = sidebar ? sidebar.classList.contains('show') : false;
    }

    toggleBtns.forEach(btn => {
        btn.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
        btn.setAttribute('title', isExpanded ? 'Ocultar menú lateral' : 'Mostrar menú lateral');
    });
}

/**
 * Cierra el sidebar en vista móvil y oculta el overlay oscuro.
 */
function closeMobileSidebar() {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    if (sidebar) sidebar.classList.remove('show');
    if (backdrop) backdrop.classList.remove('show');
    updateSidebarAria();
}

/**
 * Abre el sidebar en vista móvil mostrando el overlay oscuro.
 */
function openMobileSidebar() {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    if (sidebar) sidebar.classList.add('show');
    if (backdrop) backdrop.classList.add('show');
    updateSidebarAria();
}

/**
 * Alterna entre expandir y ocultar/colapsar el sidebar:
 * - En escritorio (>= 992px): Alterna clase 'sidebar-collapsed' (72px solo iconos).
 * - En móvil (< 992px): Alterna clase 'show' con fondo oscuro deslizante.
 */
function toggleSidebar(force) {
    const sidebar = document.getElementById('sidebar');
    if (!sidebar) return;

    if (isDesktop()) {
        // Comportamiento de escritorio: alternar modo colapsado (72px)
        const isCurrentlyCollapsed = document.documentElement.classList.contains('sidebar-collapsed');
        let shouldCollapse;
        if (typeof force === 'boolean') {
            shouldCollapse = !force;
        } else {
            shouldCollapse = !isCurrentlyCollapsed;
        }

        if (shouldCollapse) {
            document.documentElement.classList.add('sidebar-collapsed');
            setStoredSidebar('collapsed');
        } else {
            document.documentElement.classList.remove('sidebar-collapsed');
            setStoredSidebar('expanded');
        }
        updateSidebarAria();
    } else {
        // Comportamiento móvil: panel deslizante con overlay
        const isCurrentlyOpen = sidebar.classList.contains('show');
        let shouldOpen;
        if (typeof force === 'boolean') {
            shouldOpen = force;
        } else {
            shouldOpen = !isCurrentlyOpen;
        }

        if (shouldOpen) {
            openMobileSidebar();
        } else {
            closeMobileSidebar();
        }
    }
}

/**
 * Inicializa los eventos del Sidebar (Hamburguesa, Backdrop, Tecla Escape).
 */
function initSidebar() {
    // Restaurar estado de escritorio al cargar
    if (isDesktop()) {
        const stored = getStoredSidebar();
        if (stored === 'collapsed') {
            document.documentElement.classList.add('sidebar-collapsed');
        } else if (stored === 'expanded') {
            document.documentElement.classList.remove('sidebar-collapsed');
        }
    }

    updateSidebarAria();

    // Eventos de clic en todos los botones hamburguesa
    const toggleBtns = document.querySelectorAll('.sidebar-toggle-btn');
    toggleBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            toggleSidebar();
        });
    });

    // Clic en el fondo oscuro tenue (backdrop) para cerrar en móvil
    const backdrop = document.getElementById('sidebarBackdrop');
    if (backdrop) {
        backdrop.addEventListener('click', function() {
            closeMobileSidebar();
        });
    }

    // Cerrar con la tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' || e.key === 'Esc') {
            if (!isDesktop()) {
                closeMobileSidebar();
            }
        }
    });

    // Cerrar sidebar en móvil al hacer clic en un enlace de navegación
    const navLinks = document.querySelectorAll('#sidebar .nav-link-custom');
    navLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            if (!isDesktop()) {
                closeMobileSidebar();
            }
        });
    });

    // Al redimensionar la ventana, limpiar estados inconsistentes
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (isDesktop()) {
                // Al volver a escritorio, cerrar el overlay móvil
                const backdrop = document.getElementById('sidebarBackdrop');
                if (backdrop) backdrop.classList.remove('show');
                const sidebar = document.getElementById('sidebar');
                if (sidebar) sidebar.classList.remove('show');

                // Asegurar estado guardado en escritorio
                if (getStoredSidebar() === 'collapsed') {
                    document.documentElement.classList.add('sidebar-collapsed');
                } else {
                    document.documentElement.classList.remove('sidebar-collapsed');
                }
            } else {
                // En móvil, la clase de colapso de escritorio no debe achicar el drawer
                document.documentElement.classList.remove('sidebar-collapsed');
            }
            updateSidebarAria();
        }, 100);
    });
}

/* ==========================================================================
   3. INICIALIZACIÓN GLOBAL
   ========================================================================== */
document.addEventListener('DOMContentLoaded', function() {
    initTheme();
    initSidebar();
});
