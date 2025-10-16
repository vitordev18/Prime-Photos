<?php
$linharoot = $_SERVER['DOCUMENT_ROOT'];
include "$linharoot/util.php";

session_start();

$mensagemErro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    try {
        if (empty($email) || empty($senha)) {
            throw new Exception("Email e senha são obrigatórios.");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Formato de email inválido.");
        }

        $sql = "SELECT * FROM usuario WHERE email = :email LIMIT 1";
        $pdo = conecta();
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($senha, $usuario['senha'])) {
                $_SESSION['usuario_id'] = $usuario['id_usuario'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['statusConectado'] = true;
                $_SESSION['login'] = $usuario['nome'];
                header("Location: /index.php");
                exit();
            } else {
                throw new Exception("Senha incorreta!");
            }
        } else {
            header("Location: cadastro.php?email=" . urlencode($email));
            exit();
        }
    } catch (PDOException $e) {
        error_log("Erro no login: " . $e->getMessage());
        $mensagemErro = "Erro no servidor. Tente novamente.";
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
    <link rel="stylesheet" href="/front-end/styles/style.css" />
    <link rel="stylesheet" href="/front-end/styles/media.css" />
    <style>
        .error { 
            color: red; 
            margin: 10px 0; 
            padding: 10px;
            background: rgba(255,0,0,0.1);
            border-radius: 5px;
            text-align: center;
        }
    </style>
  </head>

  <body>
    <header class="header">
      <div class="header-left">
        <a href="/index.php" aria-label="Voltar para a página principal Prime Photos">
            <img
                src="/assets/Logotipo/Logo PRIME PHOTOS (positivo).svg"
                alt="Logotipo Prime Photos"
                class="header-logo"
            />
        </a>
      </div>
      <div class="header-buttons">
          <a href="/index.php" aria-label="Voltar para página principal" class="header-back-link">
              <img src="/assets/Elementos/Back.svg" alt="Ícone de seta para voltar" class="header-icon">
          </a>
      </div>
    </header>

    <main class="main">
      <form method="POST" action="login.php" class="login-form">
        <h1>Login</h1>
        
        <?php if (!empty($mensagemErro)): ?>
            <div class="error"><?php echo htmlspecialchars($mensagemErro); ?></div>
        <?php endif; ?>
        
        <label for="email" class="login-label">Email:</label>
        <input type="email" name="email" id="email" class="login-input" required autofocus value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" /> 
        
        <label for="senha" class="login-label">Senha:</label>
        <input type="password" name="senha" id="senha" class="login-input" required />

        <button type="submit" class="login-submit-enter">Entrar</button>
        
        <div class="form-links">
            <div class="form-links">
                <button type="button" onclick="window.location.href='/back-end/cadastro.php'" class="form-link">
                    Não tem conta? Cadastre-se
                </button>
                <button type="button" onclick="window.location.href='/back-end/esqueci.php'" class="form-link">
                    Esqueci minha senha
                </button>
            </div>
        </div>
      </form>
    </main>
    
    <script src="/front-end/script.js"></script>
  </body>
</html>