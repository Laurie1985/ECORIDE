<?php
namespace App\Controllers;

class HomeController extends BaseController
{
    public function index()
    {
        $data = [
            'title'   => 'Ecoride - Accueil',
            'cssFile' => 'home',
        ];

        $this->render('home/index', $data);
    }
    public function contact()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleContactForm();
        }

        $this->render('contact', [
            'title'      => 'Ecoride - Contact',
            'cssFile'    => 'contact',
            'csrf_token' => $this->generateCsrfToken(),
        ]);
    }

    private function handleContactForm()
    {
        if (! $this->validateCsrfToken()) {
            $_SESSION['error'] = 'Token invalide';
            return;
        }

        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (empty($name) || empty($email) || empty($message)) {
            $_SESSION['error'] = 'Tous les champs obligatoires doivent être remplis';
            return;
        }

        $_SESSION['success'] = 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.';
    }
}
