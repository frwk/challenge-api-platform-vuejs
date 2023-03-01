<?php

namespace App\Serializer;

use App\Entity\MediaObject;
use App\Entity\Property;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

class PropertyNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;
    private const ALREADY_CALLED = 'MEDIA_OBJECT_NORMALIZER_ALREADY_CALLED';

    public function __construct(private readonly StorageInterface $storage, private readonly FilesystemOperator $usersStorage, private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof Property;
    }

    /**
     * @inheritDoc
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $context[self::ALREADY_CALLED] = true;
        $tmp = array();
        foreach ($object->getPhotos() as $photo){
            $tmp[] = $this->usersStorage->temporaryUrl(
                $this->storage->resolvePath(
                    $this->entityManager->getRepository(MediaObject::class)->find($photo), 'file' ),
                (new \DateTime('now'))->modify('+1 hour')
            );
        }
        $object->setPhotosUrl($tmp);
        return $this->normalizer->normalize($object, $format, $context);
    }
}