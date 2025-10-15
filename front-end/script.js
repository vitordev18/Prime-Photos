// CONFIGURAÇÕES GLOBAIS
const CONFIG = {
    animation: {
        threshold: 0.2,
        rootMargin: "0px 0px -50px 0px",
        menuCloseDelay: 400
    },
    selectors: {
        menu: {
            openBtn: "menu-open-btn",
            closeBtn: "menu-close-btn",
            overlay: "main-menu-overlay",
            links: ".menu-link-item"
        }
    }
};

// UTILITÁRIOS
class Utils {
    // Debounce para otimizar performance
    static debounce(func, wait, immediate) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                timeout = null;
                if (!immediate) func(...args);
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func(...args);
        };
    }

    // Safe querySelector com fallback
    static $(selector, parent = document) {
        const element = parent.querySelector(selector);
        if (!element) {
            console.warn(`Elemento não encontrado: ${selector}`);
        }
        return element;
    }

    // Safe querySelectorAll
    static $$(selector, parent = document) {
        return Array.from(parent.querySelectorAll(selector));
    }

    // Verifica se elemento está visível na viewport
    static isElementInViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }
}

// ===== GERENCIADOR DE ANIMAÇÕES DE SCROLL =====
class ScrollAnimations {
    constructor() {
        this.animatedElements = [];
        this.observer = null;
        this.init();
    }

    init() {
        try {
            this.setupObserver();
            this.addAnimationClasses();
            this.setupSmoothScroll();
        } catch (error) {
            console.error('Erro ao inicializar ScrollAnimations:', error);
        }
    }

    setupObserver() {
        const options = {
            threshold: CONFIG.animation.threshold,
            rootMargin: CONFIG.animation.rootMargin,
        };

        this.observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("animate-in");
                    // Para melhor performance, para de observar após animação
                    this.observer.unobserve(entry.target);
                }
            });
        }, options);
    }

    addAnimationClasses() {
        const animationsConfig = [
            { 
                selector: ".header-logo, .header-buttons", 
                class: "fade-in-down", 
                delayMultiplier: 0.1, 
                baseDelay: 0 
            },
            { 
                selector: ".main-title, .main-subtext, .main-cta", 
                class: "fade-in-up", 
                delayMultiplier: 0.2, 
                baseDelay: 0 
            },
            { 
                selector: ".section-title", 
                class: "fade-in-down", 
                delayMultiplier: 0.2, 
                baseDelay: 0 
            },
            { 
                selector: ".product-card", 
                class: "fade-in-up", 
                delayMultiplier: 0.2, 
                baseDelay: 0.5 
            },
        ];

        animationsConfig.forEach(config => {
            this.animateElements(config.selector, config.class, config.delayMultiplier, config.baseDelay);
        });
        
        this.animateSections();
    }

    animateElements(selector, animationClass, delayMultiplier, baseDelay = 0) {
        const elements = Utils.$$(selector);
        
        elements.forEach((el, index) => {
            if (el) {
                el.classList.add("scroll-animate", animationClass);
                el.style.animationDelay = `${(index * delayMultiplier + baseDelay).toFixed(2)}s`;
                this.observer.observe(el);
            }
        });
    }

    animateSections() {
        const sections = Utils.$$("section");
        
        sections.forEach((section, index) => {
            if (section && index > 0) {
                section.classList.add("scroll-animate", "fade-in-up");
                section.style.animationDelay = "0.2s";
                this.observer.observe(section);
            }
        });
    }

    setupSmoothScroll() {
        const navLinks = Utils.$$('a[href^="#"]:not(.menu-link-item)');

        navLinks.forEach((link) => {
            link.addEventListener("click", (e) => {
                const targetId = link.getAttribute("href");
                
                // Validação do target
                if (!targetId || targetId === '#') return;
                
                const targetElement = Utils.$(targetId);

                if (targetElement) {
                    e.preventDefault();
                    
                    // Scroll suave com fallback
                    if ('scrollBehavior' in document.documentElement.style) {
                        targetElement.scrollIntoView({
                            behavior: "smooth",
                            block: "start",
                        });
                    } else {
                        // Fallback para browsers antigos
                        const targetPosition = targetElement.offsetTop;
                        window.scrollTo({
                            top: targetPosition,
                            behavior: 'smooth'
                        });
                    }
                }
            });
        });
    }
}

// GERENCIADOR DE MENU OVERLAY
class MenuManager {
    constructor() {
        this.menuOpenBtn = null;
        this.menuCloseBtn = null;
        this.menuOverlay = null;
        this.menuLinks = [];
        this.isMenuOpen = false;
        this.init();
    }

    init() {
        try {
            this.menuOpenBtn = document.getElementById(CONFIG.selectors.menu.openBtn);
            this.menuCloseBtn = document.getElementById(CONFIG.selectors.menu.closeBtn);
            this.menuOverlay = document.getElementById(CONFIG.selectors.menu.overlay);

            if (!this.menuOpenBtn || !this.menuOverlay) {
                console.warn("Elementos do menu Overlay não encontrados - funcionalidade desativada");
                return;
            }

            this.menuLinks = Utils.$$(CONFIG.selectors.menu.links, this.menuOverlay);
            this.setupEventListeners();
        } catch (error) {
            console.error('Erro ao inicializar MenuManager:', error);
        }
    }

    openMenu() {
        if (this.isMenuOpen) return;
        
        this.isMenuOpen = true;
        this.menuOverlay.classList.add("menu-open");
        document.body.style.overflow = "hidden";
        this.menuOpenBtn.setAttribute("aria-expanded", "true");
        
        // Foco no primeiro link do menu para acessibilidade
        if (this.menuLinks.length > 0) {
            setTimeout(() => this.menuLinks[0].focus(), 100);
        }
    }

