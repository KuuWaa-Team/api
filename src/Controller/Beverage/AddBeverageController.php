<?php

namespace App\Controller\Beverage;

use App\Entity\Beverage;
use App\Entity\Invitation;
use App\Entity\Table;
use App\Repository\BeverageRepository;
use App\Repository\EventRepository;
use App\Repository\InvitationRepository;
use App\Repository\TableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AddBeverageController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly BeverageRepository $beverageRepository,
        private readonly NormalizerInterface $normalizer
    )
    {
    }

    /**
     * @throws \JsonException
     */
    public function __invoke(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $name = $content["name"] ?? null;
        $type = $content["type"] ?? null;
        $eventId = $content["eventId"] ?? null;

        $event = $this->eventRepository->find($eventId);

        $beverage = (new Beverage())
            ->setName($name)
            ->setType($type)
            ->setEvent($event)
            ->setCountTaken(0)
        ;

        $this->beverageRepository->save($beverage, true);

        $eventBeverageCount = $event->getBeveragesCount() ?? 0;

        $event->setBeveragesCount($eventBeverageCount + 1);
        $this->eventRepository->save($event, true);

        $beverages = $this->beverageRepository->findByEvent($event);
        $response = [
            'success'   => true,
            'beverages' => $this->normalizer->normalize($beverages, null, ['groups' => ['beverage:pRead']]),
            'message'   => "Boisson enrégistrée avec succès",
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}