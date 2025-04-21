<?php

namespace App\Entity;

use App\Repository\PoliticienTypeDelitRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PoliticienTypeDelitRepository::class)]
#[ORM\HasLifecycleCallbacks]
class PoliticienTypeDelit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'politicienTypeDelits')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Le politicien est obligatoire")]
    private ?User $politicien = null;

    #[ORM\ManyToOne(inversedBy: 'politicienTypeDelits')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Le type de délit est obligatoire")]
    private ?TypeDelit $type_delit = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ["default" => false])]
    private bool $est_confirme = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPoliticien(): ?User
    {
        return $this->politicien;
    }

    public function setPoliticien(?User $politicien): static
    {
        $this->politicien = $politicien;
        return $this;
    }

    public function getTypeDelit(): ?TypeDelit
    {
        return $this->type_delit;
    }

    public function setTypeDelit(?TypeDelit $type_delit): static
    {
        $this->type_delit = $type_delit;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function isEstConfirme(): bool
    {
        return $this->est_confirme;
    }

    public function setEstConfirme(bool $est_confirme): static
    {
        $this->est_confirme = $est_confirme;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }
}

