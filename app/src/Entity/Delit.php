<?php

namespace App\Entity;

use App\Repository\DelitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DelitRepository::class)]
class Delit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_delit = null;

    #[ORM\ManyToOne(inversedBy: 'user_id')]
    private ?lieu $lieu_id = null;

    #[ORM\ManyToOne(inversedBy: 'delits')]
    private ?Utilisateur $user_id = null;

    /**
     * @var Collection<int, Preuve>
     */
    #[ORM\ManyToMany(targetEntity: Preuve::class, mappedBy: 'delit_id')]
    private Collection $preuves;

    /**
     * @var Collection<int, DelitComplice>
     */
    #[ORM\ManyToMany(targetEntity: DelitComplice::class, mappedBy: 'delit_id')]
    private Collection $delitComplices;

    public function __construct()
    {
        $this->preuves = new ArrayCollection();
        $this->delitComplices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDateDelit(): ?\DateTimeInterface
    {
        return $this->date_delit;
    }

    public function setDateDelit(\DateTimeInterface $date_delit): static
    {
        $this->date_delit = $date_delit;

        return $this;
    }

    public function getLieuId(): ?lieu
    {
        return $this->lieu_id;
    }

    public function setLieuId(?lieu $lieu_id): static
    {
        $this->lieu_id = $lieu_id;

        return $this;
    }

    public function getUserId(): ?Utilisateur
    {
        return $this->user_id;
    }

    public function setUserId(?Utilisateur $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * @return Collection<int, Preuve>
     */
    public function getPreuves(): Collection
    {
        return $this->preuves;
    }

    public function addPreufe(Preuve $preufe): static
    {
        if (!$this->preuves->contains($preufe)) {
            $this->preuves->add($preufe);
            $preufe->addDelitId($this);
        }

        return $this;
    }

    public function removePreufe(Preuve $preufe): static
    {
        if ($this->preuves->removeElement($preufe)) {
            $preufe->removeDelitId($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, DelitComplice>
     */
    public function getDelitComplices(): Collection
    {
        return $this->delitComplices;
    }

    public function addDelitComplice(DelitComplice $delitComplice): static
    {
        if (!$this->delitComplices->contains($delitComplice)) {
            $this->delitComplices->add($delitComplice);
            $delitComplice->addDelitId($this);
        }

        return $this;
    }

    public function removeDelitComplice(DelitComplice $delitComplice): static
    {
        if ($this->delitComplices->removeElement($delitComplice)) {
            $delitComplice->removeDelitId($this);
        }

        return $this;
    }
}
