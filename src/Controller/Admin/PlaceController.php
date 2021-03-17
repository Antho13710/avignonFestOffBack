<?php

namespace App\Controller\Admin;

use App\Entity\Place;
use App\Form\PlaceType;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PlaceController extends AbstractController
{
    /**
     * Display all place
     * @Route("/admin/lieu", name="admin_place_list", methods="GET")
     */
    public function index(PlaceRepository $placeRepository): Response
    {
        return $this->render('admin/place/index.html.twig', ['places' => $placeRepository->findAll()]);
    }

    /**
     * Display One place
     * @Route("/admin/lieu/{id<\d+>}",name="admin_place_show", methods="GET")
     */
    public function show(Place $place)
    {
        return $this->render('admin/place/show.html.twig', ['place' => $place]);
    }

    /**
     * @Route("/admin/lieu/ajouter",name="admin_place_add", methods={"GET", "POST"})
     */
    public function add(Request $request, EntityManagerInterface $entityManagerInterface)
    {
        $newPlace = new Place();
        $form = $this->createForm(PlaceType::class, $newPlace);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->persist($newPlace);
            $entityManagerInterface->flush();

            $this->addFlash('success', 'Lieu enregistré avec succès');
            return $this->redirectToRoute('admin_place_list');
        }

        return $this->render('admin/place/add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/admin/lieu/modifier/{id}",name="admin_place_edit", methods={"GET", "POST"})
     */
    public function edit(Place $place = null, Request $request, EntityManagerInterface $entityManagerInterface)
    {
        if ($place === null) {
            throw $this->createNotFoundException('Lieu inexistant');
        }

        $form = $this->createForm(PlaceType::class, $place);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->flush();

            $this->addFlash('success', 'Lieu modifié avec succès');
            return $this->redirectToRoute('admin_place_list');
        }

        return $this->render('admin/place/add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/admin/lieu/delete/{id}",name="admin_place_delete", methods="DELETE")
     */
    public function delete(Place $place = null, EntityManagerInterface $entityManagerInterface, Request $request)
    {
        if ($place === null) {
            throw $this->createNotFoundException('Page non trouvée');
        }

        $token = $request->get('token');

        if ($this->isCsrfTokenValid('delete-place', $token)) {
            $entityManagerInterface->remove($place);
            $entityManagerInterface->flush();

            $this->addFlash('success', 'Lieu supprimé avec succès');
            return $this->redirectToRoute('admin_place_list');
        }

        $this->addFlash('danger', 'Erreur! Veuillez contacter un administrateur');
        return $this->redirectToRoute('admin_place_list');
    }
}
