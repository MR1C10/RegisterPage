<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeload();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phpmailer = new PHPMailer(true);

    try {
        $phpmailer->isSMTP();
        $phpmailer->SMTPAuth = true;
        $phpmailer->Host = $_ENV['SMTP_HOST'];
        $phpmailer->Port = $_ENV['SMTP_PORT'];
        $phpmailer->Username = $_ENV['SMTP_USER'];
        $phpmailer->Password = $_ENV['SMTP_PASS'];
        $phpmailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

        $phpmailer->setFrom('mauricio.souza@frsp.org', $_POST['nome']);
        $phpmailer->addAddress('mauriciorcsouza1206@gmail.com');

        $phpmailer->isHTML(true);
        $phpmailer->Subject = 'Nova inscrição recebida';
        $phpmailer->Body = "
            <h2>Nova inscrição no formulário:</h2>
            <p><strong>Nome:</strong> {$_POST['nome']}</p>
            <p><strong>Gênero:</strong> {$_POST['genero']}</p>
            <p><strong>Idade:</strong> {$_POST['idade']}</p>
            <p><strong>Telefone:</strong> {$_POST['telefone']}</p>
            <p><strong>Linguagens e Tecnologias:</strong> {$_POST['linguagens']}</p>
            <p><strong>Motivo:</strong><br>{$_POST['porque']}</p>
        ";

        if ($phpmailer->send()) {
            echo 'Email enviado com sucesso!';
        } else {
            echo 'Erro ao enviar e-mail.';
        }
    } catch (Exception $e) {
        echo "Erro ao enviar e-mail: {$phpmailer->ErrorInfo}";
    }
} else {
    echo "Método de requisição inválido.";
}
