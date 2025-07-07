<?php

namespace App\Controller\Event;

use App\Entity\Event;
use App\Repository\Event\EventAdminRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ListingAdminsController extends AbstractController
{
    public function __construct(
        private readonly EventAdminRepository $adminRepository,
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(Request $request, Event $event): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    if (empty($data['name'])) {
        return new JsonResponse(['success' => false, 'message' => 'Nom requis'], Response::HTTP_BAD_REQUEST);
    }

    $roles = $data['permissions'] ?? [];

    if (!in_array('verifier_invitations', $roles)) {
        $roles[] = 'verifier_invitations';
    }

    $rolesString = implode(',', array_unique($roles));

    $admin = new EventAdmin();
    $admin->setName($data['name']);
    $admin->setEvent($event);
    $admin->setRoles($rolesString);

    $this->entityManager->persist($admin);
    $this->entityManager->flush();

    return new JsonResponse(['success' => true, 'message' => 'Admin ajouté'], Response::HTTP_CREATED);
}
}