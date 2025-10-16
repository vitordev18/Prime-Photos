<html>
     <h3>Redefinir a senha</h3>
     <form action='' method='POST'>  
          Senha (6 digitos)<br>
          <input type='password' name='senha1' maxlength='6' required><br>
          Redigite a senha<br>
          <input type='password' name='senha2' maxlength='6' required><br>                
          <input type='submit' value='Alterar'>
     </form>
     <?php
     include "/util.php";
     session_start();

     // Processa a submissão do formulário de nova senha
     if ($_POST) {  
          try {
              $conn = conecta();
              
              // Recebe e valida as senhas digitadas pelo usuário
              $senha1 = trim($_POST['senha1']);
              $senha2 = trim($_POST['senha2']);
              
              // Validação básica
              if (strlen($senha1) !== 6 || !ctype_digit($senha1)) {
                  throw new Exception("A senha deve conter exatamente 6 dígitos numéricos.");
              }
              
              // Recupera e valida o token
              if (!isset($_GET['token']) || empty($_GET['token'])) {
                  throw new Exception("Token inválido.");
              }
              $token = $_GET['token'];
              
              // VERIFICAÇÃO DE SESSÃO
              if (!isset($_SESSION["email"])) {
                  throw new Exception("Sessão de recuperação expirada. Por favor, comece o processo novamente.");
              }
              
              $email = $_SESSION["email"];
              
              // RECUPERAÇÃO DO TOKEN SALVO COM PREPARED STATEMENT
              $sql = "SELECT senha FROM usuario WHERE email = :email";
              $stmt = $conn->prepare($sql);
              $stmt->bindParam(':email', $email);
              $stmt->execute();
              $token_salvo = $stmt->fetchColumn();
              
              if (!$token_salvo) {
                  throw new Exception("Usuário não encontrado.");
              }
              
              // VALIDAÇÃO DO TOKEN
              if ($token_salvo === $token) {
                   // Verifica se as duas senhas digitadas são iguais
                   if ($senha1 === $senha2) {
                        // HASH DA NOVA SENHA
                        $senha_hash = password_hash($senha1, PASSWORD_DEFAULT);
                        
                        // ATUALIZAÇÃO DA SENHA COM PREPARED STATEMENT
                        $sql_update = "UPDATE usuario SET senha = :senha WHERE email = :email";
                        $stmt_update = $conn->prepare($sql_update);
                        $stmt_update->bindParam(':senha', $senha_hash);
                        $stmt_update->bindParam(':email', $email);
                        
                        if ($stmt_update->execute()) {
                            // Destrói a sessão
                            session_destroy();
                            echo "<br><span style='color: green;'>Senha alterada com sucesso!</span>";
                        } else {
                            throw new Exception("Erro ao atualizar a senha.");
                        }
                   } else {
                        echo "<br><span style='color: red;'>Senhas estão diferentes</span>";
                   }
              } else {
                   echo "<br><span style='color: red;'>Token inválido ou expirado. Tente novamente.</span>";
              }
              
          } catch (Exception $e) {
              echo "<br><span style='color: red;'>Erro: " . htmlspecialchars($e->getMessage()) . "</span>";
          }
          
          echo "<br><br><a href='/back-end/login.php'>Login</a>";
     }
     ?>
</html>