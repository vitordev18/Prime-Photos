<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    
    function conecta($params = "") {
        $pdo = new PDO("pgsql:host=projetoscti.com.br;
                        port=54432;
                        dbname=eq4.inf2;
                        user=eq4.inf2;
                        password=eq42675");       
        return $pdo;
    }
    
    // primeira versao
    function ExecutaSQL ($paramConn, $paramSQL) 
    {
     return $paramConn->exec($paramSQL) > 0;
    }

    function ValorSQL1 ($paramConn, $paramSQL) 
    {
      // com query vc nao passa parametros, apenas $conn e frase SQL  
      $select = $paramConn->query($paramSQL);
      $linha = $select->fetch();
      return $linha[0];
      
      /* a funcao precisa funcionar qquer q seja o campo que esta sendo pedido,
         nesse ponto vc nao saberá qual o nome do campo q deve retornar, 
         por isso, vc usa o indice ZERO -  a vantagem desse comando eh 
         receber um unico valor */
    }

    // segunda versao usando passagem e prepare internamente
    function ValorSQL2 ($paramConn, $paramSQL, $params) 
    {  
        /*
        Exemplo de uso 
        ------------------------------
        $valor_unitario = valorsql2($conn, "select valor_unitario from produto 
                                    where id_produto = :id_produto", 
                                    [ ["campo" => ":idproduto", 
                                       "valor" => $id_produto] ]);

        */
        $select = $paramConn->prepare($paramSQL);
        foreach($params as $param) { 
            /* cada linha lida é uma condicao:
               o nome do 'campo' e o 'valor do campo' 
               a cada iterao, carrega-se um bindParam */
            $select->bindParam($param['campo'], $param['valor']);
        }
        $select->execute();
        $linha = $select->fetch();

        return $linha[0]; 

        /* a funcao precisa funcionar qquer q seja o campo que esta sendo pedido,
           nesse ponto vc nao saberá qual o nome do campo q deve retornar, 
           por isso, vc usa o indice ZERO -  a vantagem desse comando eh 
           receber um unico valor */
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