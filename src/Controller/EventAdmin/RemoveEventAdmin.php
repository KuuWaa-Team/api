<?php

namespace App\Controller\EventAdmin;

use App\Entity\Event\EventAdmin;
use App\Repository\Event\EventAdminRepository;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use App\Service\PhoneService;
use App\Service\SmsService;
use App\Service\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RemoveEventAdmin extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ){}

    /**
     * @throws \JsonException
     */
    public function __invoke(EventAdmin $eventAdmin, Request $request): JsonResponse
    {
        $this->em->remove($eventAdmin);
        $this->em->flush();

        $response = [
            'success'   => true,
            'message'   => "Gestionnaire supprime avec succes",
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}