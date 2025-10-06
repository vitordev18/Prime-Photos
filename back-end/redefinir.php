<html>
     <h3>Redefinir a senha</h3>
     <form action='' method='POST'>  
          Senha (6 digitos)<br>
          <input type='password' name='senha1' maxlength='6'><br>
          Redigite a senha<br>
          <input type='password' name='senha2' maxlength='6'><br>                
          <input type='submit' value='Alterar'>
     </form>

     <?php
     include "/util.php";

     session_start();

     if ($_POST) {  
          $conn = conecta();
          // recebe senhas do form 
          $senha1 = $_POST['senha1'];
          $senha2 = $_POST['senha2'];
          // recupera o email salvo como var sessao em esqueci.php 
          $token = $_GET['token'];
          $email = $_SESSION["email"];
          // obtem a senha do banco
          $sql = "SELECT senha FROM usuario WHERE email='$email'";              
          $senha = ValorSQL1($conn, $sql);     
          // confere se o token eh VERDADEIRO
          if ($senha == $token) {
               if ($senha1 == $senha2) {
                    $senha1 = password_hash($senha1,PASSWORD_DEFAULT);
                    ExecutaSQL($conn, "UPDATE usuario SET senha='$senha1' WHERE email='$email'");
                    echo "<br>Senha alterada com sucesso";
               } else {
                    echo "<br>Senhas estão diferentes";
               }
          }
          else {
               echo "<br>Token inválido<br>";
          }
          // se o preenchimento da nova senha esta correto
          // atualiza a senha do usuario !!!
          echo "<br><br><a href='/back-end/login.php'>Login</a>";
     }
     ?>  
</html>