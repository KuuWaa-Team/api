<?php

namespace App\Controller\EventAdmin;

use App\Entity\Event\EventAdmin;
use App\Repository\Event\EventAdminRepository;
use App\Repository\EventRepository;
use App\Service\PhoneService;
use App\Service\SmsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AddAdminController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly EventAdminRepository $adminRepository,
        private readonly NormalizerInterface $normalizer,
        private readonly SmsService $smsService,
        private readonly PhoneService $phoneService,
    ){}

    /**
     * @throws \JsonException
     */
    public function __invoke(Request $request, UserInterface $user): JsonResponse
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $name = $content["name"] ?? null;
        $phone = $content["phoneNumber"] ?? null;
        $eventId = $content["eventId"] ?? null;

        $event = $this->eventRepository->find($eventId);

        if ($phone === $user->getPhoneNumber()) {
            $admins = $this->adminRepository->findByEvent($event);
            $response = [
                'success'   => false,
                'admins' => $this->normalizer->normalize($admins, null, ['groups' => ['admin:pRead']]),
                'message'   => "Vous ne pouvez pas ajouter ce numéro comme protocoleur",
            ];

            return new JsonResponse($response, Response::HTTP_OK);
        }

        if ($this->adminRepository->adminExists($phone)) {
            $admins = $this->adminRepository->findByEvent($event);
            $response = [
                'success'   => false,
                'admins' => $this->normalizer->normalize($admins, null, ['groups' => ['admin:pRead']]),
                'message'   => "Vous avez déjà ajouté ce numéro comme protocoleur",
            ];

            return new JsonResponse($response, Response::HTTP_OK);
        }

        $admin = (new EventAdmin())
            ->setName($name)
            ->setPhoneNumber($phone)
            ->setEvent($event)
            ->setStatus('waiting')
        ;

        $this->adminRepository->save($admin, true);

        $eventAdminsCount = $event->getAdminsCount() ?? 0;

        $event->setAdminsCount($eventAdminsCount + 1);
        $this->eventRepository->save($event, true);

        $message = "Salut " . $name . "\n" . $user->getFirstname() . " " . $user->getLastname() . " vous invite à être protocoleur à son évenement ". '"' . $event->getName() .  '"'
            . " prévu pour le " . $event->getDate()->format("d/m/Y") . " \n\nVeuillez lancer ou télécharger l'application KuuWaa pour accépter l'invitation\n\nPour télécharger l'application KuuWaa, cliquez sur l'un de liens suivant : "
            . "\n\nPour iOS\nhttps://apps.apple.com/ca/app/kuuwaa/id6476254390?l=fr-CA\n\nPour android\nhttps://play.google.com/store/apps/details?id=com.app.kuuwaa"
        ;

        $this->smsService->send($this->phoneService->format($phone), $message);

        $admins = $this->adminRepository->findByEvent($event);
        $response = [
            'success'   => true,
            'admins' => $this->normalizer->normalize($admins, null, ['groups' => ['admin:pRead']]),
            'message'   => "Protocoleur enrégistré avec succès",
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}