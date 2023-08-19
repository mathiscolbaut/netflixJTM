<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DownloadController extends AbstractController
{
    #[Route('/download', name: 'app_download')]
    public function index(): Response
    {
        if (isset($_GET['title'])) {
            $title = $_GET['title'];
        } else {
            $title = "Harry Potter"; // Valeur par défaut si le titre n'est pas présent dans l'URL
        }
        // Préparer les données JSON à envoyer
        $data = [
            'text' => $title
        ];

        // Effectuer la requête API
        $httpClient = HttpClient::create();
        $response = $httpClient->request('POST', 'http://localhost:3000/api/getTorrent', [
            'json' => $data
        ]);

        // Récupérer le contenu JSON de la réponse
        $content = $response->getContent();
        $torrentData = json_decode($content, true);

        return $this->render('download/index.html.twig', [
            'controller_name' => 'DownloadController',
            'titreFilm' => $title,
            'torrentData' => $torrentData,
        ]);
    }
    #[Route('/refresh', name: 'app_refresh')]
    public function refresh(Request $request, HttpClientInterface $httpClient): Response
    {
        $title = $request->get('search_title'); // Get the search value from the request

        // Perform the API request with the new title
        $data = [
            'text' => $title
        ];
        $response = $httpClient->request('POST', 'http://localhost:3000/api/getTorrent', [
            'json' => $data
        ]);

        $content = $response->getContent();
        $torrentData = json_decode($content, true);

        return $this->render('download/index.html.twig', [
            'torrentData' => $torrentData,
        ]);
    }
    #[Route('/downloadTorrent', name: 'app_downloadTorrent')]
    public function downloadTorrent(): Response
    {
         echo '<p>Hello World</p>'; 
    }
}
