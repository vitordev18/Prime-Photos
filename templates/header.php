<?php
$linharoot = $_SERVER['DOCUMENT_ROOT'];
include_once "$linharoot/util.php";

session_start();


$usuarioLogado = obterUsuarioLogado();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo isset($page_title) ? $page_title . ' - Prime Photos' : 'Prime Photos'; ?></title>
    <link rel="icon" type="image" href="/assets/Elementos/Camera COM FLASH.svg" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/front-end/styles/reset.css" />
    <link rel="stylesheet" href="/front-end/styles/style.css" />
    <link rel="stylesheet" href="/front-end/styles/media.css" />

    <?php if (isset($page_css) && is_array($page_css)): ?>
        <?php foreach ($page_css as $css_file): ?>
            <link rel="stylesheet" href="<?php echo $css_file; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <div class="scroll-progress"></div>
    
    <nav class="main-menu-overlay" id="main-menu-overlay" aria-label="Menu principal de navegação">
        <button id="menu-close-btn" class="menu-close-btn" aria-label="Fechar menu de navegação">&times;</button>
        <div class="menu-content">
            <ul class="menu-list">
                <li class="menu-item-link"><a href="/index.php#inicio" class="menu-link-item">Início</a></li>
                <li class="menu-item-link"><a href="/index.php#produtos" class="menu-link-item">Produtos</a></li>
                <li class="menu-item-link"><a href="/sobre.php" class="menu-link-item">Sobre</a></li>
                <li class="menu-item-link"><a href="/mvv.php" class="menu-link-item">Missão, Visão e Valores</a></li>
                
                <?php if ($usuarioLogado): ?>
                    <li class="menu-item-action">
                        <a href="/back-end/perfil.php" class="menu-action-link login-btn">Minha Conta</a>
                    </li>
                <?php else: ?>
                    <li class="menu-item-action">
                        <a href="/back-end/login.php" class="menu-action-link login-btn">Fazer Login</a>
                    </li>
                <?php endif; ?>

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
        <button id="menu-open-btn" type="button" aria-label="Abrir menu expansivo">
          <img src="/assets/Elementos/Menu.svg" alt="Ícone de menu" class="header-icon" />
        </button>
        <a href="/index.php" aria-label="Voltar para a página principal Prime Photos">
            <img src="/assets/Logotipo/Logo PRIME PHOTOS (positivo).svg" alt="Logotipo Prime Photos" class="header-logo"/>
        </a>
      </div>

      <div class="header-buttons">
        <a href="/back-end/carrinho_compras.php" aria-label="Abrir carrinho de compras" class="header-cart-btn">
          <img src="/assets/Elementos/Carrinho de Compra.svg" alt="ícone Carrinho de compras" class="header-icon" />
          <?php
          $quantidadeCarrinho = isset($_SESSION['carrinho']) ? count(array_filter($_SESSION['carrinho'])) : 0;
          if ($quantidadeCarrinho > 0): ?>
            <span class="carrinho-quantidade"><?php echo $quantidadeCarrinho; ?></span>
          <?php endif; ?>
        </a>
        
        <?php if ($usuarioLogado): ?>
          <div class="header-welcome">
            <span>Olá, <?php echo htmlspecialchars($usuarioLogado['nome'] ?? 'Visitante'); ?></span>
          </div>
        <?php else: ?>
          <a href="/back-end/login.php" class="header-login-link" aria-label="Fazer Login">
            <img src="/assets/Elementos/Login.svg" alt="Fazer Login" class="header-icon" />
          </a>
        <?php endif; ?>
      </div>
    </header>

    <main class="main">