<?php
namespace App\Controller;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class RechercheController extends AbstractController
{
    #[Route('/film', name: 'app_film')]
    public function index(Request $request): Response
    {
        $titre = $request->query->get('titre', '');
        $passkey = "146bd42efc53d4e6128d431139e04cd2";
        $client = new CurlHttpClient();

        $results = [];
        if (!empty($titre)) {
            $rechercheForApi = urlencode($titre);
            $apiUrl = "https://www.sharewood.tv/api/$passkey/search?name=$rechercheForApi";

            $response = $client->request('GET', $apiUrl, [
                'headers' => ['Accept' => 'application/json'],
                'verify_peer' => false,
            ]);

            $results = $response->toArray() ?? [];
        }

        return $this->render('film/index.html.twig', [
            'titre' => $titre,
            'data' => $results,
        ]);
    }

    #[Route('/search', name: 'app_film_search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $titre = $request->query->get('titre', '');
        $passkey = "146bd42efc53d4e6128d431139e04cd2";
        $client = new CurlHttpClient();

        $results = [];
        if (!empty($titre)) {
            $rechercheForApi = urlencode($titre);
            $apiUrl = "https://www.sharewood.tv/api/$passkey/search?name=$rechercheForApi";

            $response = $client->request('GET', $apiUrl, [
                'headers' => ['Accept' => 'application/json'],
                'verify_peer' => false,
            ]);

            $results = $response->toArray() ?? [];
        }

        return new JsonResponse($results);
    }

    #[Route('/download', name: 'app_film_download', methods: ['POST'])]
    public function download(Request $request): JsonResponse
    {

        $url = $request->request->get('url');
        $filename = $request->request->get('filename');
        if (empty($filename)) {
            $filename = basename(parse_url($url, PHP_URL_PATH)); // Récupère le vrai nom du fichier depuis l'URL
        }
        
        if (empty($url)) {
            return new JsonResponse(['error' => 'URL de téléchargement manquante'], Response::HTTP_BAD_REQUEST);
        }

        // Vérifie que l'URL est correcte
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return new JsonResponse(['error' => 'URL invalide : ' . $url], Response::HTTP_BAD_REQUEST);
        }

        $savePath = $this->getParameter('kernel.project_dir') . '/public/torrents/' . basename($filename);

        try {
            $client = new CurlHttpClient();
            $response = $client->request('GET', $url, [
                'headers' => ['Accept' => 'application/x-bittorrent'],
                'verify_peer' => false,
            ]);

            // Vérifie si la réponse est valide
            if ($response->getStatusCode() !== 200) {
                return new JsonResponse(['error' => 'Échec du téléchargement. Code HTTP: ' . $response->getStatusCode()], Response::HTTP_BAD_REQUEST);
            }

            file_put_contents($savePath, $response->getContent());

            return new JsonResponse([
                'success' => true,
                'message' => 'Téléchargement terminé',
                'path' => '/torrents/' . basename($filename)
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors du téléchargement : ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



}