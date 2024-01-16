<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Invitation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Invitation>
 *
 * @method Invitation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invitation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invitation[]    findAll()
 * @method Invitation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvitationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invitation::class);
    }

    public function save(Invitation $invitation, bool $flush): void
    {
        $this->getEntityManager()->persist($invitation);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Invitation[] Returns an array of Invitation objects
     */
    public function findByEvent(Event $event): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.event = :event')
            ->setParameter('event', $event)
            ->orderBy('i.name', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Invitation[] Returns an array of Invitation objects
     */
    public function findByPhoneNumber(String $phoneNumber): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.phoneNumber = :phone')
            ->leftJoin('i.event', 'e')
            ->setParameter('phone', $phoneNumber)
            ->orderBy('e.name', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    //    public function findOneBySomeField($value): ?Invitation
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
