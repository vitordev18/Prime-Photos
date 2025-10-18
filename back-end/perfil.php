<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/Sao_Paulo');

$linharoot = $_SERVER['DOCUMENT_ROOT'];
include_once "$linharoot/util.php";

$page_title = "Minha Conta";
$page_css[] = "/front-end/styles/form.css";
include "$linharoot/templates/header.php";

// Verifica se o usuário está logado, senão, redireciona
if (!$usuarioLogado) {
    header('Location: /back-end/login.php');
    exit();
}

$pedidosUsuario = [];
// Busca os dados mais recentes do usuário e também seus pedidos
try {
    $pdo = conecta();
    
    // Busca dados do usuário
    $sql_usuario = "SELECT nome, email, telefone FROM usuario WHERE id_usuario = :id";
    $stmt_usuario = $pdo->prepare($sql_usuario);
    $stmt_usuario->execute([':id' => $usuarioLogado['id']]);
    $dadosUsuario = $stmt_usuario->fetch(PDO::FETCH_ASSOC);

    // Busca pedidos do usuário
    $sql_pedidos = "SELECT c.id_compra, c.data, c.status, SUM(cp.quantidade) AS total_quantidade, SUM(cp.quantidade * cp.valor_unitario) AS preco_total
                    FROM compra AS c
                    JOIN compra_produto AS cp ON c.id_compra = cp.fk_compra
                    WHERE c.fk_usuario = :id
                    GROUP BY c.id_compra
                    ORDER BY c.data DESC";
    $stmt_pedidos = $pdo->prepare($sql_pedidos);
    $stmt_pedidos->execute([':id' => $usuarioLogado['id']]);
    $pedidosUsuario = $stmt_pedidos->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    // Em caso de erro, usa os dados da sessão como fallback e loga o erro
    error_log("Erro ao buscar dados do perfil: " . $e->getMessage());
    $dadosUsuario = [
        'nome' => $usuarioLogado['nome'],
        'email' => $usuarioLogado['email'],
        'telefone' => '' 
    ];
}

// Lógica para exibir mensagens de status
$status_dados = $_GET['status_dados'] ?? '';
$erro_dados = $_GET['erro_dados'] ?? '';
$status_senha = $_GET['status_senha'] ?? '';
$erro_senha = $_GET['erro_senha'] ?? '';
?>

<div class="perfil-container">
    <form action="atualizar_dados.php" method="POST" class="profile-card">
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
        <input type="text" id="nome" name="nome" class="login-input" value="<?php echo htmlspecialchars($dadosUsuario['nome']); ?>" required>

        <label for="email" class="login-label">Email:</label>
        <input type="email" id="email" name="email" class="login-input" value="<?php echo htmlspecialchars($dadosUsuario['email']); ?>" required>
        
        <label for="telefone" class="login-label">Telefone:</label>
        <input type="tel" id="telefone" name="telefone" class="login-input" value="<?php echo htmlspecialchars($dadosUsuario['telefone']); ?>">
        
        <button type="submit" class="login-submit-enter">Salvar Alterações</button>
    </form>

    <form action="alterar_senha.php" method="POST" class="profile-card">
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

    <div class="profile-card">
        <h2>Meus Pedidos</h2>
        <?php if (!empty($pedidosUsuario)): ?>
            <table class="pedidos-table">
                <thead>
                    <tr>
                        <th>Pedido Nº</th>
                        <th>Data</th>
                        <th>Quantidade</th>
                        <th>Valor Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidosUsuario as $pedido): ?>
                        <tr>
                            <td data-label="Pedido Nº"><?php echo htmlspecialchars($pedido['id_compra']); ?></td>
                            <td data-label="Data"><?php echo date("d/m/Y H:i", strtotime($pedido['data'])); ?></td>
                            <td data-label="Quantidade"><?php echo htmlspecialchars($pedido['total_quantidade']); ?></td>
                            <td data-label="Valor Total">R$ <?php echo number_format($pedido['preco_total'], 2, ',', '.'); ?></td>
                            <td data-label="Status"><?php echo htmlspecialchars(ucfirst($pedido['status'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="sem-pedidos">Você ainda não fez nenhum pedido.</p>
        <?php endif; ?>
    </div>
</div>

<?php include "$linharoot/templates/footer.php"; ?>