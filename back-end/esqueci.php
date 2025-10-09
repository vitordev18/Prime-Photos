<?php
$linharoot = $_SERVER['DOCUMENT_ROOT'];
include "$linharoot/util.php"; 
include "$linharoot/back-end/emails.php";

session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Recuperar Senha</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/front-end/styles/reset.css" />
    <link rel="stylesheet" href="/front-end/styles/form.css" />
    <link rel="stylesheet" href="/front-end/styles/media.css" />
</head>

<body>
    <header class="header">
        <div class="header-left">
            <a href="/index.html" aria-label="Voltar para a página principal Prime Photos">
              <img src="/assets/Logotipo/Logo PRIME PHOTOS (positivo).svg" alt="Logotipo Prime Photos" class="header-logo">
            </a>
        </div>
        <div class="header-buttons">
            <a href="/front-end/login.html" aria-label="Voltar para a página de Login" class="header-back-link">
                <img src="/assets/Elementos/Back.svg" alt="Ícone de seta para voltar" class="header-icon">
            </a>
        </div>
    </header>

    <main class="main">
        <form action='' method='POST' class="login-form">
            <h1>Recuperar Senha</h1>
            
            <p class="login-label">
              Informe seu email para receber o link de recuperação:
            </p>
            
            <label for="email" class="login-label" style="margin-bottom: 0;">Email:</label>
            <input type='email' name='email' id='email' class="login-input" required autofocus>
            
            <button type='submit' class="login-submit-enter" style="margin-top: 1rem;">
                Enviar Link de Recuperação
            </button>

            <div class="php-message" style="text-align: center; margin-top: 1rem; font-family: var(--secondary-font);">
                <?php
                if ($_POST) {
                    $conn = conecta();
                    $email = trim(htmlspecialchars($_POST['email']));
                    
                    $select = $conn->prepare("SELECT nome, senha FROM usuario WHERE email=:email");
                    $select->bindParam(':email', $email);
                    $select->execute();
                    $linha = $select->fetch();
                    
                    if ($linha) {
                        $token = $linha['senha']; 
                        $nome = $linha['nome'];
                        $seusite = "eq4.inf2"; 
                        
                        $html="<h4>Redefinir sua senha</h4><br>
                               <b>Oi $nome</b>,<br>
                               Clique no link para redefinir sua senha:<br>
                               <a href='http://$seusite.projetoscti.com.br/redefinir.php?token=$token'>
                                   Redefinir Senha Agora
                               </a>";
                               
                        $_SESSION["email"] = $email;
                        
                        if (EnviaEmail($email, '*Recupere a sua senha*', $html)) {
                            echo "<b style='color: var(--strong-orange);'>Link de recuperação enviado com sucesso!</b><br>Verifique sua caixa de entrada (e spam).";
                        } else {
                            echo "<b style='color: var(--dark-red);'>Erro ao enviar o email. Tente novamente mais tarde.</b>";
                        }
                    } else {
                        echo "<b style='color: var(--dark-red);'>Email não cadastrado no sistema.</b>";
                    }
                }    
                ?>
            </div>
        </form>
    </main>
</body>
</html>