<?php

namespace App\Controller\Event;

use App\Entity\Event;
use App\Entity\EventCategory;
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
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EditEventImageController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly NormalizerInterface $normalizer
    ) {
    }

    /**
     * @throws \JsonException
     * @throws ExceptionInterface
     */
    public function __invoke(Request $request, Event $event, UserInterface $user, KernelInterface $kernel): JsonResponse
    {
        $message = ' succès !';
        $success = true;
        $errorType = null;

        $image = $request->files->get('image');
        $fileName = uniqid('', true) . '.' . $image->guessExtension();
        $destinationPath = $kernel->getProjectDir() . '/public/events/images';
        $image->move($destinationPath, $fileName);
        $imagePath = '/events/images/'.$fileName;

        $event->setImagePath($imagePath);

        try {
            $this->eventRepository->save($event, true);
        } catch (\Throwable $th) {
            $success = false;
            $message = 'Erreur : Image non modifiée !';
        }

        $return = [
            'success'   => $success,
            'message'   => $message,
            'event' => $event,
            'errorType' => $errorType,
        ];

        $response = $this->normalizer->normalize($return, null, ['groups' => 'event:fRead']);

        return new JsonResponse($response, Response::HTTP_OK);
    }

}