// Sistema de Animações de Scroll
// Classe principal para gerenciar todas as animações baseadas em scroll
class ScrollAnimations {
  constructor() {
    this.animatedElements = []; // Array para armazenar elementos animados
    this.observer = null; // Instância do Intersection Observer
    this.init(); // Inicializa o sistema
  }

  init() {
    // Inicializa o observador de interseção
    this.setupObserver();
    
    // Adiciona classes de animação aos elementos
    this.addAnimationClasses();
    
    // Configura scroll suave para links de navegação
    this.setupSmoothScroll();
  }

  // Configura o Intersection Observer para detectar quando elementos entram na viewport
  setupObserver() {
    const options = {
      threshold: 0.1, // Dispara quando 10% do elemento está visível
      rootMargin: '0px 0px -50px 0px' // Margem para ajustar o ponto de trigger
    };

    // Cria o observador que monitora elementos
    this.observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          // Adiciona classe de animação quando elemento entra na viewport
          entry.target.classList.add('animate-in');
          // Remove observação após animação para melhorar performance
          this.observer.unobserve(entry.target);
        }
      });
    }, options);
  }

  // Adiciona classes de animação a todos os elementos que devem ser animados
  addAnimationClasses() {
    // Elementos do cabeçalho (logo, título, menu, botões)
    const headerElements = document.querySelectorAll('.header-logo, .header-title, .header-menu, .header-buttons');
    headerElements.forEach((el, index) => {
      el.classList.add('scroll-animate', 'fade-in-down');
      el.style.animationDelay = `${index * 0.1}s`; // Efeito escalonado
      this.observer.observe(el);
    });

    // Elementos da seção principal (título, subtítulo, botão CTA)
    const mainElements = document.querySelectorAll('.main-title, .main-subtext, .main-cta');
    mainElements.forEach((el, index) => {
      el.classList.add('scroll-animate', 'fade-in-up');
      el.style.animationDelay = `${index * 0.2}s`; // Delay maior para efeito dramático
      this.observer.observe(el);
    });

    // Adiciona efeito escalonado aos itens do menu
    const menuItems = document.querySelectorAll('.menu-item');
    menuItems.forEach((item, index) => {
      item.classList.add('scroll-animate', 'fade-in-left');
      item.style.animationDelay = `${index * 0.1}s`;
      this.observer.observe(item);
    });

    // Animações das seções de produtos
    const sectionTitles = document.querySelectorAll('.section-title');
    sectionTitles.forEach((title, index) => {
      title.classList.add('scroll-animate', 'fade-in-down');
      title.style.animationDelay = `${index * 0.2}s`;
      this.observer.observe(title);
    });

    // Subtítulos das seções
    const sectionSubtitles = document.querySelectorAll('.section-subtitle');
    sectionSubtitles.forEach((subtitle, index) => {
      subtitle.classList.add('scroll-animate', 'fade-in-up');
      subtitle.style.animationDelay = `${(index * 0.2) + 0.3}s`; // Delay adicional
      this.observer.observe(subtitle);
    });

    // Animações dos cards de produtos
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach((card, index) => {
      card.classList.add('scroll-animate', 'fade-in-up');
      card.style.animationDelay = `${(index * 0.2) + 0.5}s`; // Delay maior para cards
      this.observer.observe(card);
    });

    // Animações da seção sobre nós
    const aboutDescription = document.querySelector('.about-description');
    if (aboutDescription) {
      aboutDescription.classList.add('scroll-animate', 'fade-in-up');
      aboutDescription.style.animationDelay = '0.3s';
      this.observer.observe(aboutDescription);
    }

    // Estatísticas com animação de escala
    const statItems = document.querySelectorAll('.stat-item');
    statItems.forEach((stat, index) => {
      stat.classList.add('scroll-animate', 'scale-in');
      stat.style.animationDelay = `${(index * 0.2) + 0.5}s`;
      this.observer.observe(stat);
    });

    // Animações da seção de contato
    const contactItems = document.querySelectorAll('.contact-item');
    contactItems.forEach((item, index) => {
      item.classList.add('scroll-animate', 'fade-in-up');
      item.style.animationDelay = `${(index * 0.2) + 0.3}s`;
      this.observer.observe(item);
    });

    // Adiciona animações às próprias seções
    const sections = document.querySelectorAll('section');
    sections.forEach((section, index) => {
      if (index > 0) { // Pula a primeira seção pois já está animada
        section.classList.add('scroll-animate', 'fade-in-up');
        section.style.animationDelay = '0.2s';
        this.observer.observe(section);
      }
    });
  }

  // Configura scroll suave para links de navegação internos
  setupSmoothScroll() {
    const navLinks = document.querySelectorAll('a[href^="#"]'); // Links que começam com #
    
    navLinks.forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault(); // Previne comportamento padrão
        const targetId = link.getAttribute('href');
        const targetElement = document.querySelector(targetId);
        
        if (targetElement) {
          // Scroll suave até o elemento alvo
          targetElement.scrollIntoView({
            behavior: 'smooth',
            block: 'start' // Alinha ao topo da viewport
          });
        }
      });
    });
  }
}

// Efeito Parallax para elementos de fundo
// Permite criar efeitos de profundidade durante o scroll
class ParallaxEffect {
  constructor() {
    this.init();
  }

  init() {
    // Adiciona listener de scroll para atualizar efeito parallax
    window.addEventListener('scroll', () => {
      this.updateParallax();
    });
  }

  // Atualiza a posição dos elementos parallax baseado no scroll
  updateParallax() {
    const scrolled = window.pageYOffset; // Posição atual do scroll
    const parallaxElements = document.querySelectorAll('.parallax');
    
    parallaxElements.forEach(element => {
      const speed = element.dataset.speed || 0.5; // Velocidade do parallax
      const yPos = -(scrolled * speed); // Calcula nova posição Y
      element.style.transform = `translateY(${yPos}px)`; // Aplica transformação
    });
  }
}

// Inicializa animações quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
  new ScrollAnimations(); // Inicia sistema de animações
  new ParallaxEffect(); // Inicia efeito parallax
  
  // Adiciona animação de carregamento
  document.body.classList.add('loaded');
});

// Indicador de Progresso de Scroll
// Mostra uma barra no topo indicando o progresso do scroll
class ScrollProgress {
  constructor() {
    this.createProgressBar(); // Cria a barra de progresso
    this.init(); // Inicializa o sistema
  }

  // Cria o elemento da barra de progresso
  createProgressBar() {
    const progressBar = document.createElement('div');
    progressBar.className = 'scroll-progress';
    document.body.appendChild(progressBar);
  }

  init() {
    // Adiciona listener de scroll para atualizar progresso
    window.addEventListener('scroll', () => {
      this.updateProgress();
    });
  }

  // Atualiza a largura da barra de progresso baseado na posição do scroll
  updateProgress() {
    const scrollTop = window.pageYOffset; // Posição atual do scroll
    const docHeight = document.documentElement.scrollHeight - window.innerHeight; // Altura total scrollável
    const scrollPercent = (scrollTop / docHeight) * 100; // Calcula porcentagem
    
    const progressBar = document.querySelector('.scroll-progress');
    if (progressBar) {
      progressBar.style.width = `${scrollPercent}%`; // Atualiza largura da barra
    }
  }
}

// Inicializa o indicador de progresso de scroll
document.addEventListener('DOMContentLoaded', () => {
  new ScrollProgress();
});