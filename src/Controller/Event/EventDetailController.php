<?php

namespace App\Controller\Event;

use App\Entity\Event;
use App\Repository\Event\EventAdminRepository;
use App\Repository\EventRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EventDetailController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly EventAdminRepository $eventAdminRepository,
        private readonly NormalizerInterface $normalizerInterface,
    ) {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function __invoke(Event $event, UserInterface $user): Event
    {
        $eventAdmin = $this->eventAdminRepository->findByEventAndphoneNumber($event, $user->getPhoneNumber());
        if ($eventAdmin !== null && $eventAdmin->getStatus() !== 'refused') {
            $event->setShared(true);
            $event->setAdminStatus($eventAdmin->getStatus());
            $event->setAdminId($eventAdmin->getId());
        }
        return $event;
    }
}