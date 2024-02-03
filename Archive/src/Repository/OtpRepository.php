<?php

namespace App\Repository;

use App\Entity\Otp;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Otp>
 *
 * @method Otp|null find($id, $lockMode = null, $lockVersion = null)
 * @method Otp|null findOneBy(array $criteria, array $orderBy = null)
 * @method Otp[]    findAll()
 * @method Otp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OtpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Otp::class);
    }

    public function save(Otp $otp, bool $flush): void
    {
        $this->getEntityManager()->persist($otp);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws NonUniqueResultException
     */
    public function validOtp (String $otp, User $user): bool {
        $query = $this->createQueryBuilder('o')
            ->leftJoin('o.user', 'u')
            ->andWhere('u = :user')
            ->setParameter('user', $user)
            ->setMaxResults(1)
            ->orderBy('o.id', 'desc')
        ;
        $otpData = $query
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if ($otpData === null || $otpData->getOtp() !== $otp) {
            return false;
        }

        $currentDateTime = new \DateTime();
        $timeDifference = $currentDateTime->diff($otpData->getCreatedAt());

        $return = (
            $timeDifference->y === 0
            && $timeDifference->m === 0
            && $timeDifference->d === 0
            && $timeDifference->h === 0
            && $timeDifference->i < 5
        );

        if ($return) {
            $this->getEntityManager()->remove($otpData);
            $this->getEntityManager()->flush();
        }

        return $return;
    }

//    /**
//     * @return Otp[] Returns an array of Otp objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Otp
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
