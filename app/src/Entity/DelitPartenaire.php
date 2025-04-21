<?php

namespace App\Entity;

use App\Repository\DelitPartenaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: DelitPartenaireRepository::class)]
#[ORM\HasLifecycleCallbacks]
class DelitPartenaire
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

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @var Collection<int, Partenaire>
     */
    #[ORM\ManyToMany(targetEntity: Partenaire::class, inversedBy: 'delitPartenaires')]
    #[Assert\Count(
        min: 1,
        minMessage: "Au moins un partenaire doit être associé"
    )]
    private Collection $partenaire_id;

    /**
     * @var Collection<int, Delit>
     */
    #[ORM\ManyToMany(targetEntity: Delit::class, inversedBy: 'delitPartenaires')]
    #[Assert\Count(
        min: 1,
        minMessage: "Au moins un délit doit être associé"
    )]
    private Collection $delit_id;

    public function __construct()
    {
        $this->partenaire_id = new ArrayCollection();
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection<int, Partenaire>
     */
    public function getPartenaireId(): Collection
    {
        return $this->partenaire_id;
    }

    public function addPartenaireId(Partenaire $partenaireId): static
    {
        if (!$this->partenaire_id->contains($partenaireId)) {
            $this->partenaire_id->add($partenaireId);
        }

        return $this;
    }

    public function removePartenaireId(Partenaire $partenaireId): static
    {
        $this->partenaire_id->removeElement($partenaireId);

        return $this;
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
