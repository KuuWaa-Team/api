<?php
namespace App\Controller\Event;

use App\Service\EventCreationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreateEventController extends AbstractController
{
    public function __construct(
        private readonly EventCreationService $eventCreationService
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $content = $request->request->all();
            $image = $request->files->get('image');
            $templateImage = $request->files->get('template_image');

            $event = $this->eventCreationService->createEvent(
                $content, 
                $image, 
                $templateImage
            );

            return new JsonResponse([
                'success' => true,
                'eventId' => $event->getId(),
                'message' => 'Événement créé avec succès !',
                'errorType' => null,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'eventId' => null,
                'message' => $e->getMessage(),
                'errorType' => 'error',
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}