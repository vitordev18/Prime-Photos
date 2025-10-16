<?php
// Usando a superglobal $_SERVER['DOCUMENT_ROOT'] para criar um caminho absoluto e confiável
$project_root = $_SERVER['DOCUMENT_ROOT'];

// Incluindo os arquivos da biblioteca PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once $project_root . '/PHPMailer/src/Exception.php';
require_once $project_root . '/PHPMailer/src/PHPMailer.php';
require_once $project_root . '/PHPMailer/src/SMTP.php';

function EnviaEmail($pEmailDestino, $pAssunto, $pHtml) {    
    // Configurações do seu servidor de e-mail
    $smtp_user = "ecommerce@efesonet.com";
    $smtp_pass = "u!G8mDRr6PBXkH6";
    $smtp_host = "smtp.efesonet.com";
    $smtp_port = 587;

    $mail = new PHPMailer(true);

    try {
        // Configurações do servidor
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host       = $smtp_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtp_user;
        $mail->Password   = $smtp_pass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $smtp_port;
        
        // UTF-8 para compatibilidade com acentos
        $mail->CharSet = 'UTF-8';

        // Remetente e destinatário
        $mail->setFrom($smtp_user, 'Prime Photos');
        $mail->addAddress($pEmailDestino);
        
        // Conteúdo
        $mail->isHTML(true);
        $mail->Subject = $pAssunto;
        $mail->Body    = $pHtml;
        $mail->AltBody = strip_tags($pHtml);

        $mail->send();
        return true;

    } catch (Exception $e) {
        // Loga o erro para que você possa ver o que deu errado no servidor
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        return false;
    }      
}
?>