<?php

namespace App\Controller\EventPack;

use App\Repository\EventCategoryRepository;
use App\Repository\EventPackRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class GetEventPackController extends AbstractController
{

    public function __construct(
        private readonly EventPackRepository $packRepository,
        private readonly NormalizerInterface $normalizer,
    )
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $success = true;
        $errorType = null;
        $data = null;

        $paid = $request->query->all()['paid'] ?? null;

        if ($paid !== null) {
            $paid = (bool) $paid;
            $data = $this->packRepository->findBy([
                'paid' => $paid,
            ], [
                'packOrder' => "ASC"
            ]);
        } else {
            $data = $this->packRepository->findAll();
        }

        $success = true;

        $return = [
            'success'   => $success,
            'message'   => null,
            'errorType' => $errorType,
            'data'      => $data,
        ];

        $response = $this->normalizer->normalize($return, null, ['groups' => 'pack:pRead']);
        return new JsonResponse($response, Response::HTTP_OK);
    }
}