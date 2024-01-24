<?php

namespace App\Controller\Event;

use App\Entity\Event;
use App\Entity\EventCategory;
use App\Entity\User;
use App\Repository\EventCategoryRepository;
use App\Repository\EventPackRepository;
use App\Repository\EventRepository;
use App\Repository\FeatureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class EditEventController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly EventCategoryRepository $categoryRepository,
    ) {
    }

    /**
     * @throws \JsonException
     */
    public function __invoke(Event $event, Request $request, UserInterface $user, KernelInterface $kernel): JsonResponse
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $message = 'Evénement modifié avec succès !';
        $success = true;
        $errorType = null;

        $name = $content['name'] ?? null;
        $date = $content['date'] ?? null;
        $address = $content['address'] ?? null;
        $details = $content['details'] ?? null;

        $category_id = $content['category_id'] ?? null;

         if ($category_id === null || $category_id === 0) {
             $category = (new EventCategory())
                 ->setName($content['category_name'] ?? 'Non défini')
                 ->setValidated(false)
             ;
         } else {
             $category = $this->categoryRepository->find($category_id);
         }

        if ($name !== null) {
            $event->setName($name);
        }

        if ($date !== null) {
            $event->setDate(new \DateTime($date));
        }

        if ($details !== null) {
            $event->setDetails($details);
        }

        if ($address !== null) {
            $event->setAddress($address);
        }

        $event->setCategory($category);

        try {
            $this->eventRepository->save($event, true);
        } catch (\Throwable $th) {
            $success = false;
            $message = 'Erreur : événement non modifié !';
        }

        $response = [
            'success'   => $success,
            'message'   => $message,
            'errorType' => $errorType,
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
