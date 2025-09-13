<?php
    function conecta ( $params = "")
    {
        if ($params == "")
        {
            $params = "pgsql: host=projetocti.com.br ; dbname=eq4.inf2 ; user=eq4.inf2 ; password=eq42675 ";
        }
        try
        {
            $varConn = new PDO($params);
            return $varConn;
        }
        catch(PDOException $e)
        {
            echo "Nao foi possivel conectar ";
            exit;
        }
    }
?>