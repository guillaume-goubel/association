<?php

namespace App\Repository;

use App\Entity\ActivityMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ActivityMessage>
 *
 * @method ActivityMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActivityMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActivityMessage[]    findAll()
 * @method ActivityMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActivityMessage::class);
    }

    //    /**
    //     * @return ActivityMessage[] Returns an array of ActivityMessage objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ActivityMessage
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
