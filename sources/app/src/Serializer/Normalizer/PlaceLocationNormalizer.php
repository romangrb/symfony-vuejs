<?php declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: RomanHrb
 * Date: 06.01.2020
 * Time: 12:49
 */
namespace App\Serializer\Normalizer;

use App\Entity\Place;
use App\Entity\PlaceLocation;
use Carbon\Carbon;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PlaceLocationNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $data =  [
            'lat' => $object->getLat(),
            'lng' => $object->getLng()
        ];

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof PlaceLocation;
    }
}