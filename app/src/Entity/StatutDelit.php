<?php

namespace App\Entity;

use App\Repository\StatutDelitRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StatutDelitRepository::class)]
#[ORM\HasLifecycleCallbacks]
class StatutDelit
{
    const STATUT_EN_COURS = 'EN_COURS';
    const STATUT_INSTRUCTION = 'INSTRUCTION';
    const STATUT_JUGEMENT = 'JUGEMENT';
    const STATUT_TERMINE = 'TERMINE';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Assert\NotBlank(message: "Le statut est obligatoire")]
    #[Assert\Choice(choices: [
        self::STATUT_EN_COURS,
        self::STATUT_INSTRUCTION,
        self::STATUT_JUGEMENT,
        self::STATUT_TERMINE
    ], message: "Statut invalide")]
    private ?string $statut = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $est_coupable = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $peine = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $montant_amende = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $duree_peine_mois = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_jugement = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $details_jugement = null;

    #[ORM\OneToOne(inversedBy: 'statut')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Le délit est obligatoire")]
    private ?Delit $delit = null;

    #[ORM\ManyToOne(inversedBy: 'statutsDelits')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Le juge est obligatoire")]
    private ?User $juge = null;

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

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function isEstCoupable(): ?bool
    {
        return $this->est_coupable;
    }

    public function setEstCoupable(?bool $est_coupable): static
    {
        $this->est_coupable = $est_coupable;
        return $this;
    }

    public function getPeine(): ?string
    {
        return $this->peine;
    }

    public function setPeine(?string $peine): static
    {
        $this->peine = $peine;
        return $this;
    }

    public function getMontantAmende(): ?int
    {
        return $this->montant_amende;
    }

    public function setMontantAmende(?int $montant_amende): static
    {
        $this->montant_amende = $montant_amende;
        return $this;
    }

    public function getDureePeineMois(): ?int
    {
        return $this->duree_peine_mois;
    }

    public function setDureePeineMois(?int $duree_peine_mois): static
    {
        $this->duree_peine_mois = $duree_peine_mois;
        return $this;
    }

    public function getDateJugement(): ?\DateTimeInterface
    {
        return $this->date_jugement;
    }

    public function setDateJugement(?\DateTimeInterface $date_jugement): static
    {
        $this->date_jugement = $date_jugement;
        return $this;
    }

    public function getDetailsJugement(): ?string
    {
        return $this->details_jugement;
    }

    public function setDetailsJugement(?string $details_jugement): static
    {
        $this->details_jugement = $details_jugement;
        return $this;
    }

    public function getDelit(): ?Delit
    {
        return $this->delit;
    }

    public function setDelit(?Delit $delit): static
    {
        $this->delit = $delit;
        return $this;
    }

    public function getJuge(): ?User
    {
        return $this->juge;
    }

    public function setJuge(?User $juge): static
    {
        $this->juge = $juge;
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

