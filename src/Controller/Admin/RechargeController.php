<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\Wallet\OperationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class RechargeController extends AbstractController
{
    public function __construct(
        private readonly OperationRepository $operationRepository,
        private readonly UserRepository $userRepository,
        private readonly NormalizerInterface $normalizer
    ){}
    
    #[Route('/admin/recharge', name: 'app_admin_recharge')]
    public function index(): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (!$user) {
            throw new UnauthorizedHttpException("Veuillez vous connecter", 1);
        }

        $user->setCountNewRecharges(0);
        $this->userRepository->save($user);
        
        $recharges = $this->operationRepository->findAllValides();
        
        $normalizedData = $this->normalizer->normalize(
            $recharges,
            null,
            ['groups' => ['operation:fRead']]
        );

        return $this->json([
            'success' => true,
            'data' => $normalizedData
        ]);
    }
}
