<?php

namespace App\Controller\Public;
use App\Repository\InvitationRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvitationController extends AbstractController
{
    /**
     * @throws NonUniqueResultException
     */
    #[Route('/invitations/{code}', name: 'app_public_invitation')]
    public function index($code, InvitationRepository $invitationRepository): Response
    {
        $invitation = $invitationRepository->findByCode($code);
            return $this->render('public/invitation/index.html.twig', [
            'invitation' => $invitation,
        ]);
    }
}
