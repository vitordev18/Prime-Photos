<?php
$linharoot = $_SERVER['DOCUMENT_ROOT'];
include_once "$linharoot/util.php";

// Título e CSS da página para o template do header
$page_title = "Minha Conta";
$page_css[] = "/front-end/styles/form.css";
include "$linharoot/templates/header.php";

// Verifica se o usuário está logado, senão, redireciona
if (!$usuarioLogado) {
    header('Location: /back-end/login.php');
    exit();
}

// Busca os dados mais recentes do usuário no banco
try {
    $pdo = conecta();
    $sql = "SELECT nome, email, telefone FROM usuario WHERE id_usuario = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $usuarioLogado['id']]);
    $dadosUsuario = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Em caso de erro, usa os dados da sessão como fallback
    $dadosUsuario = [
        'nome' => $usuarioLogado['nome'],
        'email' => $usuarioLogado['email'],
        'telefone' => '' // Não armazena telefone na sessão
    ];
}

// Lógica para exibir mensagens de status (sucesso ou erro)
$status_dados = $_GET['status_dados'] ?? '';
$erro_dados = $_GET['erro_dados'] ?? '';
$status_senha = $_GET['status_senha'] ?? '';
$erro_senha = $_GET['erro_senha'] ?? '';
?>

<style>
    .perfil-container {
        max-width: 800px;
        margin: 2rem auto;
        padding: 1rem 2rem;
    }
    .perfil-container h1 {
        font-family: var(--primary-font);
        font-size: 2.5rem;
        color: var(--strong-orange);
        text-align: center;
        margin-bottom: 2.5rem;
    }
    .form-section {
        margin-bottom: 3rem;
    }
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

<div class="perfil-container">
    <h1>Olá, <?php echo htmlspecialchars($dadosUsuario['nome']); ?>!</h1>

    <div class="form-section">
        <form action="atualizar_dados.php" method="POST" class="login-form">
            <h2>Meus Dados</h2>

            <?php if ($status_dados === 'ok'): ?>
                <div class="status-message status-sucesso">Dados atualizados com sucesso!</div>
            <?php elseif (!empty($erro_dados)): ?>
                <div class="status-message status-erro">
                    <?php
                        switch ($erro_dados) {
                            case 'email_invalido': echo 'O e-mail informado não é válido.'; break;
                            default: echo 'Ocorreu um erro ao atualizar seus dados.'; break;
                        }
                    ?>
                </div>
            <?php endif; ?>

            <label for="nome" class="login-label">Nome:</label>
            <input type="text" id="nome" name="nome" class="login-input" value="<?php echo htmlspecialchars($dadosUsuario['nome']); ?>" readonly disabled>

            <label for="email" class="login-label">Email:</label>
            <input type="email" id="email" name="email" class="login-input" value="<?php echo htmlspecialchars($dadosUsuario['email']); ?>" required>
            
            <label for="telefone" class="login-label">Telefone:</label>
            <input type="tel" id="telefone" name="telefone" class="login-input" value="<?php echo htmlspecialchars($dadosUsuario['telefone']); ?>">
            
            <button type="submit" class="login-submit-enter">Salvar Alterações</button>
        </form>
    </div>

    <div class="form-section">
        <form action="alterar_senha.php" method="POST" class="login-form">
            <h2>Alterar Senha</h2>

            <?php if ($status_senha === 'ok'): ?>
                <div class="status-message status-sucesso">Senha alterada com sucesso!</div>
            <?php elseif (!empty($erro_senha)): ?>
                <div class="status-message status-erro">
                    <?php
                        switch ($erro_senha) {
                            case 'campos_vazios': echo 'Todos os campos são obrigatórios.'; break;
                            case 'senha_invalida': echo 'A senha atual está incorreta.'; break;
                            case 'senhas_nao_conferem': echo 'A nova senha e a confirmação não são iguais.'; break;
                            case 'senha_curta': echo 'A nova senha deve ter pelo menos 6 caracteres.'; break;
                            default: echo 'Ocorreu um erro ao alterar a senha.'; break;
                        }
                    ?>
                </div>
            <?php endif; ?>

            <label for="senha_atual" class="login-label">Senha Atual:</label>
            <input type="password" id="senha_atual" name="senha_atual" class="login-input" required>
            
            <label for="nova_senha" class="login-label">Nova Senha:</label>
            <input type="password" id="nova_senha" name="nova_senha" class="login-input" required minlength="6">
            
            <label for="confirma_senha" class="login-label">Confirmar Nova Senha:</label>
            <input type="password" id="confirma_senha" name="confirma_senha" class="login-input" required>
            
            <button type="submit" class="login-submit-enter">Alterar Senha</button>
        </form>
    </div>
</div>

<?php include "$linharoot/templates/footer.php"; ?>