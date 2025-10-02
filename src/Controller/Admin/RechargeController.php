<?php

namespace App\Controller\Admin;

use App\Repository\Wallet\OperationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class RechargeController extends AbstractController
{
    public function __construct(
        private readonly OperationRepository $operationRepository,
        private readonly NormalizerInterface $normalizer
    ){}
    
    #[Route('/admin/recharge', name: 'app_admin_recharge')]
    public function index(): Response
    {
        $recharges = $this->operationRepository->findAll();
        foreach ($recharges as $key => $recharge) {
            if ($recharge->getPhoneNumber() === '243999999999') {
                unset($recharges[$key]);
            }
        }
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
