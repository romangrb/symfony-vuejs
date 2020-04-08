<?php

namespace App\Repository;

use App\Entity\PlaceLocations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PlaceLocations|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlaceLocations|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlaceLocations[]    findAll()
 * @method PlaceLocations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaceLocationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlaceLocations::class);
    }

    // /**
    //  * @return PlaceLocations[] Returns an array of PlaceLocations objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PlaceLocations
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
