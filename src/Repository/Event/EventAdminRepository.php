<?php

namespace App\Repository\Event;

use App\Entity\Beverage;
use App\Entity\Event;
use App\Entity\Event\EventAdmin;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventAdmin>
 *
 * @method EventAdmin|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventAdmin|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventAdmin[]    findAll()
 * @method EventAdmin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventAdminRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventAdmin::class);
    }

    /**
     * @return EventAdmin[] Returns an array of EventAdmin objects
     */
    public function findByEvent(Event $event): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.event = :ev')
            ->setParameter('ev', $event)
            ->orderBy('e.name', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return ?EventAdmin Returns an array of EventAdmin objects
     * @throws NonUniqueResultException
     */
    public function findByEventUser(Event $event, User $user): ?EventAdmin
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.user', 'u')
            ->andWhere('e.event = :ev')
            ->andWhere('u.id = :evU')
            ->setParameter('ev', $event)
            ->setParameter('evU', $user->getId())
            ->orderBy('e.name', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    // /**
    //  * @return ?EventAdmin Returns an array of EventAdmin objects
    //  * @throws NonUniqueResultException
    //  */
    // public function findByEventAndphoneNumber(Event $event, String $phone): ?EventAdmin
    // {
    //     return $this->createQueryBuilder('e')
    //         ->andWhere('e.event = :ev')
    //         ->andWhere('e.phoneNumber = :phone')
    //         ->setParameter('ev', $event)
    //         ->setParameter('phone', $phone)
    //         ->orderBy('e.name', 'ASC')
    //         ->getQuery()
    //         ->getOneOrNullResult()
    //     ;
    // }

    public function adminExists(String $phone): bool
    {
        $result = $this->createQueryBuilder('e')
            ->andWhere('e.phoneNumber = :phone')
            ->setParameter('phone', $phone)
            ->getQuery()
            ->getResult()
        ;
        return count($result) > 0;
    }

    public function save(EventAdmin $admin, bool $flush = false):void {
        $this->getEntityManager()->persist($admin);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
//    public function findOneBySomeField($value): ?EventAdmin
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
