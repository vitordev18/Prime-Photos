<?php

session_start();

$linharoot = $_SERVER['DOCUMENT_ROOT'];
include "$linharoot/util.php"; 

$pdo = conecta();

if (!isset($_SESSION['carrinho']) || !is_array($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

if (isset($_POST['product_id']) && is_numeric($_POST['product_id'])) {
    $id = (int)$_POST['product_id'];
    $stmt = $pdo->prepare("SELECT id_produto, nome, valor_unitario, qtde_estoque FROM produto WHERE id_produto = ?");
    $stmt->execute([$id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($produto) {
        $qtde_no_carrinho = 0;
        foreach ($_SESSION['carrinho'] as $item) {
            if ($item['id_produto'] == $id) {
                $qtde_no_carrinho++;
            }
        }
        
        if ($produto['qtde_estoque'] > $qtde_no_carrinho) {
            $_SESSION['carrinho'][] = [
                'id_produto' => $produto['id_produto'],
                'nome' => $produto['nome'],
                'valor_unitario' => $produto['valor_unitario']
            ];
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $mensagem_erro = "Produto " . htmlspecialchars($produto['nome']) . " sem estoque suficiente!";
        }
    } else {
         $mensagem_erro = "Produto não encontrado.";
    }
}

if (isset($_POST['finalizar']) && !empty($_SESSION['carrinho'])) {
    $pdo->beginTransaction();
    $sucesso = true;

    try {
        $stmt_update = $pdo->prepare("UPDATE produto SET qtde_estoque = qtde_estoque - 1 WHERE id_produto = ? AND qtde_estoque > 0");
        
        foreach ($_SESSION['carrinho'] as $item) {
            $stmt_update->execute([$item['id_produto']]);
            
            if ($stmt_update->rowCount() === 0) {
                $sucesso = false;
                break; 
            }
        }

        if ($sucesso) {
            $pdo->commit();
            $_SESSION['carrinho'] = []; 
            $mensagem_sucesso = "Compra realizada com sucesso!";
        } else {
            $pdo->rollBack();
            $mensagem_erro = "Erro ao processar a compra. Verifique a disponibilidade do estoque.";
        }

    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Erro na transação de compra: " . $e->getMessage());
        $mensagem_erro = "Erro interno do sistema. Tente novamente mais tarde.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Carrinho de Compras</title>
    <link rel="icon" type="image" href="/assets/Elementos/Camera COM FLASH.svg"/>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/front-end/styles/reset.css" />
    <link rel="stylesheet" href="/front-end/styles/style.css" />
    <link rel="stylesheet" href="/front-end/styles/carrinho.css" />
    <link rel="stylesheet" href="/front-end/styles/media.css" />
</head>
<body>
    <nav class="main-menu-overlay" id="main-menu-overlay" aria-label="Menu principal de navegação">
        <button id="menu-close-btn" class="menu-close-btn" aria-label="Fechar menu de navegação">
            &times; 
        </button>
        <div class="menu-content">
            <ul class="menu-list">
                <li class="menu-item-link">
                    <a href="/index.html#inicio" class="menu-link-item">Início</a>
                </li>
                <li class="menu-item-link">
                    <a href="/index.html#produtos" class="menu-link-item">Produtos</a>
                </li>
                <li class="menu-item-link">
                    <a href="/front-end/sobre.html" class="menu-link-item">Sobre</a>
                </li>
                <li class="menu-item-link">
                    <a href="/front-end/sobre.html/#equipe" class="menu-link-item">Equipe</a>
                </li>
                <li class="menu-item-link">
                    <a href="/front-end/mvv.html" class="menu-link-item">Missão, Visão e Valores</a>
                </li>
                <li class="menu-item-action">
                    <a href="/front-end/login.html" class="menu-action-link login-btn">Fazer Login</a>
                </li>
                <li class="menu-item-action">
                    <a href="/back-end/carrinho.php" class="menu-action-link cart-btn">Carrinho de Compras</a>
                </li>
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
        <a href="/index.html" aria-label="Voltar para a página principal Prime Photos">
            <img
                src="/assets/Logotipo/Logo PRIME PHOTOS (positivo).svg"
                alt="Logotipo Prime Photos"
                class="header-logo"
            />
        </a>
      </div>

      <div class="header-buttons">
        <a href="/index.html" aria-label="Voltar para página principal" class="header-back-link">
          <img src="/assets/Elementos/Back.svg" alt="Ícone de seta para voltar" class="header-icon">
        </a>
        <a href="/front-end/login.html" aria-label="Fazer login" class="header-login-link">
          <img src="/assets/Elementos/Login.svg" alt="ícone Login" class="header-icon" />
        </a>
      </div>
    </header>
    
    <main class="carrinho-main">
        <h1>Seu Carrinho</h1>

        <?php 
        if (isset($mensagem_sucesso)): ?>
            <p style="color: var(--strong-orange); font-weight: bold;"><?= htmlspecialchars($mensagem_sucesso) ?></p>
        <?php elseif (isset($mensagem_erro)): ?>
            <p style="color: var(--dark-red); font-weight: bold;"><?= htmlspecialchars($mensagem_erro) ?></p>
        <?php endif; ?>

        <?php if (empty($_SESSION['carrinho'])): ?>
            <p>O carrinho está vazio</p>
            <a href="/index.html#produtos">Voltar à loja</a>
        <?php else: ?>
            <div class="table-responsive">
                <table>
                    <tr>
                        <th>Produto</th>
                        <th>Preço</th>
                    </tr>
                    <?php
                    $total = 0;
                    foreach ($_SESSION['carrinho'] as $index => $item):
                        $total += $item['valor_unitario'];
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nome']) ?></td>
                        <td>R$<?= number_format($item['valor_unitario'], 2, ',', '.')?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <th colspan="1">TOTAL</th> 
                        <th>R$<?= number_format($total, 2, ',', '.') ?></th>
                    </tr>
                </table>
            </div>

            <form method="POST" class="carrinho-form">
                <button 
                    type="submit" 
                    name="finalizar" 
                    <?= (count($_SESSION['carrinho']) === 0) ? 'disabled' : '' ?>
                >
                    Finalizar Compra
                </button>
            </form>
        <?php endif; ?>
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
                  <img src="/assets/Elementos/Instagram.svg" alt="" />
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