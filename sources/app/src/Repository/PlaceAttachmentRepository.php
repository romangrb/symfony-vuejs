<?php

namespace App\Repository;

use App\Entity\PlaceAttachment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PlaceAttachment|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlaceAttachment|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlaceAttachment[]    findAll()
 * @method PlaceAttachment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaceAttachmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlaceAttachment::class);
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
