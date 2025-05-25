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
        private readonly NormalizerInterface $normalizerInterface,
    ) {
    }

    /**
     * @throws ExceptionInterface
     * @throws NonUniqueResultException
     */
    public function __invoke(User $user): JsonResponse
    {
        $data = $this->eventRepository->findListableEvents($user);

        $response = [
            'success' => true,
            'data'    => $this->normalizerInterface->normalize($data, null, ['groups' => ['event:pRead']])
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
