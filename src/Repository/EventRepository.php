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

    public function findEventsForHomePage()
    {
        return $this->createQueryBuilder('e')
            ->join('e.activity', 'a')
            ->andWhere('e.isEnabled = :isEnabled')
            ->setParameter('isEnabled', true)
            ->orderBy('e.id', 'ASC')  
            ->setMaxResults(4)       
            ->getQuery()
            ->getResult();
    }

    public function getDistincYearCreatedAt()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "    SELECT DISTINCT(YEAR(date_start_at)) as year
                    FROM `event`
                    WHERE DATE(date_start_at) >= DATE(NOW())
                    ORDER BY year DESC
               ";

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery()->fetchAllAssociative();
        return $result;
    }

    public function getDistincYearCreatedAtForAgendaView()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "    SELECT DISTINCT(YEAR(date_start_at)) as year
                    FROM `event`
                    WHERE DATE(date_start_at) >= DATE(NOW())
                    ORDER BY year DESC
               ";

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery()->fetchAllAssociative();
        return $result;
    }

    public function getDistincYearCreatedAtForArchiveView()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "    SELECT DISTINCT(YEAR(date_start_at)) as year
                    FROM `event`
                    WHERE DATE(date_start_at) < DATE(NOW())
                    ORDER BY year DESC
               ";

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery()->fetchAllAssociative();
        return $result;
    }

    public function getDistinctMonthCreatedAt($yearChoice)
    {
        $sql_yearChoice = "";
        if ($yearChoice != null) {
            $sql_yearChoice = " AND YEAR(A.date_start_at) = :yearChoice ";
        }

        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT DISTINCT(MONTH(A.date_start_at)) AS month_number,
                    ELT(MONTH(A.date_start_at),
                        'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre') AS month_name
                FROM `event` A
                WHERE DATE(date_start_at) >= DATE(NOW())
                " . $sql_yearChoice . "

                ORDER BY month_number";

        $stmt = $conn->prepare($sql);
        if ($yearChoice != null) {
            $stmt->bindValue(':yearChoice', $yearChoice);
        }
        $result = $stmt->executeQuery()->fetchAllAssociative();
        return $result;
    }

    public function getDistinctMonthCreatedAtForAgendaView($yearChoice)
    {
        $sql_yearChoice = "";
        if ($yearChoice != null) {
            $sql_yearChoice = " AND YEAR(A.date_start_at) = :yearChoice ";
        }

        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT DISTINCT(MONTH(A.date_start_at)) AS month_number,
                    ELT(MONTH(A.date_start_at),
                        'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre') AS month_name
                FROM `event` A
                WHERE DATE(date_start_at) >= DATE(NOW())
                " . $sql_yearChoice . "
                

                ORDER BY month_number";

        $stmt = $conn->prepare($sql);
        if ($yearChoice != null) {
            $stmt->bindValue(':yearChoice', $yearChoice);
        }
        $result = $stmt->executeQuery()->fetchAllAssociative();
        return $result;
    }

    public function getDistinctMonthCreatedAtForArchiveView($yearChoice)
    {
        $sql_yearChoice = "";
        if ($yearChoice != null) {
            $sql_yearChoice = " AND YEAR(A.date_start_at) = :yearChoice ";
        }

        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT DISTINCT(MONTH(A.date_start_at)) AS month_number,
                    ELT(MONTH(A.date_start_at),
                        'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre') AS month_name
                FROM `event` A
                WHERE DATE(date_start_at) < DATE(NOW())
                " . $sql_yearChoice . "
                

                ORDER BY month_number";

        $stmt = $conn->prepare($sql);
        if ($yearChoice != null) {
            $stmt->bindValue(':yearChoice', $yearChoice);
        }
        $result = $stmt->executeQuery()->fetchAllAssociative();
        return $result;
    }

    public function getDistinctCreator()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "    SELECT U.id AS user_id, CONCAT(U.last_name, ' ', U.first_name) AS creator
                    FROM `event` E 
                    LEFT JOIN user U 
                    ON U.id = E.user_id

                    GROUP BY U.id
                    ORDER BY U.last_name ASC
               ";

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery()->fetchAllAssociative();
        return $result;
    }

    public function getEventListforAdmin($yearChoice, $monthChoice, $creatorChoice, $activityChoice)
    {
        $stmt = $this->createQueryBuilder('e');

        if ($yearChoice) {
            $stmt->andwhere('YEAR(e.dateStartAt) = :year');
            $stmt->setParameter('year', $yearChoice);
        }

        if ($monthChoice) {
            $stmt->andwhere('MONTH(e.dateStartAt) = :month');
            $stmt->setParameter('month', $monthChoice);
        }

        if ($creatorChoice) {
            $stmt->andwhere('e.user = :user');
            $stmt->setParameter('user', $creatorChoice);
        }

        if ($activityChoice && $activityChoice != 'all') {
            $stmt->andwhere('e.id = :activityChoice');
            $stmt->setParameter('activityChoice', $monthChoice);
        }

        $stmt->addOrderBy('e.dateStartAt', 'ASC');
        return $stmt->getQuery()->getResult();
    }

    public function findNextUpcomingEvent()
    {
        $today = new \DateTime();
        $today->setTime(0, 0); // On enlève l'heure pour exclure toute date du jour

        return $this->createQueryBuilder('e')
            ->andWhere('e.dateStartAt > :today')        
            ->andWhere('e.isEnabled = :isEnabled')      
            ->setParameter('today', $today)
            ->setParameter('isEnabled', true)
            ->orderBy('e.dateStartAt', 'ASC')          
            ->setMaxResults(1)                          
            ->getQuery()
            ->getOneOrNullResult();                     
    }

    public function findNextUpcomingList()
    {
        $today = new \DateTime();
        $today->setTime(0, 0);

        return $this->createQueryBuilder('e')
            ->andWhere('e.dateStartAt > :today')        
            ->andWhere('e.isEnabled = :isEnabled')     
            ->setParameter('today', $today)
            ->setParameter('isEnabled', true)
            ->orderBy('e.dateStartAt', 'ASC')          
            ->setMaxResults(4)                         
            ->getQuery()
            ->getResult();                  
    }

    public function findLastPastEventsList()
    {
        $today = new \DateTime();
        $today->setTime(0, 0);

        return $this->createQueryBuilder('e')
            ->andWhere('e.dateStartAt < :today')        
            ->andWhere('e.isEnabled = :isEnabled')     
            ->setParameter('today', $today)
            ->setParameter('isEnabled', true)
            ->orderBy('e.dateStartAt', 'ASC')          
            ->setMaxResults(4)                         
            ->getQuery()
            ->getResult();                  
    }

    public function getEventListforAgenda($yearChoice, $monthChoice, $activityChoice)
    {
        $today = new \DateTime();
        $today->setTime(0, 0);

        $stmt = $this->createQueryBuilder('e');
        $stmt->join('e.activity', 'a');
        $stmt->andWhere('e.dateStartAt >= :today');   
        $stmt->setParameter('today', $today);

        if ($yearChoice) {
            $stmt->andwhere('YEAR(e.dateStartAt) = :year');
            $stmt->setParameter('year', $yearChoice);
        }

        if ($monthChoice) {
            $stmt->andwhere('MONTH(e.dateStartAt) = :month');
            $stmt->setParameter('month', $monthChoice);
        }

        if ($activityChoice && $activityChoice != 'all') {
            $stmt->andwhere('e.id = :activityChoice');
            $stmt->setParameter('activityChoice', $monthChoice);
        }

        $stmt->addOrderBy('e.dateStartAt', 'ASC');
        return $stmt->getQuery()->getResult();
    }

    public function getEventListforArchive($yearChoice, $monthChoice, $activityChoice)
    {
        $today = new \DateTime();
        $today->setTime(0, 0);

        $stmt = $this->createQueryBuilder('e');
        $stmt->join('e.activity', 'a');
        $stmt->andWhere('e.dateStartAt < :today');   
        $stmt->setParameter('today', $today);

        if ($yearChoice) {
            $stmt->andwhere('YEAR(e.dateStartAt) = :year');
            $stmt->setParameter('year', $yearChoice);
        }

        if ($monthChoice) {
            $stmt->andwhere('MONTH(e.dateStartAt) = :month');
            $stmt->setParameter('month', $monthChoice);
        }

        if ($activityChoice && $activityChoice != 'all') {
            $stmt->andwhere('e.id = :activityChoice');
            $stmt->setParameter('activityChoice', $monthChoice);
        }

        $stmt->addOrderBy('e.dateStartAt', 'ASC');
        return $stmt->getQuery()->getResult();
    }

}
