<?php

namespace App\Controller\Event;

use App\Entity\Event;
use App\Repository\Event\EventAdminRepository;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EventDetailController extends AbstractController
{
    public function __construct(
        private readonly EventAdminRepository $eventAdminRepository,
        private readonly NormalizerInterface $normalizerInterface,
        private readonly UserRepository $userRepository
    ) {
    }

    /**
     * @throws NonUniqueResultException
     * @throws ExceptionInterface
     * @throws \JsonException
     */
    public function __invoke(Event $event, Request $request): JsonResponse
    {
        $userId = $request->query->get('userId');
        $user = null;

        if ($userId) {
            $user = $this->userRepository->find($userId);
        }

        if ($user) {
            $eventAdmin = $this->eventAdminRepository->findByEventUser($event, $user);
            if ($eventAdmin !== null) {
                $event->setShared(true);
                $event->setAdminRoles($eventAdmin->getRoles());
                $event->setAdminId($eventAdmin->getId());
            }
        }
        $data = $this->normalizerInterface->normalize($event, null, ['groups' => ['event:pRead']]);

        return new JsonResponse($data, Response::HTTP_OK);
    }
}