<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Utilisateur>
 */
class UtilisateurRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Utilisateur) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }


    public function findOneOccurence(string $test): int
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT Id FROM utilisateur
        WHERE username = :username
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['username' => $test]);

        return $resultSet->fetchOne();
    }

    public function checkUsernameExists(string $username): int
    {
        $inted = 0;
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT username FROM utilisateur
            WHERE username = :username
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['username' => $username]);

        if($resultSet->fetchOne()) {
            $inted = 1;
        }
        
        return $inted;
    }

    public function findPassword(string $test): string
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT password FROM utilisateur
        WHERE username = :username
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['username' => $test]);

        return $resultSet->fetchOne();
    }

    public function findAllUsers(): array
    {
        $test = "";
        $qb = $this->createQueryBuilder('aUser')
            ->where('aUser.username != :value')
            ->setParameter('value', $test);
        
            $query = $qb->getQuery();

            return $query->execute();
    }

    public function loadUtilisateurByIdentifier(string $username): ?Utilisateur
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->createQuery(
                'SELECT *
                FROM utilisateur
                WHERE username = :query'
            )
            ->setParameter('query', $username)
            ->getOneOrNullResult();
    }
    //public function findOneBySomeField($value): ?Utilisateur
    //{
    //    return $this->createQueryBuilder('s')
    //        ->andWhere('s.username = :value')
    //        ->getQuery()
    //        ->getOneOrNullResult()
    //    ;
    //}
    //    /**
    //     * @return Utilisateur[] Returns an array of Utilisateur objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Utilisateur
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