    closeMenu() {
        if (!this.isMenuOpen) return;
        
        this.isMenuOpen = false;
        this.menuOverlay.classList.remove("menu-open");
        document.body.style.overflow = "";
        this.menuOpenBtn.setAttribute("aria-expanded", "false");
        
        // Retorna foco para o botão de abrir menu
        this.menuOpenBtn.focus();
    }

    toggleMenu() {
        if (this.isMenuOpen) {
            this.closeMenu();
        } else {
            this.openMenu();
        }
    }

    handleMenuLinkClick(e, link) {
        const targetHref = link.getAttribute("href");
        
        // Fecha o menu imediatamente
        this.closeMenu();
        
        // Se for link interno, manipula o scroll
        if (targetHref && targetHref.startsWith("#")) {
            e.preventDefault();
            
            setTimeout(() => {
                const targetElement = Utils.$(targetHref);
                if (targetElement) {
                    targetElement.scrollIntoView({ 
                        behavior: "smooth", 
                        block: "start" 
                    });
                }
            }, CONFIG.animation.menuCloseDelay);
        }
    }

    setupEventListeners() {
        // Abre/fecha menu
        this.menuOpenBtn.addEventListener("click", () => this.toggleMenu());

        // Fecha menu pelo botão X
        if (this.menuCloseBtn) {
            this.menuCloseBtn.addEventListener("click", () => this.closeMenu());
        }

        // Fecha menu e navega nos links
        this.menuLinks.forEach((link) => {
            link.addEventListener("click", (e) => this.handleMenuLinkClick(e, link));
        });

        // Fecha menu com ESC
        document.addEventListener("keydown", (event) => {
            if (event.key === "Escape" && this.isMenuOpen) {
                this.closeMenu();
            }
        });

        // Fecha menu ao redimensionar (com debounce)
        window.addEventListener("resize", Utils.debounce(() => {
            if (this.isMenuOpen && window.innerWidth > 768) {
                this.closeMenu();
            }
        }, 250));

        // Fecha menu ao clicar fora (overlay)
        this.menuOverlay.addEventListener("click", (e) => {
            if (e.target === this.menuOverlay) {
                this.closeMenu();
            }
        });
    }
}

// BARRA DE PROGRESSO DE SCROLL
class ScrollProgress {
    constructor() {
        this.progressBar = null;
        this.init();
    }

    createProgressBar() {
        this.progressBar = document.createElement("div");
        this.progressBar.className = "scroll-progress";
        this.progressBar.setAttribute("aria-hidden", "true");
        document.body.appendChild(this.progressBar);
    }

    init() {
        try {
            this.createProgressBar();
            this.setupScrollListener();
        } catch (error) {
            console.error('Erro ao inicializar ScrollProgress:', error);
        }
    }

    setupScrollListener() {
        let ticking = false;
        
        const updateProgress = () => {
            if (!this.progressBar) return;

            const currentScrollTop = window.scrollY || document.documentElement.scrollTop;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            
            if (docHeight === 0) return;

            const scrollPercent = Math.min((currentScrollTop / docHeight) * 100, 100);
            this.progressBar.style.width = `${scrollPercent}%`;
            
            ticking = false;
        };

        const requestTick = () => {
            if (!ticking) {
                requestAnimationFrame(updateProgress);
                ticking = true;
            }
        };

        window.addEventListener('scroll', requestTick, { passive: true });
        // Atualiza também no resize
        window.addEventListener('resize', requestTick, { passive: true });
    }
}

// EFEITO PARALLAX
class ParallaxEffect {
    constructor() {
        this.parallaxElements = [];
        this.init();
    }

    init() {
        try {
            this.parallaxElements = Utils.$$('.parallax');
            
            if (this.parallaxElements.length === 0) {
                return;
            }

            this.setupScrollListener();
        } catch (error) {
            console.error('Erro ao inicializar ParallaxEffect:', error);
        }
    }

    setupScrollListener() {
        let ticking = false;
        
        const updateParallax = () => {
            const scrolled = window.scrollY || document.documentElement.scrollTop;

            this.parallaxElements.forEach((element) => {
                const speed = parseFloat(element.dataset.speed) || 0.5;
                const yPos = -(scrolled * speed);
                element.style.transform = `translateY(${yPos}px)`;
            });
            
            ticking = false;
        };

        const requestTick = () => {
            if (!ticking) {
                requestAnimationFrame(updateParallax);
                ticking = true;
            }
        };

        window.addEventListener('scroll', requestTick, { passive: true });
    }
}

// INICIALIZAÇÃO DA APLICAÇÃO
class App {
    constructor() {
        this.components = [];
        this.init();
    }

    init() {
        try {
            // Marca body como carregado
            document.body.classList.add("loaded");

            // Inicializa componentes
            this.components = [
                new ScrollAnimations(),
                new ParallaxEffect(),
                new ScrollProgress(),
                new MenuManager()
            ];

            console.log('🚀 Prime Photos - Aplicação inicializada com sucesso!');
            
        } catch (error) {
            console.error('❌ Erro ao inicializar aplicação:', error);
        }
    }

    // Método para destruir componentes
    destroy() {
        this.components.forEach(component => {
            if (typeof component.destroy === 'function') {
                component.destroy();
            }
        });
    }
}

// INICIALIZAÇÃO 
document.addEventListener("DOMContentLoaded", () => {
    window.PrimePhotosApp = new App();
});