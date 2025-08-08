<?php

namespace App\Controller\Table;

use App\Entity\Table;
use App\Repository\TableRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RemoveTableController extends AbstractController
{
    public function __construct(
        private readonly TableRepository $tableRepository,
        private readonly NormalizerInterface $normalizer,
        private readonly EntityManagerInterface $em
    )
    {
    }

    /**
     * @throws \JsonException
     */
    public function __invoke(Request $request, Table $table, KernelInterface $kernel)
    {

        $this->em->remove($table);
        $this->em->flush();

        $message = "Table supprimée avec succès";

        $tables = $this->tableRepository->findByEvent($table->getEvent());
        $response = [
            'success'   => true,
            'tables' => $this->normalizer->normalize($tables, null, ['groups' => ['table:pRead']]),
            'message'   => $message,
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}