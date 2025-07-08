<?php

namespace App\Controller\Beverage;

use App\Entity\Invitation;
use App\Repository\BeverageRepository;
use App\Repository\InvitationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SelectBeverageController extends AbstractController
{
    public function __construct(
        private readonly BeverageRepository $beverageRepository,
        private readonly InvitationRepository $invitationRepository,
        private readonly NormalizerInterface $normalizer,
    ) {}

    /**
     * @throws \JsonException
     * @throws ExceptionInterface
     */
    public function __invoke(Invitation $invitation, Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $id = $content["id"] ?? null;
        $beverage = $this->beverageRepository->find($id);
        $beverage->setCountTaken($beverage->getCountTaken() === null ? 1 :  $beverage->getCountTaken() + 1);
        // $invitation->setBeverage($beverage);
        $this->invitationRepository->save($invitation, true);
        $this->beverageRepository->save($beverage, true);

        $response = [
            'success'   => true,
            'invitation' => $this->normalizer->normalize($invitation, null, ['groups' => ['invitation:fRead']]),
            'message'   => "",
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}