<?php

namespace App\Repository;

use App\Entity\Rapports;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rapports>
 */
class RapportsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rapports::class);
    }

    public function findAllNourriture(): array
    {
        $value = "";
        $qb = $this->createQueryBuilder('aAnim')
            ->where('aAnim.nourriture != :value')
            ->setParameter('value', $value);

        $query = $qb->getQuery();

        return $query->execute();
    }

    public function findAllRapportsVet(): array
    {
        $value = "";
        $qb = $this->createQueryBuilder('aAnim')
            ->where('aAnim.detail != :value')
            ->setParameter('value', $value);

        $query = $qb->getQuery();

        return $query->execute();
    }
    //    /**
    //     * @return Rapports[] Returns an array of Rapports objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Rapports
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
