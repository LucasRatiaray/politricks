<?php

namespace App\Entity;

use App\Repository\DelitPartenaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DelitPartenaireRepository::class)]
class DelitPartenaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    /**
     * @var Collection<int, Partenaire>
     */
    #[ORM\ManyToMany(targetEntity: Partenaire::class, inversedBy: 'delitPartenaires')]
    private Collection $partenaire_id;

    /**
     * @var Collection<int, Delit>
     */
    #[ORM\ManyToMany(targetEntity: Delit::class)]
    private Collection $delit_id;

    public function __construct()
    {
        $this->partenaire_id = new ArrayCollection();
        $this->delit_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
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
