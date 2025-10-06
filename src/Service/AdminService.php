<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AdminService
{

    public function __construct(private UserRepository $userRepository)
    {
    }
    
    public function addUnreadNewEvent () {
        $admins = $this->userRepository->findBy(['admin' => true]);
        /**
         * @var User $admin
         */
        foreach ($admins as $admin) {
            $admin->setCountNewEvents(($admin->getCountNewEvents() ?? 0) + 1);
            $this->userRepository->save($admin);
        }
    }
    
    public function addUnreadNewUser () {
        $admins = $this->userRepository->findBy(['admin' => true]);
        /**
         * @var User $admin
         */
        foreach ($admins as $admin) {
            $admin->setCountNewUsers(($admin->getCountNewUsers() ?? 0) + 1);
            $this->userRepository->save($admin);
        }
    }
    
    public function addUnreadNewDownloadRequest () {
        $admins = $this->userRepository->findBy(['admin' => true]);
        /**
         * @var User $admin
         */
        foreach ($admins as $admin) {
            $admin->setCountNewDownloadRequests(($admin->getCountNewDownloadRequests() ?? 0) + 1);
            $this->userRepository->save($admin);
        }
    }
    
    public function addUnreadNewNewsletter () {
        $admins = $this->userRepository->findBy(['admin' => true]);
        /**
         * @var User $admin
         */
        foreach ($admins as $admin) {
            $admin->setCountNewNewsletters(($admin->getCountNewNewsletters() ?? 0) + 1);
            $this->userRepository->save($admin);
        }
    }
    
    public function addUnreadNewRecharge () {
        $admins = $this->userRepository->findBy(['admin' => true]);
        /**
         * @var User $admin
         */
        foreach ($admins as $admin) {
            $admin->setCountNewRecharges(($admin->getCountNewRecharges() ?? 0) + 1);
            $this->userRepository->save($admin);
        }
    }
    
    public function addUnreadNewContact () {
        $admins = $this->userRepository->findBy(['admin' => true]);
        /**
         * @var User $admin
         */
        foreach ($admins as $admin) {
            $admin->setCountNewContacts(($admin->getCountNewContacts() ?? 0) + 1);
            $this->userRepository->save($admin);
        }
    }
}
