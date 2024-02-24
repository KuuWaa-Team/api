<?php

namespace App\Controller\Event;

use App\Entity\Event;
use App\Repository\Address\AddressRepository;
use App\Repository\Address\CountryRepository;
use App\Repository\Address\ProvinceRepository;
use App\Repository\EventCategoryRepository;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class DeleteEventController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws \JsonException
     */
    public function __invoke(Event $event): JsonResponse
    {
        $message = 'Evénement supprimé avec succès !';
        $success = true;
        $errorType = null;
        if (!($event->getStatus() === 'paid')) {
            $this->em->remove($event);
            $this->em->flush();
        } else {
            $message = 'Evénement ne peut pas être supprimé !';
            $success = false;
        }

        $response = [
            'success'   => $success,
            'message'   => $message,
            'errorType' => $errorType,
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
