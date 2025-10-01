<?php
$linharoot = $_SERVER['DOCUMENT_ROOT'];

include "$linharoot/util.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    try {
        $sql = "SELECT * FROM usuario WHERE email = :email LIMIT 1";
        $pdo = conecta();
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        

        if ($stmt->rowCount() > 0) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($senha, $usuario['senha'])) {
                session_start();
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                echo "Login realizado! Seja Bem-vindo, " . $usuario['nome'];
                header("Location: $localroot/index.html");
            } else {
                echo "Senha incorreta!";
            }
        } else {
            header("Location: cadastro.php?email=" . urlencode($email));
            exit();
        }
    } catch (PDOException $e) {
        error_log("Erro no login: " . $e->getMessage());
        echo "Erro no servidor!";
    }
}
?>