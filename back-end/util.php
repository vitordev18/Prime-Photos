<?php
    $host = "projetocti.com.br";
    $dbname = "eq4.inf2";
    $user = "eq4.inf2";
    $password = "eq42675";
    
    function conecta($params = "") {
        global $host, $dbname, $user, $password;
        
        if ($params == "") {
            $params = "pgsql:host=$host;dbname=$dbname;user=$user;password=$password";
        }
        try {
            $varConn = new PDO($params);
            return $varConn;
        } catch(PDOException $e) {
            error_log("Erro de conexão: " . $e->getMessage());
            die("Erro interno do servidor. Tente novamente mais tarde.");
        }
    }
    
    $pdo = conecta();
?>