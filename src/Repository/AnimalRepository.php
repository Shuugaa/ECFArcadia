<?php

namespace App\Repository;

use App\Entity\Animal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Animal>
 */
class AnimalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Animal::class);
    }

    public function findAllAnimals(): array
    {
        $value = "";
        $qb = $this->createQueryBuilder('aAnim')
            ->where('aAnim.prenom != :value')
            ->setParameter('value', $value);

        $query = $qb->getQuery();

        return $query->execute();
    }

    public function findOneOccurence(string $value): int
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT Id FROM animal
        WHERE prenom = :prenom
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['prenom' => $value]);

        return $resultSet->fetchOne();
    }

    public function checkAnimalExists(string $nom): int
    {
        $inted = 0;
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT prenom FROM animal
            WHERE prenom = :prenom
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['prenom' => $nom]);

        if($resultSet->fetchOne()) {
            $inted = 1;
        }
        
        return $inted;
    }

    public function findAnimalWithHabitatById(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * FROM animal
        '; // WHERE habitat_id = :habitatid
        $stmt = $conn->executeQuery($sql);
        //$resultSet = $stmt->executeQuery(['habitatid' => $value]);

        return $stmt->fetchAllAssociative();
    }

    public function findAnimalInHabitatById(int $value): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT prenom
            FROM animal
            WHERE habitat_id = :habitat_id
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['habitat_id' => $value]);

        return $resultSet->fetchAllAssociative();
    }
    //    /**
    //     * @return Animal[] Returns an array of Animal objects
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

    //    public function findOneBySomeField($value): ?Animal
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
