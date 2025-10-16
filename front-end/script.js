document.addEventListener("DOMContentLoaded", () => {
    class Utils {
        static debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        static $(selector, parent = document) {
            return parent.querySelector(selector);
        }

        static $$(selector, parent = document) {
            return Array.from(parent.querySelectorAll(selector));
        }
    }

    /* Gerencia o menu lateral (overlay) */
    class MenuManager {
        constructor() {
            this.menuOpenBtn = Utils.$("#menu-open-btn");
            this.menuCloseBtn = Utils.$("#menu-close-btn");
            this.menuOverlay = Utils.$("#main-menu-overlay");
            
            if (!this.menuOpenBtn || !this.menuOverlay || !this.menuCloseBtn) {
                console.warn("Elementos essenciais do menu não encontrados. Funcionalidade desativada.");
                return;
            }

            this.isMenuOpen = false;
            this.init();
        }

        init() {
            this.menuOpenBtn.addEventListener("click", () => this.toggleMenu());
            this.menuCloseBtn.addEventListener("click", () => this.closeMenu());
            this.menuOverlay.addEventListener("click", (e) => {
                if (e.target === this.menuOverlay) this.closeMenu();
            });
            document.addEventListener("keydown", (e) => {
                if (e.key === "Escape" && this.isMenuOpen) this.closeMenu();
            });

            const menuLinks = Utils.$$('.menu-link-item', this.menuOverlay);
            menuLinks.forEach(link => {
                link.addEventListener('click', () => {
                    // Atraso sutil para dar tempo da transição do menu começar
                    setTimeout(() => this.closeMenu(), 150);
                });
            });
        }

        toggleMenu() {
            this.isMenuOpen ? this.closeMenu() : this.openMenu();
        }

        openMenu() {
            if (this.isMenuOpen) return;
            this.isMenuOpen = true;
            document.body.style.overflow = "hidden";
            this.menuOverlay.classList.add("menu-open");
            this.menuOpenBtn.setAttribute("aria-expanded", "true");
        }

        closeMenu() {
            if (!this.isMenuOpen) return;
            this.isMenuOpen = false;
            document.body.style.overflow = "";
            this.menuOverlay.classList.remove("menu-open");
            this.menuOpenBtn.setAttribute("aria-expanded", "false");
        }
    }

    /* Controla a barra de progresso de scroll no topo da página */
    class ScrollProgress {
        constructor() {
            this.progressBar = Utils.$(".scroll-progress");
            if (!this.progressBar) return;
            this.init();
        }

        init() {
            const updateProgress = () => {
                const scrollableHeight = document.documentElement.scrollHeight - window.innerHeight;
                if (scrollableHeight <= 0) {
                    this.progressBar.style.width = '0%';
                    return;
                }
                const progress = (window.scrollY / scrollableHeight) * 100;
                this.progressBar.style.width = `${progress}%`;
            };

            const debouncedUpdate = Utils.debounce(updateProgress, 10);
            window.addEventListener('scroll', debouncedUpdate, { passive: true });
            window.addEventListener('resize', debouncedUpdate, { passive: true });
        }
    }
    
    /* Gerencia animações de elementos ao entrarem na viewport */
    class ScrollAnimator {
        constructor() {
            this.elementsToAnimate = Utils.$$('.scroll-animate');
            if (this.elementsToAnimate.length === 0) return;
            this.init();
        }
        
        init() {
            const observer = new IntersectionObserver((entries, observerInstance) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                        // Para de observar o elemento após a animação para otimizar a performance
                        observerInstance.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1
            });

            this.elementsToAnimate.forEach(el => observer.observe(el));
        }
    }

    /* Classe principal da aplicação que inicializa todos os módulos */
    class App {
        constructor() {
            this.init();
        }

        init() {
            // Inicializa os componentes do front-end
            new MenuManager();
            new ScrollProgress();
            new ScrollAnimator();
            
            // Adiciona segurança a links externos
            this.secureExternalLinks();

            // Marca o corpo como carregado para transições de fade-in
            document.body.classList.add("loaded");
            console.log("🚀 Prime Photos App inicializado com sucesso!");
        }

        secureExternalLinks() {
            Utils.$$('a[target="_blank"]').forEach(link => {
                link.setAttribute('rel', 'noopener noreferrer');
            });
        }
    }

    // Inicializa a aplicação
    new App();
});