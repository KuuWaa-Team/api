<?php

namespace App\Controller\Public;
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

class InvitationController extends AbstractController
{
    public function __construct(
        private readonly ApiService $apiService,
        private readonly NotificationService $notificationService,
    ){}

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/invitations/{slug}', name: 'app_public_invitation')]
    public function index($slug, Request $request, BeverageRepository $beverageRepository, BookRepository $bookRepository, InvitationRepository $invitationRepository, DateService $dateService, KernelInterface $kernel): Response
    {
        $invitation = $invitationRepository->findBySlug($slug);
        if ($invitation === null) {
            return $this->redirect('https://kuuwaa.com/invitation-introuvable');
        }
        $date = $dateService->getFullDate($invitation->getEvent()->getStartDate(), true, true);
        $year = $invitation->getEvent()->getStartDate()->format("Y");
        $hour = $invitation->getEvent()->getStartDate()->format("H");
        $minute = $invitation->getEvent()->getStartDate()->format("i");

        $eventImage = $dateService->imageToBase64($kernel->getProjectDir() . $invitation->getEvent()->getImagePath());
        $codeImage = $dateService->imageToBase64($kernel->getProjectDir() . $invitation->getCodePath());

        $apiUrl = $this->apiService->getApiUrl();

        $form = $this->createForm(BookMessageFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $book = (new Book())
                ->setEvent($invitation->getEvent())
                ->setInvitation($invitation)
                ->setMessage($data->getMessage())
            ;

            $bookRepository->save($book, true);

//            $invitation->setBookSent(true);
            $invitationRepository->save($invitation, true);
            $this->addFlash('notice', 'Message envoyé avec succès');
        }

        list($primaryR, $primaryG, $primaryB) = $this->hexToRgb($invitation->getEvent()->getTemplate()->getPrimaryColor());
        list($secondaryR, $secondaryG, $secondaryB) = $this->hexToRgb($invitation->getEvent()->getTemplate()->getSecondaryColor());

        $formatter = new \NumberFormatter('fr_FR', \NumberFormatter::SPELLOUT);

        foreach ($invitation->getEvent()->getBeverages() as $beverage) {
            $othersCount = max(0, $beverage->getCountTaken() - 1);
            $beverage->setCountTakenText(ucfirst($formatter->format($othersCount)));
        }

        return $this->render('public/invitation/index.html.twig', [
            'invitation' => $invitation,
            'template' => $invitation->getEvent()->getTemplate(),
            'date' => $date,
            'apiUrl' => $apiUrl,
            'eventImage' => $eventImage,
            'codeImage' => $codeImage,
            'form' => $form->createView(),
            'year' => $year,
            'hour' => $hour,
            'minute' => $minute,

            // colors
            'primaryR' => $primaryR,
            'primaryG' => $primaryG,
            'primaryB' => $primaryB,
            'secondaryR' => $secondaryR,
            'secondaryG' => $secondaryG,
            'secondaryB' => $secondaryB,

            'primaryHex' => str_replace('#', '', $invitation->getEvent()->getTemplate()->getPrimaryColor()),
            'secondaryHex' => str_replace('#', '', $invitation->getEvent()->getTemplate()->getSecondaryColor()),
        ]);
    }

    #[Route('/invitations/{slug}/confirm-beverage', name: 'app_invitation_confirm_beverage', methods: ['POST'])]
    public function confirmBeverage(
        string $slug,
        Request $request,
        InvitationRepository $invitationRepository,
        BeverageRepository $beverageRepository,
    ): Response {
        $invitation = $invitationRepository->findOneBy(['slug' => $slug]);

        if (!$invitation) {
            throw $this->createNotFoundException('Invitation non trouvée.');
        }

        $beverageIds = $request->request->all('beverages');

        foreach ($invitation->getBeverages() as $beverage) {
            $invitation->removeBeverage($beverage);
            $beverage->setCountTaken($beverage->getCountTaken() - 1);
            $beverageRepository->save($beverage, true);
        }

        foreach ($beverageIds as $id) {
            $beverage = $beverageRepository->find($id);
            if ($beverage) {
                $invitation->addBeverage($beverage);
            }
        }

        $invitationRepository->save($invitation);
        return $this->redirect($request->headers->get('referer') ?? '/');
    }

    #[Route('/api/invitations/{slug}/toggle-beverage', name: 'app_invitation_toggle_beverage', methods: ['POST'])]
    public function toggleBeverage(
        string $slug,
        Request $request,
        InvitationRepository $invitationRepository,
        BeverageRepository $beverageRepository
    ): JsonResponse {
        $invitation = $invitationRepository->findOneBy(['slug' => $slug]);

        if (!$invitation) {
            return new JsonResponse(['success' => false, 'message' => 'Invitation non trouvée.'], 404);
        }

        $beverageId = $request->request->get('beverageId');
        $action = $request->request->get('action');

        $beverage = $beverageRepository->find($beverageId);

        if (!$beverage) {
            return new JsonResponse(['success' => false, 'message' => 'Boisson non trouvée.'], 404);
        }

        if ($action === 'add') {
            if (!$invitation->getBeverages()->contains($beverage)) {
                $invitation->addBeverage($beverage);
                $beverage->setCountTaken($beverage->getCountTaken() + 1);
            }
        } elseif ($action === 'remove') {
            if ($invitation->getBeverages()->contains($beverage)) {
                $invitation->removeBeverage($beverage);
                $beverage->setCountTaken($beverage->getCountTaken() - 1);
            }
        }

        $beverageRepository->save($beverage, true);
        $invitationRepository->save($invitation);

        return new JsonResponse([
            'success' => true,
            'countTaken' => $beverage->getCountTaken(),
            'countTakenText' => $beverage->getCountTaken() > 0 ? $beverage->getCountTaken().' personne'.($beverage->getCountTaken() > 1 ? 's' : '') : 'aucune'
        ]);
    }


