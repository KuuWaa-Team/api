<?php

namespace App\Controller\Table;

use App\Entity\Table;
use App\Repository\TableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EditTableController extends AbstractController
{
    public function __construct(
        private readonly TableRepository $tableRepository,
        private readonly NormalizerInterface $normalizer,
    )
    {
    }

    /**
     * @throws \JsonException
     */
    public function __invoke(Request $request, Table $table, KernelInterface $kernel)
    {
        $name = $content["name"] ?? null;
        $count = $content["count"] ?? null;
        
        $table->setName($name);
        $table->setCount((int) $count);

        $tables = $this->tableRepository->findByEvent($table->getEvent());
        $response = [
            'success'   => true,
            'tables' => $this->normalizer->normalize($tables, null, ['groups' => ['table:pRead']]),
            'message'   => "Table modifiée avec succès",
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}