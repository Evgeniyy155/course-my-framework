<?php

namespace App\Controllers;

use App\Services\YouTubeService;
use Twig\Environment;
use Web\Framework\Controller\AbstractController;
use Web\Framework\Http\Response;

class HomeController extends AbstractController
{
    public function __construct(
        private readonly YouTubeService $youTubeService,
    )
    {

    }

    public function index(): Response
    {
        return $this->render('home.html.twig', [
            'YouTubeServices' => $this->youTubeService->getUrl(),
        ]);
    }
}