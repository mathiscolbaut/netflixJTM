<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AccueilController extends AbstractController
{
    private HttpClientInterface $httpClient;
    private string $tmdbApiKey;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->tmdbApiKey = '1d3427f37b6eee91dcbf8fd38e04f6c9'; // Remplace par ta clé API TMDB
    }

    /**
     * @Route("/", name="app_accueil")
     */
    public function index(): Response
    {
        // Requête API TMDB pour récupérer les tendances
        $response = $this->httpClient->request('GET', "https://api.themoviedb.org/3/trending/all/week?api_key={$this->tmdbApiKey}&language=fr-FR");

        $data = $response->toArray();
        $trending = $data['results'] ?? []; // Récupérer la liste des films/séries tendances

        return $this->render('accueil/index.html.twig', [
            'trending' => $trending,
        ]);
    }
}
