<?php
# api/src/Entity/User.php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\UserController;
use App\Enums\UserValidationStatusEnum;
use App\Enums\WorkSituationEnum;
use App\Filter\RoleFilter;
use App\Traits\EntityIdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use App\State\UserPasswordHasher;
use App\Traits\TimestampTrait;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(),
        new Post(processor: UserPasswordHasher::class),
//        new Get(),
        new Get(
            uriTemplate: '/user/details',
            controller: UserController::class,
            normalizationContext: ['groups' => ['user_read', 'all']],
            denormalizationContext: ['groups' => ['user_write']],
            read: false,
            name: 'user_account',
        ),
        new Put(processor: UserPasswordHasher::class),
        new Patch(processor: UserPasswordHasher::class),
        new Delete(),
        // new Post(
        //     uriTemplate: '/users/{id}',
        //     inputFormats: ['multipart' => ['multipart/form-data']]),
    ],
    normalizationContext: ['groups' => ['user_read', 'all']],
    denormalizationContext: ['groups' => ['user_write']],
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity('email')]
#[ApiFilter(SearchFilter::class, properties: ['validationStatus' => 'exact'])]
#[ApiFilter(RoleFilter::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

  use TimestampTrait;
  use EntityIdTrait;

    public const ROLE_TENANT = 'ROLE_TENANT';
    public const ROLE_AGENCY = 'ROLE_AGENCY';
    public const ROLE_HOMEOWNER = 'ROLE_HOMEOWNER';

    public const ALLOWED_ROLES = [
        self::ROLE_TENANT,
        self::ROLE_HOMEOWNER,
    ];

    #[Assert\NotBlank(groups: ['register'])]
    #[Assert\Email(groups: ['register'])]
    #[Groups(['user_read', 'user_write', 'property_read'])]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private ?string $password = null;

    #[Assert\NotBlank(groups: ['register'])]
    #[Groups(['user_write'])]
    private ?string $plainPassword = null;

    #[ORM\Column(type: 'json')]
    #[Groups(['user_read', 'user_write'])]
    private array $roles = [];

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user_read', 'user_write', 'property_read', 'viewing_read'])]
    #[Assert\NotBlank]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user_read', 'user_write', 'property_read', 'viewing_read'])]
    #[Assert\NotBlank]
    private ?string $lastname = null;

    #[ORM\Column(type: 'string', enumType: WorkSituationEnum::class, nullable: true)]
    #[Groups(['user_read', 'user_write', 'property_read'])]
    private ?WorkSituationEnum $situation = null;

    #[ORM\OneToMany(mappedBy: 'user_document', targetEntity: Document::class, cascade: ['persist'])]
    #[Groups(['user_read', 'user_write'])]
    private ?Collection $documents = null;

    #[ORM\OneToMany(mappedBy: 'agent', targetEntity: Viewing::class)]
    #[Groups(['user_read'])]
    private Collection $viewings;

    #[ORM\OneToMany(mappedBy: 'lodger', targetEntity: Viewing::class)]
    #[Groups(['user_read'])]
    private Collection $visits;

    #[ORM\Column(nullable: true)]
    #[Groups(['user_read', 'user_write', 'property_read'])]
    private ?int $salary = 0;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Property::class)]
    #[Groups(['user_read', 'user_write'])]
    private Collection $properties;

    #[ORM\OneToMany(mappedBy: 'lodger', targetEntity: Request::class)]
    #[Groups(['user_read'])]
    private Collection $requests;

    #[ORM\Column(type: 'boolean', options: ["default" => false])]
    private $isVerified = false;

    #[ORM\OneToMany(mappedBy: 'lodger', targetEntity: Availaibility::class)]
    private Collection $availaibilities;

    #[ORM\Column(type: 'string', enumType: UserValidationStatusEnum::class, options: ["default" => UserValidationStatusEnum::ToComplete])]
    #[Groups(['user_read', 'user_write'])]
    private UserValidationStatusEnum $validationStatus = UserValidationStatusEnum::ToComplete;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
        $this->viewings = new ArrayCollection();
        $this->visits = new ArrayCollection();
        $this->properties = new ArrayCollection();
        $this->requests = new ArrayCollection();
        $this->availaibilities = new ArrayCollection();
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): ?self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getSituation(): ?WorkSituationEnum
    {
        return $this->situation;
    }

    public function setSituation(WorkSituationEnum $situation): self
    {
        $this->situation = $situation;

        return $this;
    }

    /**
     * @return Collection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setUserDocument($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getUserDocument() === $this) {
                $document->setUserDocument(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Viewing>
     */
    public function getViewings(): Collection
    {
        return $this->viewings;
    }

    public function addViewing(Viewing $viewing): self
    {
        if (!$this->viewings->contains($viewing)) {
            $this->viewings->add($viewing);
            $viewing->setAgent($this);
        }

        return $this;
    }

    public function removeViewing(Viewing $viewing): self
    {
        if ($this->viewings->removeElement($viewing)) {
            // set the owning side to null (unless already changed)
            if ($viewing->getAgent() === $this) {
                $viewing->setAgent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Viewing>
     */
    public function getVisits(): Collection
    {
        return $this->visits;
    }

    public function addVisit(Viewing $visit): self
    {
        if (!$this->visits->contains($visit)) {
            $this->visits->add($visit);
            $visit->setLodger($this);
        }

        return $this;
    }

    public function removeVisit(Viewing $visit): self
    {
        if ($this->visits->removeElement($visit)) {
            // set the owning side to null (unless already changed)
            if ($visit->getLodger() === $this) {
                $visit->setLodger(null);
            }
        }

        return $this;
    }

    public function getSalary(): ?int
    {
        return $this->salary;
    }

    public function setSalary(?int $salary): self
    {
        $this->salary = $salary;

        return $this;
    }

    /**
     * @return Collection<int, Property>
     */
    public function getProperties(): Collection
    {
        return $this->properties;
    }

    public function addProperty(Property $property): self
    {
        if (!$this->properties->contains($property)) {
            $this->properties->add($property);
            $property->setOwner($this);
        }

        return $this;
    }

    public function removeProperty(Property $property): self
    {
        if ($this->properties->removeElement($property)) {
            // set the owning side to null (unless already changed)
            if ($property->getOwner() === $this) {
                $property->setOwner(null);
            }
        }

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
            $request->setLodger($this);
        }

        return $this;
    }

    public function removeRequest(Request $request): self
    {
        if ($this->requests->removeElement($request)) {
            // set the owning side to null (unless already changed)
            if ($request->getLodger() === $this) {
                $request->setLodger(null);
            }
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

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
            $availaibility->setLodger($this);
        }

        return $this;
    }

    public function removeAvailaibility(Availaibility $availaibility): self
    {
        if ($this->availaibilities->removeElement($availaibility)) {
            // set the owning side to null (unless already changed)
            if ($availaibility->getLodger() === $this) {
                $availaibility->setLodger(null);
            }
        }

        return $this;
    }

    public function getValidationStatus(): ?UserValidationStatusEnum
    {
        return $this->validationStatus;
    }

    /**
     * Set the value of status
     *
     * @return  self
     */ 
    public function setValidationStatus(UserValidationStatusEnum $validationStatus): self
    {
        $this->validationStatus = $validationStatus;

        return $this;
    }
}
