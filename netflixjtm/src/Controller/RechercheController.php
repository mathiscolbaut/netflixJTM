<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RechercheController extends AbstractController
{
    /**
     * @Route("/recherche", name="app_recherche")
     */
    public function index(): Response
    {
        $titre = $_GET["titre"];

        $rechercheForApi = str_replace(' ', '$', $titre);
        $urlImage= "https://image.tmdb.org/t/p/w500/";
        $curl = curl_init('https://api.themoviedb.org/3/search/multi?api_key=1d3427f37b6eee91dcbf8fd38e04f6c9&language=fr&query='.$rechercheForApi.'&page=1&include_adult=true');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($curl);
    
        if($data == false)
        {
            var_dump(curl_error($curl));
        }
        else{
            $data = json_decode($data,true);
            $totalResults =$data['total_results'];
            $compteur =0;
            echo '<pre>';
    
    
    
                for ($i = -3; $i <= $totalResults; $i++) {
    
                        echo $data['results'][''.$compteur.'']['name'];
                        echo $data['results'][''.$compteur.'']['title'];
                        echo '<br>';
    
                        $url = $data['results'][''.$compteur.'']['poster_path'];
                        $url = $urlImage . $url;
                        echo '<img src="' . $url . '" alt="ScanLine" width="10%" height="30%" border="0" />';
                        echo '<br>';
                        echo '<br>';
                        echo '<input  type="button" name="Telecharger" value="TELECHARGER" </input>';
    
    
                        echo '<br>';
                        echo '<br>';
                        $totalResults = $totalResults-1;
                        $compteur=$compteur+1;
    
                    }
    
            echo '</pre>';
    
        }
       // curl_close($curl);
        
        
            
        return $this->render('recherche/index.html.twig', [
            'controller_name' => 'RechercheController',
            'titre' => $rechercheForApi,
            
        ]);
    }
}
