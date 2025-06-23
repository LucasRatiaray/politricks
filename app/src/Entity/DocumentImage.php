<?php

namespace App\Entity;

use App\Repository\DocumentImageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentImageRepository::class)]
class DocumentImage extends Document
{
    #[ORM\Column(length: 10, nullable: true)]
    private ?string $formatImage = null;

    #[ORM\Column(nullable: true)]
    private ?int $largeur = null;

    #[ORM\Column(nullable: true)]
    private ?int $hauteur = null;

    #[ORM\Column(nullable: true)]
    private ?int $resolution = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $datePhoto = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lieuPhoto = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $thumbnailPath = null;

    #[ORM\Column(nullable: true)]
    private ?array $personnesIdentifiees = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $appareilPhoto = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $coordonneesGPS = null;

    #[ORM\Column(nullable: true)]
    private ?bool $estRetouchee = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logicielRetouche = null;

    #[ORM\Column(nullable: true)]
    private ?array $metadonneesExif = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $qualiteImage = null;

    public function __construct()
    {
        parent::__construct();
    }

    public function getFormatImage(): ?string
    {
        return $this->formatImage;
    }

    public function setFormatImage(?string $formatImage): static
    {
        $this->formatImage = $formatImage;

        return $this;
    }

    public function getLargeur(): ?int
    {
        return $this->largeur;
    }

    public function setLargeur(?int $largeur): static
    {
        $this->largeur = $largeur;

        return $this;
    }

    public function getHauteur(): ?int
    {
        return $this->hauteur;
    }

    public function setHauteur(?int $hauteur): static
    {
        $this->hauteur = $hauteur;

        return $this;
    }

    public function getResolution(): ?int
    {
        return $this->resolution;
    }

    public function setResolution(?int $resolution): static
    {
        $this->resolution = $resolution;

        return $this;
    }

    public function getDatePhoto(): ?\DateTime
    {
        return $this->datePhoto;
    }

    public function setDatePhoto(?\DateTime $datePhoto): static
    {
        $this->datePhoto = $datePhoto;

        return $this;
    }

    public function getLieuPhoto(): ?string
    {
        return $this->lieuPhoto;
    }

    public function setLieuPhoto(?string $lieuPhoto): static
    {
        $this->lieuPhoto = $lieuPhoto;

        return $this;
    }

    public function getThumbnailPath(): ?string
    {
        return $this->thumbnailPath;
    }

    public function setThumbnailPath(?string $thumbnailPath): static
    {
        $this->thumbnailPath = $thumbnailPath;

        return $this;
    }

    public function getPersonnesIdentifiees(): ?array
    {
        return $this->personnesIdentifiees;
    }

    public function setPersonnesIdentifiees(?array $personnesIdentifiees): static
    {
        $this->personnesIdentifiees = $personnesIdentifiees;

        return $this;
    }

    public function getAppareilPhoto(): ?string
    {
        return $this->appareilPhoto;
    }

    public function setAppareilPhoto(?string $appareilPhoto): static
    {
        $this->appareilPhoto = $appareilPhoto;

        return $this;
    }

    public function getCoordonneesGPS(): ?string
    {
        return $this->coordonneesGPS;
    }

    public function setCoordonneesGPS(?string $coordonneesGPS): static
    {
        $this->coordonneesGPS = $coordonneesGPS;

        return $this;
    }

    public function isEstRetouchee(): ?bool
    {
        return $this->estRetouchee;
    }

    public function setEstRetouchee(?bool $estRetouchee): static
    {
        $this->estRetouchee = $estRetouchee;

        return $this;
    }

    public function getLogicielRetouche(): ?string
    {
        return $this->logicielRetouche;
    }

    public function setLogicielRetouche(?string $logicielRetouche): static
    {
        $this->logicielRetouche = $logicielRetouche;

        return $this;
    }

    public function getMetadonneesExif(): ?array
    {
        return $this->metadonneesExif;
    }

    public function setMetadonneesExif(?array $metadonneesExif): static
    {
        $this->metadonneesExif = $metadonneesExif;

        return $this;
    }

    public function getQualiteImage(): ?string
    {
        return $this->qualiteImage;
    }

    public function setQualiteImage(?string $qualiteImage): static
    {
        $this->qualiteImage = $qualiteImage;

        return $this;
    }
}
