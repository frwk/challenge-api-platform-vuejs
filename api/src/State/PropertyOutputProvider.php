<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\MediaObject;
use App\Entity\Property;
use App\Dto\PropertyOutput;
use App\Repository\RequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\SecurityBundle\Security;
use Vich\UploaderBundle\Storage\StorageInterface;


class PropertyOutputProvider implements ProviderInterface
{

    public function __construct(
        private readonly ProviderInterface $itemProvider,
        private readonly Security $security,
        private readonly RequestRepository $requestRepository,
        private readonly StorageInterface $storage,
        private readonly FilesystemOperator $usersStorage,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $property = $this->itemProvider->provide($operation, $uriVariables, $context);
        $user = $this->security->getUser();
        $isAlreadyApplied = null !== $this->requestRepository->findOneBy([
            "lodger" => $user,
            "property" => $property
        ]);
        $urlPhotos = array();
        foreach ($property->getPhotos() as $photo){
            $urlPhotos[] = $this->usersStorage->temporaryUrl(
                $this->storage->resolvePath(
                    $this->entityManager->getRepository(MediaObject::class)->find($photo), 'file' ),
                (new \DateTime('now'))->modify('+1 hour')
            );
        }

        return new PropertyOutput(
            $isAlreadyApplied,$property->getTitle(),
            $property->getAddress(), $property->getZipcode(),
            $property->getCity(), $property->getCountry(),
            $property->getDescription(), $property->getType(),
            $property->getRooms(), $property->getSurface(),
            $property->isHasBalcony(), $property->isHasTerrace(),
            $property->isHasCave(), $property->isHasElevator(),
            $property->isHasParking(), $property->getPrice(),
            $property->getHeatType(), $property->isIsFurnished(),
            $property->getState(), $property->getLevel(),
            $urlPhotos
        );
    }
}