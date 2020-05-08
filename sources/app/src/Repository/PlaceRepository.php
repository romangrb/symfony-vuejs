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
        $order_by = (bool) $request->get('order_by') ? 'DESC' : 'ASC';
        $order_type = $request->get('order_type');
        $search_type = $request->get('search_type');
        $search_value = $request->get('search_value');

        switch ($search_type){
            case 'Description':
                $search_by_type = 'p.description';
                break;
            default:
                $search_by_type = 'p.name';
                break;
        }

        switch ($order_type){
            case 'name':
                $order_by_val = 'p.name';
                break;
            case 'updated_at':
                $order_by_val = 'p.updatedAt';
                break;
            default:
                $order_by_val = 'p.id';
                break;
        }

        $qb = $this->createQueryBuilder('p');
        $qb->where($qb->expr()->like($search_by_type, ':value'))
            ->setParameter('value',"%$search_value%")
            ->select('p, pl')
            ->leftJoin('p.placeLocation', 'pl')
        ;

        return $qb
            ->orderBy($order_by_val, $order_by)
            ;
    }
}
