<?php
    function conecta($params = "") 
    {        
        if ($params == "") 
        {
            $params = "pgsql:host=projetoscti.com.br;dbname=eq4.inf2;user=eq4.inf2;password=eq42675;port=54432";
        }
        try 
        {
            $varConn = new PDO($params);
            return $varConn;
        } catch(PDOException $e) 
        {
            echo("Não foi possível conectar ao banco de dados");
            die;
        }
    }
?>