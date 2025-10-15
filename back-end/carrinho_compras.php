<?php
session_start();
include "util.php";

// Verificar se usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Inicializar carrinho se não existir
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Processar adição de produto (vindo do index.php)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    
    // Adicionar produto ao carrinho
    if (isset($_SESSION['carrinho'][$product_id])) {
        $_SESSION['carrinho'][$product_id]['quantidade']++;
    } else {
        $_SESSION['carrinho'][$product_id] = [
            'id' => $product_id,
            'nome' => 'Foto Polaroid',
            'preco' => 8.00,
            'quantidade' => 1
        ];
    }
    
    header("Location: carrinho_compras.php");
    exit();
}

// Processar atualização de quantidade
if (isset($_GET['action']) && isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    $action = $_GET['action'];
    
    if ($action == 'increase') {
        $_SESSION['carrinho'][$product_id]['quantidade']++;
    } elseif ($action == 'decrease') {
        $_SESSION['carrinho'][$product_id]['quantidade']--;
        if ($_SESSION['carrinho'][$product_id]['quantidade'] <= 0) {
            unset($_SESSION['carrinho'][$product_id]);
        }
    } elseif ($action == 'remove') {
        unset($_SESSION['carrinho'][$product_id]);
    }
    
    header("Location: carrinho_compras.php");
    exit();
}

// Calcular totais
$total_itens = 0;
$total_preco = 0;
foreach ($_SESSION['carrinho'] as $item) {
    $total_itens += $item['quantidade'];
    $total_preco += $item['preco'] * $item['quantidade'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho de Compras - Prime Photos</title>
    <link rel="icon" type="image" href="/assets/Elementos/Camera COM FLASH.svg">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/front-end/styles/reset.css">
    <link rel="stylesheet" href="/front-end/styles/style.css">
    <link rel="stylesheet" href="/front-end/styles/form.css">
    <link rel="stylesheet" href="/front-end/styles/media.css">
    <style>
        .carrinho-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .carrinho-vazio {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        
        .carrinho-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.5rem;
            border-bottom: 1px solid var(--light-gray);
            background: var(--white);
            margin-bottom: 1rem;
            border-radius: 8px;
        }
        
        .item-info {
            flex: 1;
        }
        
        .item-nome {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .item-preco {
            color: var(--strong-orange);
            font-weight: bold;
        }
        
        .quantidade-controls {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .quantidade-btn {
            background: var(--bright-orange);
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .quantidade-btn:hover {
            background: var(--strong-orange);
        }
        
        .quantidade-number {
            font-size: 1.2rem;
            font-weight: bold;
            min-width: 30px;
            text-align: center;
        }
        
        .remove-btn {
            background: var(--dark-red);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .carrinho-total {
            text-align: right;
            padding: 2rem 0;
            border-top: 2px solid var(--light-gray);
            margin-top: 2rem;
        }
        
        .total-valor {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--strong-orange);
        }
        
        .finalizar-compra {
            background: linear-gradient(135deg, var(--strong-orange), var(--dark-red));
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            margin-top: 1rem;
        }
        
        .finalizar-compra:hover {
            background: linear-gradient(135deg, var(--dark-red), var(--strong-orange));
            transform: translateY(-2px);
        }
        
        .header-welcome {
            color: var(--white);
            font-weight: bold;
            padding: 0.5rem 1rem;
            background: rgba(246, 170, 28, 0.2);
            border-radius: 20px;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-left">
            <a href="/index.php" aria-label="Voltar para a página principal Prime Photos">
                <img src="/assets/Logotipo/Logo PRIME PHOTOS (positivo).svg" alt="Logotipo Prime Photos" class="header-logo">
            </a>
        </div>
        <div class="header-buttons">
            <?php if (isset($_SESSION['usuario_nome'])): ?>
                <div class="header-welcome">
                    Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>
                </div>
            <?php endif; ?>
            <a href="/index.php" aria-label="Voltar para página principal" class="header-back-link">
                <img src="/assets/Elementos/Back.svg" alt="Voltar para página principal" class="header-icon">
            </a>
        </div>
    </header>

    <main class="main">
        <div class="carrinho-container">
            <h1>Meu Carrinho</h1>
            
            <?php if (empty($_SESSION['carrinho'])): ?>
                <div class="carrinho-vazio">
                    <h2>Seu carrinho está vazio</h2>
                    <p>Adicione alguns produtos para continuar!</p>
                    <a href="/index.php#produtos" class="main-cta">Ver Produtos</a>
                </div>
            <?php else: ?>
                <?php foreach ($_SESSION['carrinho'] as $item): ?>
                <div class="carrinho-item">
                    <div class="item-info">
                        <div class="item-nome"><?php echo htmlspecialchars($item['nome']); ?></div>
                        <div class="item-preco">R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></div>
                    </div>
                    
                    <div class="quantidade-controls">
                        <button class="quantidade-btn" onclick="window.location.href='carrinho_compras.php?action=decrease&product_id=<?php echo $item['id']; ?>'">-</button>
                        <span class="quantidade-number"><?php echo $item['quantidade']; ?></span>
                        <button class="quantidade-btn" onclick="window.location.href='carrinho_compras.php?action=increase&product_id=<?php echo $item['id']; ?>'">+</button>
                    </div>
                    
                    <div class="item-total">
                        R$ <?php echo number_format($item['preco'] * $item['quantidade'], 2, ',', '.'); ?>
                    </div>
                    
                    <button class="remove-btn" onclick="window.location.href='carrinho_compras.php?action=remove&product_id=<?php echo $item['id']; ?>'">Remover</button>
                </div>
                <?php endforeach; ?>
                
                <div class="carrinho-total">
                    <h2>Total: <span class="total-valor">R$ <?php echo number_format($total_preco, 2, ',', '.'); ?></span></h2>
                    <p><?php echo $total_itens; ?> ite<?php echo $total_itens == 1 ? 'm' : 'ns'; ?> no carrinho</p>
                </div>
                
                <button class="finalizar-compra">Finalizar Compra</button>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>