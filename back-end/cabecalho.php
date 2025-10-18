<?php 
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
  session_start();
  include "/util.php";
        
  $conn = conecta();

  if (isset($_SESSION['statusConectado']) and ($_SESSION['statusConectado'] == true)) {        
      echo "<br>Ola,".$_SESSION['login']."<br>";
      $params = [['campo' => ':nome',
                  'valor' => $_SESSION['login']],
                 ['campo' => ':nome',
                   'valor' => $_SESSION['login']]];
      $telefone = valorsql($conn, "SELECT telefone FROM usuario WHERE nome=:nome", $params);
      echo $telefone;
      if ($_SESSION['admin'] == true) {
        echo "<a href='/back-end/produtos.php'>Produtos</a>
        <a href='/back-end/usuarios.php'>Usuarios</a>";
      }
      echo "<a href='/back-end/atend.php'>Atendimento</a>
            <a href='/back-end/missao.php'>Missao</a>
            <a href='/back-end/logout.php'>Sair</a>";
  } else {
    echo "<a href='/back-end/login.php'>Login</a>";
  }
echo "<hr>";
?> 