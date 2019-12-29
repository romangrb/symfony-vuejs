<?php
/**
 * Created by PhpStorm.
 * User: RomanHrb
 * Date: 28.12.2019
 * Time: 18:22
 */

namespace App\Message;

class CreateEventMessage
{
    /**
     * Id
     * @var $id
     */
    private $id;

    /**
     * Event Name
     * @var $name
     */
    private $name;

    /**
     * Initiate class
     *
     * @param int $id
     * @param string $name
     */
    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * Get event id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get event name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}