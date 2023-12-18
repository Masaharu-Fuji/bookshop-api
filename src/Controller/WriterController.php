<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WriterController extends AbstractController
{
    #[Route('/writer', name: 'app_writer')]
    public function index(): Response
    {
        return $this->render('writer/index.html.twig', [
            'controller_name' => 'Tweet',
        ]);
    }
}
