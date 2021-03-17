<?php

namespace App\Controller\Api;

use App\Entity\Event;
use App\Repository\DateRepository;
use App\Repository\TypeRepository;
use App\Repository\EventRepository;
use App\Repository\PlaceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/api/home_events/{limit}", name="api_home_events", methods="GET")
     */
    public function eventsForHome(DateRepository $date, EventRepository $event, $limit): Response
    {
        $data['count'] = count($event->findAll());
        $data['events'] = $date->findEventByDateAsc($limit);
        //return events with limit who are in DB in json format and their relations ('groups' => 'home_events')
        return $this->json($data, Response::HTTP_OK, [], ['groups' => 'home_events']);
    }

    /**
     * @Route("/api/home_event/{id<\d+>}", name="api_home_event", methods="GET")
     */
    public function eventForHome(Event $event = null): Response
    {
        if (!$event) {
            return $this->json(['Message' => 'Cet évènement n\'existe pas.'], Response::HTTP_NOT_FOUND);
        }
        //return event by id who are in DB in json format and their relations ('groups' => 'home_events')
        return $this->json($event, Response::HTTP_OK, [], ['groups' => 'home_events']);
    }

    /**
     * @Route("/api/home_types", name="api_home_types", methods="GET")
     */
    public function typeForHome(TypeRepository $types): Response
    {
        //return all types who are in DB in json format ('groups' => 'home_types')
        return $this->json($types->findAllByASC(), Response::HTTP_OK, [], ['groups' => 'home_types']);
    }

    /**
     * @Route("/api/home_places", name="api_home_places", methods="GET")
     */
    public function placeForHome(PlaceRepository $places): Response
    {
        //return all types who are in DB in json format ('groups' => 'home_places')
        return $this->json($places->findAllByASC(), Response::HTTP_OK, [], ['groups' => 'home_places']);
    }

    /**
     * @Route("/api/filter", name="api_home_type", methods="GET")
     */
    public function filter(Request $request, EventRepository $event): Response
    {
        $date = $request->query->get('date');
        $type = $request->query->get('type');
        $place = $request->query->get('place');
        $price = $request->query->get('price');
        $word = $request->query->get('word');
        $limit = $request->query->get('limit');

        $data['count'] = count($event->filter($date, $type, $place, $price, $word));
        $data['events'] = $event->filter($date, $type, $place, $price, $word, $limit);

        //return all types who are in DB in json format ('groups' => 'home_types')
        return $this->json($data, Response::HTTP_OK, [], ['groups' => 'home_events']);
    }
}
