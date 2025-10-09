<?php 
ini_set ('display_errors',1); // mostra TODOS os erros do php
error_reporting (E_ALL);

require_once dirname(__DIR__) . '/PHPMailer/src/PHPMailer.php';
require_once dirname(__DIR__) . '/PHPMailer/src/SMTP.php';

function EnviaEmail ($pEmailDestino, $pAssunto, $pHtml, 
                     $pUsuario = "ecommerce@efesonet.com", 
                     $pSenha = "u!G8mDRr6PBXkH6", 
                     $pSMTP = "smtp.efesonet.com") {    

  global $PHPMailer;
  
  try {
    //cria instancia de phpmailer
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->IsSMTP(); // diz ao php que o servidor eh SMTP
    // servidor smtp
    $mail->Host = $pSMTP; // configura o servidor
    $mail->SMTPAuth = true; // requer autenticacao com o servidor                         
    $mail->SMTPSecure = 'tls';  // nivel de seguranca                           
    $mail-> SMTPOptions = array ('ssl' => array ('verificar_peer' => false, 'verify_peer_name' => false,
    'allow_self_signed' => true));
    $mail->Port = 587;  // porta do serviço no servidor     
    $mail->Username = $pUsuario; 
    $mail->Password = $pSenha; 
    $mail->From = $pUsuario; 
    $mail->FromName = "Recuperação de senhas"; 
    $mail->AddAddress($pEmailDestino, "Usuario"); 
    $mail->IsHTML(true); // o conteudo enviado eh html (poderia ser txt comum sem formato)
    $mail->Subject = $pAssunto; 
    $mail->Body = $pHtml;
    $enviado = $mail->Send(); // disparo

    return $enviado;

  } catch (PHPMailer\PHPMailer\Exception $e) {
      return false;
  } catch (Exception $e) {
      echo $e->getMessage(); // erros da aplicacao - gerais
      return false;
  }      
}
?>