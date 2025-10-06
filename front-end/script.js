// Função auxiliar para usar requestAnimationFrame (rAF) com segurança
function safeRAF(callback) {
  let isTicking = false;
  let lastScrollY = window.pageYOffset;

  window.addEventListener('scroll', () => {
    lastScrollY = window.pageYOffset;
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

  // Configura Intersection Observer para detectar elementos na viewport
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

  // Adiciona classes de animação aos elementos
  addAnimationClasses() {
    this.animateElements(
      ".header-logo, .header-title, .header-menu, .header-buttons",
      "fade-in-down",
      0.1
    );
    this.animateElements(
      ".main-title, .main-subtext, .main-cta",
      "fade-in-up",
      0.2
    );
    this.animateElements(".menu-item", "fade-in-left", 0.1);
    this.animateElements(".section-title", "fade-in-down", 0.2);
    this.animateElements(".section-subtitle", "fade-in-up", 0.2, 0.3);
    this.animateElements(".product-card", "fade-in-up", 0.2, 0.5);
    this.animateSingleElement(".about-description", "fade-in-up", 0.3);
    this.animateElements(".team-member", "fade-in-up", 0.15, 0.3);
    this.animateElements(".contact-item", "fade-in-up", 0.2, 0.3);
    this.animateSections();
  }

  // Anima múltiplos elementos
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

  // Anima um único elemento
  animateSingleElement(selector, animationClass, delay) {
    const element = document.querySelector(selector);
    if (element) {
      element.classList.add("scroll-animate", animationClass);
      element.style.animationDelay = `${delay}s`;
      this.observer.observe(element);
    }
  }

  // Anima todas as seções exceto a primeira
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

  // Configura scroll suave para links âncora
  setupSmoothScroll() {
    const navLinks = document.querySelectorAll('a[href^="#"]:not(.menu-link)');

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
    // Usando rAF para melhor performance no scroll
    safeRAF(this.updateParallax.bind(this));
  }

  // Atualiza posições dos elementos parallax
  updateParallax(scrolled) {
    const parallaxElements = document.querySelectorAll(".parallax");

    parallaxElements.forEach((element) => {
      if (element) {
        const speed = element.dataset.speed || 0.5;
        // Se scrolled não for passado, pega o valor atual
        const currentScroll = scrolled !== undefined ? scrolled : window.pageYOffset;
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

  // Cria elemento da barra de progresso
  createProgressBar() {
    this.progressBar = document.createElement("div");
    this.progressBar.className = "scroll-progress";
    document.body.appendChild(this.progressBar);
  }

  init() {
    this.createProgressBar();
    // Usando rAF para melhor performance no scroll
    safeRAF(this.updateProgress.bind(this));
  }

  // Atualiza largura da barra baseada no scroll
  updateProgress(scrollTop) {
    if (!this.progressBar) return;

    // Se scrollTop não for passado, pega o valor atual
    const currentScrollTop = scrollTop !== undefined ? scrollTop : window.pageYOffset;

    const docHeight =
      document.documentElement.scrollHeight - window.innerHeight;
    const scrollPercent = (currentScrollTop / docHeight) * 100;

    this.progressBar.style.width = `${scrollPercent}%`;
  }
}

// Gerencia funcionalidade do menu dropdown
class MenuManager {
  constructor() {
    this.menuToggleBtn = null;
    this.menuDropdown = null;
    this.overlay = null;
    this.menuItems = null;
    this.isMenuOpen = false;
    this.init();
  }

  init() {
    this.menuToggleBtn = document.getElementById("menu-toggle-btn");
    this.menuDropdown = document.getElementById("header-menu-dropdown");

    if (!this.menuToggleBtn || !this.menuDropdown) {
      console.error("Elementos do menu não encontrados");
      return;
    }

    this.menuItems = this.menuDropdown.querySelectorAll(".menu-link");
    this.createOverlay();
    this.setupEventListeners();
    this.setupMenuCloseOnClick();
  }

  // Cria overlay para fechar menu
  createOverlay() {
    this.overlay = document.createElement("div");
    this.overlay.className = "menu-overlay";
    document.body.appendChild(this.overlay);
  }

  // Alterna visibilidade do menu
  toggleMenuDropdown() {
    if (this.isMenuOpen) {
      this.closeMenu();
    } else {
      this.openMenu();
    }
  }

  // Abre o menu
  openMenu() {
    this.isMenuOpen = true;
    this.menuDropdown.classList.add("menu-open");
    this.menuDropdown.style.display = "block";

    if (this.overlay) {
      this.overlay.classList.add("active");
    }

    this.menuToggleBtn.setAttribute("aria-expanded", "true");
    this.menuToggleBtn.setAttribute("aria-label", "Fechar Menu");
    document.body.style.overflow = "hidden";

    // Anima os itens do menu
    // Link interno (#)
    if (targetHref && targetHref.startsWith("#")) {
      e.preventDefault();
      setTimeout(() => {
      const targetEl = document.querySelector(targetHref);
      if (targetEl) {
        targetEl.scrollIntoView({ behavior: "smooth", block: "start" });
      }
      }, 400);
    }
  }

  // Fecha o menu
  closeMenu() {
    this.isMenuOpen = false;
    this.menuDropdown.classList.remove("menu-open");

    if (this.overlay) {
      this.overlay.classList.remove("active");
    }

    this.menuToggleBtn.setAttribute("aria-expanded", "false");
    this.menuToggleBtn.setAttribute("aria-label", "Abrir Menu");
    document.body.style.overflow = "";

    // Remove animação dos itens
    if (this.menuItems && this.menuItems.length > 0) {
      this.menuItems.forEach((item) => {
        const listItem = item.closest(".menu-item");
        if (listItem) {
          listItem.classList.remove("animate-in");
        }
      });
    }

    // Aguarda animação CSS antes de esconder
    setTimeout(() => {
      if (!this.isMenuOpen) {
        this.menuDropdown.style.display = "none";
      }
    }, 300);
  }

  // Configura fechamento do menu ao clicar em itens
  setupMenuCloseOnClick() {
    if (!this.menuItems || this.menuItems.length === 0) return;

    this.menuItems.forEach((item) => {
      item.addEventListener("click", (e) => {
        const targetHref = item.getAttribute("href");

        // Fecha o menu imediatamente
        this.closeMenu();

        // Link interno (#)
        if (targetHref && targetHref.startsWith("#")) {
          e.preventDefault();
          setTimeout(() => {
            const targetEl = document.querySelector(targetHref);
            if (targetEl) {
              targetEl.scrollIntoView({ behavior: "smooth", block: "start" });
            }
          }, 300);
        }
        // Link externo será tratado naturalmente pelo navegador
      });
    });
  }

  // Configura event listeners do menu
  setupEventListeners() {
    // Click no botão toggle
    this.menuToggleBtn.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      this.toggleMenuDropdown();
    });

    // Click no overlay
    if (this.overlay) {
      this.overlay.addEventListener("click", () => {
        this.closeMenu();
      });
    }

    // Tecla Escape
    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape" && this.isMenuOpen) {
        this.closeMenu();
        this.menuToggleBtn.focus();
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
