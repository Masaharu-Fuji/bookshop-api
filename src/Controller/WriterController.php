<?php

namespace App\Controller;

use App\Entity\Writer;
use App\Form\ApiGetWriterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WriterController extends AbstractController
{
    #[Route('/writer', name: 'app_writer')]
    public function index(
        Request $request,
    ): Response {
        $writer = new Writer();
        $form = $this->createForm(ApiGetWriterType::class, $writer, [
            'action' => $this->generateUrl('app_writer'),
            'method' => 'GET',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute(
                'app_writer'
            );
        }
        return $this->render('writer/index.html.twig', [
            'controller_name' => 'Writer',
            'writer_form' => $form,
        ]);
    }
}
