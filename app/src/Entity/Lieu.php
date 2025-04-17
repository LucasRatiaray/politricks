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
    private Collection $delits;

    public function __construct()
    {
        $this->delits = new ArrayCollection();
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
    public function getUserId(): Collection
    {
        return $this->user_id;
    }

    public function addUserId(Delit $user_id): static
    {
        if (!$this->user_id->contains($user_id)) {
            $this->user_id->add($user_id);
            $user_id->setLieuId($this);
        }

        return $this;
    }

    public function removeUserId(Delit $user_id): static
    {
        if ($this->user_id->removeElement($user_id)) {
            // set the owning side to null (unless already changed)
            if ($user_id->getLieuId() === $this) {
                $user_id->setLieuId(null);
            }
        }

        return $this;
    }
}
