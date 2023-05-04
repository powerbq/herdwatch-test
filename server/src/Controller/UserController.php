<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse($serializer->serialize($userRepository->findAll(), 'json'), json: true);
    }

    #[Route('/', name: 'app_user_new', methods: ['POST'])]
    public function new(Request $request, UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->submit(array_merge($request->query->all(), $request->request->all()));

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return new JsonResponse($serializer->serialize($user, 'json'), json: true);
        }

        return new JsonResponse(status: 400);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse($serializer->serialize($user, 'json'), json: true);
    }

    #[Route('/{id}', name: 'app_user_edit', methods: ['PATCH'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): JsonResponse
    {
        $form = $this->createForm(UserType::class, $user);
        $form->submit(array_merge($request->query->all(), $request->request->all()));

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return new JsonResponse();
        }

        return new JsonResponse(status: 400);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['DELETE'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): JsonResponse
    {
        // if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
        $userRepository->remove($user, true);
        // }

        return new JsonResponse();
    }
}
