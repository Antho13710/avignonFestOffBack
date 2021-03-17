<?php

namespace App\Controller\Api;

use App\Entity\Contact;
use App\Service\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * @Route("/api/handle_contact/new", name="api_handle_contact_new", methods="POST")
     */
    public function new(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer, Validator $validator): Response
    {
        $data = $request->getContent();

        // Decode json data to poppulate in database
        $contact = $serializer->deserialize($data, Contact::class, 'json');

        //Handling errors
        if ($validator->validator($contact)) {
            return $validator->validator($contact);
        }

        $manager->persist($contact);
        $manager->flush();

        return $this->json(['Message' => 'Message envoyé avec succès.'], Response::HTTP_CREATED);
    }
}
