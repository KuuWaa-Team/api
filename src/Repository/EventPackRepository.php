<?php

namespace App\Repository;

use App\Entity\EventPack;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventPack>
 *
 * @method EventPack|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventPack|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventPack[]    findAll()
 * @method EventPack[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventPackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventPack::class);
    }

    /**
     * @return EventPack[] Returns an array of EventPack objects
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.price', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?EventPack
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
