<?php

namespace App\Controller\Api;

use App\Entity\Alert;
use App\Repository\AlertRepository;
use App\Service\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AlertController extends AbstractController
{
    /**
     * Endpoint to create an alert
     * 
     * @Route("/api/alert/add", name="api_alert_create", methods="POST")
     */
    public function index(SerializerInterface $serializerInterface, EntityManagerInterface $entityManagerInterface, Request $request, Validator $validator): Response
    {
        $data = $request->getContent();

        $alert = $serializerInterface->deserialize($data, Alert::class, 'json');

        if ($validator->validator($alert)) {
            return $validator->validator($alert);
        }

        $entityManagerInterface->persist($alert);
        $entityManagerInterface->flush();

        return $this->json(['Message' => 'Signalement transmis avec succès.'], Response::HTTP_CREATED);
    }
}
