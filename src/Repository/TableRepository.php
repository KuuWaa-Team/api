<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Table;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Table>
 *
 * @method Table|null find($id, $lockMode = null, $lockVersion = null)
 * @method Table|null findOneBy(array $criteria, array $orderBy = null)
 * @method Table[]    findAll()
 * @method Table[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Table::class);
    }

    public function save(Table $table, bool $flush = true): void
    {
        $this->getEntityManager()->persist($table);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

        /**
         * @return Table[] Returns an array of Table objects
         */
        public function findByEvent(Event $event): array
        {
            return $this->createQueryBuilder('t')
                ->andWhere('t.event = :event')
                ->setParameter('event', $event)
                ->orderBy('t.name', 'ASC')
                ->getQuery()
                ->getResult()
            ;
        }

}
