<?php
$linharoot = $_SERVER['DOCUMENT_ROOT'];
include "$linharoot/util.php";

session_start();

$token_from_url = $_GET['token'] ?? '';
$email_from_url = $_GET['email'] ?? '';

$mensagem = "";
$tipoMensagem = "";
$token_valido = false;

if (empty($token_from_url) || empty($email_from_url) || !filter_var($email_from_url, FILTER_VALIDATE_EMAIL)) {
    $mensagem = "Link de redefinição inválido ou incompleto.";
    $tipoMensagem = 'erro';
} else {
    try {
        $pdo = conecta();
        
        $sql_find = "SELECT senha FROM usuario WHERE email = :email LIMIT 1";
        $stmt_find = $pdo->prepare($sql_find);
        $stmt_find->execute([':email' => $email_from_url]);
        $usuario = $stmt_find->fetch();

        if ($usuario && str_starts_with($usuario['senha'], 'RESET::')) {
            list(, $stored_hash, $expires_at) = explode('::', $usuario['senha']);

            if (hash_equals($stored_hash, hash('sha256', $token_from_url)) && time() < $expires_at) {
                $token_valido = true;
            } else {
                $mensagem = "Token inválido ou expirado. Por favor, solicite um novo link.";
                $tipoMensagem = 'erro';
            }
        } else {
            $mensagem = "Nenhuma redefinição de senha pendente para este usuário ou link inválido.";
            $tipoMensagem = 'erro';
        }
    } catch (Exception $e) {
        $mensagem = "Ocorreu um erro ao validar seu link.";
        $tipoMensagem = 'erro';
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $token_valido) {
    $nova_senha = $_POST['nova_senha'] ?? '';
    $confirma_senha = $_POST['confirma_senha'] ?? '';

    if (strlen($nova_senha) < 6) {
        $mensagem = "A senha deve ter pelo menos 6 caracteres.";
        $tipoMensagem = 'erro';
    } elseif ($nova_senha !== $confirma_senha) {
        $mensagem = "As senhas não conferem.";
        $tipoMensagem = 'erro';
    } else {
        try {
            $pdo = conecta();
            $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

            $sql_update = "UPDATE usuario SET senha = :senha WHERE email = :email";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute([':senha' => $nova_senha_hash, ':email' => $email_from_url]);
            
            $mensagem = "Senha redefinida com sucesso! Você já pode fazer login.";
            $tipoMensagem = 'sucesso';
            $token_valido = false;

        } catch (Exception $e) {
            $mensagem = "Ocorreu um erro ao atualizar sua senha.";
            $tipoMensagem = 'erro';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Redefinir Senha - Prime Photos</title>
    <link rel="stylesheet" href="/front-end/styles/reset.css" />
    <link rel="stylesheet" href="/front-end/styles/form.css" />
    <link rel="stylesheet" href="/front-end/styles/media.css" />
    <style>
        .status-message {
            margin-bottom: 1.5rem; padding: 1rem; border-radius: 8px;
            text-align: center; font-weight: 500;
        }
        .status-sucesso {
            color: #2e7d32; background: rgba(46, 125, 50, 0.1);
            border: 1px solid rgba(46, 125, 50, 0.2);
        }
        .status-erro {
            color: #c62828; background: rgba(255,0,0,0.1);
            border: 1px solid rgba(255,0,0,0.2);
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-left">
            <a href="/index.php"><img src="/assets/Logotipo/Logo PRIME PHOTOS (positivo).svg" alt="Logotipo" class="header-logo"></a>
        </div>
    </header>
    <main class="main">
        <div class="login-form">
            <h1>Redefinir Senha</h1>
            
            <?php if (!empty($mensagem)): ?>
                <div class="status-message status-<?php echo $tipoMensagem; ?>">
                    <?php echo htmlspecialchars($mensagem); ?>
                    <?php if ($tipoMensagem == 'sucesso'): ?>
                        <br><br>
                        <a href="/back-end/login.php" class="login-submit-enter" style="display:inline-block; width: auto; text-decoration: none;">Ir para Login</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($token_valido): ?>
                <form action="redefinir.php?token=<?php echo htmlspecialchars($token_from_url); ?>&email=<?php echo htmlspecialchars($email_from_url); ?>" method="POST">
                    <label for="nova_senha" class="login-label">Nova Senha (mínimo 6 caracteres):</label>
                    <input type="password" name="nova_senha" id="nova_senha" class="login-input" required minlength="6">
                    
                    <label for="confirma_senha" class="login-label">Confirme a Nova Senha:</label>
                    <input type="password" name="confirma_senha" id="confirma_senha" class="login-input" required>
                    
                    <button type="submit" class="login-submit-enter" style="margin-top: 1.5rem;">
                        Salvar Nova Senha
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>