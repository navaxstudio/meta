<?php

namespace App\Entity;

use App\Repository\MetaConfigsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

date_default_timezone_set('Asia/Tehran');

#[ORM\Entity(repositoryClass: MetaConfigsRepository::class)]
class MetaConfigs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("display")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups("display")]
    private ?string $reference = null;

    #[ORM\Column(length: 255)]
    #[Groups("display")]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups("display")]
    private ?string $type = null;

    #[ORM\Column]
    #[Groups("display")]
    private ?bool $required = null;

    #[ORM\Column]
    #[Groups("display")]
    private ?int $app_code = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups("display")]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deleted_at = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups("display")]
    private ?string $params = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function isRequired(): ?bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): static
    {
        $this->required = $required;

        return $this;
    }

    public function getAppCode(): ?int
    {
        return $this->app_code;
    }

    public function setAppCode(int $app_code): static
    {
        $this->app_code = $app_code;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(): static
    {
        $this->created_at = new \DateTime(Date('Y-m-d H:i:s'));

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(): static
    {
        $this->updated_at = new \DateTime(Date('Y-m-d H:i:s'));

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(): static
    {
        $this->deleted_at = new \DateTime(Date('Y-m-d H:i:s'));

        return $this;
    }

    public function getParams(): ?string
    {
        return $this->params;
    }

    public function setParams(?string $params): static
    {
        $this->params = $params;

        return $this;
    }
}
