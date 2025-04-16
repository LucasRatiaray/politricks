<?php

namespace App\Entity;

use App\Repository\LieuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LieuRepository::class)]
class Lieu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $adresse = null;

    /**
     * @var Collection<int, Delit>
     */
    #[ORM\OneToMany(targetEntity: Delit::class, mappedBy: 'lieu_id')]
    private Collection $politicien_id;

    public function __construct()
    {
        $this->politicien_id = new ArrayCollection();
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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * @return Collection<int, Delit>
     */
    public function getPoliticienId(): Collection
    {
        return $this->politicien_id;
    }

    public function addPoliticienId(Delit $politicienId): static
    {
        if (!$this->politicien_id->contains($politicienId)) {
            $this->politicien_id->add($politicienId);
            $politicienId->setLieuId($this);
        }

        return $this;
    }

    public function removePoliticienId(Delit $politicienId): static
    {
        if ($this->politicien_id->removeElement($politicienId)) {
            // set the owning side to null (unless already changed)
            if ($politicienId->getLieuId() === $this) {
                $politicienId->setLieuId(null);
            }
        }

        return $this;
    }
}
