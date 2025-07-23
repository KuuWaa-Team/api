<?php

namespace App\Controller\Event;

use App\Entity\Address\Address;
use App\Entity\Event;
use App\Entity\EventCategory;
use App\Repository\Address\AddressRepository;
use App\Repository\Address\CountryRepository;
use App\Repository\Address\ProvinceRepository;
use App\Repository\EventCategoryRepository;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EditEventController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly EventCategoryRepository $categoryRepository,
        private readonly ProvinceRepository $provinceRepository,
        private readonly CountryRepository $countryRepository,
        private readonly NormalizerInterface $normalizerInterface,
        private readonly AddressRepository $addressRepository,
    ) {
    }

    /**
     * @throws \JsonException
     */
    public function __invoke(Event $event, Request $request, KernelInterface $kernel): JsonResponse
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $step = $content['step'];
        $message = 'Evénement modifié avec succès !';
        $success = true;
        $errorType = null;

        if ($step === "GENERAL_INFORMATIONS") {
            $title = $content['title'] ?? null;
            $details = $content['details'] ?? null;
            $category_id = $content['category_id'] ?? null;

            $category = $this->categoryRepository->find($category_id ?? 0);
            if ($category === null) {
                $category = (new EventCategory())
                    ->setName($content['category_name'] ?? 'Non défini')
                    ->setValidated(false)
                ;
                $this->categoryRepository->save($category);
            }
            $event  
                ->setTitle($title)
                ->setDetails($details)
                ->setCategory($category);
        } else if ($step == "PLACE_DATE_HOUR") {
            $startDate = $content['start_date'];
            $endDate = $content['end_date'];
            $address = $content['address'];
            $placeName = $content['place_name'];
            $placeLongitude = $content['place_longitude'];
            $placeLatitude = $content['place_latitude'];

            $event
                ->setAddress($address)
                ->setPlaceName($placeName)
                ->setPlaceLongitude($placeLongitude)
                ->setPlaceLatitude($placeLatitude)
                ->setStartDate(new \DateTime($startDate));

            if (!empty($endDate) && strtoupper($endDate) != "NULL") {
                $event->setEndDate(new \DateTime($endDate));
            } else {
                $event->setEndDate(null);
            }
        }

        try {
            $this->eventRepository->save($event, true);
        } catch (\Throwable $th) {
            $success = false;
            $message = 'Erreur : événement non modifié !';
        }
        
        $data = $this->normalizerInterface->normalize($event, null, ['groups' => ['event:pRead']]);

        $response = [
            'success'   => $success,
            'message'   => $message,
            'errorType' => $errorType,
            'event' => $data
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
