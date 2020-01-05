<?php
/**
 * Created by PhpStorm.
 * User: RomanHrb
 * Date: 28.12.2019
 * Time: 18:22
 */

namespace App\Message;

class TestMessage
{
    /**
     * Content
     *
     * @var $content
     */
    private $content;

    /**
     * Initiate class
     *
     * @param string $content
     */
    public function __construct(string $content)
    {
        $this->content = $content;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }
}