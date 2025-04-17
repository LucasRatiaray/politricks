<?php

namespace App\Entity;

use App\Repository\UserTypedelitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserTypedelitRepository::class)]
class UserTypedelit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Utilisateur>
     */
    #[ORM\ManyToMany(targetEntity: Utilisateur::class, inversedBy: 'userTypedelits')]
    private Collection $user_id;

    /**
     * @var Collection<int, TypeDelit>
     */
    #[ORM\ManyToMany(targetEntity: TypeDelit::class, inversedBy: 'userTypedelits')]
    private Collection $type_delit_id;

    public function __construct()
    {
        $this->user_id = new ArrayCollection();
        $this->type_delit_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection<int, TypeDelit>
     */
    public function getTypeDelitId(): Collection
    {
        return $this->type_delit_id;
    }

    public function addTypeDelitId(TypeDelit $typeDelitId): static
    {
        if (!$this->type_delit_id->contains($typeDelitId)) {
            $this->type_delit_id->add($typeDelitId);
        }

        return $this;
    }

    public function removeTypeDelitId(TypeDelit $typeDelitId): static
    {
        $this->type_delit_id->removeElement($typeDelitId);

        return $this;
    }
}
