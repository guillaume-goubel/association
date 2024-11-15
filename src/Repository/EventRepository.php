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

    public function getEventListforAdmin(string $yearChoice, string $monthChoice, string $creatorChoice, string $activityChoice, string $dateChoice)
    {
        $stmt = $this->createQueryBuilder('e');
        $stmt->join('e.activity', 'a');

        if ($yearChoice) {
            $stmt->andwhere('YEAR(e.dateStartAt) = :year');
            $stmt->setParameter('year', $yearChoice);
        }

        if ($monthChoice && $monthChoice != 'all') {
            $stmt->andwhere('MONTH(e.dateStartAt) = :month');
            $stmt->setParameter('month', $monthChoice);
        }

        if ($creatorChoice && $creatorChoice != 'all') {
            $stmt->andwhere('e.user = :user');
            $stmt->setParameter('user', $creatorChoice);
        }

        if ($activityChoice && $activityChoice != 'all') {
            $stmt->andwhere('a.id = :activityChoice');
            $stmt->setParameter('activityChoice', $activityChoice);
        }


        if ($dateChoice == 'dateStartAt') {
            $stmt->addOrderBy('e.dateStartAt', 'ASC');
        }else{
            $stmt->addOrderBy('e.createdAt', 'ASC');
        }

        return $stmt->getQuery();
    }

    public function findLastEventsforAdmin(int $limit): array
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.createdAt', 'ASC') 
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getEventListforAgenda(string $yearChoice, string $monthChoice, string $activityChoice)
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

        if ($monthChoice && $monthChoice != 'all') {
            $stmt->andwhere('MONTH(e.dateStartAt) = :month');
            $stmt->setParameter('month', $monthChoice);
        }

        if ($activityChoice && $activityChoice != 'all') {
            $stmt->andwhere('a.id = :activityChoice');
            $stmt->setParameter('activityChoice', $activityChoice);
        }

        $stmt->addOrderBy('e.dateStartAt', 'ASC');
        return $stmt->getQuery()->getResult();
    }

    public function getEventListforArchive(string $yearChoice, string $monthChoice, string $activityChoice)
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
            $stmt->andwhere('a.id = :activityChoice');
            $stmt->setParameter('activityChoice', $activityChoice);
        }

        $stmt->addOrderBy('e.dateStartAt', 'ASC');
        return $stmt->getQuery()->getResult();
    }

    public function getEventListforCalendar(string $yearChoice, string $activityChoice)
    {
        $stmt = $this->createQueryBuilder('e');
        $stmt->join('e.activity', 'a');

        if ($yearChoice) {
            $stmt->andwhere('YEAR(e.dateStartAt) = :year');
            $stmt->setParameter('year', $yearChoice);
        }
        
        if ($activityChoice && $activityChoice != 'all') {
            $stmt->andwhere('a.id = :activityChoice');
            $stmt->setParameter('activityChoice', $activityChoice);
        }

        $stmt->addOrderBy('e.dateStartAt', 'ASC');
        return $stmt->getQuery()->getResult();
    }

    public function getEventListforCalendarFor12Months($activityChoice = null, string $userChoice, string $yearChoice)
    {

        // Création d'un objet QueryBuilder
        $stmt = $this->createQueryBuilder('e');
        $stmt->join('e.activity', 'a');
        
        if ($yearChoice == 'yearDepth') {
            $yearDefault = (new \DateTime())->format('Y');
            $monthDefault = (new \DateTime())->format('m');
            $startDate = new \DateTime("{$yearDefault}-{$monthDefault}-01");
            $endDate = (clone $startDate)->modify('+12 months')->modify('-1 day'); // Dernier jour de la période de 12 mois
            
            // Filtrage par la plage de dates
            $stmt->andWhere('e.dateStartAt BETWEEN :startDate AND :endDate')
                ->setParameter('startDate', $startDate)
                ->setParameter('endDate', $endDate);
        }else{
            $stmt->andwhere('YEAR(e.dateStartAt) = :year');
            $stmt->setParameter('year', $yearChoice);
        }

        if ($activityChoice && $activityChoice !== 'all') {
            $stmt->andWhere('a.id = :activityChoice')
                ->setParameter('activityChoice', $activityChoice);
        }

        if ($userChoice && $userChoice !== 'all') {
            $stmt->andWhere('e.user = :userChoice')
                ->setParameter('userChoice', $userChoice);
        }
        
        // Tri des résultats par la date de début des événements
        $stmt->addOrderBy('e.dateStartAt', 'ASC');
        
        // Retourne les résultats
        return $stmt->getQuery()->getResult();
    }

    public function getDistincYearCreatedAt()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "    SELECT DISTINCT(YEAR(date_start_at)) as year
                    FROM `event`
                    WHERE DATE(date_start_at) >= DATE(NOW())
                    ORDER BY year ASC
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

        $sql = "    SELECT U.id AS user_id, CONCAT(UPPER(U.last_name), ' ', U.first_name) AS creator
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

}
