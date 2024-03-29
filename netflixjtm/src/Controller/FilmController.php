<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpClient\NativeHttpClient;

class FilmController extends AbstractController
{
    #[Route('/film', name: 'app_film')]
    public function index(): Response
    {
      
        return $this->render('film/index.html.twig', [
            'controller_name' => 'FilmController',
        ]);
    }
}
