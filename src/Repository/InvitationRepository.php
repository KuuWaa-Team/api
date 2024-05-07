<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Event\EventAdmin;
use App\Entity\Invitation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
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

    public function findByEventAndphoneNumber(Event $event, String $phone): ?EventAdmin
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.event = :ev')
            ->andWhere('i.phoneNumber = :phone')
            ->setParameter('ev', $event)
            ->setParameter('phone', $phone)
            ->orderBy('i.name', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function invitationExists(String $phone, Event $event): bool
    {
        $result = $this->createQueryBuilder('i')
            ->andWhere('i.phoneNumber = :phone')
            ->andWhere('i.event = :ev')
            ->setParameter('phone', $phone)
            ->setParameter('ev', $event)
            ->getQuery()
            ->getResult()
        ;
        return count($result) > 0;
    }

    public function invitationHasBook(String $phone, Event $event): bool
    {
        $result = $this->createQueryBuilder('i')
            ->andWhere('i.event = :ev')
            ->andWhere('ev.features = :f')
            ->setParameter('f.', $phone)
            ->setParameter('ev', $event)
            ->getQuery()
            ->getResult()
        ;
        return count($result) > 0;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByCode(String $code): ?Invitation
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findBySlug(String $slug): ?Invitation
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function invitationCodeExists(String $code): bool
    {
        $result = $this->createQueryBuilder('i')
            ->andWhere('i.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getResult()
        ;
        return count($result) > 0;
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
    public function findByPhoneNumberAndStatus(String $phoneNumber, String $status): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.phoneNumber = :phone')
            ->andWhere('i.status = :status')
            ->leftJoin('i.event', 'e')
            ->setParameter('phone', $phoneNumber)
            ->setParameter('status', $status)
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
