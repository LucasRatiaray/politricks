<?php

namespace App\Entity;

use App\Repository\TypeDelitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeDelitRepository::class)]
class TypeDelit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @var Collection<int, UserTypedelit>
     */
    #[ORM\ManyToMany(targetEntity: UserTypedelit::class, mappedBy: 'type_delit_id')]
    private Collection $userTypedelits;

    public function __construct()
    {
        $this->userTypedelits = new ArrayCollection();
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

    /**
     * @return Collection<int, UserTypedelit>
     */
    public function getUserTypedelits(): Collection
    {
        return $this->userTypedelits;
    }

    public function addUserTypedelit(UserTypedelit $userTypedelit): static
    {
        if (!$this->userTypedelits->contains($userTypedelit)) {
            $this->userTypedelits->add($userTypedelit);
            $userTypedelit->addTypeDelitId($this);
        }

        return $this;
    }

    public function removeUserTypedelit(UserTypedelit $userTypedelit): static
    {
        if ($this->userTypedelits->removeElement($userTypedelit)) {
            $userTypedelit->removeTypeDelitId($this);
        }

        return $this;
    }
}
