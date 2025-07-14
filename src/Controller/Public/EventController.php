<?php

namespace App\Controller\Public;

use App\Entity\Event;
use App\Entity\Event\Photo;
use App\Repository\Event\PhotoRepository;
use App\Service\MediaUploaderService;
use NumberFormatter;
use App\Entity\Event\Book;
use App\Entity\Invitation;
use App\Form\Invitation\BeverageFormType;
use App\Form\Invitation\BookMessageFormType;
use App\Repository\BeverageRepository;
use App\Repository\Event\BookRepository;
use App\Repository\InvitationRepository;
use App\Service\ApiService;
use App\Service\DateService;
use App\Service\NotificationService;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class EventController extends AbstractController
{
    public function __construct(
        private readonly ApiService $apiService,
        private readonly NotificationService $notificationService,
    ){}

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/event/{id}/public-book', name: 'app_public_event_public_book')] 
    public function publicBook (Event $event): Response
    {
        $apiUrl = $this->apiService->getApiUrl();
        return $this->render('public/event/public_book.html.twig', [
            'event' => $event,
            'apiUrl' => $apiUrl,
        ]);
    }
}
