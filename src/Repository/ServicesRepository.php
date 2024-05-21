<?php

namespace App\Repository;

use App\Entity\Services;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Services>
 */
class ServicesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Services::class);
    }

    public function findAllServices(): array
    {
        $value = "";
        $qb = $this->createQueryBuilder('aServ')
            ->where('aServ.nom != :value')
            ->setParameter('value', $value);

        $query = $qb->getQuery();

        return $query->execute();
    }

    public function findOneOccurence(string $value): int
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT Id FROM services
        WHERE nom = :nom
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['nom' => $value]);

        return $resultSet->fetchOne();
    }

    public function checkServicesExists(string $nom): int
    {
        $inted = 0;
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT nom FROM services
            WHERE nom = :nom
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['nom' => $nom]);

        if($resultSet->fetchOne()) {
            $inted = 1;
        }
        
        return $inted;
    }
    //    /**
    //     * @return Services[] Returns an array of Services objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Services
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
