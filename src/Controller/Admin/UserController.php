<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/admin/user", name="admin_user_list", methods="GET")
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/user/delete/{id<\d+>}",name="admin_user_delete", methods="DELETE")
     */
    public function delete(EntityManagerInterface $entityManagerInterface, User $user = null, Request $request)
    {
        if ($user === null) {
            throw $this->createNotFoundException('Oups ! Cet utilisateur n\'existe pas -_-\'');
        }

        $token = $request->get('token');

        if ($this->isCsrfTokenValid('delete-user', $token)) {
            $entityManagerInterface->remove($user);
            $entityManagerInterface->flush();

            $this->addFlash('success', 'Utilisateur supprimé avec succès');
            return $this->redirectToRoute('admin_user_list');
        }

        $this->addFlash('danger', 'Errur! Veuillez contacter un administrateur');
        return $this->redirectToRoute('admin_user_list');
    }
}
