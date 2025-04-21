<?php

namespace App\Entity;

use App\Repository\DelitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DelitRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Delit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le type de délit est obligatoire")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Le type doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le type ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $type = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La description est obligatoire")]
    #[Assert\Length(
        min: 10,
        minMessage: "La description doit contenir au moins {{ limit }} caractères"
    )]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: "La date du délit est obligatoire")]
    private ?\DateTimeInterface $date_delit = null;

    #[ORM\ManyToOne(inversedBy: 'delits')]
    #[Assert\NotNull(message: "Le lieu est obligatoire")]
    private ?Lieu $lieu_id = null;

    #[ORM\ManyToOne(inversedBy: 'delits')]
    #[Assert\NotNull(message: "L'utilisateur est obligatoire")]
    private ?User $user_id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @var Collection<int, Preuve>
     */
    #[ORM\ManyToMany(targetEntity: Preuve::class, mappedBy: 'delit_id')]
    private Collection $preuves;

    /**
     * @var Collection<int, DelitComplice>
     */
    #[ORM\ManyToMany(targetEntity: DelitComplice::class, mappedBy: 'delit_id')]
    private Collection $delitComplices;

    public function __construct()
    {
        $this->preuves = new ArrayCollection();
        $this->delitComplices = new ArrayCollection();
    }
    
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

    public function getLieuId(): ?Lieu
    {
        return $this->lieu_id;
    }

    public function setLieuId(?Lieu $lieu_id): static
    {
        $this->lieu_id = $lieu_id;

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

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * @return Collection<int, Preuve>
     */
    public function getPreuves(): Collection
    {
        return $this->preuves;
    }

    public function addPreufe(Preuve $preufe): static
    {
        if (!$this->preuves->contains($preufe)) {
            $this->preuves->add($preufe);
            $preufe->addDelitId($this);
        }

        return $this;
    }

    public function removePreufe(Preuve $preufe): static
    {
        if ($this->preuves->removeElement($preufe)) {
            $preufe->removeDelitId($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, DelitComplice>
     */
    public function getDelitComplices(): Collection
    {
        return $this->delitComplices;
    }

    public function addDelitComplice(DelitComplice $delitComplice): static
    {
        if (!$this->delitComplices->contains($delitComplice)) {
            $this->delitComplices->add($delitComplice);
            $delitComplice->addDelitId($this);
        }

        return $this;
    }

    public function removeDelitComplice(DelitComplice $delitComplice): static
    {
        if ($this->delitComplices->removeElement($delitComplice)) {
            $delitComplice->removeDelitId($this);
        }

        return $this;
    }
}
