<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$linharoot = $_SERVER['DOCUMENT_ROOT'];
include "$linharoot/util.php";

session_start();

$email = isset($_GET['email']) ? $_GET['email'] : "";
$mensagemErro = "";
$dadosForm = [
    'nome' => '',
    'email' => $email,
    'telefone' => ''
];

// Se veio do login com email pré-preenchido
if (!empty($_GET['email'])) {
    $dadosForm['email'] = $_GET['email'];
}

// Se submeteu o formulário e houve erro, mantém os dados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dadosForm = [
        'nome' => trim($_POST['nome'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'telefone' => trim($_POST['telefone'] ?? '')
    ];
    
    if (empty($dadosForm['nome']) || strlen($dadosForm['nome']) < 2) {
        $mensagemErro = "Nome deve ter pelo menos 2 caracteres.";
    } elseif (empty($dadosForm['email']) || !filter_var($dadosForm['email'], FILTER_VALIDATE_EMAIL)) {
        $mensagemErro = "Email inválido.";
    } elseif (empty($_POST['senha']) || strlen($_POST['senha']) < 6) {
        $mensagemErro = "Senha deve ter pelo menos 6 caracteres.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Prime Photos</title>
    <link rel="icon" type="image" href="/assets/Elementos/Camera COM FLASH.svg">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/front-end/styles/reset.css">
    <link rel="stylesheet" href="/front-end/styles/form.css">
    <link rel="stylesheet" href="/front-end/styles/media.css">
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
                <img src="/assets/Logotipo/Logo PRIME PHOTOS (positivo).svg" alt="Logotipo Prime Photos" class="header-logo">
            </a>
        </div>
        <div class="header-buttons">
            <a href="/back-end/login.php" aria-label="Voltar para página de login" class="header-back-link">
                <img src="/assets/Elementos/Back.svg" alt="Ícone de seta para voltar" class="header-icon">
            </a>
        </div>
    </header>

    <main class="main">
        <form method="POST" action="/back-end/salvar_cadastro.php" class="login-form">
            <h1>Cadastro</h1>

            <?php if (!empty($mensagemErro)): ?>
                <div class="error"><?php echo htmlspecialchars($mensagemErro); ?></div>
            <?php endif; ?>

            <label for="nome" class="login-label">Nome completo:</label>
            <input type="text" name="nome" id="nome" class="form-input" required minlength="2" 
                   value="<?php echo htmlspecialchars($dadosForm['nome']); ?>" autofocus>

            <label for="email" class="login-label">Email:</label>
            <input type="email" name="email" id="email" class="form-input" required
                   value="<?php echo htmlspecialchars($dadosForm['email']); ?>">

            <label for="telefone" class="login-label">Telefone (opcional):</label>
            <input type="tel" name="telefone" id="telefone" class="form-input" 
                   placeholder="(11) 99999-9999"
                   value="<?php echo htmlspecialchars($dadosForm['telefone']); ?>">

            <label for="senha" class="login-label">Senha (mínimo 6 caracteres):</label>
            <input type="password" name="senha" id="senha" class="form-input" required minlength="6">

            <button type="submit" class="form-submit-button">Cadastrar</button>
            
            <div class="form-links">
                <div class="form-links">
                    <button type="button" onclick="window.location.href='/back-end/login.php'" class="form-link">
                        Já tem uma conta? Faça login
                    </button>
                </div>
            </div>
        </form>
    </main>
</body>
</html>