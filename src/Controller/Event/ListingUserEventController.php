<?php

namespace App\Controller\Event;

use App\Entity\Event;
use App\Entity\EventCategory;
use App\Entity\User;
use App\Repository\EventRepository;
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
     */
    public function __invoke(UserInterface $user): JsonResponse
    {
        $data = [];
        $success = true;

        if ($user instanceof User) {
            $data = $user->getEvents();
        }

        $response = [
            'success' => $success,
            'data'    => $this->normalizerInterface->normalize($data, null, ['groups' => ['event:pRead']])
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
