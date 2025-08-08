<?php

namespace App\Controller\Invitation;

use App\Entity\Invitation;
use App\Repository\InvitationRepository;
use App\Repository\TableRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RemoveInvitationController extends AbstractController
{
    public function __construct(
        private readonly InvitationRepository $invitationRepository,
        private readonly TableRepository $tableRepository,
        private readonly NormalizerInterface $normalizer,
        private readonly EntityManagerInterface $em
    )
    {}

    /**
     * @throws \JsonException
     * @throws \Exception
     */
    public function __invoke(Request $request, Invitation $invitation): JsonResponse
    {
        $table = $invitation->getInvitationTable();
        $countInv = $invitation->getCount();
        $this->em->remove($invitation);
        $this->em->flush();
        $invitations = $this->invitationRepository->findByEvent($invitation->getEvent());

        if ($table !== null) {
            $table->setCount($table->getCount() - $countInv);

            if ($table->getCount() < 0) {
                $table->setCount(0);
            }
            $this->tableRepository->save($table);
        }

        return new JsonResponse([
            'invitations' => $this->normalizer->normalize($invitations, null, ['groups' => ['invitation:pRead']]),
            "success" => true,
            'message' => 'Invitation supprimée avec succès'
        ], Response::HTTP_OK);
    }
}