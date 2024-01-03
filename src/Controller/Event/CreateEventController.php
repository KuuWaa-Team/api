<?php

namespace App\Controller\Event;

use App\Entity\Event;
use App\Entity\User;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreateEventController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository
    )
    {
    }

    public function __invoke(Request $request, User $user): JsonResponse
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $message = null;
        $errorType = null;

        $name = $content['name'];
        $date = $content['date'];
        $startHour = $content['startHour'];
        $endHour = $content['endHour'];
        $details = $content['details'];
        $address = $content['address'];
        $category = $content['categoy'];

        $event = (new Event())
            ->setName($name)
            ->setDate($date)
            ->setAddress($address)
            ->setStartHour($startHour)
            ->setEndHour($endHour)
            ->setDetails($details)
            ->setCategory($category)
            ->setUser($user)
        ;

        $this->eventRepository->save($event, true);

        $response = [
            'success'   => true,
            'message'   => $message,
            'errorType' => $errorType,
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}