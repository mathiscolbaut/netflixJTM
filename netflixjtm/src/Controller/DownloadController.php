<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Filesystem\Filesystem;

class DownloadController extends AbstractController
{
    private $apiBaseUrl = 'https://www.sharewood.tv/api/146bd42efc53d4e6128d431139e04cd2'; // L'URL de l'API Sharewood
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('/download', name: 'app_download')]
    public function index(Request $request): Response
    {
        $title = $request->query->get('title', 'Harry Potter'); // Par défaut, on utilise 'Harry Potter' si aucun titre n'est fourni.

        // Préparer les paramètres pour l'API Sharewood
        $apiUrl = $this->apiBaseUrl . '/search?name=' . urlencode($title) . '&limit=25'; // Limite de 25 résultats par défaut

        // Faire la requête GET à l'API
        $response = $this->httpClient->request('GET', $apiUrl);

        // Récupérer le contenu JSON de la réponse
        $content = $response->getContent();
        $torrentData = json_decode($content, true); // Le contenu est déjà un tableau indexé

        return $this->render('download/index.html.twig', [
            'controller_name' => 'DownloadController',
            'titreFilm' => $title,
            'torrentData' => $torrentData, // On passe directement les données du tableau
        ]);
    }

    #[Route('/refresh', name: 'app_refresh')]
    public function refresh(Request $request): Response
    {
        $title = $request->get('search_title', ''); // Récupère la chaîne de recherche

        // Préparer les paramètres pour l'API Sharewood
        $apiUrl = $this->apiBaseUrl . '/search?name=' . urlencode($title) . '&limit=25'; // Limite de 25 résultats

        // Faire la requête GET à l'API
        $response = $this->httpClient->request('GET', $apiUrl);

        // Récupérer le contenu JSON de la réponse
        $content = $response->getContent();
        $torrentData = json_decode($content, true); // Le contenu est un tableau

        return $this->render('download/index.html.twig', [
            'torrentData' => $torrentData,
        ]);
    }

    #[Route('/downloadTorrent', name: 'app_downloadTorrent')]
    public function downloadTorrent(Request $request): Response
    {
        // Récupérer l'URL de téléchargement et le nom du torrent depuis le formulaire
        $downloadUrl = $request->query->get('link');
        $torrentName = $request->query->get('name'); // Le nom réel du torrent est passé dans le formulaire

        if (!$downloadUrl || !$torrentName) {
            return new Response('URL ou nom de téléchargement manquant', Response::HTTP_BAD_REQUEST);
        }

        // Faire la requête pour télécharger le fichier torrent
        $response = $this->httpClient->request('GET', $downloadUrl);

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            return new Response('Erreur lors du téléchargement du fichier', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Récupérer le contenu du fichier torrent
        $torrentContent = $response->getContent();

        // Définir le chemin où sauvegarder le fichier sur le serveur
        $savePath = $this->getParameter('kernel.project_dir') . '/public/torrents/';
        $filename = str_replace(' ', '_', $torrentName) . '.torrent'; // Nom du fichier avec remplacement des espaces

        // Utiliser le composant Filesystem pour sauvegarder le fichier sur le serveur
        $filesystem = new Filesystem();
        $filesystem->mkdir($savePath); // Créer le dossier s'il n'existe pas
        $filesystem->dumpFile($savePath . $filename, $torrentContent);

        // Optionnel : Retourner une réponse ou un message de confirmation
        return new Response('Fichier torrent téléchargé avec succès sous le nom : ' . $filename);
    }

}
