<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$linharoot = $_SERVER['DOCUMENT_ROOT'];
include "$linharoot/util.php";

session_start(); // Inicia a sessão para pegar o email

$feedback_message = "";

if ($_POST) {
    $conn = conecta();

    // recebe senhas do form
    $senha1 = $_POST['senha1'];
    $senha2 = $_POST['senha2'];

    // recupera o email salvo como var sessao em esqueci.php
    $token = $_GET['token'];
    
    // Verifica se a sessão de email existe
    if (!isset($_SESSION["email"])) {
        $feedback_message = "Sessão expirada ou inválida. Por favor, solicite um novo link de recuperação.";
        $feedback_message .= "<br><br><a href='esqueci.php'>Voltar</a>";
    } else {
        $email = $_SESSION["email"];
        
        // obtem a senha do banco
        $sql = "SELECT senha FROM usuario WHERE email=:email";  // Usando prepared statement
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $linha = $stmt->fetch();
        $senha_atual_hash = $linha ? $linha['senha'] : null;

        // confere se o token eh VERDADEIRO (token == senha antiga)
        if ($senha_atual_hash == $token) {
            if ($senha1 == $senha2) {
                // Verifica se a senha tem 6 dígitos
                if (strlen($senha1) == 6) {
                    $senha1_hash = password_hash($senha1, PASSWORD_DEFAULT);
                    
                    // Atualiza a senha no banco
                    $update_sql = "UPDATE usuario SET senha=:senha WHERE email=:email";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bindParam(':senha', $senha1_hash);
                    $update_stmt->bindParam(':email', $email);
                    $update_stmt->execute();

                    $feedback_message = "<b>Senha alterada com sucesso !!</b>";
                    unset($_SESSION["email"]); // Limpa a sessão após o sucesso
                } else {
                    $feedback_message = "A senha deve ter exatamente 6 dígitos.";
                }
            } else {
                $feedback_message = "As senhas estão diferentes.";
            }
        } else {
            $feedback_message = "Token inválido ou expirado! Tente novamente.<br>";
        }
    }
    
    // Adiciona o link de login apenas se a senha não foi alterada com sucesso
    if (strpos($feedback_message, "sucesso") === false) {
        $feedback_message .= "<br><br><a href='login.php'>Ir para Login</a>";
    } else {
        $feedback_message .= "<br><br><a href='login.php'>Ir para Login</a>";
    }
}
?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha</title>
    <link rel="stylesheet" href="/front-end/styles/reset.css" />
    <link rel="stylesheet" href="/front-end/styles/form.css" />
    <link rel="stylesheet" href="/front-end/styles/media.css" />
</head>
<body>
    <main class="main">
        <div class="forgot-password-form">
            <h2>Redefinir sua senha</h2>
            <p>Digite sua nova senha. Ela deve conter exatamente 6 dígitos.</p>

            <form action='' method='post'>
                <label class="login-label" for="senha1">Nova Senha (6 dígitos)</label>
                <input type='password' name='senha1' maxlength='6' class='form-input' id="senha1" required>
                
                <label class="login-label" for="senha2">Redigite a senha</label>
                <input type='password' name='senha2' maxlength='6' class='form-input' id="senha2" required>
                
                <input type='submit' value='Alterar Senha' class='form-submit-button'>
            </form>

            <?php
            // Imprime a mensagem de feedback do PHP aqui dentro
            if (!empty($feedback_message)) {
                // Reutilizando a classe .forgot-password-message
                echo "<div class='forgot-password-message'>$feedback_message</div>";
            }
            ?>
        </div>
    </main>
</body>
</html>