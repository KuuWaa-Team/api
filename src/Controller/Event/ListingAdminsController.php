<?php

namespace App\Controller\Event;

use App\Entity\Event;
use App\Repository\Event\EventAdminRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function __invoke(Event $event): JsonResponse
    {
        $admins = $this->adminRepository->findByEvent($event);
        $response = [
            'success'   => true,
            'data' => $this->normalizer->normalize($admins, null, ['groups' => ['admin:pRead']])
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}