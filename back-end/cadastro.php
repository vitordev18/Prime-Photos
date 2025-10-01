<?php
    $email = isset($_GET['email']) ? $_GET['email'] : "";
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="icon" type="image" href="../assets/Elementos/Camera COM FLASH.svg">
    <link rel="stylesheet" href="../front-end/styles/reset.css">
    <link rel="stylesheet" href="../front-end/styles/login.css">
    <link rel="stylesheet" href="../front-end/styles/cadastro.css">
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap");
        @import url("https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap");
    </style>
</head>
<body>
    <header class="header">
        <div class="header-left">
            <img src="/assets/Logotipo/Logo PRIME PHOTOS (positivo).svg" alt="Logotipo Prime Photos" class="header-logo">
        </div>
        <div class="header-buttons">
            <a href="/front-end/index.html" aria-label="Voltar para página principal" class="header-back-link">
                <img src="/assets/Elementos/Back.svg" alt="Voltar para página principal" class="header-icon">
            </a>
        </div>
    </header>

    <main class="main">
        <h1>Cadastro de Usuário</h1>

        <form method="POST" action="salvar_cadastro.php" class="login-form">
            <label for="nome" class="login-label">Nome:</label>
            <input type="text" name="nome" id="nome" class="login-input" required>

            <label for="email" class="login-label">Email:</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" class="login-input" required>

            <label for="senha" class="login-label">Senha:</label>
            <input type="password" name="senha" id="senha" class="login-input" required minlength="6">

            <button type="submit" class="login-submit">Cadastrar</button>
            <button type="button" onclick="window.location.href='/front-end/login.html'" class="login-submit">Já tenho conta</button>
        </form>
    </main>
</body>
</html>