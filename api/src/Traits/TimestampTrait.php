<?php

namespace App\Traits;

use App\Helpers\DateFormatterHelper;
use Gedmo\Mapping\Annotation\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait TimestampTrait
{
    #[ORM\Column]
    #[Timestampable]
    #[Groups(['all_timestamp'])]
    private ?\DateTimeImmutable $created_at = null;


    #[ORM\Column]
    #[Timestampable]
    #[Groups(['all_timestamp'])]
    private ?\DateTimeImmutable $updated_at = null;

    /**
     * @return \DateTimeImmutable|null
     */
    public function getCreatedAt(): \DateTimeImmutable|string
    {
        return DateFormatterHelper::dateTimeToString($this->created_at);
    }

    /**
     * @param \DateTimeImmutable|null $created_at
     */
    public function setCreatedAt(?\DateTimeImmutable $created_at): void
    {
        $this->created_at = $created_at;
    }


    /**
     * @return \DateTimeImmutable|null
     */
    public function getUpdatedAt(): \DateTimeImmutable|string
    {
        return DateFormatterHelper::dateTimeToString($this->updated_at);
    }

    /**
     * @param \DateTimeImmutable|null $updated_at
     */
    public function setUpdatedAt(?\DateTimeImmutable $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

}
