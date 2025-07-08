<?php

namespace App\Controller\Beverage;

use App\Entity\Beverage;
use App\Entity\Invitation;
use App\Entity\Table;
use App\Repository\BeverageRepository;
use App\Repository\EventRepository;
use App\Repository\InvitationRepository;
use App\Repository\TableRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RemoveBeverageController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly BeverageRepository $beverageRepository,
        private readonly NormalizerInterface $normalizer
    )
    {
    }

    /**
     * @throws \JsonException
     */
    public function __invoke(Beverage $beverage): JsonResponse
    {
        try {
            $this->em->remove($beverage);
            $this->em->flush();
            $message = "";
        } catch (Exception $e) {
            $message = "Cette boisson ne peut plus etre supprimee";
        }
        
        $event = $beverage->getEvent();
        $eventBeverageCount = $event->getBeveragesCount() ?? 0;
        if ($eventBeverageCount > 0) {
            $event->setBeveragesCount($eventBeverageCount - 1);
        }
        $beverages = $this->beverageRepository->findByEvent($event);
        $response = [
            'success'   => true,
            'beverages' => $this->normalizer->normalize($beverages, null, ['groups' => ['beverage:pRead']]),
            'message'   => $message,
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}