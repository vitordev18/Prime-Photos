<?php
$linharoot = $_SERVER['DOCUMENT_ROOT'];
include "$linharoot/util.php";

session_start();

// Verifica se o usuário está logado e se o método é POST
if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /back-end/login.php');
    exit();
}

// Coleta e valida os dados
$usuario_id = $_SESSION['usuario_id'];
$email = trim($_POST['email'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: /back-end/perfil.php?erro_dados=email_invalido');
    exit();
}

try {
    $pdo = conecta();
    
    // Atualiza os dados no banco de dados de forma segura
    $sql = "UPDATE usuario SET email = :email, telefone = :telefone WHERE id_usuario = :id";
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':email' => $email,
        ':telefone' => $telefone,
        ':id' => $usuario_id
    ]);

    // Atualiza o email na sessão para refletir a mudança imediatamente
    $_SESSION['usuario_email'] = $email;

    header('Location: /back-end/perfil.php?status_dados=ok');
    exit();

} catch (PDOException $e) {
    error_log("Erro ao atualizar dados do usuário: " . $e->getMessage());
    header('Location: /back-end/perfil.php?erro_dados=generico');
    exit();
}
?>