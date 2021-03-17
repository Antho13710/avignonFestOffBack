<?php

namespace App\Controller\Api;

use App\Entity\Event;
use App\Service\Validator;
use App\Repository\DateRepository;
use App\Service\UploaderBase64;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HandleEventController extends AbstractController
{
    /**
     * @Route("/api/handle_event/new", name="api_handle_event_new", methods="POST")
     */
    public function new(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer, Validator $validator, UploaderBase64 $uploaderBase64): Response
    {
        $data = $request->getContent();
        // Convert request json in array assoc to catch image parameter
        $arrayData = json_decode($data, true);

        if (!str_contains($arrayData['image'], 'data:image/') && $arrayData['image'] !== '') {
            return $this->json(['Message' => 'Mauvais format d\'image'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($arrayData['image'] !== '') {
            $uploadedImageName = $uploaderBase64->upload($arrayData['image'], "upload/images/");
        }

        // Decode json data to poppulate in database
        $event = $serializer->deserialize($data, Event::class, 'json');
        if (isset($uploadedImageName)) {
            $event->setImage($uploadedImageName);
        } else {
            $event->setImage('default.png');
        }


        //Handling errors
        if ($validator->validator($event)) {
            return $validator->validator($event);
        }

        $manager->persist($event);
        $manager->flush();

        return $this->json(['Message' => 'Évènement créé avec succès.'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/handle_event/{id<\d+>}", name="api_handle_event_put", methods="PUT")
     */
    public function edit(Event $event = null, UploaderBase64 $uploaderBase64, DateRepository $dateRepository, Request $request, EntityManagerInterface $manager, SerializerInterface $serializer, Validator $validator): Response
    {
        if ($event === null) {
            return $this->json(['Message' => 'Cet évènement n\'existe pas.'], Response::HTTP_NOT_FOUND);
        }

        $this->denyAccessUnlessGranted('edit', $event);

        $data = $request->getContent();

        $arrayData = json_decode($data, true);

        if (str_contains($arrayData['image'], 'data:image/') === true) {
            $newUploadedImageName = $uploaderBase64->upload($arrayData['image'], "upload/images/");
            if ($event->getImage() != 'default.png') {
                unlink("upload/images/" . $event->getImage());
            }
        }

        $currentImage = $event->getImage();

        // Decode json data to poppulate in database
        $editEvent = $serializer->deserialize($data, Event::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $event]);
        if (isset($newUploadedImageName)) {
            $editEvent->setImage($newUploadedImageName);
        } else {
            $editEvent->setImage($currentImage);
        }

        //Handling errors
        if ($validator->validator($editEvent)) {
            return $validator->validator($editEvent);
        }

        $manager->flush();
        $dateRepository->deleteNull();

        return $this->json(['Message' => 'L\'événement a bien été modifié'], Response::HTTP_OK);
    }

    // TODO Vérification CSRF Token après mise en place avec le front

    /**
     * @Route("/api/handle_event/{id<\d+>}", name="api_handle_event_delete", methods="DELETE")
     */
    public function delete(Event $event = null, EntityManagerInterface $entityManagerInterface)
    {
        if ($event === null) {
            return $this->json(['Message' => 'Cet évènement n\'existe pas.'], Response::HTTP_NOT_FOUND);
        }

        $this->denyAccessUnlessGranted('delete', $event);

        if ($event->getImage() != 'default.png') {
            unlink("upload/images/" . $event->getImage());
        }
        $entityManagerInterface->remove($event);
        $entityManagerInterface->flush();

        return $this->json(['Message' => 'L\'événement a bien été supprimé.'], Response::HTTP_OK);
    }
}
