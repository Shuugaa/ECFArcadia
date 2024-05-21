<?php

namespace App\Repository;

use App\Entity\Habitat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Habitat>
 */
class HabitatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Habitat::class);
    }

    public function findAllHabitat(): array
    {
        $value = "";
        $qb = $this->createQueryBuilder('aHab')
            ->where('aHab.nom != :value')
            ->setParameter('value', $value);

        $query = $qb->getQuery();

        return $query->execute();
    }

    public function findOneOccurence(string $value): int
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT Id FROM habitat
        WHERE nom = :nom
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['nom' => $value]);

        return $resultSet->fetchOne();
    }

    public function checkHabitatExists(string $nom): int
    {
        $inted = 0;
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT nom FROM habitat
            WHERE nom = :nom
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['nom' => $nom]);

        if($resultSet->fetchOne()) {
            $inted = 1;
        }
        
        return $inted;
    }

    public function findOneBySomeField(string $hab): ?Habitat
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.nom = :val')
            ->setParameter('val', $hab)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    //    /**
    //     * @return Habitat[] Returns an array of Habitat objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('h.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Habitat
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
