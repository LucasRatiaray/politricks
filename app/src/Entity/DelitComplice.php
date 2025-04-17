<?php

namespace App\Entity;

use App\Repository\DelitCompliceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DelitCompliceRepository::class)]
class DelitComplice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    /**
     * @var Collection<int, Utilisateur>
     */
    #[ORM\ManyToMany(targetEntity: Utilisateur::class, inversedBy: 'delitComplices')]
    private Collection $user_id;

    /**
     * @var Collection<int, Delit>
     */
    #[ORM\ManyToMany(targetEntity: Delit::class, inversedBy: 'delitComplices')]
    private Collection $delit_id;

    public function __construct()
    {
        $this->user_id = new ArrayCollection();
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
     * @return Collection<int, Utilisateur>
     */
    public function getUserId(): Collection
    {
        return $this->user_id;
    }

    public function addUserId(Utilisateur $userId): static
    {
        if (!$this->user_id->contains($userId)) {
            $this->user_id->add($userId);
        }

        return $this;
    }

    public function removeUserId(Utilisateur $userId): static
    {
        $this->user_id->removeElement($userId);

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
