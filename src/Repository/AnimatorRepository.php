<?php

namespace App\Repository;

use App\Entity\Animator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @extends ServiceEntityRepository<Animator>
 *
 * @method Animator|null find($id, $lockMode = null, $lockVersion = null)
 * @method Animator|null findOneBy(array $criteria, array $orderBy = null)
 * @method Animator[]    findAll()
 * @method Animator[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnimatorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Animator::class);
    }


    public function queryOrderingByName($activityChoice, $animatorChoice): Query
    {
        // return $this->createQueryBuilder('a')
        //     ->orderBy('a.lastName', 'ASC')
        //     ->getQuery()
        // ;

        $stmt = $this->createQueryBuilder('a');
        $stmt->leftjoin('a.events', 'e');
        $stmt->leftjoin('e.activity', 'ac');

        if ($activityChoice && $activityChoice !== 'all') {
            $stmt->andWhere('ac.id = :activityChoice');
            $stmt->setParameter('activityChoice', $activityChoice);
        }

        if ($animatorChoice && $animatorChoice !== 'all') {
            $stmt->andWhere('a.id = :animatorChoice');
            $stmt->setParameter('animatorChoice', $animatorChoice);
        }

        $stmt->orderBy('a.lastName', 'ASC')  ;
        return $stmt->getQuery();
    }

    public function animatorsList(): array
    {
        $stmt = $this->createQueryBuilder('a');
        $stmt->orderBy('a.lastName', 'ASC')  ;
        return $stmt->getQuery()->getResult();
    }


}
