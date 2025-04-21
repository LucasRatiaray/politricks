<?php

namespace App\Entity;

use App\Repository\TypeDelitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: TypeDelitRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['nom'], message: 'Ce type de délit existe déjà')]
class TypeDelit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: "Le nom du type de délit est obligatoire")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $nom = null;

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
     * @var Collection<int, UserTypedelit>
     */
    #[ORM\ManyToMany(targetEntity: UserTypedelit::class, mappedBy: 'type_delit_id')]
    private Collection $userTypedelits;

    public function __construct()
    {
        $this->userTypedelits = new ArrayCollection();
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

    public function getNom(): ?string
    {
        return $this->nom;
    }
    
    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
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
