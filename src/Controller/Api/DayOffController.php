<?php

namespace App\Controller\Api;

use App\Entity\DayOff;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DayOffController extends AbstractController
{
    /**
     * @Route("/api/handle_day_off/{id<\d+>}", name="api_day_off_delete", methods="DELETE")
     */
    public function delete(DayOff $dayOff = null, EntityManagerInterface $manager): Response
    {
        if (!$dayOff) {
            return $this->json(['message' => 'Relâche non trouvée.'], Response::HTTP_NOT_FOUND);
        }

        $this->denyAccessUnlessGranted('delete', $dayOff);

        $manager->remove($dayOff);
        $manager->flush();

        return $this->json(['message' => 'Relâche supprimée.'], Response::HTTP_OK);
    }
}
