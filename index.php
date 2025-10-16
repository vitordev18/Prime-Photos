<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Prime Photos</title>
    <link rel="icon" type="image" href="/assets/Elementos/Camera COM FLASH.svg" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/front-end/styles/reset.css" />
    <link rel="stylesheet" href="/front-end/styles/style.css" />
    <link rel="stylesheet" href="/front-end/styles/media.css" />
  </head>

<body>
    <?php
    session_start();
    include "util.php";
    
    $usuarioLogado = obterUsuarioLogado();
    $loginLink = $usuarioLogado ? '#' : '/back-end/login.php';
    $loginText = $usuarioLogado ? 'Minha Conta' : 'Fazer Login';
    $loginIcon = $usuarioLogado ? 'User.svg' : 'Login.svg';
    ?>
    
    <nav class="main-menu-overlay" id="main-menu-overlay" aria-label="Menu principal de navegação">
        <button id="menu-close-btn" class="menu-close-btn" aria-label="Fechar menu de navegação">
            &times; 
        </button>
        <div class="menu-content">
            <ul class="menu-list">
                <li class="menu-item-link">
                    <a href="#inicio" class="menu-link-item">Início</a>
                </li>
                <li class="menu-item-link">
                    <a href="#produtos" class="menu-link-item">Produtos</a>
                </li>
                <li class="menu-item-link">
                    <a href="/front-end/sobre.html" class="menu-link-item">Sobre</a>
                </li>
                <li class="menu-item-link">
                    <a href="/front-end/sobre.html#equipe" class="menu-link-item">Equipe</a>
                </li>
                <li class="menu-item-link">
                    <a href="/front-end/mvv.html" class="menu-link-item">Missão, Visão e Valores</a>
                </li>
                <li class="menu-item-action">
                    <a href="/back-end/perfil.php" class="menu-action-link login-btn"><?php echo $loginText; ?></a>
                </li>
                <li class="menu-item-action">
                    <a href="/back-end/carrinho_compras.php" class="menu-action-link cart-btn">Carrinho de Compras</a>
                </li>
                <?php if ($usuarioLogado): ?>
                <li class="menu-item-action">
                    <a href="/back-end/logout.php" class="menu-action-link logout-btn">Sair</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    
    <header class="header">
      <div class="header-left">
        <button
          id="menu-open-btn"
          type="button"
          aria-label="Abrir menu expansivo">
          <img src="/assets/Elementos/Menu.svg" alt="Ícone de menu" class="header-icon" />
        </button>
        <a href="/index.php" aria-label="Voltar para a página principal Prime Photos">
            <img
                src="/assets/Logotipo/Logo PRIME PHOTOS (positivo).svg"
                alt="Logotipo Prime Photos"
                class="header-logo"
            />
        </a>
      </div>

      <div class="header-buttons">
        <a href="/back-end/carrinho_compras.php" aria-label="Abrir carrinho de compras" class="header-cart-btn">
          <img src="/assets/Elementos/Carrinho de Compra.svg" alt="ícone Carrinho de compras" class="header-icon" />
          <?php
          $quantidadeCarrinho = isset($_SESSION['carrinho']) ? count($_SESSION['carrinho']) : 0;
          if ($quantidadeCarrinho > 0): ?>
            <span class="carrinho-quantidade"><?php echo $quantidadeCarrinho; ?></span>
          <?php endif; ?>
        </a>
        <?php if ($usuarioLogado): ?>
          <div class="header-welcome">
            <span>Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></span>
            <a href="/back-end/logout.php" class="header-icon">
              <img src="/assets/Elementos/Logout.svg" alt="Sair" />
            </a>
          </div>
        <?php else: ?>
          <a href="/back-end/login.php" class="header-login-link">
            <img src="/assets/Elementos/Login.svg" alt="Fazer Login" class="header-icon" />
          </a>
        <?php endif; ?>
      </div>
    </header>

    <main class="main">
      <section id="inicio" class="main-section">
        <h1 class="main-description main-title">Não vendemos apenas fotos.</h1>
        <p class="main-subtext">
          Na Prime Photos, cada imagem é uma memória viva. Transforme momentos
          especiais em recordações tangíveis e eternize suas histórias com
          qualidade e emoção.
        </p>
        <a href="#produtos" class="main-cta">Ver produto</a>
      </section>

      <section id="produtos" class="products-section">
        <div class="section-container">
          <h2 class="section-title">Produto</h2>
          <ul class="products-grid">
            <li class="product-card">
              <article>
                <figure class="product-image">
                  <div class="image-placeholder">
                    <img
                      src="/assets/Elementos/Polaroid (vermelha).svg"
                      alt="Foto Polaroid"
                    />
                  </div>
                </figure>
                <h3 class="product-title">Foto Polaroid</h3>
                <p class="product-description">
                  Impressões de alta qualidade em papel fotográfico premium.
                </p>
                <p class="product-price">R$ 8,00</p>
                <p class="product-stock in-stock">Em estoque</p>
                <form method="POST" action="/back-end/adicionar_carrinho.php">
                  <input type="hidden" name="id_produto" value="2"> 
                  <button class="purchase-button" aria-label="Adicionar Fotos Polaroid ao carrinho">
                    Adicionar ao carrinho
                  </button>
                </form>
              </article>
            </li>
          </ul>
        </div>
      </section>
    </main>

    <footer class="footer">
      <div class="footer-content">
        <div class="footer-brand-social">
          <img src="/assets/Elementos/Camera-Polaroids (com flash).svg" alt="Logotipo Prime Photos" class="footer-logo" />
        </div>
        <div class="footer-links-group">
          <div class="footer-social-icons">
            <span class="footer-heading">Redes Sociais</span>
            <ul>
              <li>
                <a href="https://www.instagram.com/primephotos_00/" target="_blank" aria-label="Instagram">
                  <img src="/assets/Elementos/Instagram.svg" alt="Instagram" />
                </a>
              </li>
            </ul>
          </div>
          <div>
            <span class="footer-heading">Empresa</span>
            <ul>
              <li>
                <a href="/front-end/sobre.html">Sobre</a>
              </li>
              <li>
                <a href="/front-end/mvv.html">Missão, Visão e Valores</a>
              </li>
            </ul>
          </div>
          <div>
            <span class="footer-heading">Endereço</span>
            <ul>
              <li>
                <address>Av. Nações Unidas, 58-50 - Bauru, SP</address>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <span>&copy; 2025 Prime Photos. Todos os direitos reservados.</span>
        <span><a href="#">Configurações de Cookies</a></span>
      </div>
    </footer>

    <script src="/front-end/script.js"></script>
  </body>
</html>