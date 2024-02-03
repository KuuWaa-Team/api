<?php

namespace App\Controller\EventCategory;

use App\Entity\User;
use App\Repository\EventCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class GetEventCategoriesController extends AbstractController
{
    public function __construct(
        private readonly EventCategoryRepository $categoryRepository,
        private readonly NormalizerInterface $normalizer,
    )
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $success = false;
        $errorType = null;
        $data = null;

        $paid = $request->query->all()['paid'] ?? null;

        if ($paid !== null) {
            $paid = (bool) $paid;
            $data = $this->categoryRepository->findBy([
                'paid'      => $paid,
                'validated' => true
            ]);
        } else {
            $data = $this->categoryRepository->findBy([
                'validated' => true
            ]);
        }
        $success = true;

        $return = [
            'success'   => $success,
            'message'   => null,
            'errorType' => $errorType,
            'data'      => $data,
        ];

        $response = $this->normalizer->normalize($return, null, ['groups' => 'category:pRead']);
        return new JsonResponse($response, Response::HTTP_OK);
    }
}