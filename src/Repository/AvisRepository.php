<?php

namespace App\Repository;

use App\Entity\Avis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Avis>
 */
class AvisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avis::class);
    }

    public function findAllAvis(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * FROM avis
        ';
        $stmt = $conn->executeQuery($sql);

        return $stmt->fetchAllAssociative();
    }

    public function findAllAvisWhereVisible(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT *
            FROM avis 
            WHERE is_visible = :value
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['value' => TRUE]);

        return $resultSet->fetchAllAssociative();
    }

    public function findOccurence(int $id): bool
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT id FROM avis WHERE id = :idAT AND is_visible = 0
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['idAT' => $id]);
        $resultat = $resultSet->fetchOne();

        if($resultat == $id)
        {
            return TRUE;
        }
        return FALSE;
    }

//    Je n'arrive pas a renvoyer App\Entity\Avis
//    public function findOccurenceReturnEntity(int $id): ?Avis
//    {
//        $conn = $this->getEntityManager()->getConnection();
//
//        $sql = '
//            SELECT * FROM avis WHERE id = :idAT
//        ';
//        $stmt = $conn->prepare($sql);
//        $resultSet = $stmt->executeQuery(['idAT' => $id]);
//
//        return $resultSet;
//    }

    public function findOneBySomeField(int $value): ?Avis
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    //    /**
    //     * @return Avis[] Returns an array of Avis objects
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

    //    public function findOneBySomeField($value): ?Avis
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
