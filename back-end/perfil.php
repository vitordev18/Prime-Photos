<?php
$linharoot = $_SERVER['DOCUMENT_ROOT'];
include "$linharoot/util.php";

$pdo = conecta();

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: /back-end/login.php');
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$usuarioLogado = obterUsuarioLogado(); 

try {
    $sql_usuario = "SELECT nome, email, telefone FROM usuario WHERE id_usuario = :id";
    $stmt_usuario = $pdo->prepare($sql_usuario);
    $stmt_usuario->execute([':id' => $usuario_id]);
    $usuario = $stmt_usuario->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        session_destroy();
        header('Location: /back-end/login.php');
        exit();
    }

    $sql_pedidos = "SELECT c.*, sum(i.valor_unitario) soma FROM compra c join compra_produto i on i.fk_compra = c.id_compra WHERE c.fk_usuario = :usuario_id group by c.id_compra order by c.id_compra desc";
    $stmt_pedidos = $pdo->prepare($sql_pedidos);
    $stmt_pedidos->execute([':usuario_id' => $usuario_id]);
    $pedidos = $stmt_pedidos->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao buscar dados do perfil: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - Prime Photos</title>

    <link rel="icon" type="image" href="/assets/Elementos/Camera COM FLASH.svg" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/front-end/styles/reset.css" />
    <link rel="stylesheet" href="/front-end/styles/style.css" />
    <link rel="stylesheet" href="/front-end/styles/media.css" />

    <style>
        .profile-section {
            padding: 4rem 2rem;
            background-color: var(--beige);
            color: var(--deep-dark);
            min-height: calc(100vh - 80px - 250px);
        }

        .profile-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .profile-header h1 {
            font-family: var(--primary-font);
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 700;
            color: var(--strong-orange);
            margin-bottom: 0.5rem;
        }

        .profile-header p {
            font-size: 1.125rem;
            color: var(--dark-red);
            margin-bottom: 3rem;
        }

        .profile-block {
            background-color: var(--white);
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(34, 9, 1, 0.08);
            margin-bottom: 2.5rem;
        }

        .profile-block h3 {
            font-family: var(--primary-font);
            font-size: 2rem;
            font-weight: 700;
            color: var(--strong-orange);
            margin-bottom: 2rem;
            border-bottom: 2px solid var(--bright-orange);
            padding-bottom: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-family: var(--secondary-font);
            font-weight: 600;
            color: var(--deep-dark);
            margin-bottom: 0.5rem;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            font-size: 1rem;
            font-family: var(--secondary-font);
            border: 1px solid var(--light-gray);
            border-radius: 6px;
            background-color: #fff;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--bright-orange);
            box-shadow: 0 0 0 3px rgba(246, 170, 28, 0.25);
        }

        .btn-submit {
            display: inline-block;
            background-color: var(--strong-orange);
            color: var(--white);
            padding: 0.8rem 1.8rem;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1rem;
            font-family: var(--secondary-font);
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-submit:hover {
            background-color: var(--bright-orange);
            color: var(--deep-dark);
            transform: translateY(-2px);
        }
        
        .pedidos-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            overflow-x: auto;
            display: block;
        }

        .pedidos-table th, .pedidos-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--light-gray);
            white-space: nowrap;
        }

        .pedidos-table thead th {
            font-family: var(--secondary-font);
            font-weight: 700;
            font-size: 0.9rem;
            text-transform: uppercase;
            color: var(--deep-dark);
            background-color: rgba(246, 170, 28, 0.2);
        }

        .pedidos-table tbody tr:hover {
            background-color: rgba(0,0,0,0.02);
        }

        .status-badge {
            padding: 0.3rem 0.7rem;
            border-radius: 15px;
            font-weight: 600;
            font-size: 0.85rem;
            background-color: var(--bright-orange);
            color: var(--deep-dark);
        }

        .footer {
            background-color: var(--deep-dark);
            color: var(--light-gray);
            padding: 4rem 2rem 0;
            font-family: var(--secondary-font);
            font-size: 0.9rem;
            line-height: 1.7;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding-bottom: 3rem;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 3rem;
        }

        .footer-brand-social {
            flex: 1;
            min-width: 250px;
        }

        .footer-logo {
            width: 120px;
            height: auto;
            margin-bottom: 1.5rem;
        }

        .footer-links-group {
            display: flex;
            flex-wrap: wrap;
            gap: 4rem;
            flex: 2;
        }

        .footer-heading {
            display: block;
            font-family: var(--primary-font);
            color: var(--bright-orange);
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1.2rem;
        }

        .footer-links-group ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links-group li {
            margin-bottom: 0.75rem;
        }

        .footer-links-group a,
        .footer-links-group address {
            color: var(--light-gray);
            text-decoration: none;
            font-style: normal;
            transition: color 0.3s ease;
        }

        .footer-links-group a:hover {
            color: var(--white);
            text-decoration: underline;
        }

        .footer-social-icons ul {
            display: flex;
            gap: 1rem;
        }

        .footer-social-icons img {
            width: 28px;
            height: 28px;
            transition: transform 0.3s ease;
        }

        .footer-social-icons a:hover img {
            transform: scale(1.15);
        }

        .footer-bottom {
            background-color: #1a0601;
            border-top: 1px solid rgba(246, 170, 28, 0.15);
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            font-size: 0.85rem;
            color: var(--light-gray);
        }

        .footer-bottom a {
            color: var(--bright-orange);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-bottom a:hover {
            color: var(--white);
            text-decoration: underline;
        }

    </style>
</head>
<body>

<header class="header">
    <div class="header-left">
        <button id="menu-open-btn" type="button" aria-label="Abrir menu expansivo">
            <img src="/assets/Elementos/Menu.svg" alt="Ícone de menu" class="header-icon" />
        </button>
        <a href="/index.php" aria-label="Voltar para a página principal Prime Photos">
            <img src="/assets/Logotipo/Logo PRIME PHOTOS (positivo).svg" alt="Logotipo Prime Photos" class="header-logo" />
        </a>
    </div>
    <div class="header-buttons">
        <a href="/back-end/carrinho_compras.php" aria-label="Abrir carrinho de compras" class="header-cart-btn">
            <img src="/assets/Elementos/Carrinho de Compra.svg" alt="ícone Carrinho de compras" class="header-icon" />
            <?php
            $quantidadeCarrinho = isset($_SESSION['carrinho']) ? count($_SESSION['carrinho']) : 0;
            if ($quantidadeCarrinho > 0): ?>
                <span class="carrinho-quantidade"><?php echo $quantidadeCarrinho; ?></span>
            <?php endif; ?>
        </a>
        <?php if ($usuarioLogado): ?>
            <div class="header-welcome">
                <span>Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></span>
                <a href="/back-end/logout.php" class="header-icon">
                    <img src="/assets/Elementos/Logout.svg" alt="Sair" />
                </a>
            </div>
        <?php else: ?>
            <a href="/back-end/login.php" class="header-login-link">
                <img src="/assets/Elementos/Login.svg" alt="Fazer Login" class="header-icon" />
            </a>
        <?php endif; ?>
    </div>
</header>
<main class="main">
    <section class="profile-section">
        <div class="profile-container">

            <div class="profile-header">
                <h1>Olá, <?php echo htmlspecialchars($usuario['nome']); ?>!</h1>
                <p>Aqui você pode gerenciar suas informações e visualizar seus pedidos.</p>
            </div>

            <?php if (isset($_GET['status'])): ?>
                <?php if ($_GET['status'] == 'dados_ok'): ?>
                    <div class="status-message status-success">Dados atualizados com sucesso!</div>
                <?php endif; ?>
                <?php if ($_GET['status'] == 'senha_ok'): ?>
                    <div class="status-message status-success">Senha alterada com sucesso!</div>
                <?php endif; ?>
            <?php endif; ?>
            <?php if (isset($_GET['erro'])): ?>
                 <div class="status-message status-error">
                    <?php
                        if ($_GET['erro'] == 'senha_invalida') echo "A senha atual está incorreta.";
                        if ($_GET['erro'] == 'senhas_nao_conferem') echo "A nova senha e a confirmação não são iguais.";
                        if ($_GET['erro'] == 'campos_vazios') echo "Por favor, preencha todos os campos.";
                    ?>
                 </div>
            <?php endif; ?>

            <div class="profile-block">
                <h3>Meus Dados</h3>
                <form action="/back-end/atualizar_dados.php" method="POST">
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="telefone" name="telefone" value="<?php echo htmlspecialchars($usuario['telefone']); ?>">
                    </div>
                    <button type="submit" class="btn-submit">Atualizar Dados</button>
                </form>
            </div>

            <div class="profile-block">
                <h3>Alterar Senha</h3>
                <form action="/back-end/alterar_senha.php" method="POST">
                    <div class="form-group">
                        <label for="senha_atual" class="form-label">Senha Atual</label>
                        <input type="password" class="form-control" id="senha_atual" name="senha_atual" required>
                    </div>
                    <div class="form-group">
                        <label for="nova_senha" class="form-label">Nova Senha</label>
                        <input type="password" class="form-control" id="nova_senha" name="nova_senha" required>
                    </div>
                    <div class="form-group">
                        <label for="confirma_senha" class="form-label">Confirmar Nova Senha</label>
                        <input type="password" class="form-control" id="confirma_senha" name="confirma_senha" required>
                    </div>
                    <button type="submit" class="btn-submit">Alterar Senha</button>
                </form>
            </div>

            <div class="profile-block">
                <h3>Meus Pedidos</h3>
                <?php if (count($pedidos) > 0): ?>
                    <div class="table-responsive">
                        <table class="pedidos-table">
                            <thead>
                                <tr>
                                    <th>Nº do Pedido</th>
                                    <th>Data</th>
                                    <th>Valor Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pedidos as $pedido): ?>
                                    <tr>
                                        <td>#<?php echo htmlspecialchars($pedido['id_compra']); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($pedido['data'])); ?></td>
                                        <td>R$ <?php echo number_format($pedido['soma'], 2, ',', '.'); ?></td>
                                        <td><span class="status-badge"><?php echo htmlspecialchars($pedido['status']); ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>Você ainda não fez nenhum pedido.</p>
                <?php endif; ?>
            </div>

        </div>
    </section>
