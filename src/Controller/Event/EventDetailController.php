<?php

namespace App\Controller\Event;

use App\Entity\Event;
use App\Repository\Event\EventAdminRepository;
use App\Repository\EventRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EventDetailController extends AbstractController
{
    public function __construct(
        private readonly EventAdminRepository $eventAdminRepository,
    ) {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function __invoke(Event $event): Event
    {
//        $eventAdmin = $this->eventAdminRepository->find($event, $user->getPhoneNumber());
//        if ($eventAdmin !== null && $eventAdmin->getStatus() !== 'refused') {
//            $event->setShared(true);
//            $event->setAdminStatus($eventAdmin->getStatus());
//            $event->setAdminId($eventAdmin->getId());
//        }
        return $event;
    }
}