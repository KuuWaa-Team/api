<?php

namespace App\Controller\EventAdmin;

use App\Entity\Event\EventAdmin;
use App\Repository\Event\EventAdminRepository;
use App\Service\ApiService;
use App\Service\DateService;
use App\Service\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class EventAdminController extends AbstractController
{
    public function __construct(
        private readonly EventAdminRepository $adminRepository,
        private readonly DateService $dateService,
        private readonly ApiService $apiService,
    ){}

    #[Route('/event-admins/{code}', name: 'app_event_admin_invitation')]
    public function eventAdminInvitation(string $code): JsonResponse
    {
        $eventAdmin = $this->adminRepository->findOneBy([
            'invitationCode' => $code
        ]);
        
        $apiUrl = $this->apiService->getApiUrl();

        $date = $this->dateService->getFullDate($eventAdmin->getEvent()->getStartDate(), true, true);

        $roles = explode(',', $eventAdmin->getRoles());
        $rolesNames = [];
        foreach ($roles as $role) {
            $rolesNames[$role] = [
                'name' => Utils::getTitle($role),
                'desc' => Utils::getDescription($role),
            ];
        }

        return $this->render('public/event_admin/invitation.html.twig', [
            'eventAdmin' => $eventAdmin,
            'apiUrl' => $apiUrl,
            'date' => $date,
            'rolesNames' => $rolesNames
        ]);
    }
}