</main>

<footer class="footer">
    <div class="footer-content">
        <div class="footer-brand-social">
            <img src="/assets/Elementos/Camera-Polaroids (com flash).svg" alt="Logotipo Prime Photos" class="footer-logo" />
        </div>
        <div class="footer-links-group">
            <div class="footer-social-icons">
                <span class="footer-heading">Redes Sociais</span>
                <ul>
                    <li>
                        <a href="https://www.instagram.com/primephotos_00/" target="_blank" aria-label="Instagram">
                            <img src="/assets/Elementos/Instagram.svg" alt="Instagram" />
                        </a>
                    </li>
                </ul>
            </div>
            <div>
                <span class="footer-heading">Empresa</span>
                <ul>
                    <li><a href="/front-end/sobre.html">Sobre</a></li>
                    <li><a href="/front-end/mvv.html">Missão, Visão e Valores</a></li>
                </ul>
            </div>
            <div>
                <span class="footer-heading">Endereço</span>
                <ul>
                    <li><address>Av. Nações Unidas, 58-50 - Bauru, SP</address></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <span>&copy; 2025 Prime Photos. Todos os direitos reservados.</span>
        <span><a href="#">Configurações de Cookies</a></span>
    </div>
</footer>
<script src="/front-end/script.js"></script>
</body>
</html>