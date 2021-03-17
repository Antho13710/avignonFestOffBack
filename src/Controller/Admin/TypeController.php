<?php

namespace App\Controller\Admin;

use App\Entity\Type;
use App\Form\TypesType;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TypeController extends AbstractController
{
    /**
     * @Route("/admin/genres", name="admin_type_list", methods="GET")
     */
    public function index(TypeRepository $typeRepository): Response
    {
        return $this->render('admin/type/index.html.twig', [
            'types' => $typeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/genre/ajout",name="admin_type_add", methods={"GET", "POST"})
     */
    public function add(Request $request, EntityManagerInterface $entityManagerInterface)
    {
        $newType = new Type();
        $form = $this->createForm(TypesType::class, $newType);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->persist($newType);
            $entityManagerInterface->flush();

            $this->addFlash('success', 'Genre enregistré avec succès');
            return $this->redirectToRoute('admin_type_list');
        }

        return $this->render('admin/type/add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/admin/genre/modifier/{id}",name="admin_type_edit", methods={"GET", "POST"})
     */
    public function edit(Type $type = null, Request $request, EntityManagerInterface $entityManagerInterface)
    {
        if ($type === null) {
            throw $this->createNotFoundException('Genre inexistant');
        }

        $form = $this->createForm(TypesType::class, $type);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->flush();

            $this->addFlash('success', 'Genre modifié avec succès');
            return $this->redirectToRoute('admin_type_list');
        }

        return $this->render('admin/type/add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/admin/genre/delete/{id}",name="admin_type_delete", methods="DELETE")
     */
    public function delete(Type $type = null, EntityManagerInterface $entityManagerInterface, Request $request)
    {
        if ($type === null) {
            throw $this->createNotFoundException('Page non trouvée');
        }

        $token = $request->get('token');

        if ($this->isCsrfTokenValid('delete-type', $token)) {
            $entityManagerInterface->remove($type);
            $entityManagerInterface->flush();

            $this->addFlash('success', 'Genre supprimé avec succès');
            return $this->redirectToRoute('admin_type_list');
        }

        $this->addFlash('danger', 'Errur! Veuillez contacter un administrateur');
        return $this->redirectToRoute('admin_type_list');
    }
}
