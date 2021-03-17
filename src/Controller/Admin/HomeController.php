<?php

namespace App\Controller\Admin;

use App\Repository\ContactRepository;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_home", methods="GET")
     */
    public function home(ContactRepository $contactRepository, EventRepository $eventRepository): Response
    {
        $countEvent = count($eventRepository->findAll());
        
        return $this->render('home.html.twig', [
            "count" => $countEvent,
            "messages" => $contactRepository->findForHomeAdmin(),
            "events" => $eventRepository->findForHomeAdmin()
        ]);
    }
}
