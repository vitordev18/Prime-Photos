// Função auxiliar para usar requestAnimationFrame (rAF) com segurança
function safeRAF(callback) {
  let isTicking = false;
  // Usando a API de scroll moderna
  let lastScrollY = window.scrollY || document.documentElement.scrollTop;

  window.addEventListener('scroll', () => {
    lastScrollY = window.scrollY || document.documentElement.scrollTop;
    if (!isTicking) {
      requestAnimationFrame(() => {
        callback(lastScrollY);
        isTicking = false;
      });
      isTicking = true;
    }
  });
  callback(lastScrollY);
}

// Gerencia animações baseadas em scroll usando Intersection Observer
class ScrollAnimations {
  constructor() {
    this.animatedElements = [];
    this.observer = null;
    this.init();
  }

  init() {
    this.setupObserver();
    this.addAnimationClasses();
    this.setupSmoothScroll();
  }

  setupObserver() {
    const options = {
      threshold: 0.2,
      rootMargin: "0px 0px -50px 0px",
    };

    this.observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("animate-in");
          this.observer.unobserve(entry.target);
        }
      });
    }, options);
  }

  addAnimationClasses() {
    const animationsConfig = [
      { selector: ".header-logo, .header-buttons", class: "fade-in-down", delayMultiplier: 0.1, baseDelay: 0 },
      { selector: ".main-title, .main-subtext, .main-cta", class: "fade-in-up", delayMultiplier: 0.2, baseDelay: 0 },
      { selector: ".section-title", class: "fade-in-down", delayMultiplier: 0.2, baseDelay: 0 },
      { selector: ".product-card", class: "fade-in-up", delayMultiplier: 0.2, baseDelay: 0.5 },
    ];

    animationsConfig.forEach(config => {
      this.animateElements(config.selector, config.class, config.delayMultiplier, config.baseDelay);
    });
    this.animateSections();
  }

  animateElements(selector, animationClass, delayMultiplier, baseDelay = 0) {
    const elements = document.querySelectorAll(selector);
    elements.forEach((el, index) => {
      if (el) {
        el.classList.add("scroll-animate", animationClass);
        el.style.animationDelay = `${index * delayMultiplier + baseDelay}s`;
        this.observer.observe(el);
      }
    });
  }

  animateSections() {
    const sections = document.querySelectorAll("section");
    sections.forEach((section, index) => {
      if (section && index > 0) {
        section.classList.add("scroll-animate", "fade-in-up");
        section.style.animationDelay = "0.2s";
        this.observer.observe(section);
      }
    });
  }

  setupSmoothScroll() {
    const navLinks = document.querySelectorAll('a[href^="#"]:not(.menu-link-item)'); 

    navLinks.forEach((link) => {
      link.addEventListener("click", (e) => {
        const targetId = link.getAttribute("href");
        const targetElement = document.querySelector(targetId);

        if (targetElement) {
          e.preventDefault();
          targetElement.scrollIntoView({
            behavior: "smooth",
            block: "start",
          });
        }
      });
    });
  }
}

// Cria efeitos parallax durante o scroll
class ParallaxEffect {
  constructor() {
    this.init();
  }

  init() {
    safeRAF(this.updateParallax.bind(this));
  }

  updateParallax(scrolled) {
    const parallaxElements = document.querySelectorAll(".parallax");

    parallaxElements.forEach((element) => {
      if (element) {
        const speed = element.dataset.speed || 0.5;
        const currentScroll = scrolled; 
        const yPos = -(currentScroll * speed);
        element.style.transform = `translateY(${yPos}px)`;
      }
    });
  }
}

// Exibe barra de progresso do scroll no topo do site
class ScrollProgress {
  constructor() {
    this.progressBar = null;
    this.init();
  }

  createProgressBar() {
    this.progressBar = document.createElement("div");
    this.progressBar.className = "scroll-progress";
    document.body.appendChild(this.progressBar);
  }

  init() {
    this.createProgressBar();
    safeRAF(this.updateProgress.bind(this));
  }

  updateProgress(scrollTop) {
    if (!this.progressBar) return;

    const currentScrollTop = scrollTop; 
    const docHeight =
      document.documentElement.scrollHeight - window.innerHeight;
      
    if (docHeight === 0) return;

    const scrollPercent = (currentScrollTop / docHeight) * 100;

    this.progressBar.style.width = `${scrollPercent}%`;
  }
}

// Gerencia a funcionalidade do Menu Overlay (Painel Lateral)
class MenuManager {
  constructor() {
    this.menuOpenBtn = null;
    this.menuCloseBtn = null;
    this.menuOverlay = null;
    this.menuLinks = null;
    this.isMenuOpen = false;
    this.init();
  }

  init() {
    this.menuOpenBtn = document.getElementById("menu-open-btn");
    this.menuCloseBtn = document.getElementById("menu-close-btn");
    this.menuOverlay = document.getElementById("main-menu-overlay");

    if (!this.menuOpenBtn || !this.menuOverlay) {
      console.error("Elementos do menu Overlay não encontrados");
      return;
    }

    this.menuLinks = this.menuOverlay.querySelectorAll(".menu-link-item");
    this.setupEventListeners();
  }
  
  openMenu() {
    this.isMenuOpen = true;
    this.menuOverlay.classList.add("menu-open");
    document.body.style.overflow = "hidden"; // Trava o scroll da página
    this.menuOpenBtn.setAttribute("aria-expanded", "true");
  }

  closeMenu() {
    this.isMenuOpen = false;
    this.menuOverlay.classList.remove("menu-open");
    document.body.style.overflow = ""; // Libera o scroll
    this.menuOpenBtn.setAttribute("aria-expanded", "false");
  }

  setupEventListeners() {
    // Abre o menu
    this.menuOpenBtn.addEventListener("click", () => {
      this.openMenu();
    });

    // Fecha o menu pelo botão X
    if (this.menuCloseBtn) {
      this.menuCloseBtn.addEventListener("click", () => {
        this.closeMenu();
      });
    }

    // Fecha o menu e navega ao clicar em um link
    this.menuLinks.forEach((link) => {
      link.addEventListener("click", (e) => {
        const targetHref = link.getAttribute("href");
        
        // 1. Fecha o menu imediatamente
        this.closeMenu();
        
        // 2. Se for um link interno (âncora #), manipula o scroll
        if (targetHref && targetHref.startsWith("#")) {
          e.preventDefault(); 
          
          // Usa o setTimeout para sincronizar com a transição CSS de 0.4s
          setTimeout(() => {
            const targetEl = document.querySelector(targetHref);
            if (targetEl) {
              targetEl.scrollIntoView({ behavior: "smooth", block: "start" });
            }
          }, 400); 
        }
      });
    });

    // Fecha o menu ao pressionar ESC
    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape" && this.isMenuOpen) {
        this.closeMenu();
      }
    });
    
    // Fecha menu ao redimensionar
    window.addEventListener("resize", () => {
      if (this.isMenuOpen) {
        this.closeMenu();
      }
    });
  }
}

// Inicializa funcionalidades quando DOM carrega
document.addEventListener("DOMContentLoaded", () => {
  document.body.classList.add("loaded");

  new ScrollAnimations();
  new ParallaxEffect();
  new ScrollProgress();
  new MenuManager();
});