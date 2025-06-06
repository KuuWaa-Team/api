<?php

namespace App\Controller\Template;

use App\Entity\Event\EventTemplate;
use App\Repository\EventCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/templates/preview', name: 'templates.')]
final class PreviewController extends AbstractController
{
    public function __construct(
        private readonly EventCategoryRepository $categoryRepository
    ) {}

    #[Route('/{id}', name: 'preview', methods: ['POST'])]
    public function preview(Request $request, EventTemplate $template): Response
    {
        $title = $request->request->get('title');
        $address = $request->request->get('address');
        $placeName = $request->request->get('placeName');
        $details = $request->request->get('details');
        $categoryId = $request->request->get('category_id');
        $startDate = $request->request->get('start_date');
        $endDate = $request->request->get('end_date');

        $imageBase64 = $request->request->get('image_base64');
        $imageName = $request->request->get('image_name');
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

        return $this->render("template/preview/{$template->getCode()}.html.twig", [
            'title' => $title,
            'address' => $address,
            'placeName' => $placeName,
            'details' => $details,
            'category' => $category,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'imagePath' => $imagePath
        ]);
    }

}
