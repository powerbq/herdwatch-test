<?php

namespace App\Controller;

use App\Entity\Group;
use App\Form\GroupType;
use App\Repository\GroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/group')]
class GroupController extends AbstractController
{
    #[Route('/', name: 'app_group_index', methods: ['GET'])]
    public function index(GroupRepository $groupRepository, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse($serializer->serialize($groupRepository->findAll(), 'json'), json: true);
    }

    #[Route('/', name: 'app_group_new', methods: ['POST'])]
    public function new(Request $request, GroupRepository $groupRepository, SerializerInterface $serializer): JsonResponse
    {
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->submit(array_merge($request->query->all(), $request->request->all()));

        if ($form->isSubmitted() && $form->isValid()) {
            $groupRepository->save($group, true);

            return new JsonResponse($serializer->serialize($group, 'json'), json: true);
        }

        return new JsonResponse(status: 400);
    }

    #[Route('/{id}', name: 'app_group_show', methods: ['GET'])]
    public function show(Group $group, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse($serializer->serialize($group, 'json'), json: true);
    }

    #[Route('/{id}', name: 'app_group_edit', methods: ['PATCH'])]
    public function edit(Request $request, Group $group, GroupRepository $groupRepository): Response
    {
        $form = $this->createForm(GroupType::class, $group);
        $form->submit(array_merge($request->query->all(), $request->request->all()));

        if ($form->isSubmitted() && $form->isValid()) {
            $groupRepository->save($group, true);

            return new JsonResponse();
        }

        return new JsonResponse(status: 400);
    }

    #[Route('/{id}', name: 'app_group_delete', methods: ['DELETE'])]
    public function delete(Request $request, Group $group, GroupRepository $groupRepository): Response
    {
        // if ($this->isCsrfTokenValid('delete' . $group->getId(), $request->request->get('_token'))) {
        $groupRepository->remove($group, true);
        // }

        return new JsonResponse();
    }
}
