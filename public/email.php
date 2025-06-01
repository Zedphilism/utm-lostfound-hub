<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php'; // Load Composer's autoloader

function sendEmail($to, $subject, $messageBody) {
    $mail = new PHPMailer(true);

    try {
        // Server config
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'teamwp833@gmail.com';          // ✅ change this
        $mail->Password   = 'neza zeqs ufqq qcuo';       // ✅ use App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('teamwp833@gmail.com', 'Lost & Found');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $messageBody;

        return $mail->send();
    } catch (Exception $e) {
        error_log('Email error: ' . $mail->ErrorInfo);
        return false;
    }
}
