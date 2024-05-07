<?php

namespace App\Controller\Public;

use App\Entity\Event\Book;
use App\Entity\Invitation;
use App\Form\Invitation\BeverageFormType;
use App\Form\Invitation\BookMessageFormType;
use App\Repository\BeverageRepository;
use App\Repository\Event\BookRepository;
use App\Repository\InvitationRepository;
use App\Service\DateService;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class InvitationController extends AbstractController
{
    /**
     * @throws NonUniqueResultException
     */
    #[Route('/invitations/{slug}', name: 'app_public_invitation')]
    public function index($slug, Request $request, BeverageRepository $beverageRepository, BookRepository $bookRepository, InvitationRepository $invitationRepository, DateService $dateService, KernelInterface $kernel): Response
    {
        $invitation = $invitationRepository->findBySlug($slug);
        $date = $dateService->getFullDate($invitation->getEvent()->getDate(), true, true) . " à " . $invitation->getEvent()->getDate()->format("H:i");
        $eventImage = $dateService->imageToBase64($kernel->getProjectDir() . $invitation->getEvent()->getImagePath());
        $codeImage = $dateService->imageToBase64($kernel->getProjectDir() . $invitation->getCodePath());
        $eventFeaturesSlug = $content = json_decode($invitation->getEvent()->getFeatureslugs(), true, 512, JSON_THROW_ON_ERROR);;
        $hasBook = in_array('BOOK', $eventFeaturesSlug, true);
        $hasBeverage = in_array('BEVERAGE', $eventFeaturesSlug, true);

        $form = $this->createForm(BookMessageFormType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $data = $form->getData();
            $book = (new Book())
                ->setEvent($invitation->getEvent())
                ->setInvitation($invitation)
                ->setMessage($data->getMessage())
            ;

            $bookRepository->save($book, true);

            $invitation->setBookSent(true);
            $invitationRepository->save($invitation, true);
            $this->addFlash('notice', 'Message envoyé avec succès');
        }

        $beveragesArray = [];
        foreach ($invitation->getEvent()->getBeverages() as $bev) {
            $beveragesArray[$bev->getName()] = $bev->getId();
        }

        $beverageForm = $this->createForm(BeverageFormType::class, null, [
            'choices' => $beveragesArray
        ]);
        $beverageForm->handleRequest($request);

        if ($beverageForm->isSubmitted() && $beverageForm->isValid()) {
            $data = $beverageForm->getData();
            $selectedBeverage = $beverageRepository->find($data['beverage']);
            $invitation->setBeverage($selectedBeverage);
            $invitationRepository->save($invitation, true);
            $this->addFlash('notice', 'Préférence envoyée avec succès');
        }

        return $this->render('public/invitation/index.html.twig', [
            'invitation' => $invitation,
            'date' => $date,
            'hasBook' => $hasBook,
            'hasBeverage' => $hasBeverage,
            'eventImage' => $eventImage,
            'codeImage' => $codeImage,
            'form' => $form->createView(),
            'beverageForm' => $beverageForm->createView(),
        ]);
    }
}
