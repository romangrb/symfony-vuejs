<?php

namespace App\Repository;

use App\Entity\PlaceAttachments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PlaceAttachments|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlaceAttachments|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlaceAttachments[]    findAll()
 * @method PlaceAttachments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaceAttachmentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlaceAttachments::class);
    }

    // /**
    //  * @return PlaceAttachments[] Returns an array of PlaceAttachments objects
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
    public function findOneBySomeField($value): ?PlaceAttachments
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
