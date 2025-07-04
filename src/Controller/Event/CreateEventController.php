<?php

namespace App\Controller\Event;
use App\Entity\Address\Address;
use App\Entity\Event;
use App\Entity\EventCategory;
use App\Entity\User;
use App\Repository\Address\AddressRepository;
use App\Repository\Address\CountryRepository;
use App\Repository\Address\ProvinceRepository;
use App\Repository\EventCategoryRepository;
use App\Repository\EventPackRepository;
use App\Repository\EventRepository;
use App\Repository\EventTemplateRepository;
use App\Repository\FeatureRepository;
use App\Repository\UserRepository;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ManipulatorInterface;
use Imagine\Image\Point;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CreateEventController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly EventCategoryRepository $categoryRepository,
        private readonly UserRepository $userRepository,
        private readonly EventTemplateRepository $templateRepository,
    ) {
    }

    /**
     * @throws \JsonException
     */
    public function __invoke(Request $request, KernelInterface $kernel): JsonResponse
    {
        $content = $request->request->all();
        $message = 'Evénement créé avec succès !';
        $success = true;
        $errorType = null;

        $userUid = $content['user_uid'];
        $user = $this->userRepository->findOneBy(['uid' => $userUid]);

        if ($user === null) {
            throw new UnauthorizedHttpException("Vous devez vous connecter");
        }

        $title = $content['title'];
        $startDate = $content['start_date'];
        $endDate = $content['end_date'];
        $details = $content['details'];

        $address = $content['address'];
        $placeName = $content['place_name'];
        $placeLongitude = $content['place_longitude'];
        $placeLatitude = $content['place_latitude'];

        $specifications = json_decode($content['specifications'], true, 512, JSON_THROW_ON_ERROR);

        $templateId = $content['template_id'];

        $category_id = $content['category_id'] ?? null;

        $category = $this->categoryRepository->find($category_id ?? 0);
        if ($category === null) {
            $category = (new EventCategory())
                ->setName($content['category_name'] ?? 'Non défini')
                ->setValidated(false)
            ;
            $this->categoryRepository->save($category);
        }
        
        $image = $request->files->get('image');
        $fileName = uniqid('', true) . '.' . $image->guessExtension();
        $destinationPath = $kernel->getProjectDir() . '/public/events/images';
        $image->move($destinationPath, $fileName);
        $imagePath = '/events/images/'.$fileName;

        $thumbnailPath = '/events/thumb/'.$fileName;
        $thumbnailAbsolutePath = $kernel->getProjectDir() . '/public/events/thumb/' . $fileName;

        $imagine = new Imagine();
        $image = $imagine->open($destinationPath . '/' . $fileName);
        $thumbnail = $image->thumbnail(new Box(375, 375), ManipulatorInterface::THUMBNAIL_OUTBOUND);
        $thumbnail->resize(new Box(375, 375))
            ->crop(new Point(0, 0), new Box(375, 375))
            ->save($thumbnailAbsolutePath);

        $template = $this->templateRepository->find($templateId);

        $endDateObj = $endDate == null ? null : new \DateTime($endDate);
        $event = (new Event())
            ->setTitle($title)
            ->setStartDate(new \DateTime($startDate))
            ->setEndDate($endDateObj)
            ->setCategory($category)
            ->setDetails($details)
            ->setAddress($address)
            ->setPlaceName($placeName)
            ->setPlaceName($placeName)
            ->setOwner($user)
            ->setTemplate($template)
            ->setPlaceLongitude($placeLongitude)
            ->setPlaceLatitude($placeLatitude)
            ->setImagePath($imagePath)
            ->setThumbPath($thumbnailPath)
        ;

        foreach ($specifications as $specification) {
            $event->addEventSpecification((new Event\EventSpecification())->setName($specification));
        }

        $this->eventRepository->save($event, true);

        $response = [
            'success'   => $success,
            'eventId'   => $success ? $event->getId() : null,
            'message'   => $message,
            'errorType' => $errorType,
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
