<?php declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: RomanHrb
 * Date: 06.01.2020
 * Time: 12:49
 */
namespace App\Serializer\Normalizer;

use App\Entity\Event;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;

class EventNormalizer implements NormalizerInterface
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
            'created_at' => $object->getCreatedAt()->format('Y-m-d H:i:s'),
            'time_remain' => $object->getTimeRemain(),
        ];

        if (isset($context['ParticipantNormalizer'])){
            $data['participants'] = (new Serializer([$context['ParticipantNormalizer']]))->normalize($object->getEventParticipants(), $format, $context);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Event;
    }
}