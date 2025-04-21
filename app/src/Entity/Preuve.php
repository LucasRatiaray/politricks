<?php

namespace App\Entity;

use App\Repository\PreuveRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PreuveRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Preuve
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(
        min: 10,
        minMessage: "La description doit contenir au moins {{ limit }} caractères"
    )]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom du fichier est obligatoire")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Le nom du fichier doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le nom du fichier ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $fichier = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @var Collection<int, Delit>
     */
    #[ORM\ManyToMany(targetEntity: Delit::class, inversedBy: 'preuves')]
    #[Assert\Count(
        min: 1,
        minMessage: "Au moins un délit doit être associé"
    )]
    private Collection $delit_id;

    public function __construct()
    {
        $this->delit_id = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getFichier(): ?string
    {
        return $this->fichier;
    }

    public function setFichier(string $fichier): static
    {
        $this->fichier = $fichier;
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

    /**
     * @return Collection<int, Delit>
     */
    public function getDelitId(): Collection
    {
        return $this->delit_id;
    }

    public function addDelitId(Delit $delitId): static
    {
        if (!$this->delit_id->contains($delitId)) {
            $this->delit_id->add($delitId);
        }

        return $this;
    }

    public function removeDelitId(Delit $delitId): static
    {
        $this->delit_id->removeElement($delitId);

        return $this;
    }
}
