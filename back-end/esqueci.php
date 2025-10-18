<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$linharoot = $_SERVER['DOCUMENT_ROOT'];
include "$linharoot/util.php";
include "emails.php";

session_start();

$feedback_message = ""; // Variável para guardar a mensagem de feedback

if ($_POST) {
    $conn = conecta();
    $email = $_POST['email'];
    $select = $conn->prepare("SELECT nome,senha FROM usuario WHERE email=:email ");
    $select->bindParam(':email', $email);
    $select->execute();
    $linha = $select->fetch();

    if ($linha) {
        $token = $linha['senha'];
        $nome = $linha['nome'];
        $seusite = "eq4.inf2";

        $html = "<h4>Redefinir sua senha</h4><br>
                  <b>Oi $nome</b>, <br>
                  Clique no link para redefinir sua senha:<br>
                  https://$seusite.projetoscti.com.br/back-end/redefinir.php?token=$token";

        // guarda o email pra recuperar a senha em redefinir.php
        $_SESSION["email"] = $email;

        if (EnviaEmail($email, '* Recupere a sua senha !! *', $html)) {
            $feedback_message = "<b>Email enviado com sucesso</b> (verifique sua caixa de spam se nao encontrar)";
        }
    } else {
        $feedback_message = "Email não cadastrado";
    }
    $feedback_message .= "<br><br><a href='login.php'>Voltar</a>";
}
?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha</title>
    <link rel="stylesheet" href="/front-end/styles/reset.css" />
    <link rel="stylesheet" href="/front-end/styles/form.css" />
    <link rel="stylesheet" href="/front-end/styles/media.css" />

</head>
<body>
    <main class="main">
        <div class="forgot-password-form">
            <h2>Esqueceu sua senha?</h2>
            <p>Não se preocupe. Insira seu e-mail abaixo para enviarmos um link de recuperação.</p>

            <form action='' method='post'>
                <input type='email' name='email' placeholder="Digite seu e-mail" required>
                <input type='submit' value='Enviar Link'>
            </form>

            <?php
            if (!empty($feedback_message)) {
                echo "<div class='forgot-password-message'>$feedback_message</div>";
            }
            ?>
        </div>
    </main>
    </body>
</html>