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
}
