<?php

namespace App\Repository;

use DateTime;
use DateInterval;
use App\Entity\Question;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    public function findByTag($tag)
    {
        return $this->createQueryBuilder('q')
            ->innerJoin('q.tags', 't')
            ->andWhere('t = :tag')
            ->andWhere('q.isBlocked = false')
            ->setParameter('tag', $tag)
            ->getQuery()
            ->getResult();
    }

    /**
     * version @Aurélien
     * une autre partie du code fait le setActive(false)
     * 
     * @return Question[] Returns an array of Question objects
     */
    public function findUpdatedMoreThanOneWeek($date): ?array
    {
        return $this->createQueryBuilder('q')
            ->where('q.updatedAt < :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }

    /**
     * Version @Randy
     * une autre partie du code fait le setActive(false)
     */
    public function findDeactivable()
    {
        $now = new DateTime();
        $date = $now->sub(new DateInterval("P7D"));

        return $this->createQueryBuilder('q')
            ->where("q.active = :active")
            ->andWhere("q.updatedAt <= :date")
            ->setParameter("active", true)
            ->setParameter("date", $date)
            ->getQuery()
            ->getResult();
    }

    /**
     * Désactive les questions
     */
    public function deactivateOutdated(): int
    {
        // on récupére la connexion "directe" à la base (DBAL => PDO)
        // on n'utilise pas l'ORM Doctrine
        // @see https://symfony.com/doc/5.4/doctrine.html#querying-with-sql
        $conn = $this->getEntityManager()->getConnection();

        // la requête SQL d'UPDATE
        // => on met à jour sans faire d'aller-retour avec PHP
        $sql = '
            UPDATE `question`
            SET active=0
            -- Date de mise à jour > 7 jours
            -- Différence enter maintenant et la date de mise à jour
            WHERE DATEDIFF(NOW(), updated_at) > 7
            -- ORDER BY `updated_at` ASC
        ';

        // on éxécute la requête
        $result = $conn->executeQuery($sql);

        // nombre de lignes affectées
        return $result->rowCount();
    }

    //    /**
//     * @return Question[] Returns an array of Question objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Question
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
