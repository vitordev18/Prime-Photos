<?php
session_start();
$linharoot = $_SERVER['DOCUMENT_ROOT'];
include "$linharoot/util.php";
$pdo = conecta();

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    
    $stmt = $pdo->prepare("SELECT * FROM produto WHERE id_produto = ?");
    $stmt->execute([$id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($produto && $produto['estoque'] > 0) {
        $_SESSION['carrinho'][] = $produto;
    } else {
        echo "<script>alert('Produto sem estoque!');</script>";
    }
}


if (isset($_POST['finalizar'])) {
    foreach ($_SESSION['carrinho'] as $item) {
        $pdo->prepare("UPDATE produtos SET estoque = estoque - 1 WHERE id = ?")
            ->execute([$item['id']]);
    }

    $_SESSION['carrinho'] = []; 
    echo "<script>alert('Compra realizada com sucesso!'); window.location.href='index.html';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Carrinho de Compras</title>
</head>
<body>
  <h1>Seu Carrinho</h1>

  <?php if (empty($_SESSION['carrinho'])): ?>
    <p>O carrinho está vazio.</p>
    <a href="index.html">Voltar à loja</a>
  <?php else: ?>
    <table>
      <tr>
        <th>Produto</th>
        <th>Preço</th>
      </tr>
      <?php
      $total = 0;
      foreach ($_SESSION['carrinho'] as $item):
        $total += $item['preco'];
      ?>
      <tr>
        <td><?= htmlspecialchars($item['nome']) ?></td>
        <td>R$ <?= number_format($item['preco'], 2, ',', '.') ?></td>
      </tr>
      <?php endforeach; ?>
      <tr>
        <th>Total</th>
        <th>R$ <?= number_format($total, 2, ',', '.') ?></th>
      </tr>
    </table>

    <form method="POST">
      <button type="submit" name="finalizar">Finalizar Compra</button>
    </form>
  <?php endif; ?>
</body>
</html>