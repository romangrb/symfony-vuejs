<?php

namespace App\Repository;

use App\Entity\PlaceContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PlaceContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlaceContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlaceContent[]    findAll()
 * @method PlaceContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaceContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlaceContent::class);
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


    /**
     * Get place from location
     *
     * @param float $lat
     * @param float $lng
     * @return PlaceContent
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPlaceContentByLocation(float $lat, float $lng): PlaceContent
    {
        $qb = $this->createQueryBuilder('pc');

        return $qb->select('pc')
            ->join('pc.place', 'p')
            ->join('p.placeLocation', 'pl')
            ->where('pl.lat = :lat')
            ->andWhere('pl.lng = :lng')
            ->setParameter('lat', $lat)
            ->setParameter('lng', $lng)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }
}
