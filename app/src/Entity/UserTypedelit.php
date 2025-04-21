<?php

namespace App\Entity;

use App\Repository\UserTypedelitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserTypedelitRepository::class)]
#[ORM\HasLifecycleCallbacks]
class UserTypedelit
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
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'userTypedelits')]
    #[Assert\Count(
        min: 1,
        minMessage: "Au moins un utilisateur doit être associé"
    )]
    private Collection $user_id;

    /**
     * @var Collection<int, TypeDelit>
     */
    #[ORM\ManyToMany(targetEntity: TypeDelit::class, inversedBy: 'userTypedelits')]
    #[Assert\Count(
        min: 1,
        minMessage: "Au moins un type de délit doit être associé"
    )]
    private Collection $type_delit_id;

    public function __construct()
    {
        $this->user_id = new ArrayCollection();
        $this->type_delit_id = new ArrayCollection();
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

    /**
     * @return Collection<int, User>
     */
    public function getUserId(): Collection
    {
        return $this->user_id;
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
     * @return Collection<int, User>
     */
    public function addUserId(User $userId): static
    {
        if (!$this->user_id->contains($userId)) {
            $this->user_id->add($userId);
        }

        return $this;
    }

    public function removeUserId(User $userId): static
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
