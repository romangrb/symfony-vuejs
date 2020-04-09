<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: RomanHrb
 * Date: 09.04.2020
 * Time: 15:53
 */

namespace App\Utils;


class CircularReferenceHandler
{
    public function __invoke($object)
    {
        return $object->getId();
    }
}