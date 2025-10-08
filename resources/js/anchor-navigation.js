// resources/js/anchor-navigation.js

/**
 * Gestione smooth scroll per navigazione con anchor
 * Basato su Intersection Observer API per performance ottimale
 */
class AnchorNavigation {
    constructor() {
        this.init();
    }

    init() {
        // Gestisci il caricamento iniziale con hash
        this.handleInitialHash();

        // Gestisci i click sui link con anchor
        this.setupAnchorLinks();

        // Gestisci i cambiamenti di hash
        this.setupHashChangeListener();

        // Setup per Livewire navigation
        this.setupLivewireNavigation();
    }

    /**
     * Gestisce l'hash presente nell'URL al caricamento della pagina
     */
    handleInitialHash() {
        if (window.location.hash) {
            // Piccolo delay per assicurarsi che il DOM sia completamente caricato
            setTimeout(() => {
                this.scrollToElement(window.location.hash);
            }, 100);
        }
    }

    /**
     * Setup per tutti i link con anchor nella pagina
     */
    setupAnchorLinks() {
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[href*="#"]');

            if (!link) return;

            const url = new URL(link.href, window.location.origin);

            // Se è un link interno alla stessa pagina
            if (url.pathname === window.location.pathname && url.hash) {
                e.preventDefault();
                this.scrollToElement(url.hash);

                // Aggiorna l'URL senza ricaricare la pagina
                history.pushState(null, null, url.hash);
            }
        });
    }

    /**
     * Listener per cambiamenti dell'hash nell'URL
     */
    setupHashChangeListener() {
        window.addEventListener('hashchange', () => {
            this.scrollToElement(window.location.hash);
        });
    }

    /**
     * Setup specifico per Livewire navigation
     */
    setupLivewireNavigation() {
        // Livewire 3 events
        document.addEventListener('livewire:navigated', () => {
            if (window.location.hash) {
                // Delay necessario per attendere il rendering completo
                setTimeout(() => {
                    this.scrollToElement(window.location.hash);
                }, 150);
            }
        });

        // Per versioni precedenti di Livewire
        if (window.Livewire) {
            Livewire.hook('message.processed', (message, component) => {
                if (window.location.hash) {
                    this.scrollToElement(window.location.hash);
                }
            });
        }
    }

    /**
     * Effettua lo scroll verso l'elemento target
     * @param {string} hash - L'hash dell'elemento target
     */
    scrollToElement(hash) {
        if (!hash || hash === '#') return;

        const targetId = hash.replace('#', '');
        const targetElement = document.getElementById(targetId);

        if (targetElement) {
            // Calcola l'offset considerando header fissi
            const headerOffset = this.getHeaderOffset();
            const elementPosition = targetElement.getBoundingClientRect().top;
            const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

            // Smooth scroll con fallback per browser non supportati
            if ('scrollBehavior' in document.documentElement.style) {
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            } else {
                window.scrollTo(0, offsetPosition);
            }

            // Aggiungi classe per evidenziare temporaneamente l'elemento
            this.highlightElement(targetElement);
        }
    }

    /**
     * Calcola l'offset dell'header fisso se presente
     * @returns {number} L'altezza dell'header fisso
     */
    getHeaderOffset() {
        // Cerca elementi comuni per header fissi
        const header = document.querySelector('header.fixed, .sticky-header, nav.fixed-top');

        if (header) {
            return header.offsetHeight + 20; // +20px di padding
        }

        // Default offset
        return 80;
    }

    /**
     * Evidenzia temporaneamente l'elemento target
     * @param {HTMLElement} element - L'elemento da evidenziare
     */
    highlightElement(element) {
        // Aggiungi classe per animazione
        element.classList.add('anchor-highlight');

        // Rimuovi la classe dopo l'animazione
        setTimeout(() => {
            element.classList.remove('anchor-highlight');
        }, 2000);
    }
}

// Inizializza quando il DOM è pronto
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        new AnchorNavigation();
    });
} else {
    new AnchorNavigation();
}

// Export per uso in altri moduli se necessario
export default AnchorNavigation;