<?php

namespace App\Controller\Admin;

use App\Entity\ContactForm;
use App\Repository\ContactFormRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ContactFormController extends AbstractController
{
    public function __construct(
        private readonly ContactFormRepository $contactFormRepository,
        private readonly NormalizerInterface $normalizer
    ){}

    #[Route('/admin/contact/form', name: 'app_admin_contact_form')]
    public function index(): Response
    {
        $contacts = $this->contactFormRepository->findAll();
        $normalizedData = $this->normalizer->normalize(
            $contacts,
            null,
            ['groups' => ['contact:fRead']]
        );

        return $this->json([
            'success' => true,
            'data' => $normalizedData
        ]);
    }
}
