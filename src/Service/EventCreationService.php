<?php
namespace App\Service;

use App\Entity\Event;
use App\Entity\EventCategory;
use App\Message\GenerateEventThumbnail;
use App\Repository\EventCategoryRepository;
use App\Repository\EventRepository;
use App\Repository\EventTemplateRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\MessageBusInterface;

class EventCreationService
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly EventCategoryRepository $categoryRepository,
        private readonly EventTemplateRepository $templateRepository,
        private readonly UserRepository $userRepository,
        private readonly MessageBusInterface $messageBus,
        private readonly string $projectDir,
        private readonly AdminService $adminService,
    ) {}

    public function createEvent(array $data, UploadedFile $image, ?UploadedFile $templateImage): Event
    {
        // Validation utilisateur
        $user = $this->userRepository->findOneBy(['uid' => $data['user_uid']]);
        if (!$user) {
            throw new \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException('Vous devez vous connecter');
        }

        // Gestion catégorie (optimisé)
        $category = $this->getOrCreateCategory($data);

        // Upload images (sans traitement lourd)
        $imagePath = $this->uploadImage($image);
        $templateImagePath = $templateImage ? $this->uploadImage($templateImage) : null;

        // Création événement
        $event = $this->buildEvent($data, $user, $category, $imagePath, $templateImagePath);

        // Sauvegarde en une seule transaction
        $this->eventRepository->save($event, true);

        // Traitement thumbnail en asynchrone
        $this->messageBus->dispatch(new GenerateEventThumbnail($event->getId(), $imagePath));

        // Notification admin
        $this->adminService->addUnreadNewEvent();

        return $event;
    }

    private function getOrCreateCategory(array $data): EventCategory
    {
        if (isset($data['category_id'])) {
            $category = $this->categoryRepository->find($data['category_id']);
            if ($category) return $category;
        }

        $category = (new EventCategory())
            ->setName($data['category_name'] ?? 'Non défini')
            ->setValidated(false);
        
        $this->categoryRepository->save($category, true);
        return $category;
    }

    private function uploadImage(UploadedFile $file): string
    {
        $destinationPath = $this->projectDir . '/public/events/images';
        $fileName = uniqid('', true) . '.' . $file->guessExtension();
        $file->move($destinationPath, $fileName);
        return '/events/images/' . $fileName;
    }

    private function buildEvent(
        array $data, 
        $user, 
        EventCategory $category, 
        string $imagePath, 
        ?string $templateImagePath
    ): Event {
        $template = $this->templateRepository->find($data['template_id']);
        $specifications = json_decode($data['specifications'], true, 512, JSON_THROW_ON_ERROR);

        $event = (new Event())
            ->setTitle($data['title'])
            ->setStartDate(new \DateTime($data['start_date']))
            ->setCategory($category)
            ->setDetails($data['details'])
            ->setAddress($data['address'])
            ->setPlaceName($data['place_name'])
            ->setOwner($user)
            ->setTemplate($template)
            ->setPlaceLongitude($data['place_longitude'])
            ->setPlaceLatitude($data['place_latitude'])
            ->setImagePath($imagePath)
            ->setTemplateImage($templateImagePath);

        if (!empty($data['end_date']) && strtoupper($data['end_date']) !== 'NULL') {
            $event->setEndDate(new \DateTime($data['end_date']));
        }

        foreach ($specifications as $specification) {
            $event->addEventSpecification(
                (new Event\EventSpecification())->setName($specification)
            );
        }

        return $event;
    }
}