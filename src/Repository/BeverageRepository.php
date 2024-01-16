<?php

namespace App\Repository;

use App\Entity\Beverage;
use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Beverage>
 *
 * @method Beverage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Beverage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Beverage[]    findAll()
 * @method Beverage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BeverageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Beverage::class);
    }

    /**
     * @return Beverage[] Returns an array of Beverage objects
     */
    public function findByEvent(Event $event): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.event = :event')
            ->setParameter('event', $event)
            ->orderBy('b.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function save(Beverage $beverage, bool $flush = false):void {
        $this->getEntityManager()->persist($beverage);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
//    public function findOneBySomeField($value): ?Beverage
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
