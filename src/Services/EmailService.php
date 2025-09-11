<?php
namespace App\Services;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class EmailService
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configure();
    }

    private function configure()
    {
        try {
            // Configuration SMTP avec vos variables
            $this->mailer->isSMTP();
            $this->mailer->Host       = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
            $this->mailer->SMTPAuth   = true;
            $this->mailer->Username   = $_ENV['MAIL_USERNAME'] ?? '';
            $this->mailer->Password   = $_ENV['MAIL_PASSWORD'] ?? '';
            $this->mailer->SMTPSecure = $_ENV['MAIL_ENCRYPTION'] === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
            $this->mailer->Port       = $_ENV['MAIL_PORT'] ?? 587;

            // Configuration expéditeur
            $this->mailer->setFrom(
                $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@ecoride.com',
                $_ENV['MAIL_FROM_NAME'] ?? 'Ecoride'
            );

            $this->mailer->CharSet = 'UTF-8';
            $this->mailer->isHTML(true);

            // Debug pour le développement (à désactiver en production)
            if ($_ENV['APP_ENV'] === 'development') {
                $this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;
            }

        } catch (Exception $e) {
            error_log("Erreur configuration PHPMailer: " . $e->getMessage());
            throw $e;
        }
    }

    public function sendTripStartedNotification($passengers, $carpool)
    {
        foreach ($passengers as $passenger) {
            try {
                $this->mailer->clearAddresses();
                $this->mailer->addAddress($passenger['email'], $passenger['username']);

                $this->mailer->Subject = 'Votre covoiturage a commencé !';
                $this->mailer->Body    = $this->getTripStartedTemplate($passenger, $carpool);

                $this->mailer->send();
                error_log("Email de démarrage envoyé à {$passenger['email']}");

            } catch (Exception $e) {
                error_log("Erreur envoi email à {$passenger['email']}: " . $e->getMessage());
            }
        }
    }

    public function sendTripCompletedNotification($passengers, $carpool)
    {
        foreach ($passengers as $passenger) {
            try {
                $this->mailer->clearAddresses();
                $this->mailer->addAddress($passenger['email'], $passenger['username']);

                $this->mailer->Subject = 'Covoiturage terminé - Confirmez votre trajet';
                $this->mailer->Body    = $this->getTripCompletedTemplate($passenger, $carpool);

                $this->mailer->send();
                error_log("Email de confirmation envoyé à {$passenger['email']}");

            } catch (Exception $e) {
                error_log("Erreur envoi email à {$passenger['email']}: " . $e->getMessage());
            }
        }
    }

    public function sendCancellationNotification($passengers, $carpool, $reason = '')
    {
        foreach ($passengers as $passenger) {
            try {
                $this->mailer->clearAddresses();
                $this->mailer->addAddress($passenger['email'], $passenger['username']);

                $this->mailer->Subject = 'Covoiturage annulé - Remboursement effectué';
                $this->mailer->Body    = $this->getCancellationTemplate($passenger, $carpool, $reason);

                $this->mailer->send();
                error_log("Email d'annulation envoyé à {$passenger['email']}");

            } catch (Exception $e) {
                error_log("Erreur envoi email à {$passenger['email']}: " . $e->getMessage());
            }
        }
    }

    private function getTripStartedTemplate($passenger, $carpool)
    {
        $departureDate = date('d/m/Y à H:i', strtotime($carpool['departure_time']));

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #28a745; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .trip-info { background: white; padding: 15px; border-left: 4px solid #28a745; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Votre covoiturage a commencé !</h2>
                </div>
                <div class='content'>
                    <p>Bonjour <strong>{$passenger['username']}</strong>,</p>

                    <p>Le conducteur vient de démarrer le trajet. Bon voyage !</p>

                    <div class='trip-info'>
                        <h3>Détails du trajet :</h3>
                        <ul>
                            <li><strong>Trajet :</strong> {$carpool['departure']} → {$carpool['arrival']}</li>
                            <li><strong>Départ prévu :</strong> {$departureDate}</li>
                            <li><strong>Places réservées :</strong> {$passenger['seats_booked']}</li>
                        </ul>
                    </div>

                    <p>À la fin du trajet, vous recevrez un email pour confirmer que tout s'est bien passé.</p>

                    <p>Bonne route !<br>
                    L'équipe Ecoride</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    private function getTripCompletedTemplate($passenger, $carpool)
    {
        $confirmUrl = ($_ENV['APP_URL'] ?? 'http://localhost') . "/reservations/confirm/{$passenger['reservation_id']}";

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #007bff; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .trip-info { background: white; padding: 15px; border-left: 4px solid #007bff; margin: 15px 0; }
                .btn { display: inline-block; background: #28a745; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 10px 0; }
                .btn:hover { background: #218838; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Covoiturage terminé</h2>
                </div>
                <div class='content'>
                    <p>Bonjour <strong>{$passenger['username']}</strong>,</p>

                    <p>Le trajet est maintenant terminé. Nous espérons que tout s'est bien passé !</p>

                    <div class='trip-info'>
                        <h3>Trajet effectué :</h3>
                        <ul>
                            <li><strong>Trajet :</strong> {$carpool['departure']} → {$carpool['arrival']}</li>
                            <li><strong>Places utilisées :</strong> {$passenger['seats_booked']}</li>
                        </ul>
                    </div>

                    <p><strong>Veuillez confirmer que tout s'est bien passé :</strong></p>

                    <p style='text-align: center;'>
                        <a href='{$confirmUrl}' class='btn'>Confirmer le trajet</a>
                    </p>

                    <p><em>Cette confirmation permet au conducteur de recevoir ses crédits. Si vous avez rencontré des problèmes durant le trajet, vous pourrez les signaler sur la page de confirmation.</em></p>

                    <p>Merci d'utiliser Ecoride !<br>
                    L'équipe Ecoride</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    private function getCancellationTemplate($passenger, $carpool, $reason)
    {
        $reasonText = $reason ? "<p><strong>Motif :</strong> " . htmlspecialchars($reason) . "</p>" : "";

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #dc3545; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .trip-info { background: white; padding: 15px; border-left: 4px solid #dc3545; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Covoiturage annulé</h2>
                </div>
                <div class='content'>
                    <p>Bonjour <strong>{$passenger['username']}</strong>,</p>

                    <p>Nous vous informons que le covoiturage suivant a été annulé par le conducteur :</p>

                    <div class='trip-info'>
                        <h3>Trajet annulé :</h3>
                        <ul>
                            <li><strong>Trajet :</strong> {$carpool['departure']} → {$carpool['arrival']}</li>
                            <li><strong>Date prévue :</strong> " . date('d/m/Y à H:i', strtotime($carpool['departure_time'])) . "</li>
                            <li><strong>Places réservées :</strong> {$passenger['seats_booked']}</li>
                        </ul>
                        {$reasonText}
                    </div>

                    <p><strong>Remboursement automatique :</strong><br>
                    Vos crédits ont été automatiquement remboursés sur votre compte.</p>

                    <p>Nous nous excusons pour ce désagrément. N'hésitez pas à rechercher un autre covoiturage sur notre plateforme.</p>

                    <p>L'équipe Ecoride</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
