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
     * @param Serializer $paginationNormalizer
     * @param string $format
     * @param array $context
     * @return array
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public static function normalizeTransform(
        PaginatedCollection $paginatedCollection,
        Serializer $serializer,
        Serializer $paginationNormalizer,
        string $format,
        array $context
    ): array
    {
        $deserialize_items = $serializer->normalize($paginatedCollection->getItems(), $format, $context);
        $paginatedCollection->setItems($deserialize_items);
        $transformed = $paginationNormalizer->normalize($paginatedCollection);

        return $transformed;
    }
}