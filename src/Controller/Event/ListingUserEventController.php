<?php

namespace App\Controller\Event;

use App\Entity\Event;
use App\Entity\EventCategory;
use App\Entity\User;
use App\Repository\Event\EventAdminRepository;
use App\Repository\EventRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ListingUserEventController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly EventAdminRepository $eventAdminRepository,
        private readonly NormalizerInterface $normalizerInterface,
    ) {
    }

    /**
     * @throws ExceptionInterface
     * @throws NonUniqueResultException
     */
    public function __invoke(UserInterface $user): JsonResponse
    {
        $data = [];
        $success = true;

        if ($user instanceof User) {
//            $data = $user->getEvents();
            $data = $this->eventRepository->findListableEvents($user);
            foreach ($data as $event) {
                if ($event->getUser() !== $user) {
                    $eventAdmin = $this->eventAdminRepository->findByEventAndphoneNumber($event, $user->getPhoneNumber());
                    if ($eventAdmin !== null && $eventAdmin->getStatus() !== 'refused') {
                        $event->setShared(true);
                        $event->setAdminStatus($eventAdmin->getStatus());
                        $event->setAdminId($eventAdmin->getId());
                    }
                }
            }
        }

        $response = [
            'success' => $success,
            'data'    => $this->normalizerInterface->normalize($data, null, ['groups' => ['event:pRead']])
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
