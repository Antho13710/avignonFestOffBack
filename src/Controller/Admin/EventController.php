<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    /**
     * @Route("/admin/evenements", name="admin_event_list", methods="GET")
     * @param EventRepository $eventRepository
     * @param PaginatorInterface $paginatorInterface
     * @param Request $request
     * @return Response
     */
    public function list(EventRepository $eventRepository, PaginatorInterface $paginatorInterface, Request $request): Response
    {
        $events = $paginatorInterface->paginate($eventRepository->findAll(), $request->query->getInt('page', 1), 10);
        $countEvent = count($eventRepository->findAll());
        return $this->render('admin/event/index.html.twig', [
            'events' => $events,
            'count' => $countEvent,
        ]);
    }

    /**
     * @Route("/admin/evenement/{id}",name="admin_event_show", methods="GET")
     * @param Event|null $event
     * @return Response
     */
    public function show(Event $event = null): Response
    {
        if ($event === null) {
            throw $this->createNotFoundException('événement introuvable');
        }

        return $this->render('admin/event/show.html.twig', ['event' => $event]);
    }

    /**
     * @Route("/admin/evenement/delete/{id}",name="admin_event_delete", methods="DELETE")
     */
    public function delete(Event $event = null, EntityManagerInterface $entityManagerInterface, Request $request)
    {
        if ($event === null) {
            throw $this->createNotFoundException('événement introuvable');
        }

        $token = $request->get('token');

        if ($this->isCsrfTokenValid('delete-event', $token)) {
            $entityManagerInterface->remove($event);
            $entityManagerInterface->flush();

            $this->addFlash('success', 'évenement supprimé avec succès');

            return $this->redirectToRoute('admin_event_list');
        }

        $this->addFlash('danger', 'Erreur Veuillez contacter un administrateur');
        return $this->redirectToRoute('admin_event_list');
    }
}
