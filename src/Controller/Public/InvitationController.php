<?php

namespace App\Controller\Public;
use App\Repository\InvitationRepository;
use App\Service\DateService;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class InvitationController extends AbstractController
{
    /**
     * @throws NonUniqueResultException
     */
    #[Route('/invitations/{code}', name: 'app_public_invitation')]
    public function index($code, InvitationRepository $invitationRepository, DateService $dateService, KernelInterface $kernel): Response
    {
        $invitation = $invitationRepository->findByCode($code);
        $date = $dateService->getFullDate($invitation->getEvent()->getDate(), true, true) . " à " . $invitation->getEvent()->getDate()->format("H:i");
        $eventImage = $dateService->imageToBase64($kernel->getProjectDir() . $invitation->getEvent()->getImagePath());
        $codeImage = $dateService->imageToBase64($kernel->getProjectDir() . $invitation->getCodePath());
        return $this->render('public/invitation/index.html.twig', [
            'invitation' => $invitation,
            'date' => $date,
            'eventImage' => $eventImage,
            'codeImage' => $codeImage
        ]);
    }
}
