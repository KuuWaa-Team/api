<?php
namespace App\MessageHandler;

use App\Message\GenerateEventThumbnail;
use App\Repository\EventRepository;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ManipulatorInterface;
use Imagine\Image\Point;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GenerateEventThumbnailHandler
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly string $projectDir
    ) {}

    public function __invoke(GenerateEventThumbnail $message): void
    {
        $event = $this->eventRepository->find($message->getEventId());
        if (!$event) return;

        $imagine = new Imagine();
        $sourcePath = $this->projectDir . '/public' . $message->getImagePath();
        $fileName = basename($message->getImagePath());
        $thumbnailPath = $this->projectDir . '/public/events/thumb/' . $fileName;

        $image = $imagine->open($sourcePath);
        $thumbnail = $image->thumbnail(
            new Box(375, 375), 
            ManipulatorInterface::THUMBNAIL_OUTBOUND
        );
        $thumbnail->resize(new Box(375, 375))
            ->crop(new Point(0, 0), new Box(375, 375))
            ->save($thumbnailPath);

        $event->setThumbPath('/events/thumb/' . $fileName);
        $this->eventRepository->save($event, true);
    }
}