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

    // Busca informações do produto e o estoque no banco de dados
    $stmt = $pdo->prepare("SELECT id_produto, nome, valor, estoque(id_produto) as estoque_disponivel FROM produto WHERE id_produto = ?");
    $stmt->execute([$id_produto]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    // Valida se o produto existe e tem estoque
    if (!$produto) {
        // Produto não encontrado no banco, redireciona
        header("Location: /index.php#produtos");
        exit();
    }
    
    if ($produto['estoque_disponivel'] <= 0) {
        // Redireciona com uma mensagem de erro
        header("Location: /index.php#produtos?erro=sem_estoque");
        exit();
    }

    // Lógica para adicionar ou incrementar no carrinho
    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }
    
    // Se o item já está no carrinho, apenas incrementa a quantidade
    if (isset($_SESSION['carrinho'][$id_produto])) {
        // Verifica se a quantidade desejada não ultrapassa o estoque
        if ($_SESSION['carrinho'][$id_produto]['quantidade'] < $produto['estoque_disponivel']) {
            $_SESSION['carrinho'][$id_produto]['quantidade']++;
        }
    } else {
        // Se for um novo item, adiciona com os dados do banco
        $_SESSION['carrinho'][$id_produto] = [
            'id' => $produto['id_produto'],
            'nome' => $produto['nome'],
            'valor_unitario' => $produto['valor'],
            'quantidade' => 1,
            'estoque_maximo' => $produto['estoque_disponivel']
        ];
    }
    
    header("Location: /back-end/carrinho_compras.php");
    exit();

} catch (PDOException $e) {
    error_log("Erro ao adicionar ao carrinho: " . $e->getMessage());
    header("Location: /index.php#produtos?erro=db_error");
    exit();
}
?>