<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Service\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserController extends AbstractController
{
    /**
     * @Route("/api/handle_user/{id<\d+>}", name="api_user_show", methods="GET")
     */
    public function show(User $user): Response
    {
        if (!$user) {
            return $this->json(['Message' => 'Cet utilisateur n\'existe pas.'], Response::HTTP_NOT_FOUND);
        }
        $this->denyAccessUnlessGranted('show', $user);

        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'user_show']);
    }

    /**
     * @Route("/api/user/new", name="api_user_new", methods="POST")
     */
    public function new(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer, Validator $validator, UserPasswordEncoderInterface $encoder): Response
    {
        $data = $request->getContent();

        // Decode json data to poppulate in database
        $user = $serializer->deserialize($data, User::class, 'json');

        //Handling errors
        if ($validator->validator($user)) {
            return $validator->validator($user);
        }

        $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
        $user->setRoles(["ROLE_ARTIST"]);

        $manager->persist($user);
        $manager->flush();

        return $this->json(['Message' => 'Compte créé'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/handle_user/{id<\d+>}", name="api_user_edit", methods="PUT")
     */
    public function edit(User $user = null, Request $request, EntityManagerInterface $manager, SerializerInterface $serializer, Validator $validator, UserPasswordEncoderInterface $encoder): Response
    {
        if (!$user) {
            return $this->json(['Message' => 'Cet utilisateur n\'existe pas.'], Response::HTTP_NOT_FOUND);
        }

        $this->denyAccessUnlessGranted('edit', $user);

        $data = $request->getContent();

        $editUser = $serializer->deserialize($data, User::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $user]);

        if ($validator->validator($editUser)) {
            return $validator->validator($editUser);
        }

        $manager->flush();

        return $this->json(['Message' => 'L\'utilisateur a bien été modifié'], Response::HTTP_OK);
    }

    /**
     * @Route("/api/handle_user/{id}", name="api_handle_user_delete", methods="DELETE")
     */
    public function delete(User $user = null, EntityManagerInterface $entityManagerInterface)
    {
        if ($user === null) {
            return $this->json(['Message' => 'Cet utilisateur n\'existe pas.'], Response::HTTP_NOT_FOUND);
        }

        $this->denyAccessUnlessGranted('delete', $user);

        $entityManagerInterface->remove($user);
        $entityManagerInterface->flush();

        return $this->json(['Message' => 'L\'utilisateur a bien été supprimé.'], Response::HTTP_OK);
    }

    /**
     * @Route("/api/handle_user/edit_password/{id<\d+>}", name="api_user_edit_password", methods="PUT")
     */
    public function editPassword(User $user, Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
    {
        if (!$user) {
            return $this->json(['Message' => 'Cet utilisateur n\'existe pas.'], Response::HTTP_NOT_FOUND);
        }

        $this->denyAccessUnlessGranted('edit', $user);

        $data = json_decode($request->getContent(), true);

        if (password_verify($data['password'], $user->getPassword()) && preg_match('/^(?=.*[A-z])(?=.*[A-Z])(?=.*[0-9])\S{8,}$/', $data['newPassword'])) {

            $user->setPassword($encoder->encodePassword($user, $data['newPassword']));
            $manager->flush();

            return $this->json(['Message' => 'Le mot de passe a bien été modifié.'], Response::HTTP_OK);
        }

        return $this->json(['Message' => 'Mot de passe incorrect.'], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
