<?php
session_start();
$linharoot = $_SERVER['DOCUMENT_ROOT'];
include "$linharoot/util.php";

// Verificar se usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Processar adição ao carrinho
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_produto'])) {
    $product_id = $_POST['id_produto'];

    // Inicializar carrinho se não existir
    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }
    $pdo = conecta();
    $stmt = $pdo->prepare("SELECT estoque(id_produto) FROM produto WHERE id_produto = ?");
    $stmt->execute([$product_id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if($produto && $produto['estoque'] == 0){
        echo "<script>alert('Produto sem estoque!'); window.location.href='/index.php';</script>";
        exit();
    }

    // Adicionar produto ao carrinho
    if (isset($_SESSION['carrinho'][$product_id])) {
        $_SESSION['carrinho'][$product_id]['estoque']++;
    } else {
        $_SESSION['carrinho'][$product_id] = [
            'id' => $product_id,
            'nome' => 'Foto Polaroid',
            'valor_unitario' => 8.00,
            'quantidade' => 1,
            'estoque' => $produto['estoque']
        ];
    }
    header("Location: /back-end/carrinho_compras.php");
    exit();
} else {
    header("Location: /index.php");
    exit();
}
?>