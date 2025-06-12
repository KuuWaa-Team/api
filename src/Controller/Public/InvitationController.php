<?php

namespace App\Controller\Public;
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
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class InvitationController extends AbstractController
{
    public function __construct(
        private readonly ApiService $apiService,
    ){}

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/invitations/{slug}', name: 'app_public_invitation')]
    public function index($slug, Request $request, BeverageRepository $beverageRepository, BookRepository $bookRepository, InvitationRepository $invitationRepository, DateService $dateService, KernelInterface $kernel): Response
    {
        $invitation = $invitationRepository->findBySlug($slug);
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

            $invitation->setBookSent(true);
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
