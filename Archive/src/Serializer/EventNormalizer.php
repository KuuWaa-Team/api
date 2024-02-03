<?php

namespace App\Serializer;

use App\Entity\Event;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Vich\UploaderBundle\Storage\StorageInterface;

class EventNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'AppEventNormalizerAlreadyCalled';

    private $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return !isset($context[self::ALREADY_CALLED]) && $data instanceof Document;
    }

    /**
     * @param Event $object
     */
    public function normalize(mixed $object, ?string $format = null, array $context = [])
    {
        $object->setShared(true);
        $context[self::ALREADY_CALLED] = true;
        return $this->normalizer->normalize($object, $format, $context);
    }
}
