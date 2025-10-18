<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$linharoot = $_SERVER['DOCUMENT_ROOT'];
include "$linharoot/util.php";

session_start();

if (isset($_SESSION['statusConectado']) && $_SESSION['statusConectado'] === true) {
    header("Location: /index.php");
    exit();
}

$mensagemErro = "";
$email_preenchido = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    $email_preenchido = htmlspecialchars($email);

    try {
        if (empty($email) || empty($senha)) {
            throw new Exception("Email e senha são obrigatórios.");
        }
        
        $pdo = conecta();
        $sql = "SELECT id_usuario, nome, email, senha FROM usuario WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            if (str_starts_with($usuario['senha'], 'RESET::')) {
                throw new Exception("Uma redefinição de senha está pendente. Verifique seu e-mail ou solicite um novo link.");
            }

            if (password_verify($senha, $usuario['senha'])) {
                session_regenerate_id(true);
                $_SESSION['usuario_id'] = $usuario['id_usuario'];
                $_SESSION['login'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['statusConectado'] = true;
                header("Location: /index.php");
                exit();
            } else {
                throw new Exception("Email ou senha incorretos.");
            }
        } else {
            header("Location: cadastro.php?email=" . urlencode($email));
            exit();
        }
    } catch (Exception $e) {
        $mensagemErro = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Prime Photos</title>
    <link rel="icon" type="image" href="/assets/Elementos/Camera COM FLASH.svg"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/front-end/styles/reset.css" />
    <link rel="stylesheet" href="/front-end/styles/form.css" />
    <link rel="stylesheet" href="/front-end/styles/media.css" />
    <style>
        .status-message {
            color: #c62828; margin-bottom: 1.5rem; padding: 1rem;
            background: rgba(255,0,0,0.1); border: 1px solid rgba(255,0,0,0.2);
            border-radius: 8px; text-align: center; font-weight: 500;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-left">
            <a href="/index.php"><img src="/assets/Logotipo/Logo PRIME PHOTOS (positivo).svg" alt="Logotipo" class="header-logo"></a>
        </div>
        <div class="header-buttons">
            <a href="/index.php"><img src="/assets/Elementos/Back.svg" alt="Voltar" class="header-icon"></a>
        </div>
    </header>
    <main class="main">
        <form method="POST" action="login.php" class="login-form">
            <h1>Login</h1>
            <?php if (!empty($mensagemErro)): ?>
                <div class="status-message"><?php echo htmlspecialchars($mensagemErro); ?></div>
            <?php endif; ?>
            <label for="email" class="login-label">Email:</label>
            
            <input type="email" name="email" id="email" class="form-input" required autofocus value="<?php echo $email_preenchido; ?>" />
            
            <label for="senha" class="login-label">Senha:</label>
            
            <input type="password" name="senha" id="senha" class="form-input" required />
            
            <button type="submit" class="form-submit-button">Entrar</button>
            
            <div class="form-links">
                <button type="button" onclick="window.location.href='/back-end/cadastro.php'" class="form-link">Não tem conta? Cadastre-se</button>
                <button type="button" onclick="window.location.href='/back-end/esqueci.php'" class="form-link">Esqueci minha senha</button>
            </div>
        </form>
    </main>
</body>
</html>