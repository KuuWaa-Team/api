<?php

namespace App\Controller\Event;

use App\Entity\Event;
use App\Entity\EventCategory;
use App\Entity\User;
use App\Repository\BeverageRepository;
use App\Repository\EventRepository;
use App\Repository\TableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ListingEventBeveragesController extends AbstractController
{
    public function __construct(
        private readonly BeverageRepository $beverageRepository,
        private readonly NormalizerInterface $normalizer,
    ) {}

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(Event $event, UserInterface $user): JsonResponse
    {
        $beverages = $this->beverageRepository->findByEvent($event);
        $response = [
            'success'   => true,
            'data' => $this->normalizer->normalize($beverages, null, ['groups' => ['beverage:pRead']])
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
