<?php

namespace App\Controller\Admin;

use App\Entity\Alert;
use App\Entity\Event;
use App\Repository\AlertRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlertController extends AbstractController
{
    /**
     * Display all alerts list sort by event
     *
     * @Route("/admin/alert", name="admin_alert_list", methods="GET")
     */
    public function index(AlertRepository $alertRepository): Response
    {
        return $this->render('admin/alert/index.html.twig', [
            'alerts' => $alertRepository->findAllSortByEvent(),
        ]);
    }

    /**
     * Display all alerts for one event
     *
     * @Route("/admin/alert/event/{id}",name="admin_alert_by_event", methods="GET")
     */
    public function alertsByEvent(AlertRepository $alertRepository, Event $event = null)
    {
        if ($event === null) {
            throw $this->createNotFoundException("Ooops ! -_-' Cette page n'existe pas !");
        }

        $alerts = $alertRepository->findBy(['event' => $event->getId()]);

        if (empty($alerts)) {
            throw $this->createNotFoundException("Ooops ! -_-' Cette page n'existe pas !");
        }

        return $this->render('admin/alert/index.html.twig', ['alerts' => $alerts]);
    }

    /**
     * Delete one alert
     *
     * @Route("/admin/alert/delete{id<\d+>}", name="admin_alert_delete", methods="DELETE")
     */
    public function delete(Alert $alert = null, EntityManagerInterface $entityManagerInterface, Request $request)
    {
        if ($alert === null) {
            throw $this->createNotFoundException("Ooops ! -_-' Cette page n'existe pas !");
        }

        $token = $request->request->get('token');

        if ($this->isCsrfTokenValid('delete-alert', $token)) {
            $entityManagerInterface->remove($alert);
            $entityManagerInterface->flush();
            
            $this->addFlash('success', 'Signalement supprimé.');
        }

        $this->addFlash('danger', 'Veuillez contacter un administrateur');
        return $this->redirectToRoute('admin_alert_list');
    }
}
