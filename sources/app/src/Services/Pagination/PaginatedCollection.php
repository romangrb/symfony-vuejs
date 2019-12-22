<?php
/**
 * Created by PhpStorm.
 * User: RomanHrb
 * Date: 26.11.2019
 * Time: 0:48
 */
namespace App\Services\Pagination;

class PaginatedCollection
{
    /** @var $items */
    private $items;

    /** @var $total */
    private $total;

    /** @var $count */
    private $count;

    /** @var $_links */
    private $_links;

    /**
     * Initiate class
     * @param array $items
     * @param $totalItems
     */
    public function __construct(array $items, $totalItems)
    {
        $this->items = $items;
        $this->total = $totalItems;
        $this->count = count($items);
    }

    /**
     * Add link prop
     * @param $ref
     * @param $url
     */
    public function addLink($ref, $url): void
    {
        $this->_links[$ref] = $url;
    }

    /**
     * Get items as array
     *
     * @return array
     */
    public function getItems(): array
    {
        return (array) $this->items;
    }
}
