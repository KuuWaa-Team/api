<?php

namespace App\Controller;

use App\Entity\ContactForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ContactFormController extends AbstractController
{
    #[Route('/contact/form', name: 'app_contact_form', methods:['POST'])]
    public function index(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (
            !$data || 
            empty($data['fullname']) || 
            empty($data['address']) || 
            empty($data['subject']) || 
            empty($data['message'])
        ) {
            return $this->json([
                'success' => false,
                'message' => 'Tous les champs sont requis.'
            ], 400);
        }

        $fullname = trim($data['fullname']);
        $email = trim($data['address']);
        $subject = trim($data['subject']);
        $message = trim($data['message']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json([
                'success' => false,
                'message' => 'Adresse email invalide.'
            ], 400);
        }

        $contact = new ContactForm();
        $contact->setFullname($fullname);
        $contact->setAddress($email);
        $contact->setSubject($subject);
        $contact->setMessage($message);

        $em->persist($contact);
        $em->flush();

        return $this->json([
            'success' => true,
            'message' => 'Votre message a été envoyé avec succès.',
            'data' => [
                'id' => $contact->getId(),
                'fullname' => $contact->getFullname(),
                'address' => $contact->getAddress(),
                'subject' => $contact->getSubject(),
                'message' => $contact->getMessage(),
            ]
        ], 201);
    }
}
