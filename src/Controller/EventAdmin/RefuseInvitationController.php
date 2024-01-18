<?php

namespace App\Controller\EventAdmin;

use App\Entity\Event\EventAdmin;
use App\Repository\Event\EventAdminRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RefuseInvitationController extends AbstractController
{
    public function __construct(
        private readonly EventAdminRepository $adminRepository
    ){}

    public function __invoke(EventAdmin $eventAdmin): JsonResponse
    {
        $eventAdmin->setStatus('refused');
        $this->adminRepository->save($eventAdmin, true);


        $response = [
            'success' => true,
            'message' => 'Invitation réfusé'
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }
}