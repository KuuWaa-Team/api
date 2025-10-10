<?php

namespace App\Controller\Admin;

use App\Entity\DownloadRequest;
use App\Entity\User;
use App\Repository\DownloadRequestRepository;
use App\Repository\UserRepository;
use App\Service\MailerService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class DownloadRequestController extends AbstractController
{
    public function __construct(
        private readonly DownloadRequestRepository $downloadRequestRepository,
        private readonly UserRepository $userRepository,
        private readonly NormalizerInterface $normalizer
    ){}

    #[Route('/admin/download/request', name: 'app_admin_download_request')]
    public function index(): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (!$user) {
            throw new UnauthorizedHttpException("Veuillez vous connecter", 1);
        }

        $user->setCountNewDownloadRequests(0);
        $this->userRepository->save($user);
        
        $requests = $this->downloadRequestRepository->findAll();
        $normalizedData = $this->normalizer->normalize(
            $requests,
            null,
            ['groups' => ['request:fRead']]
        );

        return $this->json([
            'success' => true,
            'data' => $normalizedData
        ]);
    }

    #[Route('/admin/download/request/send/{id}', name: 'app_admin_download_request_send')]
    public function send(DownloadRequest $downloadRequest, MailerService $mailerService): Response
    {
        $email = $downloadRequest->getAddress();
        $template = "email/download-request.html.twig";
        $templateDatas = [
            "address" => $email,
            "title" => "Telechargement KuuWaa Android",
            "download_link" => "https://play.google.com/store/apps/details?id=com.dmwema.kuuwaa"
        ];

        $success = true;
        $message = "Lien envoye avec success";

        try {
            $mailerService->sendEmail(
                $email,
                $template, $templateDatas, 'Lien de telechargement KuuWaa Android');

            $downloadRequest->setSent(true);
            $this->downloadRequestRepository->save($downloadRequest);
        } catch (Exception $e) {
            $success = false;
            $message = "Une erreur est survenue lors de l'envoie du mail";
        }

        return $this->json([
            'success' => $success,
            'message' => $message
        ]);
    }
}
