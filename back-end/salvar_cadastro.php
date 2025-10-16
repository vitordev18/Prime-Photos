<?php
// Iniciar sessão para possíveis mensagens
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Receber e sanitizar dados
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone'] ?? '');
    $senha = trim($_POST['senha']);
    
    try {
        // Validações
        $erros = [];
        
        if (empty($nome) || strlen($nome) < 2) {
            $erros[] = "Nome deve ter pelo menos 2 caracteres.";
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erros[] = "Email inválido.";
        }
        
        if (empty($senha) || strlen($senha) < 6) {
            $erros[] = "Senha deve ter pelo menos 6 caracteres.";
        }
        
        if (!empty($erros)) {
            throw new Exception(implode("<br>", $erros));
        }
        
        // Conexão com banco
        $host = "projetoscti.com.br";
        $dbname = "eq4.inf2";
        $user = "eq4.inf2";
        $password = "eq42675";
        $params = "pgsql:host=$host;dbname=$dbname;port=54432";
        $pdo = new PDO($params, $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Verificar se email já existe
        $sql_verifica = "SELECT id_usuario FROM usuario WHERE email = :email";
        $stmt_verifica = $pdo->prepare($sql_verifica);
        $stmt_verifica->bindParam(':email', $email);
        $stmt_verifica->execute();
        
        if ($stmt_verifica->fetch()) {
            throw new Exception("Esse e-mail já está cadastrado. <a href='/back-end/login.php'>Fazer login</a>");
        }
        
        // Hash da senha
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        
        // Inserir usuário
        $sql_insere = "INSERT INTO usuario (nome, email, telefone, senha) VALUES (:nome, :email, :telefone, :senha)";
        $stmt_insere = $pdo->prepare($sql_insere);
        $stmt_insere->bindParam(':nome', $nome);
        $stmt_insere->bindParam(':email', $email);
        $stmt_insere->bindParam(':telefone', $telefone);
        $stmt_insere->bindParam(':senha', $senhaHash);
        
        if ($stmt_insere->execute()) {
            echo "<div style='color: green; font-weight: bold;'>";
            echo "Cadastro realizado com sucesso!<br>";
            echo "<a href='/back-end/login.php' style='color: blue;'>Fazer login</a>";
            echo "</div>";
        } else {
            throw new Exception("Erro ao cadastrar usuário.");
        }
        
    } catch (PDOException $e) {
        error_log("Erro PDO no cadastro: " . $e->getMessage());
        echo "<div style='color: red;'>Erro no servidor. Tente novamente mais tarde.</div>";
    } catch (Exception $e) {
        echo "<div style='color: red;'>" . $e->getMessage() . "</div>";
    }
} else {
    echo "<div style='color: red;'>Método de requisição inválido.</div>";
}
?>