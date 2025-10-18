<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$linharoot = $_SERVER['DOCUMENT_ROOT'];
include "$linharoot/util.php";

session_start();

// Verifica se o usuário está logado e se o método é POST
if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /back-end/login.php');
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$senha_atual = $_POST['senha_atual'] ?? '';
$nova_senha = $_POST['nova_senha'] ?? '';
$confirma_senha = $_POST['confirma_senha'] ?? '';

// Validações iniciais
if (empty($senha_atual) || empty($nova_senha) || empty($confirma_senha)) {
    header('Location: /back-end/perfil.php?erro_senha=campos_vazios');
    exit();
}
if ($nova_senha !== $confirma_senha) {
    header('Location: /back-end/perfil.php?erro_senha=senhas_nao_conferem');
    exit();
}
if (strlen($nova_senha) < 6) {
    header('Location: /back-end/perfil.php?erro_senha=senha_curta');
    exit();
}

try {
    $pdo = conecta();
    
    // Busca o hash da senha atual no banco de dados
    $sql_get_senha = "SELECT senha FROM usuario WHERE id_usuario = :id";
    $stmt = $pdo->prepare($sql_get_senha);
    $stmt->execute([':id' => $usuario_id]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    // Se não encontrar o usuário ou a senha, algo está muito errado
    if (!$resultado) {
        throw new Exception('Usuário não encontrado para alteração de senha.');
    }
    
    $senha_hash_db = $resultado['senha'];

    // Compara a senha atual enviada com o hash do banco
    if (password_verify($senha_atual, $senha_hash_db)) {
        // Senha atual está correta. Gera o novo hash.
        $novo_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

        // Atualiza a senha no banco com o novo hash
        $sql_update_senha = "UPDATE usuario SET senha = :novo_hash WHERE id_usuario = :id";
        $stmt_update = $pdo->prepare($sql_update_senha);
        $stmt_update->execute([
            ':novo_hash' => $novo_hash,
            ':id' => $usuario_id
        ]);

        header('Location: /back-end/perfil.php?status_senha=ok');
        exit();

    } else {
        // Senha atual incorreta
        header('Location: /back-end/perfil.php?erro_senha=senha_invalida');
        exit();
    }

} catch (Exception $e) {
    error_log("Erro ao alterar senha: " . $e->getMessage());
    header('Location: /back-end/perfil.php?erro_senha=generico');
    exit();
}
?>