<?php

namespace App\Controller\Api;

use App\Repository\EventRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;

class ArtistController extends AbstractController
{
    /**
     * @Route("/api/home_artist", name="api_home_artist", methods="GET")
     */
    public function eventForHome(EventRepository $events, UserInterface $user): Response
    {
        //return events by User who are in DB in json format and their relations ('groups' => 'home_events')
        return $this->json($events->findBy(['user' => $user]), Response::HTTP_OK, [], ['groups' => 'home_events']);
    }
}

