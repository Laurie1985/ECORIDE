<?php
namespace App\Controllers;

class HomeController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'EcoRide - Accueil',
        ];

        $this->render('home/index', $data);
    }
}
