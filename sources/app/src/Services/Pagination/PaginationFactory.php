<?php
/**
 * Created by PhpStorm.
 * User: RomanHrb
 * Date: 26.11.2019
 * Time: 0:48
 */
namespace App\Services\Pagination;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Routing\RouterInterface;

class PaginationFactory
{
    /**
     * Max per page
     */
    const MAX_PER_PAGE = 10;

    /** @var $router */
    private $router;

    /**
     * Initiate class params
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * Create collection
     * @param QueryBuilder $qb
     * @param Request $request
     * @param int $max_page
     * @param $route
     * @param array $routeParams
     * @return PaginatedCollection
     */
    public function createCollection(QueryBuilder $qb, Request $request, $route, array $routeParams = array(), int $max_page = self::MAX_PER_PAGE): PaginatedCollection
    {
        $page = (int) $request->query->get('page', 1);
        $adapter = new DoctrineORMAdapter($qb);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($max_page);
        $pagerfanta->setCurrentPage($page);

        $programmers = [];
        foreach ($pagerfanta->getCurrentPageResults() as $result) {
            $programmers[] = $result;
        }
        $paginatedCollection = new PaginatedCollection($programmers, $pagerfanta->getNbResults());

        $paginatedCollection->addLink('current', $page);
        $paginatedCollection->addLink('first', 1);
        $paginatedCollection->addLink('last', $pagerfanta->getNbPages());
        if ($pagerfanta->hasNextPage()) {
            $paginatedCollection->addLink('next', $pagerfanta->getNextPage());
        }
        if ($pagerfanta->hasPreviousPage()) {
            $paginatedCollection->addLink('prev', $pagerfanta->getPreviousPage());
        }
        return $paginatedCollection;
    }
}
