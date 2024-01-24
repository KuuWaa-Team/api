<?php

namespace App\Controller\Public;
use App\Repository\InvitationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvitationController extends AbstractController
{
    #[Route('/invitations/{id}', name: 'app_public_invitation')]
    public function index($id, InvitationRepository $invitationRepository): Response
    {
        $invitation = $invitationRepository->find($id);
            return $this->render('public/invitation/index.html.twig', [
            'invitation' => $invitation,
        ]);
    }
}
