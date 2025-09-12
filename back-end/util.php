<?php
    function conecta ( $params = "")
    {
        if ($params == "")
        {
            $params = "pgsql: host= ; dbname= ; user= ; password= ";
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