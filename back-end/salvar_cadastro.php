<?php
require 'util.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome  = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);    
    $senha = trim($_POST['senha']);

    try {
        $sql = "SELECT id FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "Esse e-mail já está cadastrado. <a href='../front-end/login.html'>Fazer login</a>";
        } else {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuarios (nome, email, telefone ,senha) VALUES (:nome, :email, :telefone ,:senha)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telefone', $telefone);
            $stmt->bindParam(':senha', $senhaHash);

            if ($stmt->execute()) {
                echo "Cadastro realizado! <a href='../front-end/form-login.html'>Fazer login</a>";
            } else {
                echo "Erro no cadastrar do usuário.";
            }
        }
    } catch (PDOException $e) {
        error_log("Erro no cadastro: " . $e->getMessage());
        echo "Erro do servidor!";
    }
}
?>