    #[Route('/api/invitations/{slug}/music-choice', name: 'app_invitation_music_choice', methods: ['POST'])]
    public function musicChoice(
        string $slug,
        Request $request,
        InvitationRepository $invitationRepository,
    ): JsonResponse {
        $invitation = $invitationRepository->findOneBy(['slug' => $slug]);

        if (!$invitation) {
            return new JsonResponse(['success' => false, 'message' => 'Invitation non trouvée.'], 404);
        }

        $musicChoice = $request->request->get('music_choice');
        $invitation->setMusicChoice($musicChoice);

        $invitationRepository->save($invitation);

        return new JsonResponse([
            'success' => true,
        ]);
    }

    #[Route('/api/invitations/{slug}/send-book-messages', name: 'app_invitation_send_book_messages', methods: ['POST'])]
    public function sendBookMessages(
        string $slug,
        Request $request,
        InvitationRepository $invitationRepository,
        BookRepository $bookRepository,
        SluggerInterface $slugger
    ): JsonResponse {
        try {
            $invitation = $invitationRepository->findOneBy(['slug' => $slug]);

            if (!$invitation) {
                return new JsonResponse(['success' => false, 'message' => 'Invitation non trouvée.'], 404);
            }

            $message = $request->request->get('message');
            $hasAudio = filter_var($request->request->get('hasAudio'), FILTER_VALIDATE_BOOLEAN);
            $visibility = $request->request->get('visibility');

            $book = $invitation->getBook() ?? new Book();
            $book->setInvitation($invitation);
            $book->setEvent($invitation->getEvent());
            $book->setMessage($message);
            $book->setVisibility($visibility);

            if ($hasAudio && $request->files->get('audio')) {
                $audioFile = $request->files->get('audio');

                if ($audioFile->isValid()) {
                    $originalFilename = pathinfo($audioFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);

                    $destination = $this->getParameter('kernel.project_dir') . '/public/events/invitations/audios';

                    $tempFilename = $safeFilename . '-' . uniqid() . '.' . $audioFile->guessExtension();

                    try {
                        $audioFile->move($destination, $tempFilename);
                        $mp3Filename = $safeFilename . '-' . uniqid() . '.mp3';
                        $webmFilePath = $destination . '/' . $tempFilename;
                        $mp3FilePath = $destination . '/' . $mp3Filename;

                        $process = new Process(['ffmpeg', '-i', $webmFilePath, '-vn', '-acodec', 'libmp3lame', $mp3FilePath]);
                        $process->run();

                        if (!$process->isSuccessful()) {
                            throw new ProcessFailedException($process);
                        }

                        unlink($webmFilePath);

                        $book->setAudioPath('/events/invitations/audios/' . $mp3Filename);

                    } catch (\Exception $e) {
                        return new JsonResponse(['success' => false, 'message' => "Erreur lors du traitement du fichier audio : " . $e->getMessage()], 500);
                    }
                } else {
                    return new JsonResponse(['success' => false, 'message' => 'Fichier audio invalide.'], 400);
                }
            }

            $notificationMessage = $book->getInvitation()->getFullname() . " a ajouté ";
            $bookRepository->save($book);
            if (!empty($book->getMessage())) {
                $notificationMessage = 'un message écrit' + (!empty($book->getAudioPath()) ? ' et ' : '');
            }
            if (!empty($book->getAudioPath())) {
                $notificationMessage = 'un message vocal';
            }

            $tokens = [];
            $tokens[] = $book->getEvent()->getUser()->getPhoneId();

            $admins = $book->getEvent()->getEventAdmins();
            foreach ($admins as $admin) {
                if ($admin->getUser() !== null) {
                    $tokens[] = $admin->getUser()->getPhoneId();
                }
            }

            $this->notificationService->sendToMany(
                [], 'Livre d\'or', $notificationMessage
            );

            return new JsonResponse(['success' => true, 'message' => 'Message enregistré avec succès.']);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Erreur serveur : ' . $e->getMessage()], 500);
        }
    }

    #[Route('/api/invitations/{slug}/upload-photos', name: 'app_invitation_upload_photos', methods: ['POST'])]
    public function uploadPhotos(
        string $slug,
        Request $request,
        InvitationRepository $invitationRepository,
        MediaUploaderService $mediaUploader,
        PhotoRepository $photoRepository,
    ): JsonResponse {
        $invitation = $invitationRepository->findOneBy(['slug' => $slug]);
        if (!$invitation) {
            return new JsonResponse(['success' => false, 'message' => 'Invitation introuvable'], 404);
        }

        $uploaded = [];
        foreach ($request->files->get('photos', []) as $photo) {
            $fileName = $mediaUploader->upload($photo, 'events/invitations/photos');
            $photo = (new Photo())
                ->setInvitation($invitation)
                ->setPath($fileName);
            $photoRepository->save($photo);
            $uploaded[] = $fileName;
        }

        return new JsonResponse(['success' => true, 'uploaded' => $uploaded]);
    }


    function hexToRgb(string $hex): array {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0] . $hex[1].$hex[1] . $hex[2].$hex[2];
        }

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2))
        ];
    }
}
