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

     // Processa a submissão do formulário de nova senha
     if ($_POST) {  
          $conn = conecta();
          
          // Recebe as senhas digitadas pelo usuário
          $senha1 = $_POST['senha1'];
          $senha2 = $_POST['senha2'];
          
          // Recupera o token enviado pela URL (parâmetro GET)
          $token = $_GET['token'];
          
          // VERIFICAÇÃO DE SESSÃO: Confere se o email está salvo na sessão
          if (!isset($_SESSION["email"])) {
              echo "<br>Erro: Sessão de recuperação expirada. Por favor, comece o processo novamente.";
          } else {
              $email = $_SESSION["email"];
              
              // RECUPERAÇÃO DO TOKEN SALVO: Busca o valor atual do campo 'senha' (o token temporário)
              $sql = "SELECT senha FROM usuario WHERE email='$email'";              
              $token_salvo = ValorSQL1($conn, $sql);     
              
              // VALIDAÇÃO DO TOKEN: Compara o token da URL com o token salvo no banco
              if ($token_salvo == $token) {
                   // Se o token for válido, verifica se as duas senhas digitadas são iguais
                   if ($senha1 == $senha2) {
                        
                        // HASH DA NOVA SENHA: Criptografa a nova senha antes de salvar no banco
                        $senha1 = password_hash($senha1,PASSWORD_DEFAULT);
                        
                        // ATUALIZAÇÃO DA SENHA: Salva a nova hash da senha no banco
                        ExecutaSQL($conn, "UPDATE usuario SET senha='$senha1' WHERE email='$email'");
                        
                        // FINALIZAÇÃO: Destrói a sessão para garantir que o token/link não possa ser reusado
                        session_destroy();
                        
                        echo "<br>Senha alterada com sucesso";
                   } else {
                        echo "<br>Senhas estão diferentes";
                   }
              }
              else {
                   // O token não é válido (expirado, alterado ou já usado)
                   echo "<br>Token inválido ou expirado. Tente novamente.<br>";
              }
          } // Fim da verificação de sessão
          
          echo "<br><br><a href='/back-end/login.php'>Login</a>";
     }
     ?>
</html>