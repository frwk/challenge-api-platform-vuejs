<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\CreateDocumentAction;
use App\Controller\CreateMediaObjectAction;
use App\Enums\DocumentStatusEnum;
use App\Repository\DocumentRepository;
use Symfony\Component\HttpFoundation\File\File;
use App\Traits\EntityIdTrait;
use App\Traits\TimestampTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    types: ['https://schema.org/MediaObject'],
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            controller: CreateDocumentAction::class,
            openapiContext: [
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => [
                                        'type' => 'string',
                                        'format' => 'binary'
                                    ],
                                    'name'=> [
                                        'type' => 'string',
                                    ],
                                    'type'=> [
                                        'type' => 'string',
                                    ],
                                    'user_id'=> [
                                        'type' => 'string',
                                    ],
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            validationContext: ['groups' => ['Default', 'media_object_create']],
            deserialize: false
        )
    ],
    normalizationContext: ['groups' => ['document_read']],
    denormalizationContext: ['groups' => ['document_write']]
)]
#[ApiFilter(SearchFilter::class, properties: ['user_document' => 'exact'])]
#[Put(security: "is_granted('ROLE_AGENCY') or object.getUserDocument() == user")]
#[Vich\Uploadable]
#[ORM\Entity]
class Document
{
    use TimestampTrait;
    use EntityIdTrait;

    #[ApiProperty(types: ['https://schema.org/contentUrl'])]
    #[Groups(['document_read', 'user_read'])]
    public ?string $contentUrl = null;

    #[Vich\UploadableField(mapping: "media_object", fileNameProperty: "filePath")]
    #[Assert\NotNull(groups: ['document_write'])]
    public ?File $file = null;

    #[ORM\Column(nullable: true)]
    public ?string $filePath = null;


    #[ORM\Column(length: 255)]
    #[Groups(['document_read', 'document_write', 'user_write', 'user_read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['document_read', 'document_write', 'user_read'])]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    #[Groups(['document_read', 'document_write'])]
    private ?User $user_document = null;

    #[ORM\Column(type: 'boolean', options: ["default" => false])]
    #[Groups(['document_read', 'document_write', 'user_read', 'user_write'])]
    private $isValid = false;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getUserDocument(): ?User
    {
        return $this->user_document;
    }

    public function setUserDocument(?User $user_document): self
    {
        $this->user_document = $user_document;

        return $this;
    }

    public function getFileDocument(): ?File
    {
        return $this->file;
    }
    public function setFileDocument(?File $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $file
     */
    public function setFile(?File $file = null): self
    {
        $this->file = $file;

        if (null !== $file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated_at = new \DateTimeImmutable();
        }
        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }


    // /**
    //  * Get the value of status
    //  */ 
    // public function getStatus(): ?DocumentStatusEnum
    // {
    //     return $this->status;
    // }

    // /**
    //  * Set the value of status
    //  *
    //  * @return  self
    //  */ 
    // public function setStatus(DocumentStatusEnum $status): self
    // {
    //     $this->status = $status;

    //     return $this;
    // }

    public function getIsValid(): ?bool
    {
        return $this->isValid;
    }

    public function setIsValid(bool $isValid): self
    {
        $this->isValid = $isValid;

        return $this;
    }
}
