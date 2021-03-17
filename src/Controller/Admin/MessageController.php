<?php

namespace App\Controller\Admin;

use DateTime;
use App\Entity\Contact;
use App\Service\Mailer;
use App\Form\MessageType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessageController extends AbstractController
{
    /**
     * @Route("/admin/message", name="admin_message_list", methods="GET")
     */
    public function index(ContactRepository $contactRepository): Response
    {
        return $this->render('admin/message/index.html.twig', [
            'messages' => $contactRepository->findAllOrderByDate(),
        ]);
    }

    /**
     * @Route("/admin/message/{id<\d+>}", name="admin_message_show", methods="GET")
     */
    public function show(Contact $contact = null, EntityManagerInterface $entityManagerInterface): Response
    {
        if ($contact === null) {
            throw $this->createNotFoundException("Ooops ! -_-' Cette page n'existe pas !");
        }

        if ($contact->getIsRead() === false) {
            $contact->setIsRead(true);
            $entityManagerInterface->flush();
        }

        return $this->render('admin/message/show.html.twig', [
            'message' => $contact,
        ]);
    }

    /**
     * @Route("/admin/answer/{id<\d+>}",name="admin_message_answer", methods={"GET", "POST"})
     */
    public function answer(Contact $contact, Request $request, Mailer $mailer)
    {
        $form = $this->createForm(MessageType::class);

        $form->handleRequest($request);

        // dd($request->request->get('message')['subject']);

        if ($form->isSubmitted() && $form->isValid()) {
            $message = $request->get('message');

            $sendEmail = $mailer->sendEmail('admin@admin.com', $contact->getUser()->getEmail(), $message['subject'], $message['text']);

            if ($sendEmail === true) {
                $this->addFlash('success', 'Message envoyé');
                return $this->redirectToRoute('admin_message_list');
            }

            $this->addFlash('danger', 'Envoi échoué');
            return $this->redirectToRoute('admin_message_list');
        }

        return $this->render('admin/message/send.html.twig', [
            'form' => $form->createView(),
            'contact' => $contact
        ]);
    }

    /**
     * @Route("/admin/message/delete/{id<\d+>}",name="admin_message_delete", methods="DELETE")
     */
    public function delete(Contact $contact, EntityManagerInterface $entityManagerInterface, Request $request)
    {
        if ($contact === null) {
            throw $this->createNotFoundException("Ooops ! -_-' Cette page n'existe pas !");
        }

        $token = $request->request->get('token');

        if ($this->isCsrfTokenValid('delete-message', $token)) {
            $entityManagerInterface->remove($contact);
            $entityManagerInterface->flush();

            $this->addFlash('success', 'Message supprimé');
            return $this->redirectToRoute('admin_message_list');
        }

        $this->addFlash('danger', 'Erreur, veuillez contacter un administrateur.');
        $this->redirectToRoute('admin_message_list');
    }
}
