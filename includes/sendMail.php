<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Fonction pour envoyer un e-mail de confirmation
function envoyerEmailConfirmation($email) {
    $mail = new PHPMailer(true); // Instantiation de PHPMailer
    try {
        // Configuration du serveur SMTP
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST']; // Utiliser la variable d'environnement
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USER']; // Utiliser la variable d'environnement
        $mail->Password = $_ENV['SMTP_PASS']; // Utiliser la variable d'environnement
        $mail->SMTPSecure = 'tls';
        $mail->Port = $_ENV['SMTP_PORT']; // Utiliser la variable d'environnement

        // Destinataires
        $mail->setFrom($_ENV['SMTP_USER'], 'Nom de votre application');
        $mail->addAddress($email);

        // Contenu de l'e-mail
        $mail->isHTML(true);
        $mail->Subject = 'Confirmation de votre inscription';
        $mail->Body    = 'Merci de vous être inscrit. Veuillez confirmer votre adresse e-mail.';

        // Envoi de l'e-mail
        $mail->send();
    } catch (Exception $e) {
        echo 'Erreur lors de l\'envoi de l\'e-mail : ' . $mail->ErrorInfo;
    }
}
?> 