<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findEventsByRegularActivity()
    {
        return $this->createQueryBuilder('e')
            ->join('e.activity', 'a')
            ->andWhere('a.type = :type')
            ->andWhere('e.isEnabled = :isEnabled')
            ->setParameter('type', 'regular')
            ->setParameter('isEnabled', true)
            ->orderBy('e.id', 'ASC')  // Ajout d'un ordre pour limiter la requête de manière déterministe
            ->setMaxResults(3)        // Limite les résultats à 3
            ->getQuery()
            ->getResult();
    }

    public function findEventsByJourneyActivity()
    {
        return $this->createQueryBuilder('e')
            ->join('e.activity', 'a')
            ->andWhere('a.type = :type')
            ->andWhere('e.isEnabled = :isEnabled')
            ->setParameter('type', 'journey')
            ->setParameter('isEnabled', true)
            ->orderBy('e.id', 'ASC')  // Ajout d'un ordre pour limiter la requête de manière déterministe
            ->setMaxResults(3)        // Limite les résultats à 3
            ->getQuery()
            ->getResult();
    }

}
