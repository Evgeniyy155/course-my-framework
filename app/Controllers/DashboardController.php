<?php

namespace App\Controllers;

use Web\Framework\Controller\AbstractController;
use Web\Framework\Http\Response;

class DashboardController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('dashboard.html.twig');
    }
}