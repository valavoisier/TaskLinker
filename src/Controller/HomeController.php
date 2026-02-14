<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')] 
    public function home(): Response 
    { 
        // Si l'utilisateur est connecté → redirection vers les projets 
        if ($this->getUser()) { 
            return $this->redirectToRoute('project_index');
        }
        // Sinon → page d'accueil publique
        return $this->render('auth/bienvenue.html.twig'); 
    }
    
}

