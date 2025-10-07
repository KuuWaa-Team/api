<?php

namespace App\Controller\Admin;

use App\Entity\ContactForm;
use App\Entity\User;
use App\Repository\ContactFormRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ContactFormController extends AbstractController
{
    public function __construct(
        private readonly ContactFormRepository $contactFormRepository,
        private readonly UserRepository $userRepository,
        private readonly NormalizerInterface $normalizer
    ){}

    #[Route('/admin/contact/form', name: 'app_admin_contact_form')]
    public function index(): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (!$user) {
            throw new UnauthorizedHttpException("Veuillez vous connecter", 1);
        }

        $user->setCountNewContacts(0);
        $this->userRepository->save($user);
        $contacts = $this->contactFormRepository->findBy([], ['createdAt' => 'DESC']);
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
