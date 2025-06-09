<?php

namespace App\Controller\Wallet;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ListingOperationsController extends AbstractController
{
    public function __construct(
        private readonly NormalizerInterface $normalizer
    ) {}

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(User $user, Request $request): JsonResponse
    {
        $operations = $user->getOperations();
        return new JsonResponse([
            'success' => true,
            'data' => $this->normalizer->normalize($operations, null, ['groups' => ['operation:pRead']])
        ], Response::HTTP_OK);
    }
}