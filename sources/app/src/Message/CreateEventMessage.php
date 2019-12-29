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
    private $id;

    private $name;

    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): int
    {
        return $this->id;
    }
}