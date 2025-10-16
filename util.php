<?php
function conecta() {
    try {
        $host = "projetoscti.com.br";
        $dbname = "eq4.inf2";
        $user = "eq4.inf2";
        $password = "eq42675";
        $port = "54432";
        
        $pdo = new PDO("pgsql:host=$host;dbname=$dbname;port=$port", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        return $pdo;
    } catch(PDOException $e) {
        error_log("Erro de conexão: " . $e->getMessage());
        throw new Exception("Não foi possível conectar ao banco de dados");
    }
}

// Função para executar INSERT, UPDATE, DELETE
function ExecutaSQL($paramConn, $paramSQL, $params = []) {
    try {
        $stmt = $paramConn->prepare($paramSQL);
        
        foreach($params as $campo => $valor) {
            $stmt->bindValue($campo, $valor);
        }
        
        return $stmt->execute();
    } catch(PDOException $e) {
        error_log("Erro ExecutaSQL: " . $e->getMessage() . " - SQL: " . $paramSQL);
        return false;
    }
}

// Função para buscar um único valor (versão moderna)
function ValorSQL($paramConn, $paramSQL, $params = []) {
    try {
        $stmt = $paramConn->prepare($paramSQL);
        
        foreach($params as $campo => $valor) {
            $stmt->bindValue($campo, $valor);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_NUM);
        
        return $result ? $result[0] : null;
    } catch(PDOException $e) {
        error_log("Erro ValorSQL: " . $e->getMessage() . " - SQL: " . $paramSQL);
        return null;
    }
}

// Função para buscar múltiplas linhas
function BuscarTodos($paramConn, $paramSQL, $params = []) {
    try {
        $stmt = $paramConn->prepare($paramSQL);
        
        foreach($params as $campo => $valor) {
            $stmt->bindValue($campo, $valor);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Erro BuscarTodos: " . $e->getMessage() . " - SQL: " . $paramSQL);
        return [];
    }
}

// Função para verificar se usuário está logado
function usuarioEstaLogado() {
    return isset($_SESSION['statusConectado']) && $_SESSION['statusConectado'] === true;
}

// Função para obter dados do usuário logado
function obterUsuarioLogado() {
    if (usuarioEstaLogado()) {
        return [
            'id' => $_SESSION['usuario_id'] ?? null,
            'nome' => $_SESSION['login'] ?? null,
            'email' => $_SESSION['usuario_email'] ?? null,
            'admin' => $_SESSION['admin'] ?? false
        ];
    }
    return null;
}
?>