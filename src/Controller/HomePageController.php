<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{
    #[Route('/', name: 'home_page')]
    public function index(): Response
    {   if($this->getUser()){
        return $this->redirectToRoute('dashboard');
    }
        return $this->render('home_page/index.html.twig', [
            'controller_name' => 'HomePageController',
        ]);
    }

    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'HomePageController',
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): Response
    {
        return $this->redirectToRoute('hompage');
    }
}
