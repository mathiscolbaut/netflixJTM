<?php
namespace App\Controller;

use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;  // Importation manquante
use Symfony\Component\HttpFoundation\JsonResponse;  // Ajout de l'importation



class AuthController extends AbstractController
{
    /**
     * @Route("/signup", name="app_signup")
     */
    public function signup(Request $request): Response
    {
        $form = $this->createForm(UserType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vous enverrez les données à votre API ici
            $userData = $form->getData();

            // Envoyer la demande à l'API ici (démarche avec JS)
        }

        return $this->render('Auth/signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }
     /**
     * @Route("/login", name="app_login", methods={"GET"})
     */
    public function login(Request $request): Response
    {
        // Afficher la page de connexion
        return $this->render('auth/login.html.twig');
    }
}