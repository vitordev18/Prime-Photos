<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome  = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);    
    $senha = trim($_POST['senha']);

    //try {
        $host = "projetoscti.com.br";
        $dbname = "eq4.inf2";
        $user = "eq4.inf2";
        $password = "eq42675";
        $params = "pgsql:host=$host;
                   dbname=$dbname;
                   user=$user;
                   password=$password;
                   port=54432";
        $pdo = new PDO($params);
        
        $sql = "SELECT id_usuario FROM usuario WHERE email = :email ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $linha = $stmt->fetch();

        if ($linha) {
            echo "Esse e-mail já está cadastrado. <a href='/front-end/login.html'>Fazer login</a>";
        } else {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuario (nome, email, telefone ,senha) VALUES (:nome, :email, :telefone ,:senha)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telefone', $telefone);
            $stmt->bindParam(':senha', $senhaHash);

            if ($stmt->execute()) {
                echo "Cadastro realizado! <a href='/front-end/form-login.html'>Fazer login</a>";
            } else {
                echo "Erro no cadastrar do usuário.";
            }
        }
  //} catch (PDOException $e) {
  //    error_log("Erro no cadastro: " . $e->getMessage());
  //    echo "Erro do servidor!";
  //}
}
?>