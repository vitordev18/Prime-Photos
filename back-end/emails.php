<?php 
// Configurações de email através de variáveis de ambiente
function getEmailConfig() {
    return [
        'usuario' => getenv('SMTP_USER') ?: "ecommerce@efesonet.com",
        'senha' => getenv('SMTP_PASS') ?: "u!G8mDRr6PBXkH6",
        'smtp' => getenv('SMTP_HOST') ?: "smtp.efesonet.com",
        'porta' => getenv('SMTP_PORT') ?: 587
    ];
}

function EnviaEmail($pEmailDestino, $pAssunto, $pHtml) {    
    $config = getEmailConfig();

    try {
        // Verificar dependências
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            require_once dirname(__DIR__) . '/PHPMailer/src/PHPMailer.php';
            require_once dirname(__DIR__) . '/PHPMailer/src/SMTP.php';
            require_once dirname(__DIR__) . '/PHPMailer/src/Exception.php';
        }

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Configurações do servidor
        $mail->isSMTP();
        $mail->Host = $config['smtp'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['usuario'];
        $mail->Password = $config['senha'];
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $config['porta'];
        
        // Configurações de SSL
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        // Remetente e destinatário
        $mail->setFrom($config['usuario'], 'Prime Photos - Recuperação de Senha');
        $mail->addAddress($pEmailDestino);
        
        // Conteúdo
        $mail->isHTML(true);
        $mail->Subject = $pAssunto;
        $mail->Body = $pHtml;
        $mail->AltBody = strip_tags($pHtml);

        return $mail->send();

    } catch (PHPMailer\PHPMailer\Exception $e) {
        error_log("Erro PHPMailer: " . $e->errorMessage());
        return false;
    } catch (Exception $e) {
        error_log("Erro geral no envio de email: " . $e->getMessage());
        return false;
    }      
}
?>