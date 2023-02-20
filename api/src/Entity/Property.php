<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\PropertyController;
use App\Interfaces\PropertyInterface;
use App\Repository\PropertyRepository;
use App\Traits\EntityIdTrait;
use App\Traits\TimestampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    normalizationContext: ['groups' => ['property_read', 'all',]],
    denormalizationContext: ['groups' => ['property_write']],
)]
#[Get]
#[GetCollection]
#[GetCollection(
    uriTemplate: '/property/by_tenant',
    controller: PropertyController::class,
    normalizationContext: ['groups' => ['property_read', 'all',]],
    denormalizationContext: ['groups' => ['property_write']],
)]
#[Post]
#[Put(security: "is_granted('ROLE_AGENCY') or object.getOwner() == user")]
#[ORM\Entity(repositoryClass: PropertyRepository::class)]
class Property implements PropertyInterface
{
    const ITEMS_PER_PAGE = 10;
    use TimestampTrait;
    use EntityIdTrait;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['property_read', 'property_write', 'availaibility_read', 'viewing_read'])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups(['property_read', 'property_write'])]
    private ?string $address = null;

    #[ORM\Column(length: 5)]
    #[Groups(['property_read', 'property_write'])]
    private ?string $zipcode = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['property_read', 'property_write'])]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    #[Groups(['property_read', 'property_write'])]
    private ?string $country = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['property_read', 'property_write'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(['property_read', 'property_write'])]
    private ?string $type = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['property_read', 'property_write'])]
    private ?int $rooms = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Groups(['property_read', 'property_write'])]
    private ?int $surface = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['property_read', 'property_write'])]
    private ?bool $has_balcony = false;

    #[ORM\Column(nullable: true)]
    #[Groups(['property_read', 'property_write'])]
    private ?bool $has_terrace = false;

    #[ORM\Column(nullable: true)]
    #[Groups(['property_read', 'property_write'])]
    private ?bool $has_cave = false;

    #[ORM\Column(nullable: true)]
    #[Groups(['property_read', 'property_write'])]
    private ?bool $has_elevator = false;

    #[ORM\Column(nullable: true)]
    #[Groups(['property_read', 'property_write'])]
    private ?bool $has_parking = null;

    #[ORM\Column]
    #[Groups(['property_read', 'property_write'])]
    private ?int $price = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['property_read', 'property_write'])]
    private ?string $heat_type = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['property_read', 'property_write'])]
    private ?bool $is_furnished = false;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['property_read', 'property_write'])]
    private ?string $state = null;

    #[ORM\OneToMany(mappedBy: 'property', targetEntity: Availaibility::class)]
    private Collection $availaibilities;

    #[ORM\ManyToOne(inversedBy: 'properties')]
    #[Groups(['property_read', 'property_write', 'viewing_read'])]
    private ?User $owner = null;

    #[ORM\OneToMany(mappedBy: 'property', targetEntity: Request::class)]
    #[Groups(['property_read'])]
    private Collection $requests;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $level = null;

    #[ORM\OneToMany(mappedBy: 'property', targetEntity: MediaObject::class)]
    #[Groups(['property_read', 'property_write'])]
    private Collection $photos;

    public function __construct()
    {
        $this->availaibilities = new ArrayCollection();
        $this->requests = new ArrayCollection();
        $this->photos = new ArrayCollection();
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(?int $rooms): self
    {
        $this->rooms = $rooms;

        return $this;
    }

    public function getSurface(): ?int
    {
        return $this->surface;
    }

    public function setSurface(?int $surface): self
    {
        $this->surface = $surface;

        return $this;
    }

    public function isHasBalcony(): ?bool
    {
        return $this->has_balcony;
    }

    public function setHasBalcony(?bool $has_balcony): self
    {
        $this->has_balcony = $has_balcony;

        return $this;
    }

    public function isHasTerrace(): ?bool
    {
        return $this->has_terrace;
    }

    public function setHasTerrace(?bool $has_terrace): self
    {
        $this->has_terrace = $has_terrace;

        return $this;
    }

    public function isHasCave(): ?bool
    {
        return $this->has_cave;
    }

    public function setHasCave(?bool $has_cave): self
    {
        $this->has_cave = $has_cave;

        return $this;
    }

    public function isHasElevator(): ?bool
    {
        return $this->has_elevator;
    }

    public function setHasElevator(?bool $has_elevator): self
    {
        $this->has_elevator = $has_elevator;

        return $this;
    }

    public function isHasParking(): ?bool
    {
        return $this->has_parking;
    }

    public function setHasParking(?bool $has_parking): self
    {
        $this->has_parking = $has_parking;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getHeatType(): ?string
    {
        return $this->heat_type;
    }

    public function setHeatType(?string $heat_type): self
    {
        $this->heat_type = $heat_type;

        return $this;
    }

    public function isIsFurnished(): ?bool
    {
        return $this->is_furnished;
    }

    public function setIsFurnished(?bool $is_furnished): self
    {
        $this->is_furnished = $is_furnished;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return Collection<int, Availaibility>
     */
    public function getAvailaibilities(): Collection
    {
        return $this->availaibilities;
    }

    public function addAvailaibility(Availaibility $availaibility): self
    {
        if (!$this->availaibilities->contains($availaibility)) {
            $this->availaibilities->add($availaibility);
            $availaibility->setProperty($this);
        }

        return $this;
    }

    public function removeAvailaibility(Availaibility $availaibility): self
    {
        if ($this->availaibilities->removeElement($availaibility)) {
            // set the owning side to null (unless already changed)
            if ($availaibility->getProperty() === $this) {
                $availaibility->setProperty(null);
            }
        }

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, Request>
     */
    public function getRequests(): Collection
    {
        return $this->requests;
    }

    public function addRequest(Request $request): self
    {
        if (!$this->requests->contains($request)) {
            $this->requests->add($request);
            $request->setProperty($this);
        }

        return $this;
    }

    public function removeRequest(Request $request): self
    {
        if ($this->requests->removeElement($request)) {
            // set the owning side to null (unless already changed)
            if ($request->getProperty() === $this) {
                $request->setProperty(null);
            }
        }

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(?int $level): self
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return Collection<int, MediaObject>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(MediaObject $photo): self
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->setProperty($this);
        }

        return $this;
    }

    public function removePhoto(MediaObject $photo): self
    {
        if ($this->photos->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getProperty() === $this) {
                $photo->setProperty(null);
            }
        }

        return $this;
    }
}
