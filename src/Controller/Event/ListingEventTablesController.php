<?php

namespace App\Controller\Event;

use App\Entity\Event;
use App\Entity\EventCategory;
use App\Entity\User;
use App\Repository\EventRepository;
use App\Repository\TableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ListingEventTablesController extends AbstractController
{
    public function __construct(
        private readonly TableRepository $tableRepository,
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(Event $event, UserInterface $user): JsonResponse
    {
        $tables = $this->tableRepository->findByEvent($event);
        $response = [
            'success'   => true,
            'data' => $this->normalizer->normalize($tables, null, ['groups' => ['table:pRead']])
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
