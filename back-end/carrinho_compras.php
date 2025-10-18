<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);


$linharoot = $_SERVER['DOCUMENT_ROOT'];
include_once "$linharoot/util.php";

$page_title = "Carrinho de Compras";
$page_css[] = "/front-end/styles/carrinho.css";
include "$linharoot/templates/header.php";

if (!$usuarioLogado) {
    header('Location: /back-end/login.php');
    exit();
}

// Garante que o carrinho exista na sessão
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

$carrinho = &$_SESSION['carrinho']; // Usando referência para facilitar a escrita

// Lógica para Aumentar / Diminuir / Remover itens
if (isset($_GET['action']) && isset($_GET['id_produto'])) {
    $action = $_GET['action'];
    $id_produto = (int)$_GET['id_produto'];

    if (isset($carrinho[$id_produto])) {
        switch ($action) {
            case 'increase':
                if ($carrinho[$id_produto]['quantidade'] < $carrinho[$id_produto]['estoque']) {
                    $carrinho[$id_produto]['quantidade']++;
                }
                break;
            case 'decrease':
                $carrinho[$id_produto]['quantidade']--;
                if ($carrinho[$id_produto]['quantidade'] <= 0) {
                    unset($carrinho[$id_produto]);
                }
                break;
            case 'remove':
                unset($carrinho[$id_produto]);
                break;
        }
        header('Location: carrinho_compras.php');
        exit();
    }
}

// Calcular totais ANTES de finalizar a compra
$total_itens = 0;
$total_valor = 0;
foreach ($carrinho as $item) {
    $total_itens += $item['quantidade'];
    $total_valor += $item['valor_unitario'] * $item['quantidade'];
}

// Lógica para Finalizar a Compra
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['finalizar']) && !empty($carrinho)) {
    try {
        $pdo = conecta();
        $pdo->beginTransaction();
        
        $sql_compra = "INSERT INTO compra (fk_usuario, data, acrescimo_total, sessao, status) VALUES (?, NOW(), ?, ?, ?)";
        $stmt_compra = $pdo->prepare($sql_compra);
        $stmt_compra->execute([$_SESSION['usuario_id'], 0, session_id(), 'reservado']);
        $fk_compra = $pdo->lastInsertId();

        $sql_item = "INSERT INTO compra_produto (fk_produto, fk_compra, quantidade, valor_unitario) VALUES (?, ?, ?, ?)";
        $stmt_item = $pdo->prepare($sql_item);

        foreach ($carrinho as $item) {
            $stmt_item->execute([$item['id'], $fk_compra, $item['quantidade'], $item['valor_unitario']]);
        }

        $pdo->commit();
        $carrinho = []; // Esvazia o carrinho da sessão
        
        header('Location: /index.php?status=compra_ok');
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Erro ao finalizar compra: " . $e->getMessage());
        header('Location: carrinho_compras.php?erro=finalizar');
        exit();
    }
}
?>

<div class="carrinho-main">
    <h1>Meu Carrinho</h1>

    <?php if (empty($carrinho)): ?>
        <div class="carrinho-vazio">
            <h2>Seu carrinho está vazio.</h2>
            <p>Adicione produtos para vê-los aqui.</p>
            <a href="/index.php#produtos" class="main-cta">Ver Produtos</a>
        </div>
    <?php else: ?>
        <div class="carrinho-lista">
            <?php foreach ($carrinho as $item): ?>
            <div class="carrinho-item">
                <div class="item-info">
                    <span class="item-nome"><?php echo htmlspecialchars($item['nome']); ?></span>
                    <span class="item-preco-unitario">R$ <?php echo number_format($item['valor_unitario'], 2, ',', '.'); ?></span>
                </div>
                
                <div class="item-controles">
                    <a href="carrinho_compras.php?action=decrease&id_produto=<?php echo $item['id']; ?>" class="controle-btn" aria-label="Diminuir quantidade">-</a>
                    <span class="item-quantidade"><?php echo $item['quantidade']; ?></span>
                    <a href="carrinho_compras.php?action=increase&id_produto=<?php echo $item['id']; ?>" class="controle-btn" aria-label="Aumentar quantidade">+</a>
                </div>
                
                <div class="item-subtotal">
                    <span>R$ <?php echo number_format($item['valor_unitario'] * $item['quantidade'], 2, ',', '.'); ?></span>
                </div>
                
                <a href="carrinho_compras.php?action=remove&id_produto=<?php echo $item['id']; ?>" class="item-remover" aria-label="Remover item">&times;</a>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="carrinho-resumo">
            <div class="resumo-linha">
                <span>Subtotal (<?php echo $total_itens; ?> ite<?php echo $total_itens == 1 ? 'm' : 'ns'; ?>):</span>
                <span>R$ <?php echo number_format($total_valor, 2, ',', '.'); ?></span>
            </div>
            <div class="resumo-linha total">
                <span>Total:</span>
                <span>R$ <?php echo number_format($total_valor, 2, ',', '.'); ?></span>
            </div>
        </div>
        
        <form method="POST" class="carrinho-form" action="carrinho_compras.php">
            <button class="finalizar-compra-btn" type="submit" name="finalizar">Finalizar Compra</button>
        </form>
    <?php endif; ?>
</div>

<?php
include "$linharoot/templates/footer.php";
?>