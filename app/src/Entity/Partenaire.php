<?php

namespace App\Entity;

use App\Repository\PartenaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PartenaireRepository::class)]
class Partenaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    /**
     * @var Collection<int, DelitPartenaire>
     */
    #[ORM\ManyToMany(targetEntity: DelitPartenaire::class, mappedBy: 'partenaire_id')]
    private Collection $delitPartenaires;

    public function __construct()
    {
        $this->delitPartenaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, DelitPartenaire>
     */
    public function getDelitPartenaires(): Collection
    {
        return $this->delitPartenaires;
    }

    public function addDelitPartenaire(DelitPartenaire $delitPartenaire): static
    {
        if (!$this->delitPartenaires->contains($delitPartenaire)) {
            $this->delitPartenaires->add($delitPartenaire);
            $delitPartenaire->addPartenaireId($this);
        }

        return $this;
    }

    public function removeDelitPartenaire(DelitPartenaire $delitPartenaire): static
    {
        if ($this->delitPartenaires->removeElement($delitPartenaire)) {
            $delitPartenaire->removePartenaireId($this);
        }

        return $this;
    }
}
