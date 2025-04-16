<?php

namespace App\Entity;

use App\Repository\DelitRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DelitRepository::class)]
class Delit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_delit = null;

    #[ORM\ManyToOne(inversedBy: 'politicien_id')]
    private ?lieu $lieu_id = null;

    #[ORM\ManyToOne(inversedBy: 'delits')]
    private ?Utilisateur $politicien_id = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDateDelit(): ?\DateTimeInterface
    {
        return $this->date_delit;
    }

    public function setDateDelit(\DateTimeInterface $date_delit): static
    {
        $this->date_delit = $date_delit;

        return $this;
    }

    public function getLieuId(): ?lieu
    {
        return $this->lieu_id;
    }

    public function setLieuId(?lieu $lieu_id): static
    {
        $this->lieu_id = $lieu_id;

        return $this;
    }

    public function getPoliticienId(): ?Utilisateur
    {
        return $this->politicien_id;
    }

    public function setPoliticienId(?Utilisateur $politicien_id): static
    {
        $this->politicien_id = $politicien_id;

        return $this;
    }
}
