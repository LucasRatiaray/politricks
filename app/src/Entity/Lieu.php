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
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    private ?string $ville = null;

    #[ORM\Column(length: 100)]
    private ?string $pays = null;

    #[ORM\Column(length: 10)]
    private ?string $code�Postal = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 8, nullable: true)]
    private ?string $latitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 11, scale: 8, nullable: true)]
    private ?string $longitude = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $typeEtablissement = null;

    #[ORM\Column(nullable: true)]
    private ?bool $estPublic = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $niveauSecurite = null;

    #[ORM\Column(nullable: true)]
    private ?int $capaciteAccueil = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $horaireAcces = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $responsableSecurite = null;

    #[ORM\Column(nullable: true)]
    private ?bool $videoSurveillance = null;

    /**
     * @var Collection<int, Delit>
     */
    #[ORM\OneToMany(targetEntity: Delit::class, mappedBy: 'lieu')]
    private Collection $delits;

    public function __construct()
    {
        $this->delits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): static
    {
        $this->pays = $pays;

        return $this;
    }

    public function getCode�Postal(): ?string
    {
        return $this->code�Postal;
    }

    public function setCode�Postal(string $code�Postal): static
    {
        $this->code�Postal = $code�Postal;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getTypeEtablissement(): ?string
    {
        return $this->typeEtablissement;
    }

    public function setTypeEtablissement(?string $typeEtablissement): static
    {
        $this->typeEtablissement = $typeEtablissement;

        return $this;
    }

    public function isEstPublic(): ?bool
    {
        return $this->estPublic;
    }

    public function setEstPublic(?bool $estPublic): static
    {
        $this->estPublic = $estPublic;

        return $this;
    }

    public function getNiveauSecurite(): ?string
    {
        return $this->niveauSecurite;
    }

    public function setNiveauSecurite(?string $niveauSecurite): static
    {
        $this->niveauSecurite = $niveauSecurite;

        return $this;
    }

    public function getCapaciteAccueil(): ?int
    {
        return $this->capaciteAccueil;
    }

    public function setCapaciteAccueil(?int $capaciteAccueil): static
    {
        $this->capaciteAccueil = $capaciteAccueil;

        return $this;
    }

    public function getHoraireAcces(): ?string
    {
        return $this->horaireAcces;
    }

    public function setHoraireAcces(?string $horaireAcces): static
    {
        $this->horaireAcces = $horaireAcces;

        return $this;
    }

    public function getResponsableSecurite(): ?string
    {
        return $this->responsableSecurite;
    }

    public function setResponsableSecurite(?string $responsableSecurite): static
    {
        $this->responsableSecurite = $responsableSecurite;

        return $this;
    }

    public function isVideoSurveillance(): ?bool
    {
        return $this->videoSurveillance;
    }

    public function setVideoSurveillance(?bool $videoSurveillance): static
    {
        $this->videoSurveillance = $videoSurveillance;

        return $this;
    }

    /**
     * @return Collection<int, Delit>
     */
    public function getDelits(): Collection
    {
        return $this->delits;
    }

    public function addDelit(Delit $delit): static
    {
        if (!$this->delits->contains($delit)) {
            $this->delits->add($delit);
            $delit->setLieu($this);
        }

        return $this;
    }

    public function removeDelit(Delit $delit): static
    {
        if ($this->delits->removeElement($delit)) {
            // set the owning side to null (unless already changed)
            if ($delit->getLieu() === $this) {
                $delit->setLieu(null);
            }
        }

        return $this;
    }
}
