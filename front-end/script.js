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
      rootMargin: '0px 0px -50px 0px'
    };

    this.observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('animate-in');
          this.observer.unobserve(entry.target);
        }
      });
    }, options);
  }

  // Adiciona classes de animação aos elementos
  addAnimationClasses() {
    this.animateElements('.header-logo, .header-title, .header-menu, .header-buttons', 'fade-in-down', 0.1);
    this.animateElements('.main-title, .main-subtext, .main-cta', 'fade-in-up', 0.2);
    this.animateElements('.menu-item', 'fade-in-left', 0.1);
    this.animateElements('.section-title', 'fade-in-down', 0.2);
    this.animateElements('.section-subtitle', 'fade-in-up', 0.2, 0.3);
    this.animateElements('.product-card', 'fade-in-up', 0.2, 0.5);
    this.animateSingleElement('.about-description', 'fade-in-up', 0.3);
    this.animateElements('.team-member', 'fade-in-up', 0.15, 0.3);
    this.animateElements('.contact-item', 'fade-in-up', 0.2, 0.3);
    this.animateSections();
  }

  // Anima múltiplos elementos
  animateElements(selector, animationClass, delayMultiplier, baseDelay = 0) {
    const elements = document.querySelectorAll(selector);
    elements.forEach((el, index) => {
      if (el) {
        el.classList.add('scroll-animate', animationClass);
        el.style.animationDelay = `${(index * delayMultiplier) + baseDelay}s`;
        this.observer.observe(el);
      }
    });
  }

  // Anima um único elemento
  animateSingleElement(selector, animationClass, delay) {
    const element = document.querySelector(selector);
    if (element) {
      element.classList.add('scroll-animate', animationClass);
      element.style.animationDelay = `${delay}s`;
      this.observer.observe(element);
    }
  }

  // Anima todas as seções exceto a primeira
  animateSections() {
    const sections = document.querySelectorAll('section');
    sections.forEach((section, index) => {
      if (section && index > 0) {
        section.classList.add('scroll-animate', 'fade-in-up');
        section.style.animationDelay = '0.2s';
        this.observer.observe(section);
      }
    });
  }

  // Configura scroll suave para links âncora
  setupSmoothScroll() {
    const navLinks = document.querySelectorAll('a[href^="#"]');
    
    navLinks.forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        const targetId = link.getAttribute('href');
        const targetElement = document.querySelector(targetId);
        
        if (targetElement) {
          targetElement.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
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
    window.addEventListener('scroll', () => {
      this.updateParallax();
    });
  }

  // Atualiza posições dos elementos parallax
  updateParallax() {
    const scrolled = window.pageYOffset;
    const parallaxElements = document.querySelectorAll('.parallax');
    
    parallaxElements.forEach(element => {
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
    const progressBar = document.createElement('div');
    progressBar.className = 'scroll-progress';
    document.body.appendChild(progressBar);
  }

  init() {
    window.addEventListener('scroll', () => {
      this.updateProgress();
    });
  }

  // Atualiza largura da barra baseada no scroll
  updateProgress() {
    const scrollTop = window.pageYOffset;
    const docHeight = document.documentElement.scrollHeight - window.innerHeight;
    const scrollPercent = (scrollTop / docHeight) * 100;
    
    const progressBar = document.querySelector('.scroll-progress');
    if (progressBar) {
      progressBar.style.width = `${scrollPercent}%`;
    }
  }
}

// Gerencia funcionalidade do menu dropdown
class MenuManager {
  constructor() {
    this.menuToggleBtn = null;
    this.menuDropdown = null;
    this.init();
  }

  init() {
    this.menuToggleBtn = document.getElementById('menu-toggle-btn');
    this.menuDropdown = document.getElementById('header-menu-dropdown');

    if (this.menuToggleBtn && this.menuDropdown) {
      this.setupMenuCloseOnClick();
      this.setupEventListeners();
    }
  }

  // Alterna visibilidade do menu
  toggleMenuDropdown() {
    if (!this.menuToggleBtn || !this.menuDropdown) {
      console.error('Elementos do menu não encontrados');
      return;
    }

    const isExpanded = this.menuToggleBtn.getAttribute('aria-expanded') === 'true';

    if (isExpanded) {
      this.closeMenu();
    } else {
      this.openMenu();
    }
  }

  // Abre o menu
  openMenu() {
    this.menuDropdown.classList.add('menu-open');
    this.menuToggleBtn.setAttribute('aria-expanded', 'true');
    this.menuToggleBtn.setAttribute('aria-label', 'Fechar Menu');
    document.body.style.overflow = 'hidden'; // Evita o scroll da página
  }

  // Fecha o menu
  closeMenu() {
    this.menuDropdown.classList.remove('menu-open');
    this.menuToggleBtn.setAttribute('aria-expanded', 'false');
    this.menuToggleBtn.setAttribute('aria-label', 'Abrir Menu');
    document.body.style.overflow = ''; // Restaura o scroll
  }

  // Configura fechamento do menu ao clicar em itens
  setupMenuCloseOnClick() {
    const menuItems = document.querySelectorAll('.header-menu-dropdown .menu-item a');

    menuItems.forEach(item => {
      item.addEventListener('click', () => {
        this.closeMenu();
      });
    });
  }

  // Configura event listeners do menu
  setupEventListeners() {
    document.addEventListener('click', (event) => {
      this.handleOutsideClick(event);
    });

    document.addEventListener('keydown', (event) => {
      this.handleEscapeKey(event);
    });

    // Adicionado: Event listener para o botão de toggle
    this.menuToggleBtn.addEventListener('click', (e) => {
      this.toggleMenuDropdown();
    });
  }

  // Gerencia cliques fora do menu
  handleOutsideClick(event) {
    if (!this.menuToggleBtn.contains(event.target) && !this.menuDropdown.contains(event.target)) {
      if (this.menuToggleBtn.getAttribute('aria-expanded') === 'true') {
        this.closeMenu();
      }
    }
  }

  // Gerencia tecla Escape
  handleEscapeKey(event) {
    if (event.key === 'Escape') {
      if (this.menuToggleBtn.getAttribute('aria-expanded') === 'true') {
        this.closeMenu();
        this.menuToggleBtn.focus();
      }
    }
  }
}

// Inicializa funcionalidades quando DOM carrega
document.addEventListener('DOMContentLoaded', () => {
  document.body.classList.add('loaded');
  
  new ScrollAnimations();
  new ParallaxEffect();
  new ScrollProgress();

  window.menuManager = new MenuManager();

  console.log('Prime Photos - Sistemas inicializados');
});