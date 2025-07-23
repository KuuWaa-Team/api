<?php

namespace App\Controller\Event;

use App\Entity\Event;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ManipulatorInterface;
use Imagine\Image\Point;

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
    public function __invoke(Request $request, Event $event, KernelInterface $kernel): JsonResponse
    {
        $message = ' succès !';
        $success = true;
        $errorType = null;

        $image = $request->files->get('image');
        $fileName = uniqid('', true) . '.' . $image->guessExtension();
        $destinationPath = $kernel->getProjectDir() . '/public/events/images';
        $image->move($destinationPath, $fileName);
        $imagePath = '/events/images/'.$fileName;

        $thumbnailPath = '/events/thumb/'.$fileName;
        $thumbnailAbsolutePath = $kernel->getProjectDir() . '/public/events/thumb/' . $fileName;
        $imagine = new Imagine();
        $image = $imagine->open($destinationPath . '/' . $fileName);
        $thumbnail = $image->thumbnail(new Box(375, 375), ManipulatorInterface::THUMBNAIL_OUTBOUND);
        $thumbnail->resize(new Box(375, 375))
            ->crop(new Point(0, 0), new Box(375, 375))
            ->save($thumbnailAbsolutePath);
        $event
            ->setImagePath($imagePath)
            ->setThumbPath($thumbnailPath);

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