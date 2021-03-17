<?php

namespace App\Controller\Api;

use App\Entity\Date;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DateController extends AbstractController
{
    /**
     * @Route("/api/handle_date/{id<\d+>}", name="api_date_delete", methods="DELETE")
     */
    public function delete(Date $date = null, EntityManagerInterface $manager): Response
    {
        if (!$date) {
            return $this->json(['message' => 'Date non trouvée.'], Response::HTTP_NOT_FOUND);
        }

        $this->denyAccessUnlessGranted('delete', $date);

        $manager->remove($date);
        $manager->flush();

        return $this->json(['message' => 'Date supprimée.'], Response::HTTP_OK);
    }
}
