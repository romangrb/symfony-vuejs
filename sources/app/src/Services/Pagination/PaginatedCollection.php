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

    /** @var $per_page */
    private $per_page;

    /** @var $count */
    private $count;

    /** @var $_links */
    private $_links;

    /**
     * Initiate class
     *
     * @param array $items
     * @param $totalItems
     * @param $per_page
     */
    public function __construct(array $items, $totalItems, $per_page)
    {
        $this->items = $items;
        $this->per_page = $per_page;
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
     * Remove link prop
     *
     * @param $ref
     */
    public function removeLink($ref): void
    {
        unset($this->_links[$ref]);
    }

    /**
     * Set per_page
     *
     * @return int
     */
    public function getPerPage(): int
    {
        return (int) $this->per_page;
    }

    /**
     * Set per_page
     *
     * @param int $per_page
     * @return int
     */
    public function setPerPage(int $per_page): int
    {
        return $this->per_page = $per_page;
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
     * Set Total items count
     *
     * @param $total
     * @return void
     */
    public function setTotal(int $total = 0): void
    {
        $this->total = $total;
    }

    /**
     * Get Integer
     *
     * @return int
     */
    public function getTotal(): int
    {
        return (int) $this->total;
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
