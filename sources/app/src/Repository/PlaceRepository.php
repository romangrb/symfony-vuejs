<?php

namespace App\Repository;

use App\Entity\Place;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method Place|null find($id, $lockMode = null, $lockVersion = null)
 * @method Place|null findOneBy(array $criteria, array $orderBy = null)
 * @method Place[]    findAll()
 * @method Place[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Place::class);
    }

    /**
     * Get events with files, search by name, or tag
     *
     * @param Request $request
     * @return QueryBuilder
     */
    public function getPlacesWithSearchBuilder(Request $request): QueryBuilder
    {
        $name = $request->get('name');
        $description = $request->get('description');
        $order = $request->get('order') ? 'ASC' : 'DESC';

        $val = $name ?? $description;

        $qb = $this->createQueryBuilder('p');

        $qb->where(
            $qb->expr()->like('p.name', ':value')
        )->orWhere(
            $qb->expr()->like('p.description', ':value')
        )
        ->setParameter('value',"%$val%")
        ;

        return $qb
            ->orderBy('p.id', $order)
            ;
    }
}
