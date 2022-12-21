<?php

namespace App\Controller;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpClient\NativeHttpClient;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RechercheController extends AbstractController
{

  
    #[Route('/recherchefilm', name: 'app_recherchefilm')]
    public function index(): Response
    {
        $titre = $_GET["titre"];
        $rechercheForApi = str_replace(' ', '$', $titre);
        $client = new CurlHttpClient();
        
        $response = $client->request('GET', 'https://api.themoviedb.org/3/search/multi?api_key=1d3427f37b6eee91dcbf8fd38e04f6c9&language=fr&query='.$rechercheForApi.'&include_adult=true',[ 'verify_peer' => false,]);
        $content = $response->toArray();

        $result = $content['results'];
       
       for ($i=0; $i < max(array_keys($result)) ; $i++) { 
            if(empty($result[$i]['title']))
            {
                $result[$i]['title'] = $result[$i]['title'] = "Aucun Titre (Ne télécharge pas si tu ne veux pas voir ton compte BAN)";
            }
            if(empty($result[$i]['backdrop_path']))
            {
                $result[$i]['backdrop_path'] = $result[$i]['backdrop_path'] = "https://media.giphy.com/media/baPIkfAo0Iv5K/giphy.gif";
            }
            else{
                $result[$i]['backdrop_path']= "https://image.tmdb.org/t/p/w500/".$result[$i]['backdrop_path'];
            }
            if(empty($result[$i]['overview']))
            {
                $result[$i]['overview'] = $result[$i]['overview'] = "Il n'y a pas de description";
                
            }
            


       }        
        return $this->render('recherche/index.html.twig', [
            'controller_name' => 'RechercheController',
            'titre' => $rechercheForApi,
            'data' => $result,
        ]);
    }


 
   
   
    #[Route('/rechercheserie', name: 'app_rechercheserie')]
    public function serie(): Response
    {
        $titre = $_GET["titre"];
        $rechercheForApi = str_replace(' ', '$', $titre);
        $client = new CurlHttpClient();
        
        $response = $client->request('GET', 'https://api.themoviedb.org/3/search/tv?api_key=1d3427f37b6eee91dcbf8fd38e04f6c9&language=fr-FR&query='.$rechercheForApi.'&include_adult=true',[ 'verify_peer' => false,]);
        $content = $response->toArray();
       
        $result = $content['results'];
       
       for ($i=0; $i < max(array_keys($result)) ; $i++) { 
            if(empty($result[$i]['name']))
            {
                $result[$i]['name'] = $result[$i]['name'] = "Aucun Titre (Ne télécharge pas si tu ne veux pas voir ton compte BAN)";
            }
            if(empty($result[$i]['backdrop_path']))
            {
                $result[$i]['backdrop_path'] = $result[$i]['backdrop_path'] = "https://media.giphy.com/media/baPIkfAo0Iv5K/giphy.gif";
            }
            else{
                $result[$i]['backdrop_path']= "https://image.tmdb.org/t/p/w500/".$result[$i]['backdrop_path'];
            }
            if(empty($result[$i]['overview']))
            {
                $result[$i]['overview'] = $result[$i]['overview'] = "Il n'y a pas de description";
                
            }
            


       }
    

           
        
            
        return $this->render('recherche/rechercheserie.html.twig', [
            'controller_name' => 'RechercheController',
            'titre' => $rechercheForApi,
            'data' => $result,
        ]);
    }
    /**
     * @Route("/voirplus/{idSerie}", name="app_voirplus")
     */
    public function voirplus(string $idSerie): Response
    {
        
        

        $client = new CurlHttpClient();
        
        $response = $client->request('GET', 'https://api.themoviedb.org/3/tv/'.$idSerie.'?api_key=1d3427f37b6eee91dcbf8fd38e04f6c9&language=fr_FR',[ 'verify_peer' => false,]);
        $content = $response->toArray();
       
        $seasons = $content['seasons'];

        for ($i=0; $i < max(array_keys($seasons)) ; $i++) { 
            if(empty($seasons[$i]['name']))
            {
                $seasons[$i]['name'] = $seasons[$i]['name'] = "Aucun Titre (Ne télécharge pas si tu ne veux pas voir ton compte BAN)";
            }
            if(empty($seasons[$i]['poster_path']))
            {
                $seasons[$i]['poster_path'] = $seasons[$i]['poster_path'] = "https://media.giphy.com/media/baPIkfAo0Iv5K/giphy.gif";
            }
            else{
                $seasons[$i]['poster_path']= "https://image.tmdb.org/t/p/w500/".$seasons[$i]['poster_path'];
            }
            if(empty($seasons[$i]['overview']))
            {
                $seasons[$i]['overview'] = $seasons[$i]['overview'] = "Il n'y a pas de description";
                
            }

        }
        return $this->render('recherche/voirplus.html.twig', [
            'controller_name' => 'RechercheController',
            'idSerie'=>$idSerie,
            'seasons' => $seasons,
            
        ]);
    }
}