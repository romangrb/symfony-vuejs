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
        $page = $request->query->get('page', 1);
        $adapter = new DoctrineORMAdapter($qb);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($max_page);
        $pagerfanta->setCurrentPage($page);
        $programmers = [];
        foreach ($pagerfanta->getCurrentPageResults() as $result) {
            $programmers[] = $result;
        }
        $paginatedCollection = new PaginatedCollection($programmers, $pagerfanta->getNbResults());
        $createLinkUrl = function($targetPage) use ($route, $routeParams) {
            return $this->router->generate($route, array_merge(
                $routeParams,
                array('page' => $targetPage)
            ));
        };
        $paginatedCollection->addLink('current', $createLinkUrl($page));
        $paginatedCollection->addLink('first', $createLinkUrl(1));
        $paginatedCollection->addLink('last', $createLinkUrl($pagerfanta->getNbPages()));
        if ($pagerfanta->hasNextPage()) {
            $paginatedCollection->addLink('next', $createLinkUrl($pagerfanta->getNextPage()));
        }
        if ($pagerfanta->hasPreviousPage()) {
            $paginatedCollection->addLink('prev', $createLinkUrl($pagerfanta->getPreviousPage()));
        }
        return $paginatedCollection;
    }
}
