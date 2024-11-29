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

    public function getAllAnimatorsCount(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = " SELECT COUNT(a.id) as total FROM animator a;
            ";

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery()->fetchAssociative();
        return $result;
    }

    public function animatorsListByIds(array $animatorsId): array
    {
        $stmt = $this->createQueryBuilder('a');
        
        if ($animatorsId) {
            $stmt->andWhere('a.id IN (:animators)');
            $stmt->setParameter('animators', $animatorsId);
        }
        
        $stmt->orderBy('a.lastName', 'ASC')  ;
        return $stmt->getQuery()->getResult();
    }


}
