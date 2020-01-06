<?php declare(strict_types=1);
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
     *
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

    /**
     * Get items as array
     *
     * @param $items
     * @return void
     */
    public function setItems($items): void
    {
        $this->items = $items;
    }

    /**
     * Get Integer
     *
     * @return int
     */
    public function getTotal(): int
    {
        return (int) $this->items;
    }

    /**
     * Get Count
     *
     * @return int
     */
    public function getCount(): int
    {
        return (int) $this->count;
    }

    /**
     * Get Links
     *
     * @return array
     */
    public function getLinks(): array
    {
        return (array) $this->_links;
    }
}
