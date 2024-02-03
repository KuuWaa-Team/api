<?php

namespace App\Controller\Table;

use App\Entity\Invitation;
use App\Entity\Table;
use App\Repository\EventRepository;
use App\Repository\InvitationRepository;
use App\Repository\TableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AddTableController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly TableRepository $tableRepository,
        private readonly NormalizerInterface $normalizer
    )
    {
    }

    /**
     * @throws \JsonException
     */
    public function __invoke(Request $request)
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $name = $content["name"] ?? null;
        $count = $content["count"] ?? null;
        $eventId = $content["eventId"] ?? null;

        $event = $this->eventRepository->find($eventId);

        $table = (new Table())
            ->setName($name)
            ->setEvent($event)
            ->setCount($count)
            ->setCountTaken(0)
        ;

        $this->tableRepository->save($table, true);

        $eventTableCount = $event->getTablesCount() ?? 0;

        $event->setTablesCount($eventTableCount + 1);
        $this->eventRepository->save($event, true);

        $tables = $this->tableRepository->findByEvent($event);
        $response = [
            'success'   => true,
            'tables' => $this->normalizer->normalize($tables, null, ['groups' => ['table:pRead']]),
            'event' => $this->normalizer->normalize($event, null, ['groups' => ['event:fRead']]),
            'message'   => "Table enrégistrée avec succès",
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}