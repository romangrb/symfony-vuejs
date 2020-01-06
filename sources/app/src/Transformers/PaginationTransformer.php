<?php declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: RomanHrb
 * Date: 06.01.2020
 * Time: 12:49
 */
namespace App\Transformers;

use App\Services\Pagination\PaginatedCollection;
use Symfony\Component\Serializer\Serializer;

class PaginationTransformer
{
    /**
     * Transforms an object to an array.
     *
     * @param PaginatedCollection $paginatedCollection
     * @param Serializer $serializer
     * @param string $format
     * @param Serializer $paginationNormalizer
     * @return array
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public static function transform(PaginatedCollection $paginatedCollection, Serializer $serializer, Serializer $paginationNormalizer, string $format): array
    {
        $deserialize_items = $serializer->normalize($paginatedCollection->getItems(), $format);
        $paginatedCollection->setItems($deserialize_items);
        $transformed = $paginationNormalizer->normalize($paginatedCollection);

        return $transformed;
    }
}