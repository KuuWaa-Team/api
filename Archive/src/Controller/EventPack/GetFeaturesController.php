<?php

namespace App\Controller\EventPack;

use App\Repository\EventCategoryRepository;
use App\Repository\EventPackRepository;
use App\Repository\FeatureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class GetFeaturesController extends AbstractController
{

    public function __construct(
        private readonly FeatureRepository $featureRepository,
        private readonly NormalizerInterface $normalizer,
    )
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $success = true;
        $errorType = null;
        $data = $this->featureRepository->findAll();

        $return = [
            'success'   => $success,
            'message'   => null,
            'errorType' => $errorType,
            'data'      => $data,
        ];

        $response = $this->normalizer->normalize($return, null, ['groups' => 'feature:pRead']);
        return new JsonResponse($response, Response::HTTP_OK);
    }
}