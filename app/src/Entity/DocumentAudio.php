<?php

namespace App\Entity;

use App\Repository\DocumentAudioRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentAudioRepository::class)]
class DocumentAudio extends Document
{
    #[ORM\Column(length: 10, nullable: true)]
    private ?string $formatAudio = null;

    #[ORM\Column(nullable: true)]
    private ?int $duree = null;

    #[ORM\Column(nullable: true)]
    private ?int $bitrate = null;

    #[ORM\Column(nullable: true)]
    private ?int $frequenceEchantillonnage = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $nombreCanaux = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $qualiteAudio = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $dateEnregistrement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lieuEnregistrement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $materielEnregistrement = null;

    #[ORM\Column(nullable: true)]
    private ?array $personnesEnregistrees = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $transcriptionTexte = null;

    #[ORM\Column(nullable: true)]
    private ?bool $transcriptionValidee = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $languePrincipale = null;

    #[ORM\Column(nullable: true)]
    private ?array $motsClesAudio = null;

    #[ORM\Column(nullable: true)]
    private ?int $niveauSonore = null;

    #[ORM\Column(nullable: true)]
    private ?array $filtresAppliques = null;

    public function __construct()
    {
        parent::__construct();
    }

    public function getFormatAudio(): ?string
    {
        return $this->formatAudio;
    }

    public function setFormatAudio(?string $formatAudio): static
    {
        $this->formatAudio = $formatAudio;

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

    public function getBitrate(): ?int
    {
        return $this->bitrate;
    }

    public function setBitrate(?int $bitrate): static
    {
        $this->bitrate = $bitrate;

        return $this;
    }

    public function getFrequenceEchantillonnage(): ?int
    {
        return $this->frequenceEchantillonnage;
    }

    public function setFrequenceEchantillonnage(?int $frequenceEchantillonnage): static
    {
        $this->frequenceEchantillonnage = $frequenceEchantillonnage;

        return $this;
    }

    public function getNombreCanaux(): ?string
    {
        return $this->nombreCanaux;
    }

    public function setNombreCanaux(?string $nombreCanaux): static
    {
        $this->nombreCanaux = $nombreCanaux;

        return $this;
    }

    public function getQualiteAudio(): ?string
    {
        return $this->qualiteAudio;
    }

    public function setQualiteAudio(?string $qualiteAudio): static
    {
        $this->qualiteAudio = $qualiteAudio;

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

    public function getMaterielEnregistrement(): ?string
    {
        return $this->materielEnregistrement;
    }

    public function setMaterielEnregistrement(?string $materielEnregistrement): static
    {
        $this->materielEnregistrement = $materielEnregistrement;

        return $this;
    }

    public function getPersonnesEnregistrees(): ?array
    {
        return $this->personnesEnregistrees;
    }

    public function setPersonnesEnregistrees(?array $personnesEnregistrees): static
    {
        $this->personnesEnregistrees = $personnesEnregistrees;

        return $this;
    }

    public function getTranscriptionTexte(): ?string
    {
        return $this->transcriptionTexte;
    }

    public function setTranscriptionTexte(?string $transcriptionTexte): static
    {
        $this->transcriptionTexte = $transcriptionTexte;

        return $this;
    }

    public function isTranscriptionValidee(): ?bool
    {
        return $this->transcriptionValidee;
    }

    public function setTranscriptionValidee(?bool $transcriptionValidee): static
    {
        $this->transcriptionValidee = $transcriptionValidee;

        return $this;
    }

    public function getLanguePrincipale(): ?string
    {
        return $this->languePrincipale;
    }

    public function setLanguePrincipale(?string $languePrincipale): static
    {
        $this->languePrincipale = $languePrincipale;

        return $this;
    }

    public function getMotsClesAudio(): ?array
    {
        return $this->motsClesAudio;
    }

    public function setMotsClesAudio(?array $motsClesAudio): static
    {
        $this->motsClesAudio = $motsClesAudio;

        return $this;
    }

    public function getNiveauSonore(): ?int
    {
        return $this->niveauSonore;
    }

    public function setNiveauSonore(?int $niveauSonore): static
    {
        $this->niveauSonore = $niveauSonore;

        return $this;
    }

    public function getFiltresAppliques(): ?array
    {
        return $this->filtresAppliques;
    }

    public function setFiltresAppliques(?array $filtresAppliques): static
    {
        $this->filtresAppliques = $filtresAppliques;

        return $this;
    }
}
