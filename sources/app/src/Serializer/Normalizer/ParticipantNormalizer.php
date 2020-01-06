<?php declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: RomanHrb
 * Date: 06.01.2020
 * Time: 12:49
 */
namespace App\Serializer\Normalizer;

use App\Entity\EventParticipant;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;

class ParticipantNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        if (isset($context['UserNormalizer'])){
            return (new Serializer([$context['UserNormalizer']]))->normalize($object->getUser());
        } else {
            return (new Serializer([new UserNormalizer]))->normalize($object->getUser());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof EventParticipant;
    }
}