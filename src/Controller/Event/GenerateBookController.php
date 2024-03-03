<?php

namespace App\Controller\Event;

use App\Entity\Event;
use App\Repository\Event\BookRepository;
use App\Repository\EventRepository;
use App\Service\ExportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GenerateBookController extends AbstractController
{
    public function __construct(
        private readonly BookRepository $bookRepository,
        private readonly ExportService $exportService
    ) {}

    public function __invoke(Event $event)
    {
        $name = 'book' . uniqid('', true) . '.pdf';
        $books = $this->bookRepository->findByEvent($event);
        $this->exportService->export($name, $event, $books);
    }
}