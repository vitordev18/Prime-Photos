<?php
    include "util.php";
    session_start();


    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $senhaCripto = password_hash($senha, PASSWORD_DEFAULT);

    $conn = conecta();
    
    if()
    {
        $varSQL = "insert into |  | (nome, email, senha) values (:nome, :email, :senha )";
        $insert = $conn-> prepare($varSQL);
        $insert -> bindParam(':nome', $nome);
        $insert -> bindParam(':email', $email);
        $insert -> bindParam(':senha', $senhaCripto);
    }
?>