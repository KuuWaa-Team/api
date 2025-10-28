<?php
namespace App\Message;

class GenerateEventThumbnail
{
    public function __construct(
        private readonly int $eventId,
        private readonly string $imagePath
    ) {}

    public function getEventId(): int { return $this->eventId; }
    public function getImagePath(): string { return $this->imagePath; }
}