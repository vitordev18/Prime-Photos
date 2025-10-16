<?php
$linharoot = $_SERVER['DOCUMENT_ROOT'];
include "$linharoot/util.php";
include "$linharoot/back-end/emails.php";

session_start();

$mensagem = "";
$tipoMensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "Por favor, insira um email válido.";
        $tipoMensagem = 'erro';
    } else {
        try {
            $pdo = conecta();

            $sql_check = "SELECT nome FROM usuario WHERE email = :email LIMIT 1";
            $stmt_check = $pdo->prepare($sql_check);
            $stmt_check->execute([':email' => $email]);
            $usuario = $stmt_check->fetch();

            if ($usuario) {
                $token = bin2hex(random_bytes(32));
                $token_hash = hash('sha256', $token);
                $expires_at = time() + 3600;

                $reset_string = "RESET::" . $token_hash . "::" . $expires_at;

                $sql_update = "UPDATE usuario SET senha = :reset_string WHERE email = :email";
                $stmt_update = $pdo->prepare($sql_update);
                $stmt_update->execute([':reset_string' => $reset_string, ':email' => $email]);

                $nome = $usuario['nome'];
                $link_redefinicao = "http://eq4.inf2.projetoscti.com.br/back-end/redefinir.php?token=" . $token . "&email=" . urlencode($email);

                $html = "<h4>Redefinição de Senha</h4>
                         <p>Olá, $nome!</p>
                         <p>Recebemos uma solicitação para redefinir sua senha. Clique no link abaixo para criar uma nova senha:</p>
                         <p><a href='$link_redefinicao' style='display: inline-block; padding: 10px 20px; font-size: 16px; color: white; background-color: #bc3908; text-decoration: none; border-radius: 5px;'>Redefinir Senha Agora</a></p>
                         <p>Se você não solicitou isso, pode ignorar este email.</p>
                         <p>O link expira em 1 hora.</p>";

                if (EnviaEmail($email, 'Recuperação de Senha - Prime Photos', $html)) {
                    $mensagem = "Link de recuperação enviado com sucesso! Verifique sua caixa de entrada (e spam).";
                    $tipoMensagem = 'sucesso';
                } else {
                    $mensagem = "Erro ao enviar o email. Tente novamente mais tarde.";
                    $tipoMensagem = 'erro';
                }
            } else {
                $mensagem = "Email não cadastrado no sistema.";
                $tipoMensagem = 'erro';
            }
        } catch (Exception $e) {
            error_log("Erro em esqueci.php: " . $e->getMessage());
            $mensagem = "Ocorreu um erro inesperado. Tente novamente.";
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
    <title>Recuperar Senha - Prime Photos</title>
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
        <div class="header-buttons">
            <a href="/back-end/login.php"><img src="/assets/Elementos/Back.svg" alt="Voltar" class="header-icon"></a>
        </div>
    </header>
    <main class="main">
        <form action='esqueci.php' method='POST' class="login-form">
            <h1>Recuperar Senha</h1>
            <?php if (!empty($mensagem)): ?>
                <div class="status-message status-<?php echo $tipoMensagem; ?>">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>
            <p class="login-label" style="text-align: center; margin-bottom: 1rem;">
              Informe seu email para receber o link de recuperação.
            </p>
            <label for="email" class="login-label">Email:</label>
            <input type='email' name='email' id='email' class="login-input" required autofocus>
            <button type='submit' class="login-submit-enter" style="margin-top: 1.5rem;">
                Enviar Link
            </button>
        </form>
    </main>
</body>
</html>