<?php

namespace App\Repository\Event;

use App\Entity\Event;
use App\Entity\Event\Book;
use App\Entity\Invitation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function save(Book $book, bool $flush = false): void
    {
        $this->getEntityManager()->persist($book);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    /**
     * @return Book[] Returns an array of Book objects
     */
    public function findByEvent(Event $event): array
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.invitation', 'i')
            ->leftJoin('i.event', 'e')
            ->andWhere('e = :event')
            ->setParameter('event', $event)
            ->orderBy('b.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
