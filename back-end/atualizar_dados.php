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
$email = $_POST['email'];
$telefone = $_POST['telefone'];

if (empty($email)) {
    header('Location: /back-end/perfil.php?erro=email_vazio');
    exit();
}

try {
    $sql = "UPDATE usuario SET email = :email, telefone = :telefone WHERE id = :id";
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':email' => $email,
        ':telefone' => $telefone,
        ':id' => $usuario_id
    ]);

    header('Location: /back-end/perfil.php?status=dados_ok');
    exit();

} catch (PDOException $e) {
    error_log("Erro ao atualizar dados do usuário: " . $e->getMessage());
    header('Location: /back-end/perfil.php?erro=generico');
    exit();
}
?>