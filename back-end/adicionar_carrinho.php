<?php
session_start();
$linharoot = $_SERVER['DOCUMENT_ROOT'];
include "$linharoot/util.php";

// Redireciona se o usuário não estiver logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /back-end/login.php?redirect=carrinho");
    exit();
}

// Verifica se o formulário foi enviado corretamente
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['id_produto'])) {
    header("Location: /index.php#produtos");
    exit();
}

$id_produto = filter_var($_POST['id_produto'], FILTER_VALIDATE_INT);

if ($id_produto === false) {
    header("Location: /index.php#produtos");
    exit();
}

try {
    $pdo = conecta();

    // Adicionada verificação para não incluir produtos marcados como 'excluido'.
    $stmt = $pdo->prepare(
        "SELECT id_produto, nome, valor_unitario 
         FROM produto 
         WHERE id_produto = ? AND excluido = false"
    );
    $stmt->execute([$id_produto]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    // Valida se o produto existe e não está excluído
    if (!$produto) {
        // Produto não encontrado ou já foi excluído, redireciona
        header("Location: /index.php#produtos?erro=produto_invalido");
        exit();
    }
    
    // Lógica para adicionar ou incrementar no carrinho
    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }
    
    // Se o item já está no carrinho, apenas incrementa a quantidade
    if (isset($_SESSION['carrinho'][$id_produto])) {
        $_SESSION['carrinho'][$id_produto]['quantidade']++;
    } else {
        // Se for um novo item, adiciona com os dados do banco
        $_SESSION['carrinho'][$id_produto] = [
            'id' => $produto['id_produto'],
            'nome' => $produto['nome'],
            'valor_unitario' => $produto['valor_unitario'],
            'quantidade' => 1
        ];
    }
    
    header("Location: /back-end/carrinho_compras.php");
    exit();

} catch (PDOException $e) {
    // Para depuração, é bom registrar o erro real
    error_log("Erro ao adicionar ao carrinho: " . $e->getMessage());
    // Mensagem genérica para o usuário
    header("Location: /index.php#produtos?erro=db_error");
    exit();
}
?>