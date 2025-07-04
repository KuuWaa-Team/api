<?php

namespace App\Controller\Template;

use App\Entity\Event\Book;
use App\Entity\Event\EventTemplate;
use App\Entity\Invitation;
use App\Form\Invitation\BookMessageFormType;
use App\Repository\Event\BookRepository;
use App\Repository\EventCategoryRepository;
use App\Service\ApiService;
use App\Service\DateService;
use Proxies\__CG__\App\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/templates/preview', name: 'templates.')]
final class PreviewController extends AbstractController
{
    public function __construct(
        private readonly EventCategoryRepository $categoryRepository,
        private readonly ApiService $apiService,
        private readonly DateService $dateService
    ) {}

    #[Route('/{id}', name: 'preview', methods: ['POST', 'GET'])]
    public function preview(Request $request, EventTemplate $template, KernelInterface $kernel): Response
    {
        $title = $request->request->get('title');
        $address = $request->request->get('address');
        $placeName = $request->request->get('placeName');
        $details = $request->request->get('details');
        $categoryId = $request->request->get('category_id') ?? 1;
        $startDate = $request->request->get('start_date');

        $endDate = $request->request->get('end_date');
        $placeLatitude = $request->request->get('place_latitude');
        $placeLongitude = $request->request->get('place_longitude');

        $imageBase64 = $request->request->get('image_base64');
        $imageName = $request->request->get('image_name');
        $imagePath = '';
        if ($imageBase64 && $imageName) {
            $imageContent = base64_decode($imageBase64);
            $publicDir = $this->getParameter('kernel.project_dir') . '/public/uploads/previews/';
            if (!file_exists($publicDir)) {
                if (!mkdir($publicDir, 0777, true) && !is_dir($publicDir)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $publicDir));
                }
            }

            $safeName = uniqid('', true) . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $imageName);
            file_put_contents($publicDir . $safeName, $imageContent);

            $imagePath = '/uploads/previews/' . $safeName;
        }
        $category = $this->categoryRepository->find((int) $categoryId);
        $apiUrl = $this->apiService->getApiUrl();

        $invitation = (new Invitation())
            ->setEvent((new Event())
                ->setTitle($title)
                ->setTemplate($template)
                ->setHasBook(true)
                ->setHasBeverage(true)
                ->setHasTables(true)
                ->setPlaceLatitude($placeLatitude)
                ->setPlaceLongitude($placeLongitude)
                ->setPlaceName($placeName)
                ->setAddress($address)
                ->setEndDate(new \DateTime($endDate))
                ->setStartDate(new \DateTime($startDate))
                ->setImagePath($imagePath)
                ->setThumbPath($imagePath)
                ->setDetails($details)
                ->setCategory($category)
            )
            ->setName("John Doe")
            ->setCivility("Mr.")
            ->setCodePath("/templates/code.png")
        ;
        $date = $this->dateService->getFullDate($invitation->getEvent()->getStartDate(), true, true);
        $eventImage = $this->dateService->imageToBase64($kernel->getProjectDir() . $invitation->getEvent()->getImagePath());
        $codeImage = $this->dateService->imageToBase64($kernel->getProjectDir() . $invitation->getCodePath());

        $form = $this->createForm(BookMessageFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $book = (new Book())
                ->setEvent($invitation->getEvent())
                ->setInvitation($invitation)
                ->setMessage($data->getMessage())
            ;

            $this->addFlash('notice', 'Message envoyé avec succès');
        }

        $year = $invitation->getEvent()->getStartDate()->format("Y");
        $hour = $invitation->getEvent()->getStartDate()->format("H");
        $minute = $invitation->getEvent()->getStartDate()->format("i");

        list($primaryR, $primaryG, $primaryB) = $this->hexToRgb($invitation->getEvent()->getTemplate()->getPrimaryColor());
        list($secondaryR, $secondaryG, $secondaryB) = $this->hexToRgb($invitation->getEvent()->getTemplate()->getSecondaryColor());

        return $this->render("public/invitation/index.html.twig", [
            'invitation' => $invitation,
            'template' => $invitation->getEvent()->getTemplate(),
            'date' => $date,
            'apiUrl' => $apiUrl,
            'eventImage' => $eventImage,
            'codeImage' => $codeImage,
            'form' => $form->createView(),
            'year' => $year,
            'hour' => $hour,
            'minute' => $minute,

            // colors
            'primaryR' => $primaryR,
            'primaryG' => $primaryG,
            'primaryB' => $primaryB,
            'secondaryR' => $secondaryR,
            'secondaryG' => $secondaryG,
            'secondaryB' => $secondaryB,

            'primaryHex' => str_replace('#', '', $invitation->getEvent()->getTemplate()->getPrimaryColor()),
            'secondaryHex' => str_replace('#', '', $invitation->getEvent()->getTemplate()->getSecondaryColor()),
        ]);
    }

    function hexToRgb(string $hex): array {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0] . $hex[1].$hex[1] . $hex[2].$hex[2];
        }

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2))
        ];
    }

}
