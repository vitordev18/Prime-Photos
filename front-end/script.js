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
      threshold: 0.1,
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

  // Configura scroll suave para links âncora (exceto links do menu, que já tratam isso no MenuManager)
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
        // Se não existir, não faz preventDefault, deixa o link agir normalmente
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
    window.addEventListener("scroll", () => {
      this.updateParallax();
    });
  }

  // Atualiza posições dos elementos parallax
  updateParallax() {
    const scrolled = window.pageYOffset;
    const parallaxElements = document.querySelectorAll(".parallax");

    parallaxElements.forEach((element) => {
      if (element) {
        const speed = element.dataset.speed || 0.5;
        const yPos = -(scrolled * speed);
        element.style.transform = `translateY(${yPos}px)`;
      }
    });
  }
}

// Exibe barra de progresso do scroll no topo
class ScrollProgress {
  constructor() {
    this.createProgressBar();
    this.init();
  }

  // Cria elemento da barra de progresso
  createProgressBar() {
    const progressBar = document.createElement("div");
    progressBar.className = "scroll-progress";
    document.body.appendChild(progressBar);
  }

  init() {
    window.addEventListener("scroll", () => {
      this.updateProgress();
    });
  }

  // Atualiza largura da barra baseada no scroll
  updateProgress() {
    const scrollTop = window.pageYOffset;
    const docHeight =
      document.documentElement.scrollHeight - window.innerHeight;
    const scrollPercent = (scrollTop / docHeight) * 100;

    const progressBar = document.querySelector(".scroll-progress");
    if (progressBar) {
      progressBar.style.width = `${scrollPercent}%`;
    }
  }
}

// Gerencia funcionalidade do menu dropdown - VERSÃO CORRIGIDA
class MenuManager {
  constructor() {
    this.menuToggleBtn = null;
    this.menuDropdown = null;
    this.overlay = null;
    this.isMenuOpen = false;
    this.init();
  }

  init() {
    this.menuToggleBtn = document.getElementById("menu-toggle-btn");
    this.menuDropdown = document.getElementById("header-menu-dropdown");

    if (this.menuToggleBtn && this.menuDropdown) {
      this.createOverlay();
      this.setupEventListeners();
      this.setupMenuCloseOnClick();
    }
  }

  // Cria overlay para fechar menu
  createOverlay() {
    this.overlay = document.createElement("div");
    this.overlay.className = "menu-overlay";
    document.body.appendChild(this.overlay);
  }

  // Alterna visibilidade do menu
  toggleMenuDropdown() {
    if (!this.menuToggleBtn || !this.menuDropdown) {
      console.error("Elementos do menu não encontrados");
      return;
    }

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
    this.overlay.classList.add("active");
    this.menuToggleBtn.setAttribute("aria-expanded", "true");
    this.menuToggleBtn.setAttribute("aria-label", "Fechar Menu");
    document.body.style.overflow = "hidden";

    // Anima os itens do menu
    const menuItems = this.menuDropdown.querySelectorAll(".menu-item");
    menuItems.forEach((item, index) => {
      setTimeout(() => {
        item.classList.add("animate-in");
      }, index * 100);
    });
  }

  // Fecha o menu
  closeMenu() {
    this.isMenuOpen = false;
    this.menuDropdown.classList.remove("menu-open");
    this.overlay.classList.remove("active");
    this.menuToggleBtn.setAttribute("aria-expanded", "false");
    this.menuToggleBtn.setAttribute("aria-label", "Abrir Menu");
    document.body.style.overflow = "";

    // Remove animação dos itens
    const menuItems = this.menuDropdown.querySelectorAll(".menu-item");
    menuItems.forEach((item) => {
      item.classList.remove("animate-in");
    });
  }

  // Configura fechamento do menu ao clicar em itens
  setupMenuCloseOnClick() {
    const menuItems = document.querySelectorAll(
      ".header-menu-dropdown .menu-item a"
    );
    menuItems.forEach((item) => {
      item.addEventListener("click", (e) => {
        const targetId = item.getAttribute("href");
        if (targetId && targetId.startsWith("#")) {
          const targetElement = document.querySelector(targetId);
          if (targetElement) {
            e.preventDefault();
            // Fecha o menu imediatamente (mas mantém o body travado)
            this.menuDropdown.classList.remove("menu-open");
            this.overlay.classList.remove("active");
            this.menuToggleBtn.setAttribute("aria-expanded", "false");
            this.menuToggleBtn.setAttribute("aria-label", "Abrir Menu");
            // Scroll suave
            targetElement.scrollIntoView({
              behavior: "smooth",
              block: "start",
            });
            // Só libera o scroll do body após o scroll
            setTimeout(() => {
              document.body.style.overflow = "";
              // Remove animação dos itens
              const menuItems =
                this.menuDropdown.querySelectorAll(".menu-item");
              menuItems.forEach((item) => {
                item.classList.remove("animate-in");
              });
              this.isMenuOpen = false;
            }, 600);
          }
        } else {
          // Se não for âncora, fecha normalmente (ex: links externos)
          this.closeMenu();
        }
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
    this.overlay.addEventListener("click", () => {
      this.closeMenu();
    });

    // Tecla Escape
    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape" && this.isMenuOpen) {
        this.closeMenu();
        this.menuToggleBtn.focus();
      }
    });

    // Fecha menu ao rolar
    window.addEventListener("scroll", () => {
      if (this.isMenuOpen) {
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

  // Inicializa o menu manager
  window.menuManager = new MenuManager();

  console.log("Prime Photos - Sistemas inicializados");
});
