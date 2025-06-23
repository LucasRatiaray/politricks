<?php

namespace App\Entity;

use App\Repository\DocumentVideoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentVideoRepository::class)]
class DocumentVideo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $formatVideo = null;

    #[ORM\Column(nullable: true)]
    private ?int $duree = null;

    #[ORM\Column(length: 20)]
    private ?string $resolution = null;

    #[ORM\Column(nullable: true)]
    private ?int $frameRate = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $codec = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $qualiteVideo = null;

    #[ORM\Column(nullable: true)]
    private ?bool $avecSon = null;

    #[ORM\Column(nullable: true)]
    private ?bool $sousTitres = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $langueAudio = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $dateEnregistrement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lieuEnregistrement = null;

    #[ORM\Column(nullable: true)]
    private ?array $personnesFilmees = null;

    #[ORM\Column(nullable: true)]
    private ?array $timestampsImportants = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $thumbnailPath = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $urlStreamingExterne = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $plateformeHebergement = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFormatVideo(): ?string
    {
        return $this->formatVideo;
    }

    public function setFormatVideo(?string $formatVideo): static
    {
        $this->formatVideo = $formatVideo;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getResolution(): ?string
    {
        return $this->resolution;
    }

    public function setResolution(string $resolution): static
    {
        $this->resolution = $resolution;

        return $this;
    }

    public function getFrameRate(): ?int
    {
        return $this->frameRate;
    }

    public function setFrameRate(?int $frameRate): static
    {
        $this->frameRate = $frameRate;

        return $this;
    }

    public function getCodec(): ?string
    {
        return $this->codec;
    }

    public function setCodec(?string $codec): static
    {
        $this->codec = $codec;

        return $this;
    }

    public function getQualiteVideo(): ?string
    {
        return $this->qualiteVideo;
    }

    public function setQualiteVideo(?string $qualiteVideo): static
    {
        $this->qualiteVideo = $qualiteVideo;

        return $this;
    }

    public function isAvecSon(): ?bool
    {
        return $this->avecSon;
    }

    public function setAvecSon(?bool $avecSon): static
    {
        $this->avecSon = $avecSon;

        return $this;
    }

    public function isSousTitres(): ?bool
    {
        return $this->sousTitres;
    }

    public function setSousTitres(?bool $sousTitres): static
    {
        $this->sousTitres = $sousTitres;

        return $this;
    }

    public function getLangueAudio(): ?string
    {
        return $this->langueAudio;
    }

    public function setLangueAudio(?string $langueAudio): static
    {
        $this->langueAudio = $langueAudio;

        return $this;
    }

    public function getDateEnregistrement(): ?\DateTime
    {
        return $this->dateEnregistrement;
    }

    public function setDateEnregistrement(?\DateTime $dateEnregistrement): static
    {
        $this->dateEnregistrement = $dateEnregistrement;

        return $this;
    }

    public function getLieuEnregistrement(): ?string
    {
        return $this->lieuEnregistrement;
    }

    public function setLieuEnregistrement(?string $lieuEnregistrement): static
    {
        $this->lieuEnregistrement = $lieuEnregistrement;

        return $this;
    }

    public function getPersonnesFilmees(): ?array
    {
        return $this->personnesFilmees;
    }

    public function setPersonnesFilmees(?array $personnesFilmees): static
    {
        $this->personnesFilmees = $personnesFilmees;

        return $this;
    }

    public function getTimestampsImportants(): ?array
    {
        return $this->timestampsImportants;
    }

    public function setTimestampsImportants(?array $timestampsImportants): static
    {
        $this->timestampsImportants = $timestampsImportants;

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

    public function getUrlStreamingExterne(): ?string
    {
        return $this->urlStreamingExterne;
    }

    public function setUrlStreamingExterne(?string $urlStreamingExterne): static
    {
        $this->urlStreamingExterne = $urlStreamingExterne;

        return $this;
    }

    public function getPlateformeHebergement(): ?string
    {
        return $this->plateformeHebergement;
    }

    public function setPlateformeHebergement(?string $plateformeHebergement): static
    {
        $this->plateformeHebergement = $plateformeHebergement;

        return $this;
    }
}
