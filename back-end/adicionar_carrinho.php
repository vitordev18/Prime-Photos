<?php
session_start();
include "util.php";

// Verificar se usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Processar adição ao carrinho
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    
    // Inicializar carrinho se não existir
    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }
    
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
    
    header("Location: /back-end/carrinho_compras.php");
    exit();
} else {
    header("Location: /index.php");
    exit();
}
?>