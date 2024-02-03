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
use App\Repository\FeatureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class EditEventController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly EventCategoryRepository $categoryRepository,
        private readonly ProvinceRepository $provinceRepository,
        private readonly CountryRepository $countryRepository,
        private readonly AddressRepository $addressRepository,
    ) {
    }

    /**
     * @throws \JsonException
     */
    public function __invoke(Event $event, Request $request, UserInterface $user, KernelInterface $kernel): JsonResponse
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $message = 'Evénement modifié avec succès !';
        $success = true;
        $errorType = null;

        $name = $content['name'] ?? null;
        $date = $content['date'] ?? null;
        $details = $content['details'] ?? null;
        $provinceId = $content['province_id'] ?? null;
        $street = $content['street'] ?? null;
        $number = $content['number'] ?? null;
        $ref = $content['ref'] ?? null;

        $category_id = $content['category_id'] ?? null;

         if ($category_id === null || $category_id === 0) {
             $category = (new EventCategory())
                 ->setName($content['category_name'] ?? 'Non défini')
                 ->setValidated(false)
             ;
         } else {
             $category = $this->categoryRepository->find($category_id);
         }

        if ($name !== null) {
            $event->setName($name);
        }

        if ($date !== null) {
            $event->setDate(new \DateTime($date));
        }

        if ($details !== null) {
            $event->setDetails($details);
        }

        if (
            $street !== $event->getAddress()->getStreet()
            || $number !== $event->getAddress()->getNumber()
            || $ref !== $event->getAddress()->getRef()
            || $provinceId !== $event->getAddress()->getProvince()->getId()
        ) {
            $address = (new Address())
                ->setRef($ref)
                ->setCountry($this->countryRepository->find(1))
                ->setNumber($number)
                ->setStreet($street)
            ;
            if ($provinceId !== null) {
                $address->setProvince($this->provinceRepository->find($provinceId));
            }
            $this->addressRepository->save($address, true);
            $event->setAddress($address);
        }

        $event->setCategory($category);

        try {
            $this->eventRepository->save($event, true);
        } catch (\Throwable $th) {
            $success = false;
            $message = 'Erreur : événement non modifié !';
        }

        $response = [
            'success'   => $success,
            'message'   => $message,
            'errorType' => $errorType,
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
