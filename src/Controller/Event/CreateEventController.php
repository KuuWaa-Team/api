<?php

namespace App\Controller\Event;

use App\Entity\Event;
use App\Entity\EventCategory;
use App\Entity\User;
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

class CreateEventController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly EventCategoryRepository $categoryRepository,
        private readonly EventPackRepository $packRepository,
        private readonly FeatureRepository $featureRepository,
    ) {
    }

    /**
     * @throws \JsonException
     */
    public function __invoke(Request $request, UserInterface $user, KernelInterface $kernel): JsonResponse
    {
        $content = $request->request->all();
        $message = 'Evénement créé avec succès !';
        $success = true;
        $errorType = null;

        $name = $content['name'];
        $paid = $content['paid'];
        $date = $content['date'];
        $address = $content['address'];
        $details = $content['details'];

        $features = $content['features_id'] ?? null;

        $pack_id = $content['pack_id'];

        $category_id = $content['category_id'] ?? null;

         if ($category_id === null) {
             $category = (new EventCategory())
                 ->setName($content['category_name'] ?? 'Non défini')
                 ->setPaid($paid)
                 ->setValidated(false)
             ;
         } else {
             $category = $this->categoryRepository->find($category_id);
         }

        $image = $request->files->get('image');
        $fileName = uniqid('', true) . '.' . $image->guessExtension();
        $destinationPath = $kernel->getProjectDir() . '/public/events/images';
        $image->move($destinationPath, $fileName);
        $imagePath = '/events/images/'.$fileName;

         $event = (new Event())
             ->setName($name)
             ->setDate(new \DateTime($date))
             ->setEventPack(
                 $this->packRepository->find($pack_id)
             )
             ->setCategory($category)
             ->setStatus('wainting')
             ->setDetails($details)
             ->setUser($this->getUser())
             ->setPaidEvent($paid)
             ->setAddress($address)
             ->setPaymentStatus('waiting')
             ->setImagePath($imagePath)
         ;

         $this->eventRepository->save($event);

         foreach ($content as $key => $field) {
             if (str_starts_with($key, "feature")) {
                 $event->addFeature($this->featureRepository->find($field));
             }
         }

        try {
            $this->eventRepository->save($event, true);
        } catch (\Throwable $th) {
            $success = false;
            $message = 'Erreur : événement non créé !';
        }

        $response = [
            'success'   => $success,
            'message'   => $message,
            'errorType' => $errorType,
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
