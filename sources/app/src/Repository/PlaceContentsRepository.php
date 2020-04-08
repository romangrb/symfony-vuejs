<?php

namespace App\Repository;

use App\Entity\PlaceContents;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PlaceContents|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlaceContents|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlaceContents[]    findAll()
 * @method PlaceContents[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaceContentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlaceContents::class);
    }

    // /**
    //  * @return PlaceContents[] Returns an array of PlaceContents objects
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
    public function findOneBySomeField($value): ?PlaceContents
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
