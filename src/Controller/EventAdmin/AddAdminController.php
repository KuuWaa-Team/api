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
    public function __invoke(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $name = $content["name"] ?? null;
        $roles = $content['permissions'] ?? [];
        $eventId = $content["eventId"] ?? null;

        $event = $this->eventRepository->find($eventId);
        
        if (empty($data['name'])) {
            return new JsonResponse(['success' => false, 'message' => 'Nom requis'], Response::HTTP_BAD_REQUEST);
        }

        if (!in_array('verifier_invitations', $roles)) {
            $roles[] = 'verifier_invitations';
        }

        $rolesString = implode(',', array_unique($roles));

        $code = strtoupper(bin2hex(random_bytes(3)));
        do {
            $code = strtoupper(bin2hex(random_bytes(3))); // 6 caractères hexadécimaux
            $exists = $this->adminRepository->findOneBy(['invitationCode' => $code]);
        } while ($exists !== null);

        $admin = (new EventAdmin())
            ->setName($name)
            ->setRoles($rolesString)
            ->setEvent($event)
            ->setInvitationCode($code)
            ->setStatus('waiting')
        ;

        $this->adminRepository->save($admin, true);

        $eventAdminsCount = $event->getAdminsCount() ?? 0;

        $event->setAdminsCount($eventAdminsCount + 1);
        $this->eventRepository->save($event, true);

        $user = $event->getUser();

        $message = "Salut " . $name . "\n" . $user->getDisplayName() . " vous invite à gerer à son évenement ". '"' . $event->getTitle() .  '"'
            . " prévu pour le " . $event->getStartDate()->format("d/m/Y") . " \n\nVeuillez lancer ou télécharger l'application KuuWaa pour accépter l'invitation\n\nPour télécharger l'application KuuWaa, cliquez sur l'un de liens suivant : "
            . "\n\nPour iOS\nhttps://apps.apple.com/ca/app/kuuwaa/id6476254390?l=fr-CA\n\nPour android\nhttps://play.google.com/store/apps/details?id=com.app.kuuwaa"
        ;

        $admins = $this->adminRepository->findByEvent($event);
        $response = [
            'success'   => true,
            'admins' => $this->normalizer->normalize($admins, null, ['groups' => ['admin:pRead']]),
            'message'   => "Protocoleur enrégistré avec succès",
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}