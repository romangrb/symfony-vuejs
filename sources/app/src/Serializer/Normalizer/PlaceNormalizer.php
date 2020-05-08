<?php declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: RomanHrb
 * Date: 06.01.2020
 * Time: 12:49
 */
namespace App\Serializer\Normalizer;

use App\Entity\Place;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PlaceNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $data =  [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'description' => $object->getDescription(),
            'updated_at' => $object->getUpdatedAt()->format("Y-m-d\TH:i:s.000\Z"),
        ];

        if (isset($context['PlaceLocation'])){
            $data['place_location'] = (new Serializer([$context['PlaceLocation']]))
                ->normalize($object->getPlaceLocation(), $format, $context);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Place;
    }
}