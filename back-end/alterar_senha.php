<?php
$linharoot = $_SERVER['DOCUMENT_ROOT'];
include "$linharoot/util.php";

$pdo = conecta();

session_start();

if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /back-end/login.php');
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$senha_atual = $_POST['senha_atual'];
$nova_senha = $_POST['nova_senha'];
$confirma_senha = $_POST['confirma_senha'];

if (empty($senha_atual) || empty($nova_senha) || empty($confirma_senha)) {
    header('Location: /back-end/perfil.php?erro=campos_vazios');
    exit();
}

if ($nova_senha !== $confirma_senha) {
    header('Location: /back-end/perfil.php?erro=senhas_nao_conferem');
    exit();
}

try {
    $sql_get_senha = "SELECT senha_hash FROM usuarios WHERE id = :id";
    $stmt = $pdo->prepare($sql_get_senha);
    $stmt->execute([':id' => $usuario_id]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    $senha_hash_db = $resultado['senha_hash'];

    if (password_verify($senha_atual, $senha_hash_db)) {
        $novo_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

        $sql_update_senha = "UPDATE usuario SET senha_hash = :novo_hash WHERE id = :id";
        $stmt_update = $pdo->prepare($sql_update_senha);
        $stmt_update->execute([
            ':novo_hash' => $novo_hash,
            ':id' => $usuario_id
        ]);

        header('Location: /back-end/perfil.php?status=senha_ok');
        exit();

    } else {
        header('Location: /back-end/perfil.php?erro=senha_invalida');
        exit();
    }

} catch (PDOException $e) {
    header('Location: /back-end/perfil.php?erro=generico');
    exit();
}
?>